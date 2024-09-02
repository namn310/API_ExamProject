<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once  __DIR__ . '/../Controllers/QuestionController.php';
<<<<<<< HEAD
$QuestionController = new QuestionController();
=======
include_once  __DIR__ . '/../Controllers/ExamController.php';
include_once  __DIR__ . '/../Controllers/UserController.php';

$QuestionsController = new QuestionsController();
$ExamsController = new ExamsController();
$UserController = new UserController();

>>>>>>> bd810260d5f3db9ec447c3bd549798f9c1c92218
$methodRequest = $_SERVER['REQUEST_METHOD'];
$UriRequest = $_SERVER['REQUEST_URI'];
// lấy URI chính
$UriRequest = strtok($UriRequest, '?');
// định tuyến router cho API
$routers = [
    // lấy danh sách câu hỏi
    'GET' => [
<<<<<<< HEAD
        '/questions' => function () use ($QuestionController) {
            $QuestionController->index();
        },
        '/questions/detail/(\d+)' => function ($id) use ($QuestionController) {
            $QuestionController->detail($id);
        },

    ],
    // // Lấy thông tin chi tiết câu hỏi
    // 'GET' => [
    //     '/questions/detail/(\d+)' => function ($id) use ($QuestionController) {
    //         $QuestionController->detail($id);
    //     },
    // ],
    // \d+ regular expression số nguyên
    // xóa danh sách câu hỏi
    'DELETE' => [
        '/questions/delete/(\d+)' => function ($id) use ($QuestionController) {
            $QuestionController->delete($id);
=======
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
>>>>>>> bd810260d5f3db9ec447c3bd549798f9c1c92218
        }
    ],
    // Cập nhật câu hỏi
    'PUT' => [
<<<<<<< HEAD
        '/questions/update/(\d+)' => function ($id) use ($QuestionController) {
            $QuestionController->update($id);
=======
        '/questions/update/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->update($id);
>>>>>>> bd810260d5f3db9ec447c3bd549798f9c1c92218
        }
    ],
    // Tạo mới câu hỏi
    'POST' => [
<<<<<<< HEAD
        '/questions/create' => function () use ($QuestionController) {
            $QuestionController->create();
=======
        '/questions/create' => function () use ($QuestionsController) {
            $QuestionsController->create();
>>>>>>> bd810260d5f3db9ec447c3bd549798f9c1c92218
        }
    ],
    // Lấy dánh sách bài thi
    'GET' => [
        '/exams' => function () use ($ExamsController) {
            $ExamsController->index();
        },

    ],
    // Tạo mới bài thi
    'POST' => [
        '/exams/create' => function () use ($ExamsController) {
            $ExamsController->create();
        },

    ],
    'DELETE' => [
        '/exams/delete/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->delete($id);
        }
    ],
    // Cập nhật thông tin người dùng
    'PUT' => [
        '/exams/update/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->update($id);
        }
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
    header("HTTP/1.0 404 Not Found");
    echo json_encode(['message' => 'Not found']);
}
handleRoute($routers, $methodRequest, $UriRequest);
// var_dump(handleRoute($routers, $methodRequest, $UriRequest));
