<?php

use Illuminate\Support\Facades\Route;

// we'll add our public invoice routes here

Route::livewire('create-invoice', 'pages::invoice.create')
->name('create-invoice');