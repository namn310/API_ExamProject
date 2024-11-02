<?php
include_once __DIR__ . '/../Models/ChatModel.php';
class ChatController
{
    private $table;
    private $ChatModel;
    public function __construct()
    {
        $this->table = 'list_users_chat_to_admin';
        $this->ChatModel = new ChatModel();
    }
    public function getListUserChatController()
    {
        // $checkToken = CheckToken::checkToken();
        // if ($checkToken == true) {
        $result = $this->ChatModel->getListUserChatModel();
        echo json_encode(['list_user_chat' => $result]);
        // } else {
        // echo json_encode(['list_user_chat' => 'Bạn không được phép truy cập !']);
        // }
    }
    public function getChatByUserController($id)
    {
        $result = $this->ChatModel->getListChatByUserId($id);
        echo json_encode(['list_user_chat' => $result]);
    }
    public function updateStatusIsReadChatUserController($id)
    {
        $result = $this->ChatModel->updateStatusIsReadChatUserModel($id);
        if ($result === true) {
            echo json_encode(['response' => 1]);
        } else {
            echo json_encode(['response' => 0]);
        }
    }
}
