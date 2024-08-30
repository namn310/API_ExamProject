<?php
class Connection
{
    public static function GetConnect()
    {
        $conn = new PDO("mysql:hostname=localhost;dbname=examproject", "root", "");
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $conn->exec("set names utf8");
        return $conn;
    }
}
