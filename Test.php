<?php
header("Content-Type: application/json");

include_once "Models/BaseModel.php";
include_once __DIR__ . "../Connection/Connection.php";
Connection::GetConnect();
// echo getenv('KEY');

// include_once "Models/QuestionModel.php";
// echo ('<br/>');

// $person = array(
//     "name" => "John",
//     "age" => 25,
//     "city" => [
//         ['id'=>1,'name'=>2],
//         ['id'=>2,'name'=>3]
//     ]
// );
// // Loại bỏ phần tử cuối cùng
// print_r(array_pop($person));
// echo ('<br/>');
// // In mảng sau khi loại bỏ phần tử cuối
// print_r($person);



 // public function createExam($data)
    // {

    //     $columns = implode(",", array_keys($data));

    //     // Lấy giá trị từ data, dùng để prepare statement
    //     $value = ":" . implode(",:", array_keys($data));

    //     // Chuẩn bị câu lệnh SQL để chèn kỳ thi mới vào bảng exams
    //     $query = $this->conn->prepare("INSERT INTO exams ($columns) VALUES ($value)");

    //     try {
    //         // Thực thi câu lệnh
    //         $query->execute($data);

    //         // Lấy ID của kỳ thi vừa được tạo
    //         $exam_id = $this->conn->lastInsertId();

    //         // Lấy số lượng câu hỏi ngẫu nhiên từ $data
    //         $questionCount = isset($data['totalQuestion']) ? (int)$data['totalQuestion'] : 3; // Mặc định là 3 nếu không có dữ liệu

    //         // Lấy ngẫu nhiên các câu hỏi từ bảng questions
    //         $questionQuery = $this->conn->prepare("SELECT id FROM questions ORDER BY RAND() LIMIT :limit");
    //         $questionQuery->bindParam(':limit', $questionCount, PDO::PARAM_INT);
    //         $questionQuery->execute();
    //         $questions = $questionQuery->fetchAll(PDO::FETCH_ASSOC);

    //         // Lưu các câu hỏi vào bảng exams_questions
    //         foreach ($questions as $question) {
    //             $examQuestionQuery = $this->conn->prepare("INSERT INTO questions_exam (id_exam, id_ques) VALUES (:id_exam, :id_ques)");
    //             $examQuestionQuery->execute([
    //                 'id_exam' => $exam_id,
    //                 'id_ques' => $question['id']
    //             ]);
    //         }
    //     } catch (Throwable $e) {
    //         // Xử lý lỗi nếu có
    //         return false;
    //         // echo $e;
    //     }

    //     return true;
    // }
    // public function checkToken()
    // {
    //     // lấy headers từ UI khi fetch API
    //     $header = getallheaders();
    //     if (isset($header['Authorization'])) {
    //         try {
    //             $jwt = str_replace('Bearer ', '', $header['Authorization']);
    //             $decoded = JWT::decode($jwt, new Key(getenv('KEY'), 'HS256'));
    //             echo json_encode(['decode' => $decoded->data]);
    //         } catch (Throwable $e) {
    //             echo json_encode(['message' => $e]);
    //         }
    //     } else {
    //         echo json_encode(['message' => 'Không tồn tại Token']);
    //     }
    // }

    // // function question
    // public function getUserCreate()
    // {
    //     try {
    //         $query = $this->conn->prepare("select name,id from users where role=:role");
    //         $query->execute(['role' => 'admin']);
    //     } catch (Throwable $e) {
    //         echo json_encode(['message' => "Lỗi" . $e]);
    //     }
    //     echo json_encode(['data' => $query->fetchAll()]);
    // }
    // // function Exam
    // public function readQuestionExam($id)
    // {
    //     try {
    //         $query = $this->conn->prepare("SELECT questions.id, questions.class, questions.Subject, questions.title, questions.A, questions.B, questions.C,
    //                     questions.D, questions.correctAns
    //                     FROM questions
    //                     INNER JOIN questions_exam on questions.id = questions_exam.id_ques
    //                     INNER JOIN exams on questions_exam.id_exam = exams.id where exams.id=:id");
    //         $query->execute(['id' => $id]);
    //     } catch (Throwable $e) {
    //         return null;
    //     }
    //     return $query->fetchAll();
    // }
    // public function readQuestionCategory($id)
    // {
    //     try {
    //         $query = $this->conn->prepare("SELECT exams.id, exams.title, exams.duration,exams.totalQuestion FROM exams
    //             INNER JOIN category_exams on exams.category = category_exams.id
    //             WHERE category_exams.id=:id");
    //         $query->execute(['id' => $id]);
    //     } catch (Throwable $e) {
    //         return null;
    //     }
    //     return $query->fetchAll();
    // }

    // public function readCategoryExam($id)
    // {
    //     try {
    //         $query = $this->conn->prepare("SELECT category_exams.title FROM category_exams
    //             INNER JOIN exams on category_exams.id = exams.category
    //             WHERE exams.category=:id");
    //         $query->execute(['id' => $id]);
    //     } catch (Throwable $e) {
    //         return null;
    //     }
    //     return $query->fetch();
    // }

    // // function result
    // public function createResultExam($data)
    // {
    //     // $data = json_decode(file_get_contents("php://input"), true);
    //     // lấy tên cột từ data;
    //     $array = [];
    //     try {
    //         $answer = array_pop($data);
    //         $idExam = $data['id_exam'];
    //         $columns = implode(",", array_keys($data));
    //         // prepare giá trị truyền vào sql
    //         // lấy giá trị từ data
    //         $value = ":" . implode(",:", array_keys($data));
    //         // prepare query
    //         $query = $this->conn->prepare("insert into $this->table ($columns) values ($value) ");
    //         $query->execute($data);
    //         $lastRecord = $this->conn->lastInsertId();
    //         // thêm dữ liệu vào bảng result_question
    //         $query2 = $this->conn->prepare("select id_ques from questions_exam where id_exam=:id_exam");
    //         $query2->execute(['id_exam' => $idExam]);
    //         foreach ($query2->fetchAll() as $row) {
    //             $answerSelected = '';
    //             $query3 = $this->conn->prepare("insert into result_detail set id_results=:id_results,id_question=:id_question,answer=:answer");
    //             foreach ($answer as $row2) {
    //                 // duyệt mảng answer lấy id trong answer trùng với id trong query2 thì lấy câu trả lời 
    //                 if ($row2['id'] == $row->id_ques) {
    //                     $answerSelected = $row2['answer'];
    //                 }
    //             }
    //             $query3->execute(['id_results' => $lastRecord, 'id_question' => $row->id_ques, 'answer' => $answerSelected]);
    //         }
    //     } catch (Throwable $e) {
    //         echo json_encode(['message' => 'Có lỗi xảy ra ' . $e]);
    //     }
    //     echo json_encode(['message' => 'Thêm thành công ', 'lastInsert' => $lastRecord]);
    // }
    // public function getReviewModel($id)
    // {
    //     try {
    //         $query = $this->conn->prepare("select id_question,answer from result_detail where id_results=:id_results");
    //         $query->execute(['id_results' => $id]);
    //     } catch (Throwable $e) {
    //         echo json_encode(['message' => "Có lối xảy ra "]);
    //     }
    //     echo json_encode(['data' => $query->fetchAll()]);
    // }
    // public function getUserResultListModel($id)
    // {
    //     $page = isset($_GET['page']) ? $_GET['page'] : 1;
    //     $limit = 10;
    //     $offset = ($page - 1) * $limit;
    //     // lấy tổng số bản ghi trong table
    //     $count_query = $this->conn->prepare("SELECT COUNT(*) as total from results");
    //     $count_query->execute();
    //     $record_total = $count_query->fetch(PDO::FETCH_ASSOC)['total'];
    //     // tổng số trang
    //     // ceil hàm lấy phần nguyên
    //     $page_total = ceil($record_total / $limit);
    //     // lấy danh sách có phân trang
    //     $query = $this->conn->prepare("select * from $this->table LIMIT :limit OFFSET :offset");
    //     // $query = $this->conn->prepare("select * from $this->table");
    //     // gán các giá trị nguyên cho limit và offset
    //     $query->bindParam(':limit', $limit, PDO::PARAM_INT);
    //     $query->bindParam(':offset', $offset, PDO::PARAM_INT);
    //     // $query->execute([':limit' => (int)$limit, ':offset' => (int)$offset]);
    //     $query->execute();
    //     // return ['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total];
    //     try {
    //         $query = $this->conn->prepare("SELECT results.id,results.id_user,results.id_exam,results.score,results.duration,results.created_at,exams.title from results INNER JOIN exams ON results.id_exam = exams.id  WHERE results.id_user=:id_user LIMIT :limit OFFSET :offset");
    //         $query->bindParam(':limit', $limit, PDO::PARAM_INT);
    //         $query->bindParam(':offset', $offset, PDO::PARAM_INT);
    //         $query->bindParam(':id_user', $id, PDO::PARAM_INT);
    //         $query->execute();
    //     } catch (Throwable $e) {
    //         echo json_encode(['message' => 'Có lỗi xảy ra ' . $e]);
    //     }
    //     echo json_encode(['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total]);
    // }

    // =======
    // public function createResult($data)
    // {
    //     // Lấy tên cột từ data
    //     $columns = implode(",", array_keys($data));

    //     // Chuẩn bị giá trị truyền vào SQL
    //     $value = ":" . implode(",:", array_keys($data));

    //     // Chuẩn bị câu lệnh SQL
    //     $query = $this->conn->prepare("INSERT INTO {$this->table} ($columns) VALUES ($value)");

    //     try {
    //         // Thực thi câu lệnh
    //         $query->execute($data);
    //         // Lấy ID của bản ghi vừa thêm (nếu cần)
    //         $insertedId = $this->conn->lastInsertId();

    //         return [
    //             'success' => true,
    //             'message' => 'Tạo mới bản ghi thành công!',
    //             'id' => $insertedId
    //         ];
    //     } catch (Throwable $e) {
    //         // Ghi log lỗi
    //         error_log($e->getMessage());

    //         return [
    //             'success' => false,
    //             'message' => 'Có lỗi xảy ra khi tạo bản ghi.'
    //         ];
    //     }
    //     // >>>>>>> 90c1e15630e25b55373c8ddc6b35eb781eac8225
    // }