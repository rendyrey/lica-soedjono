// if ($recordData->isEmpty()) {
// // print_r($recordData);
// $mean_value = $qc_value;
// $position = ($qc_value - $target_value) / $sd;

// // $fVariance = 0.0;
// // $fVariance += pow($qc_value - $mean_value, 2);



// // $sd = (float) sqrt($fVariance) / sqrt($amountData - 1);
// // $sd_value = round($sd, 1);



// // echo 'Nilai Bawah : ' . $low_value . '<br>';
// // echo 'Nilai Atas : ' . $high_value . '<br>';
// // echo 'Nilai Target : ' . $target_value . '<br><br>';
// // echo 'Nilai Mean : ' . $mean_value . '<br>';
// // echo 'Nilai SD : ' . $sd_value . '<br>';

// return response()->json($position);
// } else {
// $position = ($qc_value - $target_value) / $sd;
// echo $position;
// // $mean_query = DB::table('qc_datas')
// // ->avg('data');
// // $mean_value = $mean_query;

// // $fVariance = 0.0;
// // foreach ($recordData as $key => $value) {

// // $fVariance += pow($value->qc_value - $mean_value, 2);
// // }

// // $sd = (float) sqrt($fVariance) / sqrt($amountData - 1);
// // $sd_value = round($sd, 1);

// // // $cv = $sd_value / $mean_value * 100;
// // // $cv_value = round($cv, 1);

// // echo 'Nilai Bawah : ' . $low_value . '<br>';
// // echo 'Nilai Atas : ' . $high_value . '<br>';
// // echo 'Nilai Target : ' . $target_value . '<br><br>';
// // echo 'Nilai Mean : ' . $mean_value . '<br>';
// // echo 'Nilai SD : ' . $sd_value . '<br>';
// // echo 'Nilai CV : ' . $cv . '<br>';
// }