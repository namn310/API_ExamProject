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
        // phân trang
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
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
        $query = $this->conn->prepare("select * from $this->table LIMIT :limit OFFSET :offset");
        // $query = $this->conn->prepare("select * from $this->table");
        // gán các giá trị nguyên cho limit và offset
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        // $query->execute([':limit' => (int)$limit, ':offset' => (int)$offset]);
        $query->execute();
        // return ['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total];
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
        } catch (Throwable $e) {
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
            $query = $this->conn->prepare("delete from $this->table where id=:id");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
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
            $arrayData = array_merge($data, $arrayId);
            $query->execute($arrayData);
        } catch (Throwable $e) {
            return false;
        }
        return true;
    }

    public function createExam($data)
    {

        $columns = implode(",", array_keys($data));

        // Lấy giá trị từ data, dùng để prepare statement
        $value = ":" . implode(",:", array_keys($data));

        // Chuẩn bị câu lệnh SQL để chèn kỳ thi mới vào bảng exams
        $query = $this->conn->prepare("INSERT INTO exams ($columns) VALUES ($value)");

        try {
            // Thực thi câu lệnh
            $query->execute($data);

            // Lấy ID của kỳ thi vừa được tạo
            $exam_id = $this->conn->lastInsertId();

            // Lấy số lượng câu hỏi ngẫu nhiên từ $data
            $questionCount = isset($data['totalQuestion']) ? (int)$data['totalQuestion'] : 3; // Mặc định là 3 nếu không có dữ liệu

            // Lấy ngẫu nhiên các câu hỏi từ bảng questions
            $questionQuery = $this->conn->prepare("SELECT id FROM questions ORDER BY RAND() LIMIT :limit");
            $questionQuery->bindParam(':limit', $questionCount, PDO::PARAM_INT);
            $questionQuery->execute();
            $questions = $questionQuery->fetchAll(PDO::FETCH_ASSOC);

            // Lưu các câu hỏi vào bảng exams_questions
            foreach ($questions as $question) {
                $examQuestionQuery = $this->conn->prepare("INSERT INTO questions_exam (id_exam, id_ques) VALUES (:id_exam, :id_ques)");
                $examQuestionQuery->execute([
                    'id_exam' => $exam_id,
                    'id_ques' => $question['id']
                ]);
            }
        } catch (Throwable $e) {
            // Xử lý lỗi nếu có
            return false;
        }

        return true;
    }
}
