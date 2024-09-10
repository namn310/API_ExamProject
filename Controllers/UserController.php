<?php
include_once __DIR__ . '/../Models/BaseModel.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController
{
    private $UserModel;
    private $table;
    public function __construct()
    {
        $this->table = 'users';
        $this->UserModel = new BaseModel($this->table);
    }
    public function index()
    {
        $result = $this->UserModel->index();
        echo json_encode(['data' => $result]);
    }
    public function detail($id)
    {
        $result = $this->UserModel->read($id);
        echo json_encode(['data' => $result]);
    }
    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);
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
        // if ($this->UserModel->create($data) == false) {
        //     echo json_encode(['message' => "Có lỗi xảy ra !"]);
        // } else {
        //     echo json_encode(['message' => "Tạo mới nguời dùng thành công !"]);
        // }
    }
    public function getExam($id)
    {
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu người dùng không tồn tại !']);
        } else {
            try {
                $this->UserModel->read($id);
            } catch (Throwable $e) {
                echo json_encode(['message' => 'Có lỗi xảy ra !']);
            }
            echo json_encode(['message' => 'Lấy thông tin người dùng thành công']);
        }
    }
    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu người dùng không tồn tại !']);
        } else {
            if ($this->UserModel->update($data, $id) == false) {
                echo json_encode(['message' => 'Cập nhật người dùng không thành công !']);
            } else {
                echo json_encode(['message' => 'Cập nhật thông tin người dùng thành công !']);
            }
        }
    }

    public function delete($id)
    {
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu người dùng không tồn tại !']);
        } else {
            if ($this->UserModel->delete($id) == false) {
                echo json_encode(['message' => 'Có lỗi xảy ra !']);
            } else {
                echo json_encode(['message' => 'Xóa thông tin người dùng thành công !']);
            }
        }
    }
    public function Login()
    {
        $conn = Connection::GetConnect();
        $key = getenv('KEY');
        $data = json_decode(file_get_contents("php://input"), true);
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
                    'jwt' => $jwt,
                ]);
            } else {
                echo json_encode(['message' => 'Đăng nhập thất bại ! Tài khoản hoặc mật khẩu không chính xác']);
            }
        } catch (Throwable $e) {
            echo json_encode(['message' => $e]);
        }
    }
    public function checkJWT()
    {
        $this->UserModel->checkToken();
    }
}
