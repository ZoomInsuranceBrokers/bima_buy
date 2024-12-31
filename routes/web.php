<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZmController;
use App\Http\Controllers\RetailController;
use App\Http\Middleware\ValidRc;
use App\Http\Middleware\ValidZm;
use App\Http\Middleware\ValidRetail;

// Route::get('/', function () {
//     return view('adminpages.index');
// });

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');


Route::middleware('auth')->group(function () {
    Route::get('profile', [LoginController::class, 'profile'])->name('profile');
    Route::post('update', [LoginController::class, 'update'])->name('profile.update');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/notifications/fetch', [LoginController::class, 'fetchNotifications'])->name('notifications.fetch');
    Route::post('/notifications/mark-as-read', [LoginController::class, 'markAsRead'])->name('notifications.markAsRead');
});

/////////////////////////////////////////admin////////////////////////////////////////

Route::get('/user/dashboard', function () {
    return view('adminpages.index');
})->name('user.dashboard');


/////////////////////////////////////////user////////////////////////////////////////

Route::middleware(['auth', ValidRc::class])->group(function () {

    Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
    Route::get('/create/lead', [UserController::class, 'createLead'])->name('user.createLead');
    Route::post('/store', [UserController::class, 'storeLead'])->name('store.user.lead');
    Route::get('/completed/lead', [UserController::class, 'completedLead'])->name('user.completedLead');
    Route::get('/policy/copy', [UserController::class, 'policyCopy'])->name('user.policyCopy');
    Route::get('/user/wallet', [UserController::class, 'wallet'])->name('user.wallet');
    Route::get('/update/lead/{id}', [UserController::class, 'showFoamToUpdateLead'])->name('user.show.foam.to.updateLead');
    Route::put('/user/lead/update/{id}', [UserController::class, 'updateLead'])->name('user.update.lead');
    Route::get('/quote-details/{iid}', [RetailController::class, 'getQuotes']);
    Route::get('/user/quote-details/{leadId}', [UserController::class, 'getQuoteDetails'])->name('user.quote.details');
    Route::post('/user/submit-quote-action', [UserController::class, 'submitQuoteAction'])->name('user.submit.quote.action');

});
/////////////////////////////////////////zm////////////////////////////////////////

Route::middleware(['auth', ValidZm::class])->group(function () {

    Route::get('/zm/dashboard', [ZmController::class, 'index'])->name('zm.dashboard');
    Route::get('/zm/wallet', [ZmController::class, 'wallet'])->name('zm.wallet');
    Route::post('/leads/action/{id}', [ZmController::class, 'postLeadAction'])->name('zm.leadAction');
    Route::get('/zm/policy/copy', [ZmController::class, 'policyCopy'])->name('zm.policyCopy');
    Route::get('/zm/completed/lead', [ZmController::class, 'completedLeads'])->name('zm.completedLeads');
});


/////////////////////////////////////////Zm and Retail common routes////////////////////////////////////////
Route::middleware('auth')->group(function () {
    Route::get('/zm/leads/details/{id}', [ZmController::class, 'getLeadDetails'])->name('zm.leadDetails');
    Route::get('/leads/details', [ZmController::class, 'getPolicyCopyDetails'])->name('zm.policyCopy.search');
    Route::get('/zm/policy/copy/{id}', [ZmController::class, 'policyCopyDetails'])->name('zm.policyCopyDetails');
    Route::get('/policy-copy/download', [ZmController::class, 'download'])->name('zm.policyCopy.download');
    Route::get('/leads/{lead}/quotes', [RetailController::class, 'getQuotes']);
});

/////////////////////////////////////////Retail////////////////////////////////////////


Route::middleware(['auth', ValidRetail::class])->group(function () {

    Route::get('/retail/dashboard', [RetailController::class, 'index'])->name('retail.dashboard');
    Route::get('/retail/completed/lead', [RetailController::class, 'completedLeads'])->name('retail.completedLeads');
    Route::post('/leads/action/retail/{id}', [RetailController::class, 'postLeadAction'])->name('retail.leadAction');
    Route::post('/quotes', [RetailController::class, 'store']);
    Route::post('/leads/payment/{id}', [RetailController::class, 'upadtePaymentStatus']);
    Route::post('/leads/{id}/upload-policy', [RetailController::class, 'uploadPolicy']);

});