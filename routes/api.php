<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::namespace('App\\Http\\Controllers\\API')->group(function () {
    //USER
    Route::get('check-permission', 'UserController@checkUserPermission');
    Route::get('get-auth-user', 'UserController@getAuthenthecatedUser');

    //   COMPANY
    Route::get('get-companies', 'CompanyController@getCompanies');
    Route::get('company-search', 'CompanyController@customSearch');
    Route::put('change-company-status', 'CompanyController@changeCompanyStatus');

    //brnaches
    Route::get('get-branch/{branch}', 'BranchController@fetchBranchDetails');


    Route::get('get-inbound-api-token', 'APIAccessController@getInboundToken');
    Route::get('get-outbound-api-token', 'APIAccessController@getOutboundToken');
    Route::get('get-transaction-inbound-api-token', 'APIAccessController@getTransactionInboundToken');
    Route::get('get-transaction-outbound-api-token', 'APIAccessController@getTransactionOutboundToken');



    //address
    Route::prefix('address')->group(function () {
        Route::get('countries', 'AddressController@countries');
        Route::get('countries/{country}/states', 'AddressController@statesByCountry');
        Route::get('states/{state}/cities', 'AddressController@citiesByState');
        Route::get('currencies', 'AddressController@currencies');
    });

    Route::prefix('customer')->group(function () {
        Route::prefix('remitter')->group(function () {
            Route::get('search', 'CustomerDetailsController@search');
        });

        Route::prefix('beneficiary')->group(function () {
            Route::get('search', 'CustomerDetailsController@search');
            Route::get('{remitterId}', 'CustomerDetailsController@searchBeneficiary');
        });
    });

    Route::get('get-billers', 'BillersController@getBillers');


    Route::get('get-biller-categories', 'MaintenanceController@getBillerCategories');
    Route::get('get-transaction-types', 'MaintenanceController@getTransactionTypes');
    Route::get('get-source-of-funds', 'MaintenanceController@getSourceOfFunds');

    Route::post('read-file', 'BatchUploadController@uploadBatchFile');

    Route::get('', 'CompanyController@customSearch');

    Route::prefix('address')->group(function () {
        Route::get('countries', 'AddressController@countries');
        Route::get('countries/{country}/states', 'AddressController@statesByCountry');
        Route::get('states/{state}/cities', 'AddressController@citiesByState');
        Route::get('country/{country}/cities', 'AddressController@citiesByCountry');
        Route::get('currencies', 'AddressController@currencies');
    });

    Route::get('get-branch-currency', 'RatesController@getBranchCurrency');
    Route::post('file-upload', 'TransactionController@uploadFile');
    Route::post('validate-bp-transaction-fields', 'TransactionController@validateBPFields');
    Route::post('validate-otc-transaction-fields', 'TransactionController@validateOTCFields');
    Route::post('validate-dtd-transaction-fields', 'TransactionController@validateDTDFields');

    Route::post('transactions-bp', 'TransactionController@saveBPTransaction');
    Route::post('transactions-otc', 'TransactionController@saveOTCTransaction');
    Route::post('transactions-dtd', 'TransactionController@saveDTDTransaction');
    Route::post('transactions-cba', 'TransactionController@saveCBATransaction');



    Route::prefix('transactions')->group(function () {
        Route::prefix('pending')->group(function () {
            Route::get('', 'TransactionController@allPendingTransactions');
            Route::get('history', 'TransactionController@trackMakersPendingTransactions');
            Route::prefix('status')->group(function () {
                Route::post('', 'TransactionController@changeStatusByRole');
                Route::put('bulk', 'TransactionController@bulkChangeStatusByRole');
            });

            Route::prefix('decline')->group(function () {
                Route::delete('', 'TransactionController@declineBatch');
                Route::delete('bulk', 'TransactionController@bulkDeclineBatch');
            });
        });

        Route::get('processed', 'TransactionController@allProcessedTransactions');

        Route::prefix('monitor')->group(function () {
            Route::get('', 'TransactionController@allMonitorTransaction');
            Route::get('history', 'TransactionController@trackMakersMonitorTransactions');
        });
    });

    Route::apiResources([
        'users' => 'UserController',
        'companies' => 'CompanyController',
        'branches' => 'BranchController',
        'maintenance' => 'MaintenanceController',
        'billers' => 'BillersController',
        'transactions' => 'TransactionController',
        'rates' => 'RatesController',
        'fees' => 'FeesController',
        'custom-fields' => 'CustomFieldsController',
        'api-tokens' => 'APIAccessController',
    ]);
});
