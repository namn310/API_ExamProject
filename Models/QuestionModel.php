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
    public function create($data)
    {
        return $this->QuestionModel->create($data);
    }
    public function read($id)
    {
        return $this->QuestionModel->read($id);
    }
    public function delete($id)
    {
        return $this->QuestionModel->delete($id);
    }
    public function update($data, $id)
    {
        $this->QuestionModel->update($data, $id);
    }
    public function getUserCreate()
    {
        $conn=Connection::GetConnect();
        try {
            $query = $conn->prepare("select name,id from users where role=:role");
            $query->execute(['role' => 'admin']);
        } catch (Throwable $e) {
            echo json_encode(['message' => "Lỗi" . $e]);
        }
        echo json_encode(['data' => $query->fetchAll()]);
    }
}
