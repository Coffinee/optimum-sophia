<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class CustomFieldsRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            return $this->createRules();
        } elseif ($this->isMethod('put')) {
            return $this->updateRules();
        }
    }

    /**
     * Define validation rules to store method for resource creation
     *
     * @return array
     */
    public function createRules(): array
    {
        return [
            'source_of_funds' => 'required',
            'code' => 'required',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required',
            'fields.*.require' => 'required',
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
            'code' => 'required|max:30',
            'name' => 'required|string|unique:maintenances,id,' . $this->get('id'),
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
            'source_of_funds.required' => 'Source of fund is required.',
            'fields.required' => 'Custom fields need at least one item.',
            'fields.*.label.required' => 'Label name is required.',
            'fields.*.require.required' => 'Is it required?',
        ];
    }
}
