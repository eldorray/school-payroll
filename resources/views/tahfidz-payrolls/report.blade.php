<!DOCTYPE html>
<html>

<head>
    <title>DAFTAR PENERIMA HONOR GURU TAHFIDZ</title>
    <style>
        @page {
            size: landscape;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 0;
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .header h1,
        .header h2,
        .header h3 {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }

        th {
            text-align: center;
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bg-gray {
            background-color: #eee;
        }

        .no-print {
            margin-bottom: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 5px 10px; cursor: pointer;">Print Report / Save as PDF</button>
    </div>

    @php
        $currentUnit = \App\Models\Unit::find(session('unit_id'));
        $settings = $activeYear ? $activeYear->getSettingsForUnit(session('unit_id')) : null;

        // Collect all unique allowance names from payrolls for dynamic columns
        $allAllowanceNames = [];
        foreach ($payrolls as $p) {
            $allowancesMap = $p->details['allowances'] ?? [];
            foreach ($allowancesMap as $name => $amount) {
                if ($amount > 0 && !in_array($name, $allAllowanceNames)) {
                    $allAllowanceNames[] = $name;
                }
            }
        }
        sort($allAllowanceNames);
        $tunjanganCount = count($allAllowanceNames);
    @endphp

    <div class="header">
        <div>DAFTAR PENERIMA HONOR GURU TAHFIDZ</div>
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
                <th colspan="2">Jam Pelajaran<br>(Rp
                    {{ number_format($settings->teaching_rate_per_hour ?? 0, 0, ',', '.') }})</th>
                <th colspan="2">Transport<br>(Rp
                    {{ number_format($settings->transport_rate_per_visit ?? 0, 0, ',', '.') }})</th>
                <th colspan="2">Jml Masa Kerja<br>(Rp
                    {{ number_format($settings->masa_kerja_rate_per_year ?? 0, 0, ',', '.') }})</th>
                @if ($tunjanganCount > 0)
                    <th colspan="{{ $tunjanganCount }}">TUNJANGAN</th>
                @endif
                <th rowspan="2">Jumlah</th>
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
                <th>Masa Kerja</th>
                <th>Jumlah</th>
                <!-- Tunjangan - Dynamic -->
                @foreach ($allAllowanceNames as $tunjanganName)
                    <th>{{ $tunjanganName }}</th>
                @endforeach
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
                $sumJam = 0;
                $sumJamRp = 0;
                $sumTrspDays = 0;
                $sumTrspRp = 0;
                $sumTenureYears = 0;
                $sumTenureRp = 0;

                // Dynamic allowance sums
                $sumAllowances = [];
                foreach ($allAllowanceNames as $name) {
                    $sumAllowances[$name] = 0;
                }

                $sumDedIncentive = 0;
                $sumDedBpjs = 0;
                $sumDedLate = 0;
                $sumDedOther = 0;
            @endphp

            @foreach ($payrolls as $index => $p)
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

                    // Gross
                    $gross = $teachingPay + $transportPay + $tenurePay + array_sum($allowancesMap);

                    // Deductions
                    $insentif = $deductions['incentive'] ?? 0;
                    $bpjs = $deductions['bpjs'] ?? 0;
                    $terlambat = $deductions['late'] ?? ($deductions['transport'] ?? 0);
                    $lainnya = $deductions['other'] ?? 0;

                    $totalDed = $insentif + $bpjs + $terlambat + $lainnya;
                    $net = $gross - $totalDed;

                    // Accumulate
                    $grandTotalGross += $gross;
                    $grandTotalNet += $net;

                    $sumJam += $hours;
                    $sumJamRp += $teachingPay;
                    $sumTrspDays += $days;
                    $sumTrspRp += $transportPay;
                    $sumTenureYears += $tenureYears;
                    $sumTenureRp += $tenurePay;

                    foreach ($allAllowanceNames as $name) {
                        $sumAllowances[$name] += $allowancesMap[$name] ?? 0;
                    }

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

                    <!-- Allowances - Dynamic -->
                    @foreach ($allAllowanceNames as $name)
                        <td class="text-right">
                            {{ ($allowancesMap[$name] ?? 0) > 0 ? number_format($allowancesMap[$name]) : '' }}</td>
                    @endforeach

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

                <!-- Allowances - Dynamic -->
                @foreach ($allAllowanceNames as $name)
                    <td class="text-right">{{ number_format($sumAllowances[$name]) }}</td>
                @endforeach

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
            <p style="margin-bottom: 5px;">{{ $currentUnit ? $currentUnit->location : 'Cirebon' }},
                {{ $currentUnit && $currentUnit->signature_date ? $currentUnit->signature_date->locale('id')->translatedFormat('d F Y') : now()->locale('id')->translatedFormat('d F Y') }}
            </p>
            <p style="margin-bottom: 60px;">Kepala Sekolah</p>
            <p style="text-decoration: underline; font-weight: bold;">
                {{ $currentUnit ? $currentUnit->principal_name : '________________________' }}</p>
        </div>
    </div>
</body>

</html>
