<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\APIAccess;

class APIAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        APIAccess::insert([
            [
                'name' => 'inbound-api',
                'api_token' => 'Get the API token in EOS SYSTEM',
            ],
            [
                'name' => 'outbound-api',
                'api_token' => 'Get the API token in EOS SYSTEM',
            ],
            [
                'name' => 'transaction-inbound-api',
                'api_token' => 'Get the API token in EOS SYSTEM',
            ],
            [
                'name' => 'transaction-outbound-api',
                'api_token' => 'Get the API token in EOS SYSTEM',
            ],
        ]);
    }
}
