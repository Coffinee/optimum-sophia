<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            $table->enum('class', ['remitter', 'beneficiary']);
            $table->string('customer_type')->nullable();
            //remitter - cba
            $table->string('code')->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();
            $table->string('suffix', 10)->nullable();
            $table->string('mobile_number', 20)->nullable();
            $table->string('email', 50)->nullable();
            $table->longText('address')->nullable();

            $table->string('country', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('zip_code')->nullable();

            $table->string('birth_date', 20)->nullable();
            $table->string('birth_place', 50)->nullable();

            $table->string('gender')->nullable();

            $table->string('civil_status')->nullable(); //limit 2
            //marital status S-single,M-maried,W-Widow/er,D-Divorced
            $table->string('nationality', 50)->nullable();

            $table->longText('same_address')->nullable();
            $table->string('same_country', 50)->nullable();
            $table->string('same_state', 50)->nullable();
            $table->string('same_city', 100)->nullable();
            $table->string('same_zip_code')->nullable();

            $table->string('source_of_funds', 50)->nullable();
            //sf01-salary,sf02-business,sf03-company funds,
            //sf04-loan,sf05-sale of property,sf06-savings or deposits,sf07-others
            $table->string('purpose_of_remittance', 50)->nullable();
            $table->string('relationship_to_the_beneficiary', 50)->nullable();
            $table->string('id_type', 50)->nullable();
            $table->string('id_number', 25)->nullable();
            $table->string('employee_business_nature', 50)->nullable();
            $table->string('employee_business_name', 100)->nullable();
            $table->string('annual_salary', 18)->nullable();
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
        Schema::dropIfExists('customer_details');
    }
};
