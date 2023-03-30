<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return  [
            'item_count' => fake()->name,
            'transaction_date' => fake()->name,
            'processing_type' => fake()->name,
            'reference_number' => fake()->name,
            'amendment_reference_number' => fake()->name, //mandatory for po2 and po3
            'transaction_type' => fake()->name,

            'original_curreny' => fake()->name,  //in_currency - List of currencie
            'original_amount' => fake()->name, //in_amount
            'exchange_rate' => fake()->name,
            'remittance_currency' => fake()->name, //OUT_currency - List of currencies
            'gross_amount' => fake()->name, //converted_amount
            'service_fee' => fake()->name,
            'net_amount' => fake()->name, //OUT_amount

            'remitter_id' => fake()->name,
            'remitter_customer_type' => fake()->name,

            //remitter - cba
            // 'remitter_reference_number'
            'remitter_last_name' => fake()->name,
            'remitter_first_name' => fake()->name,
            'remitter_middle_name' => fake()->name,
            'remitter_suffix' => fake()->name,
            'remitter_mobile_number' => fake()->name,
            'remitter_email' => fake()->name,
            'remitter_address' => fake()->name, //address1

            'remitter_country' => fake()->name,  //address 2
            'remitter_state' => fake()->name,  //address 3
            'remitter_city' => fake()->name,  //address 4
            'remitter_zip_code' => fake()->name,  //address 5

            'remitter_birth_date' => fake()->name,  //remitter birthdate // date of incorporation
            'remitter_birth_place' => fake()->name,  //remitter birth place

            'remitter_gender' => fake()->name,

            'remitter_civil_status' => fake()->name,
            'remitter_nationality' => fake()->name,

            'remitter_source_of_funds' => fake()->name,

            'remitter_purpose_of_remittance' => fake()->name,
            'remitter_relationship_to_the_beneficiary' => fake()->name,
            'remitter_id_type' => fake()->name,
            'remitter_id_number' => fake()->name,
            'remitter_employee_business_nature' => fake()->name,
            'remitter_employee_business_name' => fake()->name,
            'remitter_anual_salary' => fake()->name,

            //beneficiary - cba

            'beneficiary_id' => fake()->name,
            'beneficiary_customer_type' => fake()->name,
            // 'beneficiary_reference_number'
            'beneficiary_last_name' => fake()->name,
            'beneficiary_first_name' => fake()->name,
            'beneficiary_middle_name' => fake()->name,
            'beneficiary_suffix' => fake()->name,
            'beneficiary_mobile_number' => fake()->name,
            'beneficiary_email' => fake()->name,

            'beneficiary_address' => fake()->name,  //address1
            'beneficiary_country' => fake()->name,  //address2
            'beneficiary_state' => fake()->name,  //address3
            'beneficiary_city' => fake()->name,  //address4
            'beneficiary_zip_code' => fake()->name,  //address5

            'beneficiary_birth_date' => fake()->name,  //or date of incorporation
            'beneficiary_birth_place' => fake()->name,

            'beneficiary_gender' => fake()->name,
            'beneficiary_civil_status' => fake()->name, // marital status
            'beneficiary_nationality' => fake()->name,

            'bank_name' => fake()->name,
            'bank_branch' => fake()->name,
            'account_number' => fake()->name,
        ];

        // return [
        //     'tieup_id' => '1',
        //     'batch_number' => '123',
        //     'meta_name' => 'first_name',
        //     'meta_value' => fake()->name(),
        // ];
    }
}
