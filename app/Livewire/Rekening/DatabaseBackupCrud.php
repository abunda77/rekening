<?php

namespace App\Livewire\Rekening;

use App\Models\DatabaseBackup;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\Process\Process;

#[Layout('layouts.app.sidebar')]
#[Title('Backup Database')]
class DatabaseBackupCrud extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    // Delete modal
    public bool $showDeleteModal = false;

    public ?int $deleteId = null;

    // Bulk Delete
    public array $selected = [];

    public bool $selectAll = false;

    public bool $showBulkDeleteModal = false;

    // Create backup loading state
    public bool $isCreatingBackup = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function createBackup(): void
    {
        $this->isCreatingBackup = true;
        $filename = null;

        try {
            // Ensure backup directory exists
            $backupPath = storage_path('app/backups');
            if (! is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            // Generate filename
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "backup_{$timestamp}.sql";
            $filePath = $backupPath.'/'.$filename;

            // Get database configuration
            $dbConnection = config('database.default');
            $dbConfig = config("database.connections.{$dbConnection}");

            // Check if using MySQL/MariaDB
            if (! in_array($dbConfig['driver'] ?? '', ['mysql', 'mariadb'])) {
                throw new \Exception('Backup hanya mendukung database MySQL/MariaDB.');
            }

            // Get host (support both 'host' and 'hostname' keys)
            $host = $dbConfig['host'] ?? $dbConfig['hostname'] ?? '127.0.0.1';
            $port = $dbConfig['port'] ?? '3306';
            $username = $dbConfig['username'] ?? $dbConfig['user'] ?? 'root';
            $password = $dbConfig['password'] ?? $dbConfig['pass'] ?? '';
            $database = $dbConfig['database'] ?? $dbConfig['dbname'] ?? '';

            if (empty($database)) {
                throw new \Exception('Nama database tidak ditemukan dalam konfigurasi.');
            }

            // Create backup using mysqldump
            $command = [
                'mysqldump',
                '-h', $host,
                '-P', $port,
                '-u', $username,
            ];

            // Add password if exists
            if (! empty($password)) {
                $command[] = '-p'.$password;
            }

            $command[] = $database;

            $process = new Process($command);
            $process->setTimeout(300);
            $process->run();

            if ($process->isSuccessful()) {
                // Save backup content to file
                file_put_contents($filePath, $process->getOutput());

                // Get file size
                $size = filesize($filePath);

                // Create database record
                DatabaseBackup::create([
                    'filename' => $filename,
                    'type' => 'manual',
                    'status' => 'success',
                    'size' => $size,
                ]);

                session()->flash('success', 'Backup database berhasil dibuat.');
            } else {
                // Create failed backup record
                DatabaseBackup::create([
                    'filename' => $filename,
                    'type' => 'manual',
                    'status' => 'failed',
                    'size' => 0,
                ]);

                session()->flash('error', 'Gagal membuat backup: '.$process->getErrorOutput());
            }
        } catch (\Exception $e) {
            // Create failed backup record
            DatabaseBackup::create([
                'filename' => $filename ?? 'backup_'.now()->format('Y-m-d_H-i-s').'.sql',
                'type' => 'manual',
                'status' => 'failed',
                'size' => 0,
            ]);

            session()->flash('error', 'Terjadi kesalahan: '.$e->getMessage());
        }

        $this->isCreatingBackup = false;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            $backup = DatabaseBackup::findOrFail($this->deleteId);

            // Delete file if exists
            if ($backup->file_exists) {
                unlink($backup->file_path);
            }

            $backup->delete();
            session()->flash('success', 'Backup berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selected = $this->getRowsQuery()->paginate($this->perPage)->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected(): void
    {
        $this->selectAll = false;
    }

    public function confirmBulkDelete(): void
    {
        if (! empty($this->selected)) {
            $this->showBulkDeleteModal = true;
        }
    }

    public function bulkDelete(): void
    {
        $backups = DatabaseBackup::whereIn('id', $this->selected)->get();

        foreach ($backups as $backup) {
            // Delete file if exists
            if ($backup->file_exists) {
                unlink($backup->file_path);
            }

            $backup->delete();
        }

        session()->flash('success', $backups->count().' backup berhasil dihapus.');

        $this->selected = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;
    }

    public function cancelBulkDelete(): void
    {
        $this->showBulkDeleteModal = false;
    }

    public function getRowsQuery()
    {
        return DatabaseBackup::query()
            ->when($this->search, function ($query) {
                $query->where('filename', 'like', '%'.$this->search.'%')
                    ->orWhere('type', 'like', '%'.$this->search.'%')
                    ->orWhere('status', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $backups = $this->getRowsQuery()->paginate($this->perPage);

        return view('livewire.rekening.database-backup-crud', [
            'backups' => $backups,
        ]);
    }
}
