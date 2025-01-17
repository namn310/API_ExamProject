<?php
include_once __DIR__ . '/../Models/BaseModel.php';
include_once __DIR__ . "/../Connection/Connection.php";
require 'vendor/autoload.php';
class CategoryExamModel extends BaseModel
{
    protected $table;
    protected $CategoryExamModel;
    public function __construct()
    {
        $this->table = 'category_exams';
        $this->CategoryExamModel = new BaseModel($this->table);
        $this->conn = ConnectionDB::GetConnect();
    }
    public function index()
    {
        return $this->CategoryExamModel->index();
    }
    public function getAllCategoryModel()
    {
        try {
            $query = $this->conn->query("select * from $this->table ");
            return ['data' => $query->fetchAll()];
        } catch (Throwable $e) {
            return null;
        }
    }
    public function create($data)
    {
        // kiểm tra dữ liệu tránh truyền script vào input
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        // $data = json_decode(file_get_contents("php://input"), true);
        // lấy tên cột từ data;
        $columns = implode(",", array_keys($data));
        // prepare giá trị truyền vào sql
        // lấy giá trị từ data
        $value = ":" . implode(",:", array_keys($data));
        try {
            // prepare query
            // dùng transaction để đảm bảo tính toàn vẹn của dữ liệu
            $this->conn->beginTransaction();
            $query = $this->conn->prepare("insert into $this->table ($columns) values ($value) ");
            $query->execute($data);
            $lastInsertId = $this->conn->lastInsertId();
            $this->conn->commit();
            echo json_encode(['status' => 'success', 'id' => $lastInsertId, 'title' => $data['title'], 'description' => $data['description']]);
        } catch (Throwable $e) {
            // nếu có lỗi thì hoàn tác lại query trên
            $this->conn->rollBack();
            // return false;
            echo json_encode(['status' => "error"]);
            // throw $e;
        }
    }
    public function read($id)
    {
        return $this->CategoryExamModel->read($id);
    }
    public function delete($id)
    {
        return $this->CategoryExamModel->delete($id);
    }
    public function deleteCategoryModel($id)
    {
        try {
            $this->conn->beginTransaction();
            $query = $this->conn->prepare("delete from category_exams where id=:id");
            $query->execute(['id' => $id]);
            $this->conn->commit();
            return true;
        } catch (Throwable $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    public function update($data, $id)
    {
        $this->CategoryExamModel->update($data, $id);
    }
    public function readQuestionCategory($id)
    {
        try {
            $query = $this->conn->prepare("SELECT exams.id, exams.title, exams.duration,exams.totalQuestion FROM exams
                INNER JOIN category_exams on exams.category = category_exams.id
                WHERE category_exams.id=:id");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
        return $query->fetchAll();
    }
    // public function getUserCreate()
    // {
    //     $this->CategoryExamModel->getUserCreate();
    // }
}
