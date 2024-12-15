<?php
class ConnectionDB
{
    private static $instance = null; // Lưu trữ thể hiện kết nối duy nhất
    private static $conn = null;     // Lưu trữ kết nối PDO
    private function __construct() {}

    public static function loadEnv($path)
    {
        if (!file_exists($path)) {
            throw new Exception("File .env not found: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Bỏ qua dòng bắt đầu bằng dấu #
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Tách key và value
            list($key, $value) = explode('=', $line, 2);

            // Loại bỏ các khoảng trắng
            $key = trim($key);
            $value = trim($value);

            // Gán biến môi trường
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
    // đảm bảo chỉ có một kết nối được tạo ra 
    public static function GetConnect()
    {
        // Nếu chưa có kết nối nào, tạo một kết nối mới
        if (self::$conn === null) {
            // Đọc file env
            self::loadEnv(__DIR__ . '/../.env');
            $dbConnection = getenv('DB_CONNECTION');
            $dbHost = getenv('DB_HOST');
            $dbName = getenv('DB_DATABASE');
            $dbUser = getenv('DB_USERNAME');
            $dbPass = getenv('DB_PASSWORD');
            try {
                self::$conn = new PDO("$dbConnection:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                self::$conn->exec("set names utf8");
            } catch (Throwable $e) {
                // Xử lý lỗi kết nối
                echo json_encode("Error in connect database: " . $e->getMessage());
                exit; // Dừng ứng dụng nếu không thể kết nối
            }
        }

        // Trả về kết nối hiện tại
        return self::$conn;
    }
}
