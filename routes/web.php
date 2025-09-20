<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
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
});

require __DIR__.'/auth.php';
