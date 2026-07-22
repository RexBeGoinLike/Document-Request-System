<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/storage/{file}', function ($file) {
    abort_unless(Storage::disk('attachments')->exists($file), 404);

    return response()->file(
        Storage::disk('attachments')->path($file),
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($file) . '"',
        ]
    );
})
    ->where('file', '.*')
    ->name('attachments.view');;

