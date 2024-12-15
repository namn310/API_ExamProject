<?php
include_once  __DIR__ . '/../Controllers/QuestionController.php';
include_once  __DIR__ . '/../Controllers/ExamController.php';
include_once  __DIR__ . '/../Controllers/UserController.php';
include_once  __DIR__ . '/../Controllers/CategoryExamController.php';
include_once  __DIR__ . '/../Controllers/ResultController.php';
include_once  __DIR__ . '/../Controllers/CommentController.php';
include_once  __DIR__ . '/../Controllers/ChatController.php';
include_once  __DIR__ . '/../Controllers/DataRGui.php';
include_once  __DIR__ . '/../Controllers/IRTController.php';
include_once  __DIR__ . '/../Connection/Connection.php';
include_once  __DIR__ . '/../Connection/CheckToken.php';
include_once  __DIR__ . '/../Routes/handleRouter.php';
// Initialize the controllers
$QuestionsController = new QuestionsController();
$ExamsController = new ExamsController();
$UserController = new UserController();
$Category_exam = new CategoryExamController();
$ResultController = new ResultController();
$CommentController = new CommentController();
$ChatController = new ChatController();
$dataGui = new DataRGui();
$IRTController = new IRTController();
$methodRequest = $_SERVER['REQUEST_METHOD'];
$UriRequest = $_SERVER['REQUEST_URI'];
// lấy URI chính
$UriRequest = strtok($UriRequest, '?');

// định tuyến router cho API
$routers = [
    // lấy danh sách câu hỏi
    'GET' => [
        // lấy danh sách câu hỏi
        '/questions' => function () use ($QuestionsController) {
            $QuestionsController->index();
        },
        // chi tiết câu hỏi
        '/questions/detail/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->detail($id);
        },
        // lấy hình ảnh của các câu trả lời
        '/questions/imageAnswer/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->getImageAnswer($id);
        },
        // lấy thông tin người tạo câu hỏi
        '/questions/userCreate' => function () use ($QuestionsController) {
            $QuestionsController->getUser();
        },
        // lấy danh sách bài thi
        '/exams' => function () use ($ExamsController) {
            $ExamsController->index();
        },
        // lấy chi tiết bài thi + tổng comment
        '/exams/detail/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->detail($id);
        },
        // lấy tên bài kiểm tra
        '/exams/getNameExam/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->getNameExam($id);
        },
        // lấy danh sách các câu hỏi của bài thi
        '/exams/questions-exams/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->getQuestionsExam($id);
        },
        // lấy dữ liệu câu hỏi để làm bài thi. Dữ liệu này không gửi đáp án
        '/exams/questions-todo-exams/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->getQuestionNoResultController($id);
        },
        // lấy số lượng người làm sai câu hỏi
        '/exams/count_do_wrong/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->getNumberDoWrong($id);
        },
        // lấy danh sách người dùng
        '/users' => function () use ($UserController) {
            $UserController->index();
        },
        // lấy thông tin chi tiết người dùng
        '/users/detail/(\d+)' => function ($id) use ($UserController) {
            $UserController->detail($id);
        },
        // lấy list danh mục bài thi
        '/category-exam' => function () use ($Category_exam) {
            $Category_exam->index();
        },
        // lấy các hỏi thuộc danh mục được chọn
        '/category-question/(\d+)' => function ($id) use ($Category_exam) {
            $Category_exam->getQuestionsCategory($id);
        },
        // lấy bài thi theo danh mục
        '/category-exam/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->getCategoryExam($id);
        },
        // lấy exam theo category
        '/exam/category/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->getExamByIdCat($id);
        },
        // hiển thị danh sách kết quả các bài thi đã làm
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
        },
        // Lấy danh sách bình luận của 1 bài thi
        '/comments/(\d+)' => function ($id) use ($CommentController) {
            $CommentController->getCommentExam($id);
        },
        // lấy các comment con
        '/child-comments/(\d+)' => function ($id) use ($CommentController) {
            $CommentController->getChildCommentExam($id);
        },
        // lấy danh sách người nhắn tin cho admin
        '/list-user-chat' => function () use ($ChatController) {
            $ChatController->getListUserChatController();
        },
        // lấy danh sách tin nhắn người dùng ở view người dùng
        '/list-chat-byUserId/(\d+)' => function ($id) use ($ChatController) {
            $ChatController->getChatByUserController($id);
        },
        // cập nhật trạng thái isread thành true
        '/updateIsReadChat/(\d+)' => function ($id) use ($ChatController) {
            $ChatController->updateStatusIsReadChatUserController($id);
        },

        // IRT Url
        // lấy dữ liệu tính IRT
        '/getDataRGui' => function () use ($dataGui) {
            $dataGui->getData();
        },
        // lấy danh sách thí sinh làm bài thi 
        '/IRT-get-user-do-exam/(\d+)' => function ($id) use ($IRTController) {
            $IRTController->getDataStudentDoExamController($id);
        },
        // lấy dữ liệu kết quả bài làm theo học sinh
        '/IRT-get-result-by-user/userId=(\d+)&examId=(\d+)&resultId=(\d+)' => function ($idUser, $idExam, $idResult) use ($IRTController) {
            $IRTController->getDataResultByStudent($idUser, $idExam, $idResult);
        },
        // lấy dữ liệu kết quả bài làm theo học sinh
        '/IRT-make-Data/ExamId=(\d+)' => function ($idExam) use ($IRTController) {
            $IRTController->sendDataResultQuestionToCalculateIrtController($idExam);
        },
    ],

    'DELETE' => [
        // xóa câu hỏi
        '/questions/delete/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->delete($id);
        },
        // xóa câu hỏi của bài thi
        '/exam/delete_question/idQues=(\d+)&idExam=(\d+)' => function ($idQues, $idExam) use ($QuestionsController) {
            $QuestionsController->deleteQuestionInExamController($idQues, $idExam);
        },
        // xóa bài thi
        '/exams/delete/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->delete($id);
        },
        // xóa người dùng
        '/users/delete/(\d+)' => function ($id) use ($UserController) {
            $UserController->delete($id);
        }
    ],
    // Cập nhật thông tin
    'PUT' => [
        // cập nhật thông tin bài thi
        '/exams/update/(\d+)' => function ($id) use ($ExamsController) {
            $ExamsController->update($id);
        },
        // cập nhật người dùng
        '/users/update/(\d+)' => function ($id) use ($UserController) {
            $UserController->update($id);
        },
        // đổi mật khẩu người dùng
        '/users/reset-password' => function () use ($UserController) {
            $UserController->resetPassword();
        },
        // đổi mật khẩu admin
        '/usersAdmin/update/(\d+)' => function ($id) use ($UserController) {
            $UserController->updatePassAdmin($id);
        },
        // đổi mật khẩu người dùng khi bấm vào quên mật khẩu
        '/users/reset-passwordForgot' => function () use ($UserController) {
            $UserController->ResetPasswordForget();
        },
    ],
    // Tạo mới thông tin
    'POST' => [
        // test dữ liệu R Gui
        '/DataRGui' => function () use ($dataGui) {
            $dataGui->sendData();
        },
        // tạo mới câu hỏi          
        '/questions/create' => function () use ($QuestionsController) {
            $QuestionsController->create();
        },
        // cập nhật câu hỏi
        '/questions/update/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->update($id);
        },
        // tạo mới bài kiểm tra
        '/exams/create' => function () use ($ExamsController) {
            $ExamsController->create();
        },
        // tạo bài kiểm tra tùy ý thêm câu hỏi
        '/exams/create-exam-option' => function () use ($ExamsController) {
            $ExamsController->createExamOptionController();
        },
        // Thêm câu hỏi vào bài kiểm tra option
        '/exams/AddQuestionIntoExamOption/(\d+)' => function ($id) use ($QuestionsController) {
            $QuestionsController->AddQuestionIntoExamOptionController($id);
        },
        // tạo danh mục bài kiểm tra
        '/categoryExam/create' => function () use ($Category_exam) {
            $Category_exam->create();
        },
        // tạo kết quả bài thi sau khi nộp bài
        '/result/create' => function () use ($ResultController) {
            $ResultController->create();
        },
        // tạo mới tài khoản
        '/users/create' => function () use ($UserController) {
            $UserController->create();
        },
        //login Google
        '/users/loginGoogle' => function () use ($UserController) {
            $UserController->LoginGoogle();
        },
        // kiểm tra login
        '/users/login' => function () use ($UserController) {
            $UserController->Login();
        },
        // quên mật khẩu
        '/users/forgot-password' => function () use ($UserController) {
            $UserController->forgotPassword();
        },
        // comment 
        '/comments/create' => function () use ($CommentController) {
            $CommentController->create();
        },
    ],
    // khi xảy ra CORS trình duyệt sẽ gửi OPTIONS (preflight request) trước khi yêu cầu thực tế đến máy chủ. Mục đích kiếm tra xem máy chủ có hỗ trợ method mà web gửi lên không
    'OPTIONS' => function () {
        http_response_code(204); // No Content
        exit();
    }
];
// gọi hàm route để định tuyến request đến các controller
HandleRoute::handleroute($routers, $methodRequest, $UriRequest);
