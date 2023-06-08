<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;
use Auth;
use PDF;

class PrinterSettingController extends Controller
{
    const STATUS = 2;
    public function index()
    {
        $data['title'] = 'Printer Setting';

        return view('printer_setting.index', $data);
    }

    public function datatable()
    {
        
        $model = \App\PrinterSetting::select('*');
        
        return DataTables::of($model)
        ->addIndexColumn()
        ->escapeColumns([])
        ->make(true);
    }
    
}
