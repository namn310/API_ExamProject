<?php

use function PHPSTORM_META\type;

include_once __DIR__ . '/../Models/BaseModel.php';
require 'vendor/autoload.php';
class IRTModel extends BaseModel
{
    protected $conn;
    public function __construct()
    {
        $conn = ConnectionDB::GetConnect();
        $this->conn = $conn;
    }
    // Chỉ lấy ra ID học sinh làm bài
    public function getDataIDStudentDoExam($id)
    {
        $query1 = $this->conn->prepare("select id_exam,id_user,id from results where id_exam =:id");
        $query1->execute(['id' => $id]);
        return $query1->fetchAll();
    }
    // lấy danh sách học sinh làm bài kiểm tra
    public function getDataStudentDoExam($id)
    {
        $query1 = $this->conn->prepare("select 
        results.id_exam,
        results.id_user,
        results.id as resultID,
        users.id as userID,
        users.theta 
        from results 
        inner join users 
        on results.id_user = users.id 
        where results.id_exam =:id");
        $query1->execute(['id' => $id]);
        return $query1->fetchAll();
    }
    // lấy số lượng câu hỏi của bài thi
    public function getTotalQuestionOfExam($idExam)
    {
        $query = $this->conn->prepare("
        select QE.id_ques
        from questions_exam QE 
        inner join 
        questions Q 
        where Q.id = QE.id_ques 
        and QE.id_exam =:id ");
        $query->execute(['id' => $idExam]);
        return $query->fetchAll();
    }
    // lấy dữ liệu kết quả làm bài của học sinh
    public function getDataResultByStudentModel($idUser, $idExam, $idResult)
    {
        $query = $this->conn->prepare("
        SELECT R.id_user,
        R.id_exam,
        R.id,
        RD.id_results,
        RD.id_question,
        RD.answer,
        RD.state
        FROM results as R 
         inner join 
         result_detail as RD 
         on R.id = RD.id_results 
         where R.id_user=:id_user and R.id_exam=:id_exam and R.id=:id_result
        ");
        $query->execute(['id_user' => $idUser, 'id_exam' => $idExam, 'id_result' => $idResult]);
        return $query->fetchAll();
    }
    // tạo dữ liệu để tính toán IRT
    public function makeDataToCalculateIRT($idExam)
    {
        $hostIrt = getenv('HOST_IRT_CANCULATE');
        // lấy danh sách người làm bài kiểm tra
        $userDoExam = $this->getDataIDStudentDoExam($idExam);
        foreach ($userDoExam as $row) {
            // $dataItem = new stdClass();
            $dataItem = [];
            // lấy dữ liệu kết quả làm bài của học sinh
            $result = $this->getDataResultByStudentModel($row->id_user, $row->id_exam, $row->id);
            foreach ($result as $row2) {
                // thêm dữ liệu đúng sai vào mảng
                $dataItem[] = $row2->state;
            }
            // thêm mảng dữ liệu đúng sai của mảng trên vào danh sách dữ liệu làm bài của người dùng của đề thi đó
            $data[] = $dataItem;
        }
        // $data =  json_encode($data);
        // echo json_encode($data);
        // đường dẫn API tính IRT

        $url = $hostIrt;
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json', // Xác định loại dữ liệu gửi là JSON
                'content' => json_encode($data)  // Chuyển đổi mảng thành JSON
            ]
        ];
        // Tạo context cho stream
        $context  = stream_context_create($options);
        // Gửi yêu cầu và nhận kết quả từ API
        $result = @file_get_contents($url, false, $context);
        // Kiểm tra kết quả trả về
        if ($result === FALSE) {
            die('Error occurred while sending data to API.');
        }
        // Giải mã kết quả JSON nhận được
        $response = json_decode($result, true);
        $response = $response[0];
        // vì dữ liệu IRT được trả về sẽ tồn tại giá trị coefficient và AIC,BIC,likelihood nên thực hiện kiểm tra chuỗi string trả về
        if (strpos($response, '{"coefficients":[],"log_likelihood":{},"AIC":{},"BIC":{}}') == false) {
            // nếu có chứa các biến trên thì tiến hành tách chuỗi để lấy mảng giả trị độ khó
            $response = str_replace('{"coefficients":[', '', $response);
            $response = str_replace('],"log_likelihood":{},"AIC":{},"BIC":{}}', '', $response);
            $listDataIrt = explode(",", $response);
            echo json_encode(['dataIrt' => $listDataIrt]);
        } else {
            // còn không thì không có dữ liệu độ khó
            $listDataIrt = null;
            echo json_encode(['dataIrt' => $listDataIrt]);
        }

        // $result_from_r = ($response[0]['coefficients']);
        // $resultList = [
        //     "coefficients" => $result_from_r['coefficients'],
        //     "log_likelihood" => $result_from_r['log_likelihood'],
        //     "AIC" => $result_from_r['AIC'],
        //     "BIC" => $result_from_r['BIC']
        // ];
        // print_r($resultList);
    }
}
