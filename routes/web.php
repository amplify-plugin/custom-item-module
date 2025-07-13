<?php

use Amplify\System\CustomItem\Http\Controllers\CartController;
use Amplify\System\CustomItem\Http\Controllers\CheckoutController;
use Amplify\System\CustomItem\Http\Controllers\CustomItemController;
use Amplify\System\CustomItem\Http\Controllers\CuttingBoardController;
use Amplify\System\CustomItem\Http\Controllers\DrainTubeHeaterController;
use Amplify\System\CustomItem\Http\Controllers\EvaporatorCoilController;
use Amplify\System\CustomItem\Http\Controllers\GasketController;
use Amplify\System\CustomItem\Http\Controllers\HeaterWireController;
use Amplify\System\CustomItem\Http\Controllers\ShelvingController;
use Amplify\System\CustomItem\Http\Controllers\StripCurtainsBulkController;
use Amplify\System\CustomItem\Http\Controllers\StripCurtainsCompletedController;
use Amplify\System\CustomItem\Http\Controllers\StripCurtainsController;
use Amplify\System\CustomItem\Http\Controllers\TubularHeaterController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
    Route::get('/product/price', [HeaterWireController::class, 'getProductByCode']);
    Route::post('/custom-item/add-to-cart', [HeaterWireController::class, 'addToCart']);

});

Route::group(['prefix' => 'api/custom-item', 'as' => 'custom-item.'], function () {

    Route::group(['prefix' => 'drain-tube-heater', 'as' => 'drain-tube-heater.', 'controller' => DrainTubeHeaterController::class], function () {
        Route::get('/product', 'getProductByCode');
        Route::post('/add-to-cart', 'addToCart');
    });

    Route::group(['prefix' => 'heater-wire', 'as' => 'heater-wire.', 'controller' => HeaterWireController::class], function () {
        Route::get('/products', 'getProducts');
        Route::post('/add-to-cart', 'addToCart');
        Route::post('/product/price', 'shelvingProductPrice');
    });

    Route::group(['prefix' => 'shelving', 'as' => 'shelving.', 'controller' => ShelvingController::class], function () {

        Route::post('/add-to-cart', 'addToCart');
        Route::post('/product/price', 'shelvingProductPrice');
    });

    Route::group(['prefix' => 'strip-replacement', 'as' => 'strip-replacement.', 'controller' => StripCurtainsController::class], function () {
        Route::post('/add-to-cart', 'addToCart');
        Route::get('/strips', 'getStrips');
        Route::get('/product-price/{product}', 'getStripProductPrice');
    });

    Route::group(['prefix' => 'strip-curtains/completed', 'as' => 'strip-replacement.completed.', 'controller' => StripCurtainsCompletedController::class], function () {
        Route::post('/add-to-cart', 'addToCart');
        Route::get('/product-price/{product}', 'getStripProductPrice');
    });

    Route::group(['prefix' => 'strip-curtains/bulk', 'as' => 'strip-replacement.bulk.', 'controller' => StripCurtainsBulkController::class], function () {
        Route::post('/add-to-cart', 'addToCart');
        Route::get('/strips', 'getBulkStrips');
        Route::get('/{product}', 'getStripProductPrice');
    });

    Route::group(['prefix' => 'cutting-board', 'as' => 'strip-replacement.', 'controller' => CuttingBoardController::class], function () {
        Route::post('/add-to-cart', 'addToCart');
        Route::get('/products', 'getProducts');
        Route::get('/product-price/{product}', 'getStripProductPrice');
    });

    Route::group(['prefix' => 'gasket', 'as' => 'gasket.', 'controller' => GasketController::class], function () {
        Route::post('/add-to-cart', 'addToCart');
        Route::get('/type', 'getProductType');
        Route::post('/products', 'getProducts');
        Route::get('/{type}/product-list', 'getProductsList');
        Route::get('/product-price/{product}', 'getStripProductPrice');
        Route::post('/profile-list', 'getProfileList');
        Route::post('/profiles', 'getProfiles');
        Route::get('/price', 'getPrice');
    });

    Route::group(['prefix' => 'tubular-heater', 'as' => 'tubular-heater.', 'controller' => TubularHeaterController::class], function () {
        Route::get('/products/{num}', 'getProducts');
        Route::get('/product/{product}', 'getProductPrice');
        Route::post('add-to-cart', 'addToCart');

    });

    Route::group(['prefix' => 'evaporator-coils', 'as' => 'evaporator-coils.', 'controller' => EvaporatorCoilController::class], function () {
        Route::get('/country-list', 'getCountryList');
        Route::post('/store', 'store');
    });

});

Route::middleware('web')->group(function () {
    Route::post('/get/shipping/option/rhs', [CheckoutController::class, 'getShippingOption']);
    Route::post('/customer/submit-order', [CheckoutController::class, 'submitOrder']);
});

Route::get('/custom-item/completed/{product}', [StripCurtainsController::class, 'customItemcompleted'])->name('frontend.custom-item.completed');

Route::get('/pdf/test', function () {
    return view('custom-item::evaporator_coil_pdf');
});

Route::get('/custom/{slug}/{product?}', [CustomItemController::class, 'index'])->name('custom.product');

Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('frontend.add-to-cart');

Route::post('/create-order-from-quote', [CartController::class, 'createOrderFromQuote'])->name('frontend.create-order-from-quote');
