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
            grid-template-rows: repeat(2, 1fr); /* 2 Rows */
            grid-gap: 8mm;
            padding: 0;
            align-items: stretch;
            height: 205mm; /* F4 height minus margins */
            page-break-after: always;
        }
        
        .page-container:last-child {
            page-break-after: avoid;
        }
        
        .slip-box {
            border: 1px solid #000;
            padding: 2px;
            overflow: hidden;
            font-size: 8px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        .header h3 { margin: 0; font-size: 9px; }
        .header h4 { margin: 0; font-size: 8px; }
        
        .info-table td, .data-table td { padding: 1px 2px; border: 1px solid #aaa; }
        .section-header { padding: 1px; font-size: 8px; background-color: #eee; border: 1px solid #aaa; }
        .footer-total { padding: 2px; font-size: 9px; border: 1px solid #aaa; border-top: 2px solid #000; }
        .data-table { margin-bottom: 2px; }
        .info-table { margin-bottom: 2px; }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding: 1px;
            background-color: #fff;
        }
        .header h3 { margin: 1px 0; font-size: 9px; font-weight: bold; }
        .header h4 { margin: 1px 0; font-size: 8px; font-weight: normal; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        .info-table td { padding: 1px 3px; border: 1px solid #000; font-size: 8px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        .data-table td { border: 1px solid #000; padding: 1px 3px; font-size: 8px; }
        
        .section-header {
            background-color: #ccc;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            padding: 1px;
            font-size: 8px;
        }
        
        .footer-total {
            background-color: #ccc;
            font-weight: bold;
            font-size: 9px;
            padding: 2px;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #000;
            margin-top: auto;
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

    @php $slipCount = 0; @endphp
    @foreach($payrolls as $payroll)
        @if($slipCount % 8 == 0)
            @if($slipCount > 0)</div>@endif
            <div class="page-container">
        @endif
        @php $slipCount++; @endphp
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
            $dedTerlambat = $deductions['late'] ?? $deductions['transport'] ?? 0;
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
                    <td class="text-right" width="25%">@if($tenurePay > 0) Rp {{ number_format($tenurePay, 0, ',', '.') }} @endif</td>
                </tr>
                @php
                    $tunjanganList = ['Kepala Madrasah', 'Kurikulum', 'Kesiswaan', 'Wali Kelas', 'Operator', 'Tata Usaha', 'Media Center', 'Perpustakaan', 'Piket', 'Penjaga Sekolah'];
                @endphp
                @foreach($tunjanganList as $item)
                    <tr>
                        <td>{{ $item }}</td>
                        <td></td>
                        <td class="text-right">
                            @if(isset($allowancesMap[$item]) && $allowancesMap[$item] > 0)
                                Rp {{ number_format($allowancesMap[$item], 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                @foreach($allowancesMap as $name => $amount)
                    @if(!in_array($name, $tunjanganList) && $amount > 0)
                        <tr>
                            <td>{{ $name }}</td>
                            <td></td>
                            <td class="text-right">Rp {{ number_format($amount, 0, ',', '.') }}</td>
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
