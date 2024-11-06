<?php
// include_once __DIR__ . '/../Models/BaseModel.php';
include_once __DIR__ . '/../Models/CommentModel.php';
class CommentController
{
    private $CommentModel;
    public function __construct()
    {
        $this->CommentModel = new CommentModel();
    }
    public function index()
    {
        $result = $this->CommentModel->index();
        echo json_encode(['data' => $result]);
    }
    public function detail($id)
    {
        $result = $this->CommentModel->read($id);
        echo json_encode(['data' => $result]);
    }

    public function getCommentExam($id)
    {
        $result = $this->CommentModel->readCommentsExam($id);
        echo json_encode(['data' => $result]);
    }

    public function getChildCommentExam($id)
    {
        $result = $this->CommentModel->readChildCommentsExam($id);
        echo json_encode(['data' => $result]);
    }

    public function create()
    {
        // $checkToken = CheckToken::checkToken();
        // if ($checkToken === true) {
            $data = json_decode(file_get_contents("php://input"), true);
            // kiểm tra dữ liệu tránh truyền script vào input
            // foreach ($data as $key => $value) {
            //     $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            // }
            // $this->CommentModel->createExam($data);
            if ($this->CommentModel->create($data) == false) {
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
            // foreach ($data as $key => $value) {
            //     $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            // }
            if ($id == 0) {
                echo json_encode(['message' => 'Dữ liệu bài thi không tồn tại !']);
            } else {
                if ($this->CommentModel->update($data, $id) == false) {
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
                if ($this->CommentModel->delete($id) == false) {
                    echo json_encode(['message' => 'Có lỗi xảy ra !']);
                } else {
                    echo json_encode(['message' => 'Xóa bài thi thành công !']);
                }
            }
        // } else {
        //     echo json_encode(['message' => "Token không hợp lệ"]);
        // }
    }
}
