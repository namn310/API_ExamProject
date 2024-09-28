-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th9 28, 2024 lúc 05:37 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `examproject_v2`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `category_exams`
--

CREATE TABLE `category_exams` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `category_exams`
--

INSERT INTO `category_exams` (`id`, `title`, `description`) VALUES
(1, 'Tiếng Anh', NULL),
(2, 'Toán', NULL),
(3, 'Vật lý', NULL),
(4, 'Hóa Học', NULL),
(5, 'Bằng lái xe', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `exam_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`id`, `exam_id`, `user_id`, `parent_id`, `comment_text`, `created_at`) VALUES
(54, 7, 7, NULL, 's', '2024-09-28 03:29:59'),
(55, 7, 7, 54, 's', '2024-09-28 03:30:03'),
(56, 7, 7, 54, 's', '2024-09-28 03:30:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `exams`
--

CREATE TABLE `exams` (
  `id` int(5) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `class` tinyint(3) UNSIGNED NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `id_user` int(5) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expire_time` datetime DEFAULT NULL,
  `category` int(10) UNSIGNED NOT NULL,
  `duration` int(3) DEFAULT NULL,
  `totalQuestion` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `exams`
--

INSERT INTO `exams` (`id`, `title`, `class`, `description`, `id_user`, `created_at`, `expire_time`, `category`, `duration`, `totalQuestion`) VALUES
(7, 'THPTQG Tiếng Anh', 12, '', 8, '2024-09-23 12:51:10', '0000-00-00 00:00:00', 1, 90, 20),
(8, 'THPT', 11, '', 8, '2024-09-23 13:38:34', '0000-00-00 00:00:00', 1, 5, 5),
(9, 'Toán THPTQG', 12, '', 8, '2024-09-24 16:01:36', '0000-00-00 00:00:00', 2, 90, 4),
(16, 'fafkja', 12, '', 8, '2024-09-24 16:31:32', '0000-00-00 00:00:00', 2, 5, 4),
(17, 'THPTQG Toán 2024', 12, '', 8, '2024-09-25 20:02:38', '0000-00-00 00:00:00', 2, 90, 50),
(18, 'Bằng lái xe', 12, '', 8, '2024-09-27 16:44:47', '0000-00-00 00:00:00', 5, 60, 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `image_answers`
--

CREATE TABLE `image_answers` (
  `id` int(5) NOT NULL,
  `idQues` int(5) UNSIGNED NOT NULL,
  `imageAns` text NOT NULL,
  `stt` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `image_answers`
--

INSERT INTO `image_answers` (`id`, `idQues`, `imageAns`, `stt`) VALUES
(51, 184, '1727429989_Screenshot 2024-09-27 163718.png', 0),
(52, 184, '1727429989_Screenshot 2024-09-27 163919.png', 1),
(53, 184, '1727429989_Screenshot 2024-09-27 163933.png', 2),
(54, 185, '1727430050_Screenshot 2024-09-27 164016.png', 0),
(55, 185, '1727430050_Screenshot 2024-09-27 163919.png', 1),
(56, 185, '1727430050_Screenshot 2024-09-27 163933.png', 2),
(57, 186, '1727430133_Screenshot 2024-09-27 164120.png', 0),
(58, 186, '1727430133_Screenshot 2024-09-27 164148.png', 1),
(59, 186, '1727430133_Screenshot 2024-09-27 164201.png', 2),
(60, 187, '1727430161_Screenshot 2024-09-27 164120.png', 0),
(61, 187, '1727430161_Screenshot 2024-09-27 164148.png', 1),
(62, 187, '1727430161_Screenshot 2024-09-27 164201.png', 2),
(63, 188, '1727430208_Screenshot 2024-09-27 164306.png', 0),
(64, 188, '1727430208_Screenshot 2024-09-27 164316.png', 1),
(65, 189, '1727430260_Screenshot 2024-09-27 164306.png', 0),
(66, 189, '1727430260_Screenshot 2024-09-27 164316.png', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `questions`
--

CREATE TABLE `questions` (
  `id` int(5) UNSIGNED NOT NULL,
  `class` tinyint(3) UNSIGNED NOT NULL,
  `Subject` int(5) NOT NULL,
  `image` text DEFAULT NULL,
  `title` varchar(1024) NOT NULL,
  `correctAns` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(5) UNSIGNED NOT NULL,
  `answerlist` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `questions`
--

INSERT INTO `questions` (`id`, `class`, `Subject`, `image`, `title`, `correctAns`, `created_at`, `created_by`, `answerlist`) VALUES
(52, 12, 1, NULL, '<p><strong>Sophia and Jenny are talking about solar energy.</strong></p><p><strong>- Sophia: \"I think we should use solar energy.\"</strong></p><p><strong>- Jenny: \"___ . It\'s clean and renewable.\"</strong></p>', '[\n \"B\"\n]', '2024-09-23 12:35:19', 8, '[\n  \"I don\'t think so\",\n  \"I agree with you\",\n  \"Of course not\",\n  \"You\'re wrong\"\n]'),
(53, 12, 1, NULL, '<p><strong>Brett is talking to Julie after work.</strong></p><p><strong>- Brett: \"Let\'s eat out tonight.\"</strong></p><p><strong>- Julie: \"___\"</strong></p>', '[\n \"C\"\n]', '2024-09-23 12:36:04', 8, '[\n  \"Yes, it\'s true\",\n  \"Yes, I do\",\n  \"That\'s a great idea\",\n  \"Here you are\"\n]'),
(54, 12, 1, NULL, '<p><strong>Mark the letter A, B, C, or D on your answer sheet to indicate the word that differs from the other three in the position of stress in each of the following questions.</strong></p>', '[\n \"B\"\n]', '2024-09-23 12:37:10', 8, '[\n  \"different\",\n  \"creative\",\n  \"possible\",\n  \"national\"\n]'),
(55, 12, 1, NULL, '<p><strong>Mark the letter A, B, C, or D on your answer sheet to indicate the word that differs from the other three in the position of stress in each of the following questions.</strong></p>', '[\n \"D\"\n]', '2024-09-23 12:37:45', 8, '[\n  \"suggest\",\n  \"become\",\n  \"prepare\",\n  \"answer\"\n]'),
(56, 12, 1, NULL, '<p><strong>Mark the letter A, B, C, or D on your answer sheet to indicate the word whose underlined part differs from the other three in</strong> pronunciation <strong>in each of the following questions.</strong></p>', '[\n \"B\"\n]', '2024-09-23 12:38:50', 8, '[\n  \"coast\",\n  \"board\",\n  \"boat\",\n  \"road\"\n]'),
(57, 12, 1, NULL, '<p><strong>Mark the letter A, B, C, or D on your answer sheet to indicate the word whose underlined part differs from the other three in</strong> pronunciation <strong>in each of the following questions.</strong></p>', '[\n \"A\"\n]', '2024-09-23 12:39:15', 8, '[\n  \"nature\",\n  \"artist\",\n  \"winter\",\n  \"photo\"\n]'),
(58, 12, 1, NULL, '<p>The mass media serves as a <strong>powerful</strong> means of distributing information to the public.</p>', '[\n \"B\"\n]', '2024-09-23 12:40:01', 8, '[\n  \"cheap\",\n  \"great\",\n  \"bad\",\n  \"weak\"\n]'),
(59, 12, 1, NULL, '<p>The boy is unable to <strong>provide</strong> a good reason for his strange behaviour yesterday.</p>', '[\n \"C\"\n]', '2024-09-23 12:40:34', 8, '[\n  \"allow\",\n  \"perform\",\n  \"give\",\n  \"take\"\n]'),
(60, 12, 1, NULL, '<p>Being very frank, Tom <strong>made no bones</strong> about his discontent over the plan proposed by the team leader.</p>', '[\n \"B\"\n]', '2024-09-23 12:41:39', 8, '[\n  \"attempted to express\",\n  \"tried to hide\",\n  \"failed to control\",\n  \"wanted to voice\"\n]'),
(61, 12, 1, NULL, '<p>Despite their <strong>striking</strong> differences, they have developed a good relationship.</p>', '[\n \"A\"\n]', '2024-09-23 12:42:06', 8, '[\n  \"small\",\n  \"major\",\n  \"clear\",\n  \"big\"\n]'),
(62, 12, 1, '', '<p>Lucy was ___ runner in the competition.</p>', '[\"D\"]', '2024-09-23 12:42:52', 8, '[\"faster than\",\"fastest\",\"faster\",\"the fastest\"]'),
(63, 12, 1, NULL, '<p>I will pick you up ___</p>', '[\n \"B\"\n]', '2024-09-23 12:43:18', 8, '[\n  \"until you will arrive at the station\",\n  \"when you arrive at the station\",\n  \"as soon as you arrived at the station\",\n  \"after you had arrived at the station\"\n]'),
(64, 12, 1, NULL, '<p>A shopping mall was built in the area, ___ the local people access to a wide range of goods.</p>', '[\n \"B\"\n]', '2024-09-23 12:43:42', 8, '[\n  \"give\",\n  \"giving\",\n  \"given\",\n  \"to giving\"\n]'),
(65, 12, 1, NULL, '<p>The travel agency reports that most ___ enjoyed the city tour very much.</p>', '[\n \"A\"\n]', '2024-09-23 12:44:08', 8, '[\n  \"sightseers\",\n  \"spectators\",\n  \"commuters\",\n  \"viewers\"\n]'),
(66, 12, 1, NULL, '<p>The young star loves appearing on television and is no ___ violet when it comes to expressing her views.</p>', '[\n \"C\"\n]', '2024-09-23 12:44:33', 8, '[\n  \"rotting\",\n  \"drying\",\n  \"shrinking\",\n  \"blooming\"\n]'),
(67, 12, 1, NULL, '<p>My aunt doesn\'t like to go out at ___ night.</p>', '[\n \"D\"\n]', '2024-09-23 12:44:53', 8, '[\n  \"an\",\n  \"a\",\n  \"the\",\n  \"0\"\n]'),
(68, 12, 1, NULL, '<p>The teachers in our school are very helpful and kind ___ us.</p><p>&nbsp;</p>', '[\n \"B\"\n]', '2024-09-23 12:45:16', 8, '[\n  \"for\",\n  \"to\",\n  \"in\",\n  \"at\"\n]'),
(69, 12, 1, NULL, '<p>I stopped ___ in order to get something to eat because I was so hungry.</p>', '[\n \"A\"\n]', '2024-09-23 12:45:38', 8, '[\n  \"working\",\n  \"to working\",\n  \"worked\",\n  \"to work\"\n]'),
(70, 12, 1, NULL, '<p>The boy is practising the piano, ___?</p>', '[\n \"B\"\n]', '2024-09-23 12:46:03', 8, '[\n  \"does he\",\n  \"isn\'t he\",\n  \"doesn\'t he \",\n  \"was he\"\n]'),
(71, 12, 1, NULL, '<p>To his disappointment, she didn\'t show up; instead, all the lights went out and the house ___ into darkness.</p>', '[\n \"D\"\n]', '2024-09-23 12:46:22', 8, '[\n  \"discharged\",\n  \"dispersed\",\n  \"disarranged\",\n  \"dissolved\"\n]'),
(72, 12, 1, NULL, '<p>A good way for parents and children to get closer is to ___ housework together.</p>', '[\n \"A\"\n]', '2024-09-23 12:46:41', 8, '[\n  \"do\",\n  \"take\",\n  \"make\",\n  \"turn\"\n]'),
(73, 12, 1, NULL, '<p>Thanh ___ tea when his sister came home from school.</p>', '[\n \"B\"\n]', '2024-09-23 12:47:01', 8, '[\n  \"makes\",\n  \"was making\",\n  \"is making\",\n  \"has made\"\n]'),
(74, 12, 1, NULL, '<p>The plants ___ by my brother every day.</p>', '[\n \"B\"\n]', '2024-09-23 12:47:22', 8, '[\n  \"watered\",\n  \"are watered\",\n  \"water\",\n  \"are watering\"\n]'),
(75, 12, 1, NULL, '<p>Our friends usually ___ early in the morning.</p>', '[\n \"C\"\n]', '2024-09-23 12:47:59', 8, '[\n  \"look for\",\n  \"turn off\",\n  \"wake up\",\n  \"put on\"\n]'),
(76, 12, 1, NULL, '<p>The child took a more ___ role in his learning and made great progress.</p>', '[\n \"B\"\n]', '2024-09-23 12:48:17', 8, '[\n  \"activate\",\n  \"active\",\n  \"activity\",\n  \"actively\"\n]'),
(77, 12, 1, NULL, '<p>When trying to understand our own or other people\'s behaviour, we tend to oversimplify things. We use one or two adjectives to sum each other up. ___ , we may think of some friends as having a generally hopeful and positive outlook while others are con', '[\n \"C\"\n]', '2024-09-23 12:48:46', 8, '[\n  \"Because\",\n  \"In constrast\",\n  \"For example\",\n  \"However\"\n]'),
(78, 12, 1, NULL, '<p>Of course, in ___, none of us is so easily defined.</p>', '[\n \"C\"\n]', '2024-09-23 12:49:06', 8, '[\n  \"distance\",\n  \"return\",\n  \"reality\",\n  \"theory\"\n]'),
(79, 12, 1, NULL, '<p>The truth is that we are all made up of characteristics ___ are inconsistent and contradictory: we can be serious and reliable with our colleagues at work but we are more unpredictable and emotional with our nearest and dearest at home.</p>', '[\n \"C\"\n]', '2024-09-23 12:49:32', 8, '[\n  \"whom\",\n  \"who\",\n  \"which\",\n  \"whose\"\n]'),
(80, 12, 1, NULL, '<p>With one person we can be very cautious, but with ___ one, we are adventurous thrill-seekers.</p>', '[\n \"A\"\n]', '2024-09-23 12:49:52', 8, '[\n  \"another\",\n  \"many\",\n  \"few\",\n  \"most\"\n]'),
(81, 12, 1, NULL, '<p>Or do we ___ our personality according to the differences in our changing moods and situations?</p>', '[\n \"B\"\n]', '2024-09-23 12:50:11', 8, '[\n  \"fit\",\n  \"alter\",\n  \"grow\",\n  \"divert\"\n]'),
(118, 12, 2, '1727167929_Screenshot 2024-09-24 155124.png', '<p>Cho hàm số f(x) có bảng biến thiên như hình. Giá trị cực tiểu của hàm số đã cho là</p>', '[\"B\"]', '2024-09-24 15:52:09', 8, '[\"3\",\"-2\",\"2\",\"-1\"]'),
(119, 12, 2, '', '<p><strong>Cho hàm số f(x)= $5 - 6x^2 . \\int f(x)dx = ?$</strong></p>', '[\"B\"]', '2024-09-24 15:57:22', 8, '[\"$5 - 2x^3 +C$\",\"$5x - 2x^3 +C$\",\"$5x - 6x^3 +C$\",\"$5 - 3x^3 +C$\"]'),
(120, 12, 2, '', '<p><strong>Tập nghiệm của phương trình &nbsp;$\\log_{3} (x^2-7)=2$ là</strong></p>', '[\"A\"]', '2024-09-24 15:59:09', 8, '[\"{-4:4}\",\"{4}\",\"{2}\",\"{16}\"]'),
(121, 12, 2, '', '<p><strong>Trong không gian Oxyz cho hai điểm A(1;1;-2) và B(3;-1;2). Tọa độ của vecto AB là :</strong></p>', '[\"A\"]', '2024-09-24 16:01:13', 8, '[\"A(2;-2;4)\",\"B(2;0;0)\",\"(1;-1;2)\",\"(-2;2;-4)\"]'),
(124, 12, 2, '', '<p>Cho hình trụ có diện tích xung quanh Sxq = 36<strong>π </strong>và chiều cao h=6. Bán kính của hình trụ đã cho bằng&nbsp;</p>', '[\"C\"]', '2024-09-25 11:28:47', 8, '[\"6\",\"9\",\"3\",\"12\"]'),
(125, 12, 2, '', '<p>Dãy số nào dưới đây là một cấp số cộng</p>', '[\"A\"]', '2024-09-25 11:29:36', 8, '[\"1,3,5,7\",\"1,0,2,4\",\"1,3,5,10\",\"1,2,3,-4\"]'),
(126, 12, 2, '', '<p>Với a,b là các số thực dương tùy ý và a<strong>≠</strong>1, $\\log_{ a^2 } &nbsp;b^2 = $</p>', '[\"A\"]', '2024-09-25 11:34:48', 8, '[\"$ \\\\log_{ a }  b $\",\"$ \\\\log_{ a^4 }  b $\",\"$ (\\\\log_{ a }  b)^2 $\",\"$\\\\log_{ a }  b^4$\"]'),
(127, 12, 2, '1727261290_Screenshot 2024-09-25 174742.png', '<p>Cho hàm số bậc bốn y =f(x) có đồ thị đường cong như hình bên. Số nghiệm thực của phương trình f(x) = $\\frac{3}{2}$</p>', '[\"B\"]', '2024-09-25 17:48:10', 8, '[\"3\",\"4\",\"0\",\"2\"]'),
(128, 12, 2, '', '<p>Cho khối lăng trụ tam giác có diện tích đáy B=6 và chiều cao h=3.Thể tích của khối lăng trụ đã cho là</p>', '[\"D\"]', '2024-09-25 17:49:14', 8, '[\"24\",\"6\",\"12\",\"18\"]'),
(129, 12, 2, '', '<p>Cho hàm số f(x) có đạo hàm liên tục trên R và f(1) = 3, f(2) = 1. Giá trị của &nbsp;$\\intop\\nolimits_{1}^{2}$f\'(x)dx bằng&nbsp;</p>', '[\"C\"]', '2024-09-25 17:51:17', 8, '[\"4\",\"2\",\"-2\",\"-4\"]'),
(130, 12, 2, '', '<p>Tiệm cận đứng của đồ thị hàm số y = $\\dfrac{4x-1}{3x+2}$ có phương trình là</p>', '[\"A\"]', '2024-09-25 17:54:12', 8, '[\"x = $-\\\\dfrac{2}{3}$\",\"x = $\\\\dfrac{4}{3}$\",\"y = $\\\\dfrac{4}{3}$\",\"y = $-\\\\dfrac{2}{3}$\"]'),
(131, 12, 2, '', '<p>Số phức z = $i + i^2 + i^3$ bằng</p>', '[\"A\"]', '2024-09-25 17:55:33', 8, '[\"-1\",\"-1 + 2i\",\"1\",\"i\"]'),
(132, 12, 2, '', '<p>Trên khoảng (-<strong>∞; +∞</strong>) hàm số F(x) = $\\dfrac{1}{2}sin2x$ là một nguyên hàm của hàm số nào dưới đây</p>', '[\"C\"]', '2024-09-25 17:59:43', 8, '[\"$f_3(x) = -\\\\dfrac{1}{2}cos2x$\",\"$f_4(x) = -\\\\dfrac{1}{4}cos2x$\",\"$f_2(x) = cos2x$\",\"$f_1(x) = -cos2x$\"]'),
(133, 12, 2, '', '<p>Nếu $ \\intop\\nolimits_{-2}^{1}f(x)dx = -1 $ và $ \\intop\\nolimits_{1}^{7}f(x)dx = -5 $ thì &nbsp;$\\intop\\nolimits_{-2}^{7}f(x)dx = &nbsp;$</p>', '[\"C\"]', '2024-09-25 18:01:51', 8, '[\"-4\",\"5\",\"-6\",\"4\"]'),
(134, 12, 2, '', '<p>Tập nghiệm của bất phương trình $ \\log_{\\dfrac{1}{2}} (x+2) &gt; -1$ là</p>', '[\"C\"]', '2024-09-25 18:05:45', 8, '[\"(-2;1)\",\"(0;+∞)\",\"(-2;0)\",\"(-∞;0)\"]'),
(135, 12, 2, '1727262725_Screenshot 2024-09-25 180937.png', '<p>Đồ thị của hàm số nào dưới đây có dạng đường cong trong hình bên?</p>', '[\"D\"]', '2024-09-25 18:12:05', 8, '[\"$y=-x^3 + 3x^2 + 3$\",\"$y=x^4 - 2x^2 - 4$\",\"$y=\\\\dfrac{x-2}{2x+1}$\",\"$y=x^3+3x^2-1$\"]'),
(136, 12, 2, '', '<p>Trong không gian Oxyz, cho hai điểm A(1;-2;3) và B(3;0;1) . Gọi (S) là mặt cầu nhận AB làm đường kính, tâm của (S) có tọa độ là</p>', '[\"A\"]', '2024-09-25 18:17:07', 8, '[\"(2;-1;2)\",\"(-1;-1;1)\",\"(4;-2;4)\",\"(1;1-1)\"]'),
(137, 12, 2, '', '<p>Nghiệm của phương trình $2^{2x} = 2^{x+6}$ là</p>', '[\"C\"]', '2024-09-25 18:18:15', 8, '[\"x=-6\",\"x=2\",\"x=6\",\"x=-2\"]'),
(138, 12, 2, '', '<p>Cho hàm số y=f(x) có đạo hàm f\'(x) =2x + 4, ∀(x) ∈ &nbsp;R. Hàm số đã cho nghịch biến trên</p><p>khoảng nào dưới đây?</p>', '[\"A\"]', '2024-09-25 18:23:02', 8, '[\"(-∞;-2)\",\"(2;4)\",\"(-2;+∞)\",\"(2;+∞)\"]'),
(139, 12, 2, '', '<p>Hàm số nào sau đây là hàm số mũ</p>', '[\"B\"]', '2024-09-25 18:25:48', 8, '[\"$y=x^2024$\",\"$y=2024^x$\",\"$y=\\\\log_{3} x $\",\"$y=x^(-4)$\"]'),
(140, 12, 2, '', '<p>Trên khoảng (0;+<strong>∞</strong>) đạo hàm của hàm số $y=x^\\dfrac{1}{7}$ là</p>', '[\"A\"]', '2024-09-25 18:30:19', 8, '[\"$y\' = \\\\dfrac{1}{7}x^\\\\dfrac{-6}{7}$\",\"$y\' = \\\\dfrac{1}{7}x^\\\\dfrac{6}{7}$\",\"$y\' = x^\\\\dfrac{-6}{7}$\",\"$y\' = \\\\dfrac{7}{8}x^\\\\dfrac{8}{7}$\"]'),
(141, 12, 2, '', '<p>Cho hình nón có bán kính đáy r=3 và độ dài đường sinh l=5. Chiều cao của hình nón đã cho bằng</p>', '[\"A\"]', '2024-09-25 18:31:57', 8, '[\"4\",\"5\",\"$\\\\sqrt{34}$\",\"2\"]'),
(142, 12, 2, '1727264077_Screenshot 2024-09-25 183414.png', '<p>Cho hàm số y=f(x) có bảng biến thiên như hình. Số điểm cực trị của hàm số đã cho là</p>', '[\"A\"]', '2024-09-25 18:34:37', 8, '[\"3\",\"2\",\"4\",\"1\"]'),
(143, 12, 2, '', '<p>Trong không gian Oxyz, cho hai vecto $\\vec{a}=(2;3;-1)$ và $\\vec{b}=(-3;2;-4)$. Vecto $\\vec{a}+\\vec{b}$ có tọa độ là</p>', '[\"C\"]', '2024-09-25 18:37:09', 8, '[\"(-1;-5;5)\",\"(-5;-1;-3)\",\"(-1;-5;-5)\",\"(1;-5;5)\"]'),
(144, 12, 2, '', '<p>Trong không gian Oxyz mặt phẳng đi qua điểm M(3;4;-2) và vuông góc với trục Oz có phương trình là</p>', '[\"B\"]', '2024-09-25 18:38:16', 8, '[\"y-4=0\",\"z + 2 =0\",\"x + y + z -5 =0\",\"x - 3 = 0\"]'),
(146, 12, 2, '', '<p>Cho khối chóp tứ giác có thể tích $V= 3a^3$ và diện tích đáy B = a^2 . Chiều cao cúa khối chóp đã cho bằng</p>', '[\"D\"]', '2024-09-25 18:39:38', 8, '[\"a\",\"6a\",\"3a\",\"9a\"]'),
(147, 12, 2, '', '<p>Có bao nhiêu cách sắp xếp 6 người thành một hàng ngang?</p>', '[\"B\"]', '2024-09-25 18:40:03', 8, '[\"36\",\"720\",\"1\",\"6\"]'),
(148, 12, 2, '', '<p>Trên mặt phẳng tọa độ, M(2;-5) là điểm biểu diễn của số phức z. Phần thực của z</p>', '[\"C\"]', '2024-09-25 18:40:51', 8, '[\"-5\",\"-2\",\"2\",\"5\"]'),
(149, 12, 2, '', '<p>Cho hình chóp .SABCD có đáy là hình vuông cạnh a, cạnh bên SA vuông góc với mặt phẳng đáy và $SA= \\sqrt{2}a $ . Khoảng cách từ C đến mặt phẳng (SBD) bằng</p>', '[\"D\"]', '2024-09-25 18:44:48', 8, '[\"$\\\\dfrac{2 \\\\sqrt{10} }{5}$a\",\"$\\\\dfrac{\\\\sqrt{6} }{3}$a\",\"$\\\\dfrac{\\\\sqrt{10} }{10}$a\",\"$\\\\dfrac{\\\\sqrt{10} }{5}$a\"]'),
(150, 12, 1, '', '<p>Cho số phức z = 3 + 4i. Moodun của số phức iz bằng</p>', '[\"D\"]', '2024-09-25 18:54:44', 8, '[\"7\",\"49\",\"25\",\"5\"]'),
(151, 12, 2, '', '<p>Trên hai tia ,OxOy của góc nhọn xOy lần lượt cho 5 điểm và 6 điểm phân biệt khác O . Chọn ngã̃u nhiên 3 điểm từ 12 điểm (gồm điểm O và 11 điểm đã cho), xác suất để 3 điểm chọn được là ba đỉnh của một tam giác bằng</p>', '[\"C\"]', '2024-09-25 18:56:57', 8, '[\"$\\\\dfrac{19}{22}$\",\"$\\\\dfrac{27}{44}$\",\"$\\\\dfrac{3}{4}$\",\"$\\\\dfrac{39}{44}$\"]'),
(152, 12, 2, '', '<p>Một ô tô đang chuyển động với vận tốc 20m/s thì người lái xe đạp phanh. Từ thời điểm đó, ô tô chuyển động thẳng, chậm dần đều với vận tốc biến thiên theo thời gian được xác định bởi quy luật v(t)= -4t + 20 (m/s) trong đó t là khoảng thời gian tính bằng', '[\"B\"]', '2024-09-25 18:58:50', 8, '[\"32 m\",\"50 m\",\"48 m\",\"30m\"]'),
(153, 12, 2, '', '<p>Trong không gian Oxyz cho hai điểm A(1;2;3) và B(3;-2;5). Gọi M là điểm thỏa mãn $ \\overrightarrow{MB} = 3 \\overrightarrow{MA}$, độ dài của vecto $ \\overrightarrow{OM}$</p>', '[\"B\"]', '2024-09-25 19:02:30', 8, '[\"$\\\\dfrac{ \\\\sqrt{74} }{2}$\",\"$2 \\\\sqrt{2} $\",\"8\",\"$2 \\\\sqrt{14} $\"]'),
(154, 12, 2, '', '<p>Cho hình chóp S.ABC có đáy là tam giác vuông cân tại A , BC=2a , cạnh bên SA vuông góc với mặt phẳng đáy và SA=$ \\sqrt{3} $a . Góc giữa hai mặt phẳng (SBC) và (ABC) bằng</p>', '[\"A\"]', '2024-09-25 19:04:41', 8, '[\"$60^\\\\circ$\",\"$90^\\\\circ$\",\"$30^\\\\circ$\",\"$45^\\\\circ$\"]'),
(155, 12, 2, '', '<p>Giá trị lớn nhất của hàm số f(x) = $-6x^3 + 27x^2 - 16x + 1$ trên đoạn [1;-5] bằng</p>', '[\"B\"]', '2024-09-25 19:07:02', 8, '[\"6\",\"$\\\\dfrac{329}{9}$\",\"$-\\\\dfrac{14}{9}$\",\"-154\"]'),
(156, 12, 2, '1727266308_Screenshot 2024-09-25 191116.png', '<p>Cho hàm số bậc bốn y=f(x) . Hàm số y=f\'(x) có đồ thị là đường cong trong hình bên. Hàm số y=f(x) đồng biến trên khoảng nào dưới đây?</p>', '[\"D\"]', '2024-09-25 19:08:30', 8, '[\"(-∞;-1)\",\"(-1;2)\",\"(1;2)\",\"(-1;1)\"]'),
(157, 12, 2, '', '<p>Với a,b là hai số thực lớn hơn 1, &nbsp;$\\log_{ab}b$ bằng</p>', '[\"A\"]', '2024-09-25 19:10:42', 8, '[\"$\\\\dfrac{1}{1+\\\\log_{b}a}$\",\"$\\\\dfrac{1}{\\\\log_{b}a}$\",\"$1-\\\\log_{b}a$\",\"$1+\\\\log_{b}a$\"]'),
(158, 12, 2, '', '<p>Cho hàm số y=f(x) có f(e) = $\\dfrac{1}{5}$ và f\'(x) = $\\dfrac{1}{3}lnx$ ∀(x) thuộc (0;+<strong>∞). </strong>Biết $\\intop\\nolimits_{e}^{e^3} \\dfrac{f(x)}{x^2} = ae^(-3) + be^(-1) + c$ với a,b,c là các số hữu tỉ, giá trị của a-b+c thuộc khoảng nào dưới đ', '[\"B\"]', '2024-09-25 19:18:23', 8, '[\"$(\\\\dfrac{1}{2};\\\\dfrac{3}{4})$\",\"$(\\\\dfrac{1}{4};\\\\dfrac{1}{2})$\",\"$(\\\\dfrac{3}{4};1)$\",\"$(0;\\\\dfrac{1}{4})$\"]'),
(159, 12, 2, '', '<p>Có bao nhiêu số nguyên a lớn hơn 1 sao cho ứng với mỗi số a tồn tại không quá 4 số nguyên b thỏa mãn $5^(b^2) &lt; 25^(-b) . a^(b+2)$</p>', '[\"D\"]', '2024-09-25 19:20:01', 8, '[\"125\",\"100\",\"99\",\"124\"]'),
(160, 12, 2, '', '<p>Cho hàm số bậc bốn y=f(x) có ba điểm cực trị là $-\\dfrac{3}{2};2;\\dfrac{11}{2}$ và đạt giá trị nhỏ nhất trên ℝ. Bất phương trình $f(x) \\leqslant m$ có nghiệm thuộc đoạn [0;3] khi và chỉ khi</p>', '[\"C\"]', '2024-09-25 19:24:22', 8, '[\"$m \\\\geqslant f(3)$\",\"$f(2) \\\\geqslant m \\\\geqslant  f(3)$\",\"$m \\\\geqslant f(0) $\",\"$m \\\\geqslant f(2) $\"]'),
(161, 12, 2, '', '<p>Có bao nhiêu giá trị nguyên của tham số m sao cho ứng với mỗi m tồn tại đúng hai số phức z thỏa mãn | z - 1 - 5i | + | z-1+5i | = 10 và | z-2-i | = m ?</p>', '[\"D\"]', '2024-09-25 19:25:36', 8, '[\"5\",\"4\",\"2\",\"3\"]'),
(162, 12, 2, '', '<p>Xét hàm số \\( f(x) = ax^3 + bx^2 + cx + d \\) \\( (a, b, c, d \\in \\mathbb{R}, a &gt; 0) \\) có hai điểm cực trị \\( x_1, x_2 \\) (với \\( x_1 &lt; x_2 \\)) thỏa mãn \\( x_1 + x_2 = 0 \\). Hình phẳng giới hạn bởi đường \\( y = f\'(x)f\'\'(x) \\) và trục hoành có diện tích bằng \\( \\frac{9}{4} \\). Biết: $\\int_{x_1}^{x_2} \\frac{f\'(x)}{3x^2 + 1} dx = -\\frac{7}{2} $ giá trị của $ \\int_{0}^{x_2} (x + 2) f\'\'(x) dx $ thuộc khoảng nào dưới đây?</p>', '[\"A\"]', '2024-09-25 19:32:21', 8, '[\"(6;7)\",\"(-1;0)\",\"(0;1)\",\"(-7;-6)\"]'),
(163, 12, 2, '', '<p>Cho hình lăng trụ đứng ABC.A\'B\'C\' có đáy ABC là tam giác vuông cân tại ,AB=a . Biết góc giữa hai mặt phẳng (ABC) và (A\'BC) bằng $30^\\circ$, thể tích của khối lăng trụ đã cho&nbsp;</p>', '[\"A\"]', '2024-09-25 19:44:01', 8, '[\"$\\\\frac{\\\\sqrt{6}}{12} a^3 $\",\"$\\\\frac{\\\\sqrt{6}}{36} a^3$\",\"$\\\\frac{\\\\sqrt{6}}{4} a^3$\",\"$\\\\frac{3\\\\sqrt{6}}{4} a^3$\"]'),
(164, 12, 2, '', '<p>Trong không gian \\( Oxyz \\), cho hai đường thẳng \\( d_1: \\frac{x - 2}{1} = \\frac{y - 4}{3} = \\frac{z + 3}{5} \\) và \\( d_2: \\frac{x + 2}{1} = \\frac{y + 2}{1} = \\frac{z + 1}{1} \\). Trong các mặt cầu tiếp xúc với cả hai đường thẳng \\( d_1 \\) và \\( d_2 \\),', '[]', '2024-09-25 19:45:49', 8, '[\"\\\\( (x + 1)^2 + y^2 + (z - 1)^2 = 6 \\\\)\",\"\\\\( x^2 + (y - 3)^2 + (z + 4)^2 = 6 \\\\)\",\"\\\\( (x - 1)^2 + (y + 2)^2 + (z + 1)^2 = 6 \\\\)\",\"\\\\( x^2 + (y + 1)^2 + z^2 = 6 \\\\)\"]'),
(165, 12, 2, '', '<p>Cho hàm số \\( f(x) = \\frac{2}{x^3} + \\ln\\left(\\frac{x - 3}{x + 3}\\right) \\). Có bao nhiêu số nguyên \\( a \\in (-\\infty; 2100) \\) thỏa mãn: \\[ f(a - 2024) + f(6a - 27) \\geq 0 \\]</p>', '[\"D\"]', '2024-09-25 19:46:55', 8, '[\"2096\",\"288\",\"1807\",\"360\"]'),
(166, 12, 2, '', '<p>Xét phương trình bậc hai \\( a z^2 + b z + c = 0 \\) \\( (a, b, c \\in \\mathbb{R}, a \\neq 0) \\) có hai nghiệm phức \\( z_1, z_2 \\) có phần ảo khác 0 và $ \\left| z_2 \\right| = \\left| z_1 - \\frac{1}{9} \\right| = \\left| z_1 - z_2 \\right|. $ Giả sử \\( \\left| z_1 \\right| = \\frac{1}{\\sqrt{k}} \\) và \\( w \\) là số phức thỏa mãn \\( c w^2 + b w + a = 0 \\), có bao nhiêu số nguyên dương \\( k \\) sao cho ứng với mỗi \\( k \\) tồn tại đúng 9 số phức \\( z_3 \\) có phần ảo nguyên, \\( z_3 - w \\) là số thuần ảo và $ \\left| z_3 \\right| \\leq \\left| w \\right|? $</p>', '[\"B\"]', '2024-09-25 19:50:49', 8, '[\"23\",\"22\",\"11\",\"12\"]'),
(167, 12, 2, '', '<p>Cho hình chóp \\( S.ABC \\) có đáy là tam giác vuông cân tại \\( A \\), \\( AB = 2a \\), mặt bên \\( SAB \\) là tam giác đều nằm trong mặt phẳng vuông góc với mặt phẳng đáy. Diện tích của mặt cầu ngoại tiếp hình chóp đã cho bằng:&nbsp;</p>', '[\"C\"]', '2024-09-25 19:55:57', 8, '[\"$\\\\frac{25\\\\pi}{9} a^2 \\\\quad$\",\"$\\\\frac{25\\\\pi}{3} a^2 \\\\quad$\",\"$\\\\frac{28\\\\pi}{3} a^2 \\\\quad$\",\"$\\\\frac{28\\\\pi}{9} a^2\\\\quad$\"]'),
(168, 12, 2, '', '<p>Trong không gian \\( Oxyz \\), cho hai điểm \\( A(1; 6; -1) \\), \\( B(2; -4; -1) \\) và mặt cầu \\( (S) \\) tâm \\( I(1; 2; -1) \\) đi qua \\( A \\). Điểm \\( M(a; b; c) \\) (với \\( c &gt; 0 \\)) thuộc \\( (S) \\) sao cho tam giác \\( IAM \\) là tam giác đều, có diện tích bằng \\( 2 \\sqrt{7} \\) và khoảng cách giữa hai đường thẳng \\( BM \\) và \\( IA \\) lớn nhất. Giá trị của \\( a + b + c \\) thuộc khoảng nào dưới đây?&nbsp;</p>', '[\"D\"]', '2024-09-25 19:57:37', 8, '[\"$\\\\left( 1; \\\\frac{3}{2} \\\\right) \\\\quad$\",\"$\\\\left( \\\\frac{3}{2}; 2 \\\\right) \\\\quad$\",\"$\\\\left( 5; \\\\frac{2}{3} \\\\right) \\\\quad$\",\"$\\\\left( 2; \\\\frac{5}{2} \\\\right) $\"]'),
(169, 0, 0, '', '<p>Xét hàm số bậc bốn \\( y = f(x) \\) có \\( f\'(-1) = -5 \\). Hàm số \\( y = f\'(x) \\) đồng biến trên khoảng \\( (-\\infty; +\\infty) \\), \\( f\'(4) = 0 \\) và \\( f\'\'(-1) = a \\). Có bao nhiêu số nguyên \\( a \\in (-100; 0) \\) sao cho ứng với mỗi \\( a \\), hàm số \\( y = \\left| f(x) + \\frac{5}{x^2} \\right| \\) có đúng 3 điểm cực trị thuộc khoảng \\( (-\\infty; +\\infty) \\)?&nbsp;</p>', '[\"C\"]', '2024-09-25 19:58:07', 8, '[\"9\",\"89\",\"10\",\"90\"]'),
(170, 12, 2, '', '<p>Trong không gian \\( Oxyz \\), cho điểm \\( A(1; 2; -1) \\) và mặt phẳng \\( (P): 2x - z + 1 = 0 \\). Đường thẳng đi qua \\( A \\) và vuông góc với \\( (P) \\) có phương trình là:&nbsp;</p>', '[\"D\"]', '2024-09-25 20:02:16', 8, '[\"$\\\\begin{cases}  x = 2 + t \\\\\\\\ y = 2t \\\\\\\\ z = -1 - t  \\\\end{cases} \\\\quad$\",\"$\\\\begin{cases}  x = 1 + 2t \\\\\\\\ y = 2 - t \\\\\\\\ z = -1 + t  \\\\end{cases} \\\\quad$\",\"$\\\\begin{cases}  x = -1 + 2t \\\\\\\\ y = -2 \\\\\\\\ z = 1 - t  \\\\end{cases} \\\\quad$\",\"$\\\\begin{cases}  x = 1 + 2t \\\\\\\\ y = 2 \\\\\\\\ z = -1 - t  \\\\end{cases}$\"]'),
(184, 12, 5, '', '<p>Biển nào cấm xe ô tô tải ?&nbsp;</p>', '[\"B\"]', '2024-09-27 16:39:49', 8, '[\"\",\"\",\"\"]'),
(185, 12, 5, '', '<p>Biển nào cấm xe kéo ?</p>', '[\"C\"]', '2024-09-27 16:40:50', 8, '[\"\",\"\",\"\"]'),
(186, 12, 5, '', '<p>Biển nào cho phép xe con được vượt ?</p>', '[\"A\"]', '2024-09-27 16:42:13', 8, '[\"\",\"\",\"\"]'),
(187, 12, 5, '', '<p>Biển nào cấm xe tải vượt ?</p>', '[\"C\"]', '2024-09-27 16:42:41', 8, '[\"\",\"\",\"\"]'),
(188, 12, 5, '', '<p>Biển nào cấm rẽ trái</p>', '[\"A\"]', '2024-09-27 16:43:28', 8, '[\"\",\"\"]'),
(189, 12, 5, '', '<p>Biển nào cấm rẽ trái</p>', '[\"A\"]', '2024-09-27 16:44:20', 8, '[\"\",\"\"]');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `questions_exam`
--

CREATE TABLE `questions_exam` (
  `id` int(5) UNSIGNED NOT NULL,
  `id_ques` int(5) UNSIGNED NOT NULL,
  `id_exam` int(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `questions_exam`
--

INSERT INTO `questions_exam` (`id`, `id_ques`, `id_exam`) VALUES
(70, 62, 7),
(71, 76, 7),
(72, 65, 7),
(74, 53, 7),
(75, 79, 7),
(76, 56, 7),
(77, 75, 7),
(79, 59, 7),
(82, 66, 7),
(84, 80, 7),
(85, 69, 7),
(86, 78, 7),
(87, 73, 7),
(88, 63, 7),
(89, 67, 7),
(90, 70, 8),
(92, 73, 8),
(93, 77, 8),
(94, 81, 8),
(95, 121, 9),
(96, 65, 9),
(97, 120, 9),
(98, 64, 9),
(107, 119, 16),
(108, 120, 16),
(109, 121, 16),
(110, 118, 16),
(111, 137, 17),
(112, 128, 17),
(113, 153, 17),
(114, 127, 17),
(115, 166, 17),
(116, 126, 17),
(117, 125, 17),
(118, 162, 17),
(119, 155, 17),
(120, 148, 17),
(121, 118, 17),
(122, 163, 17),
(123, 136, 17),
(124, 158, 17),
(125, 141, 17),
(126, 134, 17),
(127, 131, 17),
(128, 143, 17),
(129, 146, 17),
(130, 156, 17),
(131, 167, 17),
(132, 170, 17),
(133, 119, 17),
(134, 144, 17),
(135, 164, 17),
(136, 139, 17),
(137, 147, 17),
(138, 154, 17),
(139, 149, 17),
(140, 157, 17),
(141, 160, 17),
(142, 138, 17),
(143, 161, 17),
(144, 132, 17),
(145, 120, 17),
(146, 130, 17),
(147, 159, 17),
(148, 165, 17),
(149, 140, 17),
(150, 168, 17),
(151, 121, 17),
(152, 133, 17),
(153, 124, 17),
(154, 151, 17),
(155, 152, 17),
(156, 129, 17),
(157, 142, 17),
(158, 135, 17),
(159, 184, 18),
(160, 188, 18),
(161, 186, 18),
(162, 185, 18),
(163, 189, 18);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `results`
--

CREATE TABLE `results` (
  `id` int(5) UNSIGNED NOT NULL,
  `id_user` int(5) UNSIGNED NOT NULL,
  `id_exam` int(5) UNSIGNED NOT NULL,
  `score` int(10) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `duration` int(10) NOT NULL,
  `correct_question` int(3) NOT NULL,
  `incorrect_question` int(3) NOT NULL,
  `blank_question` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `results`
--

INSERT INTO `results` (`id`, `id_user`, `id_exam`, `score`, `created_at`, `duration`, `correct_question`, `incorrect_question`, `blank_question`) VALUES
(68, 7, 8, 0, '2024-09-23 13:52:22', 21, 0, 5, 0),
(69, 7, 8, 8, '2024-09-23 14:50:56', 5, 4, 1, 0),
(70, 7, 16, 0, '2024-09-24 16:43:20', 300, 0, 0, 4),
(71, 7, 16, 0, '2024-09-25 13:59:32', 300, 0, 0, 4),
(72, 7, 17, 3, '2024-09-25 20:11:48', 179, 15, 33, 0),
(73, 7, 18, 4, '2024-09-28 09:17:18', -910, 2, 3, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `result_detail`
--

CREATE TABLE `result_detail` (
  `id` int(5) UNSIGNED NOT NULL,
  `id_results` int(5) UNSIGNED NOT NULL,
  `id_question` int(5) UNSIGNED NOT NULL,
  `answer` enum('A','B','C','D') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `result_detail`
--

INSERT INTO `result_detail` (`id`, `id_results`, `id_question`, `answer`) VALUES
(129, 68, 70, 'C'),
(130, 68, 70, 'D'),
(133, 68, 73, 'B'),
(134, 68, 73, 'C'),
(135, 68, 77, 'B'),
(136, 68, 77, 'C'),
(137, 68, 81, 'B'),
(138, 68, 81, 'C'),
(139, 69, 70, 'B'),
(141, 69, 73, 'B'),
(142, 69, 77, 'C'),
(143, 69, 81, 'B'),
(144, 72, 137, 'A'),
(145, 72, 128, 'A'),
(146, 72, 153, 'A'),
(147, 72, 127, 'A'),
(148, 72, 166, 'A'),
(149, 72, 126, 'A'),
(150, 72, 125, 'A'),
(151, 72, 162, 'A'),
(152, 72, 155, 'A'),
(153, 72, 148, 'A'),
(154, 72, 118, 'A'),
(155, 72, 163, 'A'),
(156, 72, 136, 'A'),
(157, 72, 158, 'A'),
(158, 72, 141, 'A'),
(159, 72, 134, 'A'),
(160, 72, 131, 'A'),
(161, 72, 143, 'A'),
(162, 72, 146, 'A'),
(163, 72, 156, 'A'),
(164, 72, 167, 'A'),
(165, 72, 170, 'A'),
(166, 72, 119, 'A'),
(167, 72, 144, 'A'),
(168, 72, 164, 'A'),
(169, 72, 139, 'A'),
(170, 72, 147, 'A'),
(171, 72, 154, 'A'),
(172, 72, 149, 'A'),
(173, 72, 157, 'A'),
(174, 72, 160, 'A'),
(175, 72, 138, 'A'),
(176, 72, 161, 'A'),
(177, 72, 132, 'A'),
(178, 72, 120, 'A'),
(179, 72, 130, 'A'),
(180, 72, 159, 'A'),
(181, 72, 165, 'A'),
(182, 72, 140, 'A'),
(183, 72, 168, 'A'),
(184, 72, 121, 'A'),
(185, 72, 133, 'A'),
(186, 72, 124, 'A'),
(187, 72, 151, 'A'),
(188, 72, 152, 'A'),
(189, 72, 129, 'A'),
(190, 72, 142, 'A'),
(191, 72, 135, 'A'),
(192, 73, 184, 'A'),
(193, 73, 188, 'B'),
(194, 73, 186, 'A'),
(195, 73, 185, 'A'),
(196, 73, 189, 'A');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(5) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `email`, `role`, `create_at`) VALUES
(1, 'namNguyen', '4297f44b13955235245b2497399d7a93', 'nam@gmail.com', 'admin', '0000-00-00 00:00:00'),
(7, 'nam', '4297f44b13955235245b2497399d7a93', 'nam1234@gmail.com', 'student', '2024-09-10 10:00:47'),
(8, 'nam', '4297f44b13955235245b2497399d7a93', 'admin@gmail.com', 'admin', '2024-09-10 11:29:26'),
(9, 'nam', '4297f44b13955235245b2497399d7a93', 'nam12345@gmail.com', 'admin', '2024-09-12 11:35:06');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `category_exams`
--
ALTER TABLE `category_exams`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Chỉ mục cho bảng `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `category` (`category`);

--
-- Chỉ mục cho bảng `image_answers`
--
ALTER TABLE `image_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idQues` (`idQues`);

--
-- Chỉ mục cho bảng `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Chỉ mục cho bảng `questions_exam`
--
ALTER TABLE `questions_exam`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_exam` (`id_exam`),
  ADD KEY `id_ques` (`id_ques`);

--
-- Chỉ mục cho bảng `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_exam` (`id_exam`);

--
-- Chỉ mục cho bảng `result_detail`
--
ALTER TABLE `result_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_results` (`id_results`),
  ADD KEY `id_question` (`id_question`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `category_exams`
--
ALTER TABLE `category_exams`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT cho bảng `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `image_answers`
--
ALTER TABLE `image_answers`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT cho bảng `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT cho bảng `questions_exam`
--
ALTER TABLE `questions_exam`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT cho bảng `results`
--
ALTER TABLE `results`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT cho bảng `result_detail`
--
ALTER TABLE `result_detail`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`category`) REFERENCES `category_exams` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `image_answers`
--
ALTER TABLE `image_answers`
  ADD CONSTRAINT `image_answers_ibfk_1` FOREIGN KEY (`idQues`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `questions_exam`
--
ALTER TABLE `questions_exam`
  ADD CONSTRAINT `questions_exam_ibfk_1` FOREIGN KEY (`id_exam`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_exam_ibfk_2` FOREIGN KEY (`id_ques`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`id_exam`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `result_detail`
--
ALTER TABLE `result_detail`
  ADD CONSTRAINT `result_detail_ibfk_1` FOREIGN KEY (`id_results`) REFERENCES `results` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `result_detail_ibfk_2` FOREIGN KEY (`id_question`) REFERENCES `questions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
