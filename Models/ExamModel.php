<?php
include_once __DIR__ . '/../Models/BaseModel.php';
class ExamModel extends BaseModel
{
    protected $table;
    protected $ExamModel;
    public function __construct()
    {
        $this->table = 'exams';
        $this->ExamModel = new BaseModel($this->table);
    }
    public function index()
    {
        return $this->ExamModel->index();
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
            // echo $e;
        }

        return true;
    }
    public function read($id)
    {
        return $this->ExamModel->read($id);
    }
    public function delete($id)
    {
        return $this->ExamModel->delete($id);
    }
    public function update($data, $id)
    {
        $this->ExamModel->update($data, $id);
    }
    public function readQuestionExam($id)
    {
        $conn=Connection::GetConnect();
        try {
            $query = $conn->prepare("SELECT questions.id, questions.class, questions.Subject, questions.title, questions.A, questions.B, questions.C,
                        questions.D, questions.correctAns
                        FROM questions
                        INNER JOIN questions_exam on questions.id = questions_exam.id_ques
                        INNER JOIN exams on questions_exam.id_exam = exams.id where exams.id=:id");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
        return $query->fetchAll();
    }
    public function readCategoryExam($id)
    {
        $conn = Connection::GetConnect();
        try {
            $query = $conn->prepare("SELECT category_exams.title FROM category_exams
                INNER JOIN exams on category_exams.id = exams.category
                WHERE exams.category=:id");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
        return $query->fetch();
    }
}
