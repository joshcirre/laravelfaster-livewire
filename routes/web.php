<?php

use Illuminate\Support\Facades\Route;

// Home page - shows all collections and categories
Route::livewire('/', 'pages::home')->name('home');

// Category page - shows subcategories within a category
Route::livewire('/products/{category}', 'pages::products.index')->name('products.index');

// Products listing - shows products for a specific subcategory
Route::livewire('/products/{category}/{subcategory}', 'pages::products.show')->name('products.show');

// Product detail page
Route::livewire('/products/{category}/{subcategory}/{product}', 'pages::products.product')->name('products.product');
