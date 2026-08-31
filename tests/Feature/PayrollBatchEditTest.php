<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\Teacher;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollBatchEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_late_deduction_uses_nominal_input_and_batch_edit_can_remove_teacher(): void
    {
        $unit = Unit::create(['name' => 'MI Test', 'code' => 'MI']);
        $year = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);
        $year->payrollSettings()->create([
            'unit_id' => $unit->id,
            'teaching_rate_per_hour' => 0,
            'transport_rate_per_visit' => 10000,
            'masa_kerja_rate_per_year' => 0,
            'late_deduction_rate' => 5000, // tarif tahun ajaran harus diabaikan
        ]);

        $keep = Teacher::create(['name' => 'Pak Budi', 'unit_id' => $unit->id, 'is_active' => true]);
        $drop = Teacher::create(['name' => 'Bu Ani', 'unit_id' => $unit->id, 'is_active' => true]);

        $this->actingAs(User::factory()->create())->withSession(['unit_id' => $unit->id]);

        $this->post(route('payrolls.store'), [
            'month' => 1,
            'year' => 2026,
            'attendance' => [
                $keep->id => ['days' => 10, 'deductions' => ['late_deduction' => 30000]],
                $drop->id => ['days' => 10],
            ],
        ])->assertSessionHas('success');

        // Potongan terlambat = nominal input, bukan jumlah x tarif tahun ajaran
        $keepPayroll = Payroll::where('teacher_id', $keep->id)->firstOrFail();
        $this->assertEquals(30000, $keepPayroll->transport_allowance_deduction_amount);
        $this->assertEquals(30000, $keepPayroll->details['breakdown']['deductions']['late']);
        $this->assertEquals(100000 - 30000, $keepPayroll->total_salary);

        // Edit batch: buang guru yang tidak seharusnya digaji
        $batch = PayrollBatch::firstOrFail();
        $dropPayroll = Payroll::where('teacher_id', $drop->id)->firstOrFail();

        $this->patch(route('payrolls.batch.update', $batch), [
            'attendance' => [
                $keepPayroll->id => ['days' => 10, 'deductions' => ['late_deduction' => 20000]],
                $dropPayroll->id => ['days' => 10],
            ],
            'remove' => [$dropPayroll->id],
        ])->assertSessionHas('success');

        $this->assertDatabaseMissing('payrolls', ['id' => $dropPayroll->id]);
        $this->assertEquals(20000, $keepPayroll->fresh()->transport_allowance_deduction_amount);
    }

    public function test_tahfidz_payroll_late_nominal_and_batch_edit_remove(): void
    {
        $unit = Unit::create(['name' => 'MI Test', 'code' => 'MI']);
        $year = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);
        $year->payrollSettings()->create([
            'unit_id' => $unit->id,
            'teaching_rate_per_hour' => 0,
            'transport_rate_per_visit' => 10000,
            'masa_kerja_rate_per_year' => 0,
        ]);

        $keep = Teacher::create(['name' => 'Ust. Fulan', 'unit_id' => $unit->id, 'is_active' => true, 'is_tahfidz' => true]);
        $drop = Teacher::create(['name' => 'Ust. Alan', 'unit_id' => $unit->id, 'is_active' => true, 'is_tahfidz' => true]);

        $this->actingAs(User::factory()->create())->withSession(['unit_id' => $unit->id]);

        $this->post(route('tahfidz-payrolls.store'), [
            'month' => 1,
            'year' => 2026,
            'attendance' => [
                $keep->id => ['days' => 10, 'deductions' => ['late_deduction' => 30000]],
                $drop->id => ['days' => 10],
            ],
        ])->assertSessionHas('success');

        $keepPayroll = Payroll::where('teacher_id', $keep->id)->firstOrFail();
        $this->assertEquals(30000, $keepPayroll->transport_allowance_deduction_amount);
        $this->assertEquals(30000, $keepPayroll->details['breakdown']['deductions']['late']);
        $this->assertEquals(100000 - 30000, $keepPayroll->total_salary);

        $batch = PayrollBatch::where('is_tahfidz', true)->firstOrFail();
        $dropPayroll = Payroll::where('teacher_id', $drop->id)->firstOrFail();

        $this->patch(route('tahfidz-payrolls.batch.update', $batch), [
            'attendance' => [
                $keepPayroll->id => ['days' => 10, 'deductions' => ['late_deduction' => 20000]],
                $dropPayroll->id => ['days' => 10],
            ],
            'remove' => [$dropPayroll->id],
        ])->assertSessionHas('success');

        $this->assertDatabaseMissing('payrolls', ['id' => $dropPayroll->id]);
        $this->assertEquals(20000, $keepPayroll->fresh()->transport_allowance_deduction_amount);
    }
}
