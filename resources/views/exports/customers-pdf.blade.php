<!DOCTYPE html>
<html>
<head>
    <title>Data Customers</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Data Customers</h2>
    <table>
        <thead>
            <tr>
                <th>NIK</th>
                <th>Nama Lengkap</th>
                <th>Ibu Kandung</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Wilayah</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->nik }}</td>
                <td>{{ $customer->full_name }}</td>
                <td>{{ $customer->mother_name }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phone_number }}</td>
                <td>
                    {{ $customer->village }}, {{ $customer->district }}<br>
                    {{ $customer->regency }}, {{ $customer->province }}
                </td>
                <td>{{ $customer->address }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
