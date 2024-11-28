<?php
include_once __DIR__ . '/../Models/BaseModel.php';
include_once __DIR__ . '/../Models/IRTModel.php';
class IRTController
{
    private $IRTmodel;
    public function __construct()
    {
        $this->IRTmodel = new IRTModel();
    }
    public function getDataStudentDoExamController($id)
    {
        $data = $this->IRTmodel->getDataStudentDoExam($id);
        $dataCountQuestionOfExam = $this->IRTmodel->getTotalQuestionOfExam($id);
        echo json_encode(['data' => $data, 'TotalQuestion' => $dataCountQuestionOfExam]);
    }
    public function getDataResultByStudent($idUser, $idExam, $idResult)
    {
        $data = $this->IRTmodel->getDataResultByStudentModel($idUser, $idExam, $idResult);
        echo json_encode(['data' => $data]);
    }
    // gửi dữ liệu để tính độ khó câu hỏi
    public function sendDataResultQuestionToCalculateIrtController($idExam)
    {
        $this->IRTmodel->makeDataToCalculateIRT($idExam);
    }
}
