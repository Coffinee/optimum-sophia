<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;


class CustomerDetails extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        //'transaction_id',
        'class', // remitter or beneficiary
        'customer_type', //I-individual, C-corporate

        //remitter - cba
        'code',
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'mobile_number',
        'email',
        'address', //address1

        'country',  //address 2
        'state',  //address 3
        'city',  //address 4
        'zip_code',  //address 5

        'birth_date',  //remitter birthdate // date of incorporation
        'birth_place',  //remitter birth place

        'gender',  //M-male, F-Female

        'civil_status',
        //marital status S-single,M-maried,W-Widow/er,D-Divorced
        'nationality',

        'same_address',
        'same_country',
        'same_state',
        'same_city',
        'same_zip_code',

        'source_of_funds',
        //sf01-salary,sf02-business,sf03-company funds,
        //sf04-loan,sf05-sale of property,sf06-savings or deposits,sf07-others
        'purpose_of_remittance',
        'relationship_to_the_beneficiary',
        'id_type',
        'id_number',
        'employee_business_nature',
        'employee_business_name',
        'annual_salary',
        'data_entry_elements'
    ];

    //add more or make it dynamic too
    public const JSON_COLUMNS = ['COUNTRY', 'STATE', 'CITY', 'NATIONALITY'];

    protected $casts = [
        'last_name' => 'string',
        'first_name' => 'string',
        'middle_name' => 'string',
        'email' => 'string',
        'address' => 'string',
        'country' => 'string',
        'state' => 'string',
        'city' => 'string',
        'same_address' => 'string',
        'same_country' => 'string',
        'same_state' => 'string',
        'same_city' => 'string',
        'same_zip_code' => 'string',
        'data_entry_elements' => 'array',

    ];

    public function transaction()
    {
        $this->belongsTo(Transaction::class);
    }

    public function setAttribute($key, $value)
    {
        if (array_key_exists($key, $this->casts) && is_string($value)) {
            $this->applyMutator($key, $value);
        }

        return parent::setAttribute($key, $value);
    }

    protected function applyMutator($key, &$value)
    {
        switch ($this->casts[$key]) {
            case 'string':
                $value = str_replace('Ñ', 'N', $value);
                break;
                // Add cases for other types if needed
        }
    }

    public function saveRemitter($request)
    {
        $attributes = [
            'class' => 'remitter',
            'customer_type' => 'I',
            'source_of_funds' => $request->source_of_fund['value'],
            'purpose_of_remittance' => $request->purpose,
            'relationship_to_the_beneficiary' => $request->relationship,
        ];
        if (array_key_exists('id', $request->remitter_details)) {

            self::where('id', $request->remitter_details['id'])->update(array_merge($attributes, ['data_entry_elements' => $request->remitter_details['fields']]));
            return self::find($request->remitter_details['id']);
        } else {
            $dataToSave = [];
            $id = 0;

            foreach ($request->remitter_details['fields'] as $remitter) {

                $dataToSave[] = [
                    'id' =>  $id,
                    'name' => $remitter['name'],
                    'col_name' => $remitter['name'],
                    'type' => $remitter['type'],
                    'tags' => $remitter['tags'] ?? '',
                    'label' => $remitter['label'],
                    'value' => in_array(strtoupper(str_replace(' ', '', $remitter['name'])), self::JSON_COLUMNS)
                        ? $remitter['value']['label']
                        : $remitter['value'],

                ];

                $id++;
            }

            return self::create(array_merge($attributes, ['data_entry_elements' => $dataToSave]));

            // return $this->create([
            //     'class' => 'remitter',
            //     'customer_type' => 'I',
            //     // 'data_entry_elements' => json_encode($dataToSave),
            //     'data_entry_elements' => $dataToSave,
            //     // 'last_name' => $request->last_name,
            //     // 'first_name' => $request->first_name,
            //     // 'middle_name' => $request->middle_name,
            //     // 'suffix' => $request->suffix,
            //     // 'mobile_number' => $request->mobile_number,
            //     // 'email' => $request->email,
            //     // 'address' => $request->address,
            //     // 'country' => $request->country['value'],
            //     // 'state' => $request->states != null || $request->states != "" ? $request->states['value'] : $request->region['value'],
            //     // 'city' => $request->city['value'],
            //     // 'zip_code' => $request->zip,
            //     // 'birth_date' => $request->date_of_birth,
            //     // 'birth_place' => $request->place_of_birth,
            //     // 'gender' => $request->gender['value'],
            //     // 'civil_status' => $request->civil_status['value'],
            //     // 'nationality' => $request->nationality['value'],
            //     'source_of_funds' => $request->source_of_fund['value'],
            //     'purpose_of_remittance' => $request->purpose,
            //     'relationship_to_the_beneficiary' => $request->relationship,
            // ]);
        }
    }
    public function saveBeneficiary($request)
    {

        $attributes = [
            'class' => 'beneficiary',
            'customer_type' => 'I',

            'source_of_funds' => $request->source_of_fund['value'],
            'purpose_of_remittance' => $request->purpose,
            'relationship_to_the_beneficiary' => $request->relationship,
        ];


        if (array_key_exists('id', $request->beneficiary_details)) {
            self::where('id', $request->beneficiary_details['id'])->update(array_merge($attributes, ['data_entry_elements' => $request->beneficiary_details['fields']]));
            return self::find($request->beneficiary_details['id']);
        } else {


            $dataToSave = [];
            $id = 0;

            foreach ($request->beneficiary_details['fields'] as $beneficiary) {
                $dataToSave[] = [
                    'id' =>  $id,
                    'name' => $beneficiary['name'],
                    'col_name' => $beneficiary['name'],
                    'type' => $beneficiary['type'],
                    'tags' => $beneficiary['tags'] ?? '',
                    'label' => $beneficiary['label'],
                    'value' => in_array($beneficiary['name'], self::JSON_COLUMNS)
                        ? $beneficiary['value']['label']
                        : $beneficiary['value'],
                ];
                $id++;
            }

            return self::create(array_merge($attributes, ['data_entry_elements' => $dataToSave]));
            // return $this->create([
            //     'class' => 'beneficiary',
            //     'customer_type' => 'I',
            //     // 'data_entry_elements' => json_encode($dataToSave),
            //     'data_entry_elements' => $dataToSave,
            //     // 'last_name' => $request->bene_last_name,
            //     // 'first_name' => $request->bene_first_name,
            //     // 'middle_name' => $request->bene_middle_name,
            //     // 'suffix' => $request->bene_suffix,
            //     // 'mobile_number' => $request->bene_mobile_number,
            //     // 'email' => $request->bene_email,
            //     // 'address' => $request->bene_address,
            //     // 'country' => $request->bene_country['value'],
            //     // 'state' => $request->bene_states != null || $request->bene_states != "" ? $request->bene_states['value'] : $request->bene_region['value'],
            //     // 'city' => $request->bene_city['value'],
            //     // 'zip_code' => $request->bene_zip,
            //     // 'birth_date' => $request->bene_date_of_birth,
            //     // 'birth_place' => $request->bene_place_of_birth,
            //     // 'gender' => $request->bene_gender['value'],
            //     // 'civil_status' => $request->bene_civil_status['value'],
            //     // 'nationality' => $request->bene_nationality['value'],

            //     'same_address' => $request->filled('confirm_address') ? $request->confirm_address : "",
            //     'same_country' => $request->filled('confirm_country') ? $request->confirm_country : "",
            //     'same_state' => $request->filled('confirm_states') ? $request->confirm_states : "",
            //     'same_city' => $request->filled('confirm_city') ? $request->confirm_city : "",
            //     'same_zip_code' => $request->filled('confirm_zip') ? $request->confirm_zip : "",

            // ]);
        }
    }

    public static function getId($code)
    {
        return self::whereCode($code)
            ->toBase()
            ->latest('id')
            ->get('id')
            ->first();
    }

    public static function getIdForExcel($class)
    {
        return self::latest('id')
            ->toBase()
            ->whereClass($class)
            ->get('id')
            ->first();
    }

    public function toSearchableArray()
    {
        return [
            'data_entry_elements' => $this->data_entry_elements,
        ];
    }
}
