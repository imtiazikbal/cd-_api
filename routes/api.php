<?php

use \App\Http\Controllers\API\V1\Client\GoogleAlanytics\GoogleAnalyticsController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\API\V1\Addons\Accounting\AccountsmoduleController;
use App\Http\Controllers\API\V1\Client\Banner\BannerController;
use App\Http\Controllers\API\V1\Client\Category\CategoryController as ClientCategory;
use App\Http\Controllers\API\V1\Client\CourierController;
use App\Http\Controllers\API\V1\Client\Customer\MerchantCustomerController;
use App\Http\Controllers\API\V1\Client\ForgetPasswordController;
use App\Http\Controllers\API\V1\Client\FraudController;
use App\Http\Controllers\API\V1\Client\Notification\NotificationController;
use App\Http\Controllers\API\V1\Client\Order\OrderController as ClientOrder;
use App\Http\Controllers\API\V1\Client\Order\RatioStatisticsController;
use App\Http\Controllers\API\V1\Client\Order\TrackingTimelineController;
use App\Http\Controllers\API\V1\Client\Page\PageController;
use App\Http\Controllers\API\V1\Client\PaymentController;
use App\Http\Controllers\API\V1\Client\Product\AttributeController;
use App\Http\Controllers\API\V1\Client\Product\ProductController as ClientProduct;
use App\Http\Controllers\API\V1\Client\Product\VariationController;
use App\Http\Controllers\API\V1\Client\SalesTarget\SalesTargetController;
use App\Http\Controllers\API\V1\Client\Setting\SettingController as MerchantSetting;
use App\Http\Controllers\API\V1\Client\Setting\SettingController;
use App\Http\Controllers\API\V1\Client\ShippingSettingController;
use App\Http\Controllers\API\V1\Client\Shop\ShopController;
use App\Http\Controllers\API\V1\Client\Slider\SliderController as ClientSlider;
use App\Http\Controllers\API\V1\Client\SmsController;
use App\Http\Controllers\API\V1\Client\SmsTemplate\SmsTemplateController;
use App\Http\Controllers\API\V1\Client\Stock\Inventory\InventoryController;
use App\Http\Controllers\API\V1\Client\Stock\ProductReturn\ProductReturnController;
use App\Http\Controllers\API\V1\Client\Stock\StockIn\StockInController;
use App\Http\Controllers\API\V1\Client\SubscriptionController;
use App\Http\Controllers\API\V1\Client\SupportTicket\SupportTicketController;
use App\Http\Controllers\API\V1\Client\TopSellingProduct\TopSellingProduct;
use App\Http\Controllers\API\V1\Customer\AuthController;
use App\Http\Controllers\API\V1\Customer\CategoryController;
use App\Http\Controllers\API\V1\Customer\OrderController;
use App\Http\Controllers\API\V1\Customer\ProductController;
use App\Http\Controllers\API\V1\PageController as MerchantPageController;
use App\Http\Controllers\API\V1\Theme\ThemeController;
use App\Http\Controllers\API\V1\TransactionController;
use App\Http\Controllers\FooterController;
use App\Http\Controllers\Merchant\Auth\LoginController;
use App\Http\Controllers\RegisterController;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1/customer')->name('customer.')->group(function () {

    Route::post('product-combined-price', [ProductController::class, 'productCombinedPrice']);

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('category-product/list/{id}', [CategoryController::class, 'productListCategoryWise']);

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('product-search', [ProductController::class, 'productSearch']);
    Route::get('top-selling-product', [TopSellingProduct::class, 'customer_index']);

    Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');
    Route::post('/order/verify', [OrderController::class, 'verify'])->name('order.verify');
    Route::get('/order/{id}/details', [OrderController::class, 'show'])->name('order.details');

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/shipping-setting/show', [ShippingSettingController::class, 'show']);

    Route::get('/order-permission/show', [ShippingSettingController::class, 'orderPermissionIndex']);

});

Route::group(['prefix' => 'v1'], function () {
    Route::post('/login', [LoginController::class, 'merchant_login'])->name('merchant.login');
    Route::post('/signup', [LoginController::class, 'register']);
    Route::post('/auth/verify', [LoginController::class, 'verify']);
    Route::post('/resend/otp', [LoginController::class, 'resendOTP']);
    Route::post('/shops/info', [ShopController::class, 'index']);
    Route::post('/shops/domain', [ShopController::class, 'domain']);
    Route::get('/google-tag-manager', [ShopController::class, 'googleTagManager']);
    Route::get('/page/{shop_id}/{page}', [MerchantPageController::class, 'show']);
    Route::get('/device/{ip}/check/{browser}', [LoginController::class, 'checkIp']);
});

Route::group(['prefix' => 'v1/client'], function () {
    Route::post('forget-password', [ForgetPasswordController::class, 'forgetPassword']);
    Route::post('/otp-verify', [ForgetPasswordController::class, 'verifyOtp']);
    Route::get('/themes/list/{page}', [ThemeController::class, 'getListByPage']);
    Route::post('/update-password', [ForgetPasswordController::class, 'updatePassword']);
});

Route::post('v1/client/transaction/store', [TransactionController::class, 'store']);
Route::post('v1/customer/resend-otp', [OrderController::class, 'ResendOTP']);

Route::prefix('v1/client')->middleware('auth-merchant')->name('client.')->group(function () {
    Route::get('logout', [LoginController::class, 'merchant_logout'])->name('logout');
    Route::prefix('settings')->name('settings.')->group(function () {

        Route::get('business-info', [MerchantSetting::class, 'business_info'])->name('business.info');
        Route::post('business-info/update', [MerchantSetting::class, 'business_info_update'])->name('business.info.update');
        Route::post('pixel/update', [MerchantSetting::class, 'pixel_update'])->name('pixel.update');
        Route::post('domain-meta/update', [MerchantSetting::class, 'domain_verify'])->name('domain.meta.update');
        Route::post('domain/update', [MerchantSetting::class, 'domain_request'])->name('domain.request.update');

        Route::post('domain/refresh', [MerchantSetting::class, 'refreshDomainRequest'])->name('domain.request.refresh');
        Route::get('/dns-records', [MerchantSetting::class, 'getDnsRecords']);
        
        Route::post('/advance-payment/status/update', [MerchantSetting::class, 'updateAdvancePaymentStatus']);
        Route::get('/advance-payment/status', [MerchantSetting::class, 'getAdvancePaymentStatus']);

        Route::post('/hold-on/status/update', [MerchantSetting::class, 'updateHoldOnStatus']);
        Route::get('/hold-on/status', [MerchantSetting::class, 'getHoldOnStatus']);
        Route::post('/shipped-date/status/update', [MerchantSetting::class, 'updateShippedDateStatus']);
        Route::get('/shipped-date/status', [MerchantSetting::class, 'getShippedDateStatus']);

        Route::get('owner-info', [MerchantSetting::class, 'owner_info'])->name('owner.info');
        Route::post('owner-info/update', [MerchantSetting::class, 'owner_info_update'])->name('owner.info.update');

        Route::post('password-security/update', [MerchantSetting::class, 'password_security_update'])->name('password.security.update');

        Route::get('website', [MerchantSetting::class, 'website'])->name('website');
        Route::post('website/update', [MerchantSetting::class, 'website_update'])->name('website.update');

        Route::post('c-status/update', [MerchantSetting::class, 'cStatusUpdateByShopWise']);
    });

    Route::post('/send-custom-sms', [SmsController::class, 'sendSms']);

    Route::get('/customer-list', [MerchantCustomerController::class, 'customerOrderList']);
    Route::get('/customer-search', [MerchantCustomerController::class, 'customerListSearch']);

    Route::post('/single-sms-send', [SmsController::class, 'single_sms_send']);
    Route::get('/smsuser', [SmsController::class, 'getCustomers']);
    Route::post('/order-sms-status-update', [SmsController::class, 'updateOrderSms']);
    Route::get('/order-sms-status-show', [SmsController::class, 'showOrderSms']);

    Route::group(['prefix' => 'support-ticket'], function () {
        Route::post('/list', [SupportTicketController::class, 'index']);
        Route::post('/store', [SupportTicketController::class, 'store']);
        Route::get('/{merchant}/details/{id}', [SupportTicketController::class, 'show']);
        Route::post('/{id}/reply', [SupportTicketController::class, 'reply']);
    });

    Route::get('/transaction/list', [TransactionController::class, 'list']);
    Route::get('/package-info/{id}', [TransactionController::class, 'packageInfo']);
    Route::get('/footer-list', [PageController::class, 'footerlist']);
    Route::get('/checkout-design-list', [PageController::class, 'checkoutdesignlist']);
    Route::get('/transaction/pdf/download/{id}', [TransactionController::class, 'generateTransactionPDF']);

    // notification show route
    Route::post('/notifications-show/{id?}', [NotificationController::class, 'NotificationShow']);
    Route::post('/notifications-read', [NotificationController::class, 'NotificationRead']);

    Route::get('/customers/{id}', [MerchantCustomerController::class, 'getCustomerByMerchant']);
    // shop steps
    Route::get('/shop-steps', [ShopController::class, 'shopSteps']);

    Route::apiResource('categories', ClientCategory::class);
    Route::get('products-for-search', [ClientProduct::class, 'productForSearch']);
    Route::resource('products', ClientProduct::class);
    Route::resource('orders', ClientOrder::class);

    //Product variations
    Route::get('/variation/attributes', [AttributeController::class, 'index']);
    Route::get('/variation/attribute-values/{id}', [AttributeController::class, 'attributeValues']);
    Route::post('/variation/sku_combinations', [VariationController::class, 'sku_combinations']);

    Route::post('/variation/attributes-store', [AttributeController::class, 'store']);
    Route::post('/variation/attributes-value-store', [AttributeController::class, 'attributeValueStore']);

    Route::get('/order/delete/{id}', [ClientOrder::class, 'orderDelete']);
    Route::get('/order/trashed/list', [ClientOrder::class, 'orderTrashedList']);
    Route::get('/order/search', [ClientOrder::class, 'orderGlobalSearch']);
    Route::get('top-selling-product', [TopSellingProduct::class, 'index']);

    Route::get('sales-target', [SalesTargetController::class, 'sales_target'])->name('sales.target');
    Route::post('sales-target/update', [SalesTargetController::class, 'sales_target_update'])->name('sales.target.update');

    Route::post('orders/status/update', [ClientOrder::class, 'order_status_update'])->name('orders.status.update');
    Route::get('/order-invoice', [ClientOrder::class, 'order_invoice'])->name('order.invoice');
    Route::post('/order/follow-up/{id}/update', [ClientOrder::class, 'updateFollowup'])->name('order.follow_up');
    Route::post('/order/advance-payment/{id}/update', [ClientOrder::class, 'advancePayment'])->name('order.advance_pay');
    Route::post('/order/note/{id}/update', [ClientOrder::class, 'noteUpdateByStatus'])->name('order.note_update');
    Route::post('/order/date/{id}/update', [ClientOrder::class, 'dateUpdateByStatus'])->name('order.date_update');
    Route::post('/order/discount/{id}/update', [ClientOrder::class, 'updateDiscount'])->name('order.discount');
    Route::post('/order/{id}/delete', [ClientOrder::class, 'delete'])->name('order.delete');
    Route::get('/order/count', [ClientOrder::class, 'getOrderCounts']);
    Route::post('/bulkdelete', [ClientOrder::class, 'bulkdelete']);
    Route::get('/order-statistics', [ClientOrder::class, 'orderStatistic']);
    Route::get('/order-delivery-report', [ClientOrder::class, 'deliveryReport']);

    Route::post('/print-selected-orders', [ClientOrder::class, 'printSelectedOrders']);

    Route::get('/recent-order/count', [ClientOrder::class, 'recentOrder']);
    Route::get('/order-tracking-timeline/{order_id}', [TrackingTimelineController::class, 'tracking_timeline']);

    Route::get('/channel-statistics', [RatioStatisticsController::class, 'orderSourceStatistic']);
    Route::get('/ratio-statistics', [RatioStatisticsController::class, 'ratioCalculation']);
    Route::get('/channel/ratio-statistics', [RatioStatisticsController::class, 'orderSourceRatioCalculation']);

    Route::post('/chart/statistics', [RatioStatisticsController::class, 'chartStatistic']);

    Route::get('/banners/index', [BannerController::class, 'list']);
    Route::post('/banners/store', [BannerController::class, 'store']);
    Route::get('/banners/delete/{id}', [BannerController::class, 'destroy']);

    Route::resource('pages', PageController::class);
    Route::post('/multi-page/update', [PageController::class, 'multiPageUpdate']);
    Route::get('/multi-page/footer-id', [PageController::class, 'getMultiPageWithFooterId']);
    Route::post('/pages/{id}/duplicate', [PageController::class, 'pageCopy']);
    Route::post('/page/update/{page_id}', [PageController::class, 'pageUpdate']);

    // slider routes
    Route::get('/sliders/index', [ClientSlider::class, 'list']);
    Route::post('/sliders/store', [ClientSlider::class, 'store']);
    Route::get('/sliders/delete/{id}', [ClientSlider::class, 'destroy']);


    Route::prefix('stocks')->name('stocks.')->group(function () {

        //Inventory
        Route::get('inventory/list', [InventoryController::class, 'index'])->name('inventory.list');
        Route::get('inventory/show/{id}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::post('inventory/update', [InventoryController::class, 'update'])->name('inventory.update');

        //Stock In
        Route::get('stock-in/list', [StockInController::class, 'index'])->name('stock.in.list');
        Route::get('stock-in/show/{id}', [StockInController::class, 'show'])->name('stock.in.show');
        Route::post('stock-in/update', [StockInController::class, 'update'])->name('stock.in.update');

        //Product return
        Route::get('product-return/list', [ProductReturnController::class, 'index'])->name('product.return.list');
        Route::post('product-return/update', [ProductReturnController::class, 'update'])->name('product.return.update');
    });

    Route::group(['prefix' => 'themes'], function () {
        Route::post('/list', [ThemeController::class, 'getThemesByType']);
        Route::post('/import-theme', [ThemeController::class, 'import']);
        Route::post('/merchant/themes', [ThemeController::class, 'getMerchantsTheme']);

        Route::post('/custom/store', [ThemeController::class, 'store']);
        Route::post('/custom/{id}/update', [ThemeController::class, 'update']);

        Route::get('/landing/page/search/{search}', [ThemeController::class, 'searchLandingPage']);
        Route::get('/active-theme/search/{search}', [ThemeController::class, 'searchActiveTheme']);
    });

    Route::group(['prefix' => 'courier'], function () {
        Route::get('/list', [CourierController::class, 'index']);
        Route::post('/provider', [CourierController::class, 'store']);
        Route::post('/send-order', [CourierController::class, 'sendOrderToCourier']);

        Route::group(['prefix' => 'redx'], function(){
            Route::get('/area', [CourierController::class, 'redxGetArea']);
            Route::get('/area/discricty-wise', [CourierController::class, 'redxGetAreaDiscrictWise']);
            Route::post('/pickup-store/create', [CourierController::class, 'redxPickupStoreCreate']);
            Route::get('/order-details', [CourierController::class, 'redxOrderDetails']);
        });
        
        Route::get('/pathao/city-list', [CourierController::class, 'getCities']);
        Route::get('/pathao/zone-list', [CourierController::class, 'getZones']);
        Route::get('/pathao/area-list', [CourierController::class, 'getArea']);
        Route::get('/pathao/store-list', [CourierController::class, 'getStores']);
    });

    Route::get('/sslcommerze/pay', [PaymentController::class, 'pay']);
    Route::get('/subscription/info', [SubscriptionController::class, 'index']);
    Route::get('/subscription/pdf/download/{id}', [SubscriptionController::class, 'subscriptionPdfDownload']);

    Route::get('/bkash/pay', [PaymentController::class, 'bkashPay']);
    Route::get('/nagad/pay', [PaymentController::class, 'nagadPay']);

    // Accounting Modules all routes
    Route::group(['prefix' => 'accounts'], function () {
        Route::post('/cash-in', [AccountsmoduleController::class, 'cashInPayment']);
        Route::post('/cash-out', [AccountsmoduleController::class, 'cashOutPayment']);
        Route::get('/payment-list', [AccountsmoduleController::class, 'PaymentListShow']);

        Route::get('/payment-edit/{id}', [AccountsmoduleController::class, 'PaymentEdit']);
        Route::post('/payment-update/{id}', [AccountsmoduleController::class, 'PaymentUpdate']);

        Route::get('/payment-delete/{id}', [AccountsmoduleController::class, 'PaymentDelete']);
        Route::get('/payment-method-delete/{id}', [AccountsmoduleController::class, 'paymentMethodDelete']);
        Route::get('/payment-calculation', [AccountsmoduleController::class, 'PaymentCalculation']);

        Route::get('/payment-search/{search}', [AccountsmoduleController::class, 'PaymentSearch']);

        Route::post('/payment-method-add', [AccountsmoduleController::class, 'paymentMethodAdd']);
        Route::get('/payment-method-show', [AccountsmoduleController::class, 'paymentMethodShow']);
        Route::get('/payment-method-delete/{id}', [AccountsmoduleController::class, 'paymentMethodDelete']);

        Route::get('/payor/delete/{id}', [AccountsmoduleController::class, 'accountPayorDelete']);
        Route::post('/payor/add', [AccountsmoduleController::class, 'accountPayorAdd']);
        Route::get('/payor/list', [AccountsmoduleController::class, 'accountPayorList']);

        Route::get('/ledger/delete/{id}', [AccountsmoduleController::class, 'accountLedgerDelete']);
        Route::post('/ledger/add', [AccountsmoduleController::class, 'accountLedgerAdd']);
        Route::get('/ledger/list', [AccountsmoduleController::class, 'accountLedgerList']);
        Route::get('/multi-search', [AccountsmoduleController::class, 'accountModuleMultiSearch']);
    });

    Route::get('/footer-list', [FooterController::class, 'index']);
    Route::get('/single-footer/{id}', [FooterController::class, 'singleFooter']);
    Route::post('/footer-add', [FooterController::class, 'addFooter']);
    Route::get('/footer-edit/{id}', [FooterController::class, 'editFooter']);
    Route::post('/footer-update/{id}', [FooterController::class, 'updateFooter']);
    Route::post('/footer/color/reset/{id}', [FooterController::class, 'footerColorReset']);

    // Google Analytics routes
    Route::get('/other-script/list', [GoogleAnalyticsController::class, 'index']);
    Route::post('/connection', [GoogleAnalyticsController::class, 'store']);

    Route::prefix('shipping-setting')->group(function () {
        Route::get('/show', [ShippingSettingController::class, 'show']);
        Route::post('/store-update', [ShippingSettingController::class, 'storeUpdate']);
        Route::post('/status-update', [ShippingSettingController::class, 'statusUpdate']);
    });

    Route::prefix('order-otp-permission')->group(function () {
        Route::get('/status-update', [ShippingSettingController::class, 'updateOrderOTPPermissionStatus']);
    });

    Route::prefix('order-permission')->group(function () {
        Route::get('/status', [ShippingSettingController::class, 'orderPermissionIndex']);
        Route::get('/status-update', [ShippingSettingController::class, 'updateOrderPermissionStatus']);
    });

    Route::get('order-attach-image/status-update', [ShopController::class, 'orderAttachImgPermUpdate']);

    Route::prefix('sms-template')->group(function () {
        Route::get('/show', [SmsTemplateController::class, 'index']);
        Route::post('/update/store', [SmsTemplateController::class, 'updateOrstore']);
        Route::get('/delete/{sms_template}', [SmsTemplateController::class, 'delete']);
    });

    Route::get('/frauds/{number}', [FraudController::class, 'index']);
    Route::get('/fraud-notes/{fraud}', [FraudController::class, 'note']);
});

// Addons all routes
Route::group(['prefix' => 'v1/client/addons'], function () {
    Route::post('/add', [AddonController::class, 'addnew']);
    Route::post('/update/{id}', [AddonController::class, 'AddonsUpdate']);
    Route::get('/delete/{id}', [AddonController::class, 'delete']);
    Route::get('/addons-list', [AddonController::class, 'list']);

    Route::post('/install', [AddonController::class, 'install']);
    Route::get('/myaddons', [AddonController::class, 'showlist']);

    Route::get('/uninstall/{id}', [AddonController::class, 'uninstall']);
    Route::get('/status-active-inactive/{id}', [AddonController::class, 'ActiveInactiveStatus']);
    Route::get('/addons-search/{search}', [AddonController::class, 'AddonsSearch']);
});
Route::get('v1/theme/list', [ThemeController::class, 'getThemesByType']);

Route::post('v1/client/register', [RegisterController::class, 'register']);
Route::post('v1/password-update', [ForgetPasswordController::class, 'updatePasswordAfterRegistration']);
Route::post('v1/customer/hash-encryption', [ForgetPasswordController::class, 'userHashEncryption']);

// Public API for order tracking timeline
Route::get('v1/order-tracker/{order_tracking_code}', [TrackingTimelineController::class, 'order_tracker']);