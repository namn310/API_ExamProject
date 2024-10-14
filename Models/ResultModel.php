<?php

use function PHPSTORM_META\type;

include_once __DIR__ . '/../Models/BaseModel.php';
require 'vendor/autoload.php';
class ResultModel extends BaseModel
{
    protected $table;
    protected $ResultModel;
    protected $tableResultDetail;
    public function __construct()
    {
        $this->table = 'results';
        $this->tableResultDetail = 'result_detail';
        $this->ResultModel = new BaseModel($this->table);
    }
    public function index()
    {
        return $this->ResultModel->index();
    }
    // Lấy danh sách các bài làm của user
    public function getUserResultListModel($id)
    {
        $conn = Connection::GetConnect();
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        // lấy tổng số bản ghi trong table
        $count_query = $conn->prepare("SELECT COUNT(*) as total from results");
        $count_query->execute();
        $record_total = $count_query->fetch(PDO::FETCH_ASSOC)['total'];
        // tổng số trang
        // ceil hàm lấy phần nguyên
        $page_total = ceil($record_total / $limit);
        // lấy danh sách có phân trang
        $query = $conn->prepare("select * from $this->table LIMIT :limit OFFSET :offset");
        // $query = $conn->prepare("select * from $this->table");
        // gán các giá trị nguyên cho limit và offset
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        // $query->execute([':limit' => (int)$limit, ':offset' => (int)$offset]);
        $query->execute();
        // return ['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total];
        try {
            $query = $conn->prepare("SELECT results.id,results.id_user,results.id_exam,results.score,results.duration,results.created_at,exams.title from results INNER JOIN exams ON results.id_exam = exams.id  WHERE results.id_user=:id_user ORDER BY results.id DESC LIMIT :limit OFFSET :offset ");
            $query->bindParam(':limit', $limit, PDO::PARAM_INT);
            $query->bindParam(':offset', $offset, PDO::PARAM_INT);
            $query->bindParam(':id_user', $id, PDO::PARAM_INT);
            $query->execute();
        } catch (Throwable $e) {
            echo json_encode(['message' => 'Có lỗi xảy ra ' . $e]);
        }
        echo json_encode(['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total]);
    }
    public function read($id)
    {
        return $this->ResultModel->read($id);
    }
    public function getReviewModel($id)
    {
        $conn = Connection::GetConnect();
        try {
            $query = $conn->prepare("select id_question,answer from $this->tableResultDetail where id_results=:id_results order by id_question");
            $query->execute(['id_results' => $id]);
        } catch (Throwable $e) {
            echo json_encode(['message' => "Có lối xảy ra "]);
        }
        echo json_encode(['data' => $query->fetchAll()]);
    }
    public function createResultExam($data)
    {
        $conn = Connection::GetConnect();
        // $data = json_decode(file_get_contents("php://input"), true);
        // lấy tên cột từ data;
        // $answer = array_pop($data);
        // $listQuestionIncorrect = array_pop($data);
        // $array = [];
        // echo json_encode($data['id_exam']);
        try {
            $answer = array_pop($data);
            $listQuestionIncorrect = array_pop($data);
            $idExam = $data['id_exam'];
            $columns = implode(",", array_keys($data));
            // prepare giá trị truyền vào sql
            // lấy giá trị từ data
            $value = ":" . implode(",:", array_keys($data));
            // prepare query
            $query = $conn->prepare("insert into $this->table ($columns) values ($value) ");
            $query->execute($data);
            $lastRecord = $conn->lastInsertId();
            // tăng số lần làm bài thi thêm 1 
            $query2 = $conn->prepare("select count_user_do from exams where id=:id");
            $query2->execute(['id' => $idExam]);
            $resultQuery2 = $query2->fetch();
            $count_user_do = $resultQuery2->count_user_do;
            // cập nhật lại giá trị số lần làm bài thi
            $query6 = $conn->prepare("update exams set count_user_do=:number where id=:id");
            $query6->execute(['number' => $count_user_do + 1, 'id' => $idExam]);
            // thêm dữ liệu vào bảng result_question
            $query3 = $conn->prepare("insert into result_detail set id_results=:id_results,id_question=:id_question,answer=:answer");
            if ($answer !== null) {
                foreach ($answer as $row2) {
                    // duyệt mảng answer lấy id trong answer trùng với id trong query2 thì lấy câu trả lời 
                    // if ($row2['id'] == $row->id_ques) {
                    $answerSelected = $row2['answer'] !== null ? $row2['answer'] : null;
                    // }
                    // }
                    $query3->execute(['id_results' => $lastRecord, 'id_question' => $row2['id'], 'answer' => $answerSelected]);
                    // set trạng thái câu hỏi người làm đúng hay sai
                }
            }
            foreach ($listQuestionIncorrect as $row3) {
                $idQuestion = $row3['id'];
                $query5 = $conn->prepare("select id_question from result_detail where id_question =:id");
                $query5->execute(['id' => $idQuestion]);
                // kiểm tra xem có tồn tại idQues trong bảng result_detail không
                $resultIdQues = $query5->fetchAll();
                if ($resultIdQues) {
                    // nếu có thì lấy số người làm sai ra rồi cộng thêm 1
                    $query4 = $conn->prepare("SELECT result_detail.number_do_wrong from result_detail inner join results on result_detail.id_results = results.id where result_detail.id_question=:id and results.id_exam =:id_exam");
                    $query4->execute(['id' => $idQuestion, 'id_exam' => $idExam]);
                    $result = $query4->fetch();
                    $numberWrong = $result->number_do_wrong;
                    $query5 = $conn->prepare("UPDATE result_detail inner join results on result_detail.id_results = results.id set result_detail.number_do_wrong=:number where result_detail.id_question=:id and results.id_exam =:id_exam ");
                    $query5->execute(['id' => $idQuestion, 'number' => $numberWrong + 1, 'id_exam' => $idExam]);
                }
            }
        } catch (Throwable $e) {
            echo json_encode(['message' => 'Có lỗi xảy ra ' . $e]);
        }
        echo json_encode(['message' => 'Thêm thành công ', 'lastInsert' => $lastRecord]);
    }
    public function update($data, $id)
    {
        $this->ResultModel->update($data, $id);
    }
    public function delete($id)
    {
        $this->ResultModel->delete($id);
    }
}
