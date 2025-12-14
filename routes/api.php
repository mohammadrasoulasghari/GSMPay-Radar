<?php

use App\Http\Controllers\Api\PrAnalysisWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::post('/webhooks/pr-analysis', [PrAnalysisWebhookController::class, 'store'])
    ->name('webhooks.pr-analysis');
