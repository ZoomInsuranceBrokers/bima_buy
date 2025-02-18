<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZmController;
use App\Http\Controllers\RetailController;
use App\Http\Middleware\ValidRc;
use App\Http\Middleware\ValidZm;
use App\Http\Middleware\ValidRetail;
use App\Http\Middleware\ValidAdmin;

// Route::get('/', function () {
//     return view('adminpages.index');
// });

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::get('forgot-password', [LoginController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
Route::get('reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [LoginController::class, 'reset'])->name('password.update');

//////////////////////////////////////////////////////login user routes///////////////////////////////////////////////

Route::middleware('auth')->group(function () {
    Route::get('/otp-form', [LoginController::class, 'showOtpForm'])->name('otp.form');
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp'])->name('otp.verify');
    Route::get('profile', [LoginController::class, 'profile'])->name('profile');
    Route::post('update', [LoginController::class, 'update'])->name('profile.update');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/notifications/fetch', [LoginController::class, 'fetchNotifications'])->name('notifications.fetch');
    Route::post('/notifications/mark-as-read', [LoginController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('/payment/screenshort/link/{id}', [RetailController::class, 'getPaymentScreenShortAndLink']);
    Route::get('/remarks/{id}', [LoginController::class, 'getRemarks']);
    Route::get('/retail/report', [RetailController::class, 'totalSalesReport'])->name('retail.report');
    Route::post('/retail/generate/report', [RetailController::class, 'downloadReport'])->name('retail.generate.report');
});

/////////////////////////////////////////admin////////////////////////////////////////
Route::middleware(['auth', ValidAdmin::class])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'todayReport'])->name('admin.dashboard');
    Route::get('/admin/total/leads', [AdminController::class, 'totalReport'])->name('admin.total.leads.report');
    Route::get('/admin/adduser', [AdminController::class, 'addUser'])->name('admin.adduser');
    Route::post('/user/store', [AdminController::class, 'store'])->name('admin.user.store');

    Route::get('total/leads/today', [AdminController::class, 'todayTotalLeadsReport'])->name('admin.total.leads.today');
    Route::get('completed/leads/today', [AdminController::class, 'todayCompleteLeadsReport'])->name('admin.completed.leads.today');
    Route::get('cancel/leads/today', [AdminController::class, 'todayCancelLeadsReport'])->name('admin.cancel.leads.today');
    Route::get('today/pending/leads/at/rc', [AdminController::class, 'todayPendLeadsAtRcEnd'])->name('admin.pending.leads.today.rc');
    Route::get('today/pending/leads/at/zm', [AdminController::class, 'todayPendLeadsinAtZm'])->name('admin.pending.leads.today.zm');
    Route::get('today/pending/leads/at/retail', [AdminController::class, 'todayPendLeadsAtRcEnd'])->name('admin.pending.leads.today.retail');
    

    Route::get('total/leads', [AdminController::class, 'totalLeadsReport'])->name('admin.total.leads');
    Route::get('completed/leads', [AdminController::class, 'totalCompleteLeadsReport'])->name('admin.completed.leads');
    Route::get('cancel/leads', [AdminController::class, 'totalCancelLeadsReport'])->name('admin.cancel.leads');
    Route::get('pending/leads/at/rc', [AdminController::class, 'totalPendLeadsAtRcEnd'])->name('admin.pending.leads.at.rc');
    Route::get('pending/leads/at/zm', [AdminController::class, 'totalPendLeadsinAtZm'])->name('admin.pending.leads.at.zm');
    Route::get('pending/leads/at/retail', [AdminController::class, 'totalPendLeadsAtRetailEnd'])->name('admin.pending.leads.at.retail');


});


/////////////////////////////////////////user////////////////////////////////////////

Route::middleware(['auth', ValidRc::class])->group(function () {

    Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
    Route::get('/create/lead', [UserController::class, 'createLead'])->name('user.createLead');
    Route::post('/store', [UserController::class, 'storeLead'])->name('store.user.lead');
    Route::get('/completed/lead', [UserController::class, 'completedLead'])->name('user.completedLead');
    Route::get('/policy/copy', [UserController::class, 'policyCopy'])->name('user.policyCopy');
    Route::get('/user/wallet', [UserController::class, 'wallet'])->name('user.wallet');
    Route::get('/update/lead/{id}', [UserController::class, 'showFoamToUpdateLead'])->name('user.show.form.to.updateLead');
    Route::put('/user/lead/update/{id}', [UserController::class, 'updateLead'])->name('user.update.lead');
    Route::get('/quote-details/{iid}', [RetailController::class, 'getQuotes']);
    Route::post('/user/submit-quote-action', [UserController::class, 'submitQuoteAction'])->name('user.submit.quote.action');
    Route::post('/leads/{id}/upload-Payment-Scree-Short', [UserController::class, 'uploadPaymentScreenShort'])->name('user.upload.payment.screen.short');
    Route::get('/user/cancel/leads', [UserController::class, 'cancelLeads'])->name('user.cancelLeads');
});
/////////////////////////////////////////zm////////////////////////////////////////

Route::middleware(['auth', ValidZm::class])->group(function () {

    Route::get('/zm/dashboard', [ZmController::class, 'index'])->name('zm.dashboard');
    Route::get('/zm/wallet', [ZmController::class, 'wallet'])->name('zm.wallet');
    Route::post('/leads/action/{id}', [ZmController::class, 'postLeadAction'])->name('zm.leadAction');
    Route::get('/zm/policy/copy', [ZmController::class, 'policyCopy'])->name('zm.policyCopy');
    Route::get('/zm/completed/lead', [ZmController::class, 'completedLeads'])->name('zm.completedLeads');
    Route::get('/zm/cancel/leads', [ZmController::class, 'cancelLeads'])->name('zm.cancelLeads');
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
    Route::post('/save/paymentlink/{id}', [RetailController::class, 'savePaymentLink']);
    Route::post('/leads/payment/{id}', [RetailController::class, 'upadtePaymentStatus']);
    Route::post('/leads/{id}/upload-policy', [RetailController::class, 'uploadPolicy']);
    Route::get('/retail/cancel/leads', [RetailController::class, 'cancelLeads'])->name('retail.cancelLeads');

});
