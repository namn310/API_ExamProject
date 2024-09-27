<?php
include_once __DIR__ . '/../Models/BaseModel.php';
class CommentModel extends BaseModel
{
    protected $table;
    protected $CommentModel;
    public function __construct()
    {
        $this->table = 'comments';
        $this->CommentModel = new BaseModel($this->table);
    }
    public function index()
    {
        return $this->CommentModel->index();
    }
    public function create($data)
    {
        return $this->CommentModel->create($data);
    }
    
    public function read($id)
    {
        return $this->CommentModel->read($id);
    }
    public function delete($id)
    {
        return $this->CommentModel->delete($id);
    }
    public function update($data, $id)
    {
        $this->CommentModel->update($data, $id);
    }

    public function readCommentsExam($id)
    {
        $conn = Connection::GetConnect();
        try {
            $query = $conn->prepare("SELECT comments.id, comments.exam_id,comments.created_at, comments.user_id, comments.comment_text, users.id, users.name FROM comments
                inner JOIN users on comments.user_id = users.id
                WHERE exam_id=:id AND parent_id IS NULL");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
        return $query->fetchAll();
    }

    public function readChildCommentsExam($id)
    {
        $conn = Connection::GetConnect();
        try {
            $query = $conn->prepare("SELECT comments.id, comments.exam_id,comments.created_at,comments.parent_id, comments.user_id, comments.comment_text, users.id, users.name FROM comments
                inner JOIN users on comments.user_id = users.id
                WHERE exam_id=:id AND parent_id=1");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
        return $query->fetchAll();
    }
}
