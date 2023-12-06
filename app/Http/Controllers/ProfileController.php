<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Services\Phone\Nigeria as NigerianPhone;

class ProfileController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ProfileRequest $request)
    {
        $data = $request->validated();

        // Update the user details
        $request->user()
                ->update([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => app()->make(NigerianPhone::class)->convert($data['phone'])
                ]);

        return $this->sendSuccess('Profile updated successfully.', 200, $request->user());
    }
}
