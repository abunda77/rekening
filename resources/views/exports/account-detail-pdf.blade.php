<!DOCTYPE html>
<html>
<head>
    <title>Account Detail - {{ $account->account_number }}</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #0284c7; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #666; }
        
        .section { margin-bottom: 25px; }
        .section-title { font-size: 16px; font-weight: bold; border-bottom: 1px solid #eee; margin-bottom: 15px; padding-bottom: 5px; color: #0284c7; }
        
        .grid { display: table; width: 100%; margin-bottom: 20px; }
        .row { display: table-row; }
        .label { display: table-cell; width: 140px; font-weight: bold; color: #555; padding: 5px 0; vertical-align: top; }
        .value { display: table-cell; padding: 5px 0; border-bottom: 1px dotted #eee; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detail Rekening</h1>
        <p>Generated on {{ date('d M Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Informasi Rekening</div>
        <div class="grid">
            <div class="row">
                <div class="label">No. Rekening</div>
                <div class="value">{{ $account->account_number }}</div>
            </div>
            <div class="row">
                <div class="label">Bank</div>
                <div class="value">{{ $account->bank_name }}</div>
            </div>
            <div class="row">
                <div class="label">Cabang</div>
                <div class="value">{{ $account->branch ?? '-' }}</div>
            </div>
            <div class="row">
                <div class="label">Tanggal Buka</div>
                <div class="value">{{ $account->opening_date ? $account->opening_date->format('d M Y') : '-' }}</div>
            </div>
            <div class="row">
                <div class="label">Tanggal Berakhir</div>
                <div class="value">{{ $account->expired_on ? $account->expired_on->format('d M Y') : '-' }}</div>
            </div>
            <div class="row">
                <div class="label">Status</div>
                <div class="value">{{ ucfirst($account->status) }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Informasi Nasabah</div>
        <div class="grid">
            <div class="row">
                <div class="label">Nama Lengkap</div>
                <div class="value">{{ $account->customer ? $account->customer->full_name : '-' }}</div>
            </div>
            <div class="row">
                <div class="label">NIK</div>
                <div class="value">{{ $account->customer ? $account->customer->nik : '-' }}</div>
            </div>
        </div>
    </div>
    
    @if($account->mobile_banking)
    <div class="section">
        <div class="section-title">Mobile Banking</div>
        <p style="white-space: pre-wrap;">{{ $account->mobile_banking }}</p>
    </div>
    @endif

    @if($account->note)
    <div class="section">
        <div class="section-title">Catatan</div>
        <p>{{ $account->note }}</p>
    </div>
    @endif

    <div class="footer">
        Dicetak oleh Sistem Rekening
    </div>
</body>
</html>
