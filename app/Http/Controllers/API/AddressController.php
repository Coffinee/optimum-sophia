<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Country, State, City};

class AddressController extends BaseController
{

    public function countries()
    {
        $data = Country::all('name', 'id', 'currency');
        $arr = [];
        foreach ($data as $i) {
            $arr[] = [
                "label" => $i->name,
                "value" => $i->id,
                "currency" => $i->currency,
                "currency_name" => $i->currency_name,
            ];
        }
        return $this->sendResponse($arr, "All countries in array");
    }



    public function statesByCountry(Country $country)
    {
        $stateExists = State::where(['country_id' => $country->id, 'type' => 'state'])->exists();
        $regionExists = State::where(['country_id' => $country->id, 'type' => 'region'])->exists();
        $noType = State::where(['country_id' => $country->id, 'type' => null])->exists();

        $data = State::select('name', 'id')->where(['country_id' => $country->id, 'type' => $stateExists ? 'state' : ($regionExists ? 'region' : null)])->orderBy('name', 'ASC')->get();
        $provinces = State::select('name', 'id')->where(['country_id' => $country->id, 'type' => 'province'])->orderBy('name', 'ASC')->get();
        $arr = [];
        foreach ($data as $i) {
            $arr[] = [
                "label" => $i->name,
                "value" => $i->id,
            ];
        }

        $prov = [];
        foreach ($provinces as $i) {
            $prov[] = [
                "label" => $i->name,
                "value" => $i->id,
            ];
        }
        return $this->sendResponse(['data' => $arr, 'hasState' => $noType ? $noType : $stateExists, 'provinces' => $prov], "States filtered by Country");
    }

    public function provinceByCountry(Country $country)
    {
        $data = State::select('name', 'id')
            ->where(['country_id', $country->id, 'type', 'province'])->get();
        $arr = [];
        foreach ($data as $i) {
            $arr[] = [
                "label" => $i->name,
                "value" => $i->id,
            ];
        }
        return $this->sendResponse($arr, "Province filtered by Country");
    }


    public function citiesByState(State $state)
    {
        $data = City::select('name', 'id')
            ->where('state_id', $state->id)->toBase()->get();
        $arr = [];
        foreach ($data as $i) {
            $arr[] = [
                "label" => $i->name,
                "value" => $i->id,
            ];
        }
        return $this->sendResponse($arr, "Cities filtered by State");
    }

    public function citiesByCountry(Country $country)
    {
        $data = City::distinct()->where('country_id', $country->id)->toBase()->get(['name']);
        $arr = [];
        foreach ($data as $item) {
            $arr[] = [
                "label" => $item->name,
                "value" => $item->name,
            ];
        }
        return $this->sendResponse($arr, "Cities filtered by Country");
    }

    public function currencies()
    {
        $data = Country::groupBy('currency')->get(['id', 'currency', 'currency_name']);
        $arr = [];
        foreach ($data as $i) {
            $arr[] = [
                "label" => $i->currency . ' - ' . $i->currency_name,
                "value" => $i->id
            ];
        }
        return $this->sendResponse($arr, "All Currencies");
    }
}
