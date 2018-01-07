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
require('../vendor/autoload.php');
require( 'ssp.class.php' );

// DB table to use
$table = 'persons';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case object
// parameter names
$columns = array(
    array( 'db' => 'id', 'dt' => 0 ),
    array( 'db' => 'firstName', 'dt' => 1 ),
    array( 'db' => 'lastName',  'dt' => 2 ),
);

$dbopts = parse_url(getenv('JAWSDB_URL'));

// SQL server connection information
$sql_details = array(
    'user' => $dbopts['user'],
    'pass' => $dbopts['pass'],
    'db'   => ltrim($dbopts['path'],'/'),
    'host' => $dbopts['host']
);

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->get('/cowsay', function() use($app) {
  $app['monolog']->addDebug('cowsay');
  return "<pre>".\Cowsayphp\Cow::say("Cool beans")."</pre>";
});

$app->post('/persons', function() use($app) {
  // Fix for Angular, based on:
  //   Angular HTTP post to PHP and undefined
  //   https://stackoverflow.com/questions/15485354/angular-http-post-to-php-and-undefined/#24140930
  if (empty($_POST))
      $_POST = json_decode(file_get_contents('php://input'), true);

  /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  * If you just want to use the basic configuration for DataTables with PHP
  * server-side, there is no need to edit below this line.
  */
  return json_encode(
      SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns )
  );
});

$app->run();
