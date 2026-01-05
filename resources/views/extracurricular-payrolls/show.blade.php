<!DOCTYPE html>
<html>
<head>
    <title>Slip Honor Ekskul</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; font-size: 11px; }
        .slip-box {
            border: 2px solid #000;
            width: 350px;
            margin: 0 auto;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding: 5px;
            background-color: #f9f9f9;
        }
        .header h3, .header h4 { margin: 2px 0; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .info-table td { padding: 3px 5px; border: 1px solid #000; }
        
        .section-header {
            background-color: #d3d3d3;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            padding: 3px;
        }
        
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table td { border: 1px solid #000; padding: 3px 5px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .total-row {
            background-color: #d3d3d3;
            font-weight: bold;
        }
        
        .footer-total {
            background-color: #bfbfbf;
            font-weight: bold;
            font-size: 1.1em;
            padding: 5px;
            display: flex;
            justify-content: space-between;
            border-top: 2px solid #000;
        }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .slip-box { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 5px 15px; cursor: pointer;">Print Slip</button>
    </div>

    @php
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthName = $months[$extracurricularPayroll->month] ?? '';
    @endphp

    <div class="slip-box">
        <div class="header">
            <h3>HONOR EKSTRAKURIKULER</h3>
            <h3>{{ $unit->name ?? 'SEKOLAH' }}</h3>
            <h4>TAHUN PELAJARAN {{ $activeYear->name ?? '-' }}</h4>
        </div>

        <table class="info-table">
            <tr>
                <td width="25%">NAMA :</td>
                <td>{{ $extracurricularPayroll->teacher->name }}</td>
            </tr>
            <tr>
                <td>JABATAN :</td>
                <td>{{ $extracurricularPayroll->teacher->position ?? 'Pembina Ekskul' }}</td>
            </tr>
            <tr>
                <td>BULAN :</td>
                <td>{{ $monthName }} {{ $extracurricularPayroll->year }}</td>
            </tr>
        </table>

        <!-- Section: Honor Ekskul -->
        <div class="section-header">HONOR EKSKUL</div>
        <table class="data-table">
            <tr>
                <td>Bidang Ekskul</td>
                <td colspan="2">{{ $extracurricularPayroll->extracurricular->name }}</td>
            </tr>
            <tr>
                <td>Tarif per Volume</td>
                <td width="30%"></td>
                <td class="text-right" width="30%">Rp {{ number_format($extracurricularPayroll->rate, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Volume (Pertemuan)</td>
                <td class="text-center">{{ $extracurricularPayroll->volume }}</td>
                <td></td>
            </tr>
        </table>
        
        <div class="footer-total">
            <span>JUMLAH DI TERIMA</span>
            <span>Rp {{ number_format($extracurricularPayroll->total, 0, ',', '.') }}</span>
        </div>

    </div>
</body>
</html>
