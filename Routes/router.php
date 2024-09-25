<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS,PUT,PATCH");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
header("Content-Type: multipart/form-data");
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');


include_once  __DIR__ . '/../Controllers/QuestionController.php';
include_once  __DIR__ . '/../Controllers/ExamController.php';
include_once  __DIR__ . '/../Controllers/UserController.php';
include_once  __DIR__ . '/../Controllers/CategoryExamController.php';
include_once  __DIR__ . '/../Controllers/ResultController.php';

// Initialize the controllers
$QuestionsController = new QuestionsController();
$ExamsController = new ExamsController();
$UserController = new UserController();
$Category_exam = new CategoryExamController();
$ResultController = new ResultController();

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
        '/questions/userCreate' => function () use ($QuestionsController) {
            $QuestionsController->getUser();
        },
        '/exams' => function () use ($ExamsController) {
            $ExamsController->index();
        },
        '/exams/detail/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->detail($id);
        },
        '/exams/questions-exams/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->getQuestionsExam($id);
        },
        '/users' => function () use ($UserController) {
            $UserController->index();
        },
        '/users/detail/(\d+)' => function ($id) use ($UserController) {
            $UserController->detail($id);
        },
        '/category-exam' => function () use ($Category_exam) {
            $Category_exam->index();
        },
        '/category-question/(\d+)' => function ($id) use ($Category_exam) {
            $Category_exam->getQuestionsCategory($id);
        },
        '/category-exam/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->getCategoryExam($id);
        },
        '/result' => function () use ($ResultController) {
            $ResultController->index();
        },
        // lấy chi tiết kết quả bài thi
        '/result/detail/(\d+)' => function ($id) use ($ResultController) {
            $ResultController->detail($id);
        },
        // lấy dữ liệu để hiển thị lại bài thi đã thi
        '/result/review/(\d+)' => function ($id) use ($ResultController) {
            $ResultController->getReview($id);
        },
        // lấy danh sách các bài thi đã làm của User 
        '/UserlistResult/(\d+)' => function ($id) use ($ResultController) {
            $ResultController->getResultListUser($id);
        }
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
        '/questions/update/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->update($id);
        },
        '/exams/create' => function () use ($ExamsController) {
            $ExamsController->create();
        },
        '/categoryExam/create' => function () use ($Category_exam) {
            $Category_exam->create();
        },
        '/result/create' => function () use ($ResultController) {
            $ResultController->create();
        },
        '/users/create' => function () use ($UserController) {
            $UserController->create();
        },
        '/users/login' => function () use ($UserController) {
            $UserController->Login();
        },
        '/users/jwt' => function () use ($UserController) {
            $UserController->checkJWT();
        }
    ],
    // khi xảy ra CORS trình duyệt sẽ gửi OPTIONS (preflight request) trước khi yêu cầu thực tế đến máy chủ. Mục đích kiếm tra xem máy chủ có hỗ trợ method mà web gửi lên không
    'OPTIONS' => function () {
        http_response_code(204); // No Content
        exit();
    }
];

function handleRoute($routers, $methodRequest, $UriRequest)
{
    if ($methodRequest === 'OPTIONS') {
        if (isset($routers['OPTIONS'])) {
            $routers['OPTIONS']();
        }
    }
    if (isset($routers[$methodRequest])) {
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
}

handleRoute($routers, $methodRequest, $UriRequest);
