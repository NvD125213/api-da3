<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SliderController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\StaticalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiAdminMiddleware;

// Login/Register
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::get('/check-auth', [AuthController::class, 'checkAuth']);

Route::middleware(['auth:sanctum', ApiAdminMiddleware::class])->group(function() {
    Route::post('/logout', [AuthController::class,'logout']);
    Route::get('/chekingAuthenticated', function() {
        return response()->json(['message' => 'Bạn được sử dụng tuyến đường', 'status' => 200], 200);
    });
});

Route::prefix('categories')->group(function () {
    Route::get('/getParent', [CategoryController::class, 'getParent']);
    Route::get('/getSubCategories/{id}', [CategoryController::class, 'getSubCategories']);
    Route::get('/getAll', [CategoryController::class, 'getAll']);
    Route::get('/getAllWithPaginate', [CategoryController::class, 'getPaginatedCategories']);
    Route::post('/create', [CategoryController::class, 'create']);
    Route::put('/update/{id}', [CategoryController::class, 'update']);
    Route::delete('/delete/{id}', [CategoryController::class, 'delete']);
});

Route::prefix('product')->group(function() {
    Route::post('/create', [ProductController::class, 'create']);
    Route::get('/getAll', [ProductController::class, 'index']);
    Route::post('/update/{id}', [ProductController::class, 'update']);
    Route::get('/paginate', [ProductController::class, 'getProductsPaginate']);
    Route::get('/getProductCart', [ProductController::class, 'getProductCart']);
    Route::get('/getProductDetail/{id}', [ProductController::class, 'showProductDetail']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/AdvanceSearch', [ProductController::class, 'advancedSearch']);
    Route::get('/getTrendyProduct', [ProductController::class, 'getTrendyProduct']);
    Route::get('/bestsellers', [ProductController::class, 'bestsellers']);

});

Route::get('/getProductByCategory/{categoryId}',[ProductController::class,'getProductsByCategory']);
Route::prefix('order')->group(function() {
    Route::middleware('auth:sanctum')->post('/create', [CheckoutController::class, 'create']);
    Route::get('/getOrderUnconfirmed', [CheckoutController::class,'getOrderUnconfimred']);
    Route::get('/getOrdersCormfirmed', [CheckoutController::class,'getOrdersCormfirmed']);
    Route::get('/getOrderDetails/{id}', [CheckoutController::class,'getOrderDetails']);
    Route::post('/confirmOrder', [CheckoutController::class,'confirmOrder']);
    Route::get('/getOrderByUser', [CheckoutController::class,'getOrderByUserID'])->middleware('auth:sanctum');
});


Route::prefix('slider')->group(function() {
    Route::get('/getSlider', [SliderController::class,'getSlider']);
    Route::post('/create', [SliderController::class,'createSlider']);
    Route::post('/update/{id}', [SliderController::class,'updateSlider']);
    Route::delete('/delete/{id}', [SliderController::class,'deleteSlider']);
});

Route::get('/statical', [StaticalController::class, 'getStatistics']);

Route::get('/getAll', [UserController::class, 'getAllUser']);

Route::get('/categories/id={parentID}', [CategoryController::class, 'getSubCategories']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
