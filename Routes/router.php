<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once  __DIR__ . '/../Controllers/QuestionController.php';
include_once  __DIR__ . '/../Controllers/ExamController.php';
include_once  __DIR__ . '/../Controllers/UserController.php';

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

    ],
    // Lấy thông tin chi tiết câu hỏi
    'GET' => [
        '/questions/detail/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->detail($id);
        },
    ],
    // \d+ regular expression số nguyên
    // xóa danh sách câu hỏi
    'DELETE' => [
        '/questions/delete/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->delete($id);
        }
    ],
    // Cập nhật câu hỏi
    'PUT' => [
        '/questions/update/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->update($id);
        }
    ],
    // Tạo mới câu hỏi
    'POST' => [
        '/questions/create' => function () use ($QuestionsController) {
            $QuestionsController->create();
        }
    ],
    'GET' => [
        '/exams' => function () use ($ExamsController) {
            $ExamsController->index();
        },

    ],
    // User
    // Lấy danh sách người dùng
    'GET' => [
        '/users' => function () use ($UserController) {
            $UserController->index();
        },
    ],
    // Lấy thông tin chi tiết người dùng
    'GET' => [
        '/users/detail/(\d+)' => function ($id) use ($UserController) {
            $UserController->detail($id);
        },
    ],
    // Xóa thông tin người dùng
    'DELETE' => [
        '/users/delete/(\d+)' => function ($id) use ($UserController) {
            $UserController->delete($id);
        }
    ],
    // Cập nhật thông tin người dùng
    'PUT' => [
        '/users/update/(\d+)' => function ($id) use ($UserController) {
            $UserController->update($id);
        }
    ],
    // Tạo mới thông tin người dùng
    'POST' => [
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
    echo json_encode(['message' => 'Not found']);
}
handleRoute($routers, $methodRequest, $UriRequest);
// var_dump(handleRoute($routers, $methodRequest, $UriRequest));
