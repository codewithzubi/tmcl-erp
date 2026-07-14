<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarnAllocationController;
use App\Http\Controllers\BonelessProcessingRecordController;
use App\Http\Controllers\BotiProcessingRecordController;
use App\Http\Controllers\CarcassWeightRecordController;
use App\Http\Controllers\CartonController;
use App\Http\Controllers\CustomerAttachmentController;
use App\Http\Controllers\CustomerContactPersonController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDiscussionNoteController;
use App\Http\Controllers\CustomerPurchaseOrderController;
use App\Http\Controllers\CustomFieldDefinitionController;
use App\Http\Controllers\CustomFieldValueController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DutyRosterController;
use App\Http\Controllers\EventLogController;
use App\Http\Controllers\GateEntryAttachmentController;
use App\Http\Controllers\GateEntryController;
use App\Http\Controllers\GrnController;
use App\Http\Controllers\LivestockInspectionController;
use App\Http\Controllers\LivestockSupplyRecordController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\MeatAllocationController;
use App\Http\Controllers\MeatDeductionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OffalRecoveryController;
use App\Http\Controllers\OffalSettlementController;
use App\Http\Controllers\PacketController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\PurchaseRequisitionController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SlaughterRecordController;
use App\Http\Controllers\StorageSessionController;
use App\Http\Controllers\StorageUnitController;
use App\Http\Controllers\SupplierAttachmentController;
use App\Http\Controllers\SupplierContactPersonController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPurchaseOrderController;
use App\Http\Controllers\SupplierQuotationController;
use App\Http\Controllers\SupplierSettlementController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VeterinaryInspectionController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Administration
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('event-logs', EventLogController::class)->except('update');
    Route::get('/settings', [SystemSettingController::class, 'show']);
    Route::put('/settings', [SystemSettingController::class, 'update']);
    Route::apiResource('notifications', NotificationController::class)->only(['index', 'store', 'destroy']);
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
    Route::apiResource('custom-field-definitions', CustomFieldDefinitionController::class);
    Route::get('/custom-field-values', [CustomFieldValueController::class, 'index']);
    Route::put('/custom-field-values', [CustomFieldValueController::class, 'upsert']);
    Route::apiResource('shifts', ShiftController::class);
    Route::apiResource('duty-rosters', DutyRosterController::class);

    // CRM
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('customer-contact-persons', CustomerContactPersonController::class)->except('show');
    Route::apiResource('customer-discussion-notes', CustomerDiscussionNoteController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('customer-attachments', CustomerAttachmentController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('requirements', RequirementController::class);
    Route::apiResource('proposals', ProposalController::class);
    Route::apiResource('customer-purchase-orders', CustomerPurchaseOrderController::class);
    Route::apiResource('sales-orders', SalesOrderController::class);

    // Supplier
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('supplier-contact-persons', SupplierContactPersonController::class)->except('show');
    Route::apiResource('supplier-attachments', SupplierAttachmentController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('livestock-supply-records', LivestockSupplyRecordController::class)->only(['index', 'store', 'destroy']);

    // Procurement
    Route::apiResource('purchase-requisitions', PurchaseRequisitionController::class);
    Route::apiResource('supplier-quotations', SupplierQuotationController::class);
    Route::apiResource('supplier-purchase-orders', SupplierPurchaseOrderController::class);

    // Gate Management
    Route::apiResource('gate-entries', GateEntryController::class);
    Route::apiResource('gate-entry-attachments', GateEntryAttachmentController::class)->only(['index', 'store', 'destroy']);

    // Pipeline: GRN -> Livestock Inspection -> Barn Allocation -> Lot
    Route::apiResource('grns', GrnController::class);
    Route::apiResource('livestock-inspections', LivestockInspectionController::class);
    Route::apiResource('barn-allocations', BarnAllocationController::class);
    Route::apiResource('lots', LotController::class);
    Route::post('/lots/{lot}/hold', [LotController::class, 'hold']);
    Route::post('/lots/{lot}/release', [LotController::class, 'release']);

    // Slaughter -> Carcass Weight -> Veterinary Inspection -> Meat Deduction
    Route::apiResource('slaughter-records', SlaughterRecordController::class);
    Route::apiResource('offal-recoveries', OffalRecoveryController::class);
    Route::apiResource('carcass-weight-records', CarcassWeightRecordController::class);
    Route::post('/carcass-weight-records/{carcass_weight_record}/lock', [CarcassWeightRecordController::class, 'lock']);
    Route::post('/carcass-weight-records/{carcass_weight_record}/unlock', [CarcassWeightRecordController::class, 'unlock']);
    Route::apiResource('veterinary-inspections', VeterinaryInspectionController::class);
    Route::apiResource('meat-deductions', MeatDeductionController::class)->except('show');

    // Settlement & Allocation
    Route::apiResource('supplier-settlements', SupplierSettlementController::class);
    Route::apiResource('offal-settlements', OffalSettlementController::class)->except('show');
    Route::apiResource('meat-allocations', MeatAllocationController::class);

    // Cold Storage
    Route::apiResource('storage-units', StorageUnitController::class);
    Route::apiResource('storage-sessions', StorageSessionController::class);

    // Processing
    Route::apiResource('boneless-processing-records', BonelessProcessingRecordController::class);
    Route::apiResource('boti-processing-records', BotiProcessingRecordController::class);

    // Packaging
    Route::apiResource('packets', PacketController::class);
    Route::apiResource('cartons', CartonController::class);

    // Logistics
    Route::apiResource('shipments', ShipmentController::class);
    Route::apiResource('dispatches', DispatchController::class);
});
