<!DOCTYPE html>
<html>
<head>
    <title>Bulk Slip Honor Ekskul</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; font-size: 9px; }
        @page { size: 330mm 215mm; margin: 5mm; } /* F4 Landscape */
        
        .page-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: auto auto;
            grid-gap: 5px;
            padding: 0;
            align-items: start;
        }
        
        .slip-box {
            border: 1px solid #000;
            padding: 2.5px;
            overflow: hidden;
            font-size: 8px;
            box-sizing: border-box;
        }

        .header h3 { margin: 0; font-size: 9px; }
        .header h4 { margin: 0; font-size: 8px; }
        
        .info-table td, .data-table td { padding: 1px 2px; border: 1px solid #aaa; }
        .section-header { padding: 1px; font-size: 8px; background-color: #eee; border: 1px solid #aaa; }
        .footer-total { padding: 2px; font-size: 9px; border: 1px solid #aaa; border-top: 2px solid #000; }
        .data-table { margin-bottom: 2px; }
        .info-table { margin-bottom: 2px; }
        .header { margin-bottom: 2px; border-bottom: 1px solid #aaa; }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding: 1px;
            background-color: #fff;
        }
        .header h3 { margin: 1px 0; font-size: 9px; font-weight: bold; }
        .header h4 { margin: 1px 0; font-size: 8px; font-weight: normal; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
        .info-table td { padding: 2px 4px; border: 1px solid #000; font-size: 9px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
        .data-table td { border: 1px solid #000; padding: 2px 4px; font-size: 9px; }
        
        .section-header {
            background-color: #ccc;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            padding: 2px;
            font-size: 9px;
        }
        
        .footer-total {
            background-color: #ccc;
            font-weight: bold;
            font-size: 9px;
            padding: 2px;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #000;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; font-size: 14px;">Print Semua Slip</button>
    </div>

    @php
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthName = $months[$month] ?? '';
    @endphp

    <div class="page-container">
    @foreach($payrolls as $payroll)
        <div class="slip-box">
            <div class="header">
                <h3>HONOR EKSTRAKURIKULER</h3>
                <h3>{{ $unit->name ?? 'SEKOLAH' }}</h3>
                <h4>TAHUN PELAJARAN {{ $activeYear->name ?? '-' }}</h4>
            </div>

            <table class="info-table">
                <tr>
                    <td width="25%">NAMA :</td>
                    <td>{{ $payroll->teacher->name }}</td>
                </tr>
                <tr>
                    <td>JABATAN :</td>
                    <td>{{ $payroll->teacher->position ?? 'Pembina Ekskul' }}</td>
                </tr>
                <tr>
                    <td>BULAN :</td>
                    <td>{{ $monthName }} {{ $payroll->year }}</td>
                </tr>
            </table>

            <div class="section-header">HONOR EKSKUL</div>
            <table class="data-table">
                <tr>
                    <td>Bidang Ekskul</td>
                    <td colspan="2">{{ $payroll->extracurricular->name }}</td>
                </tr>
                <tr>
                    <td>Tarif per Volume</td>
                    <td width="30%"></td>
                    <td class="text-right" width="30%">Rp {{ number_format($payroll->rate, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Volume (Pertemuan)</td>
                    <td class="text-center">{{ $payroll->volume }}</td>
                    <td></td>
                </tr>
            </table>
            
            <div class="footer-total">
                <span>JUMLAH DI TERIMA</span>
                <span>Rp {{ number_format($payroll->total, 0, ',', '.') }}</span>
            </div>
        </div>
    @endforeach
    </div>
</body>
</html>
