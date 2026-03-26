-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2026 at 09:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exam_monitoring`
--

-- --------------------------------------------------------

--
-- Table structure for table `cheating_events`
--

CREATE TABLE `cheating_events` (
  `event_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `cheating_type_id` int(11) NOT NULL,
  `status` enum('suspected','confirmed','rejected') DEFAULT 'suspected',
  `confidence_score` decimal(5,2) DEFAULT NULL,
  `snapshot_path` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `event_time` datetime DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cheating_events`
--

INSERT INTO `cheating_events` (`event_id`, `student_id`, `cheating_type_id`, `status`, `confidence_score`, `snapshot_path`, `video_path`, `event_time`, `created_at`) VALUES
(15, 56, 3, 'suspected', 60.00, 'string', 'string', '2026-03-05 23:52:50', '2026-03-05 20:52:50'),
(16, 56, 2, 'suspected', 0.00, 'string', 'string', '2026-03-06 00:00:58', '2026-03-05 21:00:58'),
(17, 56, 5, 'suspected', 0.62, NULL, NULL, '2026-03-07 14:27:16', '2026-03-07 11:27:16'),
(18, 56, 5, 'suspected', 0.73, NULL, NULL, '2026-03-07 14:27:16', '2026-03-07 11:27:16'),
(19, 56, 5, 'suspected', 0.56, NULL, NULL, '2026-03-07 14:27:22', '2026-03-07 11:27:22'),
(20, 56, 5, 'suspected', 0.78, NULL, NULL, '2026-03-07 14:27:23', '2026-03-07 11:27:23'),
(21, 56, 5, 'suspected', 0.45, NULL, NULL, '2026-03-07 14:27:23', '2026-03-07 11:27:23'),
(22, 56, 5, 'suspected', 0.31, NULL, NULL, '2026-03-07 14:27:24', '2026-03-07 11:27:24'),
(23, 56, 5, 'suspected', 0.30, NULL, NULL, '2026-03-07 14:27:26', '2026-03-07 11:27:26'),
(24, 56, 5, 'suspected', 0.30, NULL, NULL, '2026-03-07 14:27:26', '2026-03-07 11:27:26'),
(25, 56, 4, 'confirmed', 0.35, NULL, NULL, '2026-03-07 14:30:51', '2026-03-07 11:30:51'),
(26, 56, 4, 'suspected', 0.46, NULL, NULL, '2026-03-07 14:30:52', '2026-03-07 11:30:52'),
(27, 56, 5, 'rejected', 0.55, NULL, NULL, '2026-03-07 14:31:56', '2026-03-07 11:31:56'),
(28, 56, 5, 'suspected', 0.78, NULL, NULL, '2026-03-07 14:31:56', '2026-03-07 11:31:56'),
(29, 56, 5, 'suspected', 0.72, NULL, NULL, '2026-03-08 22:12:05', '2026-03-08 19:12:05'),
(30, 56, 5, 'suspected', 0.34, NULL, NULL, '2026-03-09 00:47:20', '2026-03-08 21:47:20'),
(31, 56, 5, 'suspected', 0.49, NULL, NULL, '2026-03-09 00:47:23', '2026-03-08 21:47:23'),
(32, 56, 5, 'suspected', 0.29, NULL, NULL, '2026-03-09 00:47:23', '2026-03-08 21:47:23'),
(33, 56, 5, 'suspected', 0.34, NULL, NULL, '2026-03-09 00:51:52', '2026-03-08 21:51:52'),
(34, 56, 5, 'suspected', 0.64, NULL, NULL, '2026-03-09 00:51:52', '2026-03-08 21:51:52'),
(35, 56, 6, 'suspected', 0.00, NULL, NULL, '2026-03-09 01:21:45', '2026-03-08 22:21:45'),
(36, 56, 6, 'suspected', 0.00, NULL, NULL, '2026-03-09 01:21:45', '2026-03-08 22:21:45'),
(37, 56, 6, 'rejected', 0.00, NULL, NULL, '2026-03-09 01:21:45', '2026-03-08 22:21:45'),
(38, 56, 5, 'suspected', 0.70, 'evidence/snapshot_1773505590.jpg', 'evidence/video_1773505590.avi', '2026-03-14 19:26:31', '2026-03-14 16:26:31'),
(39, 56, 5, 'suspected', 0.78, 'evidence/snapshot_1773523929.jpg', NULL, '2026-03-15 00:32:10', '2026-03-14 21:32:10'),
(40, 56, 5, 'suspected', 0.69, 'evidence/snapshot_1773524999.jpg', NULL, '2026-03-15 00:49:59', '2026-03-14 21:49:59'),
(41, 56, 7, 'suspected', 0.90, 'evidence/snapshot_1773525150.jpg', NULL, '2026-03-15 00:52:30', '2026-03-14 21:52:30'),
(42, 56, 7, 'suspected', 0.90, 'evidence/snapshot_1773525457.jpg', 'evidence/video_1773525457.avi', '2026-03-15 00:57:39', '2026-03-14 21:57:39'),
(43, 56, 5, 'suspected', 0.56, 'evidence/snapshot_1773525790.jpg', 'evidence/video_1773525790.avi', '2026-03-15 01:03:12', '2026-03-14 22:03:12'),
(44, 56, 6, 'suspected', 0.00, 'evidence/snapshot_1773610000.jpg', 'evidence/video_1773610000.avi', '2026-03-16 00:26:43', '2026-03-15 21:26:43'),
(45, 56, 5, 'suspected', 0.69, 'evidence/snapshot_1773610166.jpg', NULL, '2026-03-16 00:29:26', '2026-03-15 21:29:26'),
(46, 56, 7, 'suspected', 0.90, 'evidence/snapshot_1773610345.jpg', 'evidence/video_1773610345.avi', '2026-03-16 00:32:26', '2026-03-15 21:32:26'),
(47, 56, 5, 'suspected', 0.86, 'evidence/snapshot_1773610843.jpg', 'evidence/video_1773610843.avi', '2026-03-16 00:40:43', '2026-03-15 21:40:43'),
(48, 56, 5, 'suspected', 0.64, 'evidence/snapshot_1773611373.jpg', 'evidence/video_1773611373.avi', '2026-03-16 00:49:35', '2026-03-15 21:49:35'),
(49, 56, 5, 'suspected', 0.67, 'evidence/snapshot_1773611950.jpg', 'evidence/video_1773611950.mp4', '2026-03-16 00:59:13', '2026-03-15 21:59:13'),
(50, 56, 5, 'suspected', 0.51, 'C:/xampp/htdocs/exam_monitoring2/evidence/snapshot_1773612596.jpg', 'C:/xampp/htdocs/exam_monitoring2/evidence/video_1773612596.mp4', '2026-03-16 01:09:59', '2026-03-15 22:09:59'),
(51, 56, 5, 'suspected', 0.46, 'evidence/snapshot_1773613056.jpg', 'evidence/video_1773613056.mp4', '2026-03-16 01:17:40', '2026-03-15 22:17:40'),
(52, 56, 4, 'suspected', 0.40, 'evidence/snapshot_1774031376.jpg', 'evidence/video_1774031376.mp4', '2026-03-20 21:29:39', '2026-03-20 18:29:39'),
(53, 56, 5, 'confirmed', 0.58, 'evidence/snapshot_1774031983.jpg', 'evidence/video_1774031983.mp4', '2026-03-20 21:39:46', '2026-03-20 18:39:46'),
(54, 56, 5, 'rejected', 0.60, 'evidence/snapshot_1774033209.jpg', 'evidence/video_1774033209.avi', '2026-03-20 22:00:13', '2026-03-20 19:00:13'),
(55, 56, 6, 'rejected', 0.00, 'evidence/snapshot_1774034097.jpg', 'evidence/video_1774034097.mp4', '2026-03-20 22:15:00', '2026-03-20 19:15:00'),
(56, 56, 5, 'rejected', 0.69, 'evidence/snapshot_1774035109.jpg', 'evidence/video_1774035109.mp4', '2026-03-20 22:31:52', '2026-03-20 19:31:52'),
(57, 56, 5, 'suspected', 0.67, 'evidence/snapshot_1774127061.jpg', 'evidence/video_1774127061.mp4', '2026-03-22 00:04:26', '2026-03-21 21:04:26'),
(58, 56, 5, 'suspected', 0.51, 'evidence/snapshot_1774127596.jpg', 'evidence/video_1774127596.mp4', '2026-03-22 00:13:20', '2026-03-21 21:13:20'),
(59, 56, 6, 'suspected', 0.00, 'evidence/snapshot_1774127623.jpg', 'evidence/video_1774127623.mp4', '2026-03-22 00:13:47', '2026-03-21 21:13:47'),
(60, 56, 5, 'suspected', 0.29, 'evidence/snapshot_1774128001.jpg', 'evidence/video_1774128001.mp4', '2026-03-22 00:20:09', '2026-03-21 21:20:09');

-- --------------------------------------------------------

--
-- Table structure for table `cheating_types`
--

CREATE TABLE `cheating_types` (
  `cheating_type_id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cheating_types`
--

INSERT INTO `cheating_types` (`cheating_type_id`, `type_name`, `description`) VALUES
(1, 'استخدام الهاتف', 'تم اكتشاف هاتف محمول أثناء الاختبار'),
(2, 'وجود شخص آخر', 'تم اكتشاف شخص إضافي في الكاميرا'),
(3, 'استخدام سماعات', 'تم اكتشاف سماعات أثناء الاختبار'),
(4, 'النظر بعيداً عن الشاشة', 'الطالب ينظر بعيداً لفترة طويلة'),
(5, 'حركة رأس غير طبيعية', 'حركات رأس متكررة تشير لاحتمال الغش'),
(6, 'ضوضاء أو صوت مرتفع', 'تم اكتشاف صوت مرتفع في الميكروفون'),
(7, 'محاولة مغادرة الكاميرا', 'الطالب خرج من إطار الكاميرا');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'video_before_seconds', '3'),
(2, 'video_after_seconds', '5'),
(3, 'email_enabled', '1'),
(4, 'save_video', '1'),
(5, 'save_snapshot', '1');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `student_number` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `major` varchar(100) DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `face_embedding` longblob NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_number`, `name`, `major`, `level`, `face_embedding`, `image_path`, `created_at`) VALUES
(56, '89988989', 'مبارك سالم عبدالله القوينص', 'CS', 'الرابع', 0xe78a1140b0c9e5bf9ed2353f8e3adbbe1c2cd3be9f45ed3ee011023fa98e423fb95bf0becfca6ebfe1c8633fae016c3f81cf073f2a0ac0be3627093fdf8a2bbf4148bdbf0cf523be5c0d1ebf5af6af3ef499843e275575bff4fd0bbf698498bf88af00c0d84f423d29f37fbfdf7e21bf8029923bcce2ff3ef13df03f21052bbfeab540be9a0a913f3ee45b40d623723fb203fa3df4b3c43e62a62fbf56e08d3ea86db63d89605dbf409b353ed35a92bfdfb4a9bf744118bd5463113f9d2c263fb185643faa8713400c6b4f3fdb5a0f3f8034cb3face28fbfb1ca33be85387ebf1eccf4bf0236a0bff5f429c0c8cc02c02a32d23fa6958d3f76bd9d3ed771ce3f62d7bfbf0ac510bea611cc3e720b73bf36519a3fc4ce1ebfadbd123f1ebbe43d6e75993f295ee3bb619f7b3f4db35cbef27039bfa21e7abee56176bf3b21d6be0e8d30bf333b1ac0a96f553f1366e9bd6a6534bfdee8453e1378eabe15710f3ec810273d22f32bbf751ad6bf8f2e8c3f2ac7e33e227703be4d3ab93fef88cd3ec00dedbd8bf8afbf4813393d4849e9bf600460bb3ce595be409597bcdb9251bfbc2b8b3f4088c63ca1e2abbeb082c5bf21e5733f41f48fbd0820813ebee14cbeed8d1d3f286c36bf426cbdbd7cf9cbbeae6f37bd0edc143fc0a0c53ed27ea63e66af07bfd16284bfac37fe3f92c1833e403180be924acb3faa3e9f3f4ccfa73fbb270fbf9e537a3fd720debe9fdd743ebe9fa43d8c1b58bf58b361bf3d73d3bd9c1eaf3ca6d5adbffac5ab3f917b16bf3dc3963fbe6b1a3f71d8a3bfba3e5a3f8e9ad0bf49aa123f8d52653fa406693f89b8ff3ea68e723f294ed73f86bb0abedacd073e148e283fc99184bffce8263e5e1644bf1e1a75bd256bf43f3647ad3e003a7b3a300ea9bf5829153f57ea184028a7953ea5a9cdbed4bbfe3fd61b49bea209853f9189943ff63675bf8628f93e4a45b6be4b5879bd20e48f3e4d6cf5be83a8283f98dee33c77cb7d3f409e52bf6c5a193fa3a0803efcf0cb3f4d1f013e59208d3f8f89e7bf048e94be485081bf7076613fa875303e3806fcbe2bad15bff9ad7d3fc85802c0f1eec4bd7eaaddbe178107be9ed691be0ba1ffbf60fcbbbb92addc3e7bef76bf0bed15bf55ac3ebf566275bfa096153e5204e7be58da87bf5bede33ff92a9dbfc81048bfc3b1f63f4aad93be8a01ec3f98cc69bf05b33940bf1eca3e592450bfeb2cf93efd84a63fbfafa33e593c083fbb6e1e3d86f387bf19ef233f36e1103f580498bf90801c3e5070d33f969e71bee980763fbfabb3bf7fc9d93fb40ad23f52ac18bf10ff00c0647f253f183a22bf2e100cbf4d9da33fc0d215be1c5bc6be9522dcbf1cdfe0bd2c194a3f6ad548be087d1dbc1145ca3ff065e93d42b8aebed6d58bbe38869abfea4c29bf0bd0a1be3d9b3a3fb14995bed05d2c3e2a64a93d0e861940160eafbe0025673e9c9e7cbfe650b83edd0a883f7a35433fb909fa3e3856a7bf7c2a2dbf6334ad3fdaf337bffe2c1440f72d3c3f37fd853f9819f23d9954a53f3e91833eb61d833e9d7e343fee140940becb953f81c94abf1d3073bff04fa0befe1fdcbeb4cd8fbedc26dc3eab2ba6befcef3bbf46f7293f760d72bf44d6863da4b875bfc6f720bff3e9c93fe5e51abf74ea30be9c7c033e9174593f1c11cebf347547be30fc5d3f91d8f33e80703a3f890cad3f228296be8140843f3cd4fd3ec9a5a03f3e5e733fd8070fbe898cf2bfb9c7293f7ec091bf1ca8c43d517802c0b68bb7bda635583f15f72dbeb47b823f881ae1bdf48b633e0afeadbe57ce6cbf309d29be70e70abf1e3b363f4f83f93f7d781a3e50a114c02f75ecbe08b0e93f338403bfc38c993d97aa85bfd2fabb3ed1b1a23f5f4cb13f08721abf986b9cbff72ec63f7ae682bfb8c5f03ca33c77bf9dc6b6bfd8709f3f0f010abfba12913f3ccb5fbf4e4449bfacb1b1bf0e3b153ed1d0323fbc8482be38d055be86d66fbf389b7e3e6205c3bf5ca8c93f4943a9bf72d64a3dd1429a3e5960a03f7cd4933f3d3defbf32c389bff49faf3fabc86e3fb4e34dbfb60f2e3e3e04e8bc532c61befbb7743fdddd633fcc74b63f58619cbe2468c6be6a6aee3e69da19bff4da603f20ae43beda87c6be8d92203ddf451e3f0f6e9abfc08cf03b93be20bfb90f9fbf389bcfbe8a5ba03e469f14bfbcc6e63ebc91dabe83ebf1bcb673d03f3795ca3ec0ee143fa8bc673cf0e5b7bfbe92ce3db9e74dbf6106613f6992bdbff699f43e97c4cfbdf0135ebf93719c3ff3d431bf04c2dbbf019495bf4517a9bf12ae31c0011be6be068f8dbe03a59abf5d65763fc03eddbf493c3440d0cc063f98f7e43db39006c06989273faeb289bffabcd3be5aa7d0bec38c2fbf2e27de3e810e1fbef4d8a13f13fbf5bf0cade53e42f1073f98177abfb1598c3c0199fd3ef72f88bf24d349bf6433dfbf9cc528be66d5bf3e389b7abee83a603edc4a0cbf68dd213f2481a93fe899e8bf7b0e823f3400a1beda20543eb3123dbedc86983f68768d3f7fdaea3e113056beaf033b3f076f1dbf22733fbf276ce0bdfee1d83d99a78abf965ebebf8b95d0bd7fe78c3fe566363f29219bbe668302be51c09cbf4950b5bf5977bdbf1aa151be49b32d405c9c2fbe5174863e80640d3cb2533e3f40f3bb3eb82b08bf2bda34be593c823f1696b2bebde6d73ff257883f105ab33f48c61fbff0defbbf110f983d1f701ac03404293f7c68a0bdf802d5be47770bbfafd1513f48890b3e1eb8133fd2e2cd3e5ade1e3f1ea9913ec217ecbfaeeb17be2204083e51957e3f0e93bd3fdb0db7bf2c848fbf53260340314c84bf2e15d93f4eccd43e3469dfbecf90acbfd6c7183f09c7b23f3018243f, 'uploads/students/student_56_1772724892.jpg', '2026-03-02 10:27:26'),
(57, '00000000', 'سالم عبدالله سالم القوينص', 'CS', 'الرابع', 0xfffacebee5a5223f989929bdc9bd0ac04cdf943e7e2fcabfdbf7913f41a1183f9efd25bdcce84e3f6f83013f9c74803d9b59b1bf7fb992bf9e44a9be424fca3e801cfa3eaa6844bfa235f6bee62403bf16b1eb3f7edea03e7aed9c3f25d2c6bf1fc997bfd9d6d13e7b57c73e5a2b193ec93acfbf99163fbfcf43f93da45c93bcc4fd7b3d1500d9bf81572d3f2dec6a3f50e6443c1ea6b63ffca5523f0ba7e03e04ef0cbf806448bea125d9bd4e87b13f9cdeb4bf0605863f05828c3f71cb1d3f6bef723fed7517bfb0839abf9177c73fd057f03f31df95bebe994740b2955fbe7ddab53fde1b8abfe04840bfe623f6be3cf5edbd906d1cbfd83dbf3caa63ab3efe15abbf0619923e64f253bf2e786c3ecbc30a40e1526ebfcf521dbf4bb04f3fb90f4fbe79b44f3fdd271abf237c84bd46bc943e26c9053f8a988dbeb5a4a7be38c42f3f333703bf50747e3e08bc8d3f7642f9bee1364d3f5216024025207c3f047c38bfa2e2de3e3038d03dfa67f13ee623183ed0f0fdbd327534bf372d10402841a7bfaad2c73eed1496bfe4ad45bfe2b10cbf2aa92abf081e773f1589e5be54937fbf12599d3f0ed7413fbb18b0beb833833fc627663f36707b3f28331d3ed4d6d8bfeb0651bf3afadabe731e703fb0ad5a3f77de88bf0818f03e5d504dbfe79c34bf6c05d4bd82d5923fc11d97bf9048e8bbbd6c403f4c82623f3ddf8f3eef1865bf2f0230bf0c169fbe94595abe445e1ac098ac01c032fe8a3eb93bd33ec3e973bf9d36923f7a036abfb014273f5047e03c7c7408c0fb9e8bbf229e85bf10ea39bfedaf99bf8a13083f2937dcbcb314b63f72ee1f3f8a3cef3cb0b072bb425ae73e587baebf01fc8dbf4eef97bec3acd23ee08a0abfc418683f5d83d6bf7e56d73fe6cbacbfdea5e03e744b393f1efe4dbfa358c6bf2852843c3abd263f8f995d3f68a886becaf6d13fe40f0c40e6fffcbd183e6bbf68e8c83e2373d53fea80cc3ef0b6673fe9dca33e68fa13bf10cddcbfa07badbe51fac63fad149a3f125833bf3b83ef3d5ba24440cce567bfcb088c3fb0d317be4795b1be46f133bda851903f5d840bbfc6310bbf53a81c40116394be48ef0c3f79a6c63eb7e0213ea51794bfaa221ebe12e63cbe20951fbf289c443f55498ebdd79788bfbc4ed2bfe7113e3f3d80263feabfda3f9b9e8a3f4087b8bcbdb0763fd7164e3f947e543fbb44c8be6e511bbfc48403be7307013e3b38d93eea42a33e234a99bfc749dd3f7ac7413dcdedffbe08a46e3dbacea6be1d8ba53e7700793fe4b6b7bef5fa6bbf28e3c63fa2375b3f4b0588bfc9d927c0d7cadb3d34f6eebfe01cd13eb0f557bc5690773ebe86edbf568dc83e9a985ebff1b4cd3f35ce563fba208a3f28ff48bf1b3151bf9a5982be7ce3963dfc6827c0a54db83f693227bf95ddb73e7f23b1bf54f6cb3f4acdb33fdcad303d9233d1bfea3e77bf3693413f47d2453f019504bf84410a3f66e0dcbf5970f2bf64ec08bfde71173f747fc3bea8eafbbf03d7c0bff7b7c23f26ea81bf08e8a43e32cb2ebd314667bf9a2c32bfabd1283ff3e913be2bee6ebfb19db6bee3f37f3ffe1f5a3e344a13bf70efbcbfa5e70e3f5ee294be2cbd0abf270ba63d1bda84bfd264b83f75ca99bfbffdfcbe50f60fbe3897483fe5fca23fecabbb3e5b6e313f1c7530bf644681bf42b6203fb8dc99bee6626e3e865b12bfcaae4bbea9e31a40e73baa3fd5cb3fc00bca953f34c115bf3075be3fb6758c3d3938bd3f8f59b73df0cef3be0881a93e928699bfbd53913fcd734dbf49174abe6c5d883e0b34b5be9a5c81bfc05ed1ba8128a93f7da085be15bd22c08fec4dbeb0a755bfbf180540fa50143f8ee9dfbe1c5454bf36bd31be6c6928bfbe2d243f2078c9bf8474bf3fc4a977bfcad116bf7ccc5abdf2d76dbf16c3123feafda8be762d043e58e7ec3f4dbd29bf77191bbe6cdd024002a4643e5277ad3f2ec34d3f1af68dbfb18c8a3ee8e8b2bf087ba53e3a8b35bd2cc6883fae897a3f6341ba3f84bb383f16358e3e40c72ac09824ebbf277d96bedcf2903e8513753fd0f41c3f17a30abffa14be3f5e003dbfa0779cbf5e19bc3f1315d53f569724be1f7fdabff27f74bfeaecf4bf1060d1bd5d3a8c3f75ef06bf2524533f1a01c9bea4a7c7bf184eb63e241f7fc0e629e73e1ae18dbd345ed8be3ce7c83f00b5673b11b1183f4ebe5b3e660401bf89a68f3f3ae9a53f7d701cc069d499bfcefb9a3e34a3813f3fdfcdbee36b0f406a3f1f3f9e9a703e37f2033f90bf353f1199ddbd705ee5bf88f1873f3c163bbfbeecbf3da52938bff3758dbf1ccc93bf683b0dbc780ce63de471f53fb2b4243fa6aa11bf0c00113e7ce9ebbe2a200cc074b2a93fe9eb95bf2511403e86db8dbfff2e53be4aaaf23cd74b99beb958113d88fc433c99ed24bfa0eb8e3fa151c23eaab1aa3f49872f3f4384b73f6e27f83e0a8b8cbf26ef453f290ef43f211a0abf1b1583bfb4942bbfa26a303e61980bbfd2a03c3fb7e5973f2ca3ae3d2a609a3f2fcf25be983042bf07dd1640918e453eccafb9be2381843ec105fbbf0e3eecbdd6b601be947449bfe8cd99bee867af3e285f3abb4a9231bf43ccd3bf50eb38bfc1c7d6be6dac3d3f7059313fd0fa273efa1bc43e1c84d2bdc2230fbfe0eab93fa733023ee0e61c3d2564483dc6b602be546c0640c03cd33fcb229f3e78f01b3fccee96bf258b783f9f2ef53ef1ce143febd3d43f440604bf774d9e3e33ff1b3faa01bfbf0408a93f4603db3d752b9b3e58b2b7be2813a3bfb293113f9169a43f96bfb7bec83838bd503a463f1e8bcbbff14335401e8c193f5036993fbc3f9dbe2ab0413f8836c1bf1ff20fbffeb5a13fa8a1a1bf, 'uploads/students/student_69a5fc79215081.66296488.jpg', '2026-03-02 21:09:13'),
(59, '09879877', 'الحسين سالم عبدالله القوينص', 'IT', 'الرابع', 0x415515bebaf2653f7404933fae019b3fe02c11bfb8cf593d301e64bf5dbb853f6e13ca3fbc8c28c0a2983bbf87413a3f1c21cf3ffb0f333f1a63793f88f9353edaf81ebed0163b3c42a212bff4be313e7897d03f59a51bbfd91589bf41e5ebbfd42cbfbf1b96c8bf47e42a3e782eecbfb4dbf2bee027f53f41705c3f88ebb3beaee1af3f3fe97f3f595ac73f330d85bfb09d10c0ef9558bf8273df3ee0c6c03e805a7b3c959b70be978de43f106f0bc0d0c189bf0941fc3dc12dac3e8cea453fc9ff3b3ff47cdd3f7e33413f4cfa73be3213143eaba374bf928aa83fac09113f1038f93e0e40b0bd427848bfe67298bfaca308bff4203e3f1170f03f6100b0bef8e5b3beb67e39bfc744c2bf91a468bfd047aabcb80b90bc1e18413f5c48acbf03eec33fb73851bf9c4b9abe516336c02fa21a40d6fb88bfba82cbbd907c04bf28c2f0bee521db3e8b6fb23dcabdc83fe9f566bf186f9e3e39b58fbfaaa751bfcc9a5a3f50341bbe289635c0342c25c072b5c03ed6249abfb43f6ebd21a6084044b837be10a0b9bfec70b7be4edb443fb4a357bfd6685b3ff635863fb031e5bd8a485ebe6d054b3f885490bdd57f82bf565575bf3066113f71e1d9beff292cbfa9ea4bbf96c4b43d6ba36fbfdf38753fb0476a3ed8b7bf3e6b394d3f486878be767e703f3821e3bf563d763f0cad2a3e28f583bf4ad8be3f32f3183fa893144075b1273f004b8abe0006cabf79e6f5beefd54ebf647765be51fc8f3f9297ef3ec2a7053fd575064084698cbf8e9e3bc0b24ebfbe58c81abf206d4c3f8b5132407b562ebf50c2e63f30ad084058b928bfcac9093e20694e3f6d7dd33dc2b082bf27f3a53f054b443f9fccdc3d24b8b13e8ef37fbead7a9fbfe59630beea0bda3ccd35dabf7cf1123ec6df78bf7adea23f16543bbf85a909c0115a4a3fbe30bfbf968c173f88e6be3ec74c0bbfb9f2123ffc181e3f6e0d18be91db95bf895d01c0f3639b3f306c8a3ffe38febf496b923fbdc362bf3e48083e66d4afbf2231f53eb65bd23ef699a23e70c71e3f3190d6bf0a45333f5be8d03fa83a803e8d634e3f6d21e13ff90d8fbf80b8a03f7be125c0e7c1883f5c962cbf48409a3f36ed84bd8c9361bd602fc03d62eb26bf565ca2bf9bfcfabffa77753f464849beeeb8ca3f29f75a3ea1096240cc562a3d2369fe3f14b437be5a35213fcdc7aabe5e54e9bf644edabdc76888bd01d607bf62cf29bfec8f153fc58a4ebf532bb83fc2461dbf48d77e3e3da6093feb83ab3f3f581b3e7404583f33f092be1c137b3e8efe7ebf7fdf19c03ce65b3f3bdd0bbd3c1e943f00fa603e95c52940983220bfd398a8bf11f05a3f01369a3e802e22c07b72aebf07e77c3fbfc76cbd260dca3fefee263f4d5c6ebfedfdc83e65a3cbbe15573bbe8133b73f8600293e9229a83f62cd4ebf1b5b94bea7467b3e5cceb93fe990c7bfbb65e1bf46f2123fe792d73f2b9d963f6a8a083f768c393dc0410cbe94edd6bfa81cd1bdae656fbfe3a5083ff95803bf777d3cbee289d33fd7faa23ea4e7b1bef50fad3ffe4a8d3da581b93f7d7c11bf7663a0bf273b32be1b64743f47b393be08f5363f2b47f6bff3f482bf84eb0a3f0cd3be3c59eccbbf55f0cd3f9eac8c3e7e1c89bea9c8833f653d0ac0a07da63f539e0b40306f043fa39e66bf557a0a3fc6ee323f44b11f3f22f41940ba882ebeb8178b3e31c2afbe83394f3f14749f3d3ec9e4be3232bcbf0ebaa4bf20747a3b7a96a7bff943a3bf3de87a3f6eb7ce3e9d13013fd590f2bfb03ba1bfa313eabf80ebc53f306ca7bf796a7c3faa37fd3d8e8e59bf1faad33f875a8a3e45397f3ff6f419c0f6890fbfa80d803f19165bbf133aadbf26d3543ef09e16bfb4150140dddb7a3fd95200c010fda73f8b5e853f6abdadbf02ce793ed4f2473f251c8dbf839531bddbb7a3bec8e3093d743722bf60f2ab3f50943140bc2f45bf7969833ff84405bf684fc9bc07bef23c2c2fa9bfce39acbd307f6c3c6ac73bbf31014cbfa02166bf0378623f6f95863f2a171fbe250715c0f4cd0a40485beb3f1472e0bf8ae90b3fa6e9713f6ec68fbf7b420b4058e66b3faa88fe3fdeacf73ee9e2373f978c0c3f5653c1bf2e87c83fe1f91c3f0edb8fbf575bf03efe1f823fb41019bfaa8377bfaaa7b3bfaed30a4092eadcbd714467bf89b419bf70c944beca824ebef9b2eabe2f438d3de6e8184034b733bf8d21913f28c4233fc4dc473f5a54853e0ee0c6be88769abfa018c7bc6454223e3cf03640afc4c73f69309e3d9aae0abff75bb8bef05ce1bf0d8d6cbfaecf843d2dd18a3f72a492bf72748ebf35d7923e24d504bd0bbe883f5e94453e7f7ab1bf42745cbe000cdcbfbc96733fd2aba5bf3262a0be708ca2bd26d110bf2a32a93f1d3d77befc12b43f8dd6a33fbb5b28bf37ffaf3fdaaacf3ed0018bbea87699bf4d4e0c3fe429db3feb440840c0ba5ebf520c513ffae99dbfc04972bfbf6f0ebfc0aab3be166e143ea365843e9ebf243f271f94bf12b5cbbdd08dd83bf07ee7bf7151f3bfba0d253ff98f40be5daf03be15d469bff6ec213f9edda13fd0d904be85b5cdbef3f267be9815edbf873162bf9336c03eaea88ebd42a16e3f2c49b1be1c8c30bfd9c54b40001942bbefa898be3d70a23f09430340c8a8963fe7b9aabffa8e55bfb1c8a33f2286b5bf49949b3f30371b40cdf6543f704200402b1e8b3fc868b1bf0c91d9bf9caeb43f61229abf9f55d4bf755d8dbef2f194be269d66bfa97fecbf14530440dce697be436299be9886643eaa9e813fce7d13bfcd667d3f6556e6bf2e7f50bfa44eb23f3424f9be8aa61840d4440cc0ae2a7ebee4b8323e669e293e54cf553e9df19cbf8e4fb73e, 'uploads/students/student_69a6c11e42f838.89431632.jpg', '2026-03-03 11:08:15');

-- --------------------------------------------------------

--
-- Table structure for table `supervisors`
--

CREATE TABLE `supervisors` (
  `supervisor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','supervisor') NOT NULL DEFAULT 'supervisor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervisors`
--

INSERT INTO `supervisors` (`supervisor_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'admin', 'admin@exam.com', '$2y$10$oYmHt5mVcGYktEhBlmZDU.KlPjM0Nkrxu2IVXm5Z8s3ow/DjD8GF2', 'admin', '2025-12-27 08:23:03'),
(3, 'mubark', '', '$2y$10$EVZ1OonSFLHiSfxq0mRhL.YoAQjCw53F5YPXTD5VB8UX6DOMfWNjm', 'supervisor', '2026-03-21 20:35:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cheating_events`
--
ALTER TABLE `cheating_events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `fk_event_student` (`student_id`),
  ADD KEY `fk_event_type` (`cheating_type_id`);

--
-- Indexes for table `cheating_types`
--
ALTER TABLE `cheating_types`
  ADD PRIMARY KEY (`cheating_type_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notification_event` (`event_id`),
  ADD KEY `fk_notification_supervisor` (`supervisor_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_number` (`student_number`);

--
-- Indexes for table `supervisors`
--
ALTER TABLE `supervisors`
  ADD PRIMARY KEY (`supervisor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cheating_events`
--
ALTER TABLE `cheating_events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `cheating_types`
--
ALTER TABLE `cheating_types`
  MODIFY `cheating_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `supervisors`
--
ALTER TABLE `supervisors`
  MODIFY `supervisor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cheating_events`
--
ALTER TABLE `cheating_events`
  ADD CONSTRAINT `fk_event_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_event_type` FOREIGN KEY (`cheating_type_id`) REFERENCES `cheating_types` (`cheating_type_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notification_event` FOREIGN KEY (`event_id`) REFERENCES `cheating_events` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_notification_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisors` (`supervisor_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
