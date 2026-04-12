<?php

use Illuminate\Support\Facades\Route;

// Rute fallback untuk React SPA.
// Harus diletakkan terakhir agar tidak menimpa rute web/API lain.
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|percetakan|keuangan|livewire|build).*$');
