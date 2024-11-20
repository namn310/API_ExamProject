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
        // $checkToken = CheckToken::checkToken();
        // if ($checkToken === true) {
        $data = json_decode(file_get_contents("php://input"), true);
        // kiểm tra dữ liệu tránh truyền script vào input
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        // $this->ExamModel->createExam($data);
        if ($this->ExamModel->createExam($data) == false) {
            echo json_encode(['message' => "Có lỗi xảy ra !"]);
        } else {
            echo json_encode(['message' => "Tạo mới bài thi thành công !"]);
        }
        // } else {
        //     echo json_encode(['message' => "Token không hợp lệ"]);
        // }
    }
    public function update($id)
    {
        // $checkToken = CheckToken::checkToken();
        // if ($checkToken === true) {
        $data = json_decode(file_get_contents("php://input"), true);
        // kiểm tra dữ liệu tránh truyền script vào input
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu bài thi không tồn tại !']);
        } else {
            if ($this->ExamModel->update($data, $id) == false) {
                echo json_encode(['message' => 'Cập nhật bài thi không thành công !']);
            } else {
                echo json_encode(['message' => 'Cập nhật thông tin bài thi thành công !']);
            }
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
            echo json_encode(['message' => 'Dữ liệu bài thi không tồn tại !']);
        } else {
            if ($this->ExamModel->delete($id) == false) {
                echo json_encode(['message' => 'Có lỗi xảy ra !']);
            } else {
                echo json_encode(['message' => 'Xóa bài thi thành công !']);
            }
        }
        // } else {
        //     echo json_encode(['message' => "Token không hợp lệ"]);
        // }
    }

    public function getQuestionsExam($id)
    {
        $result = $this->ExamModel->readQuestionExam($id);
        echo json_encode(['data' => $result]);
    }
    public function getQuestionNoResultController($id)
    {
        $result = $this->ExamModel->getQuestionNoResult($id);
        echo json_encode(['data' => $result]);
    }
    public function getCategoryExam($id)
    {
        $result = $this->ExamModel->readCategoryExam($id);
        echo json_encode(['data' => $result]);
    }
    public function getExamByIdCat($id)
    {
        $result = $this->ExamModel->getExamByCatModel($id);
        echo json_encode(['data' => $result]);
    }
    // lấy số lượng người làm sai các câu hỏi trong bài kiểm tra
    public function getNumberDoWrong($id)
    {
        $result = $this->ExamModel->getNumberDoWrongModel($id);
        if ($result == null) {
            echo json_encode(null);
        } else {
            echo json_encode($result);
        }
    }
}
