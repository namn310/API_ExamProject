<?php
include_once __DIR__ . '/../Models/BaseModel.php';
include_once  __DIR__ . '/../Connection/Connection.php';
class ChatModel extends BaseModel
{
    protected $table;
    protected $ChatModel;
    protected $conn;
    public function __construct()
    {
        $this->table = 'list_users_chat_to_admin';
        $this->ChatModel = new BaseModel($this->table);
        $this->conn = ConnectionDB::GetConnect();
    }
    public function getListUserChatModel()
    {
        try {
            $query = $this->conn->query("select list_users_chat_to_admin.id_user, list_users_chat_to_admin.isRead, users.name from list_users_chat_to_admin inner join users on list_users_chat_to_admin.id_user = users.id");
            $result = $query->fetchAll();
            if ($query->rowCount() > 0) {
                foreach ($result as $row) {
                    $id = $row->id_user;
                    $query2 = $this->conn->prepare("select * from messages where id_sender=:id_sender or id_receiver=:id_sender order by id asc ");
                    $query2->execute(['id_sender' => $id]);
                    if ($query2->rowCount() > 0) {
                        $row->list_message = $query2->fetchAll();
                    }
                }
            }
        } catch (Throwable $e) {
            return $result = null;
        }
        return $result;
    }
    public function getListChatByUserId($id)
    {
        try {
            $query = $this->conn->prepare("select * from messages where id_sender=:id or id_receiver=:id order by id asc ");
            $query->execute(['id' => $id]);
            $result = $query->fetchAll();
        } catch (Throwable $e) {
            $result = null;
        }
        return $result;
    }
    // cập nhật trạng thái isread
    public function updateStatusIsReadChatUserModel($id)
    {
        try {
            $this->conn->beginTransaction();
            $query = $this->conn->prepare("update list_users_chat_to_admin set isRead=:isRead where id_user=:id_user");
            $query->execute(['isRead' => 1, 'id_user' => $id]);
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();
            return false;
        }
        return true;
    }
}
