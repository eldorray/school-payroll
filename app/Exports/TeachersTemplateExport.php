<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeachersTemplateExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * Return sample data for the template
     *
     * @return array
     */
    public function array(): array
    {
        return [
            ['Ahmad Suryadi, S.Pd', 'Guru Kelas', '198501152010011001', '2020-01-15'],
            ['Siti Fatimah, M.Pd', 'Kepala Sekolah', '197803202005012001', '2005-01-20'],
            ['Budi Santoso, S.Pd.I', 'Guru', '', '2022-07-01'],
        ];
    }

    /**
     * Define the headers for the template
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nama',
            'Jabatan',
            'NIP',
            'Tanggal_Bergabung',
        ];
    }

    /**
     * Style the worksheet
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(20);

        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ];
    }
}
