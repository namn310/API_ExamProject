<?php
    include '../../Models/QuestionsModal.php';
    include "../../Connection/Connection.php";
    
    header('Access-Control-Allow-Origin:*');
    header('Content-Type: application/json');

    $QuestionModel = new QuestionModal();
    $data = $QuestionModel->read();
    echo json_encode($data);

?>