<?php
header("Content-Type: application/json");

include_once "Models/BaseModel.php";
include_once __DIR__ . "../Connection/Connection.php";
Connection::GetConnect();
// echo getenv('KEY');


$person = array(
    "name" => "John",
    "age" => 25,
    "city" => [
        ['id'=>1,'name'=>2],
        ['id'=>2,'name'=>3]
    ]
);
// Loại bỏ phần tử cuối cùng
print_r(array_pop($person));
echo ('<br/>');
// In mảng sau khi loại bỏ phần tử cuối
print_r($person);
