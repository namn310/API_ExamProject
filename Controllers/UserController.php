<?php
// include_once __DIR__ . '/../Models/BaseModel.php';
include_once __DIR__ . '/../Models/UserModel.php';
require __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use FTP\Connection;

// use Google_Service_Oauth2;

class UserController
{
    private $UserModel;
    // private $table;
    public function __construct()
    {
        // $this->table = 'users';
        // $this->UserModel = new BaseModel($this->table);
        $this->UserModel = new UserModel();
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
        // kiểm tra dữ liệu tránh truyền script vào input
        // foreach ($data as $key => $value) {
        //     $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        // }
        $this->UserModel->createUser($data);
    }
    public function update($id)
    {
        // $checkToken = CheckToken::checkToken();
        // if ($checkToken === true) {
        $data = json_decode(file_get_contents("php://input"), true);
        // kiểm tra dữ liệu tránh truyền script vào input
        // foreach ($data as $key => $value) {
        //     $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        // }
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu người dùng không tồn tại !']);
        } else {
            if ($this->UserModel->update($data, $id) == false) {
                echo json_encode(['message' => 'Cập nhật người dùng không thành công !']);
            } else {
                echo json_encode(['message' => 'Cập nhật thông tin người dùng thành công !']);
            }
        }
        // } else {
        //     echo json_encode(['message' => "Token không hợp lệ"]);
        // }
    }
    public function updatePassAdmin($id)
    {
        // $checkToken = CheckToken::checkToken();
        // if ($checkToken === true) {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($id == 0) {
            echo json_encode('Dữ liệu người dùng không tồn tại !');
        } else {
            $this->UserModel->updatePassAdmin($data, $id);
        }
        // } else {
        //     echo json_encode(['message' => "Token không hợp lệ"]);
        // }
    }
    public function delete($id)
    {
        // $checkToken = CheckToken::checkToken();
        // if ($checkToken === true) {
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu người dùng không tồn tại !']);
        } else {
            if ($this->UserModel->delete($id) == false) {
                echo json_encode(['message' => 'Có lỗi xảy ra !']);
            } else {
                echo json_encode(['message' => 'Xóa thông tin người dùng thành công !']);
            }
        }
        // } else {
        //     echo json_encode(['message' => "Token không hợp lệ"]);
        // }
    }
    public function Login()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $this->UserModel->LoginModel($data);
    }
    public function resetPassword()
    {
        // $checkToken = CheckToken::checkToken();
        // if ($checkToken === true) {
        $data = json_decode(file_get_contents("php://input"), true);
        // kiểm tra dữ liệu tránh truyền script vào input
        // foreach ($data as $key => $value) {
        //     $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        // }
        $this->UserModel->resetPasswordModel($data);
        // } else {
        //     echo json_encode(['message' => "Token không hợp lệ"]);
        // }
    }
    public function forgotPassword()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $this->UserModel->forgotPasswordModel($data);
    }
    public function ResetPasswordForget()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $this->UserModel->ResetPasswordForgetModel($data);
    }
    // public function checkJWT()   
    // {
    //     // $this->UserModel->checkToken();
    // }
    public function LoginGoogle()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $this->UserModel->loginGoogleModel($data);
    }
}
