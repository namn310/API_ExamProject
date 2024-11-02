<?php
require __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . "/../Connection/Connection.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CheckToken
{
    // lấy giá trị authorization từ header
    public static function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Check cho các server khác nhau
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    // lấy token từ header
    public static function getToken()
    {
        $header = CheckToken::getAuthorizationHeader();
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                return $matches[1];
                // echo json_encode($matches[1]);
            }
        } else {
            return null;
        }
    }
    // kiểm tra token
    public static function checkToken()
    {
        $conn = Connection::GetConnect();
        $token = CheckToken::getToken();
        // echo json_encode($token);
        Connection::loadEnv(__DIR__ . '/../.env');
        $key = getenv('KEY');
        // $decode = JWT::decode($token, new Key($key, 'HS256'));
        // echo json_encode($decode);
        if (!empty($token)) {
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            $role = $decode->data->role;
            $id = $decode->data->id;
            $name = $decode->data->name;
            $email = $decode->data->email;
            $query = $conn->prepare("select id from users where id=:id and email=:email and role=:role and name=:name limit 1");
            $query->execute(['id' => $id, 'email' => $email, 'role' => $role, 'name' => $name]);
            if ($query->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
// CheckToken::checkToken();
