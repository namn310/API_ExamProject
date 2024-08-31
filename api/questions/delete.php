<?php
    header('Access-Control-Allow-Origin:*');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: PATCH');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

    include '../../Models/QuestionsModal.php';
    include "../../Connection/Connection.php";

    $QuestionModel = new QuestionModal();
    $id = isset($_GET['id']) ? $_GET['id'] : die();
    if($QuestionModel->delete($id)){
        echo json_encode(array('message','Success'));
    }else{
        echo json_encode(array('message','Error'));
    }

?>