<?php

use Illuminate\Http\Middleware\CheckResponseForModifications;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Controllers\ActionController;
use Laravel\Nova\Http\Controllers\AssociatableController;
use Laravel\Nova\Http\Controllers\AttachableController;
use Laravel\Nova\Http\Controllers\AttachedResourceUpdateController;
use Laravel\Nova\Http\Controllers\CardController;
use Laravel\Nova\Http\Controllers\CreationFieldController;
use Laravel\Nova\Http\Controllers\CreationPivotFieldController;
use Laravel\Nova\Http\Controllers\DashboardCardController;
use Laravel\Nova\Http\Controllers\DashboardController;
use Laravel\Nova\Http\Controllers\DashboardMetricController;
use Laravel\Nova\Http\Controllers\DetailMetricController;
use Laravel\Nova\Http\Controllers\FieldController;
use Laravel\Nova\Http\Controllers\FieldDestroyController;
use Laravel\Nova\Http\Controllers\FieldDownloadController;
use Laravel\Nova\Http\Controllers\FieldPreviewController;
use Laravel\Nova\Http\Controllers\FilterController;
use Laravel\Nova\Http\Controllers\ImpersonateController;
use Laravel\Nova\Http\Controllers\LensActionController;
use Laravel\Nova\Http\Controllers\LensCardController;
use Laravel\Nova\Http\Controllers\LensController;
use Laravel\Nova\Http\Controllers\LensFilterController;
use Laravel\Nova\Http\Controllers\LensMetricController;
use Laravel\Nova\Http\Controllers\LensResourceCountController;
use Laravel\Nova\Http\Controllers\LensResourceDestroyController;
use Laravel\Nova\Http\Controllers\LensResourceForceDeleteController;
use Laravel\Nova\Http\Controllers\LensResourceRestoreController;
use Laravel\Nova\Http\Controllers\MetricController;
use Laravel\Nova\Http\Controllers\MorphableController;
use Laravel\Nova\Http\Controllers\MorphedResourceAttachController;
use Laravel\Nova\Http\Controllers\NotificationDeleteController;
use Laravel\Nova\Http\Controllers\NotificationIndexController;
use Laravel\Nova\Http\Controllers\NotificationReadAllController;
use Laravel\Nova\Http\Controllers\NotificationReadController;
use Laravel\Nova\Http\Controllers\PivotFieldDestroyController;
use Laravel\Nova\Http\Controllers\RelatableAuthorizationController;
use Laravel\Nova\Http\Controllers\ResourceAttachController;
use Laravel\Nova\Http\Controllers\ResourceCountController;
use Laravel\Nova\Http\Controllers\ResourceDestroyController;
use Laravel\Nova\Http\Controllers\ResourceDetachController;
use Laravel\Nova\Http\Controllers\ResourceForceDeleteController;
use Laravel\Nova\Http\Controllers\ResourceIndexController;
use Laravel\Nova\Http\Controllers\ResourcePreviewController;
use Laravel\Nova\Http\Controllers\ResourceRestoreController;
use Laravel\Nova\Http\Controllers\ResourceSearchController;
use Laravel\Nova\Http\Controllers\ResourceShowController;
use Laravel\Nova\Http\Controllers\ResourceStoreController;
use Laravel\Nova\Http\Controllers\ResourceUpdateController;
use Laravel\Nova\Http\Controllers\ScriptController;
use Laravel\Nova\Http\Controllers\SearchController;
use Laravel\Nova\Http\Controllers\SoftDeleteStatusController;
use Laravel\Nova\Http\Controllers\StyleController;
use Laravel\Nova\Http\Controllers\TrixAttachmentController;
use Laravel\Nova\Http\Controllers\UpdateFieldController;
use Laravel\Nova\Http\Controllers\UpdatePivotFieldController;

Route::domain(env('APP_URL'))->group(function () {

// Scripts & Styles...
Route::get('/scripts/{script}', ScriptController::class)->middleware(CheckResponseForModifications::class);
Route::get('/styles/{style}', StyleController::class)->middleware(CheckResponseForModifications::class);

// Global Search...
Route::get('/search', SearchController::class);

// Impersonation...
Route::post('impersonate', [ImpersonateController::class, 'impersonate']);
Route::delete('impersonate', [ImpersonateController::class, 'stopImpersonating']);

// Fields...
Route::get('/{resource}/field/{field}', FieldController::class);
Route::post('/{resource}/field/{field}/preview', FieldPreviewController::class);
Route::post('/{resource}/trix-attachment/{field}', [TrixAttachmentController::class, 'store']);
Route::delete('/{resource}/trix-attachment/{field}', [TrixAttachmentController::class, 'destroyAttachment']);
Route::delete('/{resource}/trix-attachment/{field}/{draftId}', [TrixAttachmentController::class, 'destroyPending']);
Route::get('/{resource}/creation-fields', CreationFieldController::class);
Route::get('/{resource}/{resourceId}/update-fields', UpdateFieldController::class);
Route::get('/{resource}/{resourceId}/creation-pivot-fields/{relatedResource}', CreationPivotFieldController::class);
Route::get('/{resource}/{resourceId}/update-pivot-fields/{relatedResource}/{relatedResourceId}', UpdatePivotFieldController::class);
Route::patch('/{resource}/creation-fields', [CreationFieldController::class, 'sync']);
Route::patch('/{resource}/{resourceId}/update-fields', [UpdateFieldController::class, 'sync']);
Route::patch('/{resource}/{resourceId}/creation-pivot-fields/{relatedResource}', [CreationPivotFieldController::class, 'sync']);
Route::patch('/{resource}/{resourceId}/update-pivot-fields/{relatedResource}/{relatedResourceId}', [UpdatePivotFieldController::class, 'sync']);
Route::get('/{resource}/{resourceId}/download/{field}', FieldDownloadController::class);
Route::delete('/{resource}/{resourceId}/field/{field}', FieldDestroyController::class);
Route::delete('/{resource}/{resourceId}/{relatedResource}/{relatedResourceId}/field/{field}', PivotFieldDestroyController::class);

// Dashboards...
Route::get('/dashboards/{dashboard}', DashboardController::class);
Route::get('/dashboards/cards/{dashboard}', DashboardCardController::class);

// Notifications...
Route::get('/nova-notifications', NotificationIndexController::class);
Route::post('/nova-notifications/read-all', NotificationReadAllController::class);
Route::post('/nova-notifications/{notification}/read', NotificationReadController::class);
Route::delete('/nova-notifications/{notification}/delete', NotificationDeleteController::class);

// Actions...
Route::get('/{resource}/actions', [ActionController::class, 'index']);
Route::post('/{resource}/action', [ActionController::class, 'store']);
Route::patch('/{resource}/action', [ActionController::class, 'sync']);

// Filters...
Route::get('/{resource}/filters', FilterController::class);

// Lenses...
Route::get('/{resource}/lenses', [LensController::class, 'index']);
Route::get('/{resource}/lens/{lens}', [LensController::class, 'show']);
Route::get('/{resource}/lens/{lens}/count', LensResourceCountController::class);
Route::delete('/{resource}/lens/{lens}', LensResourceDestroyController::class);
Route::delete('/{resource}/lens/{lens}/force', LensResourceForceDeleteController::class);
Route::put('/{resource}/lens/{lens}/restore', LensResourceRestoreController::class);
Route::get('/{resource}/lens/{lens}/actions', [LensActionController::class, 'index']);
Route::post('/{resource}/lens/{lens}/action', [LensActionController::class, 'store']);
Route::patch('/{resource}/lens/{lens}/action', [LensActionController::class, 'sync']);
Route::get('/{resource}/lens/{lens}/filters', [LensFilterController::class, 'index']);

// Cards / Metrics...
Route::get('/metrics/{metric}', DashboardMetricController::class);
Route::get('/{resource}/metrics', [MetricController::class, 'index']);
Route::get('/{resource}/metrics/{metric}', [MetricController::class, 'show']);
Route::get('/{resource}/{resourceId}/metrics/{metric}', DetailMetricController::class);

Route::get('/{resource}/lens/{lens}/metrics', [LensMetricController::class, 'index']);
Route::get('/{resource}/lens/{lens}/metrics/{metric}', [LensMetricController::class, 'show']);

Route::get('/{resource}/cards', CardController::class);
Route::get('/{resource}/lens/{lens}/cards', LensCardController::class);

// Authorization Information...
Route::get('/{resource}/relate-authorization', RelatableAuthorizationController::class);

// Soft Delete Information...
Route::get('/{resource}/soft-deletes', SoftDeleteStatusController::class);

// Resource Management...
Route::get('/{resource}', ResourceIndexController::class);
Route::get('/{resource}/search', ResourceSearchController::class);
Route::get('/{resource}/count', ResourceCountController::class);
Route::delete('/{resource}/detach', ResourceDetachController::class);
Route::put('/{resource}/restore', ResourceRestoreController::class);
Route::delete('/{resource}/force', ResourceForceDeleteController::class);
Route::get('/{resource}/{resourceId}', ResourceShowController::class);
Route::get('/{resource}/{resourceId}/preview', ResourcePreviewController::class);
Route::post('/{resource}', ResourceStoreController::class);
Route::put('/{resource}/{resourceId}', ResourceUpdateController::class);
Route::delete('/{resource}', ResourceDestroyController::class);

// Associatable Resources...
Route::get('/{resource}/associatable/{field}', AssociatableController::class);
Route::get('/{resource}/{resourceId}/attachable/{field}', AttachableController::class);
Route::get('/{resource}/morphable/{field}', MorphableController::class);

// Resource Attachment...
Route::post('/{resource}/{resourceId}/attach/{relatedResource}', ResourceAttachController::class);
Route::post('/{resource}/{resourceId}/update-attached/{relatedResource}/{relatedResourceId}', AttachedResourceUpdateController::class);
Route::post('/{resource}/{resourceId}/attach-morphed/{relatedResource}', MorphedResourceAttachController::class);
});