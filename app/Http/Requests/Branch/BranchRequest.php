<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class BranchRequest extends FormRequest
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
            'company' => 'required',
            'name' => 'required|string|max:191|unique:branches',
            'code' => 'required',
            'address' => 'required|string',
            'country' => 'required',
            'zip_code' => 'required',
            'prefix' => 'required|string|max:30',
            'currencies' => 'required|array|min:2',
            'currencies.*' => 'required',
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
            'company' => 'required',
            'name' => 'required|string|max:191|unique:branches,id', $this->request('id'),
            'code' => 'required',
            'address' => 'required|string',
            'country' => 'required',
            'zip_code' => 'required',
            'prefix' => 'required|string|max:30',
            'currencies' => 'required|array|min:2',
            'currencies.*' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'company.required' => 'Company is required.',
            'name.required' => 'Branch name is required.',
            'code.required' => 'Partner code is required.',
            'address.required' => 'Address is required.',
            'country.required' => 'Country is required.',
            'zip_code.required' => 'Zip code is required.',
            'prefix.required' => 'Prefix for reference is required.',
            'currencies.required' => 'Add at least 1 currencies.',
        ];
    }
}
