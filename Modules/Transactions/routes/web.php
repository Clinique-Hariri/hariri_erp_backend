<?php

use Illuminate\Support\Facades\Route;
use Modules\Transactions\Http\Controllers\TransactionsController;

Route::middleware(['auth', 'verified'])->group(function () {
});
