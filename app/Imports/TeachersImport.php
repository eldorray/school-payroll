<?php

namespace App\Imports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Carbon\Carbon;

class TeachersImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    protected $unitId;
    protected $importedCount = 0;

    public function __construct($unitId)
    {
        $this->unitId = $unitId;
    }

    /**
     * Map Excel row to Teacher model
     *
     * @param array $row
     * @return Teacher|null
     */
    public function model(array $row)
    {
        $this->importedCount++;

        // Handle date parsing - try multiple formats
        $joinedAt = null;
        if (!empty($row['tanggal_bergabung'])) {
            try {
                // If it's a number (Excel serial date)
                if (is_numeric($row['tanggal_bergabung'])) {
                    $joinedAt = Carbon::createFromTimestamp(
                        ($row['tanggal_bergabung'] - 25569) * 86400
                    );
                } else {
                    $joinedAt = Carbon::parse($row['tanggal_bergabung']);
                }
            } catch (\Exception $e) {
                $joinedAt = null;
            }
        }

        // Clean NIP - treat empty, '-', or whitespace-only values as null
        $nip = $this->cleanNip($row['nip'] ?? null);

        return new Teacher([
            'unit_id' => $this->unitId,
            'name' => $row['nama'],
            'position' => $row['jabatan'] ?? null,
            'nip' => $nip,
            'joined_at' => $joinedAt,
        ]);
    }

    /**
     * Clean NIP value - treat empty, '-', or whitespace-only values as null
     *
     * @param mixed $nip
     * @return string|null
     */
    private function cleanNip($nip)
    {
        if (empty($nip)) {
            return null;
        }

        $nip = trim((string) $nip);
        
        // Treat '-', '', or whitespace-only as null
        if ($nip === '' || $nip === '-' || $nip === '–' || preg_match('/^[\s\-–]+$/', $nip)) {
            return null;
        }

        return $nip;
    }

    /**
     * Validation rules for each row
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'nip' => 'nullable|string|max:50',
            'tanggal_bergabung' => 'nullable',
        ];
    }

    /**
     * Custom validation messages
     *
     * @return array
     */
    public function customValidationMessages(): array
    {
        return [
            'nama.required' => 'Kolom Nama wajib diisi pada baris :attribute.',
            'nama.max' => 'Nama tidak boleh lebih dari 255 karakter pada baris :attribute.',
        ];
    }

    /**
     * Get the count of imported rows
     *
     * @return int
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
