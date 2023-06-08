<?php

namespace App\Http\Controllers;

use Illuminate\http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use File;


class PrinterConfigController extends Controller
{
    public function index(){

        $width = 50; //mm
        $height = 20; //mm
        $resolution = 8;
        $padleft = $width * $resolution;
        $ip_address = "shiro4";
        $printer_name = "zd230-1";

        $name = "John Doe";
        $medrec = "RM0012345";

        $nomor = "220907002";
        $base_path =  base_path();
        $dataToWrite = "";
        $dataToWrite = $dataToWrite . "^XA";
        $dataToWrite = $dataToWrite . "^CFA,30";
        $dataToWrite = $dataToWrite . "^FB".$padleft.",1,0,C"; //center
        $dataToWrite = $dataToWrite . "^FO0,15^FD".$name."\&^FS";
        $dataToWrite = $dataToWrite . "^FB".$padleft.",1,0,C"; //center
        $dataToWrite = $dataToWrite . "^FO0,40^FD".$medrec."\&^FS";
        $dataToWrite = $dataToWrite . "^BY2,3,40";
        $dataToWrite = $dataToWrite . "^FO60,70^BC^FD".$nomor."^FS";
        $dataToWrite = $dataToWrite . "^XZ";
        $dataToWrite = urlencode($dataToWrite);
        $test = 'http://labelary.com/viewer.html?density='.$resolution.'&width='.$width.'&height='.$height.'&units=mm&index=0&zpl='.$dataToWrite;
        echo $test;
        // $file = time() .rand(). '_print.zpl';
        // $destinationPath=base_path()."/temp_print/";
        // if (!is_dir($destinationPath)) {  mkdir($destinationPath,0777,true);  }
        // File::put($destinationPath.$file,$dataToWrite);
        // $print_file = $destinationPath.$file;
        // copy($print_file, "//".$ip_address. "/".$printer_name);

    }
}