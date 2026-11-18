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
        Total: {{ $rows->count() }} rekening
        @if($status !== '')
            &middot; Status: {{ \App\Services\ExpiredAccountQuery::STATUSES[$status] }}
        @endif
        @if($search !== '')
            &middot; Pencarian: "{{ $search }}"
        @endif
        &middot; Dicetak: {{ now()->format('d M Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                @if($showType)
                    <th>Jenis</th>
                @endif
                @if($showCompany)
                    <th>Perusahaan</th>
                @endif
                <th>No. Rekening</th>
                <th>Nasabah</th>
                <th>User</th>
                <th>Agent Supplier</th>
                <th>Bank</th>
                <th>Expired On</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @if($showType)
                        <td>{{ $row->type === 'bank' ? 'Bank' : 'PT' }}</td>
                    @endif
                    @if($showCompany)
                        <td>{{ $row->company_name ?? '-' }}</td>
                    @endif
                    <td>{{ $row->account_number }}</td>
                    <td>{{ $row->customer_name ?? '-' }}</td>
                    <td>{{ $row->agent_name ?? '-' }}</td>
                    <td>{{ $row->agent_supplier_name ?? '-' }}</td>
                    <td>{{ $row->bank_name }}</td>
                    <td>{{ $row->expired_on ? \Illuminate\Support\Carbon::parse($row->expired_on)->format('d M Y') : '-' }}</td>
                    <td>{{ \App\Services\ExpiredAccountQuery::STATUSES[$row->status] ?? ucfirst((string) $row->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 7 + ($showType ? 1 : 0) + ($showCompany ? 1 : 0) }}">Tidak ada rekening yang cocok dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
