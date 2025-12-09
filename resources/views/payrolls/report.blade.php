<!DOCTYPE html>
<html>
<head>
    <title>DAFTAR PENERIMA HONOR PENDIDIK DAN TENAGA KEPENDIDIKAN</title>
    <style>
        @page { size: landscape; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 0; margin: 0; }
        .header { text-align: center; margin-bottom: 10px; font-weight: bold; }
        .header h1, .header h2, .header h3 { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; border: 2px solid #000; }
        th, td { border: 1px solid #000; padding: 4px; vertical-align: middle; }
        th { text-align: center; background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bg-gray { background-color: #eee; }
        .no-print { margin-bottom: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 5px 10px; cursor: pointer;">Print Report / Save as PDF</button>
    </div>

    @php $currentUnit = \App\Models\Unit::find(session('unit_id')); @endphp
    <div class="header">
        <div>DAFTAR PENERIMA HONOR PENDIDIK DAN TENAGA KEPENDIDIKAN</div>
        <div>{{ $currentUnit ? strtoupper($currentUnit->name) : 'SEKOLAH' }}</div>
        <div>TAHUN PELAJARAN {{ $activeYear ? $activeYear->name : 'N/A' }}</div>
    </div>

    <div style="margin-bottom: 5px;">
        <strong>Bulan : {{ date('F Y', mktime(0, 0, 0, $month, 10, $year)) }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="30">NO</th>
                <th rowspan="2">NAMA</th>
                <th rowspan="2">Jabatan</th>
                <th colspan="2">Jam Pelajaran<br>(Rp {{ number_format($activeYear->payrollSettings->teaching_rate_per_hour ?? 0, 0, ',', '.') }})</th>
                <th colspan="2">Transport<br>(Rp {{ number_format($activeYear->payrollSettings->transport_rate_per_visit ?? 0, 0, ',', '.') }})</th>
                <th colspan="2">Jml Masa Kerja<br>(Rp {{ number_format($activeYear->payrollSettings->masa_kerja_rate_per_year ?? 0, 0, ',', '.') }})</th>
                <th colspan="6">TUNJANGAN</th>
                <th rowspan="2">Jumlah</th> <!-- Gross -->
                <th colspan="4">POTONGAN</th>
                <th rowspan="2">Jumlah Di Terima</th>
                <th rowspan="2" width="50">Paraf</th>
            </tr>
            <tr>
                <!-- Jam -->
                <th>Jml Jam</th>
                <th>Jumlah</th>
                <!-- Transport -->
                <th>Jml Hadir</th>
                <th>Jumlah</th>
                <!-- Masa Kerja -->
                <th>Masa Kerja</th> <!-- Years -->
                <th>Jumlah</th>
                <!-- Tunjangan -->
                <th>Media Center</th>
                <th>Wakbid</th>
                <th>Wakel</th>
                <th>OPS</th>
                <th>Bend/TU</th>
                <th>Piket</th>
                <!-- Potongan -->
                <th>Insentif</th>
                <th>BPJS</th>
                <th>Terlambat</th>
                <th>Jml</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotalGross = 0;
                $grandTotalNet = 0;
                // Accumulators
                $sumJam = 0; $sumJamRp = 0;
                $sumTrspDays = 0; $sumTrspRp = 0;
                $sumTenureYears = 0; $sumTenureRp = 0;
                
                $sumAllowances = [
                    'Media Center' => 0, 'Wakbid' => 0, 'Wakel' => 0, 
                    'OPS' => 0, 'Bend/TU' => 0, 'Piket' => 0
                ];
                
                $sumDedIncentive = 0;
                $sumDedBpjs = 0;
                $sumDedLate = 0;
                $sumDedOther = 0;
            @endphp

            @foreach($payrolls as $index => $p)
                @php
                    $d = $p->details;
                    $breakdown = $d['breakdown'] ?? [];
                    $deductions = $breakdown['deductions'] ?? [];
                    $allowancesMap = $d['allowances'] ?? [];
                    
                    // Values
                    $hours = $p->teaching_hours;
                    $teachingPay = $breakdown['teaching'] ?? 0;
                    
                    $days = $p->attendance_days;
                    $transportPay = $breakdown['transport'] ?? 0;

                    $tenureYears = $d['tenure_years'] ?? 0;
                    $tenurePay = $breakdown['tenure'] ?? 0;

                    // Allowances Smart Mapping
                    $mediaCenter = 0; $wakbid = 0; $wakel = 0; $ops = 0; $bendTu = 0; $piket = 0;
                    
                    foreach ($allowancesMap as $name => $amount) {
                         if ($amount <= 0) continue;
                         $n = strtolower($name);
                         
                         if (str_contains($n, 'media center')) {
                             $mediaCenter += $amount;
                         } elseif (str_contains($n, 'wakbid') || str_contains($n, 'wakil') || str_contains($n, 'waka') || str_contains($n, 'kurikulum') || str_contains($n, 'kesiswaan') || str_contains($n, 'humas') || str_contains($n, 'sarpras')) {
                             $wakbid += $amount;
                         } elseif (str_contains($n, 'wakel') || str_contains($n, 'wali kelas')) {
                             $wakel += $amount;
                         } elseif (str_contains($n, 'ops') || str_contains($n, 'operator')) {
                             $ops += $amount;
                         } elseif (str_contains($n, 'bend') || str_contains($n, 'tu') || str_contains($n, 'tata usaha')) {
                             $bendTu += $amount;
                         } elseif (str_contains($n, 'piket')) {
                             $piket += $amount;
                         }
                    }
                    
                    // Gross for "Jumlah" column (Teaching + Transport + Tenure + All Allowances)
                    // Note: array_sum($allowancesMap) captures ALL allowances, even those not mapped to columns.
                    // This ensures the Total is always correct.

                    $gross = $teachingPay + $transportPay + $tenurePay + array_sum($allowancesMap);

                    // Deductions
                    $insentif = $deductions['incentive'] ?? 0;
                    $bpjs = $deductions['bpjs'] ?? 0;
                    $terlambat = $deductions['transport'] ?? 0; // mapped from transport_deduction
                    $lainnya = $deductions['other'] ?? 0;
                    
                    // Note on 'Terlambat': Image has "Terlambat" under Potongan.
                    // Also "Info" from user: "unpaid_leave_amount (Potongan Terlambat)".
                    
                    $totalDed = $insentif + $bpjs + $terlambat + $lainnya;
                    $net = $gross - $totalDed;

                    // Accumulate
                    $grandTotalGross += $gross;
                    $grandTotalNet += $net;
                    
                    $sumJam += $hours; $sumJamRp += $teachingPay;
                    $sumTrspDays += $days; $sumTrspRp += $transportPay;
                    $sumTenureYears += $tenureYears; $sumTenureRp += $tenurePay;
                    
                    $sumAllowances['Media Center'] += $mediaCenter;
                    $sumAllowances['Wakbid'] += $wakbid;
                    $sumAllowances['Wakel'] += $wakel;
                    $sumAllowances['OPS'] += $ops;
                    $sumAllowances['Bend/TU'] += $bendTu;
                    $sumAllowances['Piket'] += $piket;

                    $sumDedIncentive += $insentif;
                    $sumDedBpjs += $bpjs;
                    $sumDedLate += $terlambat;
                    $sumDedOther += $lainnya;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $p->teacher->name }}</td>
                    <td>{{ $p->teacher->position }}</td>
                    
                    <!-- Teaching -->
                    <td class="text-center">{{ $hours > 0 ? $hours : '' }}</td>
                    <td class="text-right">{{ $teachingPay > 0 ? number_format($teachingPay) : '-' }}</td>
                    
                    <!-- Transport -->
                    <td class="text-center">{{ $days > 0 ? $days : '' }}</td>
                    <td class="text-right">{{ $transportPay > 0 ? number_format($transportPay) : '-' }}</td>
                    
                    <!-- Tenure -->
                    <td class="text-center">{{ $tenureYears > 0 ? number_format($tenureYears, 0) : '' }}</td>
                    <td class="text-right">{{ $tenurePay > 0 ? number_format($tenurePay) : '' }}</td>
                    
                    <!-- Allowances -->
                    <td class="text-right">{{ $mediaCenter > 0 ? number_format($mediaCenter) : '' }}</td>
                    <td class="text-right">{{ $wakbid > 0 ? number_format($wakbid) : '' }}</td>
                    <td class="text-right">{{ $wakel > 0 ? number_format($wakel) : '' }}</td>
                    <td class="text-right">{{ $ops > 0 ? number_format($ops) : '' }}</td>
                    <td class="text-right">{{ $bendTu > 0 ? number_format($bendTu) : '' }}</td>
                    <td class="text-right">{{ $piket > 0 ? number_format($piket) : '' }}</td>
                    
                    <!-- Gross -->
                    <td class="text-right bg-gray"><strong>{{ number_format($gross) }}</strong></td>
                    
                    <!-- Deductions -->
                    <td class="text-right">{{ $insentif > 0 ? number_format($insentif) : '' }}</td>
                    <td class="text-right">{{ $bpjs > 0 ? number_format($bpjs) : '' }}</td>
                    <td class="text-right">{{ $terlambat > 0 ? number_format($terlambat) : '-' }}</td>
                    <td class="text-right">{{ $lainnya > 0 ? number_format($lainnya) : '-' }}</td>
                    
                    <!-- Net -->
                    <td class="text-right bg-gray"><strong>{{ number_format($net) }}</strong></td>
                    <td></td>
                </tr>
            @endforeach
            
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td colspan="3">Jumlah</td>
                <!-- Teaching -->
                <td class="text-center">{{ $sumJam }}</td>
                <td class="text-right">{{ number_format($sumJamRp) }}</td>
                
                <!-- Transport -->
                <td class="text-center">{{ $sumTrspDays }}</td>
                <td class="text-right">{{ number_format($sumTrspRp) }}</td>
                
                <!-- Tenure -->
                <td class="text-center">{{ $sumTenureYears }}</td>
                <td class="text-right">{{ number_format($sumTenureRp) }}</td>
                
                <!-- Allowances -->
                <td class="text-right">{{ number_format($sumAllowances['Media Center']) }}</td>
                <td class="text-right">{{ number_format($sumAllowances['Wakbid']) }}</td>
                <td class="text-right">{{ number_format($sumAllowances['Wakel']) }}</td>
                <td class="text-right">{{ number_format($sumAllowances['OPS']) }}</td>
                <td class="text-right">{{ number_format($sumAllowances['Bend/TU']) }}</td>
                <td class="text-right">{{ number_format($sumAllowances['Piket']) }}</td>
                
                <!-- Gross -->
                <td class="text-right">{{ number_format($grandTotalGross) }}</td>
                
                <!-- Deductions -->
                <td class="text-right">{{ number_format($sumDedIncentive) }}</td>
                <td class="text-right">{{ number_format($sumDedBpjs) }}</td>
                <td class="text-right">{{ number_format($sumDedLate) }}</td>
                <td class="text-right">{{ number_format($sumDedOther) }}</td>
                
                <!-- Net -->
                <td class="text-right">{{ number_format($grandTotalNet) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    <!-- Signature Section -->
    <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
        <div style="text-align: center; width: 250px;">
            <p style="margin-bottom: 5px;">{{ $currentUnit ? $currentUnit->location : 'Cirebon' }}, {{ $currentUnit && $currentUnit->signature_date ? $currentUnit->signature_date->locale('id')->translatedFormat('d F Y') : now()->locale('id')->translatedFormat('d F Y') }}</p>
            <p style="margin-bottom: 60px;">Kepala Sekolah</p>
            <p style="text-decoration: underline; font-weight: bold;">{{ $currentUnit ? $currentUnit->principal_name : '________________________' }}</p>
        </div>
    </div>
</body>
</html>
