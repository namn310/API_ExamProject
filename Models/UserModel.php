<?php
include_once __DIR__ . '/../Models/BaseModel.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use React\Socket\Connector;

class UserModel extends BaseModel
{

    protected $table;
    protected $UserModel;
    protected $conn;
    public function __construct()
    {
        $this->conn = ConnectionDB::GetConnect();
        $this->table = 'users';
        $this->UserModel = new BaseModel($this->table);
    }
    public function index()
    {
        // phân trang
        $page = (isset($_GET['page']) && $_GET['page'] !== '' && $_GET['page'] !== 'undefined') ? $_GET['page'] : 1;
        $role = (isset($_GET['role']) && $_GET['role'] !== '' && $_GET['role'] !== 'undefined') ? $_GET['role'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        if ($role !== 'admin' && $role !== 'student' && $role !== 'teacher') {
            // lấy tổng số bản ghi trong table
            $count_query = $this->conn->prepare("SELECT COUNT(*) as total from $this->table");
            $count_query->execute();
            $record_total = $count_query->fetch(PDO::FETCH_ASSOC)['total'];
            // tổng số trang
            // ceil hàm lấy phần nguyên
            $page_total = ceil($record_total / $limit);
            // lấy danh sách có phân trang
            // nếu trường category không được chọn thì lấy tất
            $query = $this->conn->prepare("select id,name,email,role,create_at from $this->table LIMIT :limit OFFSET :offset");
            // gán các giá trị nguyên cho limit và offset
            $query->bindParam(':limit', $limit, PDO::PARAM_INT);
            $query->bindParam(':offset', $offset, PDO::PARAM_INT);
            // $query->execute([':limit' => (int)$limit, ':offset' => (int)$offset]);
            $query->execute();
            return ['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total];
            // return $query->fetchAll();
        } else {
            // lấy tổng số bản ghi trong table
            $count_query = $this->conn->prepare("SELECT COUNT(*) as total from $this->table where role=:role");
            $count_query->execute(['role' => $role]);
            $record_total = $count_query->fetch(PDO::FETCH_ASSOC)['total'];
            // tổng số trang
            // ceil hàm lấy phần nguyên
            $page_total = ceil($record_total / $limit);
            // lấy danh sách có phân trang
            // nếu trường category không được chọn thì lấy tất
            $query = $this->conn->prepare("select id,name,email,role,create_at from $this->table where role=:role LIMIT :limit OFFSET :offset");
            // gán các giá trị nguyên cho limit và offset
            $query->bindParam(':role', $role);
            $query->bindParam(':limit', $limit, PDO::PARAM_INT);
            $query->bindParam(':offset', $offset, PDO::PARAM_INT);
            // $query->execute([':limit' => (int)$limit, ':offset' => (int)$offset]);
            $query->execute();
            return ['data' => $query->fetchAll(), 'limit' => $limit, 'current_page' => $page, 'total_page' => $page_total, 'record_total' => $record_total];
            // return $query->fetchAll();
        }
    }
    public function sendOTPToRegisterModel($data)
    {
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $email = rtrim($data['email']);
        $token = $data['tk'];
        $roleAccount = $data['role'];
        $typeToken = $data['type_token'];
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = getenv('MAIL_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = getenv('MAIL_USERNAME');
        $mail->Password = getenv('MAIL_PASSWORD');
        $mail->SMTPSecure = getenv('MAIL_ENCRYPTION');
        $mail->Port = getenv('MAIL_PORT');
        $mail->setFrom(getenv('MAIL_USERNAME'), 'ExamTutor');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'ResetPass';
        // $resetLinkGmail = $resetLink . '?token=' . urlencode($token);
        $OTP = $this->generateOTP();
        // nếu muốn dùng html trong Body thì dùng EOD
        $mail->Body =
            <<<EOD
                    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu của bạn</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: green;
            text-decoration: none;
            border-radius: 5px;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }

        .a {
            text-decoration: none;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <h2>Xin chào,</h2>
        <p>Đây là mã OTP xác nhận đăng ký tài khoản của bạn</p>

        <p style="text-align: center;font-size:30px">
            <strong>$OTP</strong>
        </p>

        <p>
            Lưu ý: Mã OTP này sẽ hết hạn sau <strong>30 phút</strong> để đảm bảo an toàn và <strong>tuyệt đối không chia
                sẻ mã này cho người khác</strong>. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này,
            tài khoản của bạn sẽ không bị ảnh hưởng.
        </p>

        <div class="footer">
            <p>Trân trọng,</p>
            <p>Đội ngũ hỗ trợ khách hàng - ExamTutor</p>
        </div>

        <hr>
    </div>
</body>

</html>
EOD;
        // echo json_encode($mail->Body);
        try {
            // kiểm tra xem thể loại token là gì
            //  - nếu là token đăng ký thì vào TH1
            //  - nếu là token để active account thì vào TH2

            // TH2
            if ($typeToken === 'activeAccount') {
                $mail->send();
                $now = new DateTime();
                $CurrentDateTime = $now->format('Y-m-d H:i:s');
                $this->conn->beginTransaction();
                $query2 = $this->conn->prepare("insert into token_confirm_regist_and_change_pass (email,token,OTP,role_account,type_token,created_at) values (:email,:token,:OTP,:role,:type,:create)");
                $query2->execute(['email' => $email, 'token' => $token, 'OTP' => $OTP, 'role' => $roleAccount, 'type' => $typeToken, 'create' => $CurrentDateTime]);
                $this->conn->commit();
                echo json_encode(['message' => 'Mã OTP đã được gửi']);
            } else {
                // TH1
                // kiểm tra xem email người dùng nhập đã tồn tại trong hệ thống hay chưa
                $query = $this->conn->prepare("select id,email from users where email=:email and role=:role");
                $query->execute(['email' => $email, 'role' => $roleAccount]);
                if ($query->rowCount() > 0) {
                    echo json_encode(['message' => 'Email đã tồn tại !']);
                } else {
                    $mail->send();
                    $now = new DateTime();
                    $CurrentDateTime = $now->format('Y-m-d H:i:s');
                    $this->conn->beginTransaction();
                    $query2 = $this->conn->prepare("insert into token_confirm_regist_and_change_pass (email,token,OTP,role_account,type_token,created_at) values (:email,:token,:OTP,:role,:type,:create)");
                    $query2->execute(['email' => $email, 'token' => $token, 'OTP' => $OTP, 'role' => $roleAccount, 'type' => $typeToken, 'create' => $CurrentDateTime]);
                    $this->conn->commit();
                    echo json_encode(['message' => 'Mã OTP đã được gửi']);
                }
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            echo json_encode(['message' => 'Không thể gửi email. Lỗi: ' . $mail->ErrorInfo]);
        }
    }
    // đăng ký tài khoản
    public function createUser($data)
    {
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $name = $data['name'];
        $email = $data['email'];
        $pass = md5($data['password']);
        $role = $data['role'];
        $OTP = $data['OTP'];
        $token = $data['token'];
        try {
            $this->conn->beginTransaction();
            $query = $this->conn->prepare("select id from $this->table where email=:email");
            $query->execute(['email' => $email]);
            if ($query->rowCount() > 0) {
                echo json_encode(['message' => 'Email đã tồn tại']);
            } else {
                // kiểm tra token và OTP 
                $now = new DateTime();
                $CurrentDateTime = $now->format('Y-m-d H:i:s');
                $query3 = $this->conn->prepare("select * from token_confirm_regist_and_change_pass where email=:email and token=:token and OTP=:OTP order by id desc limit 1");
                $query3->execute(['email' => $email, 'token' => $token, 'OTP' => $OTP]);
                // nếu tồn tại thì thêm người dùng mới
                if ($query3->rowCount() > 0) {
                    $query2 = $this->conn->prepare("insert into $this->table (name,password,email,role,active) values (:name,:pass,:email,:role,:active)");
                    $query2->execute(['name' => $name, 'pass' => $pass, 'email' => $email, 'role' => $role, 'active' => 1]);
                    // xóa token của email này
                    $query4 = $this->conn->prepare("delete from token_confirm_regist_and_change_pass where email =:email ");
                    $query4->execute(['email' => $email]);
                    // xóa token đã quá hạn 
                    $query5 = $this->conn->query("select created_at,id from token_confirm_regist_and_change_pass");
                    if ($query5->rowCount() > 1) {
                        foreach ($query5->fetchAll() as $row) {
                            $id = $row['id'];
                            $TimeCreatedToTimeStamp = (new DateTime($query5['created_at']))->getTimestamp();
                            $diffInSecond = ($now->getTimestamp() - $TimeCreatedToTimeStamp);
                            // nếu diffInSecond lớn hơn 1800 thì đã hết hạn tiến hành xóa
                            if ($diffInSecond > 1800) {
                                $query6 = $this->conn->prepare("delete from token_confirm_regist_and_change_pass where id=:id ");
                                $query6->execute(['id' => $id]);
                            }
                        }
                    }
                    $this->conn->commit();
                    echo json_encode(['message' => 'Đăng ký tài khoản thành công']);
                }
                // còn không thì là OTP sai
                else {
                    echo json_encode(['message' => 'Mã OTP không hợp lệ']);
                }
            }
        } catch (Throwable $e) {
            $this->conn->rollBack();
            echo json_encode(['message' => $e]);
        }
    }
    //active tài khoản người dùng
    public function activeAccountModel($data)
    {
        $email = $data['email'];
        $role = $data['role'];
        $OTP = $data['OTP'];
        $token = $data['token'];
        $type_token = $data['type_token'];
        try {
            $this->conn->beginTransaction();
            $query = $this->conn->prepare("select id from $this->table where email=:email and active =:active and role=:role limit 1");
            $query->execute(['email' => $email, 'active' => 0, 'role' => $role]);
            if ($query->rowCount() > 0) {
                // kiểm tra token và OTP 
                $account = $query->fetch(PDO::FETCH_ASSOC);
                $idAccount = $account['id'];
                $now = new DateTime();
                $CurrentDateTime = $now->format('Y-m-d H:i:s');
                $query3 = $this->conn->prepare("select * from token_confirm_regist_and_change_pass where email=:email and token=:token and OTP=:OTP and type_token=:typeToken order by id desc limit 1");
                $query3->execute(['email' => $email, 'token' => $token, 'OTP' => $OTP, 'typeToken' => $type_token]);
                // nếu tồn tại thì thêm người dùng mới
                if ($query3->rowCount() > 0) {
                    $query2 = $this->conn->prepare("update users set active=:active where id=:id");
                    $query2->execute(['id' => $idAccount, 'active' => 1]);
                    // xóa token của email này
                    $query4 = $this->conn->prepare("delete from token_confirm_regist_and_change_pass where email =:email ");
                    $query4->execute(['email' => $email]);
                    // xóa token đã quá hạn 
                    $query5 = $this->conn->query("select created_at,id from token_confirm_regist_and_change_pass");
                    if ($query5->rowCount() > 1) {
                        foreach ($query5->fetchAll() as $row) {
                            $id = $row['id'];
                            $TimeCreatedToTimeStamp = (new DateTime($query5['created_at']))->getTimestamp();
                            $diffInSecond = ($now->getTimestamp() - $TimeCreatedToTimeStamp);
                            // nếu diffInSecond lớn hơn 1800 thì đã hết hạn tiến hành xóa
                            if ($diffInSecond > 1800) {
                                $query6 = $this->conn->prepare("delete from token_confirm_regist_and_change_pass where id=:id ");
                                $query6->execute(['id' => $id]);
                            }
                        }
                    }
                    $this->conn->commit();
                    echo json_encode(['message' => 'Xác thực tài khoản thành công']);
                }
                // còn không thì là OTP sai
                else {
                    echo json_encode(['message' => 'Mã OTP không hợp lệ']);
                }
            } else {
                echo json_encode(['message' => 'Email không tồn tại']);
            }
        } catch (Throwable $e) {
            $this->conn->rollBack();
            echo json_encode(['message' => $e]);
            // throw $e;
        }
    }
    public function read($id)
    {
        $query = $this->conn->prepare("select id,name,email,role,create_at from $this->table where id=:id");
        $query->execute(['id' => $id]);
        return $query->fetch();
    }
    public function delete($id)
    {
        return $this->UserModel->delete($id);
    }
    public function update($data, $id)
    {
        $this->UserModel->update($data, $id);
    }
    public function updateEmailModel($data, $id)
    {
        $pass = md5($data['password']);
        $email = $data['email'];
        try {
            $this->conn->beginTransaction();
            // kiểm tra xem mật khẩu có đúng không
            $query2 = $this->conn->prepare("select id,password from users where id=:id and password=:pass");
            $query2->execute(['id' => $id, 'pass' => $pass]);
            if ($query2->rowCount() > 0) {
                $query1 = $this->conn->prepare("select email from users where email=:email");
                $query1->execute(['email' => $email]);
                if ($query1->rowCount() > 0) {
                    echo json_encode(['message' => 'Email đã tồn tại']);
                } else {

                    $query = $this->conn->prepare("update users set email=:email where id=:id");
                    $query->execute(['email' => $email, 'id' => $id]);
                    $this->conn->commit();
                    echo json_encode(['message' => 'Cập nhật thành công !']);
                }
            } else {
                // $this->conn->commit();
                echo json_encode(['message' => 'Mật khẩu không chính xác !']);
            }
        } catch (Throwable $e) {
            $this->conn->rollBack();
            echo json_encode(['message' => 'Có lỗi xảy ra']);
            // throw $e;
        }
    }
    public function updatePassAdmin($data, $id)
    {
        $oldpass = json_decode($data['oldpass']);
        $newpass = json_decode($data['newpass']);

        $query = $this->conn->prepare("select id from users where id=:id and password=:password limit 1");
        $query->execute(['id' => $id, 'password' => $oldpass]);
        if ($query->rowCount() > 0) {
            try {
                $this->conn->beginTransaction();
                $query2 = $this->conn->prepare("update users set password=:password where id=:id");
                $query2->execute(['password' => $newpass, 'id' => $id]);
                $this->conn->commit();
            } catch (Throwable $e) {
                $this->conn->rollBack();
                echo json_encode($e);
            }
            echo json_encode('Cập nhật mật khẩu thành công');
        } else {
            echo json_encode("Mật khẩu không chính xác");
        }
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
    public function LoginModel($data)
    {
        $key = getenv('KEY');
        try {
            $email = $data['email'];
            $pass = md5($data['password']);
            $role = $data['role'];
            $query = $this->conn->prepare("select * from $this->table where email=:email and password=:password and role=:role LIMIT 1");
            $query->execute(['email' => $email, 'password' => $pass, 'role' => $role]);
            $user = $query->fetch(PDO::FETCH_ASSOC);
            if ($query->rowCount() > 0) {
                $CheckActive = $user['active'];
                $timeCreate = time();
                $timeExpire = time() + 86400;
                if ($role === 'admin') {
                    // kiểm tra xem tài khoản đã được active hay chưa
                    if ($CheckActive > 0) {
                        $payload = [
                            'iat' => $timeCreate,
                            'exp' => $timeExpire,
                            'data' => [
                                'id' => $user['id'],
                                'email' => $user['email'],
                                'name' => $user['name'],
                                'role' => $user['role'],
                                'type_account' => 'account'
                            ]
                        ];
                        $jwt = JWT::encode($payload, $key, 'HS256');
                        echo json_encode([
                            'message' => 'Đăng nhập thành công !',
                            'jwtAdmin' => $jwt,
                        ]);
                    } else {
                        echo json_encode([
                            'message' => 'Tài khoản chưa được kích hoạt ! Vui lòng kích hoạt tài khoản',
                            'jwtAdmin' => null,
                        ]);
                    }
                }
                if ($role === 'student') {
                    if ($CheckActive > 0) {
                        $payload = [
                            'iat' => $timeCreate,
                            'exp' => $timeExpire,
                            'data' => [
                                'id' => $user['id'],
                                'email' => $user['email'],
                                'name' => $user['name'],
                                'role' => $user['role'],
                                'type_account' => 'account'
                            ]
                        ];
                        $jwt = JWT::encode($payload, $key, 'HS256');
                        echo json_encode([
                            'message' => 'Đăng nhập thành công !',
                            'jwtStudent' => $jwt,
                        ]);
                    } else {
                        echo json_encode([
                            'message' => 'Tài khoản chưa được kích hoạt ! Vui lòng kích hoạt tài khoản',
                            'jwtStudent' => null,
                        ]);
                    }
                }
                if ($role === 'teacher') {
                    if ($CheckActive > 0) {
                        $payload = [
                            'iat' => $timeCreate,
                            'exp' => $timeExpire,
                            'data' => [
                                'id' => $user['id'],
                                'email' => $user['email'],
                                'name' => $user['name'],
                                'role' => $user['role'],
                                'type_account' => 'account'
                            ]
                        ];
                        $jwt = JWT::encode($payload, $key, 'HS256');
                        echo json_encode([
                            'message' => 'Đăng nhập thành công !',
                            'jwtAdmin' => $jwt,
                        ]);
                    } else {
                        echo json_encode([
                            'message' => 'Tài khoản chưa được kích hoạt ! Vui lòng kích hoạt tài khoản',
                            'jwtAdmin' => null,
                        ]);
                    }
                }
            } else {
                echo json_encode(['message' => 'Đăng nhập thất bại ! Tài khoản hoặc mật khẩu không chính xác']);
            }
        } catch (Throwable $e) {
            echo json_encode(['message' => "Có lỗi xảy ra " . $e]);
        }
    }
    public function loginGoogleModel($data)
    {
        $key = getenv('KEY');
        $token = $data['token'] ?? '';
        $role = $data['role'] ?? '';
        // echo json_encode($token);
        $googleClientId = getenv('GOOGLE_CLIENT_ID');
        $googleClientSecret = getenv('GOOGLE_CLIENT_SECRET');
        $googleClientUri = getenv('GOOGLE_REDIRECT_URI');
        $client = new Google_Client();
        $client->setClientId($googleClientId);
        try {
            $payload = $client->verifyIdToken($token);
            if ($payload) {
                $userId = $payload['sub'];  // Google user ID
                $userEmail = $payload['email'];
                $userName = $payload['name'];
                // kiểm tra xem tài khoản đã tồn tại trong hệ thống chưa

                $query1 = $this->conn->prepare("select id,name,email,type_account,id_account_social,role from users where (type_account=:type_account and role=:role) and (id_account_social=:id and email=:email) ");
                $query1->execute([
                    'type_account' => 'google',
                    'id' => $userId,
                    'email' => $userEmail,
                    'role' => $role
                ]);
                if ($query1->rowCount() > 0) {
                    $result = $query1->fetch(PDO::FETCH_ASSOC);
                    $id = $result['id'];
                    $role = $result['role'];
                }
                // nếu chưa có thì tạo mới vào bảng users
                else {
                    $this->conn->beginTransaction();
                    $query2 = $this->conn->prepare("insert into users (name,password,email,role,type_account,id_account_social,active) values (:name,:password,:email,:role,:type_account,:id_account_social,:active)");
                    $hashPassword = hash("sha256", `$userEmail` . `$userName`);
                    $query2->execute([
                        'name' => $userName,
                        'password' => $hashPassword,
                        'email' => $userEmail,
                        'role' => $role,
                        'type_account' => 'google',
                        'id_account_social' => $userId,
                        'active' => 1
                    ]);
                    $this->conn->commit();
                    $id = $this->conn->lastInsertId();
                }
                // tạo token đăng nhập
                $timeCreate = time();
                $timeExpire = time() + 86400;
                if ($role == 'student') {
                    $payload = [
                        'iat' => $timeCreate,
                        'exp' => $timeExpire,
                        'data' => [
                            'id' => $id,
                            'email' => $userEmail,
                            'name' => $userName,
                            'role' => $role,
                            'type_account' => 'google'
                        ]
                    ];
                    $jwt = JWT::encode($payload, $key, 'HS256');
                    echo json_encode([
                        'message' => 'Đăng nhập thành công !',
                        'jwtStudent' => $jwt,
                    ]);
                }
                if ($role == 'admin') {
                    $payload = [
                        'iat' => $timeCreate,
                        'exp' => $timeExpire,
                        'data' => [
                            'id' => $id,
                            'email' => $userEmail,
                            'name' => $userName,
                            'role' => $role,
                            'type_account' => 'google'
                        ]
                    ];
                    $jwt = JWT::encode($payload, $key, 'HS256');
                    echo json_encode([
                        'message' => 'Đăng nhập thành công !',
                        'jwtAdmin' => $jwt,
                    ]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid token']);
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error verifying token: ' . $e->getMessage()]);
        }
    }
    public function resetPasswordModel($data)
    {
        $key = getenv('KEY');
        $token = $data['token'];
        $newPassword = md5($data['new_password']);
        $oldPasswordInput = md5($data['old_password']);
        try {
            $this->conn->beginTransaction();
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            // echo json_encode($decoded->data->id);
            $userId = $decoded->data->id;
            $userEmail = $decoded->data->email;
            $query = $this->conn->prepare("select id,password from $this->table where id=:id and email=:email");
            $query->execute(['id' => $userId, 'email' => $userEmail]);
            if ($query->rowCount() > 0) {
                $result = $query->fetch();
                $oldPassword = $result->password;
                // so sánh mật khẩu cũ nhập vào với mật khẩu trong database
                if ($oldPassword === $oldPasswordInput) {
                    $updated = $this->conn->prepare("update $this->table set password=:password where id=:id and email=:email ");
                    $updated->execute(['password' => $newPassword, 'id' => $userId, 'email' => $userEmail]);
                    echo json_encode(['message' => 'Đổi mật khẩu thành công !']);
                } else {
                    echo json_encode(['message' => 'Mật khẩu cũ không chính xác']);
                }
            }
            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            echo json_encode(['message' => 'Có lỗi xảy ra !' . $e->getMessage()]);
        }
    }
    public function forgotPasswordModel($data)
    {

        $key = getenv('KEY');
        // $data = json_decode($data);
        try {
            $query = $this->conn->prepare("select id,email from $this->table where email=:email");
            $query->execute(['email' => trim($data['email'])]);
            if ($query->rowCount() > 0) {
                $user = $query->fetch(PDO::FETCH_ASSOC);
                $userId = $user['id'];
                $userEmail = $user['email'];
                // tạo một token quên mật khẩu gửi về client
                // life time là 30 phút
                $payload = [
                    'data' => [
                        'iat' => time(),
                        'exp' => time() + 1800,
                        'id' => $userId,
                        'email' => $userEmail,
                    ]
                ];
                $tokenResetPass = JWT::encode($payload, $key, 'HS256');
                // $resetLink = "http://localhost:5173/reset-passwordForgot";
                $this->EmailReset($userEmail, $tokenResetPass);
                echo json_encode(['message' => 'Email đã gửi !', 'tokenResetPass' => $tokenResetPass]);
            } else {
                echo json_encode(['message' => 'Email không tồn tại !']);
            }
        } catch (Throwable $e) {
            echo json_encode(['message' => 'Có lỗi xảy ra !' . $e]);
        }
    }
    private function EmailReset($email, $token)
    {
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = getenv('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('MAIL_USERNAME');
            $mail->Password = getenv('MAIL_PASSWORD');
            $mail->SMTPSecure = getenv('MAIL_ENCRYPTION');
            $mail->Port = getenv('MAIL_PORT');
            $mail->setFrom(getenv('MAIL_USERNAME'), 'ExamTutor');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'ResetPass';
            // $resetLinkGmail = $resetLink . '?token=' . urlencode($token);
            $OTP = $this->generateOTP();
            // nếu muốn dùng html trong Body thì dùng EOD
            $mail->Body = <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu của bạn</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: green;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
        .a{
        text-decoration:none;
        color: #fff;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h2>Xin chào,</h2>
        <p>Bạn vừa yêu cầu đặt lại mật khẩu cho tài khoản của mình. Để hoàn tất quá trình, vui lòng nhấp vào nút bên dưới:</p>
        
        <p style="text-align: center;font-size:30px">
            <strong>$OTP</strong>
        </p>

        <p>
            Lưu ý: Mã OTP này sẽ hết hạn sau <strong>30 phút</strong> để đảm bảo an toàn và <strong>tuyệt đối không chia sẻ mã này cho người khác</strong>. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này, tài khoản của bạn sẽ không bị ảnh hưởng.
        </p>

        <div class="footer">
            <p>Trân trọng,</p>
            <p>Đội ngũ hỗ trợ khách hàng - ExamTutor</p>
        </div>

        <hr>
    </div>
</body>
</html>
EOD;
            // echo json_encode($mail->Body);
            $mail->send();
            $now = new DateTime();
            $CurrentDateTime = $now->format('Y-m-d H:i:s');
            $this->conn->beginTransaction();
            $query = $this->conn->prepare("insert into token_forgot_password (token,time_create,OTP) values (:token,:date,:OTP)");
            $query->execute(['token' => $token, 'date' => $CurrentDateTime, 'OTP' => $OTP]);
            $this->conn->commit();
            echo json_encode(['message' => 'Mã OTP đã được gửi']);
        } catch (Exception $e) {
            $this->conn->rollBack();
            echo json_encode(['message' => 'Không thể gửi email. Lỗi: ' . $mail->ErrorInfo]);
        }
    }
    // tạo mã otp
    private function generateOTP()
    {
        $otp = '';
        //độ dài OTP là 6
        $length = 6;
        for ($i = 0; $i < $length; $i++) {
            $otp .= mt_rand(0, 9);  // Sinh số ngẫu nhiên từ 0 đến 9
        }
        return $otp;
    }
    // người dùng đổi mật khẩu trong link quên mật khẩu sau khi lấy otp từ mail
    public function ResetPasswordForgetModel($data)
    {
        try {
            $token = json_decode(trim($data['token']));
            $newpass = json_decode(trim($data['new_password']));
            $OTP = json_decode(trim($data['OTP']));
            $key = getenv('KEY');
            $decodedToken = JWT::decode($token, new Key($key, 'HS256'));
            $idUser = $decodedToken->data->id;
            $emailUser = $decodedToken->data->email;
            $this->conn->beginTransaction();
            $query1 = $this->conn->prepare("select id,time_create,OTP from token_forgot_password where token=:token and OTP=:OTP order by id desc limit 1");
            $query1->execute(['token' => $token, 'OTP' => $OTP]);
            if ($query1->rowCount() > 0) {
                date_default_timezone_set("Asia/Ho_Chi_Minh");
                $result = $query1->fetch(PDO::FETCH_ASSOC);
                $timeCreateToken = $result['time_create'];
                $OTP_query = $result['OTP'];
                // lấy thời gian tạo token
                $timeCreateToken = new DateTime($timeCreateToken);
                // lấy giờ phút giây để so sánh
                $timeCreateTokenToTimeStamp = $timeCreateToken->getTimestamp();
                // chọn vùng thời gian việt nam
                $now = new DateTime();
                // độ lệch giữa hai ngày
                // lấy hiệu thời gian của hai khoảng thời gian để tính độ chênh
                $diffInSecond = ($now->getTimestamp() - $timeCreateTokenToTimeStamp);
                //nếu lớn hơn 30p = 1800
                if ($diffInSecond > 1800) {
                    // nếu token hết hạn thì xóa đi
                    $query3 = $this->conn->prepare("delete from token_forgot_password where token=:token");
                    $query3->execute(['token' => $token]);
                    echo json_encode(['message' => 'Mã OTP đã hết hiệu lực !']);
                } elseif ($OTP != $OTP_query) {
                    echo json_encode(['message' => 'Mã OTP đã hết hiệu lực !']);
                } else {
                    $query2 = $this->conn->prepare("update $this->table set password=:password where id=:idUser and email=:email");
                    $query2->execute(['password' => $newpass, 'idUser' => $idUser, 'email' => $emailUser]);
                    // xóa token sau khi đổi pass 
                    $query3 = $this->conn->prepare("delete from token_forgot_password where token=:token");
                    $query3->execute(['token' => $token]);
                    echo json_encode(['message' => 'Đổi mật khẩu thành công !']);
                }
            } else {
                echo json_encode(['message' => 'Không tồn tại Token hợp lệ !']);
            }
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();
            echo json_encode(['message' => 'Có lỗi xảy ra ' . $e]);
        }
    }
}
