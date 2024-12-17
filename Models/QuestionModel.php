<?php
include_once __DIR__ . '/../Models/BaseModel.php';
class QuestionModel extends BaseModel
{
    protected $table;
    protected $QuestionModel;
    protected $conn;
    public function __construct()
    {
        $this->conn = ConnectionDB::GetConnect();
        $this->table = 'questions';
        $this->QuestionModel = new BaseModel($this->table);
    }
    public function index()
    {
        // lấy id đầu tiên trong danh mục câu hỏi
        $query2 = $this->conn->query("select id from category_exams limit 1");
        $category = $query2->fetch()->id;
        // phân trang
        $page = (isset($_GET['page']) && $_GET['page'] !== '' && $_GET['page'] !== 'undefined') ? $_GET['page'] : 1;
        // nếu có trường category thì lấy còn không thì category = 0
        $category = (isset($_GET['category']) && $_GET['category'] !== '' && $_GET['category'] !== 'undefined') ? $_GET['category'] : 0;
        // nếu có trường class thì lấy còn không thì mặc định là 0
        $classes = (isset($_GET['class']) && $_GET['class'] !== '' && $_GET['class'] !== 'undefined') ? $_GET['class'] : 0;
        // giới hạn 10 bản ghi một lần
        $limit = 10;
        $offset = ($page - 1) * $limit;
        // nếu danh mục và lớp học bằng 0 thì lấy tất cả câu hỏi
        if (($category == 0 && $classes == 0)) {
            // lấy tổng số bản ghi trong table
            $count_query = $this->conn->prepare("SELECT COUNT(*) as total from $this->table");
            $count_query->execute();
            $record_total = $count_query->fetch(PDO::FETCH_ASSOC)['total'];
            // tổng số trang
            // ceil hàm lấy phần nguyên
            $page_total = ceil($record_total / $limit);
            // lấy danh sách có phân trang
            // nếu trường category không được chọn thì lấy tất
            $query = $this->conn->prepare("SELECT 
            q.id,c.class,q.Subject,q.image,q.title,q.correctAns,q.created_by,q.answerlist
            FROM $this->table q
            INNER JOIN classes c
            ON q.class = c.id
            ORDER BY q.id DESC
            LIMIT :limit
            OFFSET :offset");
            // gán các giá trị nguyên cho limit và offset
            $query->bindParam(':limit', $limit, PDO::PARAM_INT);
            $query->bindParam(':offset', $offset, PDO::PARAM_INT);
            // $query->execute([':limit' => (int)$limit, ':offset' => (int)$offset]);
            $query->execute();
            return ['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total, 'catIf' => $category];
            // return $query->fetchAll();
        }
        // nếu class = 0 thì và category khác 0 thì lấy theo danh mục
        elseif ($classes == 0 && $category !== 0) {
            // lấy tổng số bản ghi trong table
            $count_query = $this->conn->prepare("SELECT COUNT(*) as total from $this->table where Subject=:category");
            $count_query->execute(['category' => $category]);
            $record_total = $count_query->fetch(PDO::FETCH_ASSOC)['total'];
            // tổng số trang
            // ceil hàm lấy phần nguyên
            $page_total = ceil($record_total / $limit);
            // lấy danh sách có phân trang
            // nếu trường category không được chọn thì lấy tất
            $query = $this->conn->prepare("SELECT 
            q.id,c.class,q.Subject,q.image,q.title,q.correctAns,q.created_by,q.answerlist
            FROM $this->table q
            INNER JOIN classes c
            ON q.class = c.id
            WHERE q.Subject=:category
            ORDER BY q.id DESC
            LIMIT :limit
            OFFSET :offset");
            // gán các giá trị nguyên cho limit và offset
            $query->bindParam(':category', $category, PDO::PARAM_INT);
            $query->bindParam(':limit', $limit, PDO::PARAM_INT);
            $query->bindParam(':offset', $offset, PDO::PARAM_INT);
            // $query->execute([':limit' => (int)$limit, ':offset' => (int)$offset]);
            $query->execute();
            return ['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total, 'cat' => $category];
        }
        // nếu class khác 0 còn category bằng 0 thì lấy theo lớp
        elseif ($classes !== 0 && $category == 0) {
            // lấy tổng số bản ghi trong table
            $count_query = $this->conn->prepare("SELECT COUNT(*) as total from $this->table where class=:class");
            $count_query->execute(['class' => $classes]);
            $record_total = $count_query->fetch(PDO::FETCH_ASSOC)['total'];
            // tổng số trang
            // ceil hàm lấy phần nguyên
            $page_total = ceil($record_total / $limit);
            // lấy danh sách có phân trang
            // nếu trường category không được chọn thì lấy tất
            $query = $this->conn->prepare("SELECT 
            q.id,c.class,q.Subject,q.image,q.title,q.correctAns,q.created_by,q.answerlist
            FROM $this->table q
            INNER JOIN classes c
            ON q.class = c.id
            WHERE q.class=:class
            ORDER BY q.id DESC
            LIMIT :limit
            OFFSET :offset");
            // gán các giá trị nguyên cho limit và offset
            $query->bindParam(':class', $classes, PDO::PARAM_INT);
            $query->bindParam(':limit', $limit, PDO::PARAM_INT);
            $query->bindParam(':offset', $offset, PDO::PARAM_INT);
            // $query->execute([':limit' => (int)$limit, ':offset' => (int)$offset]);
            $query->execute();
            return ['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total, 'cat' => $category];
        }
        // nếu không thì lấy theo cả danh mục và lớp học
        else {
            // lấy tổng số bản ghi trong table
            $count_query = $this->conn->prepare("SELECT COUNT(*) as total from $this->table where class=:class and Subject=:category ");
            $count_query->execute(['class' => $classes, 'category' => $category]);
            $record_total = $count_query->fetch(PDO::FETCH_ASSOC)['total'];
            // tổng số trang
            // ceil hàm lấy phần nguyên
            $page_total = ceil($record_total / $limit);
            // lấy danh sách có phân trang
            // nếu trường category không được chọn thì lấy tất
            $query = $this->conn->prepare("SELECT 
            q.id,c.class,q.Subject,q.image,q.title,q.correctAns,q.created_by,q.answerlist
            FROM $this->table q
            INNER JOIN classes c
            ON q.class = c.id
            WHERE q.class=:class
            and Subject=:category
            ORDER BY q.id DESC
            LIMIT :limit
            OFFSET :offset");
            // gán các giá trị nguyên cho limit và offset
            $query->bindParam(':category', $category, PDO::PARAM_INT);
            $query->bindParam(':class', $classes, PDO::PARAM_INT);
            $query->bindParam(':limit', $limit, PDO::PARAM_INT);
            $query->bindParam(':offset', $offset, PDO::PARAM_INT);
            // $query->execute([':limit' => (int)$limit, ':offset' => (int)$offset]);
            $query->execute();
            return ['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total, 'cat' => $category];
        }
    }
    public function checkExtensionImage($Extension)
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $Extensionimage = $Extension['extension'];
        // Validate input
        if (!is_string($Extensionimage) || empty($Extensionimage)) {
            return false;
        }
        // Convert to lowercase and trim whitespace
        $extension = strtolower(trim($Extensionimage));

        // Check if the extension is allowed
        return in_array($extension, $allowedExtensions);
    }
    public function create($data2)
    {
        $data = [];
        foreach ($_POST as $key => $value) {
            // Thêm từng giá trị vào mảng $data với định dạng key => value
            $data[$key] = $value;
        }
        $numberAnswer = json_decode($_POST['answerlist'], true);
        if (isset($_FILES['image'])) {
            $fileInfo = pathinfo($_FILES['image']['name']);
            if ($this->checkExtensionImage($fileInfo) == true) {
                $folder = __DIR__ . '/../assets/image/Question/';
                if (!is_dir($folder)) {
                    mkdir($folder, 0777, true);  // Tạo thư mục với quyền ghi đầy đủ
                }
                $image_name = time() . '_' . basename($_FILES['image']['name']);
                $upload_file = $folder . $image_name;
                $data['image'] = $image_name;
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_file);
            } else {
                echo json_encode(['message' => "File ảnh không đúng định dạng !"]);
                exit;
            }
        } else {
            $data['image'] = '';
        }
        // return $this->QuestionModel->create($data);
        // lấy tên cột từ data;
        $columns = implode(",", array_keys($data));
        // $query = $this->conn->prepare("insert into $this->table ($columns) values ($value) ");
        // // prepare giá trị truyền vào sql
        // // lấy giá trị từ data
        $value = ":" . implode(",:", array_keys($data));
        // // prepare query

        try {
            $this->conn->beginTransaction();
            $query = $this->conn->prepare("insert into $this->table ($columns) values ($value) ");
            $query->execute($data);
            $LastInsertId = $this->conn->lastInsertId();
            foreach ($numberAnswer as $index => $value) {
                if (isset($_FILES["answerImage_$index"])) {
                    $folderImgAnswer = __DIR__ . '/../assets/image/AnswerQuestion/';
                    if (!is_dir($folderImgAnswer)) {
                        mkdir($folderImgAnswer, 0777, true);  // Tạo thư mục với quyền ghi đầy đủ
                    }
                    // tên file ảnh
                    $imageQuestionName = time() . '_' . basename($_FILES["answerImage_$index"]['name']);
                    $upload_file_ImgAnswer = $folderImgAnswer . $imageQuestionName;
                    // lưu file vào đường dẫn
                    move_uploaded_file($_FILES["answerImage_$index"]['tmp_name'], $upload_file_ImgAnswer);
                    // thêm ảnh vào bảng
                    $queryImage = $this->conn->prepare("insert into image_answers set idQues=:idQues,imageAns=:image,stt=:stt");
                    $queryImage->execute(['idQues' => $LastInsertId, 'image' => $imageQuestionName, 'stt' => $index]);
                }
            }
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();
            echo json_encode(['message' => "Có lỗi xảy ra " . $e]);
        }
        echo json_encode(['message' => "Thêm thành công"]);
    }
    public function read($id)
    {
        return $this->QuestionModel->read($id);
    }
    // lấy ảnh của câu trả lời
    public function getImageAnswerModel($id)
    {

        $query = $this->conn->prepare("select * from image_answers where idQues=:id");
        $query->execute(['id' => $id]);
        return $query->fetchAll();
    }
    public function delete($id)
    {
        try {
            $this->conn->beginTransaction();
            // xóa hình ảnh câu hỏi nếu có
            // Folder ảnh đề bài
            $folder = __DIR__ . '/../assets/image/Question/';
            // Folder ảnh câu hỏi
            $folderImgAnswer = __DIR__ . '/../assets/image/AnswerQuestion/';

            // Xóa ảnh các câu trả lời của câu hỏi
            $query3 = $this->conn->prepare("select imageAns from image_answers where idQues=:id");
            $query3->execute(['id' => $id]);
            foreach ($query3->fetchAll() as $row) {
                $oldImg = $row->imageAns;
                if (!empty($oldImg)) {
                    $oldFileImageAnswer = $folderImgAnswer . $oldImg;
                    if (file_exists($oldFileImageAnswer)) {
                        unlink($oldFileImageAnswer);
                    }
                }
            }
            // Xóa ảnh đề bài của câu hỏi
            $query2 = $this->conn->prepare("select image from $this->table where id=:id");
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
            $query = $this->conn->prepare("delete from $this->table where id=:id");
            $query->execute(['id' => $id]);
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();
            return false;
        }
        return true;
    }
    // xóa câu hỏi trong bài kiểm tra
    public function deleteQuestionInExamModel($idQues, $idExam)
    {
        try {
            if ($this->delete($idQues) == true) {
                $this->conn->beginTransaction();
                $queryTotalQuestionExam = $this->conn->prepare("select id,totalQuestion from exams where id=:id");
                $queryTotalQuestionExam->execute(['id' => $idExam]);
                $TotalQuestion = $queryTotalQuestionExam->fetch()->totalQuestion;
                // cập nhật lại số lượng câu hỏi của bài thi sau khi đã xóa câu hỏi
                $query = $this->conn->prepare("update exams set totalQuestion=:total where id=:id");
                $query->execute(['total' => $TotalQuestion - 1, 'id' => $idExam]);
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (Throwable $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    public function updateQuestion($id)
    {
        $data = [];
        foreach ($_POST as $key => $value) {
            // Thêm từng giá trị vào mảng $data với định dạng key => value
            $data[$key] = $value;
        }
        // xóa bỏ các phần tử có key là answerImage trong mảng 
        foreach ($data as $key => $value) {
            if (strpos($key, 'answerImage_') !== false) {
                unset($data[$key]);
            }
        }
        $numberAnswer = json_decode($_POST['answerlist'], true);
        // link file ảnh câu hỏi
        $folder = __DIR__ . '/../assets/image/Question/';
        // link file ảnh câu trả lời
        $folderImgAnswer = __DIR__ . '/../assets/image/AnswerQuestion/';
        if (isset($_FILES['image'])) {
            $fileInfo = pathinfo($_FILES['image']['name']);
            if ($this->checkExtensionImage($fileInfo) == true) {
                if (!is_dir($folder)) {
                    mkdir($folder, 0777, true);  // Tạo thư mục với quyền ghi đầy đủ
                }
                $image_name = time() . '_' . basename($_FILES['image']['name']);
                $upload_file = $folder . $image_name;
                $data['image'] = $image_name;
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_file);
            } else {
                echo json_encode(['message' => 'File ảnh không đúng định dạng']);
                exit;
            }
        } else {
            // if (!empty($data['image'])) {
            //     $data['image'] = $data['image'];
            // } else {
            $data['image'] = '';
            // }
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
            $this->conn->beginTransaction();
            $query2 = $this->conn->prepare("select * from $this->table where id=:id LIMIT 1");
            $query2->execute(['id' => $id]);
            $result = $query2->fetch();
            $img = $result->image;
            $oldFileImage = $folder . $img;
            // if (file_exists($oldFileImage)) {
            //     unlink($oldFileImage);
            // }
            $query = $this->conn->prepare("update $this->table set $setClause where id=:id");
            $arrayId = ['id' => $id];
            //merge mảng để execute query
            $arrayData = array_merge($data, $arrayId);
            $query->execute($arrayData);
            // duyệt mảng ảnh câu trả lời nếu tồn tại thì tiến hành update trong db và file ảnh
            foreach ($numberAnswer as $index => $value) {
                // kiểm tra xem có tồn tại file ảnh post lên không
                // nếu có thì tiến hành update
                if (isset($_FILES["answerImage_$index"])) {
                    // lấy ảnh từ bảng
                    $getOldImageAnswer = $this->conn->prepare("select imageAns from image_answers where idQues=:id and stt=:stt");
                    $getOldImageAnswer->execute(['id' => $id, 'stt' => $index]);
                    $result = $getOldImageAnswer->fetch();
                    $OldImageAnswer = $result->imageAns;
                    $OldImageAnswerFile = $folderImgAnswer . $OldImageAnswer;
                    if (!is_dir($folderImgAnswer)) {
                        mkdir($folderImgAnswer, 0777, true);  // Tạo thư mục với quyền ghi đầy đủ
                    }
                    // kiểm tra xem trong file có ảnh này chưa nếu có thì tiến hành xóa để update ảnh mới
                    if (file_exists($OldImageAnswerFile)) {
                        unlink($OldImageAnswerFile);
                    }
                    // update hình ảnh mới
                    $imageQuestionName = time() . '_' . basename($_FILES["answerImage_$index"]['name']);
                    // tên file ảnh
                    $upload_file_ImgAnswer = $folderImgAnswer . $imageQuestionName;
                    move_uploaded_file($_FILES["answerImage_$index"]['tmp_name'], $upload_file_ImgAnswer);
                    // cập nhật lại hình ảnh trong table
                    $queryImage2 = $this->conn->prepare("update image_answers set imageAns=:imageAns where idQues=:idQues and stt=:stt");
                    $queryImage2->execute(['idQues' => $id, 'imageAns' => $imageQuestionName, 'stt' => $index]);
                }
                // Nếu không có thì người dùng đã xóa ảnh câu trả lời => tiến hành xóa ảnh trong folder và table
                else {
                    // // lấy ảnh từ bảng
                    $getOldImageAnswer = $this->conn->prepare("select imageAns from image_answers where idQues=:id and stt=:stt");
                    $getOldImageAnswer->execute(['id' => $id, 'stt' => $index]);
                    $result = $getOldImageAnswer->fetch();
                    // kiểm tra xem dữ liệu lấy ra có rỗng hay không
                    if ($result && !empty($result->imageAns)) {
                        $OldImageAnswer = $result->imageAns;
                        $OldImageAnswerFile = $folderImgAnswer . $OldImageAnswer;
                        if (file_exists($OldImageAnswerFile)) {
                            unlink($OldImageAnswerFile);
                        }
                        // xóa ảnh của câu trả lời
                        $query3 = $this->conn->prepare("delete from image_answers where idQues=:id and stt=:stt");
                        $query3->execute(['id' => $id, 'stt' => $index]);
                    }
                }
            }
            // Xóa ảnh của đề bài
            // nếu sau khi update người dùng xóa ảnh của câu hỏi đi thì xóa ảnh của câu hỏi trong folder
            $query4 = $this->conn->prepare("select image from $this->table where id=:id");
            $query4->execute(['id' => $id]);
            $result = $query4->fetch();
            if ($result && !empty($result4->image)) {
                $img2 = $result->image;
                if (!empty($img2)) {
                    // $oldFileImage = $folder . $img;
                    if (file_exists($oldFileImage)) {
                        unlink($oldFileImage);
                    }
                }
            }
            $this->conn->commit();
            return true;
        } catch (Throwable $e) {
            $this->conn->rollBack();
            return false;
            // throw $e;
            // echo json_encode($arrayData);
        }

        // echo json_encode(2);
    }
    public function getUserCreate()
    {

        try {
            $query = $this->conn->prepare("select name,id from users where role=:role");
            $query->execute(['role' => 'admin']);
        } catch (Throwable $e) {
            echo json_encode(['message' => "Lỗi" . $e]);
        }
        echo json_encode(['data' => $query->fetchAll()]);
    }
    // thêm câu hỏi vào bài kiểm tra tùy chọn
    public function AddQuestionIntoExamOptionModel($id)
    {
        try {
            $data = [];
            foreach ($_POST as $key => $value) {
                // Thêm từng giá trị vào mảng $data với định dạng key => value
                $data[$key] = $value;
            }
            $numberAnswer = json_decode($_POST['answerlist'], true);
            if (isset($_FILES['image'])) {
                $fileInfo = pathinfo($_FILES['image']['name']);
                if ($this->checkExtensionImage($fileInfo) == true) {
                    $folder = __DIR__ . '/../assets/image/Question/';
                    if (!is_dir($folder)) {
                        mkdir($folder, 0777, true);  // Tạo thư mục với quyền ghi đầy đủ
                    }
                    $image_name = time() . '_' . basename($_FILES['image']['name']);
                    $upload_file = $folder . $image_name;
                    $data['image'] = $image_name;
                    move_uploaded_file($_FILES['image']['tmp_name'], $upload_file);
                } else {
                    echo json_encode(['message' => "File ảnh không đúng định dạng !"]);
                    exit;
                }
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
            // bắt đầu transaction
            $this->conn->beginTransaction();
            $query = $this->conn->prepare("insert into $this->table ($columns) values ($value) ");
            $query->execute($data);
            $LastInsertId = $this->conn->lastInsertId();
            foreach ($numberAnswer as $index => $value) {
                if (isset($_FILES["answerImage_$index"])) {
                    $folderImgAnswer = __DIR__ . '/../assets/image/AnswerQuestion/';
                    if (!is_dir($folderImgAnswer)) {
                        mkdir($folderImgAnswer, 0777, true);  // Tạo thư mục với quyền ghi đầy đủ
                    }
                    // tên file ảnh
                    $imageQuestionName = time() . '_' . basename($_FILES["answerImage_$index"]['name']);
                    $upload_file_ImgAnswer = $folderImgAnswer . $imageQuestionName;
                    // lưu file vào đường dẫn
                    move_uploaded_file($_FILES["answerImage_$index"]['tmp_name'], $upload_file_ImgAnswer);
                    // thêm ảnh vào bảng
                    $queryImage = $this->conn->prepare("insert into image_answers set idQues=:idQues,imageAns=:image,stt=:stt");
                    $queryImage->execute(['idQues' => $LastInsertId, 'image' => $imageQuestionName, 'stt' => $index]);
                }
            }
            // thêm câu hỏi vào bài thi
            $queryInsertQuestionIntoExam = $this->conn->prepare("insert into questions_exam (id_ques,id_exam) values (:id_ques,:id_exam)");
            $queryInsertQuestionIntoExam->execute(['id_ques' => $LastInsertId, 'id_exam' => $id]);
            // cập nhật lại tổng số lượng câu hỏi trong bài thi
            $CurrentTotalQuestionInExamQuery = $this->conn->prepare("select id,totalQuestion from exams where id=:id");
            $CurrentTotalQuestionInExamQuery->execute(['id' => $id]);

            $CurrentTotalQuestionNumber = ($CurrentTotalQuestionInExamQuery->fetch())->totalQuestion;
            // cập nhật số lượng câu hỏi vào bảng exam
            $updateTotalQuestionQuery = $this->conn->prepare("update exams set totalQuestion=:total where id=:id");
            $updateTotalQuestionQuery->execute(['total' => $CurrentTotalQuestionNumber + 1, 'id' => $id]);
            // commit query
            $this->conn->commit();
            return true;
        } catch (Throwable $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
