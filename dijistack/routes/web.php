<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    LoginController,
    CompanyController,
    UserController,
    WhatsaapManagementController,
    WhatsaapBotController,
    WhatsAppChatController,
    TechnicalServiceController,
    CustomerController,
    LocationController,
    ServiceWarrantyController,
    SmsLogController
};
// Erişim Yetkiniz Sayfası
Route::get('/no-authority', function () {
    return view('no-authority');
})->name('no-authority');
// WhatsApp Webhook - AI Mesajlaşma Buradan Döner
Route::post('/whatsapp/webhook', [WhatsaapBotController::class, 'handleWebhook']);
// Auth İşlemleri
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [UserController::class, 'login'])->name('login.post');
// WhatsApp Chat Görüntüleme ve Mesaj Gönderme
Route::get('/whatsapp/chat/{session}', [WhatsAppChatController::class, 'show'])->name('whatsapp.chat');
Route::post('/whatsapp/chat/send', [WhatsAppChatController::class, 'send'])->name('whatsapp.chat.send');


Route::middleware(['auth'])->group(function () {
    // Çıkış İşlemi
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    // Ülke Şehir İlçeler
    Route::prefix('location')->group(function () {
      Route::get('/countries', [LocationController::class, 'countries']);
      Route::get('/cities/{country}', [LocationController::class, 'cities']);
      Route::get('/districts/{city}', [LocationController::class, 'districts']);
    });
    // Domain (Şirket) Bazlı Yönetim Paneli
     Route::prefix('{domain}')->group(function () {
        // Kontrol Paneli Anasayfa
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // WhatsApp AI Yönetimi
        Route::prefix('whatsapp-management')->name('whatsapp.')->group(function () {
            Route::get('/whatsapp/messages', [WhatsAppChatController::class, 'index'])
            ->name('whatsapp.messages'); 
            Route::get('/connection', [WhatsaapManagementController::class, 'connection'])->name('connection');
            // Ürün ve Hizmet (Knowledge Base) Yönetimi
            Route::prefix('services')->name('services.')->group(function () {
                Route::get('/', [WhatsaapManagementController::class, 'index'])->name('index');
                Route::get('/fetch', [WhatsaapManagementController::class, 'fetchServices'])->name('fetch'); 
                Route::get('/create', [WhatsaapManagementController::class, 'createService'])->name('create');
                Route::get('/create-pdf', [WhatsaapManagementController::class, 'createServiceViaPDF'])->name('create.pdf');
                Route::post('/store', [WhatsaapManagementController::class, 'storeService'])->name('store');
                Route::post('/store-pdf', [WhatsaapManagementController::class, 'storePDF'])->name('storePDF');
                Route::post('/update', [WhatsaapManagementController::class, 'updateService'])->name('update');
                Route::delete('/delete/{id}', [WhatsaapManagementController::class, 'destroyService'])->name('delete');
            });
            // Raporlar Yönetimi
             Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/person-reports', [WhatsaapManagementController::class, 'personReports'])->name('person.reports');
                Route::get('/message-reports', [WhatsaapManagementController::class, 'messageReports'])->name('message.reports');
                Route::get('/emotion-analysis', [WhatsaapManagementController::class, 'emotionAnalysisReport'])->name('emotion.analysis');
                Route::get('/keyword-analysis', [WhatsaapManagementController::class, 'keywordAnalysisReport'])->name('keyword.analysis');
            });
        });
        // Teknik Servis Yönetimi
        Route::prefix('technical-service')->name('technical-service.')->group(function () {
            Route::get('/list', [TechnicalServiceController::class, 'list'])->name('list');
            Route::get('/create', [TechnicalServiceController::class, 'create'])->name('create');
            Route::post('/store', [TechnicalServiceController::class, 'store'])->name('store');
            Route::get('/fetch', [TechnicalServiceController::class, 'fetch'])->name('fetch');
            Route::get('/edit/{id}',[TechnicalServiceController::class,'edit'])->name('edit');
            Route::get('/warranty-data/{id}/fetch',[TechnicalServiceController::class,'serviceProductWarrantyStatuses'])->name('warranty-data');
            Route::get('/activities/{id}/fetch', [TechnicalServiceController::class, 'serviceActivitiesFetch'])->name('activities-fetch');
            Route::get('/notes/{id}/fetch', [TechnicalServiceController::class, 'serviceNotesFetch'])->name('notes-fetch');
            Route::get('/sms-logs/{id}/fetch', [TechnicalServiceController::class, 'singleFetchSmsLog'])->name('sms-logs-fetch');
            Route::get('/request/{id}/fetch', [TechnicalServiceController::class, 'singleServiceRequestFetch'])->name('request-fetch');
        });
        // Müşteri Yönetimi 
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/search', [CustomerController::class, 'technicalServiceCustomerSearch'])->name('search');
            Route::post('/store', [CustomerController::class, 'technicalServiceCustomerStore'])->name('store');
        });
        // Teknik Servis Ürün Garanti   
        Route::prefix('service-warranty')->group(function () {
            Route::post('/check-imei', [ServiceWarrantyController::class, 'checkImei']);    
         });
        // Sms Log  
       
    });
});