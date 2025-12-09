<!DOCTYPE html>
<html>
<head>
    <title>Bulk Salary Slips</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; font-size: 9px; }
        @page { size: 330mm 215mm; margin: 5mm; } /* F4 Landscape */
        
        .page-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 Columns */
            grid-template-rows: auto auto; /* 2 Rows - Auto Height */
            grid-gap: 5px;
            padding: 0;
            align-items: start; /* Align slips to top */
        }
        
        .slip-box {
            border: 1px solid #000;
            padding: 2.5px;
            overflow: hidden;
            font-size: 8px; /* Slightly larger font */
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

        /* Reuse Single Slip Styles but Scaled Down */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding: 1px;
            background-color: #fff; /* White background to save ink/match simple look */
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
            font-size: 10px;
            padding: 3px;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #000;
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

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; font-size: 14px;">Print All Slips</button>
    </div>

    <div class="page-container">
    @foreach($payrolls as $payroll)
        @php
            $d = $payroll->details;
            $activeYear = $payroll->academicYear;
            
            $breakdown = $d['breakdown'] ?? [];
            $allowancesMap = $d['allowances'] ?? [];
            $deductions = $breakdown['deductions'] ?? [];

            $hours = $payroll->teaching_hours;
            $teachingPay = $breakdown['teaching'] ?? 0;
            $teachingRate = $d['teaching_rate'] ?? 0;

            $days = $payroll->attendance_days;
            $transportPay = $breakdown['transport'] ?? 0;
            $transportRate = $d['transport_rate'] ?? 0;

            $tenureYears = $d['tenure_years'] ?? 0;
            $tenurePay = $breakdown['tenure'] ?? 0;
            
            $sumA = $teachingPay + $transportPay;
            $sumB = $tenurePay + array_sum($allowancesMap);
            
            $dedInsentif = $deductions['incentive'] ?? 0;
            $dedBpjs = $deductions['bpjs'] ?? 0;
            $dedTerlambat = $deductions['transport'] ?? 0;
            $dedOther = $deductions['other'] ?? 0;
            
            $sumC = $dedInsentif + $dedBpjs + $dedTerlambat + $dedOther;
            $net = ($sumA + $sumB) - $sumC;
            
            $tunjanganList = ['Masa Kerja', 'Kepsek', 'Wakbid', 'Wakel', 'OPS', 'Bend/TU', 'Media Center', 'Piket'];
        @endphp

        <div class="slip-box">
            <div class="header">
                <h3>HONOR TENAGA PENDIDIK & KEPENDIDIKAN</h3>
                <h3>SMP GARUDA</h3>
                <h4>TAHUN PELAJARAN {{ $activeYear->name }}</h4>
            </div>

            <table class="info-table">
                <tr>
                    <td width="25%">NAMA :</td>
                    <td>{{ $payroll->teacher->name }}</td>
                </tr>
                <tr>
                    <td>JABATAN :</td>
                    <td>{{ $payroll->teacher->position }}</td>
                </tr>
                <tr>
                    <td>BULAN :</td>
                    <td>{{ date('F Y', mktime(0, 0, 0, $payroll->month, 10, $payroll->year)) }}</td>
                </tr>
            </table>

            <!-- Section A: Main Income -->
            <table class="data-table">
                <tr>
                    <td>Jml Jam</td>
                    <td class="text-center" width="10%">{{ $hours }}</td>
                    <td width="20%">Rp {{ number_format($teachingRate, 0, ',', '.') }}</td>
                    <td class="text-right" width="25%">Rp {{ number_format($teachingPay, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Transport</td>
                    <td class="text-center">{{ $days }}</td>
                    <td>Rp {{ number_format($transportRate, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($transportPay, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="font-weight:bold;">Jumlah</td>
                    <td class="text-right" style="font-weight:bold;">Rp {{ number_format($sumA, 0, ',', '.') }}</td>
                </tr>
            </table>

            <!-- Section B: Tunjangan -->
            <div class="section-header">TUNJANGAN</div>
            <table class="data-table">
                <tr>
                    <td>Masa Kerja</td>
                    <td width="30%"></td>
                    <td class="text-right" width="25%">Rp {{ number_format($tenurePay, 0, ',', '.') }}</td>
                </tr>
                @foreach($tunjanganList as $item)
                    @if($item != 'Masa Kerja')
                        <tr>
                            <td>{{ $item }}</td>
                            <td></td>
                            <td class="text-right">
                                @if(isset($allowancesMap[$item]) && $allowancesMap[$item] > 0)
                                    Rp {{ number_format($allowancesMap[$item], 0, ',', '.') }}
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
                 <tr>
                    <td colspan="2" style="font-weight:bold;">Jumlah</td>
                    <td class="text-right" style="font-weight:bold;">Rp {{ number_format($sumB, 0, ',', '.') }}</td>
                </tr>
            </table>

            <!-- Section C: Potongan -->
            <!-- Section C: Potongan -->
            <div class="section-header">POTONGAN</div>
            <table class="data-table">
                <tr>
                    <td>Insentif</td>
                    <td width="30%"></td>
                    <td class="text-right" width="25%">@if($dedInsentif > 0) Rp {{ number_format($dedInsentif, 0, ',', '.') }} @endif</td>
                </tr>
                <tr>
                    <td>BPJS</td>
                    <td></td>
                    <td class="text-right">@if($dedBpjs > 0) Rp {{ number_format($dedBpjs, 0, ',', '.') }} @endif</td>
                </tr>
                <tr>
                    <td>Terlambat</td>
                    <td></td>
                    <td class="text-right">@if($dedTerlambat > 0) Rp {{ number_format($dedTerlambat, 0, ',', '.') }} @endif</td>
                </tr>
                 <tr>
                    <td>Jml</td>
                    <td colspan="2" style="text-align:right; font-weight:bold;">Rp {{ number_format($sumC, 0, ',', '.') }}</td>
                </tr>
            </table>
            
            <div class="footer-total">
                <span>JUMLAH DI TERIMA</span>
                <span>Rp {{ number_format($net, 0, ',', '.') }}</span>
            </div>
        </div>
    @endforeach
    </div>
</body>
</html>
