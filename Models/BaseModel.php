<?php
include_once __DIR__ . "/../Connection/Connection.php";
require __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class BaseModel
{
    protected $table;
    protected $conn;
    public function __construct($table)
    {
        $this->table = $table;
        $conn = ConnectionDB::GetConnect();
        $this->conn = $conn;
    }
    // lấy dữ liệu
    public function index()
    {

        // phân trang
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        // nếu có trường category thì lấy còn không thì mặc định lấy category đầu tiên
        $limit = 10;
        $offset = ($page - 1) * $limit;
        // lấy tổng số bản ghi trong table
        $count_query = $this->conn->prepare("SELECT COUNT(*) as total from $this->table");
        $count_query->execute();
        $record_total = $count_query->fetch(PDO::FETCH_ASSOC)['total'];
        // tổng số trang
        // ceil hàm lấy phần nguyên
        $page_total = ceil($record_total / $limit);
        // lấy danh sách có phân trang
        // nếu trường category không được chọn thì lấy tất

        $query = $this->conn->prepare("select * from $this->table LIMIT :limit OFFSET :offset");
        // $query = $this->conn->prepare("select * from $this->table");
        // gán các giá trị nguyên cho limit và offset
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        // $query->execute([':limit' => (int)$limit, ':offset' => (int)$offset]);
        $query->execute();
        return ['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total];
        // return $query->fetchAll();
    }
    // create dữ liệu
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
            $this->conn->commit();
        } catch (Throwable $e) {
            // nếu có lỗi thì hoàn tác lại query trên
            $this->conn->rollBack();
            return false;
        }
        return true;
    }
    // read data
    public function read($id)
    {
        try {
            $query = $this->conn->prepare("select * from $this->table where id=:id");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
        return $query->fetch();
    }
    // delete data
    public function delete($id)
    {
        try {
            $this->conn->beginTransaction();
            $query = $this->conn->prepare("delete from $this->table where id=:id");
            $query->execute(['id' => $id]);
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();
            return false;
        }
        return true;
    }
    // update data 
    public function update($data, $id)
    {
        // kiểm tra dữ liệu tránh truyền script vào input
        // foreach ($data as $key => $value) {
        //     $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        // }
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
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();
            return false;
            // echo json_encode($e);
        }
        return true;
        // echo json_encode(2);
    }
}
