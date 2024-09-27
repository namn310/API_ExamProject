<?php
include_once __DIR__ . '/../Models/BaseModel.php';
class CommentModel extends BaseModel
{
    protected $table;
    protected $CommentModel;
    public function __construct()
    {
        $this->table = 'comments';
        $this->CommentModel = new BaseModel($this->table);
    }
    public function index()
    {
        return $this->CommentModel->index();
    }
    public function createExam($data)
    {
        $conn=Connection::GetConnect();
        $columns = implode(",", array_keys($data));
        // Lấy giá trị từ data, dùng để prepare statement
        $value = ":" . implode(",:", array_keys($data));
        // Chuẩn bị câu lệnh SQL để chèn kỳ thi mới vào bảng exams
        $query = $conn->prepare("INSERT INTO exams ($columns) VALUES ($value)");
        // echo json_encode($data['category']);
        $cat=$data['category'];
        try {
            // Thực thi câu lệnh
            $query->execute($data);

            // Lấy ID của kỳ thi vừa được tạo
            $exam_id = $conn->lastInsertId();

            // Lấy số lượng câu hỏi ngẫu nhiên từ $data
            $questionCount = isset($data['totalQuestion']) ? (int)$data['totalQuestion'] : 3; // Mặc định là 3 nếu không có dữ liệu

            // Lấy ngẫu nhiên các câu hỏi từ bảng questions
            $questionQuery = $conn->prepare("SELECT id FROM questions where Subject=:category ORDER BY RAND() LIMIT :limit");
            $questionQuery->bindParam(':limit', $questionCount, PDO::PARAM_INT);
            $questionQuery->bindParam(':category', $cat, PDO::PARAM_INT);
            $questionQuery->execute();
            $questions = $questionQuery->fetchAll(PDO::FETCH_ASSOC);

            // Lưu các câu hỏi vào bảng exams_questions
            foreach ($questions as $question) {
                $examQuestionQuery = $conn->prepare("INSERT INTO questions_exam (id_exam, id_ques) VALUES (:id_exam, :id_ques)");
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
    public function read($id)
    {
        return $this->CommentModel->read($id);
    }
    public function delete($id)
    {
        return $this->CommentModel->delete($id);
    }
    public function update($data, $id)
    {
        $this->CommentModel->update($data, $id);
    }

    public function readCommentsExam($id)
    {
        $conn = Connection::GetConnect();
        try {
            $query = $conn->prepare("SELECT * FROM comments WHERE exam_id=:id AND parent_id IS NULL");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
        return $query->fetch();
    }
}
