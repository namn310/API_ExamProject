<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once  __DIR__ . '/../Controllers/ExamController.php';
$ExamController = new ExamController();
$methodRequest = $_SERVER['REQUEST_METHOD'];
$UriRequest = $_SERVER['REQUEST_URI'];
// lấy URI chính
$UriRequest = strtok($UriRequest, '?');
$routers = [
    'GET' => [
        '/exam' => function () use ($ExamController) {
            $ExamController->index();
        },
    ],
    'GET' => [
        '/nam' => function () use ($ExamController) {
            $ExamController->index();
        },
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
