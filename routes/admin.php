<?php

use App\Http\Controllers\Admin\Auth\DashboardController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\API\V1\TransactionController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    Artisan::call('optimize');
});
// Route::get('/per-day-new-register-count', [MerchantController::class, 'perDayNewRegisterCount']);
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('check.login');

Route::group(['middleware' => ['auth'], 'prefix' => 'panel'], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::group(['prefix' => 'merchants'], function () {
        Route::get('/', [MerchantController::class, 'index'])->name('admin.merchants');
        Route::get('/merchants', [MerchantController::class, 'merchants']);
        Route::post('/update-merchant', [MerchantController::class, 'update'])->name('update.merchant');
        Route::get('/search/order-count', [MerchantController::class, 'searchOrderCount']);
        Route::get('/per-day-order-count', [MerchantController::class, 'perDayOrderCount']);
        Route::get('/per-day-new-register-count', [MerchantController::class, 'perDayNewRegisterCount']);
        Route::get('/statuses', [MerchantController::class, 'statuses']);
        Route::get('/payment_status', [MerchantController::class, 'paymentStatus']);
        Route::get('/{merchant}', [MerchantController::class, 'show'])->name('admin.merchant.details');
        Route::post('/{merchant}/update-status', [MerchantController::class, 'changeStatus'])->name('admin.merchant.change_status');
        Route::post('/{merchant}/delete', [MerchantController::class, 'destroy'])->name('admin.merchant.delete');
        Route::post('/{merchant}/faqsupdate', [MerchantController::class, 'faqsupdate'])->name('faqsupdate');
        Route::put('/{merchant}/updateduedate', [MerchantController::class, 'updateDueDate'])->name('updateduedate');
        Route::post('/{merchant}/update-payment-status', [MerchantController::class, 'paymentStatusUpdate'])->name('admin.merchant.payment_status_change');
    });

    Route::group(['prefix' => 'staffs'], function () {
        Route::get('/', [StaffController::class, 'index'])->name('admin.staffs');
        Route::get('/list', [StaffController::class, 'getStafflist']);
        Route::get('/create', [StaffController::class, 'create'])->name('admin.staffs.create');
        Route::post('/store', [StaffController::class, 'store'])->name('admin.staffs.store');
        Route::get('/edit/{user}', [StaffController::class, 'edit'])->name('admin.staffs.edit');
        Route::put('/update/{user}', [StaffController::class, 'update'])->name('admin.staffs.update');
        Route::delete('/delete/{user}', [StaffController::class, 'destroy'])->name('admin.staffs.delete');
        Route::post('/update-status', [StaffController::class, 'updateStatus'])->name('admin.staffs.update_status');
    });

    Route::group(['prefix' => 'support-ticket'], function () {
        Route::get('/', [SupportTicketController::class, 'index'])->name('admin.support_ticket');
        Route::get('/tickets', [SupportTicketController::class, 'tickets']);
        Route::get('/{uuid}', [SupportTicketController::class, 'show']);
        Route::get('/details/{uuid}', [SupportTicketController::class, 'getTicketDetails']);
        Route::post('/store', [SupportTicketController::class, 'store']);
        Route::post('/reply/{id}', [SupportTicketController::class, 'replyToTicket']);
        Route::post('/status-update/{support_ticket}', [SupportTicketController::class, 'statusUpdate']);
        Route::post('/multi-search', [SupportTicketController::class, 'multiSearchSupportTicket']);
    });

    Route::group(['prefix' => 'themes'], function () {
        Route::get('/', [ThemeController::class, 'index'])->name('admin.themes');
        Route::get('/list', [ThemeController::class, 'getThemes']);
        Route::post('/store', [ThemeController::class, 'store']);
    });

    Route::group(['prefix' => 'domain'], function () {
        Route::get('/request/{type?}', [ThemeController::class, 'domainRequest'])->name('admin.domain.request');
        Route::post('/request/search', [ThemeController::class, 'domainRequestSearch'])->name('admin.domain.search');
        Route::post('/request/update/{id}', [ThemeController::class, 'domainRequestUpdate'])->name('admin.domain.request.update');
        Route::get('/refresh/{id}', [ThemeController::class, 'refreshDomain'])->name('admin.domain.refresh');
        Route::get('/request/status-update/{id?}/{type?}', [ThemeController::class, 'domainRequestStatusUpdate'])->name('admin.domain.request.status.update');
        Route::post('/request/status-reject', [ThemeController::class, 'domainRequestStatusReject'])->name('admin.domain.request.status.reject');
    });

    Route::group(['prefix' => 'page'], function () {
        Route::get('/edit/{id}', [MerchantController::class, 'superAdminPageEdit'])->name('admin.page.edit');
        Route::post('/update/{id}', [MerchantController::class, 'superAdminPageUpdate'])->name('admin.page.update');
    });

    Route::get('download/{id}/attachment/',[SupportTicketController::class, 'download']);

    // Route::group(['prefix' => 'couriers', 'controller' => AdminCourierController::class], function () {
    //     Route::get('/', 'index')->name('admin.couriers');
    //     Route::put('/{adminCourier}/update', 'update')->name('admin.couriers.update');
    // });

    Route::post('/order-count-filter', [OrderController::class, 'orderFilter']);
    Route::get('/transaction/status/{id}', [TransactionController::class, 'paymentStatusUpdate'])->name('transaction.status');
});