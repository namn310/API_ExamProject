<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once  __DIR__ . '/../Controllers/QuestionController.php';
$ExamController = new ExamController();
$methodRequest = $_SERVER['REQUEST_METHOD'];
$UriRequest = $_SERVER['REQUEST_URI'];
// lấy URI chính
$UriRequest = strtok($UriRequest, '?');
// định tuyến router cho API
$routers = [
    // lấy danh sách câu hỏi
    'GET' => [
        '/questions' => function () use ($ExamController) {
            $ExamController->index();
        },

    ],
    // Lấy thông tin chi tiết câu hỏi
    'GET' => [
        '/questions/detail/(\d+)' => function ($id) use ($ExamController) {
            $ExamController->detail($id);
        },
    ],
    // \d+ regular expression số nguyên
    // xóa danh sách câu hỏi
    'DELETE' => [
        '/questions/delete/(\d+)' => function ($id) use ($ExamController) {
            $ExamController->delete($id);
        }
    ],
    // Cập nhật câu hỏi
    'PUT' => [
        '/questions/update/(\d+)' => function ($id) use ($ExamController) {
            $ExamController->update($id);
        }
    ],
    // Tạo mới câu hỏi
    'POST' => [
        '/questions/create' => function () use ($ExamController) {
            $ExamController->create();
        }
    ]

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
