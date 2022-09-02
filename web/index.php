<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

// Disable CORS so we can call it from anywhere
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'persons';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case object
// parameter names
$columns = array(
    array( 'db' => 'id', 'dt' => 'id' ),
    array( 'db' => 'firstName', 'dt' => 'firstName' ),
    array( 'db' => 'lastName',  'dt' => 'lastName' ),
);

$db_user = getenv('DATABASE_USERNAME');
$db_pass = getenv('DATABASE_PASSWORD');
$db_host = getenv('DATABASE_HOST');
$db_name = getenv('DATABASE_NAME');

$sql_details = array(
    'user' => $db_user,
    'pass' => $db_pass,
    'db'   => ltrim($db_name,'/'),
    'host' => $db_host
);

// Fix for Angular, based on:
//   Angular HTTP post to PHP and undefined
//   https://stackoverflow.com/questions/15485354/angular-http-post-to-php-and-undefined/#24140930
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
    $_POST = json_decode(file_get_contents('php://input'), true);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( 'ssp.class.php' );

echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns )
);
