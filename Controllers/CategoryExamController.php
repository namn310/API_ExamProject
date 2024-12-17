<?php
include_once __DIR__ . '/../Models/CategoryExamModel.php';
class CategoryExamController
{
    private $CategoryModel;
    // private $table;
    public function __construct()
    {
        // $this->table = 'category_exams';
        $this->CategoryModel = new CategoryExamModel();
    }
    public function index()
    {
        $result = $this->CategoryModel->index();
        echo json_encode(['data' => $result]);
    }
    public function getAllCategoryController()
    {
        $result = $this->CategoryModel->getAllCategoryModel();
        echo json_encode(['data' => $result]);
    }
    public function detail($id)
    {
        $result = $this->CategoryModel->read($id);
        echo json_encode(['data' => $result]);
    }
    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        // kiểm tra dữ liệu tránh truyền script vào input
        $this->CategoryModel->create($data);
    }
    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        // kiểm tra dữ liệu tránh truyền script vào input
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        // $this->CategoryModel->update($data, $id);
        if ($id == 0) {
            echo json_encode(['message' => 'Dữ liệu danh mục bài thi không tồn tại !']);
        } else {
            if ($this->CategoryModel->update($data, $id) === false) {
                echo json_encode(['message' => 'Cập nhật danh mục bài thi không thành công !']);
            } else {
                echo json_encode(['message' => 'Cập nhật danh mục bài thi thành công !']);
            }
        }
    }

    public function delete($id)
    {
        // $checkToken = CheckToken::checkToken();
        // if ($checkToken === true) {
        if ($id == 0) {
            echo json_encode(['message' => 'Danh mục không tồn tại !']);
        } else {
            if ($this->CategoryModel->delete($id) == false) {
                echo json_encode(['message' => 'Có lỗi xảy ra !']);
            } else {
                echo json_encode(['message' => 'Xóa danh mục thành công !']);
            }
        }
        // } else {
        //     echo json_encode(['message' => "Token không hợp lệ"]);
        // }
    }
    public function deleteCategoryController($id)
    {
        if ($this->CategoryModel->deleteCategoryModel($id) == true) {
            echo json_encode(['message' => 'Xóa danh mục bài thi thành công !']);
        } else {
            echo json_encode(['message' => 'Xóa danh mục bài thi thất bại !']);
        }
    }
    public function getQuestionsCategory($id)
    {
        $result = $this->CategoryModel->readQuestionCategory($id);
        echo json_encode(['data' => $result]);
    }
}
