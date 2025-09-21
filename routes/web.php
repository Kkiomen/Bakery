<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('bakery-homepage');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    // Trasy dla kartoteki towarowej (surowce)
    Route::prefix('materials')->name('materials.')->group(function () {
        Route::get('/', App\Livewire\Materials\MaterialIndex::class)->name('index');
        Route::get('/create', App\Livewire\Materials\MaterialCreate::class)->name('create');
        Route::get('/{material}', function (App\Models\Material $material) {
            return view('materials.show', compact('material'));
        })->name('show');
        Route::get('/{material}/edit', App\Livewire\Materials\MaterialForm::class)->name('edit');
    });

    // Trasy dla kartoteki produktowej (gotowe wypieki)
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', App\Livewire\Products\ProductIndex::class)->name('index');
        Route::get('/create', App\Livewire\Products\ProductCreate::class)->name('create');
        Route::get('/{product}', function (App\Models\Product $product) {
            return view('products.show', compact('product'));
        })->name('show');
        Route::get('/{product}/edit', App\Livewire\Products\ProductForm::class)->name('edit');
        Route::get('/{product}/substitutes', App\Livewire\Products\ProductSubstitutes::class)->name('substitutes');
    });

    // Trasy dla kartoteki receptur
    Route::prefix('recipes')->name('recipes.')->group(function () {
        Route::get('/', App\Livewire\Recipes\RecipeIndex::class)->name('index');
        Route::get('/create', App\Livewire\Recipes\RecipeCreate::class)->name('create');
        Route::get('/{recipe}', function (App\Models\Recipe $recipe) {
            return view('recipes.show', compact('recipe'));
        })->name('show');
        Route::get('/{recipe}/edit', App\Livewire\Recipes\RecipeForm::class)->name('edit');
        Route::get('/{recipe}/steps', App\Livewire\Recipes\RecipeSteps::class)->name('steps');
    });

    // Trasy dla zarządzania produkcją
    Route::prefix('production')->name('production.')->group(function () {
        Route::get('/', App\Livewire\Production\ProductionOrdersList::class)->name('orders.index');
        Route::get('/calendar', App\Livewire\Production\ProductionCalendar::class)->name('calendar');
        Route::get('/orders/create', function () {
            return view('production.orders.create');
        })->name('orders.create');
        Route::get('/orders/{order}', function (App\Models\ProductionOrder $order) {
            return view('production.orders.show', compact('order'));
        })->name('orders.show');
        Route::get('/orders/{order}/edit', function (App\Models\ProductionOrder $order) {
            return view('production.orders.edit', compact('order'));
        })->name('orders.edit');
    });

    // Trasy dla panelu piekarzy
    Route::prefix('baker')->name('baker.')->group(function () {
        Route::get('/', function () {
            return view('baker.dashboard');
        })->name('dashboard');
    });

    // Test Livewire
    Route::get('/test-livewire', App\Livewire\Test\SimpleTest::class)->name('test-livewire');

    // Trasy dla kontrahentów
    Route::prefix('contractors')->name('contractors.')->group(function () {
        Route::get('/', function () {
            return view('contractors.index');
        })->name('index');
        Route::get('/{contractor}/edit', function (\App\Models\Contractor $contractor) {
            return view('contractors.edit', compact('contractor'));
        })->name('edit');
    });

    // Trasy dla zarządzania dostawami (właściciel/admin)
    Route::prefix('deliveries')->name('deliveries.')->group(function () {
        Route::get('/test', function () {
            return view('deliveries.test');
        })->name('test');
        Route::get('/simple-test', App\Livewire\Deliveries\SimpleDeliveryTest::class)->name('simple-test');
        Route::get('/simple', function () {
            return view('deliveries.simple');
        })->name('simple');
        Route::get('/', function () {
            return view('deliveries.index');
        })->name('index');
        Route::get('/create', function () {
            return view('deliveries.create');
        })->name('create');
        Route::get('/{delivery}', function (App\Models\Delivery $delivery) {
            return view('deliveries.show', compact('delivery'));
        })->name('show');
        Route::get('/{delivery}/edit', function (App\Models\Delivery $delivery) {
            return view('deliveries.edit', compact('delivery'));
        })->name('edit');
    });

    // Trasy dla panelu kierowcy
    Route::prefix('driver')->name('driver.')->group(function () {
        Route::get('/', App\Livewire\Driver\DriverDashboard::class)->name('dashboard');
        Route::get('/deliveries/{delivery}', function (App\Models\Delivery $delivery) {
            return view('driver.delivery-details', compact('delivery'));
        })->name('deliveries.show');
    });
});

require __DIR__.'/auth.php';
