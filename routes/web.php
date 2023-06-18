<?php

use App\Post;
use App\ReportedPost;
use \Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/import-excel','ExcelController@uploadUsers');
Route::get('/deleted-import-users','ExcelController@deleteUsers');

Route::get('/seed', function () {
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'DiscussionsNotificationTextChange']);
    return "Database seeding completed successfully";
});

Route::get('/run-seeder/{seeder}', function ($seeder) {
    $seederClass = ucfirst($seeder);
    $seederFile = database_path('seeds/' . $seederClass . '.php');
    if (File::exists($seederFile)) {
        Artisan::call('db:seed', ['--class' => $seederClass]);
        return 'Seeder executed: ' . $seederClass;
    } else {
        return 'Invalid seeder: ' . $seederClass;
    }
});
Route::get('/', function (Request $request) {
    return view('welcome')->with([
        'registration_pages' => \App\RegistrationPage::where('is_welcome_page_accessible', 1)->get(),
        'home_page_images' => \App\HomePageImage::where('lang', ($request->has('locale') ? $request->locale : \Illuminate\Support\Facades\App::getLocale()))->get(),
    ]);
});

Route::get('/spa', function () {
    return view('spa');
})->name('spa');

Route::get('/privacy-policy', function () {
    return view('pages.privacypolicy');
});

Route::get('/users/jumblification', 'Admin\UserController@clearExtraSoftDeletedUsers');

Route::get('/logLeadRedirect', 'HomeController@LogLeadRedirect');

Auth::routes();

Route::post('/users/timezone', 'UserController@updateTimezone');
Route::get('/users/getPrice', 'Auth\RegisterController@getPrice');
Route::get('/purchases', 'ReceiptController@index');
Route::get('/purchases/{receipt}', 'ReceiptController@show');
Route::get('/purchases/{receipt}/export', 'ReceiptController@export');
Route::get('/users/{id}/blocked-users', 'UserController@blockedUsers')->name('blocked-users');
Route::get('/users/{id}/block-user', 'UserController@blockUser')->name('block-user');

Route::get('/addToCalendar', 'Auth\RegisterController@addToCalendar');

Route::get('/logo', 'AssetController@logo');
Route::get('/email-template', 'AssetController@emailTemplate');
Route::post('/user-api/image-uploader', 'Api\UserController@uploadImage');

Route::get('/register/pick', 'Auth\RegisterController@pickRegistration');
Route::get('/register/{slug}', 'Auth\RegisterController@register');
Route::post('/register/{slug}', 'Auth\RegisterController@registerUser');
Route::get('/register/{slug}/checkCoupon', 'Auth\RegisterController@checkCoupon');
Route::get('/signup', 'Auth\RegisterController@openSignup');
Route::post('/signup', 'Auth\RegisterController@createAccount');



Route::get('/contact-us', 'HomeController@contactUs');
Route::post('/contact-us', 'HomeController@contact');

Route::post('/pusher/auth', 'PusherController@auth');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/logout', 'Auth\LoginController@logout');

Route::put('/toggle-like', 'PostController@toggleLike');
Route::put('/comment-like', 'PostController@commentLike');
Route::get('/likes', 'PostController@getLikes');
Route::get('/commentlikes', 'PostController@getcommentLikes');
Route::get('/delete-account/{id}', 'UserController@deleteAccount');
Route::post('/save-token', 'UserController@saveToken')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('save-token');
Route::post('/delete-token', 'UserController@deleteToken')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('delete-token');
Route::get('/verify-token/{token}', 'UserController@verifyNotificationToken')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('verify-token');
Route::post('/send-notification', 'notificationController@sendPushNotification');
Route::post('/users/create', 'Api\UserController@createUser')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/pages/{id}/{slug}', 'Admin\PagesController@showPageOnPopup');
Route::group(['middleware' => ['auth', 'admin']], function () {
    
    Route::get('/admin/terms-and-conditions', 'Admin\TermsConditionsController@index')->name('terms-and-conditions');
    Route::post('/admin/terms-and-conditions/{id}/update', 'Admin\TermsConditionsController@update');

    

    Route::get('/admin/pages', 'Admin\PagesController@index');

    Route::get('/admin/pages/create', 'Admin\PagesController@create');
    Route::post('/admin/pages', 'Admin\PagesController@store');
    Route::get('/admin/pages/{id}/edit', 'Admin\PagesController@edit');
    Route::get('/admin/pages/{id}/destroy', 'Admin\PagesController@destroy');

    Route::get('/admin/pages/{id}/template', 'Admin\PagesController@getTemplate');
    Route::post('/admin/pages/{id}/edit', 'Admin\PagesController@update');
    Route::get('/admin/pages/{id}/{slug}', 'Admin\PagesController@getHtml');
    Route::get('/admin/pages/{id}', 'Admin\PagesController@show');
    
    Route::get('/pages/{slug}', 'Admin\PagesController@ShowMorePages');

    Route::get('/admin/instance-settings', 'Admin\OrganizationController@instanceSettings');
    Route::put('/admin/instance-settings', 'Admin\OrganizationController@updateInstanceSettings');

    Route::get('/admin/system/platform-branding', 'Admin\System\PlatformBranding@edit');
    Route::post('/admin/system/platform-branding', 'Admin\System\PlatformBranding@store');
    Route::get('/admin/system/dashboard-settings', 'Admin\System\DashboardSettings@edit');
    Route::post('/admin/system/dashboard-settings', 'Admin\System\DashboardSettings@store');
    Route::get('/admin/system/feed-post-settings', 'Admin\System\FeedPostSettings@edit');
    Route::post('/admin/system/feed-post-settings', 'Admin\System\FeedPostSettings@store');
    Route::get('/admin/system/payment-configuration', 'Admin\System\PaymentSettings@edit');
    Route::post('/admin/system/payment-configuration', 'Admin\System\PaymentSettings@store');
    Route::get('/admin/system/gdpr-settings', 'Admin\System\GDPRSettings@edit');
    Route::post('/admin/system/gdpr-settings', 'Admin\System\GDPRSettings@store');
    Route::get('/admin/system/feature-settings', 'Admin\System\FeatureNames@edit');
    Route::post('/admin/system/feature-settings', 'Admin\System\FeatureNames@store'); 
    Route::get('/admin/system/homepage-login', 'Admin\System\HomepageLoginSettings@edit');
    Route::post('/admin/system/homepage-login', 'Admin\System\HomepageLoginSettings@store');
    Route::get('/admin/system/profile-options', 'Admin\System\ProfileOptions@edit');
    Route::post('/admin/system/profile-options', 'Admin\System\ProfileOptions@store');

    Route::get('/admin/system/onboarding', 'Admin\System\OnboardingSettings@index');
    Route::get('/admin/system/onboarding/step-preview', 'Admin\System\OnboardingSettings@stepPreview');
    Route::get('/admin/system/onboarding/welcome', 'Admin\System\OnboardingSettings@editWelcome');
    Route::post('/admin/system/onboarding/welcome', 'Admin\System\OnboardingSettings@storeWelcome');
    Route::get('/admin/system/onboarding/intro-video', 'Admin\System\OnboardingSettings@editIntroVideo');
    Route::post('/admin/system/onboarding/intro-video', 'Admin\System\OnboardingSettings@storeIntroVideo');
    Route::get('/admin/system/onboarding/basic', 'Admin\System\OnboardingSettings@editBasic');
    Route::post('/admin/system/onboarding/basic', 'Admin\System\OnboardingSettings@storeBasic');
    Route::get('/admin/system/onboarding/image-bio', 'Admin\System\OnboardingSettings@editImageBio');
    Route::post('/admin/system/onboarding/image-bio', 'Admin\System\OnboardingSettings@storeImageBio');
    Route::get('/admin/system/onboarding/about-me', 'Admin\System\OnboardingSettings@editAboutMe');
    Route::post('/admin/system/onboarding/about-me', 'Admin\System\OnboardingSettings@storeAboutMe');
    Route::get('/admin/system/onboarding/category/{taxonomy}', 'Admin\System\OnboardingSettings@editCategory');
    Route::post('/admin/system/onboarding/category/{taxonomy}', 'Admin\System\OnboardingSettings@storeCategory');
    Route::get('/admin/system/onboarding/questions', 'Admin\System\OnboardingSettings@editQuestions');
    Route::post('/admin/system/onboarding/questions', 'Admin\System\OnboardingSettings@storeQuestions');
    Route::get('/admin/system/onboarding/notifications', 'Admin\System\OnboardingSettings@editNotifications');
    Route::post('/admin/system/onboarding/notifications', 'Admin\System\OnboardingSettings@storeNotifications');
    Route::get('/admin/system/onboarding/groups-selection', 'Admin\System\OnboardingSettings@editGroupsSelection');
    Route::post('/admin/system/onboarding/groups-selection', 'Admin\System\OnboardingSettings@storeGroupsSelection');
    Route::get('/admin/system/onboarding/gdpr', 'Admin\System\OnboardingSettings@editGdpr');
    Route::post('/admin/system/onboarding/gdpr', 'Admin\System\OnboardingSettings@storeGdpr');
    Route::get('/admin/system/onboarding/completed', 'Admin\System\OnboardingSettings@editCompleted');
    Route::post('/admin/system/onboarding/completed', 'Admin\System\OnboardingSettings@storeCompleted');

    Route::get('/admin/dashboard-settings', 'Admin\RegisterController@dashboardSetting');

    Route::post('/admin/update-dashboard-settings', 'Admin\RegisterController@UpdateDashboardSetting');


    Route::get('/admin/registration', 'Admin\RegisterController@index');
    Route::get('/admin/registration/create', 'Admin\RegisterController@create');
    Route::post('/admin/registration/image', 'Admin\RegisterController@uploadImage');
    Route::get('/admin/registration/{registerPage}', 'Admin\RegisterController@show');
    Route::get('/admin/registration/{registerPage}/edit', 'Admin\RegisterController@edit');
    Route::put('/admin/registration/{registerPage}', 'Admin\RegisterController@update');
    Route::get('/admin/registration/{registerPage}/tickets', 'Admin\RegisterController@indexTickets');
    Route::get('/admin/registration/{registerPage}/tickets/new', 'Admin\RegisterController@newTicket');
    Route::post('/admin/registration/{registerPage}/tickets', 'Admin\RegisterController@storeTicket');
    Route::get('/admin/registration/{registerPage}/tickets/{ticket}', 'Admin\RegisterController@showTicket');
    Route::get('/admin/registration/{registerPage}/tickets/{ticket}/edit', 'Admin\RegisterController@editTicket');
    Route::put('/admin/registration/{registerPage}/tickets/{ticket}', 'Admin\RegisterController@updateTicket');
    Route::delete('/admin/registration/{registerPage}/tickets/{ticket}', 'Admin\RegisterController@deleteTicket');
    Route::get('/admin/registration/{registerPage}/purchases', 'Admin\RegisterController@indexPurchases');

    Route::get('/admin/registration/{registerPage}/report', 'Admin\RegisterController@indexRegisterReport');
    Route::get('/admin/report/{registerPage}/export', 'Admin\RegisterController@exportRegisterReport');

    Route::get('/admin/registration/{registerPage}/purchases/export', 'Admin\RegisterController@exportPurchases');
    Route::get('/admin/registration/{registerPage}/purchases/{receipt}', 'Admin\RegisterController@showPurchase');
    Route::put('/admin/purchases/{receipt}/status', 'Admin\RegisterController@changeStatus');
    Route::post('/admin/registration', 'Admin\RegisterController@store');
    Route::delete('/admin/registration/{registerPage}', 'Admin\RegisterController@destroy');

    Route::get('/admin/virtual-rooms/{room}/edit', 'Admin\VirtualRoomController@edit');
    Route::put('/admin/virtual-rooms/{room}/edit', 'Admin\VirtualRoomController@update');
    Route::put('/admin/virtual-rooms/{room}/change-image', 'Admin\VirtualRoomController@changeImageByRoom');
    Route::post('/admin/virtual-rooms/{room}/areas', 'Admin\VirtualRoomController@saveAreasByRoom');

    Route::get('/admin/make-groups', 'Admin\MakeGroupsController@make');
    Route::get('/admin/', 'Admin\OrganizationController@dashboard');
    Route::get('/admin/settings', 'Admin\OrganizationController@settings');
    Route::post('/admin/settings', 'Admin\OrganizationController@updateSettings');




    Route::get('/admin/billing', 'Admin\OrganizationController@billing');
    Route::get('/admin/users', 'Admin\UserController@index')->name('admin.users');
    Route::get('/admin/users/export', 'Admin\UserController@downloadUsersCsv');
    Route::get('/admin/users/invites', 'Admin\InvitesController@index');
    Route::get('/admin/users/invites/create', 'Admin\InvitesController@create');
    Route::post('/admin/users/invites', 'Admin\InvitesController@sendInvite');
    Route::get('/admin/users/invites/bulk-resend', 'Admin\InvitesController@bulkResend');
    Route::post('/admin/users/invites/bulk-resend', 'Admin\InvitesController@postBulkResend');
    Route::get('/admin/users/invites/cleanup', 'Admin\InvitesController@cleanup');
    Route::post('/admin/users/invites/cleanup', 'Admin\InvitesController@postCleanup');
    Route::get('/admin/users/invites/{hash}/resend', 'Admin\InvitesController@resendInvite');
    Route::get('/admin/users/invites/{hash}/delete', 'Admin\InvitesController@delete');
    Route::post('/admin/users/clear', 'Admin\UserController@clearExtraSoftDeletedUsers');

    Route::get('/admin/users/bulk-delete', 'Admin\UserController@BulkDeleteView');
    Route::post('/admin/users/bulkDeleteUsers', 'Admin\UserController@bulkDeleteUsers');
    Route::post('/admin/users/bulk-delete-conform', 'Admin\UserController@BulkDeleteConformation');




    Route::post('/admin/users/{id}/passwordResetLink', 'Admin\UserController@generateResetLink');
    Route::get('/admin/users/{id}/passwordResetLink', 'Api\UserController@getResetLink');
    Route::get('/admin/users/{id}/edit', 'Admin\UserController@edit');
    Route::get('/admin/users/{id}/categories', 'Admin\UserController@editCategories');
    Route::post('/admin/users/{id}/categories', 'Admin\UserController@updateCategories');
    Route::get('/admin/users/{id}', 'Admin\UserController@show');
    Route::put('/admin/users/{id}', 'Admin\UserController@update');
    Route::delete('/admin/users/{id}', 'Admin\UserController@delete');
    Route::post('/admin/users/{user}/auth', 'Admin\UserController@auth');
    Route::post('/admin/users/{user}/restore', 'Admin\UserController@restore');
    Route::post('/admin/users/{user}/notify', 'Admin\UserController@notify');

    Route::get('/admin/users/{user}/groups', 'Admin\UserController@showGroups');
    Route::post('/admin/users/{user}/groups', 'Admin\UserController@updateGroups');
    Route::get('/admin/users/{user}/activity', 'Admin\UserController@showActivity');

    Route::get('/admin/users/{user}/purchases', 'Admin\UserController@indexPurchases');
    Route::get('/admin/users/{user}/purchases/{receipt}', 'Admin\UserController@showReceipt');

    Route::get('/admin/questions/sort', 'Admin\QuestionsController@indexSort');
    Route::put('/admin/questions/sort', 'Admin\QuestionsController@sort');
    Route::resource('/admin/questions', 'Admin\QuestionsController');

    Route::get('/admin/groups', 'Admin\GroupController@index');
    Route::get('/admin/groups/create', 'Admin\GroupController@create');
    Route::get('/admin/groups/sort', 'Admin\GroupController@indexSort');
    Route::put('/admin/groups/sort', 'Admin\GroupController@sort');
    Route::get('/admin/groups/bulk-settings', 'Admin\GroupController@bulkSettings');
    Route::put('/admin/groups/bulk-settings', 'Admin\GroupController@updateBulkSettings');
    Route::get('/admin/groups/configuration', 'Admin\GroupController@configuration');
    Route::post('/admin/groups/configuration', 'Admin\GroupController@storeConfiguration');

    Route::post('/admin/groups/', 'Admin\GroupController@store');
    Route::get('/admin/groups/{id}/settings', 'Admin\GroupController@edit');
    Route::put('/admin/groups/{id}', 'Admin\GroupController@update');
    Route::delete('/admin/groups/{id}', 'Admin\GroupController@delete');
    Route::get('/admin/groups/{id}', 'Admin\GroupController@show');
    Route::put('/admin/groups/{id}/assign', 'Admin\GroupController@assign');
    Route::get('/admin/groups/{id}/users', 'Admin\GroupController@users');
    Route::put('/admin/groups/{id}/users', 'Admin\GroupController@addAllUsers');
    Route::get('/admin/groups/{id}/users/bulk-add', 'Admin\GroupController@bulkAddUsers');
    Route::put('/admin/groups/{id}/users/bulk-add', 'Admin\GroupController@updateBulkUsers');
    Route::get('/admin/groups/{id}/budgets', 'Admin\GroupController@budgets');
    Route::get('/admin/groups/{id}/files', 'Admin\GroupFileController@index');
    Route::get('/admin/groups/{id}/files/{folder}', 'Admin\GroupFileController@folder');
    Route::get('/admin/groups/{id}/files/{file}/download', 'Admin\GroupFileController@downloadFile');
    Route::get('/admin/groups/{id}/subgroups', 'Admin\GroupSubgroupController@index');
    Route::put('/admin/groups/{id}/subgroups/sort', 'Admin\GroupSubgroupController@sort');
    Route::get('/admin/groups/{id}/activity', 'Admin\GroupClicksController@index');
    Route::get('/admin/groups/{id}/activity/{action}','Admin\GroupClicksController@show');
    Route::post('/admin/groups/bulk-add', 'Admin\GroupController@bulkAddUsersToGroup');

    Route::get('/admin/groups/{id}/virtual-room', 'Admin\VirtualRoomController@roomEditor');
    Route::post('/admin/groups/{id}/virtual-room/new', 'Admin\VirtualRoomController@newRoom');
    Route::post('/admin/groups/{id}/virtual-room/areas', 'Admin\VirtualRoomController@saveAreas');
    Route::put('/admin/groups/{id}/virtual-room/change-image', 'Admin\VirtualRoomController@changeImage');

    Route::get('/admin/groups/{id}/lounge', 'Admin\LoungeController@roomEditor');
    Route::put('/admin/groups/{id}/lounge/settings', 'Admin\LoungeController@saveSettings');
    Route::post('/admin/groups/{id}/lounge/areas', 'Admin\LoungeController@saveAreas');
    Route::put('/admin/groups/{id}/lounge/change-image', 'Admin\LoungeController@changeImage');

    Route::get('/admin/budgets', 'Admin\BudgetController@index');
    Route::get('/admin/budgets/create', 'Admin\BudgetController@create');
    Route::post('/admin/budgets/', 'Admin\BudgetController@store');
    Route::get('/admin/budgets/export', 'Admin\BudgetController@export');
    Route::get('/admin/budgets/{id}/edit', 'Admin\BudgetController@edit');
    Route::put('/admin/budgets/{id}', 'Admin\BudgetController@update');
    Route::get('/admin/budgets/{id}', 'Admin\BudgetController@show');
    Route::delete('/admin/budgets/{id}', 'Admin\BudgetController@delete');
    Route::get('/admin/budgets/{id}/expenses/create', 'Admin\BudgetController@addExpense');
    Route::post('/admin/budgets/{id}/expenses', 'Admin\BudgetController@postExpense');
    Route::get('/admin/budgets/{id}/expenses/export', 'Admin\BudgetController@exportExpenses');
    Route::get('/admin/budgets/{id}/expenses/{expense}/edit', 'Admin\BudgetController@editExpense');
    Route::put('/admin/budgets/{id}/expenses/{expense}', 'Admin\BudgetController@updateExpense');
    Route::delete('/admin/budgets/{id}/expenses/{expense}', 'Admin\BudgetController@deleteExpense');

    Route::get('/admin/events', 'Admin\EventController@index');
    Route::get('/admin/events/calendar', 'Admin\EventController@calendar');
    Route::get('/admin/events/create', 'Admin\EventController@create');
    Route::post('/admin/events/create', 'Admin\EventController@store');
    Route::get('/admin/events/{event}', 'Admin\EventController@show');
    Route::get('/admin/events/{event}/edit', 'Admin\EventController@edit');
    Route::put('/admin/events/{event}', 'Admin\EventController@update');
    Route::delete('/admin/events/{event}', 'Admin\EventController@delete');
    Route::post('/admin/events/{event}/restore', 'Admin\EventController@restore');
    Route::put('/admin/events/{event}/users/add', 'Admin\EventController@addUser');
    Route::get('/admin/events/{event}/rsvp-export', 'Admin\EventController@rsvpExport');

    Route::get('/admin/categories/merge', 'Admin\CategoryController@indexMergables');
    Route::put('/admin/categories/merge', 'Admin\CategoryController@merge');

    Route::get('/admin/categories/approval', 'Admin\CategoryController@approval');
    Route::put('/admin/categories/approve', 'Admin\CategoryController@approve');

    Route::get('/admin/categories/expense-categories', 'Admin\ExpenseCategoryController@index');
    Route::get('/admin/categories/expense-categories/create', 'Admin\ExpenseCategoryController@create');
    Route::post('/admin/categories/expense-categories', 'Admin\ExpenseCategoryController@store');
    Route::get('/admin/categories/expense-categories/{id}/edit', 'Admin\ExpenseCategoryController@edit');
    Route::put('/admin/categories/expense-categories/{id}', 'Admin\ExpenseCategoryController@update');
    Route::delete('/admin/categories/expense-categories/{id}', 'Admin\ExpenseCategoryController@delete');

    Route::get('/admin/categories/titles', 'Admin\TitleController@index');
    Route::get('/admin/categories/titles/create', 'Admin\TitleController@create');
    Route::post('/admin/categories/titles/', 'Admin\TitleController@store');
    Route::get('/admin/categories/titles/{id}/edit', 'Admin\TitleController@edit');
    Route::put('/admin/categories/titles/{id}', 'Admin\TitleController@update');
    Route::delete('/admin/categories/titles/{id}', 'Admin\TitleController@delete');

    Route::get('/admin/categories/departments', 'Admin\DepartmentController@index');
    Route::get('/admin/categories/departments/create', 'Admin\DepartmentController@create');
    Route::post('/admin/categories/departments/', 'Admin\DepartmentController@store');
    Route::get('/admin/categories/departments/{id}/edit', 'Admin\DepartmentController@edit');
    Route::put('/admin/categories/departments/{id}', 'Admin\DepartmentController@update');
    Route::delete('/admin/categories/departments/{id}', 'Admin\DepartmentController@delete');

    Route::get('/admin/categories', 'Admin\TaxonomiesController@index');
    Route::get('/admin/categories/create', 'Admin\TaxonomiesController@create');
    Route::post('/admin/categories', 'Admin\TaxonomiesController@store');
    Route::get('/admin/categories/sort', 'Admin\TaxonomiesController@sortTaxonomies');
    Route::put('/admin/categories/sort', 'Admin\TaxonomiesController@updateSortTaxonomies');
    Route::get('/admin/categories/{taxonomy}', 'Admin\TaxonomiesController@show');
    Route::get('/admin/categories/{taxonomy}/add-users', 'Admin\TaxonomiesController@addUsers');
    Route::post('/admin/categories/{taxonomy}/add-users', 'Admin\TaxonomiesController@postAddUsers');
    Route::get('/admin/categories/{taxonomy}/sort', 'Admin\TaxonomiesController@sort');
    Route::put('/admin/categories/{taxonomy}/sort', 'Admin\TaxonomiesController@updateSort');
    Route::put('/admin/categories/{taxonomy}/sort/copy', 'Admin\TaxonomiesController@copySort');
    Route::put('/admin/categories/{taxonomy}/sort/alphabetize', 'Admin\TaxonomiesController@alphabetize');
    Route::get('/admin/categories/{taxonomy}/edit', 'Admin\TaxonomiesController@edit');
    Route::put('/admin/categories/{taxonomy}', 'Admin\TaxonomiesController@update');
    Route::delete('/admin/categories/{taxonomy}', 'Admin\TaxonomiesController@destroy');
    Route::get('/admin/categories/{taxonomy}/groupings', 'Admin\TaxonomiesController@editGroupings');
    Route::put('/admin/categories/{taxonomy}/groupings', 'Admin\TaxonomiesController@updateGroupings');

    Route::get('/admin/categories/{taxonomy}/custom-group-sort', 'Admin\TaxonomiesController@custom_group_sort');
    Route::put('/admin/categories/{taxonomy}/custom-group-sort', 'Admin\TaxonomiesController@updateSort');

    Route::put('/admin/categories/{taxonomy}/sort/alphabetizecustomgroup', 'Admin\TaxonomiesController@alphabetizecustomgroup');

    Route::get('/admin/options/create', 'Admin\OptionsController@create');
    Route::post('/admin/options', 'Admin\OptionsController@store');
    Route::get('/admin/options/{option}/edit', 'Admin\OptionsController@edit');
    Route::put('/admin/options/{option}', 'Admin\OptionsController@update');
    Route::delete('/admin/options/{option}', 'Admin\OptionsController@destroy');

    Route::get('/admin/badges', 'Admin\BadgeController@index');
    Route::get('/admin/badges/create', 'Admin\BadgeController@create');
    Route::post('/admin/badges/', 'Admin\BadgeController@store');
    Route::get('/admin/badges/{id}/edit', 'Admin\BadgeController@edit');
    Route::put('/admin/badges/{id}', 'Admin\BadgeController@update');
    Route::delete('/admin/badges/{id}', 'Admin\BadgeController@delete');

    Route::get('/admin/points', 'Admin\PointsController@index');
    Route::put('/admin/points', 'Admin\PointsController@update');

    Route::get('/admin/content', 'Admin\ContentController@index');
    Route::get('/admin/content/articles/add', 'Admin\ContentController@addArticle');
    Route::post('/admin/content/articles', 'Admin\ContentController@storeArticle');
    Route::post('/admin/content/articles/fetch', 'Admin\ContentController@fetch');
    Route::get('/admin/content/articles/export', 'Admin\ContentController@export');
    Route::get('/admin/content/articles/{id}', 'Admin\ContentController@showArticle');
    Route::get('/admin/content/articles/{id}/edit', 'Admin\ContentController@editArticle');
    Route::put('/admin/content/articles/{id}', 'Admin\ContentController@updateArticle');
    Route::delete('/admin/content/articles/{id}', 'Admin\ContentController@deleteArticle');
    Route::get('/admin/content/feeds', 'Admin\ContentController@feeds');
    Route::get('/admin/content/feeds/create', 'Admin\ContentController@createFeed');
    Route::post('/admin/content/feeds/', 'Admin\ContentController@storeFeed');
    Route::get('/admin/content/feeds/{feed}/edit', 'Admin\ContentController@editFeed');
    Route::put('/admin/content/feeds/{feed}', 'Admin\ContentController@updateFeed');
    Route::delete('/admin/content/feeds/{feed}', 'Admin\ContentController@deleteFeed');

    Route::get('/admin/emails/', 'Admin\EmailController@overview');
    Route::get('/admin/emails/notifications', 'Admin\EmailNotificationController@index');
    Route::get('/admin/emails/notifications/{notification}', 'Admin\EmailNotificationController@show');
    Route::get('/admin/emails/notifications/{notification}/edit', 'Admin\EmailNotificationController@edit');
    Route::post('/admin/emails/notifications/{notification}', 'Admin\EmailNotificationController@update');
    Route::get('/admin/emails/notifications/{notification}/template', 'Admin\EmailNotificationController@getTemplate');
    Route::get('/admin/emails/notifications/{notification}/html', 'Admin\EmailNotificationController@getHtml');

    Route::get('/admin/notifications/push', 'Admin\PushNotificationController@index');
    Route::get('/admin/notifications/push/{notification}/edit', 'Admin\PushNotificationController@edit');
    Route::put('/admin/notifications/push/{notification}', 'Admin\PushNotificationController@update');

    Route::get('/admin/reported', 'Admin\ReportedController@index');
    Route::get('/admin/reported/resolved', 'Admin\ReportedController@resolved');
    Route::get('/admin/posts', 'Admin\PostController@indexPosts');
    Route::get('/admin/posts/scheduled', 'Admin\PostController@indexScheduledPosts');
    Route::get('/admin/posts/create', 'Admin\PostController@create');
    Route::post('/admin/posts', 'Admin\PostController@store');
    Route::get('/admin/posts/{post}/edit', 'Admin\PostController@edit');
    Route::put('/admin/posts/{post}', 'Admin\PostController@update');

    Route::get('/admin/emails/campaigns', 'Admin\EmailCampaignController@index');
    Route::get('/admin/emails/campaigns/create', 'Admin\EmailCampaignController@create');
    Route::post('/admin/emails/campaigns', 'Admin\EmailCampaignController@store');
    Route::get('/admin/emails/campaigns/{campaign}/edit', 'Admin\EmailCampaignController@edit');
    Route::post('/admin/emails/campaigns/{campaign}', 'Admin\EmailCampaignController@update');
    Route::get('/admin/emails/campaigns/{campaign}', 'Admin\EmailCampaignController@show');
    Route::get('/admin/emails/campaigns/{campaign}/template', 'Admin\EmailCampaignController@getTemplate');
    Route::get('/admin/emails/campaigns/{campaign}/html', 'Admin\EmailCampaignController@getHtml');
    Route::get('/admin/emails/campaigns/{campaign}/send', 'Admin\EmailCampaignController@send');
    Route::post('/admin/emails/campaigns/{campaign}/review', 'Admin\EmailCampaignController@review');
    Route::post('/admin/emails/campaigns/{campaign}/send', 'Admin\EmailCampaignController@postSend');
    Route::get('/admin/emails/campaigns/{campaign}/schedule', 'Admin\EmailCampaignController@schedule');
    Route::get('/admin/emails/campaigns/{campaign}/duplicate', 'Admin\EmailCampaignController@duplicate');
    Route::delete('/admin/emails/campaigns/{campaign}', 'Admin\EmailCampaignController@delete');

    Route::get('/admin/emails/welcome', 'Admin\EmailWelcomeController@index');
    Route::get('/admin/emails/welcome/create', 'Admin\EmailWelcomeController@create');
    Route::get('/admin/emails/welcome/{email}', 'Admin\EmailWelcomeController@show');
    Route::get('/admin/emails/welcome/{email}/edit', 'Admin\EmailWelcomeController@edit');
    Route::post('/admin/emails/welcome/{email}', 'Admin\EmailWelcomeController@update');
    Route::delete('/admin/emails/welcome/{email}', 'Admin\EmailWelcomeController@delete');
    Route::post('/admin/emails/welcome', 'Admin\EmailWelcomeController@store');
    Route::get('/admin/emails/welcome/{email}/template', 'Admin\EmailWelcomeController@getTemplate');
    Route::get('/admin/emails/welcome/{email}/html', 'Admin\EmailWelcomeController@getHtml');

    Route::post('/admin/image-uploader/', 'Admin\EmailController@uploadImage');

    Route::get('/admin/segments/', 'Admin\SegmentController@index');
    Route::get('/admin/segments/create', 'Admin\SegmentController@create');
    Route::post('/admin/segments', 'Admin\SegmentController@store');
    Route::get('/admin/segments/{segment}', 'Admin\SegmentController@show');
    Route::delete('/admin/segments/{segment}', 'Admin\SegmentController@delete');
    Route::get('/admin/segments/{segment}/edit', 'Admin\SegmentController@edit');
    Route::get('/admin/exports/{export}/download', 'Admin\SegmentController@downloadExport');
    Route::post('/admin/segments/{segment}/export/start', 'Admin\SegmentController@startExport');
    Route::get('/admin/exports/{export}/check', 'Admin\SegmentController@checkIfExportCompleted');
    Route::put('/admin/segments/{segment}', 'Admin\SegmentController@update');
    Route::get('/admin/segments/{segment}/members', 'Admin\SegmentController@members');
    Route::get('/admin/segments/{segment}/behavior/', 'Admin\SegmentController@behavior');
    Route::get('/admin/segments/{segment}/demographics', 'Admin\SegmentController@demographics');
    Route::get('/admin/segments/{segment}/demographics/groups/', 'Admin\Reports\DemographicController@indexGroups');
    Route::get('/admin/segments/{segment}/demographics/groups/{group}', 'Admin\Reports\DemographicController@showGroup');
    Route::get('/admin/segments/{segment}/demographics/departments/', 'Admin\Reports\DemographicController@indexDepartments');
    Route::get('/admin/segments/{segment}/demographics/departments/{department}', 'Admin\Reports\DemographicController@showDepartment');
    Route::get('/admin/segments/{segment}/demographics/taxonomies/{taxonomy}', 'Admin\Reports\DemographicController@indexTaxonomyOptions');
    Route::get('/admin/segments/{segment}/demographics/taxonomies/{taxonomy}/options/{option}', 'Admin\Reports\DemographicController@showTaxonomyOption');
    Route::get('/admin/segments/{segment}/demographics/mentors/', 'Admin\Reports\DemographicController@indexMentors');
    Route::get('/admin/segments/{segment}/demographics/mentors/{type}', 'Admin\Reports\DemographicController@showMentor');
    Route::get('/admin/segments/{segment}/demographics/introductions/', 'Admin\Reports\DemographicController@indexIntroductions');
    Route::get('/admin/segments/{segment}/demographics/introductions/{type}', 'Admin\Reports\DemographicController@showIntroduction');
    Route::get('/admin/segments/{segment}/demographics/titles/{title}', 'Admin\Reports\DemographicController@indexTitle');
    Route::get('/admin/segments/{segment}/demographics/titles/{title}/users/{user}', 'Admin\Reports\DemographicController@showTitleBreakdown');

    Route::get('/admin/api/behavior-analytics', 'Admin\SegmentController@demographicsApi');

    Route::get('/admin/ideations', 'Admin\IdeationController@index');
    Route::get('/admin/ideations/create', 'Admin\IdeationController@create');
    Route::post('/admin/ideations', 'Admin\IdeationController@store');
    Route::get('/admin/ideations/closed', 'Admin\IdeationController@closed');
    Route::get('/admin/ideations/approval', 'Admin\IdeationController@approvalQueue');
    Route::put('/admin/ideations/approve', 'Admin\IdeationController@approve');
    Route::put('/admin/ideations/reject', 'Admin\IdeationController@reject');
    Route::put('/admin/ideations/{ideation}/restore', 'Admin\IdeationController@restore');
    Route::get('/admin/ideations/{ideation}/edit', 'Admin\IdeationController@edit');
    Route::get('/admin/ideations/{ideation}/files', 'Admin\IdeationController@files');
    Route::delete('/admin/ideations/{ideation}/files/{file}', 'Admin\IdeationController@deleteFile');
    Route::get('/admin/ideations/{ideation}/members', 'Admin\IdeationController@members');
    Route::get('/admin/ideations/{ideation}/members/invite', 'Admin\IdeationController@invite');
    Route::post('/admin/ideations/{ideation}/members/invite', 'Admin\IdeationController@sendInvite');
    Route::delete('/admin/ideations/{ideation}/members/{user}', 'Admin\IdeationController@removeMember');
    Route::get('/admin/ideations/{ideation}/invitations', 'Admin\IdeationController@invitations');
    Route::delete('/admin/ideations/{ideation}/invitations/{user}', 'Admin\IdeationController@removeInvitation');
    Route::put('/admin/ideations/{ideation}', 'Admin\IdeationController@update');
    Route::get('/admin/ideations/{ideation}', 'Admin\IdeationController@show');

    Route::get('/admin/tutorials', 'Admin\TutorialController@index');
    Route::get('/admin/tutorials/{tutorial}/edit', 'Admin\TutorialController@edit');
    Route::put('/admin/tutorials/{tutorial}', 'Admin\TutorialController@update');

    Route::get('/admin/mobile', 'Admin\MobileLinkController@index');
    Route::get('/admin/mobile/{mobile}/edit', 'Admin\MobileLinkController@edit');
    Route::put('/admin/mobile/{mobile}', 'Admin\MobileLinkController@update');
});

Route::get('/zoom/signature', 'ZoomMeetingController@getSignature');
Route::get('/zoom/closing', 'ZoomMeetingController@showClosing');
Route::get('/zoom/{meetingId}', 'ZoomMeetingController@get');

Route::get('/chat-rooms/{room}/messages', 'ChatRoomsController@loadMessages');
Route::put('/chat-rooms/{room}/messages', 'ChatRoomsController@newMessage');

Route::get('/article/{id}', 'HomeController@getArticle');

Route::get('/notifications', 'NotificationController@index');
Route::post('/notifications/mark-all-as-read', 'NotificationController@markAllAsRead');

Route::get('/events/{event}', 'CalendarController@show');

Route::get('/profile', 'UserController@profile');
Route::post('/profile', 'UserController@update');
Route::get('/account', 'UserController@account');
Route::post('/account/push-notification/{id}', 'UserController@pushNotification');
Route::put('/account', 'UserController@updateAccount');
Route::put('/gdpr', 'UserController@updateGDPR');
Route::put('/account/email', 'UserController@updateEmail');
Route::get('/account/email/{key}', 'UserController@verifyEmail');
Route::get('/users/{id}', 'UserController@show');
Route::get('/my-points', 'UserController@points');
Route::get('/my-profile', 'UserController@redirectToShowUser');




Route::get('/shoutouts/sent', 'ShoutoutsController@sent');
Route::get('/shoutouts/received', 'ShoutoutsController@received');
Route::get('/shoutouts/create', 'ShoutoutsController@create');
Route::post('/shoutouts', 'ShoutoutsController@store');

Route::get('/search', 'SearchController@search');

Route::get('/mentors/ask', 'MentorController@ask');

Route::get('/invite/{slug}', 'InviteController@show');
Route::post('/invite/{slug}', 'InviteController@accept');

Route::get('/calendar', 'CalendarController@index');

Route::get('/messages', 'MessageController@index');
Route::get('/messages/new', 'MessageController@create');
Route::post('/messages', 'MessageController@send');
Route::get('/messages/{id}', 'MessageController@show');
Route::post('/messages/{id}', 'MessageController@reply');
Route::get('/messages/{id}/delete', 'MessageController@delete');
Route::get('/messages/{id}/undo-delete', 'MessageController@undoDelete');
Route::get('/messages/{id}/status/{time}', 'Api\MessageController@checkForNewMessages');

Route::get('/video-room/{slug}', 'VideoRoomsController@show');

Route::get('/onboarding', 'UserController@wizard');

Route::get('/new-onboarding', 'UserController@newwizard');

Route::post('/new-onboarding', 'UserController@submitWizard');

Route::get('/test', 'UserController@test');


Route::post('/onboarding', 'UserController@submitWizard');

Route::post('/options', 'CategoryController@store');

Route::get('/browse', 'UserController@browse');

Route::get('/ideations', 'IdeationController@index');
Route::post('/ideations', 'IdeationController@store');
Route::get('/ideations/invited', 'IdeationController@invited');
Route::get('/ideations/joined', 'IdeationController@joined');
Route::get('/ideations/propose', 'IdeationController@propose');
Route::get('/ideations/proposed', 'IdeationController@listProposed');
Route::post('/ideations/propose', 'IdeationController@submitProposal');
Route::get('/ideations/create', 'IdeationController@create');
Route::get('/ideations/{ideation}', 'IdeationController@show');
Route::put('/ideations/{ideation}', 'IdeationController@update');
Route::get('/ideations/{ideation}/review', 'IdeationController@review');
Route::post('/ideations/{ideation}/approve', 'IdeationController@approve');
Route::post('/ideations/{ideation}/decline', 'IdeationController@decline');
Route::get('/ideations/{ideation}/edit', 'IdeationController@edit');
Route::get('/ideations/{ideation}/files', 'IdeationController@filesIndex');
Route::post('/ideations/{ideation}/files', 'IdeationController@uploadFile');
Route::delete('/ideations/{ideation}/files/{file}', 'IdeationController@deleteFile');
Route::get('/ideations/{ideation}/files/{file}/download', 'IdeationController@downloadFile');
Route::get('/ideations/{ideation}/articles', 'IdeationController@articlesIndex');
Route::post('/ideations/{ideation}/articles', 'IdeationController@addArticle');
Route::delete('/ideations/{ideation}/articles/{article}/delete', 'IdeationController@deleteArticle');
Route::put('/ideations/{ideation}/articles/{article}/report', 'IdeationController@reportArticle');
Route::put('/ideations/{ideation}/articles/{article}/resolve', 'IdeationController@resolveArticle');
Route::get('/ideations/{ideation}/surveys', 'IdeationController@surveysIndex');
Route::post('/ideations/{ideation}/surveys', 'IdeationController@addSurvey');
Route::delete('/ideations/{ideation}/surveys/{survey}/delete', 'IdeationController@deleteSurvey');
Route::get('/ideations/{ideation}/members', 'IdeationController@membersIndex');
Route::get('/ideations/{ideation}/video', 'IdeationController@videoConference');
Route::delete('/ideations/{ideation}/members/{member}/remove', 'IdeationController@removeMember');
Route::post('/ideations/{ideation}/invite', 'IdeationController@invite');
Route::post('/ideations/{ideation}/join', 'IdeationController@join');
Route::post('/ideations/{ideation}/leave', 'IdeationController@leave');
Route::delete('/ideations/{ideation}/delete', 'IdeationController@delete');
Route::post('/ideations/{ideation}/reply', 'IdeationController@postReply');
Route::get('/ideations/{ideation}/posts/{post}/edit', 'IdeationController@editPost');
Route::put('/ideations/{ideation}/posts/{post}/report', 'IdeationController@report');
Route::put('/ideations/{ideation}/posts/{post}/resolve', 'IdeationController@resolve');
Route::put('/ideations/{ideation}/posts/{post}', 'IdeationController@updatePost');
Route::delete('/ideations/{ideation}/posts/{post}/delete', 'IdeationController@deletePost');
Route::get('/ideations/{ideation}/viewInvitation', 'IdeationController@viewInvitation');

Route::get('/introductions/new', 'IntroductionController@create');
Route::post('/introductions', 'IntroductionController@store');
Route::get('/introductions/', 'IntroductionController@index');
Route::get('/introductions/received', 'IntroductionController@received');
Route::get('/introductions/sent', 'IntroductionController@sent');
Route::get('/introductions/{introduction}', 'IntroductionController@show');
Route::get('/introductions/{introduction}/edit', 'IntroductionController@edit');
Route::put('/introductions/{introduction}', 'IntroductionController@update');
Route::delete('/introductions/{introduction}', 'IntroductionController@destroy');



Route::get('/my-groups', 'GroupController@index');
Route::get('/groups/{group}', 'GroupController@show')->name('group_home');
Route::get('/groups/{group}/register', 'GroupController@register');
Route::post('/groups/{group}/register', 'GroupController@postRegister');
Route::post('/groups/join', 'GroupController@joinWithCode');
Route::get('/groups/{group}/sequence', 'Group\SequenceController@show');
Route::get('/groups/{group}/sequence/new', 'Group\SequenceController@createModule');
Route::get('/groups/{group}/sequence/reorder', 'Group\SequenceController@reorderModules');
Route::post('/groups/{group}/sequence/reorder', 'Group\SequenceController@postReorderModules');
Route::post('/groups/{group}/sequence/modules', 'Group\SequenceController@storeModule');
Route::get('/groups/{group}/sequence/modules/{module}', 'Group\SequenceController@showModule');
Route::delete('/groups/{group}/sequence/modules/{module}', 'Group\SequenceController@deleteModule');
Route::get('/groups/{group}/sequence/modules/{module}/edit', 'Group\SequenceController@editModule');
Route::post('/groups/{group}/sequence/modules/{module}', 'Group\SequenceController@updateModule');
Route::post('/groups/{group}/sequence/modules/{module}/completed', 'Group\SequenceController@markModuleCompleted');
Route::post('/groups/{group}/sequence/modules/{module}/uncomplete', 'Group\SequenceController@markModuleIncomplete');
Route::post('/groups/{group}/sequence', 'GroupController@createSequence');
Route::put('/groups/{group}/sequence', 'GroupController@updateSequence');
Route::get('/groups/{group}/sequence/reminders', 'Group\SequenceReminderController@index');
Route::get('/groups/{group}/sequence/reminders/create', 'Group\SequenceReminderController@create');
Route::get('/groups/{group}/sequence/reminders/{reminder}', 'Group\SequenceReminderController@show');
Route::get('/groups/{group}/sequence/reminders/{reminder}/template', 'Group\SequenceReminderController@template');
Route::post('/groups/{group}/sequence/reminders', 'Group\SequenceReminderController@store');
Route::get('/groups/{group}/sequence/reminders/{reminder}/edit', 'Group\SequenceReminderController@edit');
Route::post('/groups/{group}/sequence/reminders/{reminder}', 'Group\SequenceReminderController@update');
Route::get('/groups/{group}/edit', 'GroupController@edit');
Route::put('/groups/{group}/edit', 'GroupController@update');
Route::get('/groups/{group}/edit-virtual-room', 'GroupController@editVirtualRoom');
Route::post('/groups/{group}/edit-virtual-room/new', 'GroupController@newRoom');
Route::put('/groups/{group}/edit-virtual-room/change-image', 'GroupController@changeImage');
Route::post('/groups/{group}/edit-virtual-room/areas', 'GroupController@saveAreas');
Route::get('/groups/{group}/edit-lounge', 'GroupController@editLounge');
Route::post('/groups/{group}/edit-lounge/new', 'GroupController@newLounge');
Route::put('/groups/{group}/edit-lounge/change-image', 'GroupController@changeLoungeImage');
Route::post('/groups/{group}/edit-lounge/areas', 'GroupController@saveLoungeAreas');
Route::get('/groups/{group}/subgroups', 'GroupController@subgroupsIndex');
Route::get('/groups/{group}/subgroups/{subgroup}/log', 'GroupController@logClickedSubgroup');
Route::get('/groups/{group}/posts', 'Group\PostController@index');
Route::get('/groups/{group}/posts/new', 'Group\PostController@create'); 
Route::post('/groups/{group}/posts', 'Group\PostController@store');
Route::get('/groups/{group}/posts/select-type', 'Group\PostController@selectType');
Route::put('/groups/{group}/posts/{post}/report', 'Group\PostController@report');
Route::put('/groups/{group}/posts/{post}', 'Group\PostController@update');
Route::get('/groups/{group}/posts/{post}/edit', 'Group\PostController@edit');
Route::get('/groups/{group}/posts/{post}', 'Group\PostController@show');
Route::post('/groups/{group}/posts/{post}/pin', 'Group\PostController@pin');
Route::get('/groups/{group}/posts/{post}/log', 'Group\PostController@logClick');
Route::put('/groups/{group}/discussions/{discussion}/report', 'Group\DiscussionController@report');
Route::put('/groups/{group}/posts/{post}/resolve', 'Group\PostController@resolve');
Route::put('/groups/{group}/discussions/{discussion}/resolve', 'Group\DiscussionController@resolve');
Route::delete('/groups/{group}/posts/{post}', 'Group\PostController@delete');
Route::delete('/groups/{group}/posts/{post}/delete', 'Group\PostController@delete');
Route::post('/groups/{group}/posts/{post}/moveUp', 'Group\PostController@moveUp');
Route::post('/groups/{group}/posts/{post}/moveDown', 'Group\PostController@moveDown');
Route::get('/groups/{group}/flagged', 'Group\PostController@indexReported');
Route::get('/groups/{group}/resolved', 'Group\PostController@indexResolved');

Route::post('/groups/{group}/join', 'Group\MemberController@join');

Route::get('/groups/{group}/shoutouts', 'Group\ShoutoutController@index');
Route::get('/groups/{group}/shoutouts/new', 'Group\ShoutoutController@create');
Route::post('/groups/{group}/shoutouts', 'Group\ShoutoutController@store');

Route::get('/groups/{group}/shoutouts/{id}/edit', 'Group\ShoutoutController@edit');
Route::post('/groups/{group}/UpdateShoutout/{id}', 'Group\ShoutoutController@UpdateShoutout');

Route::get('/groups/{group}/discussions', 'Group\DiscussionController@index');
Route::get('/groups/{group}/discussions/create', 'Group\DiscussionController@create');
Route::post('/groups/{group}/discussions', 'Group\DiscussionController@store');
Route::get('/groups/{group}/discussions/{slug}', 'Group\DiscussionController@show');
Route::put('/groups/{group}/discussions/{slug}', 'Group\DiscussionController@update');
Route::get('/groups/{group}/discussions/{slug}/posts/{post}/edit', 'Group\DiscussionController@editPost');
Route::put('/groups/{group}/discussions/{slug}/posts/{post}', 'Group\DiscussionController@updatePost');
Route::delete('/groups/{group}/discussions/{slug}/posts/{post}/delete', 'Group\DiscussionController@deletePost');
Route::post('/groups/{group}/discussions/{slug}/posts/{post}/flag', 'Group\DiscussionController@flagPost');
Route::post('/groups/{group}/discussions/{slug}/posts/{post}/resolve', 'Group\DiscussionController@resolve');
Route::get('/groups/{group}/discussions/{slug}/edit', 'Group\DiscussionController@edit');
Route::post('/groups/{group}/discussions/{slug}/reply', 'Group\DiscussionController@postReply');
Route::delete('/groups/{group}/discussions/{slug}/delete', 'Group\DiscussionController@deleteThread');

Route::post('/articles/fetch', 'Admin\ContentController@fetch');
Route::get('/content/{article}/log', 'ContentController@logClick');
Route::get('/groups/{group}/content', 'Group\ContentController@index');
Route::get('/groups/{group}/content/{content}/log', 'Group\ContentController@log');
Route::get('/groups/{group}/content/{content}/edit', 'Group\ContentController@edit');
Route::put('/groups/{group}/content/{content}', 'Group\ContentController@update');
Route::get('/groups/{group}/content/add', 'Group\ContentController@add');
Route::get('/groups/{group}/content/export', 'Group\ContentController@export');
Route::post('/groups/{group}/content', 'Group\ContentController@store');

Route::get('/groups/{group}/calendar', 'Group\EventController@index');
Route::get('/groups/{group}/events/new', 'Group\EventController@create');
Route::post('/groups/{group}/events', 'Group\EventController@store');
Route::get('/groups/{group}/events/{event}', 'Group\EventController@show');
Route::get('/groups/{group}/events/{event}/rsvp-export', 'Group\EventController@rsvpExport');
Route::post('/groups/{group}/events/{event}/rsvp', 'Group\EventController@rsvp');
Route::post('/groups/{group}/events/{event}/waitlist', 'Group\EventController@waitlist');
Route::get('/groups/{group}/events/{event}/edit', 'Group\EventController@edit');
Route::put('/groups/{group}/events/{event}/cancel', 'Group\EventController@cancel');
Route::put('/groups/{group}/events/{event}', 'Group\EventController@update');
Route::delete('/groups/{group}/events/{event}', 'Group\EventController@delete');

Route::get('/groups/{group}/lounge', 'Group\LoungeController@show');

Route::get('/groups/{group}/members', 'Group\MemberController@index');
Route::get('/groups/{group}/members/add', 'Group\MemberController@add');
Route::post('/groups/{group}/members/add', 'Group\MemberController@addUser');
Route::get('/groups/{group}/members/manage', 'Group\MemberController@manage');
Route::get('/groups/{group}/members/toggle-admin', 'Group\MemberController@toggleAdmin');
Route::get('/groups/{group}/members/remove', 'Group\MemberController@remove');

Route::post('/groups/image-uploader/', 'GroupController@uploadImage');

Route::get('/groups/{group}/email-campaigns', 'Group\EmailCampaignsController@index');
Route::get('/groups/{group}/email-campaigns/create', 'Group\EmailCampaignsController@create');
Route::post('/groups/{group}/email-campaigns', 'Group\EmailCampaignsController@store');
Route::get('/groups/{group}/email-campaigns/{campaign}', 'Group\EmailCampaignsController@show');
Route::get('/groups/{group}/email-campaigns/{campaign}/edit', 'Group\EmailCampaignsController@edit');
Route::put('/groups/{group}/email-campaigns/{campaign}', 'Group\EmailCampaignsController@update');
Route::get('/groups/{group}/email-campaigns/{campaign}/html', 'Group\EmailCampaignsController@html');
Route::get('/groups/{group}/email-campaigns/{campaign}/template', 'Group\EmailCampaignsController@template');
Route::get('/groups/{group}/email-campaigns/{campaign}/select-recipients', 'Group\EmailCampaignsController@selectRecipients');
Route::get('/groups/{group}/email-campaigns/{campaign}/schedule', 'Group\EmailCampaignsController@schedule');
Route::put('/groups/{group}/email-campaigns/{campaign}/review', 'Group\EmailCampaignsController@review');
Route::put('/groups/{group}/email-campaigns/{campaign}/send', 'Group\EmailCampaignsController@send');

Route::get('/groups/{group}/files', 'Group\FileController@index');
Route::post('/groups/{group}/files/folders', 'Group\FileController@storeFolder');
Route::get('/groups/{group}/files/{folder}', 'Group\FileController@folder');
Route::post('/groups/{group}/files/upload', 'Group\FileController@upload');
Route::get('/groups/{group}/files/{id}/download', 'Group\FileController@download');
Route::delete('/groups/{group}/files/{id}', 'Group\FileController@deleteFile');
Route::delete('/groups/{group}/folders/{id}', 'Group\FileController@deleteFolder');

Route::get('/groups/{group}/budgets', 'Group\BudgetController@index');
Route::get('/groups/{group}/budgets/{id}', 'Group\BudgetController@show');
Route::get('/groups/{group}/budgets/{id}/expenses/create', 'Group\BudgetController@addExpense');
Route::post('/groups/{group}/budgets/{id}/expenses', 'Group\BudgetController@saveExpense');
Route::get('/groups/{group}/budgets/{id}/expenses/{expense}/edit', 'Group\BudgetController@editExpense');
Route::get('/groups/{group}/budgets/{id}/expenses/{expense}/download', 'Group\BudgetController@downloadExpenseReceipt');
Route::put('/groups/{group}/budgets/{id}/expenses/{expense}', 'Group\BudgetController@updateExpense');
Route::delete('/groups/{group}/budgets/{id}/expenses/{expense}', 'Group\BudgetController@deleteExpense');

Route::get('/groups/{group}/reports/demographics', 'Group\ReportController@demographics');
Route::get('/groups/{group}/reports/behavior', 'Group\ReportController@behavior');
Route::get('/api/groups/behavior-analytics', 'Group\ReportController@demographicsApi');

Route::get('/groups/{group}/activity', 'Group\ActivityController@index');
Route::get('/groups/{group}/activity/{action}', 'Group\ActivityController@show');

Route::get('/groups/{group}/reports/demographics/groups', 'Group\ReportController@indexGroups');
Route::get('/groups/{group}/reports/demographics/groups/{id}', 'Group\ReportController@showGroups');
Route::get('/groups/{group}/reports/demographics/departments', 'Group\ReportController@indexDepartments');
Route::get('/groups/{group}/reports/demographics/departments/{department}', 'Group\ReportController@showDepartments');
Route::get('/groups/{group}/reports/demographics/interests', 'Group\ReportController@indexInterests');
Route::get('/groups/{group}/reports/demographics/interests/{interest}', 'Group\ReportController@showInterests');
Route::get('/groups/{group}/reports/demographics/skillsets', 'Group\ReportController@indexSkillsets');
Route::get('/groups/{group}/reports/demographics/skillsets/{skillset}', 'Group\ReportController@showSkillsets');
Route::get('/groups/{group}/reports/demographics/mentors', 'Group\ReportController@indexMentors');
Route::get('/groups/{group}/reports/demographics/mentors/{mentorStatus}', 'Group\ReportController@showMentors');
Route::get('/groups/{group}/reports/demographics/introductions', 'Group\ReportController@indexIntroductions');
Route::get('/groups/{group}/reports/demographics/introductions/{introductionStatus}', 'Group\ReportController@showIntroductions');
Route::get('/groups/{group}/reports/demographics/titles/{title}/{titleUser}', 'Group\ReportController@showTitles');
Route::get('/groups/{group}/reports/demographics/titles/{title}', 'Group\ReportController@indexTitles');
Route::get('/groups/{group}/reports/demographics/taxonomies/{taxonomy}', 'Group\ReportController@indexTaxonomyOptions');
Route::get('/groups/{group}/reports/demographics/taxonomies/{taxonomy}/options/{option}', 'Group\ReportController@showTaxonomyOption');

Route::post('/groups/{group}/chat-room/clear', 'Group\ChatRoomController@clear');
Route::post('/groups/{group}/chat-room/download', 'Group\ChatRoomController@download');

Route::get('/posts/{post}', 'PostController@show');
Route::get('/posts/{post}/edit', 'PostController@edit');
Route::put('/posts/{post}/content', 'PostController@updateContent');
Route::post('/posts/{post}/report', 'PostController@report');
Route::put('/posts/{post}/report', 'PostController@report');
Route::post('/posts/{post}/resolve', 'PostController@resolve');
Route::get('/posts/{post}/log', 'PostController@logClick');
Route::put('/posts/{post}', 'PostController@update');
Route::delete('/posts/{post}', 'PostController@delete');
Route::get('/report-user/{post}/', 'UserController@reportUser');
Route::post('/report-user/{post}/', 'UserController@postReportUser')->name('post-report-user');

Route::get('/watch', 'VideoController@show');

Route::get('/management/my-direct-reports', 'ManagementController@directReports');
Route::get('/management/organization', 'ManagementController@organization');
Route::get('/management/organization/{user}', 'ManagementController@organizationForUser');
Route::get('/management/breakdowns/shoutouts-made', 'ManagementController@breakdownShoutoutsMadeIndex');
Route::get('/management/breakdowns/shoutouts-made/{user}', 'ManagementController@breakdownShoutoutsMadeShow');

Route::get('/api/search', 'Api\UserController@search');
Route::get('/api/groups/search', 'Api\GroupController@search');
Route::get('/api/unread-notifications', 'Api\UserController@getUnreadNotificationCount');
Route::get('/api/new-notifications/{time}', 'Api\UserController@getNewNotifications');
Route::post('/api/mentor-results', 'Api\MentorController@results');

Route::get('terms-and-conditions', 'Admin\TermsConditionsController@termsOfCondition')->name('terms-and-conditions');


