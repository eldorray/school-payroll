<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Honor Ekstrakurikuler - {{ $monthName }} {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            padding: 20mm;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .header h3 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .period {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signature-section {
            margin-top: 50px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
            width: 200px;
        }
        .signature-space {
            height: 80px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        @media print {
            body { padding: 10mm; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>HONOR EKSTRAKURIKULER</h1>
        <h2>{{ $unit->name ?? 'SEKOLAH' }}</h2>
        <h3>TAHUN PELAJARAN {{ $activeYear->name ?? '-' }}</h3>
    </div>

    <div class="period">
        <strong>Bulan : {{ $monthName }} {{ $year }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">NO</th>
                <th>NAMA</th>
                <th>BIDANG ESKUL</th>
                <th class="text-right" style="width: 100px;">RUPIAH</th>
                <th class="text-center" style="width: 70px;">VOLUME</th>
                <th class="text-right" style="width: 110px;">JUMLAH</th>
                <th style="width: 100px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrolls as $index => $payroll)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $payroll->teacher->name }}</td>
                <td>{{ $payroll->extracurricular->name }}</td>
                <td class="text-right">{{ number_format($payroll->rate, 0, ',', '.') }}</td>
                <td class="text-center">{{ $payroll->volume }}</td>
                <td class="text-right">{{ number_format($payroll->total, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
            <tr>
                <td colspan="5" class="text-center"><strong>Jumlah</strong></td>
                <td class="text-right"><strong>{{ number_format($payrolls->sum('total'), 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p><em>Kepala Sekolah</em></p>
            <div class="signature-space"></div>
            <p class="signature-name">{{ $unit->principal_name ?? '______________________' }}</p>
        </div>
    </div>
</body>
</html>
