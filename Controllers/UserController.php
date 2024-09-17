<?php
// include_once __DIR__ . '/../Models/BaseModel.php';
include_once __DIR__ . '/../Models/UserModel.php';

require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
        $this->UserModel->createUser($data);
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
        $data = json_decode(file_get_contents("php://input"), true);
        $this->UserModel->LoginModel($data);
    }
    public function checkJWT()
    {
        // $this->UserModel->checkToken();
    }
    public function logOut() {}
}
