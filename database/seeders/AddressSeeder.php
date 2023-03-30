<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Country, State, City};
use DB;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //change path into storage or s3
        $countrySqlPath = public_path('assets/sql/countries.sql');
        $this->sqlSeeder($countrySqlPath);

        $stateSqlPath = public_path('assets/sql/states.sql');
        $this->sqlSeeder($stateSqlPath);

        $citySqlPath = public_path('assets/sql/cities.sql');
        $this->sqlSeeder($citySqlPath);
    }

    private function sqlSeeder($sqlPath)
    {

        $sql = file_get_contents($sqlPath);
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $stmt) {
            DB::statement($stmt);
        }
    }
}
