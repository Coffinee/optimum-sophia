<?php

use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use App\Models\{Transaction, User};


function money_format($value)
{
    return number_format((float)$value, 2, '.', ',');
}

function fileNameOnly_format($file)
{
    return pathinfo($file, PATHINFO_FILENAME);
}

function getFileName($file)
{
    return $file->getClientOriginalName();
}

function getFileExtension($file)
{
    return $file->extension();
}

function checkEmpty($value)
{
    return !empty($value) ? $value : '';
}
function getLastName($value)
{
    $fullName = explode(" ", $value);
    if (end($fullName) == "") {
        array_pop($fullName);
    }
    $lastName = end($fullName);

    return checkEmpty($lastName);
}

function getFirstName($value)
{
    $fullName = explode(" ", $value, 2);
    $lastName = array_pop($fullName);
    $firstName = implode(" ", $fullName);

    return checkEmpty($firstName);
}

function getBankNameForBrunphil($value)
{
    return checkEmpty(strstr($value, 'Acct No.;', true));
}

function getAccountNumberForBrunphil($value)
{
    return checkEmpty(ltrim(strrchr($value, ';'), '; '));
}

function removeSpecialCharacters($value, $pattern = '/[^a-zA-Z0-9]/s')
{
    return preg_match($pattern, $value) ?  preg_replace($pattern, '', $value) : $value;
}

function itemCountFormat($value, $length = 5, $pad_string = '0', $pad_type = STR_PAD_LEFT): string
{
    $value = $value + 1;
    return str_pad($value, $length, $pad_string, $pad_type);
}


function paginate(Collection $results, $showPerPage, $pageName = 'page')
{
    $pageNumber = Paginator::resolveCurrentPage($pageName);

    $totalPageNumber = $results->count();

    return paginator($results->forPage($pageNumber, $showPerPage), $totalPageNumber, $showPerPage, $pageNumber, [
        'path' => Paginator::resolveCurrentPath(),
        'pageName' => $pageName,
    ]);
}

function paginator($items, $total, $perPage, $currentPage, $options)
{
    return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
        'items',
        'total',
        'perPage',
        'currentPage',
        'options'
    ));
}

function checkExecutionTime()
{
    $time_start = microtime(true);

    Transaction::paginate(10);

    $time_end = microtime(true);
    $time = number_format($time_end - $time_start, 3);

    dd($time);
}

function strRemoveSpaces($string)
{
    return str_replace(' ', '', $string);
}

function makeStrUpperCase($string)
{
    return strtoupper($string);
}
