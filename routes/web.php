<?php

use App\Livewire\Rekening\AccountCrud;
use App\Livewire\Rekening\AgentCrud;
use App\Livewire\Rekening\CardCrud;
use App\Livewire\Rekening\ComplaintCrud;
use App\Livewire\Rekening\CustomerCrud;
use App\Livewire\Agent\Auth\Login as AgentLogin;
use App\Livewire\Agent\Dashboard as AgentDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rekening Management Routes
Route::middleware(['auth', 'verified'])->prefix('rekening')->group(function () {
    Route::get('/agents', AgentCrud::class)->name('rekening.agents');
    Route::get('/customers', CustomerCrud::class)->name('rekening.customers');
    Route::get('/accounts', AccountCrud::class)->name('rekening.accounts');
    Route::get('/cards', CardCrud::class)->name('rekening.cards');
    Route::get('/complaints', ComplaintCrud::class)->name('rekening.complaints');
});

// Agent Portal Routes
Route::prefix('agent')->name('agent.')->group(function () {
    Route::get('/login', AgentLogin::class)->middleware('guest:agent')->name('login');
    Route::get('/dashboard', AgentDashboard::class)->middleware('auth:agent')->name('dashboard');
    Route::get('/help-desk', \App\Livewire\Agent\HelpDesk::class)->middleware('auth:agent')->name('help-desk');
});

require __DIR__.'/settings.php';
