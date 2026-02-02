<?php

use App\Livewire\Rekening\DatabaseBackupCrud;
use App\Models\DatabaseBackup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles and permissions
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    // Create and authenticate user with Super Admin role
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');

    // Create backup directory
    $backupPath = storage_path('app/backups');
    if (! is_dir($backupPath)) {
        mkdir($backupPath, 0755, true);
    }
});

afterEach(function () {
    // Clean up backup files
    $backupPath = storage_path('app/backups');
    if (is_dir($backupPath)) {
        $files = glob($backupPath.'/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
});

it('renders successfully for Super Admin', function () {
    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->assertSuccessful();
});

it('denies access to unauthorized users', function () {
    $unauthorizedUser = User::factory()->create();
    $unauthorizedUser->assignRole('User');

    $this->actingAs($unauthorizedUser)
        ->get(route('rekening.backups'))
        ->assertForbidden();
});

it('displays backups in the list', function () {
    $backup = DatabaseBackup::factory()->create([
        'filename' => 'backup_2026-01-01_00-00-00.sql',
        'type' => 'manual',
        'status' => 'success',
    ]);

    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->assertSee('backup_2026-01-01_00-00-00.sql')
        ->assertSee('Manual')
        ->assertSee('Sukses');
});

it('can delete a backup', function () {
    $backup = DatabaseBackup::factory()->create([
        'filename' => 'test_backup.sql',
        'status' => 'success',
    ]);

    // Create dummy file
    $filePath = storage_path('app/backups/test_backup.sql');
    file_put_contents($filePath, 'dummy content');

    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->call('confirmDelete', $backup->id)
        ->call('delete');

    expect(DatabaseBackup::find($backup->id))->toBeNull();
    expect(file_exists($filePath))->toBeFalse();
});

it('can search backups', function () {
    DatabaseBackup::factory()->create([
        'filename' => 'backup_manual_2026.sql',
        'type' => 'manual',
    ]);
    DatabaseBackup::factory()->create([
        'filename' => 'backup_scheduled_2026.sql',
        'type' => 'scheduled',
    ]);

    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->set('search', 'manual')
        ->assertSee('backup_manual_2026.sql')
        ->assertDontSee('backup_scheduled_2026.sql');
});

it('can sort backups by filename', function () {
    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->call('sortBy', 'filename')
        ->assertSet('sortField', 'filename')
        ->assertSet('sortDirection', 'asc');
});

it('can sort backups by created_at', function () {
    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->call('sortBy', 'created_at')
        ->assertSet('sortField', 'created_at')
        ->assertSet('sortDirection', 'asc');
});

it('can bulk delete backups', function () {
    $backup1 = DatabaseBackup::factory()->create(['filename' => 'backup1.sql']);
    $backup2 = DatabaseBackup::factory()->create(['filename' => 'backup2.sql']);

    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->set('selected', [$backup1->id, $backup2->id])
        ->call('confirmBulkDelete')
        ->call('bulkDelete');

    expect(DatabaseBackup::find($backup1->id))->toBeNull();
    expect(DatabaseBackup::find($backup2->id))->toBeNull();
});

it('displays human readable file size', function () {
    DatabaseBackup::factory()->create([
        'filename' => 'test_backup.sql',
        'size' => 1048576, // 1 MB
    ]);

    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->assertSee('1.00 MB');
});

it('shows empty state when no backups exist', function () {
    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->assertSee('Tidak ada data backup');
});

it('can filter backups by search term', function () {
    DatabaseBackup::factory()->create([
        'filename' => 'january_backup.sql',
        'type' => 'manual',
    ]);
    DatabaseBackup::factory()->create([
        'filename' => 'february_backup.sql',
        'type' => 'scheduled',
    ]);

    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->set('search', 'january')
        ->assertSee('january_backup.sql')
        ->assertDontSee('february_backup.sql');
});

it('displays backup status badges correctly', function () {
    DatabaseBackup::factory()->create([
        'filename' => 'success_backup.sql',
        'status' => 'success',
    ]);
    DatabaseBackup::factory()->create([
        'filename' => 'failed_backup.sql',
        'status' => 'failed',
    ]);

    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->assertSee('Sukses')
        ->assertSee('Gagal');
});

it('displays backup type badges correctly', function () {
    DatabaseBackup::factory()->create([
        'filename' => 'manual_backup.sql',
        'type' => 'manual',
    ]);
    DatabaseBackup::factory()->create([
        'filename' => 'scheduled_backup.sql',
        'type' => 'scheduled',
    ]);

    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->assertSee('Manual')
        ->assertSee('Terjadwal');
});

it('can cancel delete operation', function () {
    $backup = DatabaseBackup::factory()->create();

    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->call('confirmDelete', $backup->id)
        ->call('cancelDelete')
        ->assertSet('showDeleteModal', false)
        ->assertSet('deleteId', null);

    expect(DatabaseBackup::find($backup->id))->not->toBeNull();
});

it('can cancel bulk delete operation', function () {
    Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->set('selected', [1, 2, 3])
        ->call('confirmBulkDelete')
        ->call('cancelBulkDelete')
        ->assertSet('showBulkDeleteModal', false);
});

it('toggles select all checkbox', function () {
    $backups = DatabaseBackup::factory()->count(3)->create();

    $component = Livewire::actingAs($this->user)
        ->test(DatabaseBackupCrud::class)
        ->set('selectAll', true);

    expect($component->get('selected'))->toHaveCount(3);
});

it('allows access to users with Super Admin role via route', function () {
    $this->actingAs($this->user)
        ->get(route('rekening.backups'))
        ->assertSuccessful();
});
