<?php
include_once __DIR__ . '/../Models/BaseModel.php';
class ResultController
{
    private $ResultModel;
    private $table;
    public function __construct()
    {
        $this->table = 'results';
        $this->ResultModel = new BaseModel($this->table);
    }
    public function index()
    {
        $result = $this->ResultModel->index();
        echo json_encode(['data' => $result]);
    }
    public function detail($id)
    {
        $result = $this->ResultModel->read($id);
        echo json_encode(['data' => $result]);
    }
    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $result = $this->ResultModel->createResult($data);
        if ($result['success'] == false) {
            echo json_encode(['message' => $result['message']]);
        } else {
            echo json_encode([
                'message' => 'Tạo mới bài thi thành công!',
                'id' => $result['id'] // ID của bản ghi mới tạo
            ]);
        }
    }
    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu bài thi không tồn tại !']);
        } else {
            if ($this->ResultModel->update($data, $id) == false) {
                echo json_encode(['message' => 'Cập nhật bài thi không thành công !']);
            } else {
                echo json_encode(['message' => 'Cập nhật thông tin bài thi thành công !']);
            }
        }
    }

    public function delete($id)
    {
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu bài thi không tồn tại !']);
        } else {
            if ($this->ResultModel->delete($id) == false) {
                echo json_encode(['message' => 'Có lỗi xảy ra !']);
            } else {
                echo json_encode(['message' => 'Xóa bài thi thành công !']);
            }
        }
    }
}
