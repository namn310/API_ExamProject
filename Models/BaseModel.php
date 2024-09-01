<?php
include_once __DIR__ . "/../Connection/Connection.php";
class BaseModel
{
    protected $table;
    protected $conn;
    public function __construct($table)
    {
        $this->table = $table;
        $conn = Connection::GetConnect();
        $this->conn = $conn;
    }
    // lấy dữ liệu
    public function index()
    {
        $query = $this->conn->query("select * from $this->table");
        return $query->fetchAll();
    }
    // create dữ liệu
    public function create($data)
    {
        // $data = json_decode(file_get_contents("php://input"), true);
        // lấy tên cột từ data;
        $columns = implode(",", array_keys($data));
        // prepare giá trị truyền vào sql
        // lấy giá trị từ data
        $value = ":" . implode(",:", array_keys($data));
        // prepare query
        $query = $this->conn->prepare("insert into $this->table ($columns) values ($value) ");
        try {
            $query->execute($data);
        } catch (Throwable) {
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
        } catch (Throwable) {
            return null;
        }
        return $query->fetch();
    }
    // delete data
    public function delete($id)
    {
        try {
            $query = $this->conn->prepare("delete from $this->table where id=:id");
            $query->execute(['id' => $id]);
        } catch (Throwable) {
            return false;
        }
        return true;
    }
    // update data 
    public function update($data, $id)
    {
        $string = "";
        $columns = implode(",", array_keys($data));
        $columns_set_name = explode(',', $columns);
        foreach ($columns_set_name as $row) {
            $string .= $row . '=:' . $row . ',';
        }
        $setClause = rtrim($string, ",");
        // ví dụ chuỗi string sẽ có dạng name=:name,....
        // echo $setClause;
        try {
            $query = $this->conn->prepare("update $this->table set $setClause where id=:id");
            $arrayId = ['id' => $id];
            //merge mảng để execute query
            $arrayData = array_merge_recursive($data, $arrayId);
            $query->execute($arrayData);
        } catch (Throwable) {
            return false;
        }
        return true;
    }
}
