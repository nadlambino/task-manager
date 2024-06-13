<?php

use App\Http\Controllers\Api\v1\StatusController;
use App\Http\Controllers\Api\v1\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::put('/tasks/{task}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
    Route::get('/tasks/parents', [TaskController::class, 'parents'])->name('tasks.parents');
    Route::apiResource('/tasks', TaskController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::put('/statuses/{status}/restore', [StatusController::class, 'restore'])->name('statuses.restore');
    Route::put('/statuses/{status}/sort', [StatusController::class, 'sort'])->name('statuses.sort');
    Route::apiResource('/statuses', StatusController::class)->only(['index', 'store', 'update', 'destroy']);
});
