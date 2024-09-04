<?php
header("Content-Type: application/json");

include_once "Models/BaseModel.php";

// // include_once "Connection/Connection.php";
// // $conn = Connection::GetConnect();
// // $table = "vouchers";
// $modal = new BaseModel($table);
// // $data = [
// //     // 'id' => '9',
// //     'ma' => 'nguyenphuongnam333333333333',
// //     'count' => 123,
// //     'dk_hoadon' => 0,
// //     'dk_soluong' => 0,
// //     'discount' => 12,
// //     'status' => 0,
// //     'description' => "chưa dùng được",
// //     'time_start' => "2024-08-26",
// //     'time_end' => "2024-08-30",
// //     'created_at' => '2024-08-30'
// // ];
// // $modal->update($data, 13);
// include_once  __DIR__ . '../Controllers/ExamController.php';
// $a=new ExamController();
// $a->index();
// print_r($a);
// $data = json_decode(file_get_contents("php://input"), true);
// lấy tên cột từ data;
$string= '{"class":11,"Subject":"Englishhhhh","title":"hsg","A":"1","B":"2","C":"3","D":"4","correctAns":"A","created_at":"2024-08-31 00:00:00","created_by":1}';
$data=json_decode($string,true);

$columns = implode(",", array_keys($data));
print_r($data);
// prepare giá trị truyền vào sql
// lấy giá trị từ data
$value = ":" . implode(",:", array_keys($data));
print_r($value);
