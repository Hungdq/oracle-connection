<?php

// Create connection to Oracle, change HOST IP and SID string!
$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 129.154.75.30)(PORT = 1521)))(CONNECT_DATA=(SID=ORCL)))";
// Enter here your username (DBUSER) and password!
$conn = oci_connect("SYSTEM", "XQuKfZZF8_",$db);
if (!$conn) {
    $m = oci_error();
    echo $m['message']. PHP_EOL;
    exit;
}
else {
//    print "Oracle database connection online". PHP_EOL;
    $stid = oci_parse($conn, 'SELECT * FROM HUNGDQ');
    oci_execute($stid);
    $row = oci_fetch_array($stid);
    var_dump($row);
}
die;
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

Route::get('/', function () {
    return view('welcome');
});

Route::resource('employees', 'EmployeeController', ['only'=> ['index','create','store','destroy','update']]);
Route::get('/employees/{filter}/{value}','EmployeeController@search');

Route::get('/employees/client', function () {
    return view('employees');
});

Route::get('/oracle', function() {
//    phpinfo();die;
    $conn = oci_connect('SYSTEM', 'XQuKfZZF8_', 'Database20181106074108:1521/PDB1.611795443.oraclecloud.internal');
    if (!$conn) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }
});
