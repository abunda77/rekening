<!DOCTYPE html>
<html>
<head>
    <title>Customer Detail - {{ $customer->full_name }}</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #059669; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #666; }
        
        .section { margin-bottom: 25px; }
        .section-title { font-size: 16px; font-weight: bold; border-bottom: 1px solid #eee; margin-bottom: 15px; padding-bottom: 5px; color: #059669; }
        
        .grid { display: table; width: 100%; margin-bottom: 20px; }
        .row { display: table-row; }
        .label { display: table-cell; width: 140px; font-weight: bold; color: #555; padding: 5px 0; vertical-align: top; }
        .value { display: table-cell; padding: 5px 0; border-bottom: 1px dotted #eee; }
        
        .ktp-image { text-align: center; margin-top: 20px; }
        .ktp-image img { max-width: 400px; max-height: 250px; border: 1px solid #ccc; padding: 5px; border-radius: 5px; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Customer</h1>
        <p>Generated on {{ date('d M Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Informasi Pribadi</div>
        <div class="grid">
            <div class="row">
                <div class="label">NIK</div>
                <div class="value">{{ $customer->nik }}</div>
            </div>
            <div class="row">
                <div class="label">Nama Lengkap</div>
                <div class="value">{{ $customer->full_name }}</div>
            </div>
            <div class="row">
                <div class="label">Email</div>
                <div class="value">{{ $customer->email ?? '-' }}</div>
            </div>
            <div class="row">
                <div class="label">No. Telepon</div>
                <div class="value">{{ $customer->phone_number ?? '-' }}</div>
            </div>
            <div class="row">
                <div class="label">Nama Ibu Kandung</div>
                <div class="value">{{ $customer->mother_name ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Alamat & Lokasi</div>
        <div class="grid">
            <div class="row">
                <div class="label">Provinsi</div>
                <div class="value">{{ $customer->province ?? '-' }}</div>
            </div>
            <div class="row">
                <div class="label">Kabupaten/Kota</div>
                <div class="value">{{ $customer->regency ?? '-' }}</div>
            </div>
            <div class="row">
                <div class="label">Kecamatan</div>
                <div class="value">{{ $customer->district ?? '-' }}</div>
            </div>
            <div class="row">
                <div class="label">Kelurahan/Desa</div>
                <div class="value">{{ $customer->village ?? '-' }}</div>
            </div>
            <div class="row">
                <div class="label">Alamat Lengkap</div>
                <div class="value">{{ $customer->address ?? '-' }}</div>
            </div>
        </div>
    </div>

    @if($customer->note)
    <div class="section">
        <div class="section-title">Catatan</div>
        <p>{{ $customer->note }}</p>
    </div>
    @endif

    @if($customer->upload_ktp)
    <div class="section">
        <div class="section-title">Scan KTP</div>
        <div class="ktp-image">
            <!-- Checking if file exists in public path to ensure DomPDF can find it -->
            @php
                $path = public_path('storage/' . $customer->upload_ktp);
            @endphp
            @if(file_exists($path))
                <img src="{{ $path }}">
            @else
               <p style="color: red; font-size: 12px;">(Image file not found at path: {{ $path }})</p>
            @endif
        </div>
    </div>
    @endif

    <div class="footer">
        Dicetak oleh Sistem Rekening
    </div>
</body>
</html>
