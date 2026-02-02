<?php

use App\Livewire\Agent\Auth\Login as AgentLogin;
use App\Livewire\Agent\Dashboard as AgentDashboard;
use App\Livewire\Rekening\AccountCrud;
use App\Livewire\Rekening\AgentCrud;
use App\Livewire\Rekening\CardCrud;
use App\Livewire\Rekening\ComplaintCrud;
use App\Livewire\Rekening\CustomerCrud;
use App\Livewire\Rekening\DatabaseBackupCrud;
use App\Livewire\Rekening\PermissionCrud;
use App\Livewire\Rekening\RoleCrud;
use App\Livewire\Rekening\ShipmentCrud;
use App\Livewire\Rekening\UserCrud;
use App\Models\DatabaseBackup;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', \App\Http\Controllers\DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rekening Management Routes
Route::middleware(['auth', 'verified'])->prefix('rekening')->group(function () {
    Route::get('/agents', AgentCrud::class)->name('rekening.agents');
    Route::get('/customers', CustomerCrud::class)->name('rekening.customers');
    Route::get('/accounts', AccountCrud::class)->name('rekening.accounts');
    Route::get('/cards', CardCrud::class)->name('rekening.cards');
    Route::get('/complaints', ComplaintCrud::class)->name('rekening.complaints');
    Route::get('/shipments', ShipmentCrud::class)->name('rekening.shipments');
    Route::get('/roles', RoleCrud::class)->name('rekening.roles')->middleware(['role:Super Admin']);
    Route::get('/permissions', PermissionCrud::class)->name('rekening.permissions')->middleware(['role:Super Admin']);
    Route::get('/users', UserCrud::class)->name('rekening.users')->middleware(['role:Super Admin']);
    Route::get('/backups', DatabaseBackupCrud::class)->name('rekening.backups')->middleware(['role:Super Admin']);

    // Backup download route
    Route::get('/backups/{backup}/download', function (DatabaseBackup $backup) {
        if (! $backup->file_exists) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return response()->download($backup->file_path, $backup->filename);
    })->name('rekening.backups.download')->middleware(['role:Super Admin']);
});

// Agent Portal Routes
Route::prefix('agent')->name('agent.')->group(function () {
    Route::get('/login', AgentLogin::class)->middleware('guest:agent')->name('login');
    Route::get('/dashboard', AgentDashboard::class)->middleware('auth:agent')->name('dashboard');
    Route::get('/help-desk', \App\Livewire\Agent\HelpDesk::class)->middleware('auth:agent')->name('help-desk');
    Route::get('/shipment', \App\Livewire\Agent\Shipment::class)->middleware('auth:agent')->name('shipment');
});

require __DIR__.'/settings.php';
