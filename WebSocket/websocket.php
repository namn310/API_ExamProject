<?php
require __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/../Models/BaseModel.php';
include_once __DIR__ . '/../WebSocket/checkConnect.php';

use Connection as GlobalConnection;
use IMAP\Connection;
// use FTP\Connection;
use Ratchet\Http\HttpServer;  //Lớp này xử lý các yêu cầu HTTP và chuyển tiếp chúng đến WebSocket.
use Ratchet\Server\IoServer; // Lớp này khởi tạo server và quản lý vòng lặp sự kiện.
use Ratchet\WebSocket\WsServer; // Lớp này cho phép giao tiếp WebSocket.
use Ratchet\MessageComponentInterface; // Giao diện mà bạn cần thực hiện để tạo một server WebSocket.
use Ratchet\ConnectionInterface; // Giao diện cho các kết nối của khách hàng.

class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $table;
    protected $conn;
    public function __construct()
    {
        $this->conn = ConnectionDB::GetConnect();
        $this->clients = new \SplObjectStorage;
        //chỉ giữ các đối tượng, giúp tiết kiệm bộ nhớ vì nó không cần phải sao chép dữ liệu cho từng đối tượng.
        // Tìm kiếm và quản lý các đối tượng trong SplObjectStorage thường hiệu quả hơn so với các cấu trúc dữ liệu khác khi làm việc với nhiều đối tượng.
        // SplObjectStorage hỗ trợ việc thêm thuộc tính cho các đối tượng lưu trữ, cho phép bạn gán thêm thông tin hoặc trạng thái cho mỗi kết nối.
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }
    private function getImageExtension($data)
    {
        $finfo = finfo_open();
        $mimeType = finfo_buffer($finfo, $data, FILEINFO_MIME_TYPE);
        finfo_close($finfo);
        switch ($mimeType) {
            case 'image/jpeg':
                return 'jpg';
            case 'image/png':
                return 'png';
            case 'image/gif':
                return 'gif';
            default:
                return 'unknown';
        }
    }
    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            // lưu tin nhắn vào database
            $data = json_decode($msg, true);
            // if (isset($data['message'], $data['image'], $data['from'], $data['to'], $data['time'], $data['sender'], $data['type_message'])) {
            $message = $data['message'];
            $sender = $data['from'];
            $receiver = $data['to'];
            $time = $data['time'];
            $type_sender = $data['sender'];
            $type_message = $data['type_message'];
            if ($data['image'] !== '' && $data['image'] !== null && $data['image'] !== 'underfined') {
                $base64Image = $data['image'];

                // Giải mã Base64
                $imageMessage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            } else {
                $imageMessage = '';
            }
            // thêm người dùng nhắn tin vào danh sách nhắn tin
            $this->conn->beginTransaction();
            // nếu người dùng không phải admin thì người nhắn vào list_user_chat_to_user
            if ($data['sender'] !== 'admin') {
                $query2 = $this->conn->prepare("select id from list_users_chat_to_admin where id_user=:id_user");
                $query2->execute(['id_user' => $sender]);
                // nếu không tồn tại kết quả thì thêm người dùng vào
                if ($query2->rowCount() < 1) {
                    $query3 = $this->conn->prepare("insert into list_users_chat_to_admin (id_user) values (:id_user)");
                    $query3->execute(['id_user' => $sender]);
                }
            }
            // lưu tin nhắn vào db
            $query = $this->conn->prepare("INSERT INTO messages (id_sender, id_receiver, type_sender, message, type_message, create_at) VALUES (:id_sender, :id_receiver, :type_sender, :message, :typeMess, :date)");
            // nếu tin nhắn là ảnh thì lưu ảnh vào cột message
            if ($message !== '' && $message !== null && $message !== 'underfined') {
                $query->execute([
                    'id_sender' => $sender,
                    'id_receiver' => $receiver,
                    'type_sender' => $type_sender,
                    'message' => $message,
                    'typeMess' => 'text',
                    'date' => $time
                ]);
                $type_message = 'text';
            }
            // nếu tin nhắn là text thì lưu vào cột message
            else {
                // thư mục sẽ lưu ảnh vào
                $folder = __DIR__ . '/../assets/image/ImageMessage/';
                if (!is_dir($folder)) {
                    mkdir($folder, 0777, true);  // Tạo thư mục với quyền ghi đầy đủ
                }
                $fileName = time() . '_' . uniqid() . '.' . $this->getImageExtension($imageMessage);
                file_put_contents($folder . $fileName, $imageMessage);
                $query->execute([
                    'id_sender' => $sender,
                    'id_receiver' => $receiver,
                    'type_sender' => $type_sender,
                    'message' => $fileName,
                    'typeMess' => 'image',
                    'date' => $time

                ]);
                $message = $fileName;
                $type_message = 'image';
            }
            // echo ($data['sender']);
            // kiểm tra xem đây có phải tin nhắn của admin không
            if (isset($data['to']) && $data['to'] === 'admin') {
                foreach ($this->clients as $client) {
                    // Gửi tin nhắn đến tất cả các client, ngoại trừ người gửi
                    if (
                        $client !== $from && $client->resourceId == 8
                    ) {
                        $client->send(json_encode([
                            'from' => 'user',
                            'message' => $message,
                            //  thông báo tin nhắn đến admin

                        ]));
                    }
                }
            } else {
                // Gửi tin nhắn đến tất cả clients ngoại trừ người gửi
                foreach ($this->clients as $client) {
                    if ($from !== $client) {
                        $client->send($msg);
                    }
                }
            }
            $this->conn->commit();
            // } else {
            //     echo false;
            // }
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log($e->getMessage());  // Ghi lỗi
            // throw $e;  // Phát lỗi ra ngoài hoặc xử lý thêm
        }
    }
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
    public static function RunServerSocket()
    {
        // CHẠY server socket
        try {
            $server = IoServer::factory(
                new HttpServer(
                    new WsServer(
                        new Chat()
                    )
                ),
                9001 // cổng server
            );
            $server->run();
        } catch (Throwable $e) {
            echo ("Error !" . $e);
        }
        echo ("Connect Successful");
    }
    // kiểm tra cổng được dùng chưa
    private static function isPortInUse($port)
    {
        $connection = @fsockopen('localhost', $port);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }
        return false;
    }
}
// php WebSocket/websocket.php
Chat::RunServerSocket();
