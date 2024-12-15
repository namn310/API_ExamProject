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
    // lấy ảnh của câu hỏi
    public function getImageAnswer($id)
    {
        $result = $this->QuestionModel->getImageAnswerModel($id);
        echo json_encode(['data' => $result]);
    }
    public function create()
    {
        // $checkToken = CheckToken::checkToken();
        // if ($checkToken) {
        $data2 = json_decode(file_get_contents("php://input"), true);
        $this->QuestionModel->create($data2);
        // } else {
        //     echo json_encode(['message' => "Token không hợp lệ"]);
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
        // $checkToken = CheckToken::checkToken();
        // if ($checkToken === true) {
        $data2 = json_decode(file_get_contents("php://input"), true);
        // kiểm tra dữ liệu tránh truyền script vào input
        // foreach ($data2 as $key => $value) {
        //     $data2[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        // }
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu bài thi không tồn tại !']);
        } else {
            // $this->QuestionModel->update($data2, $id);
            if ($this->QuestionModel->update($data2, $id) == false) {
                echo json_encode(['message' => 'Cập nhật câu hỏi không thành công !']);
            } else {
                echo json_encode(['message' => 'Cập nhật thông tin câu hỏi thành công !']);
            }
        }
        // } else {
        //     echo json_encode(['message' => "Token không hợp lệ"]);
        // }
    }

    public function delete($id)
    {
        // $checkToken = CheckToken::checkToken();
        // if ($checkToken) {
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
        // } else {
        //     echo json_encode(['message' => "Token không hợp lệ"]);
        // }
    }
    public function getUser()
    {
        return $this->QuestionModel->getUserCreate();
    }
    // thêm câu hỏi vào bài kiểm tra tùy chọn   
    public function AddQuestionIntoExamOptionController($id)
    {
        try {
            $result = $this->QuestionModel->AddQuestionIntoExamOptionModel($id);
            if ($result == true) {
                echo json_encode(['result' => "success"]);
            } else {
                echo json_encode(['result' => "error"]);
            }
        } catch (Throwable $e) {
            echo json_encode(['result' => "error"]);
        }
    }
    public function deleteQuestionInExamController($idQues, $idExam)
    {
        try {
            // echo json_encode(['idQues' => $idQues, 'idExam' => $idExam]);
            $result = $this->QuestionModel->deleteQuestionInExamModel($idQues, $idExam);
            if ($result == false) {
                echo json_encode(['result' => 'false']);
            } else {
                echo json_encode(['result' => 'true']);
            }
        } catch (Throwable $e) {
            echo json_encode(['result' => 'false']);
        }
    }
}
