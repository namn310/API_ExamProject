<?php
include_once __DIR__ . '/../Models/ClassModel.php';
class ClassController
{
    private $ClassModel;
    // private $table;
    public function __construct()
    {
        // $this->table = 'category_exams';
        $this->ClassModel = new ClassModel();
    }
    public function index()
    {
        $result = $this->ClassModel->index();
        echo json_encode($result);
    }
    public function getAllClassController()
    {
        try {
            $result = $this->ClassModel->getAllClassModel();
            echo json_encode($result);
        } catch (Throwable $e) {
            echo json_encode(null);
        }
    }
    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        // kiểm tra dữ liệu tránh truyền script vào input
        $this->ClassModel->createClassModel($data);
    }
    public function delete($id)
    {
        $result  = $this->ClassModel->deleteClassModel($id);
        if ($result == true) {
            echo json_encode(['status' => "success"]);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $this->ClassModel->updateClassModel($data, $id);
    }
}
