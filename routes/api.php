<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AdvertController, AuthController, CategoryController, ContentController, DiscoverController, FavouriteController, FeaturedPodcastsController, FeaturedShowsController, FeedController, LatestAudiosController, LatestEpisodesController, LatestVideosController, LiveController, LiveEventController, PlaylistController, PodcastController, PopularPodcastsController, PopularShowsController, ProfileController, QueueController, RecentlyPlayedController, RecentUploadsController, RecommendedAudiosController, RecommendedPodcastsController, RecommendedShowsController, RecommendedVideosController, SearchContentsController, SearchPodcastsController, SearchShowsController, ShowController, SuggestedPodcastsController, SuggestedShowsController, TagController, TestController, TopAudiosController, TopVideosController};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/test', [TestController::class, 'index']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/**
 * Authentication Routes
 */
Route::name('auth')
    ->controller(AuthController::class)
    ->group(function() {
        
        Route::post('/register', 'register')->name('.register')->middleware('decrypt_password');

        Route::post('/login', 'login')->name('.login')->middleware('decrypt_password');

        Route::post('/login-with-google', 'loginWithGoogle')->name('.login-with-google');
        Route::get('/login-with-google-redirect', 'loginWithGoogleRedirect')->name('.login-with-google-redirect');

        Route::post('/login-with-facebook', 'loginWithFacebook')->name('.login-with-facebook');
        Route::get('/login-with-facebook-redirect', 'loginWithFacebookRedirect')->name('.login-with-facebook-redirect');

        Route::post('/resend-verification-email', 'resendVerificationEmail')->name('.resend-verification-email');

        Route::get('/verify-email', 'verifyEmail')->name('.verify-email');

        Route::post('/forgot-password', 'forgotPassword')->name('.forgot-password');

        Route::put('/reset-password', 'resetPassword')->name('.reset-password')->middleware('decrypt_password');

        Route::middleware([
                'auth:sanctum',
            ])
            ->group(function() {

                Route::post('/logout', 'logout')->name('.logout');

                Route::middleware([
                        'user'
                    ])
                    ->group(function() {

                        Route::put('/change-password', 'changePassword')->name('.change-password')->middleware('decrypt_password');
                    });

                Route::name('.admin')
                    ->prefix('/admin')
                    ->middleware([
                        'administrator'
                    ])
                    ->group(function() {
                        
                        Route::put('/change-password', 'adminChangePassword')->name('.change-password')->middleware('decrypt_password');
                    });
            });

        Route::name('.admin')
            ->prefix('/admin')
            ->group(function() {

                Route::post('/register', 'adminRegister')->name('.register')->middleware('decrypt_password');

                Route::post('/login', 'adminLogin')->name('.login')->middleware('decrypt_password');

                Route::post('/forgot-password', 'adminForgotPassword')->name('.forgot-password');

                Route::put('/reset-password', 'adminResetPassword')->name('.reset-password')->middleware('decrypt_password');
            });
    });
/**
 * End of Authentication Routes
 */


/**
 * Unauthenticated Routes
 */
Route::get('/discover', DiscoverController::class)->name('discover');

Route::get('/popular-podcasts', PopularPodcastsController::class)->name('popular-podcasts');

Route::get('/popular-shows', PopularShowsController::class)->name('popular-shows');

Route::get('/latest-episodes', LatestEpisodesController::class)->name('latest-episodes');

Route::get('/latest-audios', LatestAudiosController::class)->name('latest-audios');

Route::get('/latest-videos', LatestVideosController::class)->name('latest-videos');

Route::get('/top-audios', TopAudiosController::class)->name('top-audios');

Route::get('/top-videos', TopVideosController::class)->name('top-videos');

Route::get('/recent-uploads', RecentUploadsController::class)->name('recent-uploads');

Route::get('/search-contents', SearchContentsController::class)->name('search-contents');

Route::get('/search-podcasts', SearchPodcastsController::class)->name('search-podcasts');

Route::get('/search-shows', SearchShowsController::class)->name('search-shows');

Route::get('/live-radio', [LiveController::class, 'indexRadio'])->name('live-radio.index');
/**
 * End of Unauthenticated Routes
 */


/**
 * General Authenticated Routes
 */
Route::middleware([
    'auth:sanctum'
])
->group(function() {

    Route::put('/profile', ProfileController::class)->name('profile');
});
/**
 * End of General Authenticated Routes
 */


/**
 * User exclusive routes
 */
Route::middleware([
        'auth:sanctum',
        'user'
    ])
    ->group(function() {

        Route::middleware([
            'email.verified'
        ])
        ->group(function() {
            Route::get('/recently-played', RecentlyPlayedController::class)->name('recently-played');

            Route::get('/suggested-podcasts', SuggestedPodcastsController::class)->name('suggested-podcasts');

            Route::get('/suggested-shows', SuggestedShowsController::class)->name('suggested-shows');

            Route::get('/recommended-podcasts', RecommendedPodcastsController::class)->name('recommended-podcasts');

            Route::get('/recommended-shows', RecommendedShowsController::class)->name('recommended-shows');

            Route::get('/recommended-audios', RecommendedAudiosController::class)->name('recommended-audios');

            Route::get('/recommended-videos', RecommendedVideosController::class)->name('recommended-videos');

            Route::get('/featured-podcasts', FeaturedPodcastsController::class)->name('featured-podcasts');

            Route::get('/featured-shows', FeaturedShowsController::class)->name('featured-shows');

            Route::name('queues')
                ->prefix('/queues')
                ->controller(QueueController::class)
                ->group(function() {

                    Route::get('', 'index')->name('.index');
                });

            Route::name('contents')
                ->prefix('/contents')
                ->controller(ContentController::class)
                ->group(function() {

                    Route::post('/{content}/like', 'like')->name('.like');
                    Route::post('/{content}/dislike', 'dislike')->name('.dislike');
                    Route::post('/{content}/queue', 'queue')->name('.queue');
                    Route::post('/{content}/unqueue', 'unqueue')->name('.unqueue');
                    Route::post('/{content}/favourite', 'favourite')->name('.favourite');
                    Route::post('/{content}/unfavourite', 'unfavourite')->name('.unfavourite');
                    Route::post('/{content}/viewed', 'viewed')->name('.viewed');
                    Route::get('/{content}/is-liked', 'isLiked')->name('.is-liked');
                    Route::get('/{content}/is-queued', 'isQueued')->name('.is-queued');
                    Route::get('/{content}/is-favourite', 'isFavourite')->name('.is-favourite');
                    Route::get('/{content}/is-viewed', 'isViewed')->name('.is-viewed');
                    Route::post('/{content}/comments', 'storeComment')->name('.store-comment');
                    Route::put('/{content}/comments/{comment}', 'updateComment')->name('.update-comment');
                    Route::delete('/{content}/comments/{comment}', 'destroyComment')->name('.destroy-comment');
                });

            Route::name('playlists')
                ->prefix('/playlists')
                ->controller(PlaylistController::class)
                ->group(function() {

                    Route::get('', 'index')->name('.index');
                    Route::post('', 'store')->name('.store');
                    Route::get('/{playlistId}', 'show')->name('.show');
                    Route::put('/{playlistId}', 'update')->name('.update');
                    Route::delete('/{playlistId}', 'destroy')->name('.destroy');
                    Route::post('/{playlistId}/add', 'add')->name('.add');
                    Route::post('/{playlistId}/remove', 'remove')->name('.remove');
                    Route::get('/{playlistId}/contents', 'contents')->name('.contents');
                });

            Route::name('favourites')
                ->prefix('/favourites')
                ->controller(FavouriteController::class)
                ->group(function() {

                    Route::get('', 'index')->name('.index');
                });

            Route::name('podcasts')
                ->prefix('/podcasts')
                ->controller(PodcastController::class)
                ->group(function() {

                    Route::get('/following', 'following')->name('.following');
                    Route::post('/{podcast}/follow', 'userFollow')->name('.user-follow');
                    Route::post('/{podcast}/unfollow', 'userUnfollow')->name('.user-unfollow');
                    Route::get('/{podcast}/is-followed', 'isFollowed')->name('.is-followed');
                });

            Route::name('shows')
                ->prefix('/shows')
                ->controller(ShowController::class)
                ->group(function() {

                    Route::get('/following', 'following')->name('.following');
                    Route::post('/{show}/follow', 'userFollow')->name('.user-follow');
                    Route::post('/{show}/unfollow', 'userUnfollow')->name('.user-unfollow');
                    Route::get('/{show}/is-followed', 'isFollowed')->name('.is-followed');
                });
        });
    });
/**
 * End of User exclusive routes
 */


/**
 * Other General Routes
 */
Route::name('live-events')
    ->prefix('/live-events')
    ->controller(LiveEventController::class)
    ->group(function() {

        Route::get('', 'index')->name('.index');
        Route::get('/upcoming', 'upcoming')->name('.upcoming');
        Route::get('/{liveEvent}', 'show')->name('.show');
    });

Route::name('contents')
    ->prefix('/contents')
    ->controller(ContentController::class)
    ->group(function() {

        Route::get('/{content}/comments', 'indexComments')->name('.index-comments');
        Route::get('/{content}/comments/{comment}', 'showComment')->name('.show-comment');
    });

Route::name('podcasts')
    ->prefix('/podcasts')
    ->controller(PodcastController::class)
    ->group(function() {

        Route::get('', 'userIndex')->name('.user-index');
        Route::get('/{podcast}', 'userShow')->name('.user-show');
        Route::get('/{podcast}/contents', 'userContents')->name('.user-contents');
    });

Route::name('shows')
    ->prefix('/shows')
    ->controller(ShowController::class)
    ->group(function() {

        Route::get('', 'userIndex')->name('.user-index');
        Route::get('/{show}', 'userShow')->name('.user-show');
        Route::get('/{show}/contents', 'userContents')->name('.user-contents');
    });

Route::name('categories')
    ->prefix('/categories')
    ->controller(CategoryController::class)
    ->group(function() {

        Route::get('', 'index')->name('.index');
        Route::get('/{category}', 'show')->name('.show');
        Route::get('/{category}/podcasts', 'showPodcasts')->name('.show-podcasts');
        Route::get('/{category}/shows', 'showShows')->name('.show-shows');
    });

Route::name('adverts')
    ->prefix('/adverts')
    ->controller(AdvertController::class)
    ->group(function() {

        Route::get('', 'userIndex')->name('.user-index');
        Route::get('/{advert}', 'userShow')->name('.user-show');
        Route::post('/{advert}/impressions', 'impressions')->name('.impressions');
    });
/**
 * End of Other General Routes
 */


/**
 * Administrator exclusive routes
 */
Route::name('admin')
    ->prefix('/admin')
    ->middleware([
        'auth:sanctum',
        'administrator'
    ])
    ->group(function() {

        Route::post('/feeds', FeedController::class);

        Route::name('.categories')
            ->prefix('/categories')
            ->controller(CategoryController::class)
            ->group(function() {

                Route::post('', 'store')->name('.store');
                Route::post('/{category}', 'update')->name('.update');
                Route::delete('/{category}', 'destroy')->name('.destroy');
            });

        Route::name('.tags')
            ->prefix('/tags')
            ->controller(TagController::class)
            ->group(function() {

                Route::get('', 'index')->name('.index');
                Route::post('', 'store')->name('.store');
                Route::get('/{tag}', 'show')->name('.show');
                Route::put('/{tag}', 'update')->name('.update');
                Route::delete('/{tag}', 'destroy')->name('.destroy');
            });

        Route::name('.contents')
            ->prefix('/contents')
            ->controller(ContentController::class)
            ->group(function() {

                Route::get('', 'index')->name('.index');
                Route::post('', 'store')->name('.store');
                Route::get('/{content}', 'show')->name('.show');
                Route::post('/{content}', 'update')->name('.update');
                Route::delete('/{content}', 'destroy')->name('.destroy');
            });

        Route::name('.podcasts')
            ->prefix('/podcasts')
            ->controller(PodcastController::class)
            ->group(function() {

                Route::get('', 'index')->name('.index');
                Route::post('', 'store')->name('.store');
                Route::get('/{podcast}', 'show')->name('.show');
                Route::post('/{podcast}', 'update')->name('.update');
                Route::delete('/{podcast}', 'destroy')->name('.destroy');
                Route::get('/{podcast}/contents', 'contents')->name('.contents');
            });

        Route::name('.shows')
            ->prefix('/shows')
            ->controller(ShowController::class)
            ->group(function() {

                Route::get('', 'index')->name('.index');
                Route::post('', 'store')->name('.store');
                Route::get('/{show}', 'show')->name('.show');
                Route::post('/{show}', 'update')->name('.update');
                Route::delete('/{show}', 'destroy')->name('.destroy');
                Route::get('/{show}/contents', 'contents')->name('.contents');
            });

        Route::post('/live-radio', [LiveController::class, 'storeRadio'])->name('.live-radio.store');

        Route::name('.live-events')
            ->prefix('/live-events')
            ->controller(LiveEventController::class)
            ->group(function() {

                Route::post('', 'store')->name('.store');
                Route::put('/{liveEvent}', 'update')->name('.update');
                Route::delete('/{liveEvent}', 'destroy')->name('.destroy');
            });

        Route::name('.adverts')
            ->prefix('/adverts')
            ->controller(AdvertController::class)
            ->group(function() {

                Route::get('', 'index')->name('.index');
                Route::post('', 'store')->name('.store');
                Route::get('/{advert}', 'show')->name('.show');
                Route::post('/{advert}', 'update')->name('.update');
                Route::delete('/{advert}', 'destroy')->name('.destroy');
            });
    });
/**
 * End of Administrator exclusive routes
 */