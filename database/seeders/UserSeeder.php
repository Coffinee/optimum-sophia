<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'role' => 'admin',
                'user_name' => 'developer',
                'first_name' => 'Dexter',
                'last_name' => 'Castillo',
                'middle_name' => 'Mahinay',
                'mobile_number' => '09174617171',
                'email' => 'dccoc12@gmail.com',
                'company_id' => 1,
                'branch_id' => 1,
                'status' => true,
                'password' => Hash::make('asdasd123'),
                'is_online' => false,
                'email_verified_at' => date('Y-m-d h:i:s'),
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'role' => 'maker',
                'user_name' => 'maker',
                'first_name' => 'Jesus',
                'last_name' => 'Bermudo',
                'middle_name' => 'Mahinay',
                'mobile_number' => '09174617171',
                'email' => 'jesus@dev.com',
                'company_id' => 1,
                'branch_id' => 1,
                'status' => true,
                'password' => Hash::make('asdasd123'),
                'is_online' => false,
                'email_verified_at' => date('Y-m-d h:i:s'),
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'role' => 'verifier',
                'user_name' => 'verifier',
                'first_name' => 'Rennard',
                'last_name' => 'Bartolome',
                'middle_name' => 'Mahinay',
                'mobile_number' => '09174617171',
                'email' => 'ren@dev.com',
                'company_id' => 1,
                'branch_id' => 1,
                'status' => true,
                'password' => Hash::make('asdasd123'),
                'is_online' => false,
                'email_verified_at' => date('Y-m-d h:i:s'),
                'created_at' => date('Y-m-d h:i:s'),
            ],
            [
                'role' => 'approver',
                'user_name' => 'approver',
                'first_name' => 'Joeren',
                'last_name' => 'Dupaya',
                'middle_name' => 'Mahinay',
                'mobile_number' => '09174617171',
                'email' => 'joeren@dev.com',
                'company_id' => 1,
                'branch_id' => 1,
                'status' => true,
                'password' => Hash::make('asdasd123'),
                'is_online' => false,
                'email_verified_at' => date('Y-m-d h:i:s'),
                'created_at' => date('Y-m-d h:i:s'),
            ]
        ];
        DB::table('users')->insert($users);

        $permissions = [
            [
                'user_id' => "1",
                'permissions' => collect([
                    "file_upload", "data_entry", "amendment", "inquiry", "monitor_transactions", "pending_transactions", "proccessed_transactions", "refund_request",
                    "disposition_report", "long_outstanding_report", "status_report", "rate_management", "fees_management", "company", "branch", "users", "others", "billers"
                ]),
            ], [
                'user_id' => "2",
                'permissions' => collect([
                    "file_upload", "data_entry", "amendment", "inquiry", "monitor_transactions", "pending_transactions", "proccessed_transactions", "refund_request",
                    "rate_management", "fees_management", "company", "branch", "users", "others", "billers"
                ]),
            ], [
                'user_id' => "3",
                'permissions' => collect([
                    "file_upload", "data_entry", "amendment", "inquiry", "monitor_transactions", "pending_transactions", "proccessed_transactions", "refund_request",
                    "rate_management", "fees_management", "company", "branch", "users", "others", "billers"
                ]),
            ], [
                'user_id' => "4",
                'permissions' => collect([
                    "file_upload", "data_entry", "amendment", "inquiry", "monitor_transactions", "pending_transactions", "proccessed_transactions", "refund_request",
                    "rate_management", "fees_management", "company", "branch", "users", "others", "billers"
                ]),
            ]
        ];

        DB::table('user_permissions')->insert($permissions);
    }
}
