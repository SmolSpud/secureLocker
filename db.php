<?php
$serverName = "localhost\\SQLEXPRESS";
$connectionOptions = [
    "Database" => "locker_system"
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
