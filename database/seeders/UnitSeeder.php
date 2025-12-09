<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        Unit::updateOrCreate(
            ['code' => 'smp'],
            [
                'name' => 'SMP Garuda',
                'location' => 'Cirebon',
                'principal_name' => 'Drs. H. Ahmad Suryadi, M.Pd',
            ]
        );

        Unit::updateOrCreate(
            ['code' => 'mi'],
            [
                'name' => 'MI Daarul Hikmah',
                'location' => 'Cirebon',
                'principal_name' => 'Hj. Siti Nurhalimah, S.Pd.I',
            ]
        );
    }
}
