<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SuggestionAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\BookingRoomController;
use App\Http\Controllers\MaterialMovementController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\APDController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleTypeController;
use App\Http\Controllers\AssetLoanController;
use App\Http\Controllers\CalculatorBOMController;
use App\Http\Controllers\CalculatorFuelController;
use App\Http\Controllers\DefectController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TransferInController;
use App\Http\Controllers\TransferOutController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\BackupAgentController;

use App\Http\Controllers\PRController;
use App\Http\Controllers\POController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\GroupMaterialController;
use App\Http\Controllers\IncomingInspectionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\ITProjectController;
use App\Http\Controllers\TicketCategoryController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\ITAssetsController;
use App\Http\Controllers\ITBackupController;
use App\Http\Controllers\ITBackupScheduleController;
use App\Http\Controllers\ITStorageController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\IssueTrackerController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\WorkstationController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\PushTestController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BarcodePartController;
use App\Http\Controllers\CAPAController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\ImprovementController;
use App\Http\Controllers\ProductKnowledgeController;
use App\Http\Controllers\RekapBupotController;
use App\Http\Controllers\RemoteAccessController;
use App\Http\Controllers\STOController;
use App\Http\Controllers\TemporaryStockController;
use App\Http\Controllers\TransferChemicalController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Minishlink\WebPush\WebPush; 

// Arahkan root "/" ke login
Route::get('/', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth');
Route::get('/login-magang', function () {
    return view('auth.login-magang'); })->name('login.magang');

Route::get('/suggestion/login', [SuggestionAuthController::class, 'showLogin'])->name('suggestion.login');
Route::post('/suggestion/otp/send', [SuggestionAuthController::class, 'sendOtp'])->name('suggestion.otp.send');
Route::post('/suggestion/otp/verify', [SuggestionAuthController::class, 'verifyOtp'])->name('suggestion.otp.verify');

Route::middleware(['suggestion.auth'])->prefix('suggestion')->name('suggestion.')->group(function () {

    Route::get('/dashboard',  [SuggestionController::class, 'index'])->name('dashboard');
    Route::post('/logout', [SuggestionAuthController::class, 'logout'])
            ->name('logout');
    Route::get('/create',     [SuggestionController::class, 'create'])->name('create');
    Route::post('/draft', [SuggestionController::class, 'storeDraft'])->name('draft');
Route::post('/submit', [SuggestionController::class, 'submit'])->name('submit');
Route::get('/{suggestion}/edit',     [SuggestionController::class, 'edit'])->name('edit');
Route::put('/{suggestion}/update', [SuggestionController::class, 'update'])->name('updateData');
    Route::get('/my',         [SuggestionController::class, 'mySuggestions'])->name('my');
    Route::post('/formula',            [SuggestionController::class, 'storeFormula'])->name('formula.store');

    // Photo — harus sebelum /{suggestion}
    Route::delete('/photo/{photo}', [SuggestionController::class, 'deletePhoto'])->name('photo.delete');

    // Improvement — harus sebelum /{suggestion}
    Route::prefix('improvement')->name('improvement.')->group(function () {
        Route::get('/dashboard',           [ImprovementController::class, 'dashboard'])->name('dashboard');
        Route::post('/period',             [ImprovementController::class, 'storePeriod'])->name('period.store');
        Route::patch('/period/{period}',   [ImprovementController::class, 'updatePeriod'])->name('period.update');
        Route::delete('/period/{period}',  [ImprovementController::class, 'deletePeriod'])->name('period.delete');
        Route::patch('/formula/{formula}', [ImprovementController::class, 'updateFormula'])->name('formula.update');
    });

    // SPV & Manager actions — harus sebelum /{suggestion}
    Route::post('/{suggestion}/spv-action', [SuggestionController::class, 'spvAction'])->name('spv.action');
    Route::post('/{suggestion}/score',      [SuggestionController::class, 'score'])->name('score');
    Route::patch('/{suggestion}/close',     [SuggestionController::class, 'close'])->name('close');

    // /{suggestion} PALING BAWAH + constraint angka saja
    Route::get('/{id}/detail', [SuggestionController::class, 'detail']);
    Route::patch('/{suggestion}', [SuggestionController::class, 'update'])->name('update')->whereNumber('suggestion');
});

 Route::get('/product-knowledge/index', [ProductKnowledgeController::class, 'index'])->name('product-knowledge.index');
Route::get('/product-knowledge/data', [ProductKnowledgeController::class, 'search'])->name('product-knowledge.data');

Route::get('/check-session', function () {
    if (!auth()->check()) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }
    return response()->json(['message' => 'OK'], 200);
});

Route::post('/save-subscription', function(Request $request){
    $userId = Auth::id();
    $subscription = $request->all();
    $ipAddress = $request->ip();

    DB::table('subscriptions')->updateOrInsert(
        ['user_id' => $userId, 'subscription' => json_encode($subscription)],
        ['ip_address' => $ipAddress, 'updated_at' => now()]
    );

    return response()->json(['success' => true]);
});



Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    Route::get('/check-auth', function () {
    return [
        'auth_id' => auth()->id(),
        'user' => auth()->user(),
        'roles' => auth()->user() ? auth()->user()->roles->pluck('name') : null,
        'departments' => auth()->user() ? auth()->user()->departments->pluck('name') : null,
    ];
})->middleware('auth');

Route::get('/security', [SecurityController::class, 'index'])
    ->name('security');
    Route::get('/security/catering', [SecurityController::class, 'getDataCatering'])->name('security.catering');
    Route::post('/security/store/barang', [SecurityController::class, 'storeBarang'])->name('store.barang');
    Route::get('/security-goods/list', [SecurityController::class, 'getDataBarang'])->name('barang.list');
    Route::post('/security/catering/broadcast', [SecurityController::class, 'broadcastNewCatering']);

     Route::get('/temporary-stock/index', [TemporaryStockController::class, 'index'])->name('temporary-stock.index');
     Route::get('/temporary-stock/movement/{code}', [TemporaryStockController::class, 'movement']);


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/todo', [TodoController::class, 'index'])->name('todo.index');
    Route::post('/todo', [TodoController::class, 'store'])->name('todo.store');
    Route::patch('/todo/{id}/toggle', [TodoController::class, 'toggle'])->name('todo.toggle');
    Route::delete('/todo/{id}/destroy', [TodoController::class, 'destroy'])->name('todo.destroy');
    Route::post('/todo/{id}/reschedule', [TodoController::class, 'reschedule'])->name('todo.reschedule');


Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/article/dashboard', [ArticleController::class, 'index'])->name('article.index');
    Route::get('/article/lists', [ArticleController::class, 'select'])->name('article.select');
    Route::get('/article/data', [ArticleController::class, 'data'])->name('article.data');
    Route::get('/article/create', [ArticleController::class, 'create'])->name('article.create');
    Route::post('/article/import', [ArticleController::class, 'create'])->name('article.import');
    Route::get('/article/template', [ArticleController::class, 'template'])->name('article.template');
    Route::post('/article/store', [ArticleController::class, 'store'])->name('article.store');
    Route::get('/article-type/index', [ArticleTypeController::class, 'index'])->name('article-type.index');
    Route::get('/article-type/data', [ArticleTypeController::class, 'data'])->name('article-type.data');
    Route::get('/article-type/select', [ArticleTypeController::class, 'select'])->name('article-type.select');
    Route::post('/article-type/store', [ArticleController::class, 'store'])->name('article-type.store');
    Route::get('/group-of-material/index', [GroupMaterialController::class, 'index'])->name('gom.index');
    Route::get('/group-of-material/data', [GroupMaterialController::class, 'data'])->name('gom.data');
    Route::get('/group-of-material/select', [GroupMaterialController::class, 'select'])->name('gom.select');
    Route::post('/group-of-material/store', [GroupMaterialController::class, 'store'])->name('gom.store');
});

Route::prefix('hr')->name('hr.')->group(function () {
    Route::get('/department/index', [DepartmentController::class, 'index'])->name('department.index');
     Route::get('/department/select', [DepartmentController::class, 'select'])->name('department.select');
    Route::get('/department/data', [DepartmentController::class, 'data'])->name('department.data');
    Route::post('/department/store', [DepartmentController::class, 'store'])->name('department.store');
    Route::get('/sections/by-department/{department}',
    [EmployeeController::class, 'getSectionByDepartment']
);
     Route::get('/position/index', [PositionController::class, 'index'])->name('position.index');
      Route::get('/position/create', [PositionController::class, 'create'])->name('position.create');
      Route::get('/employee/dashboard', [EmployeeController::class, 'index'])->name('employee.index');
       Route::get('/employee/create', [EmployeeController::class, 'create'])->name('employee.create');
       Route::get('/employee/contract', [EmployeeController::class, 'createContract'])->name('contract.create');
       Route::get('/attendance/index', [AttendanceController::class, 'index'])->name('attendance.index');
       Route::get('/attendance/data', [AttendanceController::class, 'getDataAttendance'])->name('attendance.data');
});

Route::prefix('facility')->name('facility.')->group(function () {
    Route::get('/booking-room/index', [BookingRoomController::class, 'index'])->name('booking-room.index');
     Route::get('/booking-room/history', [BookingRoomController::class, 'history'])->name('booking-room.history');
      Route::get('/booking-room/data', [BookingRoomController::class, 'data'])->name('booking-room.data');
    Route::get('/booking-room/schedule', [BookingRoomController::class, 'schedule'])->name('booking-room.schedule');
    Route::post('/booking-room/store', [BookingRoomController::class, 'store'])->name('booking-room.store');
    Route::get('/booking-room/user-bookings', [BookingRoomController::class,'getUserBookings']);
    Route::get('/booking-room/today-schedule', [DashboardController::class,'todaySchedule']);
    Route::post('/booking-room/cancel/{id}', [BookingRoomController::class,'cancelBooking']);
    Route::get('/booking-room/export', [BookingRoomController::class, 'export'])->name('booking-room.export');
    Route::post('/booking-room/approve', [BookingRoomController::class, 'approve'])->name('booking-room.approve');
    Route::delete('/booking-room/cancelled/acknowledge/{id}', [BookingRoomController::class, 'acknowledge'])->name('booking-room.cancelled.acknowledge');
    Route::get('/room/select', [RoomController::class, 'select'])->name('room.select');
    Route::get('/room/index', [RoomController::class, 'index'])->name('room.index');
    Route::get('/room/data', [RoomController::class, 'data'])->name('room.data');
    Route::post('/room/store', [RoomController::class, 'store'])->name('room.store');
    Route::get('/room/{id}/edit', [RoomController::class, 'edit'])->name('room.edit');
    Route::put('/room/{id}/update', [RoomController::class, 'update'])->name('room.update');
    Route::delete('/room/{id}/destroy', [RoomController::class, 'destroy'])->name('room.destroy');
    Route::get('/assets-loan/index', [AssetLoanController::class, 'index'])->name('alo.index');
    Route::post('/assets-loan/store', [AssetLoanController::class, 'store'])->name('alo.store');
    Route::post('/assets-loan/approve', [AssetLoanController::class, 'approve'])->name('alo.approve');
    Route::post('/assets-loan/reject', [AssetLoanController::class, 'reject'])->name('alo.reject');
    Route::post('/assets-loan/return', [AssetLoanController::class, 'returnLoan'])->name('alo.return');
    Route::delete('/assets-loan/{loan}', [AssetLoanController::class, 'cancel'])->name('alo.cancel');
    Route::post('/assets-loan/condition', [AssetLoanController::class, 'confirmCondition'])->name('alo.condition');
    Route::get('/facility/apd', [APDController::class, 'index'])->name('apd.index');
    Route::post('/facility/adjustment/store', [APDController::class, 'storeAdjustment'])->name('adjustment.store');
    Route::get('/facility/apd/stock/{id}', [APDController::class, 'getApdStock'])->name('apd.stock');
    Route::get('/apd/movement/{id}', [APDController::class, 'movement']);
    Route::post('/apd-distribution/store', [APDController::class, 'storeDistribution'])->name('distribution.store');
    Route::post('/apd/store', [APDController::class, 'store'])->name('apd.store');
    Route::post('/apd/update/{id}', [APDController::class, 'update']);
    Route::delete('/apd/delete/{id}', [ApdController::class, 'destroy']);
    Route::get('/api/distributions/{employee}', [ApdController::class, 'getEmployeeAPD']);
    Route::get('/apd/chart/years', [APDController::class, 'getAvailableYears']);
    Route::get('/apd/chart/yearly', [ApdController::class, 'yearlyChart']);
    Route::get('/apd/reminder', [APDController::class, 'getAPDReminder']);
    Route::get('/apd/recommendation', [APDController::class, 'getAPDRecommendation']);
Route::get('/apd/{id}/globalMovement', [APDController::class, 'globalMovement']);

     Route::post('/apd-return/store', [APDController::class, 'storeReturn'])->name('return.store');
     Route::get('/apd/reminder-email', [APDController::class, 'sendAPDReminderEmail']);
Route::get('/apd/transactions/data', [APDController::class, 'data'])->name('apd.transactions.data');
Route::get('/facility/sto/index', [STOController::class, 'index'])->name('sto.index');
Route::get('/facility/sto/indexv2', [STOController::class, 'indexv2'])->name('sto.indexv2');
Route::get('/sto/reference-print', [STOController::class, 'referencePrint']);
Route::get('/facility/sto/create', [STOController::class, 'create'])->name('sto.create');
Route::post('/sto/save', [STOController::class, 'store'])->name('sto.store');
Route::get('/sto/data', [STOController::class, 'datatables'])->name('sto.data');
Route::get('/sto/{id}/edit', [STOController::class, 'edit'])->name('sto.edit');
Route::put('/sto/update/{id}', [StoController::class, 'update']);
Route::delete('/sto/delete/{id}', [STOController::class, 'destroy'])->name('sto.destroy');

Route::get('/sto/select', [STOController::class, 'selectSto'])
     ->name('sto.select');
Route::get('/article/select', [STOController::class, 'selectArticle'])
     ->name('article.select');
     Route::get('/articles/by-warehouse', [STOController::class, 'getArticlesByWarehouse']);
Route::get('/sto/get-sto-by-warehouse', [STOController::class, 'getStoByWarehouse']);
Route::get('/sto/report', [STOController::class, 'exportReport']);
Route::get('/sto/review', [STOController::class, 'exportReview']);



     

});

Route::prefix('mr')->name('mr.')->group(function () {
    Route::get('/document/index', [DocumentController::class, 'index'])->name('doc.index');
    Route::get('/document/create', [DocumentController::class, 'create'])->name('doc.create');
    Route::get('/get-document-number', [DocumentController::class, 'getDocumentNumber']);
    Route::get('/document/data', [DocumentController::class, 'data'])->name('doc.data');
    Route::get('/document/{id}/edit', [DocumentController::class, 'edit'])->name('doc.edit');
    Route::get('/document/{id}/detail', [DocumentController::class, 'show'])->name('doc.detail');
    Route::post('/document/store', [DocumentController::class, 'store'])->name('doc.store');
    Route::put('/document/{id}/update', [DocumentController::class, 'update'])->name('doc.update');
    Route::post('/document/{id}/resubmit', [DocumentController::class, 'resubmit'])->name('doc.resubmit');
    Route::post('/document/{id}/approve', [DocumentController::class, 'approve'])->name('doc.approve');
    Route::post('/document/{id}/review', [DocumentController::class, 'review'])->name('doc.review');
    Route::get('/document/copies/{id}', [DocumentController::class, 'getCopies']);
    Route::get('/document/editor/{filename}', [DocumentController::class, 'loadExcel']);
    Route::post('/document/editor-save/{id}', [DocumentController::class, 'saveExcel']);
    Route::post('/document/return/{id}', [DocumentController::class, 'returnDocument'])->name('doc.return');
    Route::post('/document/{id}/reject', [DocumentController::class, 'reject'])->name('doc.reject');
    Route::post('/document/{id}/obsolete', [DocumentController::class, 'obsolete'])->name('doc.obsolete');
    Route::post('/document/{id}/authorized', [DocumentController::class, 'authorized'])->name('doc.authorized');
    Route::post('/document/save-socialize', [DocumentController::class,'saveSocialize']);
    Route::get('/document/data', [DocumentController::class, 'data'])->name('doc.data');
    Route::post('/document/{id}/note', [DocumentController::class, 'addNote'])->name('add.note');
    Route::put('/document/{id}/resubmit', [DocumentController::class, 'resubmit']);
    Route::post(
        '/document-copy/{id}/confirm-receive',
        [DocumentController::class, 'confirmReceive']
    )->name('document.confirm.receive');
    Route::get('/mr/document/{id}/pdf', [DocumentController::class, 'generatePdf'])
    ->name('doc.pdf');
    Route::get('/documents/last-number', [DocumentController::class, 'getLastDocumentNumber'])
     ->name('doc.lastNumber');
    Route::delete('/document/{id}/destroy', [DocumentController::class, 'destroy'])->name('doc.destroy');
    Route::get('/document/{id}/revision', [DocumentController::class, 'revision'])->name('doc.rev');
    Route::post('/document/{id}/revision-update', [DocumentController::class, 'storeRevision'])->name('doc.revision.update');
    Route::get('/capa/index', [CAPAController::class, 'index'])->name('capa.index');
    Route::get('/capa/{id}/verified', [CAPAController::class, 'verified'])->name('capa.verified');
    Route::post('/capa/check-number', [CAPAController::class, 'checkNumber'])->name('capa.checkNumber');
    Route::get('/capa/data', [CAPAController::class, 'data'])->name('capa.data');
    Route::get('/capa/create', [CAPAController::class, 'create'])->name('capa.create');
    Route::post('/capa/save', [CAPAController::class, 'store'])->name('capa.store');
    Route::post('/capa/{id}/posted', [CAPAController::class, 'posted'])->name('capa.posted');
    Route::put('/capa/verified-save/{id}', [CAPAController::class, 'updateVerified'])->name('capa.verified.save');
    Route::get('/capa/{id}/review', [CAPAController::class, 'review'])->name('capa.review');
    Route::post('/capa/review-save/{id}', [CAPAController::class, 'updateReview'])->name('capa.review.save');
    Route::get('/capa/{id}/submit', [CAPAController::class, 'submit'])->name('capa.submit');
    Route::put('/capa/submit-save/{id}', [CAPAController::class, 'updateSubmitted'])->name('capa.submitted.save');
    Route::get('/capa/{id}/authorized', [CAPAController::class, 'authorized'])->name('capa.authorized');
    Route::get('/capa/{capa_id}/comments', [CAPAController::class, 'getComments']);
    Route::post('/capa/return-action', [CAPAController::class, 'returnAction'])->name('capa.returnAction');
    Route::post('/capa/return-evidence', [CAPAController::class, 'returnEvidence'])->name('capa.returnEvidence');
    Route::post('/capa/authorized-save/{id}', [CAPAController::class, 'updateAuthorized'])->name('capa.authorized.save');
    Route::delete('/capa/evidence-delete/{id}', [CAPAController::class, 'destroyEvidence'])->name('capa.evidence.destroy');
    Route::get('/capa/{id}/pdf', [CAPAController::class, 'pdf'])->name('capa.pdf');
    Route::get('/capa/{id}/print',[CAPAController::class,'print'])->name('capa.print');
    Route::get('/capa/{id}/detail', [CAPAController::class, 'detail'])->name('capa.detail');
    Route::post('/capa/approve/{id}', [CAPAController::class, 'updateApprove'])->name('capa.approve');
    Route::delete('/capa/delete/{id}', [CAPAController::class, 'destroy'])->name('capa.destroy');
    Route::get('/capa/{id}/edit', [CAPAController::class, 'edit'])->name('capa.edit');
    Route::put('/capa/{id}/update', [CAPAController::class, 'update'])->name('capa.update');
    Route::get('/capa/{id}/process', [CAPAController::class, 'process'])->name('capa.process');
    Route::put('/capa/submit-process/{id}', [CAPAController::class, 'updateProcess'])->name('capa.process.save');
    Route::delete('/capa/{capa}/document/{type}',[CAPAController::class, 'deleteDocument'])->name('capa.document.delete');
    Route::get('/capa/{deptId}/users', [CAPAController::class, 'userList'])->name('capa.user');

});

Route::prefix('production')->name('production.')->group(function () {
    Route::get('/material-movement/index', [MaterialMovementController::class, 'index'])->name('mrc.index');
     Route::get('/material-movement/create', [MaterialMovementController::class, 'create'])->name('mrc.create');
     Route::get('/workstation/index', [WorkstationController::class, 'index'])->name('workstation.index');
     Route::get('/workstation/data', [WorkstationController::class, 'data'])->name('workstation.data');
     Route::post('/workstation/store', [WorkstationController::class, 'store'])->name('workstation.store');
});

Route::prefix('setting')->name('setting.')->group(function () {
    Route::get('/user/index', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
    Route::get('/user/select', [UserController::class, 'select'])->name('user.select');
    Route::get('/user/production/lists', [UserController::class, 'selectProduction'])->name('user-production.select');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::post('/user/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggleStatus');
    Route::get('/role/index', [RoleController::class, 'index'])->name('role.index');
    Route::get('/role/data', [RoleController::class, 'data'])->name('role.data');
    Route::post('/role/store', [RoleController::class, 'store'])->name('role.store');
});

Route::prefix('ppic')->name('ppic.')->group(function () {
    Route::get('/logistic/transfer_in/index', [TransferInController::class, 'index'])->name('transfer-in.index');
    Route::get('/logistic/transfer_in/data', [TransferInController::class, 'data'])->name('transfer-in.data');
    Route::get('/logistic/transfer_in/from', [TransferInController::class, 'getLocations']);
    Route::get('/logistic/transfer_in/create', [TransferInController::class, 'create'])->name('transfer.create');
    Route::post('/logistic/transfer_in/store', [TransferInController::class, 'store'])->name('transfer.store');
    Route::post('/logistic/transfer_in/{id}/approve', [TransferInController::class, 'approve'])->name('transfer.approve');
    Route::get('/logistic/transfer_in/show/{id}', [TransferInController::class, 'show'])->name('transfer-in.show');
    Route::delete('/logistic/transfer_in/{id}', [TransferInController::class, 'destroy'])->name('transfer-in.destroy');
    Route::get('/logistic/transfer_in/search_all', [TransferInController::class, 'searchAll'])->name('transfer-in.searchAll');
    Route::get('/logistic/transfer_in/article-search', [ArticleController::class, 'search'])->name('transfer-in.search');
    Route::get('/logistic/transfer_in/find/{code}', [ArticleController::class, 'find'])->name('transfer-in.find');
    Route::get('/logistic/transfer_in/article-dropdown', [ArticleController::class, 'dropdown'])->name('transfer-in.dropdown');
    Route::get('/logistic/transfer_in/article-by-code', [TransferInController::class, 'getArticleByCode'])->name('transfer.articleByCode');
    Route::get('/logistic/warehouse/index', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('/logistic/warehouse/data', [WarehouseController::class, 'getData'])->name('warehouse.data');
    Route::post('/logistic/warehouse/store', [WarehouseController::class, 'store'])->name('warehouse.store');
    Route::get('/logistic/warehouse/active-list', [WarehouseController::class, 'getActiveWarehouses'])->name('warehouse.list');
    Route::get('/logistic/transfer_out/index', [TransferOutController::class, 'index'])->name('transfer-out.index');
    Route::get('/logistic/transfer_out/data', [TransferOutController::class, 'data'])->name('transfer-out.data');
    Route::get('/logistic/transfer_out/show/{id}', [TransferOutController::class, 'show'])->name('transfer-out.show');
    Route::get('/logistic/transfer_out/create', [TransferOutController::class, 'create'])->name('transfer-out.create');
    Route::post('/logistic/transfer_out/store', [TransferOutController::class, 'store'])->name('transfer_out.store');
    Route::post('/logistic/transfer_out/{id}/approve', [TransferOutController::class, 'approve'])->name('transfer-out.approve');
    Route::get('/logistic/transfer_out/transfer-in-search', [TransferInController::class, 'search'])->name('transfer_in.search');
    Route::get('/logistic/transfer_out/scan-lookup/{code}', [TransferOutController::class, 'scanLookup'])->name('transfer_out.scan');
    Route::get('/logistic/transfer_out/destination', [TransferOutController::class, 'getDestinations'])->name('transfer_out.destination');
    Route::get('/logistic/receiving/dashboard', [ReceivingController::class, 'index'])->name('rec.index');
    Route::get('/logistic/receiving/create', [ReceivingController::class, 'create'])->name('rec.create');
    Route::post('/logistic/receiving/store', [ReceivingController::class, 'store'])->name('rec.store');
    Route::get('/api/get-po-by-supplier/{code}', [POController::class, 'getBySupplier']);
    Route::get('/api/get-po-items/{id}', [POController::class, 'getPoItems']);
    Route::get('/logistic/receiving/data', [ReceivingController::class, 'data'])->name('rec.data');
    Route::get('/logistic/receiving/show/{id}', [ReceivingController::class, 'show'])->name('receiving.show');
    Route::get('/logistic/stock/dashboard', [StockController::class, 'index'])->name('stock.index');
    Route::get('/logistic/stock/data', [StockController::class, 'data'])->name('stock.data');
    Route::get('/logistic/stock/movement', [StockController::class, 'movement'])->name('stock.movement');
    Route::get('/logistic/stock/periodic', [StockController::class, 'periodic'])->name('stock.periodic');
    Route::get('/generate-barcode-part/index', [BarcodePartController::class, 'index'])->name('barcode-part.index');
    Route::post('/process-barcode-part', [BarcodePartController::class, 'process'])->name('barcode-part.process');
    Route::get('/product-knowledge/index', [ProductKnowledgeController::class, 'index'])->name('product-knowledge.index');
    Route::get('/product-knowledge/data', [ProductKnowledgeController::class, 'search'])->name('product-knowledge.data');
    Route::get('/transfer-chemical/dashboard', [TransferChemicalController::class, 'index'])->name('tfcm1.index');
    Route::get('/transfer-chemical/create', [TransferChemicalController::class, 'create'])->name('tfcm1.create');
    Route::get('/transfer-chemical/api', [TransferChemicalController::class, 'chemicals'])->name('tfcm1.api');
    Route::get('/transfer-chemical/data', [TransferChemicalController::class, 'data'])->name('tfcm1.data');
    Route::post('/transfer-chemical/store', [TransferChemicalController::class, 'store'])->name('tfcm1.store');
    Route::get('/transfer-chemical/{id}', [TransferChemicalController::class, 'show'])  ->name('tfcm1.show');
    Route::get('/transfer-chemical/{transfer}/export/ims', 
    [TransferChemicalController::class, 'exportIMS'])
    ->name('tfcm1.export.ims');
    Route::get('/transfer-chemical/{transfer}/print',
    [TransferChemicalController::class, 'print'])
    ->name('ppic.tfcm1.print');
    Route::get('/transfer-chemical/data/detail',
    [TransferChemicalController::class, 'dataDetail'])
    ->name('tfcm1.data.detail');
});

Route::prefix('purchasing')->name('purchasing.')->group(function () {
    Route::get('/purchase-request/index', [PRController::class, 'index'])->name('pr.index');
    Route::get('/purchase-request/create', [PRController::class, 'create'])->name('pr.create');
    Route::get('/purchase-request/data', [PRController::class, 'data'])->name('pr.data');
    Route::get('/purchase-request/article', [PRController::class, 'getArticles'])->name('pr.article');
    Route::get('/purchase-request/{id}/show', [PRController::class, 'show'])->name('pr.show');
    Route::post('/purchase-request/store', [PRController::class, 'store'])->name('pr.store');
    Route::post('/purchase-request/{pr}/approve', [PRController::class, 'approve'])->name('pr.approve');
    Route::post('/purchase-request/{pr}/authorized', [PRController::class, 'authorized'])->name('pr.authorized');
    Route::post('/purchase-request/{pr}/verified', [PRController::class, 'verified'])->name('pr.verified');
    Route::post('/purchase-request/{pr}/reject', [PRController::class, 'reject'])->name('pr.reject');
    Route::get('/purchase-request/article-search', [PRController::class, 'search'])->name('pr.search');
    Route::get('/purchase-request/by-supplier/{supplierId}', [PRController::class, 'getPRBySupplier'])->name('pr.supplier');
    Route::get('/pr/by-supplier', [PRController::class, 'bySupplier'])->name('pr.by_supplier');
    Route::get('/purchase-requests/by-supplier/{supplierCode}', [PRController::class, 'getBySupplier']);
    Route::get('/purchase-request-items/by-ids', [PRController::class, 'getByIds']);
    Route::get('/last-price/{article_code}', [POController::class, 'getLastPrice'])->name('po.price_history');
    Route::get('/price-history/{article_code}', [POController::class, 'getPriceHistory']);
    Route::get('/supplier/lists', [SupplierController::class, 'select'])->name('supplier.select');

    Route::get('/purchase-order/index', [POController::class, 'index'])->name('po.index');
    Route::get('/purchase-order/create', [POController::class, 'create'])->name('po.create');
    Route::post('/purchase-order/store', [POController::class, 'store'])->name('po.store');
    Route::get('/purcahse-order/data', [POController::class, 'data'])->name('po.data');
    Route::get('/purchase-order/supplier', [PRController::class, 'supplier'])->name('po.list');
    Route::post('/purchase-order/{po}/approve', [POController::class, 'approve'])->name('po.approve');
    Route::post('/purchase-order/{po}/authorized', [POController::class, 'authorized'])->name('po.authorized');
    Route::post('/purchase-order/{po}/verified', [POController::class, 'verified'])->name('po.verified');
    Route::get('/purchase-order/show/{id}', [POController::class, 'show'])->name('po.show');
    Route::get('/supplier/dashboard', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
    Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');

});

Route::prefix('marketing')->name('marketing.')->group(function () {
    Route::get('/customer/index', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::get('/setting/index', [ConversionController::class, 'indexSetting'])->name('setting.index');
    Route::get('/conversion/detail/{id}', [ConversionController::class, 'show'])->name('conversion.show');
    Route::delete('/conversion/destroy/{id}', [ConversionController::class, 'destroy'])->name('conversion.destroy');
    Route::post('/setting/store', [ConversionController::class, 'storeConversionValue'])->name('setting.store');
    Route::get('/setting/data', [ConversionController::class, 'dataConversionValue'])->name('setting.data');
    Route::get('/price/index', [ConversionController::class, 'indexPrice'])->name('price.index');
    Route::get('/pricing/data', [ConversionController::class,'getPricingData'])->name('price.data');
    Route::post('pricing/sync', [ConversionController::class, 'syncPricing'])->name('pricing.sync');
    Route::get('/article/data', [ConversionController::class,'getArticles'])->name('article.data');
    Route::post('/basic-price/store', [ConversionController::class, 'storeBasicPrice'])->name('price.store');
    Route::get('/conversion/index', [ConversionController::class,'index'])->name('conversion.index');
    Route::get('/conversion/datatable', [ConversionController::class,'data'])->name('conversion.datatable');
    Route::get('/conversion/create', [ConversionController::class,'create'])->name('conversion.create');
     Route::get('/conversion/data', [ConversionController::class,'getConversion'])->name('conversion.data');
     Route::post('/conversion/store', [ConversionController::class,'store'])->name('conversion.store');
     Route::get('/conversion/chart', [ConversionController::class, 'conversionChart'])
    ->name('conversion.chart');
    
    
});

Route::prefix('it')->name('it.')->group(function () {
    Route::get('/ticket/index', [TicketController::class, 'index'])->name('ticket.index');
    Route::get('/ticket/data', [TicketController::class, 'data'])->name('ticket.data');
    Route::get('/ticket/create', [TicketController::class, 'create'])->name('ticket.create');
    Route::get('/ticket/{id}/edit', [TicketController::class, 'edit'])->name('ticket.edit');
    Route::delete('/attachment/{id}/destroy', [TicketController::class, 'destroy_attachment'])->name('ticket.destroy_attachment');
    Route::put('/ticket/{id}/update', [TicketController::class, 'update'])->name('ticket.update');
    Route::get('/ticket/detail/{id}', [TicketController::class, 'show'])->name('ticket.show');
    Route::post('/ticket/store', [TicketController::class, 'store'])->name('ticket.store');
    Route::post('/ticket/{ticket}/approve', [TicketController::class, 'approve'])->name('ticket.approve');
    Route::post('/ticket/{ticket}/reject', [TicketController::class, 'reject'])->name('ticket.reject');
    Route::post('/ticket/{ticket}/process', [TicketController::class, 'process'])->name('ticket.process');
    Route::post('/ticket/{id}/hold', [TicketController::class, 'hold'])->name('ticket.hold');
    Route::post('/ticket/{id}/resume', [TicketController::class, 'resume'])->name('ticket.resume');
    Route::post('/ticket/{id}/done', [TicketController::class, 'done'])->name('ticket.done');
    Route::post('/ticket/{id}/close', [TicketController::class, 'close'])->name('ticket.close');
    Route::get('/ticket/report', [TicketController::class, 'dailyReport']);
    Route::delete('/ticket/{id}/destroy', [TicketController::class, 'destroy'])->name('ticket.destroy');
    Route::get('/category/index', [TicketCategoryController::class, 'index'])->name('category.index');
    Route::get('/category/data', [TicketCategoryController::class, 'data'])->name('category.data');
    Route::get('/category/dropdown', [TicketCategoryController::class, 'getCategoryDropdown'])->name('category.dropdown');
    Route::post('/category/store', [TicketCategoryController::class, 'store'])->name('category.store');
    Route::get('/departments/{department}/users', [DepartmentController::class, 'api'])->name('category.user');
    Route::get('/project/index', [ITProjectController::class, 'index'])->name('project.index');
    Route::get('/project/create', [ITProjectController::class, 'create'])->name('project.create');
    Route::get('/assets/index', [ITAssetsController::class, 'index'])->name('assets.index');
    Route::get('/assets/data', [ITAssetsController::class, 'data'])->name('assets.data');
    Route::get('/assets/create', [ITAssetsController::class, 'create'])->name('assets.create');
    Route::get('/assets/edit/{id}', [ITAssetsController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/update/{id}', [ITAssetsController::class, 'update'])->name('assets.update');
    Route::post('/assets/store', [ITAssetsController::class, 'store'])->name('assets.store');
    Route::get('/backup/index', [ITBackupController::class, 'index'])->name('backup.index');
     Route::get('/backup/data', [ITBackupController::class, 'data'])->name('backup.data');
    Route::post('/backup/store', [ITBackupController::class, 'store'])->name('backup.store');
    Route::get('/backup-schedule/index', [ITBackupScheduleController::class, 'index'])->name('backup-schedule.index');
    Route::get('/backup-schedule/data', [ITBackupScheduleController::class, 'data'])->name('backup-schedule.data');
    Route::get('/backup-schedule/select', [ITBackupScheduleController::class, 'select'])->name('backup-schedule.select');
    Route::post('/backup-schedule/store', [ITBackupScheduleController::class, 'store'])->name('backup-schedule.store');
    Route::get('/storage/index', [ITStorageController::class, 'index'])->name('storage.index');
    Route::get('/storage/select', [ITStorageController::class, 'select'])->name('storage.select');
    Route::get('/storage/data', [ITStorageController::class, 'data'])->name('storage.data');
    Route::get('/storage/create', [ITStorageController::class, 'create'])->name('storage.create');
    Route::post('/storage/store', [ITStorageController::class, 'store'])->name('storage.store');
    Route::get('/issue-tracker/index', [IssueTrackerController::class, 'index'])->name('issue.index');
    Route::get('/issue-tracker/create', [IssueTrackerController::class, 'create'])->name('issue.create');
    Route::post('/issue-tracker/store', [IssueTrackerController::class, 'store'])->name('issue.store');
    Route::get('/issue-tracker/data', [IssueTrackerController::class, 'data'])->name('issue.data');
    Route::post('/issue-tracker/{id}/approve', [IssueTrackerController::class, 'approve'])->name('issue.approve');
    Route::post('/issue-tracker/{id}/checking', [IssueTrackerController::class, 'checking'])->name('issue.checking');
    Route::post('/issue-tracker/{id}/verification', [IssueTrackerController::class, 'verification'])->name('issue.verification');
    Route::post('/issue-tracker/{id}/authorized', [IssueTrackerController::class, 'authorized'])->name('issue.authorized');
    Route::post('/issue-tracker/{id}/done', [IssueTrackerController::class, 'done'])->name('issue.done');
    Route::post('/issue-tracker/{id}/closed', [IssueTrackerController::class, 'close'])->name('issue.closed');
    Route::post('/issue-tracker/{id}/reject', [IssueTrackerController::class, 'reject'])->name('issue.reject');
    Route::get('/issue-tracker/detail/{id}', [IssueTrackerController::class, 'show'])->name('issue.show');
    Route::get('/issue-tracker/edit/{id}', [IssueTrackerController::class, 'edit'])->name('issue.edit');
    Route::put('/issue-tracker/{id}/update', [IssueTrackerController::class, 'update'])->name('issue.update');
    Route::delete('/issue-tracker/{id}/destroy', [IssueTrackerController::class, 'destroy'])->name('issue.destroy');
     Route::get('/issue-tracker/monthly_report', [IssueTrackerController::class, 'monthlyReport'])->name('issue.monthly');
    
     Route::get('/remote-access/index', [RemoteAccessController::class, 'index'])->name('remote.index');
    });

    Route::prefix('qc')->name('qc.')->group(function () {  
    Route::get('/qc/master-defect/dashboard', [DefectController::class, 'indexv2'])->name('defect.index');
    Route::get('/qc/master-defect/data', [DefectController::class, 'datav2'])->name('defect.data');
    Route::get('/get-articles', [ArticleController::class, 'getByInspectionPost']);
    Route::get('/get-article-by-code', [ArticleController::class, 'getArticleByCode']);
    Route::get('/get-defects/{post}', [DefectController::class, 'getByInspectionPost']);
    Route::post('/qc/master-defect/store', [DefectController::class, 'storev2'])->name('defect.store');
    Route::get('/qc/unloading/dashboard', [InspectionController::class, 'unloading'])->name('unloading.index');
    Route::get('/inspections/dashboard', [InspectionController::class, 'index'])->name('inspections.index');
    Route::get('/inspections/data', [InspectionController::class, 'data'])->name('inspections.data');
    Route::get('/inspections/create', [InspectionController::class, 'create'])->name('inspections.create');
    Route::get('/inspections/operator/create', [InspectionController::class, 'createOperator'])->name('inspections.create.operator');
    Route::post('/inspections/store', [InspectionController::class, 'store'])->name('inspections.store');
    Route::get('/inspections/{id}/detail', [InspectionController::class, 'show'])->name('inspections.show');
    Route::put('/inspections/{id}/update', [InspectionController::class, 'update'])->name('inspections.update');
    Route::delete('/inspections/{id}/destroy', [InspectionController::class, 'destroy'])->name('inspections.destroy');
    Route::get('/inspection/summary', [InspectionController::class, 'getSummary']);
    Route::get('/inspection/top-defect', [InspectionController::class, 'getTopDefect']);
    Route::get('/inspection/chart', [InspectionController::class, 'getDataChart'])->name('inspection.chart');
Route::get('/inspection/pareto', [InspectionController::class, 'paretoDefect'])->name('inspection.pareto');
    Route::get('/incoming/dashboard', [IncomingInspectionController::class, 'index'])->name('incoming.index');
    Route::get('/incoming/data', [IncomingInspectionController::class, 'data'])->name('incoming.data');
    Route::get('/incoming/create', [IncomingInspectionController::class, 'create'])->name('incoming.create');
    Route::post('/incoming/store', [IncomingInspectionController::class, 'store'])->name('incoming.store');
    Route::get('/incoming/{id}/detail', [IncomingInspectionController::class, 'show'])->name('incoming.show');
    Route::post('/incoming/{id}/verified', [IncomingInspectionController::class, 'verified']);
    Route::get('/api/articles/by-supplier/{supplierId}', [ArticleController::class, 'getBySupplier']);
    Route::get('/api/inspections/filter', [InspectionController::class, 'getInspectionNumbers']);
    Route::get('/inspection/edit/{id}', [InspectionController::class, 'edit'])->name('inspection.edit');
    Route::put('/inspection/update/{id}', [InspectionController::class, 'update'])->name('inspection.update');


    });

   Route::prefix('fa')->name('fa.')->group(function () {  
    Route::get('calculator-bom', [CalculatorBOMController::class, 'index'])->name('cabom.index');
     Route::get('demo/logistik', [CalculatorBOMController::class, 'demo'])->name('demo.logistik');
    Route::post('excel/uploadBOM', [CalculatorBOMController::class, 'upload'])->name('cabom.upload');
    Route::get('data/fg', [CalculatorBOMController::class, 'getFinishGoods'])->name('cabom.fg');
    Route::get('data/cm', [CalculatorBOMController::class, 'getChemical'])->name('cabom.select-cm');
    Route::get('data/cm-table', [CalculatorBOMController::class, 'getChemicalByFG'])->name('cabom.cm');
    Route::get('data/fg-table', [CalculatorBOMController::class, 'getFGByChemical'])->name('cabom.cm-table');
    Route::get('get-cm-info', [CalculatorBOMController::class, 'getFGInfo'])->name('cabom.get-fg-info');
    Route::get('get-fg-info', [CalculatorBOMController::class, 'getCMInfo'])->name('cabom.get-cm-info');
    Route::get('get-cm-buy', [CalculatorBOMController::class, 'getCmTotalBuy'])->name('cabom.get-cm-total-buy');
    Route::get('data/rm-table', [CalculatorBOMController::class, 'getRMByFG'])->name('cabom.rm'); 
    Route::get('chemical-check', [CalculatorBOMController::class, 'cekcm'])->name('cmbom.index');
    Route::post('/excel/import', [CalculatorBOMController::class, 'importBOM'])->name('cmbom.upload');
    Route::get('/excel/rm', [CalculatorBOMController::class, 'getRM']);
    Route::get('/excel/cm', [CalculatorBOMController::class, 'getCM']);
    Route::get('/excel/fg', [CalculatorBOMController::class, 'getFG']);
    Route::get('/excel/fgrm', [CalculatorBOMController::class, 'getFGbyRM']);
    Route::get('/excel/export-cm-fg', [CalculatorBOMController::class, 'exportCMFG'])->name('cmbom.export');
    Route::get('/excel/export-rm-fg', [CalculatorBOMController::class, 'exportRMFG'])->name('rmbom.export');
    Route::get('/excel/export-cm-summary', [CalculatorBOMController::class, 'exportChemicalSummaryFull'])->name('cabom.export');
    Route::get('calculator-fuel', [CalculatorFuelController::class, 'index'])->name('bbm.index');
    Route::post('/bbm/store', [CalculatorFuelController::class, 'storeBBM'])->name('bbm.store');
    Route::post('/armada/store', [CalculatorFuelController::class, 'storeArmada'])->name('armada.store');
    Route::put('/bbm/update/{id}', [CalculatorFuelController::class, 'updateBBM'])->name('bbm.update');
    Route::put('/armada/update/{id}', [CalculatorFuelController::class, 'updateArmada'])->name('armada.update');
    Route::delete('/armada/delete/{id}', [CalculatorFuelController::class, 'destroyArmada']);
    Route::delete('/bbm/delete/{id}', [CalculatorFuelController::class, 'destroyBBM']);
    Route::get('/rekap-bupot/index', [RekapBupotController::class, 'index'])->name('bupot.index');
    Route::post('/process-pdf', [RekapBupotController::class, 'process'])->name('bupot.process');
    });


    Route::prefix('cooperative')->name('cooperative.')->group(function () {
    Route::get('/sales/dashboard', [SalesController::class, 'index'])->name('sales.index');
        });
});

require __DIR__.'/auth.php';