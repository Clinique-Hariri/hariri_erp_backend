<?php

use Illuminate\Support\Facades\Route;
use Modules\Patients\Http\Controllers\PatientsApiController;

Route::middleware(['auth', 'verified'])->group(function () {

});
