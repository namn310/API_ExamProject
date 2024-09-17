<?php
include_once __DIR__ . '/../Models/BaseModel.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserModel extends BaseModel
{
    protected $table;
    protected $UserModel;
    public function __construct()
    {
        $this->table = 'users';
        $this->UserModel = new BaseModel($this->table);
    }
    public function index()
    {
        return $this->UserModel->index();
    }
    // public function create($data)
    // {
    //     // return $this->UserModel->create($data);
    //     // $data = json_decode(file_get_contents("php://input"), true);
    //     print_r($data);
    //     $conn = Connection::GetConnect();
    //     // $name = $data['name'];
    //     $email = $data['email'];
    //     $pass = md5($data['password']);
    //     // $role = $data['role'];
    //     $data['password'] = md5($pass);
    //     print_r($data);
    //     // try {
    //     //     $query = $conn->prepare("select id from $this->table where email=:email");
    //     //     $query->execute(['email' => $email]);
    //     //     if ($query->rowCount() > 0) {
    //     //         echo json_encode(['message' => 'Email đã tồn tại']);
    //     //     } else {
    //     //         $this->UserModel->create($data);
    //     //         echo json_encode(['message' => 'Đăng ký tài khoản thành công']);
    //     //     }
    //     // } catch (Throwable $e) {
    //     //     echo json_encode(['message' => $e]);
    //     // }
    // }
    public function createUser($data)
    {
        $conn = Connection::GetConnect();
        $name = $data['name'];
        $email = $data['email'];
        $pass = md5($data['password']);
        $role = $data['role'];
        try {
            $query = $conn->prepare("select id from $this->table where email=:email");
            $query->execute(['email' => $email]);
            if ($query->rowCount() > 0) {
                echo json_encode(['message' => 'Email đã tồn tại']);
            } else {
                $query2 = $conn->prepare("insert into $this->table (name,password,email,role) values (:name,:pass,:email,:role)");
                $query2->execute(['name' => $name, 'pass' => $pass, 'email' => $email, 'role' => $role]);
                echo json_encode(['message' => 'Đăng ký tài khoản thành công']);
            }
        } catch (Throwable $e) {
            echo json_encode(['message' => $e]);
        }
    }
    public function read($id)
    {
        return $this->UserModel->read($id);
    }
    public function delete($id)
    {
        return $this->UserModel->delete($id);
    }
    public function update($data, $id)
    {
        $this->UserModel->update($data, $id);
    }
    public function getUserCreate()
    {
        try {
            $query = $this->conn->prepare("select name,id from users where role=:role");
            $query->execute(['role' => 'admin']);
        } catch (Throwable $e) {
            echo json_encode(['message' => "Lỗi" . $e]);
        }
        echo json_encode(['data' => $query->fetchAll()]);
    }
    public function LoginModel($data)
    {
        $key = getenv('KEY');
        // $data = json_decode(file_get_contents("php://input"), true);
        $conn = Connection::GetConnect();
        try {
            $email = $data['email'];
            $pass = md5($data['password']);
            $role = $data['role'];
            $query = $conn->prepare("select * from $this->table where email=:email and password=:password and role=:role LIMIT 1");
            $query->execute(['email' => $email, 'password' => $pass, 'role' => $role]);
            $user = $query->fetch(PDO::FETCH_ASSOC);
            if ($query->rowCount() > 0) {
                $timeCreate = time();
                $timeExpire = time() + 86400;
                if ($role === 'admin') {
                    $payload = [
                        'iat' => $timeCreate,
                        'exp' => $timeExpire,
                        'data' => [
                            'id' => $user['id'],
                            'email' => $user['email'],
                            'name' => $user['name'],
                            'role' => $user['role']
                        ]
                    ];
                    $jwt = JWT::encode($payload, $key, 'HS256');
                    echo json_encode([
                        'message' => 'Đăng nhập thành công !',
                        'jwtAdmin' => $jwt,
                    ]);
                }
                if ($role === 'student') {
                    $payload = [
                        'iat' => $timeCreate,
                        'exp' => $timeExpire,
                        'data' => [
                            'id' => $user['id'],
                            'email' => $user['email'],
                            'name' => $user['name'],
                            'role' => $user['role']
                        ]
                    ];
                    $jwt = JWT::encode($payload, $key, 'HS256');
                    echo json_encode([
                        'message' => 'Đăng nhập thành công !',
                        'jwtStudent' => $jwt,
                    ]);
                }
            } else {
                echo json_encode(['message' => 'Đăng nhập thất bại ! Tài khoản hoặc mật khẩu không chính xác']);
            }
        } catch (Throwable $e) {
            echo json_encode(['message' => "Có lỗi xảy ra ". $e]);
        }
    }
}
