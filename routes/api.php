<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/invites/send', 'Api\InviteController@sendInvite');
Route::post('/users/query-builder', 'Api\UserQueryController@process');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/feed', 'Api\PostController@feed');
    Route::get('/user', 'Api\UserController@userInfo');
    Route::get('/dashboard-notifications', 'Api\UserController@dashboardNotifications');
    Route::get('/notifications', 'Api\UserController@notifications');
    Route::get('/localization', 'Api\SettingController@localization');
    Route::get('/user/people-you-should-know', 'Api\UserController@peopleYouShouldKnow');
    Route::get('/dashboardHeader', 'Api\SettingController@getDashboardHeader');
    Route::post('/comment-save', 'Api\PostController@CommentSave');
    Route::delete('/delete-comment/{id}', 'Api\PostController@deleteComment');

    Route::get('/get-comment/{id}', 'Api\PostController@getComment');
    Route::get('/mobile-links', 'Api\SettingController@getMobileLinks');

    Route::get('/articles', 'Api\ArticlesController@latest');

    Route::get('/groups', 'Api\GroupController@userGroups');
    Route::get('/groups/joinable', 'Api\GroupController@getJoinableGroups');
    Route::get('/groups/{slug}', 'Api\GroupController@show');
    Route::post('/groups/{slug}/join', 'Api\GroupController@join');
    Route::get('/groups/{slug}/reported-posts', 'Api\GroupController@getReportedPosts');
    Route::get('/settings', 'Api\SettingController@getsettings');
    Route::get('/users/admin', 'Api\UserController@isUserAdmin');
    Route::delete('/posts/{post}', 'Api\PostController@delete');
    Route::put('/posts/{post}/report', 'Api\PostController@report');
    Route::put('/posts/{post}/resolve', 'Api\PostController@resolve');
    Route::get('/posts/{post}/likes', 'Api\PostController@likes');
    Route::get('/discussions/{discussion}', 'Api\Groups\DiscussionController@show');
    Route::post('/discussions/{discussion}', 'Api\Groups\DiscussionController@postReply');
    Route::get('/discussions/{discussion}/posts', 'Api\Groups\DiscussionController@getPosts');
    Route::get('/groups/{group}/discussions', 'Api\Groups\DiscussionController@index');
    Route::post('/groups/{group}/posts/{post}/moveUp', 'Api\PostController@moveUp');
    Route::post('/groups/{group}/posts/{post}/moveDown', 'Api\PostController@moveDown');
    Route::post('/groups/{group}/posts/{post}/pin', 'Api\PostController@pin');
    
    Route::get('/pages/{slug?}', 'Api\UserController@pagesShow');
});