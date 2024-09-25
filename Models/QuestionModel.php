<?php
include_once __DIR__ . '/../Models/BaseModel.php';
class QuestionModel extends BaseModel
{
    protected $table;
    protected $QuestionModel;
    public function __construct()
    {
        $this->table = 'questions';
        $this->QuestionModel = new BaseModel($this->table);
    }
    public function index()
    {
        return $this->QuestionModel->index();
    }
    public function create($data2)
    {
        $data = [];
        foreach ($_POST as $key => $value) {
            // Thêm từng giá trị vào mảng $data với định dạng key => value
            $data[$key] = $value;
        }
        if (isset($_FILES['image'])) {
            $folder = __DIR__ . '/../assets/image/Question/';
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);  // Tạo thư mục với quyền ghi đầy đủ
            }
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $upload_file = $folder . $image_name;
            $data['image'] = $image_name;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_file);
        } else {
            $data['image'] = '';
        }
        // return $this->QuestionModel->create($data);
        // lấy tên cột từ data;
        $columns = implode(",", array_keys($data));
        // // prepare giá trị truyền vào sql
        // // lấy giá trị từ data
        $value = ":" . implode(",:", array_keys($data));
        // // prepare query
        $conn = Connection::GetConnect();
        $query = $conn->prepare("insert into $this->table ($columns) values ($value) ");
        try {
            $query->execute($data);
        } catch (Throwable $e) {
            echo json_encode(['message' => "Có lỗi xảy ra " . $e]);
        }
        echo json_encode(['message' => "Thêm thành công"]);
    }
    public function read($id)
    {
        return $this->QuestionModel->read($id);
    }
    public function delete($id)
    {
        try {
            // xóa hình ảnh câu hỏi nếu có
            $folder = __DIR__ . '/../assets/image/Question/';
            $conn = Connection::GetConnect();
            $query2 = $conn->prepare("select image from $this->table where id=:id");
            $query2->execute(['id' => $id]);
            $result = $query2->fetch();
            $img = $result->image;
            if (!empty($img)) {
                $oldFileImage = $folder . $img;
                if (file_exists($oldFileImage)) {
                    unlink($oldFileImage);
                }
            }

            // xóa câu hỏi
            $query = $conn->prepare("delete from $this->table where id=:id");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return false;
        }
        return true;
    }
    public function update($data2, $id)
    {
        $data = [];
        foreach ($_POST as $key => $value) {
            // Thêm từng giá trị vào mảng $data với định dạng key => value
            $data[$key] = $value;
        }
        $folder = __DIR__ . '/../assets/image/Question/';
        if (isset($_FILES['image'])) {

            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);  // Tạo thư mục với quyền ghi đầy đủ
            }
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $upload_file = $folder . $image_name;
            $data['image'] = $image_name;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_file);
        } else {
            if (!empty($data['image'])) {
                $data['image'] = $data['image'];
            } else {
                $data['image'] = '';
            }
        }
        $string = "";
        $columns = implode(",", array_keys($data));
        $columns_set_name = explode(',', $columns);
        foreach ($columns_set_name as $row) {
            $string .= $row . '=:' . $row . ',';
        }
        $setClause = rtrim($string, ",");
        // echo json_encode($data);
        // ví dụ chuỗi string sẽ có dạng name=:name,....
        // echo $setClause;
        try {
            $conn = Connection::GetConnect();
            $query2 = $conn->prepare("select image from $this->table where id=:id");
            $query2->execute(['id' => $id]);
            $result = $query2->fetch();
            $img = $result->image;
            $oldFileImage = $folder . $img;
            if (file_exists($oldFileImage)) {
                unlink($oldFileImage);
            }
            $query = $conn->prepare("update $this->table set $setClause where id=:id");
            $arrayId = ['id' => $id];
            //merge mảng để execute query
            $arrayData = array_merge($data, $arrayId);
            $query->execute($arrayData);
            // nếu sau khi update người dùng xóa ảnh của câu hỏi đi thì xóa ảnh của câu hỏi trong folder
            $query3 = $conn->prepare("select image from $this->table where id=:id");
            $query3->execute(['id' => $id]);
            $result = $query3->fetch();
            $img2 = $result->image;
            if (!empty($img2)) {
                if (file_exists($oldFileImage)) {
                    unlink($oldFileImage);
                }
            }
        } catch (Throwable $e) {
            return false;
            // echo json_encode($e);
        }
        return true;
        // echo json_encode(2);
    }
    public function getUserCreate()
    {
        $conn = Connection::GetConnect();
        try {
            $query = $conn->prepare("select name,id from users where role=:role");
            $query->execute(['role' => 'admin']);
        } catch (Throwable $e) {
            echo json_encode(['message' => "Lỗi" . $e]);
        }
        echo json_encode(['data' => $query->fetchAll()]);
    }
}
