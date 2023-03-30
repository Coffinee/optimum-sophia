<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;



class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startTime = microtime(true);
        // transaction::factory(5000)->create(); //8.58s
        Transaction::factory(50000)->create(); //105.95s
        $seconds = number_format((microtime(true) - $startTime), 2);


        dd($seconds);
    }
}
