<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Transaction;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // $table->string('file_name');
            // $table->string('file_date');
            // $table->string('total_count');
            // $table->string('total_amount');

            $table->foreignId('company_id');
            $table->foreignId('branch_id');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('class', ['data-entry', 'bulk-upload']);
            // $table->string('company_id');

            $table->string('item_count')->nullable();
            $table->string('transaction_date')->nullable();

            $table->string('processing_type', 3)->nullable();
            //po1-processing,po2-amendment,po3-refund
            $table->string('reference_number', 50)->nullable(); //unique
            $table->string('amendment_reference_number', 50)->nullable(); //mandatory for po2 and po3 //unique
            $table->string('transaction_type')->nullable(); //3
            //t01-cba,t02-bp,t03-otc,t04-dtd
            $table->string('original_currency', 12)->nullable(); //in_currency - List of currencie
            $table->string('original_amount', 18)->nullable(); //in_amount
            $table->string('exchange_rate', 18)->nullable();
            $table->string('remittance_currency', 3)->nullable(); //OUT_currency - List of currencies
            $table->string('gross_amount', 18)->nullable(); //converted_amount
            $table->string('service_fee', 18)->nullable();

            $table->string('net_amount', 18)->nullable(); //OUT_amount

            $table->foreignId('remitter_id')->constrained('customer_details');
            $table->bigInteger('beneficiary_id')->nullable();
            // $table->string('remitter_code')->nullable();
            // $table->string('remitter_customer_type')->nullable(); //I-individual, C-corporate //limit 1

            // //remitter - cba
            // $table->string('remitter_code')->nullable();
            // $table->string('remitter_last_name', 100)->nullable();
            // $table->string('remitter_first_name', 100)->nullable();
            // $table->string('remitter_middle_name', 100)->nullable();
            // $table->string('remitter_suffix', 10)->nullable();
            // $table->string('remitter_mobile_number', 20)->nullable();
            // $table->string('remitter_email', 50)->nullable();
            // $table->longText('remitter_address')->nullable(); //address1

            // $table->string('remitter_country', 50)->nullable(); //address 2
            // $table->string('remitter_state', 50)->nullable(); //address 3
            // $table->string('remitter_city', 100)->nullable(); //address 4
            // $table->string('remitter_zip_code')->nullable(); //address 5 //4

            // $table->string('remitter_birth_date', 20)->nullable(); //remitter birthdate // date of incorporation
            // $table->string('remitter_birth_place', 50)->nullable(); //remitter birth place

            // $table->string('remitter_gender')->nullable(); //M-male, F-Female //limit 1

            // $table->string('remitter_civil_status')->nullable(); //limit 2
            // //marital status S-single,M-maried,W-Widow/er,D-Divorced
            // $table->string('remitter_nationality', 50)->nullable();

            // $table->string('remitter_source_of_funds', 50)->nullable();
            // //sf01-salary,sf02-business,sf03-company funds,
            // //sf04-loan,sf05-sale of property,sf06-savings or deposits,sf07-others
            // $table->string('remitter_purpose_of_remittance', 50)->nullable();
            // $table->string('remitter_relationship_to_the_beneficiary', 50)->nullable();
            // $table->string('remitter_id_type', 50)->nullable();
            // $table->string('remitter_id_number', 25)->nullable();
            // $table->string('remitter_employee_business_nature', 50)->nullable();
            // $table->string('remitter_employee_business_name', 100)->nullable();
            // $table->string('remitter_anual_salary', 18)->nullable();

            //beneficiary - cba

            // $table->string('beneficiary_id', 50)->nullable();
            // $table->string('beneficiary_customer_type')->nullable(); //limit 1

            // $table->string('beneficiary_code')->nullable();
            // $table->string('beneficiary_last_name', 100)->nullable();
            // $table->string('beneficiary_first_name', 100)->nullable();
            // $table->string('beneficiary_middle_name', 100)->nullable();
            // $table->string('beneficiary_suffix', 10)->nullable();
            // $table->string('beneficiary_mobile_number', 20)->nullable();
            // $table->string('beneficiary_email', 50)->nullable();

            // $table->string('beneficiary_address')->nullable(); //address1 //50
            // $table->string('beneficiary_country', 50)->nullable(); //address2
            // $table->string('beneficiary_state', 50)->nullable(); //address3
            // $table->string('beneficiary_city', 50)->nullable(); //address4
            // $table->string('beneficiary_zip_code', 10)->nullable(); //address5

            // $table->string('beneficiary_birth_date', 50)->nullable(); //or date of incorporation
            // $table->string('beneficiary_birth_place', 50)->nullable();

            // $table->string('beneficiary_gender')->nullable(); //limit 1
            // $table->string('beneficiary_civil_status')->nullable(); //1 // marital status
            // $table->string('beneficiary_nationality', 50)->nullable();

            $table->string('bank_name')->nullable(); //if full name convert it into 5 letters.
            $table->string('bank_branch')->nullable(); //20
            $table->string('account_number', 20)->nullable();

            $table->string('instruction_1')->nullable();
            $table->string('instruction_2')->nullable();
            $table->string('instruction_3')->nullable();
            $table->string('good_value_date')->nullable();

            $table->enum('status', Transaction::getTransactionStatuses())->nullable();
            $table->string('path')->nullable();
            $table->string('batch_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
