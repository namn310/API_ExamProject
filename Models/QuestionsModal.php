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
        $query = $this->conn->prepare("SELECT * FROM questions");
        $query->execute();
        return $query->fetchAll();
    }
}
?>
