<?php


include_once __DIR__ . '/../Models/BaseModel.php';
class ExamModel extends BaseModel
{
    protected $table;
    protected $ExamModel;
    protected $conn;
    public function __construct()
    {
        $this->table = 'exams';
        $this->conn = ConnectionDB::GetConnect();
        $this->ExamModel = new BaseModel($this->table);
    }
    public function index()
    {
        return $this->ExamModel->index();
    }
    // tạo mới bài kiểm tra random câu hỏi
    public function createExam($data)
    {
        $columns = implode(",", array_keys($data));
        // Lấy giá trị từ data, dùng để prepare statement
        $value = ":" . implode(",:", array_keys($data));
        // Chuẩn bị câu lệnh SQL để chèn kỳ thi mới vào bảng exams
        $query = $this->conn->prepare("INSERT INTO exams ($columns) VALUES ($value)");
        // echo json_encode($data['category']);
        $cat = $data['category'];
        try {
            $this->conn->beginTransaction();
            // Thực thi câu lệnh
            $query->execute($data);

            // Lấy ID của kỳ thi vừa được tạo
            $exam_id = $this->conn->lastInsertId();

            // Lấy số lượng câu hỏi ngẫu nhiên từ $data
            $questionCount = isset($data['totalQuestion']) ? (int)$data['totalQuestion'] : 3; // Mặc định là 3 nếu không có dữ liệu

            // Lấy ngẫu nhiên các câu hỏi từ bảng questions
            $questionQuery = $this->conn->prepare("SELECT id FROM questions where Subject=:category ORDER BY RAND() LIMIT :limit");
            $questionQuery->bindParam(':limit', $questionCount, PDO::PARAM_INT);
            $questionQuery->bindParam(':category', $cat, PDO::PARAM_INT);
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
            $this->conn->commit();
        } catch (Throwable $e) {
            // Xử lý lỗi nếu có
            $this->conn->rollBack();
            return false;
        }

        return true;
    }
    // tạo mới bài kiểm tra tùy ý số lượng câu hỏi 
    public function createExamOptionModel($data)
    {
        try {
            $this->conn->beginTransaction();
            $data['totalQuestion'] = 0;
            $columns = implode(",", array_keys($data));
            // Lấy giá trị từ data, dùng để prepare statement
            $value = ":" . implode(",:", array_keys($data));
            // Chuẩn bị câu lệnh SQL để chèn kỳ thi mới vào bảng exams
            $query = $this->conn->prepare("INSERT INTO exams ($columns) VALUES ($value)");
            $query->execute($data);
            $lastInsertId =  $this->conn->lastInsertId();
            $this->conn->commit();
            echo json_encode(['result' => $lastInsertId, 'status' => 'success']);
        } catch (Throwable $e) {
            // return false;
            $this->conn->rollback();
            echo json_encode(['result' => "Có lỗi xảy ra !", 'status' => 'error']);
        }
    }
    //lấy tên bài kiểm tra
    public function getNameExamModel($id)
    {
        try {
            $query = $this->conn->prepare("select title from exams where id=:id");
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Throwable $e) {
            return null;
        }
    }
    public function read($id)
    {
        $result = $this->ExamModel->read($id);
        $query1 = $this->conn->prepare("select count(id) as total from comments where exam_id=:id");
        $query1->execute(['id' => $id]);
        $TotalComment = $query1->fetch();
        echo json_encode(['result' => $result, 'Totalcomment' => $TotalComment]);
    }
    public function delete($id)
    {
        $this->ExamModel->delete($id);
    }
    public function update($data, $id)
    {
        $this->ExamModel->update($data, $id);
    }
    public function readQuestionExam($id)
    {
        $conn = ConnectionDB::GetConnect();
        try {
            $query = $conn->prepare("SELECT questions.image,questions.id, questions.class, questions.Subject, questions.title, questions.answerlist, questions.correctAns
                        FROM exams
                        INNER JOIN questions_exam on exams.id = questions_exam.id_exam
                        INNER JOIN questions on questions_exam.id_ques = questions.id where exams.id=:id");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
        return $query->fetchAll();
        // echo json_encode($query->fetchAll());
    }
    public function getQuestionNoResult($id)
    {
        $conn = ConnectionDB::GetConnect();
        try {
            $query = $conn->prepare("SELECT questions.image,questions.id, questions.class, questions.Subject, questions.title, questions.answerlist
                        FROM exams
                        INNER JOIN questions_exam on exams.id = questions_exam.id_exam
                        INNER JOIN questions on questions_exam.id_ques = questions.id where exams.id=:id");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
        return $query->fetchAll();
    }
    public function readCategoryExam($id)
    {
        $conn = ConnectionDB::GetConnect();
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
    public function getExamByCatModel($id)
    {
        $conn = ConnectionDB::GetConnect();
        try {
            $query = $conn->prepare("SELECT * from $this->table WHERE category=:id");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
        return $query->fetch();
    }
    // lấy số lượng người làm sai câu hỏi 
    public function getNumberDoWrongModel($id)
    {
        $conn = ConnectionDB::GetConnect();
        try {
            $query = $conn->prepare("SELECT result_detail.id_question,result_detail.number_do_wrong from result_detail INNER JOIN results on result_detail.id_results = results.id where results.id_exam=:id  ");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
        return $query->fetchAll();
    }
}
