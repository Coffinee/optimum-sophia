<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class DataEntryDTDRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return $this->isMethod('post') ? $this->createRules() : $this->updateRules();
    }

    /**
     * Define validation rules to store method for resource creation
     *
     * @return array
     */
    public function createRules(): array
    {
        return [
            // 'last_name' => 'required',
            // 'first_name' => 'required',
            // 'mobile_number' => 'required',
            // 'date_of_birth' => 'required',
            // 'gender' => 'required',
            // 'address' => 'required',
            // 'country' => 'required',
            // 'city' => 'required',
            // 'zip' => 'required',
            // 'civil_status' => 'required',
            // 'nationality' => 'required',
            'remitter_details' => 'required|array',
            'remitter_details.*.value' => 'required_if:remitter_details.*.require,true',
            // 'bene_last_name' => 'required',
            // 'bene_first_name' => 'required',
            // 'bene_mobile_number' => 'required',
            // 'bene_address' => 'required',
            // 'bene_date_of_birth' => 'required',
            // 'bene_place_of_birth' => 'required',
            // 'bene_gender' => 'required',
            // 'bene_civil_status' => 'required',
            // 'bene_nationality' => 'required',
            'beneficiary_details' => 'required|array',
            'beneficiary_details.*.value' => 'required_if:remitter_details.*.require,true',

            'confirm_address' => 'required',
            'confirm_country' => 'required',
            'confirm_city' => 'required',

            'source_of_fund' => 'required',
            'source_of_fund_fields' => 'required|array',
            'source_of_fund_fields.*.value' => 'required_if:source_of_fund_fields.*.require,true',

            'purpose' => 'required',
            'relationship' => 'required',
            'transaction_type' => 'required',
            'original_currency' => 'required',
            'original_amount' => 'required',
            'exchange_rate' => 'required',
            'gross_amount' => 'required',
            'service_fee' => 'required',
            'net_amount' => 'required',
            'batch_number' => 'required|unique:transactions,id,' . $this->get('id'),
        ];
    }

    /**
     * Define validation rules to update method for resource update
     *
     * @return array
     */
    public function updateRules(): array
    {
        return [
            'category' => 'required',
            'name' => 'required|unique:billers,id,' . $this->get('id'),
            'fields' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [
            // 'biller_fields.*.value.required_if' => 'This :biller_fields.*.label is required',
            'last_name.required' => 'Last Name is required.',
            'first_name.required' => 'First Name is required.',
            'mobile_number.required' => 'Mobile Number is required.',
            'date_of_birth.required' => 'Date of Birth is required.',

            'bene_last_name.required' => 'Last Name is required.',
            'bene_first_name.required' => 'First Name is required.',
            'bene_mobile_number.required' => 'Mobile Number is required.',
            'bene_date_of_birth.required' => 'Date of Birth is required.',


            'confirm_address.required' => 'Address is required.',
            'confirm_country.required' => 'Country is required.',
            'confirm_city.required' => 'City is required.',

            'gender.required' => 'Gender is required.',
            'source_of_fund.required' => 'Source of Fund is required.',
            'biller_name.required' => 'Biller is required.',
            'original_currency.required' => 'Currency is required.',
            'original_amount.required' => 'Amount is required.',
            'exchange_rate.required' => 'Exchange Rate is required.',
            'gross_amount.required' => 'Total Amount is required.',
            'service_fee.required' => 'Service Fee is required.',
            'net_amount.required' => 'Net Amount is required.',
            'purpose.required' => 'Purpose of Remittance is required.',
        ];

        foreach ($this->get('source_of_fund_fields') as $key => $field) {
            if (isset($field['label'])) {
                $messages["source_of_fund_fields.$key.value.required_if"] = $field['label'] . " is required.";
            }
        }



        return $messages;
    }
}
