<?php
include_once "Models/BaseModel.php";
include_once "Connection/Connection.php";
$conn = Connection::GetConnect();
$table = "vouchers";
$modal = new BaseModel($table);
$data = [
    // 'id' => '9',
    'ma' => 'nguyenphuongnam333333333333',
    'count' => 123,
    'dk_hoadon' => 0,
    'dk_soluong' => 0,
    'discount' => 12,
    'status' => 0,
    'description' => "chưa dùng được",
    'time_start' => "2024-08-26",
    'time_end' => "2024-08-30",
    'created_at' => '2024-08-30'
];
$modal->update($data, 13);
