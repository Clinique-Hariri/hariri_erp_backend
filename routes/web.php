<?php

use Illuminate\Support\Facades\Route;

Route::get('/{locale}', function ($locale) {
  session()->put('locale', $locale);
  return redirect()->back();
});

//this project is just and api
Route::get('/', function () {
    return response()->json(['message' => 'Welcome to Hariri ERP API', 'version' => '1.0.0']);
})->name('home');
