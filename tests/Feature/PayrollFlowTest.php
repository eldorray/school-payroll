<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Payroll;
use App\Models\Teacher;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_payroll_calculation_logic()
    {
        // 1. Unit + Academic Year with per-unit rates
        $unit = Unit::create(['name' => 'MI Test', 'code' => 'MI']);

        $year = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $year->payrollSettings()->create([
            'unit_id' => $unit->id,
            'teaching_rate_per_hour' => 50000,
            'transport_rate_per_visit' => 20000,
            'masa_kerja_rate_per_year' => 0,
        ]);

        // 2. Teacher with annual teaching hours + allowance
        $teacher = Teacher::create([
            'name' => 'Pak Budi',
            'unit_id' => $unit->id,
            'is_active' => true,
        ]);

        $year->teacherAnnualSettings()->create([
            'teacher_id' => $teacher->id,
            'teaching_hours_per_month' => 10,
            'bpjs_amount' => 0,
        ]);

        $year->teacherAllowances()->create([
            'teacher_id' => $teacher->id,
            'allowance_name' => 'Wali Kelas',
            'amount' => 100000,
        ]);

        // 3. Process Payroll (unit dipilih saat login, disimpan di session)
        $this->actingAs(User::factory()->create())->withSession(['unit_id' => $unit->id]);

        $response = $this->post(route('payrolls.store'), [
            'month' => 1,
            'year' => 2026,
            'attendance' => [
                $teacher->id => ['days' => 5],
            ],
        ]);

        $response->assertRedirect(route('payrolls.index', ['month' => 1, 'year' => 2026]));
        $response->assertSessionHas('success');

        // 4. Verify Database
        $this->assertDatabaseHas('payrolls', [
            'teacher_id' => $teacher->id,
            'unit_id' => $unit->id,
            'month' => 1,
            'year' => 2026,
            'teaching_hours' => 10,
            'attendance_days' => 5,
        ]);

        $payroll = Payroll::where('teacher_id', $teacher->id)->first();

        // Expected: (10 * 50000) + (5 * 20000) + 100000 = 700000.
        $this->assertEquals(700000, $payroll->total_salary);

        // Verify details json
        $this->assertEquals(50000, $payroll->details['teaching_rate']);
        $this->assertEquals(100000, $payroll->details['breakdown']['allowances']);
    }
}
