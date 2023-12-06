<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Support\Facades\{Hash, DB};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\{AdminChangePasswordRequest, AdminForgotPasswordRequest, RegisterRequest, LoginRequest, AdminRegisterRequest, AdminLoginRequest, AdminResetPasswordRequest, ChangePasswordRequest, ForgotPasswordRequest, LoginWithFacebookRequest, LoginWithGoogleRequest, ResendUserVerificationEmailRequest, ResetPasswordRequest};
use App\Exceptions\{AdminEmailUnregisteredException, InvalidCredentialsException, CustomException, EmailAlreadyVerifiedException, EmailUnregisteredException, ResetPasswordVerificationLinkExpiredException};
use App\Models\{Role, User};
use App\Notifications\{AdminResetPasswordNotification, SendUserEmailVerificationNotification, ResetPasswordNotification};

class AuthController extends Controller
{
    /**
     * Register as a user
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = DB::transaction(function() use ($data) {
            // Create the user
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            // Create the verification hash
            $user->forceFill([
                'verification' => hash_hmac('sha512', "{$user->email}-{$user->id}", config('fbn.email_verification_hash')),
                'verification_expires_at' => now()->addSeconds(config('fbn.verification_expiration_time'))
            ])->save();

            return $user;
        });

        // Send email verification notification
        $user->notify(new SendUserEmailVerificationNotification);

        return $this->sendSuccess('User registration successful. Check your email to verify your account.', 201);
    }

    /**
     * Login as a user
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        // Get the user
        $user = User::users()
                    ->where('email', $data['email'])
                    ->first();

        // Check if a user with the email exists
        if (!isset($user)) {
            throw new InvalidCredentialsException;
        }

        // Check if the password matches
        if (!Hash::check($data['password'], $user->password)) {
            throw new InvalidCredentialsException;
        }

        /**
         * We login in the user but we logout any other instances of the user login
         */
        $token = DB::transaction(function() use ($user) {
            // Logout all other user logins
            $user->tokens()->delete();

            // Login the user
            $token = $user->createToken('token-'.uniqid())->plainTextToken;

            return $token;
        });

        return $this->sendSuccess('Login successful.', 200, [
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Login as a user using Google
     */
    public function loginWithGoogle(LoginWithGoogleRequest $request)
    {
        $data = $request->validated();

        try {
            // Get the raw user details
            $googleUser = Socialite::driver('google')
                                ->userFromToken($data['access_token'])
                                ->getRaw();

            // Login the user
            $loginDetails = DB::transaction(function() use ($googleUser) {
                /**
                 * We fetch the user or we create the user
                 */
                $user = User::firstOrCreate([
                    'email' => $googleUser['email']
                ], [
                    'first_name' => $googleUser['given_name'],
                    'last_name' => $googleUser['family_name']
                ]);

                // Check if the email is verified, then we verify the user email
                if (isset($googleUser['email_verified']) &&
                    $googleUser['email_verified']) {
                    $user->markEmailAsVerified();
                }

                // Refresh the user to get a fresh instance of the user to populate null fields if created
                $user->refresh();

                // Login the user
                $token = $user->createToken('token-'.uniqid())->plainTextToken;

                return compact('user', 'token');
            });

            return $this->sendSuccess('Login successful.', 200, $loginDetails);
        } catch (Throwable $e) {
            throw new CustomException('Login with Google failed: '.$e->getMessage(), 401);
        }
    }

    /**
     * Login with Google redirect
     */
    public function loginWithGoogleRedirect(Request $request)
    {

    }

    /**
     * Login as a user using Facebook
     */
    public function loginWithFacebook(LoginWithFacebookRequest $request)
    {
        $data = $request->validated();

        try {
            // Get the raw user details
            $facebookUser = Socialite::driver('facebook')
                                    ->fields([
                                        'first_name',
                                        'last_name',
                                        'email',
                                    ])
                                    ->scopes(['public_profile', 'email'])
                                    ->userFromToken($data['access_token'])
                                    ->getRaw();

            // Check if email is returned
            if (!isset($facebookUser['email'])) {
                throw new CustomException('Email permission is required.', 401);
            }

            // Login the user
            $loginDetails = DB::transaction(function() use ($facebookUser) {
                /**
                 * We fetch the user or we create the user
                 */
                $user = User::firstOrCreate([
                    'email' => $facebookUser['email']
                ], [
                    'first_name' => $facebookUser['first_name'],
                    'last_name' => $facebookUser['last_name']
                ]);

                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }

                // Refresh the user to get a fresh instance of the user to populate null fields if created
                $user->refresh();

                // Login the user
                $token = $user->createToken('token-'.uniqid())->plainTextToken;

                return compact('user', 'token');
            });

            return $this->sendSuccess('Login successful.', 200, $loginDetails);
        } catch (Throwable $e) {
            throw new CustomException('Login with Facebook failed: '.$e->getMessage(), 401);
        }
    }

    /**
     * Login with Facebook redirect
     */
    public function loginWithFacebookRedirect(Request $request)
    {

    }

    /**
     * Initiate forgot password process
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $data = $request->validated();

        // Get the user
        $user = User::users()
                    ->firstWhere('email', $data['email']);

        if (!isset($user)) {
            throw new EmailUnregisteredException;
        }

        // Create the hash for Reset password 
        $user->forceFill([
            'reset_password_verification' => hash_hmac('sha512', $user->email.'-'.$user->id, config('fbn.reset_password_verification_hash')),
            'reset_password_verification_expires_at' => now()->addSeconds(config('fbn.reset_password_verification_expiration_time'))
        ])->save();

        // Send reset password notification
        $user->notify(new ResetPasswordNotification);

        return $this->sendSuccess('Check your email to reset your password');
    }

    /**
     * Reset the password of the user
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();

        // Get the user
        $user = User::users()
                    ->firstWhere('reset_password_verification', $data['token']);

        if (!isset($user)) {
            throw new EmailUnregisteredException;
        }

        // Check if the reset password verification link has expired
        if (isset($user->reset_password_verification_expires_at) && $user->reset_password_verification_expires_at < now()) {
            throw new ResetPasswordVerificationLinkExpiredException;
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'reset_password_verification' => null,
            'reset_password_verification_expires_at' => null
        ])->save();

        return $this->sendSuccess('Password reset successfully');
    }

    /**
     * Verify the email of a user
     */
    public function verifyEmail(Request $request)
    {
        // Get the token from the request
        $token = $request->input('token');

        // Check if there is a token
        if (!isset($token)) {
            return __('app.email_verification_failed');
        }

        // Get the user based on the token
        $user = User::users()
                    ->where('verification', $token)
                    ->first();

        // Check if there is a user with the verification
        if (!isset($user)) {
            return __('app.email_verification_failed');
        }

        // Check if the email has been verified
        if ($user->hasVerifiedEmail()) {
            return __('app.email_already_verified');
        }

        // Check if the password verification link has expired
        if (isset($user->verification_expires_at) && $user->verification_expires_at < now()) {
            return __('app.verification_link_expired');
        }

        DB::transaction(function() use ($user) {
            // Verify the email of the user
            $user->markEmailAsVerified();

            $user->forceFill([
                'verification_expires_at' => null
            ])->save();
        });

        return 'Email verification successful.';
    }

    /**
     * Resend the verification email of a user
     */
    public function resendVerificationEmail(ResendUserVerificationEmailRequest $request)
    {
        $data = $request->validated();

        // Get the user
        $user = User::users()
                    ->where('email', $data['email'])
                    ->first();

        // Check if the user exists
        if (!isset($user)) {
            throw new EmailUnregisteredException;
        }

        // Check if the user has already been verified
        if ($user->hasVerifiedEmail()) {
            throw new EmailAlreadyVerifiedException;
        }

        // We check if the the user has a verification hash
        if (!isset($user->verification)) {
            // Create the verification hash
            $user->forceFill([
                'verification' => hash_hmac('sha512', "{$user->email}-{$user->id}", config('fbn.email_verification_hash')),
                'verification_expires_at' => now()->addSeconds(config('fbn.verification_expiration_time'))
            ])->save();
        }

        // Send email verification notification
        $user->notify(new SendUserEmailVerificationNotification);

        return $this->sendSuccess('Verification email sent successfully.');
    }

    /**
     * Logout from the application
     */
    public function logout(Request $request)
    {
        // Logout the user
        $request->user()
                ->tokens()
                ->delete();

        return $this->sendSuccess('Logout successful.');
    }

    /**
     * Change password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();

        // Change the password of the user
        $request->user()
                ->update([
                    'password' => Hash::make($data['password'])
                ]);

        return $this->sendSuccess('Password changed successfully.');
    }

    /**
     * Register as an administrator
     */
    public function adminRegister(AdminRegisterRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function() use ($data) {
            // Create the admin
            $admin = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            // Upgrade to the role of an administrator
            $admin->forceFill([
                'role_id' => Role::ADMINISTRATOR
            ])->save();
        });

        return $this->sendSuccess('Administrator registration successful.', 201);
    }

    /**
     * Login as an administrator
     */
    public function adminLogin(AdminLoginRequest $request)
    {
        $data = $request->validated();

        // Get the adminstrator
        $admin = User::administrators()
                    ->where('email', $data['email'])
                    ->first();

        // Check if a user with the email exists
        if (!isset($admin)) {
            throw new InvalidCredentialsException;
        }

        // Check if the password matches
        if (!Hash::check($data['password'], $admin->password)) {
            throw new InvalidCredentialsException;
        }

        /**
         * We login in the administrator but we logout any other instances of the administrative login
         */
        $token = DB::transaction(function() use ($admin) {
            // Logout all other administrative logins
            $admin->tokens()->delete();

            // Login the administrator
            $token = $admin->createToken('admin-token-'.uniqid())->plainTextToken;

            return $token;
        });

        return $this->sendSuccess('Login successful.', 200, [
            'user' => $admin,
            'token' => $token
        ]);
    }

    /**
     * Initiate forgot password process for an administrator
     */
    public function adminForgotPassword(AdminForgotPasswordRequest $request)
    {
        $data = $request->validated();

        // Get the administrator
        $admin = User::administrators()
                    ->firstWhere('email', $data['email']);

        if (!isset($admin)) {
            throw new AdminEmailUnregisteredException;
        }

        // Create the hash for Reset password 
        $admin->forceFill([
            'reset_password_verification' => hash_hmac('sha512', $admin->email.'-'.$admin->id, config('fbn.reset_password_verification_hash')),
            'reset_password_verification_expires_at' => now()->addSeconds(config('fbn.reset_password_verification_expiration_time'))
        ])->save();

        // Send reset password notification
        $admin->notify(new AdminResetPasswordNotification);

        return $this->sendSuccess('Check your email to reset your administrative password.');
    }

    /**
     * Reset the password of the user
     */
    public function adminResetPassword(AdminResetPasswordRequest $request)
    {
        $data = $request->validated();

        // Get the administrator
        $admin = User::administrators()
                    ->firstWhere('reset_password_verification', $data['token']);

        if (!isset($admin)) {
            throw new AdminEmailUnregisteredException;
        }

        // Check if the reset password verification link has expired
        if (isset($admin->reset_password_verification_expires_at) && $admin->reset_password_verification_expires_at < now()) {
            throw new ResetPasswordVerificationLinkExpiredException;
        }

        $admin->forceFill([
            'password' => Hash::make($data['password']),
            'reset_password_verification' => null,
            'reset_password_verification_expires_at' => null
        ])->save();

        return $this->sendSuccess('Administrative password reset successfully');
    }

    /**
     * Change password for an administrator
     */
    public function adminChangePassword(AdminChangePasswordRequest $request)
    {
        $data = $request->validated();

        // Change the password of the administrator
        $request->user()
                ->update([
                    'password' => Hash::make($data['password'])
                ]);

        return $this->sendSuccess('Administrative password changed successfully.');
    }
}
