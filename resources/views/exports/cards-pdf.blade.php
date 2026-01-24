<!DOCTYPE html>
<html>
<head>
    <title>Data Cards</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Data Kartu ATM</h2>
    <table>
        <thead>
            <tr>
                <th>No. Kartu</th>
                <th>Tipe</th>
                <th>Bank</th>
                <th>No. Rekening</th>
                <th>Nasabah</th>
                <th>Expired</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cards as $card)
            <tr>
                <td>{{ $card->card_number }}</td>
                <td>{{ $card->card_type }}</td>
                <td>{{ $card->account ? $card->account->bank_name : '-' }}</td>
                <td>{{ $card->account ? $card->account->account_number : '-' }}</td>
                <td>{{ $card->account && $card->account->customer ? $card->account->customer->full_name : '-' }}</td>
                <td>{{ $card->expiry_date ? $card->expiry_date->format('m/Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
