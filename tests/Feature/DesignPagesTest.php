<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\ExtracurricularPayroll;
use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\Teacher;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_redesigned_pages_render_and_summary_uses_only_selected_unit_and_period(): void
    {
        $unit = Unit::create(['name' => 'MI Daarul Hikmah', 'code' => 'MI']);
        $otherUnit = Unit::create(['name' => 'Other', 'code' => 'OTHER']);
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-01', 'end_date' => '2027-06-30', 'is_active' => true]);
        $year->payrollSettings()->create(['unit_id' => $unit->id, 'teaching_rate_per_hour' => 20000, 'transport_rate_per_visit' => 10000]);
        $teacher = Teacher::create(['unit_id' => $unit->id, 'name' => 'Guru Contoh', 'is_active' => true, 'is_tahfidz' => true]);
        $extra = Extracurricular::create(['unit_id' => $unit->id, 'name' => 'Pramuka', 'rate' => 50000]);
        $batch = PayrollBatch::create(['unit_id' => $unit->id, 'academic_year_id' => $year->id, 'month' => 9, 'year' => 2026, 'name' => 'Honor September']);
        $payrollData = ['unit_id' => $unit->id, 'academic_year_id' => $year->id, 'teacher_id' => $teacher->id, 'month' => 9, 'year' => 2026, 'total_salary' => 900000, 'details' => ['breakdown' => ['teaching' => 500000, 'transport' => 200000, 'allowances' => 200000, 'tenure' => 100000, 'deductions' => ['bpjs' => 35000, 'late' => 65000]]]];
        $payroll = Payroll::create($payrollData + ['payroll_batch_id' => $batch->id]);
        Payroll::create(array_replace($payrollData, ['unit_id' => $otherUnit->id, 'total_salary' => 9999999]));
        Payroll::create(array_replace($payrollData, ['month' => 8, 'total_salary' => 8888888]));
        $extraPayroll = ExtracurricularPayroll::create(['unit_id' => $unit->id, 'academic_year_id' => $year->id, 'teacher_id' => $teacher->id, 'extracurricular_id' => $extra->id, 'month' => 9, 'year' => 2026, 'volume' => 2, 'rate' => 50000, 'total' => 100000]);
        $this->actingAs(User::factory()->create())->withSession(['unit_id' => $unit->id]);

        $this->get(route('dashboard', ['month' => 9, 'year' => 2026]))->assertOk()
            ->assertViewHas('totalPaid', 1000000)->assertViewHas('recipientCount', 1)
            ->assertViewHas('deductions', 100000)->assertViewHas('composition', fn ($value) => $value->sum() == 1100000)
            ->assertSee('Ringkasan')->assertSee('Honor September');
        $this->get(route('dashboard', ['month' => 13]))->assertSessionHasErrors('month');
        $this->get(route('dashboard', ['month' => 1, 'year' => 2025]))->assertOk()->assertViewHas('totalPaid', 0);

        foreach (['teachers.index', 'teachers.create', 'teachers.import', 'academic-years.index', 'academic-years.create', 'extracurriculars.index', 'extracurriculars.create', 'payrolls.index', 'payrolls.create', 'tahfidz-payrolls.index', 'tahfidz-payrolls.create', 'extracurricular-payrolls.index', 'extracurricular-payrolls.create', 'units.edit', 'profile.edit', 'backups.index'] as $route) {
            $this->get(route($route))->assertOk()->assertSee('app-sidebar');
        }
        $this->get(route('teachers.edit', $teacher))->assertOk();
        $this->get(route('academic-years.edit', $year))->assertOk();
        $this->get(route('extracurriculars.edit', $extra))->assertOk();
        $this->get(route('payrolls.batch.edit', $batch))->assertOk()->assertSee('Total tersimpan');
        $batch->update(['is_tahfidz' => true]);
        $payroll->update(['is_tahfidz' => true]);
        $this->get(route('tahfidz-payrolls.batch.edit', $batch))->assertOk()->assertSee('Total tersimpan');
        foreach (['payrolls', 'tahfidz-payrolls', 'extracurricular-payrolls'] as $section) {
            foreach (['report', 'print_all'] as $page) {
                $this->get(route($section . '.' . $page, ['month' => 9, 'year' => 2026]))->assertOk()->assertSee('Kembali ke daftar');
            }
            $this->get(route($section . '.show', $section === 'extracurricular-payrolls' ? $extraPayroll : $payroll))->assertOk()->assertSee('Cetak slip');
        }
    }
}
