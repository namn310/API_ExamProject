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
    public function __construct()
    {
        $this->table = 'users';
        $this->UserModel = new BaseModel($this->table);
    }
    public function index()
    {
        $conn = ConnectionDB::GetConnect();
        $query = $conn->query("select id,name,email,create_at from $this->table");
        return $query->fetchAll();
    }
    public function createUser($data)
    {
        $conn = ConnectionDB::GetConnect();
        $name = $data['name'];
        $email = $data['email'];
        $pass = md5($data['password']);
        $role = $data['role'];
        try {
            $query = $conn->prepare("select id from $this->table where email=:email");
            $query->execute(['email' => $email]);
            if ($query->rowCount() > 0) {
                echo json_encode(['message' => 'Email đã tồn tại']);
            } else {
                $query2 = $conn->prepare("insert into $this->table (name,password,email,role) values (:name,:pass,:email,:role)");
                $query2->execute(['name' => $name, 'pass' => $pass, 'email' => $email, 'role' => $role]);
                echo json_encode(['message' => 'Đăng ký tài khoản thành công']);
            }
        } catch (Throwable $e) {
            echo json_encode(['message' => $e]);
        }
    }
    public function read($id)
    {
        $conn = ConnectionDB::GetConnect();
        $query = $conn->prepare("select id,name,email,role,create_at from $this->table where id=:id");
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
    public function updatePassAdmin($data, $id)
    {
        $oldpass = json_decode($data['oldpass']);
        $newpass = json_decode($data['newpass']);
        $conn = ConnectionDB::GetConnect();
        $query = $conn->prepare("select id from users where id=:id and password=:password limit 1");
        $query->execute(['id' => $id, 'password' => $oldpass]);
        if ($query->rowCount() > 0) {
            try {
                $query2 = $conn->prepare("update users set password=:password where id=:id");
                $query2->execute(['password' => $newpass, 'id' => $id]);
            } catch (Throwable $e) {
                echo json_encode($e);
            }
            echo json_encode('Cập nhật mật khẩu thành công');
        } else {
            echo json_encode("Mật khẩu không chính xác");
        }
    }
    public function getUserCreate()
    {
        $conn = ConnectionDB::GetConnect();
        try {
            $query = $conn->prepare("select name,id from users where role=:role");
            $query->execute(['role' => 'admin']);
        } catch (Throwable $e) {
            echo json_encode(['message' => "Lỗi" . $e]);
        }
        echo json_encode(['data' => $query->fetchAll()]);
    }
    public function LoginModel($data)
    {
        $key = getenv('KEY');
        // $data = json_decode(file_get_contents("php://input"), true);
        $conn = ConnectionDB::GetConnect();
        try {
            $email = $data['email'];
            $pass = md5($data['password']);
            $role = $data['role'];
            $query = $conn->prepare("select * from $this->table where email=:email and password=:password and role=:role LIMIT 1");
            $query->execute(['email' => $email, 'password' => $pass, 'role' => $role]);
            $user = $query->fetch(PDO::FETCH_ASSOC);
            if ($query->rowCount() > 0) {
                $timeCreate = time();
                $timeExpire = time() + 86400;
                if ($role === 'admin') {
                    $payload = [
                        'iat' => $timeCreate,
                        'exp' => $timeExpire,
                        'data' => [
                            'id' => $user['id'],
                            'email' => $user['email'],
                            'name' => $user['name'],
                            'role' => $user['role']
                        ]
                    ];
                    $jwt = JWT::encode($payload, $key, 'HS256');
                    echo json_encode([
                        'message' => 'Đăng nhập thành công !',
                        'jwtAdmin' => $jwt,
                    ]);
                }
                if ($role === 'student') {
                    $payload = [
                        'iat' => $timeCreate,
                        'exp' => $timeExpire,
                        'data' => [
                            'id' => $user['id'],
                            'email' => $user['email'],
                            'name' => $user['name'],
                            'role' => $user['role']
                        ]
                    ];
                    $jwt = JWT::encode($payload, $key, 'HS256');
                    echo json_encode([
                        'message' => 'Đăng nhập thành công !',
                        'jwtStudent' => $jwt,
                    ]);
                }
            } else {
                echo json_encode(['message' => 'Đăng nhập thất bại ! Tài khoản hoặc mật khẩu không chính xác']);
            }
        } catch (Throwable $e) {
            echo json_encode(['message' => "Có lỗi xảy ra " . $e]);
        }
    }
    public function resetPasswordModel($data)
    {
        $key = getenv('KEY');
        $conn = ConnectionDB::GetConnect();
        $token = $data['token'];
        $newPassword = md5($data['new_password']);
        $oldPasswordInput = md5($data['old_password']);
        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            // echo json_encode($decoded->data->id);
            $userId = $decoded->data->id;
            $userEmail = $decoded->data->email;
            $query = $conn->prepare("select id,password from $this->table where id=:id and email=:email");
            $query->execute(['id' => $userId, 'email' => $userEmail]);
            if ($query->rowCount() > 0) {
                $result = $query->fetch();
                $oldPassword = $result->password;
                // so sánh mật khẩu cũ nhập vào với mật khẩu trong database
                if ($oldPassword === $oldPasswordInput) {
                    $updated = $conn->prepare("update $this->table set password=:password where id=:id and email=:email ");
                    $updated->execute(['password' => $newPassword, 'id' => $userId, 'email' => $userEmail]);
                    echo json_encode(['message' => 'Đổi mật khẩu thành công !']);
                } else {
                    echo json_encode(['message' => 'Mật khẩu cũ không chính xác']);
                }
            }
        } catch (Exception $e) {
            echo json_encode(['message' => 'Có lỗi xảy ra !' . $e->getMessage()]);
        }
    }
    public function forgotPasswordModel($data)
    {
        $conn = ConnectionDB::GetConnect();
        $key = getenv('KEY');
        // $data = json_decode($data);
        try {
            $query = $conn->prepare("select id,email from $this->table where email=:email");
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
            $conn = ConnectionDB::GetConnect();
            $now = new DateTime();
            $CurrentDateTime = $now->format('Y-m-d H:i:s');
            $query = $conn->prepare("insert into token_forgot_password (token,time_create,OTP) values (:token,:date,:OTP)");
            $query->execute(['token' => $token, 'date' => $CurrentDateTime, 'OTP' => $OTP]);
        } catch (Exception $e) {
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
            $conn = ConnectionDB::GetConnect();
            $query1 = $conn->prepare("select id,time_create,OTP from token_forgot_password where token=:token and OTP=:OTP order by id desc limit 1");
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
                    $query3 = $conn->prepare("delete from token_forgot_password where token=:token");
                    $query3->execute(['token' => $token]);
                    echo json_encode(['message' => 'Mã OTP đã hết hiệu lực !']);
                } elseif ($OTP != $OTP_query) {
                    echo json_encode(['message' => 'Mã OTP đã hết hiệu lực !']);
                } else {
                    $query2 = $conn->prepare("update $this->table set password=:password where id=:idUser and email=:email");
                    $query2->execute(['password' => $newpass, 'idUser' => $idUser, 'email' => $emailUser]);
                    // xóa token sau khi đổi pass 
                    $query3 = $conn->prepare("delete from token_forgot_password where token=:token");
                    $query3->execute(['token' => $token]);
                    echo json_encode(['message' => 'Đổi mật khẩu thành công !']);
                }
            } else {
                echo json_encode(['message' => 'Không tồn tại Token hợp lệ !']);
            }
        } catch (Throwable $e) {
            echo json_encode(['message' => 'Có lỗi xảy ra ' . $e]);
        }
    }
}
