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
        // lấy id đầu tiên trong danh mục câu hỏi
        $query2 = $this->conn->query("select id from category_exams limit 1");
        $category = $query2->fetch()->id;
        // phân trang
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        // nếu có trường category thì lấy còn không thì mặc định lấy category đầu tiên
        $category = isset($_GET['category']) ? $_GET['category'] : 0;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        if ($category == 0 || $category === ' ') {
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
            return ['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total, 'catIf' => $category];
            // return $query->fetchAll();
        } else {
            // lấy tổng số bản ghi trong table
            $count_query = $this->conn->prepare("SELECT COUNT(*) as total from $this->table where category=:category");
            $count_query->execute(['category' => $category]);
            $record_total = $count_query->fetch(PDO::FETCH_ASSOC)['total'];
            // tổng số trang
            // ceil hàm lấy phần nguyên
            $page_total = ceil($record_total / $limit);
            // lấy danh sách có phân trang
            // nếu trường category không được chọn thì lấy tất
            $query = $this->conn->prepare("select * from $this->table where category=:category LIMIT :limit OFFSET :offset");
            // $query = $this->conn->prepare("select * from $this->table");
            // gán các giá trị nguyên cho limit và offset
            $query->bindParam(':category', $category, PDO::PARAM_INT);
            $query->bindParam(':limit', $limit, PDO::PARAM_INT);
            $query->bindParam(':offset', $offset, PDO::PARAM_INT);
            // $query->execute([':limit' => (int)$limit, ':offset' => (int)$offset]);
            $query->execute();
            return ['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total, 'cat' => $category];
        }
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
        try {
            $this->conn->beginTransaction();
            $query = $this->conn->prepare("delete from $this->table where id=:id");
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
        try {
            $class = $data['class'];
            $category = $data['category'];
            $description = $data['description'];
            $duration = $data['duration'];
            $title = $data['title'];
            $expire_time = $data['expire_time'];
            // Format expire_time for SQL
            $exprireTimeFormat = new DateTime($expire_time);
            $expireTimeFormatted = $exprireTimeFormat->format('Y-m-d H:i:s');
            $this->conn->beginTransaction();
            // Prepare the update query
            $query = $this->conn->prepare("UPDATE $this->table 
            SET class = :class, 
                category = :category, 
                description = :description, 
                duration = :duration, 
                title = :title, 
                expire_time = :expire_time 
            WHERE id = :id");
            // Execute the query
            $query->execute([
                'class' => $class,
                'category' => $category,
                'description' => $description,
                'duration' => $duration,
                'title' => $title,
                'expire_time' => $expireTimeFormatted,
                'id' => $id
            ]);

            $this->conn->commit();
            return true;
            // echo json_encode(1);
        } catch (Throwable $e) {
            $this->conn->rollBack();
            return false;
            // throw $e;
        }
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
