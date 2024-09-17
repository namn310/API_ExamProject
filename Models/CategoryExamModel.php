<?php
include_once __DIR__ . '/../Models/BaseModel.php';
require 'vendor/autoload.php';
class CategoryExamModel extends BaseModel
{
    protected $table;
    protected $CategoryExamModel;
    public function __construct()
    {
        $this->table = 'category_exams';
        $this->CategoryExamModel = new BaseModel($this->table);
    }
    public function index()
    {
        return $this->CategoryExamModel->index();
    }
    public function create($data)
    {
        return $this->CategoryExamModel->create($data);
    }
    public function read($id)
    {
        return $this->CategoryExamModel->read($id);
    }
    public function delete($id)
    {
        return $this->CategoryExamModel->delete($id);
    }
    public function update($data, $id)
    {
        $this->CategoryExamModel->update($data, $id);
    }
    public function readQuestionCategory($id)
    {
        try {
            $query = $this->conn->prepare("SELECT exams.id, exams.title, exams.duration,exams.totalQuestion FROM exams
                INNER JOIN category_exams on exams.category = category_exams.id
                WHERE category_exams.id=:id");
            $query->execute(['id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
        return $query->fetchAll();
    }
    // public function getUserCreate()
    // {
    //     $this->CategoryExamModel->getUserCreate();
    // }
}
