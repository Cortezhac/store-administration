<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Models\Category;

Route::middleware(['auth', 'web'])
    ->group(function () {
        Route::livewire('category', 'category::index')->name('category.index');
        Route::livewire('category/create', 'category::form')->name('category.create');
        Route::livewire('category/{category}', 'category::show')->name('category.show');
        Route::livewire('category/{category}/edit', 'category::form')->name('category.edit');

        Route::get('category/{category}/icon', function (Category $category) {
            if ($category->icon === null) {
                abort(404);
            }

            if (! Storage::disk('local')->exists($category->icon)) {
                abort(404);
            }

            return Storage::disk('local')->response($category->icon);
        })->name('category.icon');
    });
