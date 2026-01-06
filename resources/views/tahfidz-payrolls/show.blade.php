<!DOCTYPE html>
<html>
<head>
    <title>Salary Slip</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; font-size: 11px; }
        .slip-box {
            border: 2px solid #000;
            width: 350px; /* Fixed width similar to image */
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
        $d = $payroll->details;
        $activeYear = $payroll->academicYear;
        
        $breakdown = $d['breakdown'] ?? [];
        $allowancesMap = $d['allowances'] ?? [];
        $deductions = $breakdown['deductions'] ?? [];

        // Values
        $hours = $payroll->teaching_hours;
        $teachingPay = $breakdown['teaching'] ?? 0;
        $teachingRate = $d['teaching_rate'] ?? 0;

        $days = $payroll->attendance_days;
        $transportPay = $breakdown['transport'] ?? 0;
        $transportRate = $d['transport_rate'] ?? 0;

        $tenureYears = $d['tenure_years'] ?? 0;
        $tenurePay = $breakdown['tenure'] ?? 0;
        
        // Sums
        $sumA = $teachingPay + $transportPay;
        $sumB = $tenurePay + array_sum($allowancesMap); // Tunjangan
        
        $dedInsentif = $deductions['incentive'] ?? 0;
        $dedBpjs = $deductions['bpjs'] ?? 0;
        $dedTerlambat = $deductions['late'] ?? $deductions['transport'] ?? 0;
        $dedOther = $deductions['other'] ?? 0;
        
        $sumC = $dedInsentif + $dedBpjs + $dedTerlambat + $dedOther; // Potongan
        
        $net = ($sumA + $sumB) - $sumC;
        
        // Allowed Tunjangan Lists (Fixed rows as per image)
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
            <!-- Row for Masa Kerja -->
            <tr>
                <td>Masa Kerja</td>
                <td width="30%"></td>
                <td class="text-right" width="25%">@if($tenurePay > 0) Rp {{ number_format($tenurePay, 0, ',', '.') }} @endif</td>
            </tr>
            <!-- Loop fixed list -->
            @php
                $tunjanganList = ['Kepala Madrasah', 'Kurikulum', 'Kesiswaan', 'Wali Kelas', 'Operator', 'Tata Usaha', 'Media Center', 'Perpustakaan', 'Piket', 'Penjaga Sekolah'];
                $displayedKeys = [];
            @endphp
            @foreach($tunjanganList as $item)
                <tr>
                    <td>{{ $item }}</td>
                    <td></td>
                    <td class="text-right">
                        @if(isset($allowancesMap[$item]) && $allowancesMap[$item] > 0)
                            Rp {{ number_format($allowancesMap[$item], 0, ',', '.') }}
                            @php $displayedKeys[] = $item; @endphp
                        @endif
                    </td>
                </tr>
            @endforeach
            <!-- Show extra allowances not in fixed list -->
            @foreach($allowancesMap as $name => $amount)
                @if(!in_array($name, $tunjanganList) && $amount > 0)
                    <tr>
                        <td>{{ $name }}</td>
                        <td></td>
                        <td class="text-right">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
            <!-- Sum B -->
             <tr>
                <td colspan="2" style="font-weight:bold;">Jumlah</td>
                <td class="text-right" style="font-weight:bold;">Rp {{ number_format($sumB, 0, ',', '.') }}</td>
            </tr>
        </table>

         <!-- Section C: Potongan -->
        <div class="section-header">POTONGAN</div>
        <table class="data-table">
            <tr>
                <td>Insentif</td>
                <td width="30%"></td>
                <td class="text-right" width="25%">
                    @if($dedInsentif > 0) Rp {{ number_format($dedInsentif, 0, ',', '.') }} @endif
                </td>
            </tr>
            <tr>
                <td>BPJS</td>
                <td></td>
                <td class="text-right">
                    @if($dedBpjs > 0) Rp {{ number_format($dedBpjs, 0, ',', '.') }} @endif
                </td>
            </tr>
            <tr>
                <td>Terlambat</td>
                <td></td>
                <td class="text-right">
                    @if($dedTerlambat > 0) Rp {{ number_format($dedTerlambat, 0, ',', '.') }} @endif
                </td>
            </tr>
            <tr>
                <td>Jml</td> <!-- Wait, Jml means Other? Or Total? In image "Jml" is the row for Total Deductions? -->
                <!-- Image 2 Column 4 Row 4 "Jml": Rp 11,000 ?? -->
                <!-- No, Image 2 Slip 1: Potongan -> Insentif, BPJS(11k), Terlambat(65k), Jml(76k). -->
                <!-- So "Jml" IS THE TOTAL DEDUCTIONS ROW! -->
                <!-- My variable $dedOther might still be needed if there are other deductions not listed. -->
                <!-- Let's map "Other" to "Lainnya" if needed, but for now I will output Total here -->
                <td colspan="2" style="text-align:right; font-weight:bold;">Rp {{ number_format($sumC, 0, ',', '.') }}</td>
                 <!-- Actually the image 2 logic:
                 Row "Jml" has value 76,000.
                 And below that is "JUMLAH DI TERIMA" -> 1,352,000.
                 So the "Jml" row in Potongan IS the sum of deductions.
                 -->
            </tr>
        </table>
        
        <div class="footer-total">
            <span>JUMLAH DI TERIMA</span>
            <span>Rp {{ number_format($net, 0, ',', '.') }}</span>
        </div>

    </div>
</body>
</html>
