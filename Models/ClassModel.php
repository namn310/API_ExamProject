<?php
include_once __DIR__ . '/../Models/BaseModel.php';
include_once __DIR__ . "/../Connection/Connection.php";
require 'vendor/autoload.php';
class ClassModel extends BaseModel
{
    protected $table;
    protected $ClassModel;
    public function __construct()
    {
        $this->table = 'classes';
        $this->ClassModel = new BaseModel($this->table);
        $this->conn = ConnectionDB::GetConnect();
    }
    public function index()
    {
        return $this->ClassModel->index();
    }
    public function getAllClassModel()
    {
        try {
            $query = $this->conn->query("select * from $this->table ");
            return ['data' => $query->fetchAll()];
        } catch (Throwable $e) {
            return null;
        }
    }
    public function createClassModel($data)
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
            echo json_encode(['status' => 'success', 'id' => $lastInsertId, 'class' => $data['class'], 'description' => $data['description']]);
        } catch (Throwable $e) {
            // nếu có lỗi thì hoàn tác lại query trên
            $this->conn->rollBack();
            // return false;
            echo json_encode(['status' => "error"]);
            // throw $e;
        }
    }
    public function deleteClassModel($id)
    {
        return $this->ClassModel->delete($id);
    }
    public function updateClassModel($data, $id)
    {
        try {
            $string = "";
            $columns = implode(",", array_keys($data));
            $columns_set_name = explode(',', $columns);
            foreach ($columns_set_name as $row) {
                $string .= $row . '=:' . $row . ',';
            }
            $setClause = rtrim($string, ",");
            // ví dụ chuỗi string sẽ có dạng name=:name,....
            // echo $setClause;
            $this->conn->beginTransaction();
            $query = $this->conn->prepare("update $this->table set $setClause where id=:id");
            $arrayId = ['id' => $id];
            //merge mảng để execute query
            $arrayData = array_merge($data, $arrayId);
            $query->execute($arrayData);
            $lastInsertId = $this->conn->lastInsertId();
            $this->conn->commit();
            echo json_encode(['status' => 'success', 'id' => $lastInsertId, 'class' => $data['class'], 'description' => $data['description']]);
        } catch (Throwable $e) {
            $this->conn->rollBack();
            echo json_encode(['status' => 'error']);
        }
    }
}
