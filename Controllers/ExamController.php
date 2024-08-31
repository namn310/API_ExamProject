<?php
include_once __DIR__. '/../Models/BaseModel.php';
class ExamController
{
    private $ExamModel;
    private $table;
    public function __construct()
    {
        $this->table = 'questions';
        $this->ExamModel = new BaseModel($this->table);
    }
    public function index()
    {
        $result = $this->ExamModel->index();
        echo json_encode(['data' => $result]);
    }
    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($this->ExamModel->create($data) == false) {
            echo json_encode(['message' => "Có lỗi xảy ra !"]);
        } else {
            echo json_encode(['message' => "Tạo mới bài thi thành công !"]);
        }
    }
    public function getExam()
    {

        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu bài thi không tồn tại !']);
        } else {
            try {
                $this->ExamModel->read($id);
            } catch (Throwable) {
                echo json_encode(['message' => 'Có lỗi xảy ra !']);
            }
            echo json_encode(['message' => 'Lấy thông tin bài thi thành công']);
        }
    }
    public function update()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu bài thi không tồn tại !']);
        } else {
            if ($this->ExamModel->update($data, $id) == false) {
                echo json_encode(['message' => 'Bài thi không tồn tại !']);
            } else {
                echo json_encode(['message' => 'Cập nhật thông tin bài thi thành công !']);
            }
        }
    }

    public function delete()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu bài thi không tồn tại !']);
        } else {
            if ($this->ExamModel->delete($id) == false) {
                echo json_encode(['message' => 'Có lỗi xảy ra !']);
            } else {
                echo json_encode(['message' => 'Xóa bài thi thành công !']);
            }
        }
    }
}