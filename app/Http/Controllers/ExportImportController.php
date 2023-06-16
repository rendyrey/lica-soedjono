<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\PatientsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Patient;
use DB;

class ExportImportController extends Controller
{
    public function patientsExport()
    {
        return Excel::download(new PatientsExport, 'Patients.xlsx');
    }

    public function patientsImport(Request $request)
    {
        try{
            $data = Excel::toArray('', $request->file('file_excel'))[0];
            DB::beginTransaction();
            foreach(array_slice($data, 1) as $row) {
                if ($row[1] == NULL || $row[1] == '') {
                    continue;
                }
                $patient = Patient::where('medrec', $row[1])->first();
                if ($patient) {
                    $patient->medrec = $row[1];
                    $patient->name = $row[2];
                    $patient->gender = $row[3];
                    $patient->birthdate = $row[4];
                    $patient->address = $row[5];
                    $patient->phone = $row[6];
                    $patient->email = $row[7];
                    $patient->save();
                } else {
                    $data['medrec'] = $row[1];
                    $data['name'] = $row[2];
                    $data['gender'] = $row[3];
                    $data['birthdate'] = $row[4];
                    $data['address'] = $row[5];
                    $data['phone'] = $row[6];
                    $data['email'] = $row[7];
                    Patient::create($data);
                }
            }

            DB::commit();
            return redirect('master/patient')->with('message','Successfully Import Patient Data')->with('panel','success');
        } catch (\Exception $e){
            DB::rollback();
            return redirect('master/patient')->with('message', "Error import patient data, please pay attention to the format, you can export first the patient data")->with('panel','danger');
        }
    }
}