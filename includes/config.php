<?php session_start();
error_reporting(1);

if ($_SERVER['HTTP_HOST'] != 'localhost'):

    define('BASE_URL', "http://kamrankazmi.com/fabian_api/");
    define('APP_TITLE', 'Fabian Co.');
    define("DB_SERVER", "localhost");
    define("DB_USER", "kkdomain");
    define("DB_PASS", "y3V#vBQ&%V+h");
    define("DB_NAME", "fabian_api");
else:
    define('BASE_URL', "http://localhost/fabian_api/");
    define('APP_TITLE', 'Fabian Co.');
    define("DB_SERVER", "localhost");
    define("DB_USER", "root");
    define("DB_PASS", "");
    define("DB_NAME", "fabian_api");
endif;

define("API_PUBLIC_KEY", 'pk_test_3lHF0OKkRbbKyCvv5frF9iqi');
define("API_SECRET_KEY", 'sk_test_YW6ZE4skcfZkjv23ksT69IdI');
define("ADMIN_EMAIL", "django.is.freeman@gmail.com");

$class = "index";       // default
$method = "welcome";    // default

date_default_timezone_set('Europe/Berlin');
header('Content-Type: application/json');
header("access-control-allow-origin: *");

if (isset($_REQUEST['class']) && isset($_REQUEST['method'])):
    $class = $_REQUEST['class'];
    $method = $_REQUEST['method'];
endif;
?>
