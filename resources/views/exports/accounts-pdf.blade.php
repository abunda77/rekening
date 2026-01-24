<!DOCTYPE html>
<html>
<head>
    <title>Data Accounts</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Data Rekening</h2>
    <table>
        <thead>
            <tr>
                <th>No. Rekening</th>
                <th>Bank</th>
                <th>Cabang</th>
                <th>Nasabah</th>
                <th>NIK</th>
                <th>Status</th>
                <th>Tgl Buka</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $account)
            <tr>
                <td>{{ $account->account_number }}</td>
                <td>{{ $account->bank_name }}</td>
                <td>{{ $account->branch }}</td>
                <td>{{ $account->customer ? $account->customer->full_name : '-' }}</td>
                <td>{{ $account->customer ? $account->customer->nik : '-' }}</td>
                <td>{{ ucfirst($account->status) }}</td>
                <td>{{ $account->opening_date ? $account->opening_date->format('d M Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
