<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\Payroll;

class PayrollFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_payroll_calculation_logic()
    {
        // 1. Create Academic Year
        $year = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $year->payrollSettings()->create([
            'teaching_rate_per_hour' => 50000,
            'transport_rate_per_visit' => 20000,
        ]);

        // 2. Create Teacher with Allowance
        $teacher = Teacher::create(['name' => 'Pak Budi']);
        
        $year->teacherAllowances()->create([
            'teacher_id' => $teacher->id,
            'allowance_name' => 'Wali Kelas',
            'amount' => 100000,
        ]);

        // 3. Process Payroll
        // Simulating the form submission
        $response = $this->post(route('payrolls.store'), [
            'month' => 1,
            'year' => 2026,
            'attendance' => [
                $teacher->id => [
                    'hours' => 10,
                    'days' => 5,
                ]
            ]
        ]);

        $response->assertRedirect(route('payrolls.index'));
        $response->assertSessionHas('success');

        // 4. Verify Database
        $this->assertDatabaseHas('payrolls', [
            'teacher_id' => $teacher->id,
            'month' => 1,
            'year' => 2026,
            'teaching_hours' => 10,
            'attendance_days' => 5,
        ]);

        $payroll = Payroll::where('teacher_id', $teacher->id)->first();
        
        // Expected: (10 * 50000) + (5 * 20000) + 100000 = 500000 + 100000 + 100000 = 700000.
        $this->assertEquals(700000, $payroll->total_salary);

        // Verify details json
        $this->assertEquals(50000, $payroll->details['teaching_rate']);
        $this->assertEquals(100000, $payroll->details['breakdown']['allowances']);
    }
}
