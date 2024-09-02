<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once  __DIR__ . '/../Controllers/QuestionController.php';
include_once  __DIR__ . '/../Controllers/ExamController.php';
include_once  __DIR__ . '/../Controllers/UserController.php';

// Initialize the controllers
$QuestionsController = new QuestionsController();
$ExamsController = new ExamsController();
$UserController = new UserController();

$methodRequest = $_SERVER['REQUEST_METHOD'];
$UriRequest = $_SERVER['REQUEST_URI'];
// lấy URI chính
$UriRequest = strtok($UriRequest, '?');

// định tuyến router cho API
$routers = [
    // lấy danh sách câu hỏi
    'GET' => [
        '/questions' => function () use ($QuestionsController) {
            $QuestionsController->index();
        },
        '/questions/detail/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->detail($id);
        },
        '/exams' => function () use ($ExamsController) {
            $ExamsController->index();
        },
        '/exams/detail/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->detail($id);
        },
        '/users' => function () use ($UserController) {
            $UserController->index();
        },
        '/users/detail/(\d+)' => function ($id) use ($UserController) {
            $UserController->detail($id);
        },
    ],
    // xóa danh sách câu hỏi
    'DELETE' => [
        '/questions/delete/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->delete($id);
        },
        '/exams/delete/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->delete($id);
        },
        '/users/delete/(\d+)' => function ($id) use ($UserController) {
            $UserController->delete($id);
        }
    ],
    // Cập nhật thông tin
    'PUT' => [
        '/questions/update/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->update($id);
        },
        '/exams/update/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->update($id);
        },
        '/users/update/(\d+)' => function ($id) use ($UserController) {
            $UserController->update($id);
        }
    ],
    // Tạo mới thông tin
    'POST' => [
        '/questions/create' => function () use ($QuestionsController) {
            $QuestionsController->create();
        },
        '/exams/create' => function () use ($ExamsController) {
            $ExamsController->create();
        },
        '/users/create' => function () use ($UserController) {
            $UserController->create();
        }
    ],
];

function handleRoute($routers, $methodRequest, $UriRequest)
{
    foreach ($routers[$methodRequest] as $router => $function) {
        if (preg_match("#^$router$#", $UriRequest, $value)) {
            // ví dụ http là : API_EXAMPLE/exams?id=1 thì array_shift($value)=1;
            array_shift($value);
            return call_user_func_array($function, $value);
        }
    }
    header("HTTP/1.0 404 Not Found");
    echo json_encode(['message' => 'Not found']);
}

handleRoute($routers, $methodRequest, $UriRequest);
