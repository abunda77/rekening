<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { margin-bottom: 4px; }
        .meta { margin-bottom: 16px; color: #555; }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <p class="meta">
        Total: {{ $accounts->count() }} rekening &middot; Dicetak: {{ now()->format('d M Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                @if($showCompany)
                    <th>Perusahaan</th>
                @endif
                <th>No. Rekening</th>
                <th>Nasabah</th>
                <th>Agent</th>
                <th>Bank</th>
                <th>Expired On</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $account)
                <tr>
                    @if($showCompany)
                        <td>{{ $account->company_name }}</td>
                    @endif
                    <td>{{ $account->account_number }}</td>
                    <td>{{ $account->customer?->full_name ?? '-' }}</td>
                    <td>{{ $account->agent?->agent_name ?? '-' }}</td>
                    <td>{{ $account->bank_name }}</td>
                    <td>{{ $account->expired_on?->format('d M Y') ?? '-' }}</td>
                    <td>{{ ['aktif' => 'Aktif', 'bermasalah' => 'Bermasalah', 'nonaktif' => 'Non-Aktif'][$account->status] ?? ucfirst((string) $account->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showCompany ? 7 : 6 }}">Belum ada data rekening yang expired pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
