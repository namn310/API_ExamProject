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
        $conn = ConnectionDB::GetConnect();
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
        $conn = ConnectionDB::GetConnect();
        try {
            $query = $conn->prepare("select id_question,answer,state from $this->tableResultDetail where id_results=:id_results order by id_question");
            $query->execute(['id_results' => $id]);
        } catch (Throwable $e) {
            echo json_encode(['message' => "Có lối xảy ra "]);
        }
        echo json_encode(['data' => $query->fetchAll()]);
    }

    public function createResultExam($data)
    {
        $conn = ConnectionDB::GetConnect();
        // $data = json_decode(file_get_contents("php://input"), true);
        // lấy tên cột từ data;
        // $answer = array_pop($data);
        // $listQuestionIncorrect = array_pop($data);
        // $array = [];
        // echo json_encode($data['id_exam']);
        try {
            // // tổng số câu hỏi trong bài kiếm tra
            $totalQuestionInExam = array_pop($data);
            // // lấy danh sách câu trả lời của thí sinh
            $answer = array_pop($data);
            $blankAnswerQuestion = $data['blank_question'];
            // số câu hỏi làm đúng
            $correct_question = 0;
            // số điểm của mỗi câu hỏi
            $scorePerQuestion = 10 / $totalQuestionInExam;
            $listQuestionInExam = array_pop($data);
            $idExam = $data['id_exam'];
            $listCorrectAnswer = [];
            // duyệt mảng answer để lấy id các câu hỏi
            foreach ($listQuestionInExam as $row) {
                $id_ques = $row['id'];
                // lấy ra đáp án của các câu hỏi
                $queryGetCorrectAns = $conn->prepare("select id,correctAns from questions where id=:id");
                $queryGetCorrectAns->execute(['id' => $id_ques]);
                $itemCorrectAns = $queryGetCorrectAns->fetch(PDO::FETCH_ASSOC)['correctAns'];
                // loại bỏ các ký tự JSON
                $itemCorrectAns = trim($itemCorrectAns, "[]\"\n ");
                $itemCorrectAns = explode(',', $itemCorrectAns);
                $itemCorrectAns = array_map(function ($item) {
                    return trim($item, ' "'); // Loại bỏ cả dấu " và khoảng trắng đầu/cuối
                }, $itemCorrectAns);
                // tạo đối tượng
                $listItem = new stdClass();
                $listItem->id = $id_ques;
                $listItem->answer = $itemCorrectAns;
                // thêm phần tử listItem vào mảng đáp án
                $listCorrectAnswer[] = $listItem;
            }
            //  so sánh hai mảng answer và  $listCorrectAnswer để tính điểm

            // số câu trả lời đúng
            // duyệt mảng answer và thêm câu trả lời vào listQuestionExam để thực hiện so sánh đáp án
            foreach ($listQuestionInExam as $key => $row1) {
                $listQuestionInExam[$key]['answer'] = null;
                foreach ($answer as $row2) {
                    if ($row2['id'] === $row1['id']) {
                        $listQuestionInExam[$key]['answer'] = $row2['answer'];
                        break;
                    }
                }
            }
            // tạo mảng danh sách check đáp án
            // $listCheckAnswer = [];
            // duyệt mảng danh sách câu trả lời đùng
            foreach ($listCorrectAnswer as $row1) {
                $id = $row1->id;
                // $item = new stdClass();
                // $item->id = $id;
                // $item->status = 0;
                // duyệt mảng câu trả lời của người dùng
                foreach ($listQuestionInExam as $key => $row2) {
                    $row2Id =  $row2['id'];
                    $row2Answer = $row2['answer'];
                    // tìm đến phần tử có id giống với id trong danh sách câu trả lời đùng
                    if ($id == $row2Id) {
                        // dùng array_diff để so sánh 2 mảng đáp áp
                        //  array_diff sẽ trả về phần tử chỉ xuất hiện ở mảng đầu mà không xuất hiện ở các mảng khác
                        if (!empty($row2Answer)) {
                            if (!(array_diff($row2Answer, $row1->answer)) && !(array_diff($row1->answer, $row2Answer))) {
                                // thực hiện so sánh thì đáp án
                                // gắn cờ bằng 1 nghĩa là câu này đúng
                                $listQuestionInExam[$key]['state'] = 1;
                                $correct_question += 1;
                                break;
                            }
                        } else {
                        }
                    }
                }
                // $listCheckAnswer[] = $item;
            }
            // số câu hỏi làm sai
            $incorrect_question = $totalQuestionInExam - $correct_question - $blankAnswerQuestion;
            // thêm dữ liệu vào mảng data
            $data['incorrect_question'] = $incorrect_question;
            $data['score'] = $scorePerQuestion * $correct_question;
            $data['correct_question'] = $correct_question;
            // echo json_encode([
            //     $answer,
            //     $listCorrectAnswer,
            //     $listQuestionInExam,
            //     $correct_question,
            //     $incorrect_question,
            //     $data['score'],
            //     $scorePerQuestion
            // ]);
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
            $query3 = $conn->prepare("insert into result_detail set id_results=:id_results,id_question=:id_question,answer=:answer,state=:state");
            if ($answer !== null) {
                foreach ($listQuestionInExam as $row2) {
                    // duyệt mảng answer lấy id trong answer trùng với id trong query2 thì lấy câu trả lời 
                    // if ($row2['id'] == $row->id_ques) {
                    $answerSelected = $row2['answer'] !== null ? $row2['answer'] : null;
                    // }
                    // }
                    $query3->execute(['id_results' => $lastRecord, 'id_question' => $row2['id'], 'answer' => json_encode($answerSelected),'state'=>$row2['state']]);
                    // set trạng thái câu hỏi người làm đúng hay sai
                }
            }
            foreach ($listQuestionInExam as $row3) {
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
