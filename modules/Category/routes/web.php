<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'web'])
    ->group(function () {
        Route::livewire('category', 'category::index')->name('category.index');
        Route::livewire('category/{category}/edit', 'category::index')->name('category.edit');
    });

