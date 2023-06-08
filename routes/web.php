<?php

use Illuminate\Support\Facades\Route;
use App\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// manipulasi data
Route::get('manipulation-room-data', 'ManipulationController@roomData');
Route::get('manipulation-doctor-data', 'ManipulationController@doctorData');
Route::get('manipulation-insurance-data', 'ManipulationController@insuranceData');
Route::get('manipulation-patient-data', 'ManipulationController@patientData');
Route::get('manipulation-price-data', 'ManipulationController@priceData');
Route::get('manipulation-delete-master-test-data', 'ManipulationController@deleteMasterTest');

Route::get('manipulation-transaction-data', 'ManipulationController@transactionData');
Route::get('manipulation-transaction-test-data', 'ManipulationController@transactionTestData');
Route::get('manipulation-finish-transaction-data', 'ManipulationController@finishTransactionData');
Route::get('manipulation-finish-transaction-test-data', 'ManipulationController@finishTransactionTestData');

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();

Route::get('home', 'HomeController@index')->name('home');
Route::get('check-email', function (Request $request) {
    $user = User::where('email', $request->email)->exists();
    if ($user) {
        return response()->json('Email has already been taken');
    }
    return response()->json('true');
});
Route::get('check-username', function (Request $request) {
    $user = User::where('username', $request->username)->exists();
    if ($user) {
        return response()->json('Username has already been taken');
    }
    return response()->json('true');
});

Route::get('send', 'MailController@send');


// Dashboard
Route::middleware(['auth'])->group(function () {
    // badge info
    Route::get('main-layout/badge-info', 'Controller@badgeInfo');
    // end of badge info
    // BEGIN Pre Analytics
    Route::prefix('pre-analytics')->group(function () {
        Route::get('/', 'PreAnalyticController@index')->name('pre-analytics');
        // Analytics datatables
        Route::get('datatable/{startDate?}/{endDate?}', 'PreAnalyticController@datatable');
        Route::get('transaction-test/{transactionId}/datatable', 'PreAnalyticController@datatableTransactionTest');
        Route::get('transaction-specimen/{transactionId}/datatable', 'PreAnalyticController@datatableTransactionSpecimen');
        Route::get('edit-test/{roomClass}/{transactionId}/datatable', 'PreAnalyticController@datatableEditTest');
        Route::post('edit-test/selected-test/{roomClass}/{transactionId}', 'PreAnalyticController@selectedEditTest');
        Route::post('edit-test/add', 'PreAnalyticController@editTestAdd');
        Route::post('edit-test/update', 'PreAnalyticController@editTestUpdate');
        Route::post('transaction/note/update', 'PreAnalyticController@updateNote');
        Route::delete('edit-test/{transactionTestId}/delete', 'PreAnalyticController@editTestDelete');

        Route::get('test/{roomClass}/datatable/withoutId/{ids}', 'PreAnalyticController@datatableSelectTest');
        Route::get('test/{roomClass}/datatable', 'PreAnalyticController@datatableTest');
        // END of analytics datatable
        Route::get('analyzer-test/{testId}', 'PreAnalyticController@analyzerTest');
        Route::post('create', 'PreAnalyticController@create');
        Route::post('transaction-test/update-analyzer/{transactionTestId}', 'PreAnalyticController@updateAnalyzer');
        Route::post('specimen-test/update-draw', 'PreAnalyticController@updateDraw');
        Route::post('specimen-test/draw-all/{value}', 'PreAnalyticController@drawAll');
        Route::get('specimen-test/is-all-drawn/{transactionId}', 'PreAnalyticController@isAllDrawn');
        Route::post('check-in/{isManual?}', 'PreAnalyticController@checkIn');
        Route::delete('transaction/delete/{id}', 'PreAnalyticController@deleteTransaction');

        Route::get('get-user', 'PreAnalyticController@getUser');
        Route::put('update-cito-checkin', 'PreAnalyticController@updateCitoCheckin');

        Route::get('check-medical-record/{medrec}', 'PreAnalyticController@checkMedRec');
        Route::get('edit-patient-details/{transactionId}', 'PreAnalyticController@editPatientDetails');
        Route::put('update-patient-details', 'PreAnalyticController@updatePatientDetails');

        Route::get('go-to-analytics-btn/{transactionId}', 'PreAnalyticController@goToAnalyticBtn');
        Route::put('go-to-analytics', 'PreAnalyticController@goToAnalytic');
        Route::get('is-verified-test-exists/{transactionId}', 'PreAnalyticController@isVerifiedTestExist');
    });
    // END Pre Analytics

    // BEGIN Analytics
    Route::prefix('analytics')->group(function () {
        Route::get('/', 'AnalyticController@index')->name('analytics');

        // Datatable
        Route::get('datatable/{startDate?}/{endDate?}/{groupId?}', 'AnalyticController@datatable');
        Route::get('datatable-test/{transactionId}', 'AnalyticController@datatableTest');
        Route::get('result-label/{testId}', 'AnalyticController@resultlabel');
        Route::get('diff-counting/{transactionId}', 'AnalyticController@diffCounting');
        // End datatable

        Route::get('transaction/{transactionId}', 'AnalyticController@transaction');
        Route::put('update-result-number/{transactionTestId}', 'AnalyticController@updateResultNumber');
        Route::put('update-result-label/{transactionTestId}', 'AnalyticController@updateResultLabel');
        Route::put('update-result-description/{transactionTestId}', 'AnalyticController@updateResultDescription');

        Route::put('print-package-name/{transactionId}/{packageId}', 'AnalyticController@printPackageName');

        Route::put('verify-all/{transactionId}', 'AnalyticController@verifyAll');
        Route::put('unverify-all/{transactionId}', 'AnalyticController@unverifyAll');
        Route::put('unvalidate-all/{transactionId}', 'AnalyticController@unvalidateAll');
        Route::put('verify-test/{transactionTestId}', 'AnalyticController@verifyTest');
        Route::put('validate-all/{transactionId}', 'AnalyticController@validateAll');
        Route::put('validate-test/{transactionTestId}', 'AnalyticController@validateTest');
        Route::put('update-test-memo', 'AnalyticController@updateTestMemo');
        Route::put('update-memo-result', 'AnalyticController@updateMemoResult');

        Route::get('check-formula/{transactionId}/{transactionTestId}/{testId}', 'AnalyticController@checkFormula');
        Route::post('update-verificator-validator', 'AnalyticController@updateVerificatorValidator');

        Route::get('check-critical-test/{transactionId}', 'AnalyticController@checkCriticalTest');
        Route::put('report-critical-tests', 'AnalyticController@reportCriticalTest');
        Route::get('check-action-btn-test-status/{transactionId}', 'AnalyticController@checkActionBtnTestStatus');
        Route::put('go-to-post-analytics/{transactionId}', 'AnalyticController@goToPostAnalytics');

        Route::get('load-history-test/{patientId}', 'AnalyticController@loadHistoryTest');

        Route::delete('delete-transaction-test/{id}', 'AnalyticController@deleteTransactionTest');
        Route::post('mark-duplo', 'AnalyticController@markDuplo');
    });
    // END Analytics

    Route::prefix('post-analytics')->group(function () {
        Route::get('/', 'PostAnalyticController@index')->name('post-analytics');
        Route::get('datatable/{startDate?}/{endDate?}/{groupId?}', 'PostAnalyticController@datatable');
        Route::get('datatable-test/{transactionId}', 'PostAnalyticController@datatableTest');
        Route::get('datatable-process-time/{transactionId}', 'PostAnalyticController@datatableProcessTime');
        Route::post('return-to-analytic/{transactionId}', 'PostAnalyticController@returnToAnalytics');

        Route::put('print-package-name/{transactionId}/{packageId}', 'PostAnalyticController@printPackageName');

        Route::get('/get-verificator-name/{id}', 'PostAnalyticController@getVerificatorName');

        Route::put('update-is-print/{testtransactionId}', 'PostAnalyticController@updatePrintTest');
        Route::put('update-is-print-memo/{testtransactionId}', 'PostAnalyticController@updatePrintMemo');
    });

    // BEGIN all route for master data
    Route::get('master/{masterData}', 'MasterController@index');
    Route::post('master/{masterData}/create', 'MasterController@create');
    Route::get('master/{masterData}/edit/{id}', 'MasterController@edit');
    Route::put('master/{masterData}/update', 'MasterController@update');
    Route::put('master/{masterData}/active/{id}/{isActive}', 'MasterController@active');
    Route::put('master/{masterData}/default/{id}/{isActive}', 'MasterController@default');
    Route::delete('master/{masterData}/delete/{id}', 'MasterController@delete');
    // datatable route for master data
    Route::get('master/datatable/{masterData}/{with?}', 'MasterController@datatable');
    Route::get('master/ref-range/datatable', 'MasterController@testRangeDatatable');
    Route::get('master/test-label/datatable', 'MasterController@testLabelDatatable');
    Route::get('master/range/{testId}', 'MasterController@rangeDatatable');
    Route::get('master/result-range/{testId}', 'MasterController@resultRangeDatatable');
    // END all route for master data
    Route::get('master/test-packages/{Ids}', 'MasterController@getTestPackage');

    // for select option form
    Route::get('master/select-options/{masterData}/{searchKey}/{roomType?}', 'MasterController@selectOptions');

    //for printing
    Route::get('/printTestGroup/{groupId}/{transactionId}', 'PrintController@printTestGroup');     // print test by group in analytic
    Route::get('/printHasilTest/{id}', 'PrintController@hasilTest');                               // print post option BAHASA INDONESIA
    Route::get('/printAnalyticResult/{id}', 'PrintController@printAnalytic');                      // print analytic option BAHASA INDONESIA

    // Route::get('/barcode/{id}', 'PrintController@barcode');
    Route::get('/printBarcode-label/{id}', 'PrintController@showBarcode');                                            // print barcode all specimen
    Route::get('/printBarcode-single-specimen/{id}/{specimen_id}', 'PrintController@showBarcodeSingleSpecimen');      // print single specimen
    Route::get('/getFirstPrint/{id}', 'PostAnalyticController@getFirstPrint');

    // REPORT
    Route::prefix('report')->group(function () {

        // for select option form
        Route::get('/select-options', 'ReportController@selectOptionsType');
        Route::get('/select-patient-options', 'ReportController@selectOptionsPatient');
        Route::get('/select-insurance-options', 'ReportController@selectOptionsInsurance');
        Route::get('/select-doctor-options', 'ReportController@selectOptionsDoctor');

        // CRITICAL REPORT
        Route::get('/critical', 'ReportController@criticalReport');
        Route::get('/critical-datatable/{startDate?}/{endDate?}/{groupId?}', 'ReportController@criticalDatatable');
        Route::get('/critical-print/{startDate?}/{endDate?}/{groupId?}', 'ReportController@criticalPrint');

        // DUPLO REPORT
        Route::get('/duplo', 'ReportController@duploReport');
        Route::get('/duplo-datatable/{startDate?}/{endDate?}/{groupId?}', 'ReportController@duploDatatable');
        Route::get('/duplo-print/{startDate?}/{endDate?}/{groupId?}', 'ReportController@duploPrint');

        // GROUP TEST REPORT
        Route::get('/group-test', 'ReportController@groupTestReport');
        Route::get('/group-test-datatable/{startDate?}/{endDate?}/{groupId?}', 'ReportController@groupTestDatatable');
        Route::get('/group-test-print/{startDate?}/{endDate?}/{groupId?}', 'ReportController@groupTestPrint');

        // TAT REPORT
        Route::get('/tat', 'ReportController@TATReport');
        Route::get('/tat-datatable/{startDate?}/{endDate?}', 'ReportController@TATDatatable');
        Route::get('/tat-print/{startDate?}/{endDate?}', 'ReportController@TATPrint');
        Route::get('/tat-group-print/{startDate?}/{endDate?}', 'ReportController@TATGroupPrint');

        // TAT Target REPORT
        Route::get('/tat-target', 'ReportController@TATTargetReport');
        Route::get('/tat-target-datatable/{startDate?}/{endDate?}', 'ReportController@TATTargetDatatable');
        Route::get('/tat-target-print/{startDate?}/{endDate?}', 'ReportController@TATTargetPrint');

        // TAT CITO REPORT
        Route::get('/tat-cito', 'ReportController@TATCitoReport');
        Route::get('/tat-cito-datatable/{startDate?}/{endDate?}', 'ReportController@TATCitoDatatable');
        Route::get('/tat-cito-print/{startDate?}/{endDate?}', 'ReportController@TATCitoPrint');

        // PATIENT REPORT
        Route::get('/patient', 'ReportController@patientReport');
        Route::get('/patient-datatable/{startDate?}/{endDate?}/{type?}', 'ReportController@patientDatatable');
        Route::get('/patient-print/{startDate?}/{endDate?}/{type?}', 'ReportController@patientPrint');

        // PATIENT DETAIL REPORT
        Route::get('/patient-detail', 'ReportController@patientDetailReport');
        Route::get('/patient-detail-datatable/{startDate?}/{endDate?}/{patient?}', 'ReportController@patientDetailDatatable');
        Route::get('/patient-detail-print/{startDate?}/{endDate?}/{patient?}', 'ReportController@patientDetailPrint');

        // TEST REPORT
        Route::get('/test', 'ReportController@testReport');
        Route::get('/test-datatable/{startDate?}/{endDate?}/{testId?}', 'ReportController@testDatatable');
        Route::get('/test-print/{startDate?}/{endDate?}/{testId?}', 'ReportController@testPrint');

        // SARS COV-2 ANTIGEN REPORT
        Route::get('/sars-cov', 'ReportController@sarsCovReport');
        Route::get('/sars-cov-datatable/{startDate?}/{endDate?}', 'ReportController@sarsCovDatatable');
        Route::get('/sars-cov-print/{startDate?}/{endDate?}', 'ReportController@sarsCovPrint');

        // RAPID HIV REPORT
        Route::get('/rapid-hiv', 'ReportController@rapidHivReport');
        Route::get('/rapid-hiv-datatable/{startDate?}/{endDate?}', 'ReportController@rapidHivDatatable');
        Route::get('/rapid-hiv-print/{startDate?}/{endDate?}', 'ReportController@rapidHivPrint');

        // SPECIMEN REPORT
        Route::get('/specimen', 'ReportController@specimenReport');
        Route::get('/specimen-datatable/{startDate?}/{endDate?}/{specimenId?}', 'ReportController@specimenDatatable');
        Route::get('/specimen-print/{startDate?}/{endDate?}/{specimenId?}', 'ReportController@specimenPrint');

        // PHLEBOTOMY & SAMPLING REPORT
        Route::get('/flebotomi-sampling', 'ReportController@flebotomiSamplingReport');
        Route::get('/flebotomi-sampling-datatable/{startDate?}/{endDate?}/{specimenId?}', 'ReportController@flebotomiSamplingDatatable');
        Route::get('/flebotomi-sampling-print/{startDate?}/{endDate?}/{specimenId?}', 'ReportController@flebotomiSamplingPrint');

        // VERIFICATION & VALIDATION REPORT
        Route::get('/verification-validation', 'ReportController@verificationValidationReport');
        Route::get('/verification-validation-datatable/{startDate?}/{endDate?}/{testId?}', 'ReportController@verificationValidationDatatable');
        Route::get('/verification-validation-print/{startDate?}/{endDate?}/{testId?}', 'ReportController@verificationValidationPrint');

        // INSURANCE REPORT
        Route::get('/insurance', 'ReportController@insuranceReport');
        Route::get('/insurance-datatable/{startDate?}/{endDate?}', 'ReportController@insuranceDatatable');
        Route::get('/insurance-print/{startDate?}/{endDate?}', 'ReportController@insurancePrint');

        // BPJS REPORT
        Route::get('/bpjs', 'ReportController@bpjsReport');
        Route::get('/bpjs-datatable/{startDate?}/{endDate?}', 'ReportController@bpjsDatatable');
        Route::get('/bpjs-print/{startDate?}/{endDate?}', 'ReportController@bpjsPrint');

        // BILLING REPORT
        Route::get('/billing', 'ReportController@billingReport');
        Route::get('/billing-datatable/{startDate?}/{endDate?}/{insuranceId?}', 'ReportController@billingDatatable');
        Route::get('/billing-print/{startDate?}/{endDate?}/{insuranceId?}', 'ReportController@billingPrint');

        // DOCTOR REPORT
        Route::get('/doctor', 'ReportController@doctorReport');
        Route::get('/doctor-datatable/{startDate?}/{endDate?}/{doctor?}', 'ReportController@doctorDatatable');
        Route::get('/doctor-print/{startDate?}/{endDate?}/{doctor?}', 'ReportController@doctorPrint');

        // VISIT REPORT
        Route::get('/visit', 'ReportController@visitReport');
        Route::get('/visit-datatable/{startDate?}/{endDate?}', 'ReportController@visitDatatable');
        Route::get('/visit-print/{startDate?}/{endDate?}', 'ReportController@visitPrint');

        // ANALYZER REPORT
        Route::get('/analyzer', 'ReportController@analyzerReport');
        Route::get('/analyzer-datatable/{startDate?}/{endDate?}', 'ReportController@analyzerDatatable');
        Route::get('/analyzer-print/{startDate?}/{endDate?}', 'ReportController@analyzerPrint');
        
        // USER REPORT
        Route::get('/user', 'ReportController@userReport');
        Route::get('/user-datatable/{startDate?}/{endDate?}', 'ReportController@userDatatable');
        Route::get('/user-print/{startDate?}/{endDate?}', 'ReportController@userPrint');

        // USER PROCESS REPORT
        Route::get('/user-process', 'ReportController@userProcessReport');
        Route::get('/user-process-datatable/{startDate?}/{endDate?}', 'ReportController@userProcessDatatable');
        Route::get('/user-process-print/{startDate?}/{endDate?}', 'ReportController@userProcessPrint');
 
        
    });
    
    // LOG INTEGRATION
    Route::prefix('log-integration')->group(function () {
        Route::get('/', 'IntegrationLogController@index')->name('log-integration');
        Route::get('datatable-post-data/{startDate?}/{endDate?}', 'IntegrationLogController@datatablePostData');
        Route::get('datatable-send-data/{startDate?}/{endDate?}', 'IntegrationLogController@datatableSendData');
        Route::get('show-log/{logId}', 'IntegrationLogController@showLog');
    });

    //printer
    Route::prefix('printer-setting')->group(function () {
        Route::get('/', 'PrinterSettingController@index');
        Route::get('/datatable', 'PrinterSettingController@datatable');
        Route::post('create', 'PrinterSettingController@create');
        Route::post('update', 'PrinterSettingController@update');
        Route::delete('delete/{id}', 'PrinterSettingController@delete');
    });

    // QMS
    Route::prefix('qms')->group(function () {
        Route::get('/', 'QueueManagementController@index');
        Route::get('/datatable-pre-analytics/{startDate?}/{endDate?}', 'QueueManagementController@datatablePreAnalytics');
        Route::get('/datatable-analytics/{startDate?}/{endDate?}', 'QueueManagementController@datatableAnalytics');
        Route::get('/datatable-post-analytics/{startDate?}/{endDate?}', 'QueueManagementController@datatablePostAnalytics');
        Route::put('/update-completed-patient', 'QueueManagementController@updateCompletedPatient');
        Route::get('/display', 'QueueManagementController@displayQueue');
        Route::get('/datatable-pre-display', 'QueueManagementController@datatablePreDisplay');
    });

    // Quality Control
    Route::prefix('quality-control')->group(function () {
        Route::get('/', 'QcController@index');
        Route::get('/get-test/{analyzer_id}', 'QcController@getTest');
        Route::get('/get-qc-id/{month}/{year}/{analyzer}/{test}', 'QcController@getQcId');
        Route::get('/get-reference-1/{id}', 'QcController@getReferenceData1');
        Route::get('/get-reference-2/{id}', 'QcController@getReferenceData2');
        Route::get('/get-reference-3/{id}', 'QcController@getReferenceData3');
        Route::post('/add-reference-1', 'QcController@addReference1');
        Route::post('/add-reference-2', 'QcController@addReference2');
        Route::post('/add-reference-3', 'QcController@addReference3');

        Route::get('/datatable-qc-data/{id}/{startDate?}/{endDate?}', 'QcController@datatableQcData');

        Route::post('/create-qc-data-level-1', 'QcController@createQcDataLevel1');
        Route::post('/create-qc-data-level-2', 'QcController@createQcDataLevel2');
        Route::post('/create-qc-data-level-3', 'QcController@createQcDataLevel3');
        Route::get('/edit-qc-data-1/{id}', 'QcController@editQcDataLevel1');
        Route::get('/edit-qc-data-2/{id}', 'QcController@editQcDataLevel2');
        Route::get('/edit-qc-data-3/{id}', 'QcController@editQcDataLevel3');
        Route::put('/update-qc-data-level-1', 'QcController@updateQcDataLevel1');
        Route::put('/update-qc-data-level-2', 'QcController@updateQcDataLevel2');
        Route::put('/update-qc-data-level-3', 'QcController@updateQcDataLevel3');
        Route::delete('/delete-qc-data-level-1/{id}/{qc_id}', 'QcController@deleteQcDataLevel1');
        Route::delete('/delete-qc-data-level-2/{id}/{qc_id}', 'QcController@deleteQcDataLevel2');
        Route::delete('/delete-qc-data-level-3/{id}/{qc_id}', 'QcController@deleteQcDataLevel3');

        Route::get('/check-position-qc-data-level-1/{qc_id}/{qc_value}', 'QcController@checkPositionQCData1');
        Route::get('/check-position-qc-data-level-2/{qc_id}/{qc_value}', 'QcController@checkPositionQCData2');
        Route::get('/check-position-qc-data-level-3/{qc_id}/{qc_value}', 'QcController@checkPositionQCData3');
        Route::get('/check-position-qc-data-level-1-edit/{qc_id}/{qc_value}', 'QcController@checkPositionQCDataEdit1');
        Route::get('/check-position-qc-data-level-2-edit/{qc_id}/{qc_value}', 'QcController@checkPositionQCDataEdit2');
        Route::get('/check-position-qc-data-level-3-edit/{qc_id}/{qc_value}', 'QcController@checkPositionQCDataEdit3');

        Route::get('/load-graph-data/{qc_id1}/{qc_id2}/{qc_id3}/{startDate?}/{endDate?}', 'QcController@loadGraphData');

        Route::get('/load-graph-data-level-1/{qc_id}/{startDate?}/{endDate?}', 'QcController@loadGraphData1');
        Route::get('/load-graph-data-level-2/{qc_id}', 'QcController@loadGraphData2');

        Route::get('/print-qc-data-level-1/{qc_id}/{startDate?}/{endDate?}', 'QcController@printQcData1');
        Route::get('/print-qc-data-level-2/{qc_id}/{startDate?}/{endDate?}', 'QcController@printQcData2');
        Route::get('/print-qc-data-level-3/{qc_id}/{startDate?}/{endDate?}', 'QcController@printQcData3');

        Route::get('/export-qc-data', 'QcController@exportQcData');
    });

    // Viewer
    Route::get('/lica-viewer', 'ViewerController@index')->name('lica-viewer');
    Route::get('/lica-viewer/datatable/{startDate?}/{endDate?}/{groupId?}', 'ViewerController@datatable');
    Route::get('/lica-viewer/datatable-test/{transactionId}', 'ViewerController@datatableTest');

    // user management
    Route::prefix('user-management')->group(function () {
        Route::get('/', 'UserManagementController@index')->name('user-management');
        Route::get('datatable', 'UserManagementController@datatable');
        Route::post('add', 'UserManagementController@create');
        Route::get('edit/{id}', 'UserManagementController@edit');
        Route::put('update', 'UserManagementController@update');
        Route::delete('delete/{id}', 'UserManagementController@delete');
    });
});

//utility
Route::get('/run-migrations', function () {
    return Artisan::call('migrate', ["--force" => true]);
});
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('route:clear');
    // return what you want
});

// interfacing
Route::post('/interfacing-insert', 'InterfacingController@insert');
Route::get('/print-barcode', 'PrinterConfigController@index');

// API
// Route::post('api/insert_patient2', 'ApiController@insertPatient2');
Route::post('api/insert_patient', 'ApiController@insertPatient');
Route::put('api/update_patient', 'ApiController@updatePatient');
Route::get('api/get_result/{id}', 'ApiController@getResult');
Route::get('api/send_result/{id}', 'ApiController@sendResult');
