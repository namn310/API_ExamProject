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
        return $this->QuestionModel->index();
    }
    public function checkExtensionImage($Extensionimage)
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        if (in_array(strtolower($Extensionimage), $allowedExtensions)) {
            return true;
        } else {
            return false;
        }
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
        $query = $this->conn->prepare("insert into $this->table ($columns) values ($value) ");
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
    public function update($data2, $id)
    {
        $data = [];
        foreach ($_POST as $key => $value) {
            // Thêm từng giá trị vào mảng $data với định dạng key => value
            $data[$key] = $value;
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
                echo json_encode(['message' => "File ảnh không đúng định dạng !"]);
                exit;
            }
        } else {
            if (!empty($data['image'])) {
                $data['image'] = $data['image'];
            } else {
                $data['image'] = '';
            }
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
            if (file_exists($oldFileImage)) {
                unlink($oldFileImage);
            }
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
                    // $getOldImageAnswer = $this->conn->prepare("select imageAns from image_answers where idQues=:id and stt=:stt");
                    // $getOldImageAnswer->execute(['id' => $id, 'stt' => $index]);
                    // $result = $getOldImageAnswer->fetch();
                    // $OldImageAnswer = $result->imageAns;
                    // $OldImageAnswerFile = $folderImgAnswer . $OldImageAnswer;
                    // if (file_exists($OldImageAnswerFile)) {
                    //     unlink($OldImageAnswerFile);
                    // }
                    // // xóa ảnh của câu trả lời
                    // $query3 = $this->conn->prepare("delete from image_answers where idQues=:id and stt=:stt");
                    // $query3->execute(['id'=>$id,'stt'=>$index]);
                }
            }
            // Xóa ảnh của đề bài
            // nếu sau khi update người dùng xóa ảnh của câu hỏi đi thì xóa ảnh của câu hỏi trong folder
            $query4 = $this->conn->prepare("select image from $this->table where id=:id");
            $query4->execute(['id' => $id]);
            $result = $query4->fetch();
            $img2 = $result->image;
            if (!empty($img2)) {
                // $oldFileImage = $folder . $img;
                if (file_exists($oldFileImage)) {
                    unlink($oldFileImage);
                }
            }
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();
            return false;
            // echo json_encode($e);
        }
        return true;
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
}
