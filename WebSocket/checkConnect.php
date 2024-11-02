<?php
require __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/../Models/BaseModel.php';

class checkConnectSocket
{
    public static function checkConnect()
    {
        // $conn = Connection::GetConnect();
        // $query1 = $conn->query("select id,status from status_socket_server where id=1");
        // if (!$query1->rowCount() > 0) {
        //     $result = null;
        // } else {
        //     $status = $query1->fetch(PDO::FETCH_ASSOC);
        //     if (empty($status)) {
        //         // chưa tồn tại status
        //         $result = 0;
        //     } else {
        //         $result = $status['status'];
        //     }
        // }
        // return $result;
        try {
            $conn = ConnectionDB::GetConnect();
            $query1 = $conn->query("SELECT status FROM status_socket_server WHERE id=1");
            $status = $query1->fetch(PDO::FETCH_ASSOC);

            if ($status === false) {
                return null;  // Chưa tồn tại bản ghi nào
            }

            return (int)$status['status'];  // Trả về giá trị trạng thái
        } catch (PDOException $e) {
            // Xử lý lỗi kết nối hoặc truy vấn
            error_log("Database error: " . $e->getMessage());
            return null;
        }
    }
    public static function createConnectSocket()
    {
        // $conn = Connection::GetConnect();
        // $conn->query("insert into status_socket_server (status) values (1) ");
        // return true;
        try {
            $conn = ConnectionDB::GetConnect();
            $conn->query("INSERT INTO status_socket_server (id, status) VALUES (1, 1)");
            return true;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    public static function updateConnectSocket()
    {
        // $conn = Connection::GetConnect();
        // $conn->query("update status_socket_server set status = 1 where id=1 ");
        // return true;
        // try {
        //     $conn = ConnectionDB::GetConnect();
        //     $conn->query("UPDATE status_socket_server SET status = 1 WHERE id = 1");
        //     return true;
        // } catch (PDOException $e) {
        //     error_log("Database error: " . $e->getMessage());
        //     return false;
        // }
    }
}
