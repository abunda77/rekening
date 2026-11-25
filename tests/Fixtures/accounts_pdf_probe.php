<?php

/**
 * Renders the accounts PDF view with synthetic rows so the test suite can verify that
 * large exports stay inside a sane memory/time budget. Run in a separate PHP process
 * with a hard memory limit; a fatal error means the view regressed.
 *
 * Usage: php accounts_pdf_probe.php <rows>
 */
use App\Models\Account;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$count = max(1, (int) ($argv[1] ?? 100));

$customer = new Customer([
    'full_name' => 'Nama Nasabah Contoh Panjang',
    'nik' => '3273010101010001',
]);

$accounts = collect();

for ($i = 0; $i < $count; $i++) {
    $account = new Account([
        'account_number' => '1234567890'.$i,
        'bank_name' => 'Bank Contoh Indonesia',
        'branch' => 'Cabang Jakarta Pusat',
        'status' => 'active',
        'opening_date' => '2024-01-15',
        'expired_on' => '2026-01-15',
    ]);

    $account->setRelation('customer', $customer);

    $accounts->push($account);
}

$started = microtime(true);

$pdf = Pdf::loadView('exports.list-pdf', [
    'title' => 'Data Rekening',
    'headers' => [
        'No. Rekening', 'Bank', 'Cabang', 'Nasabah', 'NIK', 'Status', 'Tgl Buka', 'Tgl Berakhir',
    ],
    'rows' => $accounts->map(fn (Account $account) => [
        $account->account_number,
        $account->bank_name,
        $account->branch,
        $account->customer->full_name,
        $account->customer->nik,
        ucfirst((string) $account->status),
        $account->opening_date->format('d M Y'),
        $account->expired_on->format('d M Y'),
    ])->all(),
    'meta' => ['Total: '.$count.' rekening'],
]);

$pdf->setPaper('a4', 'landscape');

$output = $pdf->output();

printf(
    "rows=%d bytes=%d sec=%.2f peak_mb=%.1f\n",
    $count,
    strlen($output),
    microtime(true) - $started,
    memory_get_peak_usage(true) / 1048576,
);
