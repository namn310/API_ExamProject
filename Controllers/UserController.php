<?php
include_once __DIR__ . '/../Models/BaseModel.php';
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
        if ($this->UserModel->create($data) == false) {
            echo json_encode(['message' => "Có lỗi xảy ra !"]);
        } else {
            echo json_encode(['message' => "Tạo mới nguời dùng thành công !"]);
        }
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
}