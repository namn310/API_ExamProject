<?php
include_once __DIR__ . '/../Models/BaseModel.php';
include_once __DIR__ . '/../Models/QuestionModel.php';
class QuestionsController
{
    private $ExamModel;
    private $table;
    private $QuestionModel;
    public function __construct()
    {
        $this->table = 'questions';
        $this->QuestionModel = new QuestionModel();
        // $this->ExamModel=new BaseModel($this->table);
    }
    public function index()
    {
        $result = $this->QuestionModel->index();
        echo json_encode(['question' => $result]);
    }
    public function detail($id)
    {
        $result = $this->QuestionModel->read($id);
        echo json_encode(['data' => $result]);
    }
    public function create()
    {
        $data2 = json_decode(file_get_contents("php://input"), true);
        $this->QuestionModel->create($data2);
        // if ($this->QuestionModel->create($data) == false) {
        //     echo json_encode(['message' => "Có lỗi xảy ra !"]);
        // } else {
        //     echo json_encode(['message' => "Tạo mới bài thi thành công !"]);
        // }
    }
    public function getExam($id)
    {
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu bài thi không tồn tại !']);
        } else {
            try {
                $this->QuestionModel->read($id);
            } catch (Throwable $e) {
                echo json_encode(['message' => 'Có lỗi xảy ra !']);
            }
            echo json_encode(['message' => 'Lấy thông tin bài thi thành công']);
        }
    }
    public function update($id)
    {
        $data2 = json_decode(file_get_contents("php://input"), true);
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu bài thi không tồn tại !']);
        } else {
            // $this->QuestionModel->update($data2, $id);
            if ($this->QuestionModel->update($data2, $id) == false) {
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
            // $this->QuestionModel->delete($id);
            if ($this->QuestionModel->delete($id) == false) {
                echo json_encode(['message' => 'Có lỗi xảy ra !']);
            } else {
                echo json_encode(['message' => 'Xóa bài thi thành công !']);
            }
        }
    }
    public function getUser()
    {
       return $this->QuestionModel->getUserCreate();
    }
}
