<?php
class QuestionModal
{
    protected $conn;

    public function __construct()
    {
        $this->conn = Connection::GetConnect();
    }

    // Lay tat ca data bang question
    public function read()
    {
        $query = $this->conn->prepare("SELECT * FROM questions where deleted = false");
        $query->execute();
        return $query->fetchAll();
    }

    public function delete($id)
    {
        $query = $this->conn->prepare("update questions set deleted = true where id=:id");
        if($query->execute(['id' => $id])){
            return true;
        }
        return false;
    }
}
?>
