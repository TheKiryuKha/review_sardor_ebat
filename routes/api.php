<?php

declare(strict_types=1);

namespace Routes\api;

use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/payments/process', [PaymentController::class, 'process']);