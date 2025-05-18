<?php

use App\Http\Controllers\BlogPost;
use App\Http\Controllers\Booking;
use App\Http\Controllers\Inventory;
use App\Http\Controllers\Service;
use App\Http\Controllers\Settings;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Style;

// Appointments Booking Routes 
Route::prefix('appointments')->name('book.')->group(function () {
  Route::post('book', [Booking::class, 'bookAppointment'])->name('appointment');
  Route::get('list', [Booking::class, 'listAppointments'])->name('list');
  Route::put('update/{id}', [Booking::class, 'updateAppointment'])->name('update');
});

// Services Routes 
Route::prefix('services')->name('services.')->group(function () {
  Route::post('create', [Service::class, 'newService'])->name('create');
  Route::get('list', [Service::class, 'listServices'])->name('list');
  Route::put('update/{id}', [Service::class, 'updateService'])->name('update');
  Route::delete('delete/{id}', [Service::class, 'delete'])->name('delete');
});

// Appointments Booking Routes 
Route::prefix('inventory')->name('inventory.')->group(function () {
  Route::post('create', [Inventory::class, 'create'])->name('create');
  Route::get('list', [Inventory::class, 'show'])->name('list');
  Route::put('update/{id}', [Inventory::class, 'update'])->name('update');
  Route::delete('delete/{id}', [Inventory::class, 'delete'])->name('delete');
});

// Styles Routes 
Route::prefix('styles')->name('styles.')->group(function () {
  Route::post('create', [Style::class, 'create'])->name('create');
  Route::get('show', [Style::class, 'show'])->name('show');
  Route::put('update/{id}', [Style::class, 'update'])->name('update');
  Route::delete('delete/{id}', [Style::class, 'delete'])->name('delete');
});

// Styles Routes 
Route::prefix('blogs')->name('blogs.')->group(function () {
  Route::post('create', [BlogPost::class, 'create'])->name('create');
  Route::get('show', [BlogPost::class, 'show'])->name('show');
  Route::put('update/{id}', [BlogPost::class, 'update'])->name('update');
  Route::get('view/{id}', [BlogPost::class, 'view'])->name('view');
  Route::delete('delete/{id}', [BlogPost::class, 'delete'])->name('delete');
});



// Admin Settings Routes
Route::prefix('settings/business/details')->name('settings.')->group(function () {
  Route::post('create', [Settings::class, 'businessDetails'])->name('business.details');
  Route::get('fetch', [Settings::class, 'fetchBusinessDetails'])->name('business.fetch');
});
