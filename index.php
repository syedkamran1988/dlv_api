<?php require __DIR__ . "/includes/config.php";
require __DIR__ . "/lib/init.php";
require __DIR__ . "/classes/database.php";
require __DIR__ . "/classes/common.php";

include __DIR__ . "/classes/" . $class . ".php";

if (class_exists($class)) {
    if (method_exists($class, $method)) {
        $obj = new $class();
        $obj->$method();
    }
}
?>

