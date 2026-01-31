<?php

namespace App\Console\Commands;

use App\Models\AgentNotification;
use Illuminate\Console\Command;

class CleanupNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup 
                            {--months= : Number of months to retain notifications (overrides config)}
                            {--dry-run : Preview what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old agent notifications based on retention policy';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $months = $this->option('months') ?? config('notifications.retention_months', 3);
        $dryRun = $this->option('dry-run');

        if ($months <= 0) {
            $this->warn('Retention period is set to 0 or less. Cleanup is disabled.');

            return self::SUCCESS;
        }

        $cutoffDate = now()->subMonths((int) $months);

        $query = AgentNotification::where('created_at', '<', $cutoffDate);
        $count = $query->count();

        if ($count === 0) {
            $this->info('No notifications older than '.$months.' months found.');

            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info("[DRY RUN] Would delete {$count} notifications older than {$months} months.");

            return self::SUCCESS;
        }

        $this->info("Deleting {$count} notifications older than {$months} months...");

        $deleted = $query->delete();

        $this->info("Successfully deleted {$deleted} old notifications.");

        return self::SUCCESS;
    }
}
