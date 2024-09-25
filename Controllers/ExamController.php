<?php
// include_once __DIR__ . '/../Models/BaseModel.php';
include_once __DIR__ . '/../Models/ExamModel.php';
class ExamsController
{
    private $ExamModel;
    // private $table;
    public function __construct()
    {
        // $this->table = 'exams';
        // $this->ExamModel = new BaseModel($this->table);
        $this->ExamModel = new ExamModel();
    }
    public function index()
    {
        $result = $this->ExamModel->index();
        echo json_encode(['data' => $result]);
    }
    public function detail($id)
    {
        $result = $this->ExamModel->read($id);
        echo json_encode(['data' => $result]);
    }
    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        // $this->ExamModel->createExam($data);
        if ($this->ExamModel->createExam($data) == false) {
            echo json_encode(['message' => "Có lỗi xảy ra !"]);
        } else {
            echo json_encode(['message' => "Tạo mới bài thi thành công !"]);
        }
    }
    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu bài thi không tồn tại !']);
        } else {
            if ($this->ExamModel->update($data, $id) == false) {
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
            if ($this->ExamModel->delete($id) == false) {
                echo json_encode(['message' => 'Có lỗi xảy ra !']);
            } else {
                echo json_encode(['message' => 'Xóa bài thi thành công !']);
            }
        }
    }

    public function getQuestionsExam($id)
    {
        $result = $this->ExamModel->readQuestionExam($id);
        echo json_encode(['data' => $result]);
    }

    public function getCategoryExam($id)
    {
        $result = $this->ExamModel->readCategoryExam($id);
        echo json_encode(['data' => $result]);
    }
}
