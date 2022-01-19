<?php
$host = "std-mysql.ist.mospolytech.ru";
$name = "std_1603_kursach";
$password = "Dbhnefkbpfwbz2";
$db = "std_1603_kursach";
$connect = mysqli_connect($host, $name, $password, $db);

if ($connect == false)
{
    echo "Ошибка подключения";
}
?>