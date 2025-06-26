-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 26, 2025 at 09:17 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aiub-portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_calendar`
--

CREATE TABLE `academic_calendar` (
  `id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('holiday','exam','deadline','registration','other') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_calendar`
--

INSERT INTO `academic_calendar` (`id`, `event_date`, `title`, `description`, `type`) VALUES
(1, '2024-01-15', 'Spring Semester Classes Start', 'First day of classes for Spring 2024.', 'registration'),
(2, '2024-02-21', 'Int. Mother Language Day', 'University closed.', 'holiday'),
(3, '2024-03-25', 'Midterm Exams Start', 'Midterm examinations for Spring 2024 begin.', 'exam'),
(4, '2024-04-05', 'Midterm Exams End', 'Midterm examinations for Spring 2024 conclude.', 'exam'),
(5, '2024-04-10', 'Tuition Fee Payment Deadline', 'Final day to pay tuition fees without a late fine.', 'deadline'),
(6, '2024-04-11', 'Eid-ul-Fitr Holiday Starts', 'University closed for Eid celebrations (Tentative).', 'holiday'),
(7, '2024-04-14', 'Pohela Boishakh', 'University closed for Pohela Boishakh.', 'holiday'),
(8, '2024-05-01', 'May Day', 'University closed for May Day.', 'holiday'),
(9, '2024-05-10', 'Last Day of Classes (Spring)', 'Final day of lectures for Spring 2024.', 'other'),
(10, '2024-05-20', 'Final Exams Start (Spring)', 'Final examinations for Spring 2024 begin.', 'exam');

-- --------------------------------------------------------

--
-- Table structure for table `academic_results`
--

CREATE TABLE `academic_results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `grade` varchar(5) NOT NULL,
  `grade_point` decimal(3,2) NOT NULL,
  `midterm_score` int(11) DEFAULT NULL,
  `final_score` int(11) DEFAULT NULL,
  `quiz_score` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_results`
--

INSERT INTO `academic_results` (`id`, `student_id`, `semester_id`, `course_id`, `grade`, `grade_point`, `midterm_score`, `final_score`, `quiz_score`) VALUES
(1, 1, 2, 1, 'A+', 4.00, 28, 48, 19),
(2, 1, 2, 2, 'A', 3.75, 25, 45, 18),
(3, 1, 2, 3, 'A-', 3.50, 24, 42, 17),
(4, 1, 2, 4, 'B+', 3.25, 22, 40, 16),
(5, 1, 2, 5, 'A', 3.75, 26, 45, 17),
(6, 1, 2, 8, 'B', 3.00, 21, 38, 14),
(7, 1, 2, 16, 'A-', 3.50, 23, 43, 18),
(8, 1, 2, 17, 'A+', 4.00, 29, 49, 20),
(9, 1, 3, 6, 'A', 3.75, 27, 46, 18),
(10, 1, 3, 7, 'B+', 3.25, 23, 41, 15),
(11, 1, 3, 10, 'A+', 4.00, 28, 48, 19),
(12, 1, 3, 18, 'A', 3.75, 25, 45, 18),
(13, 2, 2, 1, 'B+', 3.25, 23, 41, 15),
(14, 2, 2, 3, 'B', 3.00, 21, 38, 14),
(15, 2, 2, 4, 'C+', 2.75, 20, 35, 13),
(16, 2, 2, 5, 'C', 2.50, 18, 32, 12),
(17, 3, 3, 1, 'A', 3.75, 26, 46, 18),
(18, 3, 3, 4, 'A+', 4.00, 29, 49, 20),
(19, 3, 3, 5, 'A', 3.75, 27, 47, 18),
(20, 3, 3, 16, 'A+', 4.00, 29, 49, 20),
(21, 1, 3, 19, 'B', 3.00, 22, 39, 15),
(22, 1, 3, 20, 'A', 3.75, 26, 46, 18),
(23, 1, 3, 27, 'A-', 3.50, 24, 43, 17),
(24, 1, 3, 30, 'B+', 3.25, 23, 41, 15),
(25, 2, 3, 18, 'A', 3.75, 25, 45, 18),
(26, 2, 3, 19, 'C', 2.50, 18, 32, 12),
(27, 2, 3, 20, 'B', 3.00, 21, 38, 14),
(28, 2, 3, 27, 'A', 3.75, 27, 47, 18),
(29, 2, 3, 30, 'A-', 3.50, 24, 42, 17),
(30, 3, 2, 8, 'A+', 4.00, 28, 48, 19),
(31, 3, 2, 16, 'A', 3.75, 25, 45, 18);

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `publish_date` date NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-file-alt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `publish_date`, `icon`) VALUES
(1, 'Midterm Exam Schedule Published', 'The midterm examination schedule for Spring 2023-2024 has been published. Please check the Exam Schedule page for details.', '2025-06-26', 'fa-file-alt'),
(2, 'Merit-Based Scholarship Applications Open', 'Applications for merit-based scholarships for the upcoming semester are now open. Deadline is April 30th.', '2024-03-12', 'fa-graduation-cap'),
(3, 'Career Development Workshop', 'Join our career development workshop on March 25th to learn about CV writing and interview skills. Register online.', '2024-03-10', 'fa-briefcase'),
(4, 'Library Hours Extended for Midterms', 'The library will remain open until 10:00 PM from March 20th to April 5th.', '2024-03-18', 'fa-book-open');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `application_type` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `student_id`, `application_type`, `details`, `status`, `request_date`) VALUES
(2, 2, 'ID Card Requisition', 'Lost original ID card.', 'Approved', '2025-06-25 22:59:45'),
(3, 1, 'Financial Aid', 'Application for tuition fee waiver.', 'Rejected', '2025-06-25 22:59:45'),
(4, 3, 'Program Change', 'Request to change program from EEE to CSE.', 'Pending', '2025-06-25 22:59:45'),
(6, 1, 'Course Drop', 'Request to drop course: BAE 2101 - Bangladesh Studies (Section A)', 'Pending', '2025-06-26 00:02:30');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `due_date` datetime NOT NULL,
  `specs_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `section_id`, `title`, `due_date`, `specs_url`) VALUES
(1, 1, 'Assignment 1: Requirement Analysis', '2025-07-20 23:59:59', '#'),
(2, 1, 'Assignment 2: System Design', '2025-07-10 23:59:59', '#'),
(3, 3, 'Project Milestone 1: ERD Design', '2025-07-25 23:59:59', '#'),
(4, 9, 'Lab Task 1: Basic SQL Queries', '2025-08-02 23:59:59', '#'),
(5, 7, 'Presentation on SDLC Models', '2025-08-20 23:59:59', '#');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `credits` decimal(2,1) NOT NULL,
  `description` text DEFAULT NULL,
  `prerequisite_course_id` int(11) DEFAULT NULL,
  `course_type` enum('Core','Elective','General') NOT NULL DEFAULT 'Core'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `title`, `credits`, `description`, `prerequisite_course_id`, `course_type`) VALUES
(1, 'CSC 1101', 'Introduction to Computer Studies', 1.0, 'Basic computer literacy.', NULL, 'Core'),
(2, 'CSC 1203', 'Programming Language I (Java)', 3.0, 'Fundamentals of OOP using Java.', 1, 'Core'),
(3, 'MAT 1102', 'Differential Calculus', 3.0, 'Core concepts of differential calculus.', NULL, 'General'),
(4, 'PHY 1101', 'Physics I', 3.0, 'Mechanics, waves, and thermodynamics.', NULL, 'General'),
(5, 'ENG 1101', 'English Reading & Speaking', 3.0, 'Developing English communication skills.', NULL, 'General'),
(6, 'CSC 2108', 'Data Structures', 3.0, 'Study of fundamental data structures.', 2, 'Core'),
(7, 'CSC 2215', 'Computer Architecture', 3.0, 'Organization of computer systems.', 2, 'Core'),
(8, 'EEE 2104', 'Electronic Devices & Circuits', 3.0, 'Intro to semiconductor devices.', 4, 'Core'),
(9, 'CSE 3205', 'Software Engineering', 3.0, 'Principles of software development.', 6, 'Core'),
(10, 'CSE 3107', 'Database Systems', 3.0, 'Core concepts of database design.', 6, 'Core'),
(11, 'CSE 3111', 'Computer Networks', 3.0, 'Introduction to computer networks.', 7, 'Core'),
(12, 'CSE 3203', 'Algorithms', 3.0, 'Design and analysis of algorithms.', 6, 'Core'),
(13, 'MAT 3109', 'Numerical Methods', 3.0, 'Numerical techniques for problems.', 3, 'General'),
(14, 'CSE 4100', 'Web Technologies', 3.0, 'Client-side and server-side web dev.', 10, 'Core'),
(15, 'CSE 4200', 'Artificial Intelligence', 3.0, 'Introduction to AI concepts.', 12, 'Core'),
(16, 'PHY 1203', 'Physics II', 3.0, 'Electricity, magnetism, and optics.', 4, 'General'),
(17, 'MAT 2106', 'Linear Algebra', 3.0, 'Study of vectors, matrices.', 3, 'General'),
(18, 'BAE 2101', 'Bangladesh Studies', 3.0, 'History, culture of Bangladesh.', NULL, 'General'),
(19, 'ACC 2102', 'Principles of Accounting', 3.0, 'Fundamentals of financial accounting.', NULL, 'General'),
(20, 'ECO 3113', 'Principles of Economics', 3.0, 'Intro to micro/macroeconomics.', NULL, 'General'),
(21, 'CSE 3110', 'Operating Systems', 3.0, 'Core concepts of operating systems.', 7, 'Core'),
(22, 'CSE 4118', 'Compiler Design', 3.0, 'Principles of compiler construction.', 28, 'Core'),
(23, 'CSE 4219', 'Computer Graphics', 3.0, 'Fundamentals of 2D/3D graphics.', 17, 'Elective'),
(24, 'CSE 4231', 'Natural Language Processing', 3.0, 'Techniques for computer processing of human language.', 15, 'Elective'),
(25, 'CSE 4232', 'Machine Learning', 3.0, 'Theory/practice of ML algorithms.', 15, 'Elective'),
(26, 'CSE 4233', 'Cryptography & Network Security', 3.0, 'Principles of cryptography.', 11, 'Elective'),
(27, 'MGT 3202', 'Principles of Management', 3.0, 'Intro to management functions.', NULL, 'General'),
(28, 'CSE 3213', 'Theory of Computation', 3.0, 'Study of formal languages.', 6, 'Core'),
(29, 'COE 3103', 'Data Communication', 3.0, 'Fundamentals of data transmission.', 11, 'Core'),
(30, 'BBA 1102', 'Principles of Business', 3.0, 'An overview of business areas.', NULL, 'General'),
(31, 'CSE 4999', 'Thesis / Project', 3.0, 'Capstone project for final year students.', 9, 'Core');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum`
--

CREATE TABLE `curriculum` (
  `id` int(11) NOT NULL,
  `program_name` varchar(100) NOT NULL,
  `semester_level` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum`
--

INSERT INTO `curriculum` (`id`, `program_name`, `semester_level`, `course_id`) VALUES
(1, 'BSc in CS&E', 1, 1),
(2, 'BSc in CS&E', 1, 3),
(3, 'BSc in CS&E', 1, 4),
(4, 'BSc in CS&E', 1, 5),
(5, 'BSc in CS&E', 2, 2),
(6, 'BSc in CS&E', 2, 8),
(7, 'BSc in CS&E', 2, 16),
(8, 'BSc in CS&E', 2, 17),
(9, 'BSc in CS&E', 3, 6),
(10, 'BSc in CS&E', 3, 7),
(11, 'BSc in CS&E', 3, 10),
(12, 'BSc in CS&E', 3, 18),
(13, 'BSc in CS&E', 4, 12),
(14, 'BSc in CS&E', 4, 13),
(15, 'BSc in CS&E', 4, 21),
(16, 'BSc in CS&E', 4, 28),
(17, 'BSc in CS&E', 5, 9),
(18, 'BSc in CS&E', 5, 11),
(19, 'BSc in CS&E', 5, 15),
(20, 'BSc in CS&E', 5, 29),
(21, 'BSc in CS&E', 6, 14),
(22, 'BSc in CS&E', 6, 19),
(23, 'BSc in CS&E', 6, 27),
(24, 'BSc in CS&E', 6, 20),
(25, 'BSc in CS&E', 7, 22),
(26, 'BSc in CS&E', 7, 23),
(27, 'BSc in CS&E', 7, 25),
(28, 'BSc in CS&E', 7, 26),
(29, 'BSc in CS&E', 8, 31),
(30, 'BSc in CS&E', 8, 24),
(31, 'BSc in CS&E', 8, 30);

-- --------------------------------------------------------

--
-- Table structure for table `exam_schedule`
--

CREATE TABLE `exam_schedule` (
  `id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `exam_type` enum('Quiz-1','Quiz-2','Midterm','Final') NOT NULL,
  `exam_datetime` datetime NOT NULL,
  `room` varchar(100) NOT NULL,
  `syllabus` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_schedule`
--

INSERT INTO `exam_schedule` (`id`, `semester_id`, `course_id`, `exam_type`, `exam_datetime`, `room`, `syllabus`) VALUES
(1, 1, 9, 'Quiz-1', '2024-03-12 10:00:00', 'D-502', 'Chapter 1, 2'),
(2, 1, 9, 'Quiz-2', '2024-04-02 10:00:00', 'D-502', 'Chapter 3, 4'),
(3, 1, 13, 'Quiz-1', '2024-04-05 09:00:00', 'B-102', 'Root Finding Methods'),
(4, 1, 10, 'Midterm', '2024-04-10 12:00:00', 'C-303', 'Chapters 1-5'),
(5, 1, 9, 'Midterm', '2024-04-11 10:00:00', 'D-502', 'Chapters 1-4'),
(6, 1, 4, 'Midterm', '2024-04-12 14:00:00', 'A-201 (Annex 2)', 'Mechanics & Waves'),
(7, 2, 6, 'Final', '2023-12-18 09:00:00', 'Auditorium', 'Full Syllabus'),
(8, 2, 8, 'Final', '2023-12-20 01:00:00', 'Auditorium', 'Full Syllabus');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title_dept` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `office` varchar(50) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `name`, `title_dept`, `email`, `phone`, `office`, `education`) VALUES
(1, 'Dr. M. A. Rahman', 'Professor & Dean, FST', 'rahman.m@aiub.edu', '+8801711000001', 'Admin Building, Room 101', 'PhD in Computer Engineering (Tokyo, Japan)'),
(2, 'Dr. Tabin Hasan', 'Professor & Head, Dept. of CSE', 'tabin.hasan@aiub.edu', '+8801711000002', 'D-Building, Room 301', 'PhD in Computer Science (Concordia, Canada)'),
(3, 'Dr. Dip Nandi', 'Associate Professor, Dept. of CSE', 'dip.nandi@aiub.edu', '+8801711000003', 'D-Building, Room 305', 'PhD in CSE (Jahangirnagar University)'),
(4, 'Ms. Sadia Hamid', 'Assistant Professor, Dept. of CSE', 'sadia.hamid@aiub.edu', '+8801711000004', 'D-Building, Room 403', 'M.Sc. in Computer Science (AIUB)'),
(5, 'Mr. Abir Ahmed', 'Lecturer, Dept. of CSE', 'abir.ahmed@aiub.edu', '+8801711000005', 'Faculty Area, D-Building 5th Floor', 'B.Sc. in CSE (AIUB)'),
(6, 'Dr. S. M. Khaled', 'Professor, Dept. of Science & Humanities', 'khaled.sm@aiub.edu', '+8801711000006', 'Annex-3, Room 201', 'PhD in Physics (Dhaka University)'),
(7, 'Prof. A. K. M. Nazim', 'Professor, Dept. of EEE', 'nazim.akm@aiub.edu', '+8801811000007', 'E-Building, Room 205', 'PhD in Electrical Engineering (USA)'),
(8, 'Dr. Mahfuzur Rahman', 'Associate Professor, FBA', 'mahfuz.rahman@aiub.edu', '+8801911000008', 'FBA Building, Room 502', 'PhD in Business Administration (Australia)'),
(9, 'Dr. Farheen Hassan', 'Professor, Dept. of CSE', 'farheen.hassan@aiub.edu', NULL, 'D-Building, Room 302', 'PhD in CSE (USA)'),
(10, 'Mr. Sharfuddin Mahmood', 'Lecturer, Dept. of CSE', 'sharfuddin.m@aiub.edu', NULL, 'Faculty Area, D-Building 4th Floor', 'B.Sc. in CSE (AIUB)');

-- --------------------------------------------------------

--
-- Table structure for table `financial_transactions`
--

CREATE TABLE `financial_transactions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `status` enum('Completed','Pending','Failed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financial_transactions`
--

INSERT INTO `financial_transactions` (`id`, `student_id`, `semester_id`, `transaction_id`, `description`, `amount`, `payment_method`, `payment_date`, `status`) VALUES
(1, 1, 1, 'TXN-2024-12548', 'Spring 2024 Full Semester Fee', 125000.00, 'Online Banking', '2024-02-10', 'Completed'),
(2, 1, 2, 'TXN-2023-98765', 'Fall 2023 Full Semester Fee', 115000.00, 'Bank Deposit', '2023-09-12', 'Completed'),
(3, 2, 1, 'TXN-2024-12549', 'Spring 2024 Full Semester Fee', 95000.00, 'bKash', '2024-02-11', 'Completed'),
(4, 1, 3, 'TXN-2023-45678', 'Summer 2023 Full Semester Fee', 85000.00, 'Credit Card', '2023-06-10', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `publish_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `section_id`, `title`, `content`, `publish_date`) VALUES
(3, 1, 'Midterm Exam Schedule Confirmed', 'The midterm exam for CSE3205 Section A will be held on March 25, 2024, during the scheduled class time in room D-502.', '2024-03-18'),
(4, 1, 'Assignment 2 Posted', 'The specifications for Assignment 2 (System Design) have been posted. The deadline is April 10th.', '2024-03-22'),
(5, 1, 'Guest Lecture on Agile Methodologies', 'We will have a guest lecture from a senior software architect on Wednesday, April 3rd. Attendance is mandatory.', '2024-03-28'),
(6, 1, 'Reminder: Project Proposal Due', 'Please submit your project proposals by this Friday. No late submissions will be accepted.', '2024-03-04'),
(7, 1, 'Quiz 1 Syllabus', 'The syllabus for Quiz 1 will cover chapters 1 and 2 of the textbook.', '2024-03-05'),
(8, 1, 'Class Canceled on April 1st', 'Please note that the class on Monday, April 1st, is canceled. We will cover the topics in the following session.', '2024-03-30'),
(9, 3, 'Project Group Formation', 'Please form your project groups (3-4 members) and submit the member list by the end of this week.', '2024-03-01'),
(10, 3, 'Midterm Review Session', 'A review session for the midterm exam will be held on April 8th at 3:00 PM in room C-303.', '2024-04-02'),
(11, 3, 'Lab Software Installation Guide', 'An installation guide for MySQL Workbench has been uploaded to the course materials section.', '2024-02-28'),
(12, 3, 'Quiz 2 Next Week', 'Be prepared for Quiz 2 next Tuesday. It will cover SQL Joins and Normalization.', '2024-04-11'),
(13, 3, 'Updated Lecture Slides', 'The lecture slides for Chapter 5 (Transactions) have been updated with additional examples.', '2024-04-09'),
(14, 4, 'Makeup Class Schedule', 'A makeup class for the session missed on March 10th will be held this Friday at 11:00 AM online. The link will be shared via email.', '2024-03-11'),
(15, 4, 'MATLAB Tutorial Session', 'An optional MATLAB tutorial will be conducted this Saturday for those who need extra help with the assignments.', '2024-03-14'),
(16, 4, 'Important Formula Sheet Uploaded', 'A formula sheet for the upcoming midterm has been uploaded. You will be allowed to bring a printed copy to the exam.', '2024-04-01'),
(17, 4, 'Office Hours Rescheduled', 'My office hours for this week are rescheduled to Thursday, 4 PM to 5 PM.', '2024-03-26'),
(18, 2, 'Welcome to Programming Language I', 'Welcome to the course! Please go through the course outline and install the required JDK and IDE before our first class.', '2024-02-25'),
(19, 2, 'Lab 1 Instructions', 'The instructions for Lab 1 have been posted. Please complete it before the next lab session.', '2024-03-03'),
(20, 5, 'Data Structures Midterm Syllabus', 'The midterm will cover all topics up to and including Binary Search Trees.', '2024-03-20'),
(21, 6, 'Web Tech Project Groups', 'Please finalize your project groups for the Web Technologies course by next Monday.', '2024-03-21'),
(22, 8, 'Algorithms Quiz 1', 'The first quiz for Algorithms (Section B) will be held next Wednesday on complexity analysis.', '2024-03-19'),
(23, 18, 'No Class on Victory Day', 'As per the university calendar, there will be no class on December 16th.', '2023-12-14'),
(24, 19, 'EEE Circuit Analysis Lab Manual', 'The lab manual for the entire semester has been uploaded. Please download it.', '2023-09-05'),
(25, 20, 'Computer Architecture Midterm Date', 'The midterm for Computer Architecture Section A will be on October 25th.', '2023-10-10'),
(26, 21, 'English Speaking Practice Session', 'An extra speaking practice session will be held this Friday at the language lab.', '2023-09-20'),
(27, 22, 'Intro to CS Lab Grouping', 'Lab groups will be formed during the first lab class. Please be present.', '2023-09-06'),
(28, 23, 'PL1 Section C Quiz', 'Quiz 1 for PL1 Section C will be on basic Java syntax and variables.', '2023-09-15'),
(29, 24, 'Calculus Assignment 1', 'The first assignment for Differential Calculus is now available. Due next week.', '2023-09-12'),
(30, 25, 'Physics I Lab Safety Briefing', 'A mandatory lab safety briefing will be held at the beginning of the first lab session.', '2023-09-11'),
(31, 26, 'Database Systems Project Ideas', 'A list of suggested project ideas has been posted. You may also propose your own.', '2023-09-22'),
(32, 27, 'Summer 2023 Final Grades Published', 'The final grades for the Summer 2022-2023 semester have been officially published.', '2023-08-30'),
(33, 28, 'Welcome to Summer Semester', 'Welcome to the Summer 2022-2023 semester! Let\'s have a productive session.', '2023-06-01'),
(34, 29, 'English Communication Final Presentation', 'The schedule for the final presentation has been posted. Please check your slot.', '2023-08-15'),
(35, 30, 'Bangladesh Studies Term Paper', 'The topic for the term paper must be approved by the end of this month.', '2024-03-10');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `student_id`, `section_id`, `registration_date`) VALUES
(1, 1, 7, '2025-06-25 22:59:45'),
(2, 1, 9, '2025-06-25 22:59:45'),
(3, 1, 10, '2025-06-25 22:59:45'),
(4, 1, 30, '2025-06-25 23:57:21'),
(5, 1, 3, '2025-06-26 00:13:31'),
(6, 1, 14, '2025-06-26 00:55:08'),
(7, 1, 13, '2025-06-26 01:06:58'),
(8, 1, 18, '2025-06-26 01:10:58'),
(9, 1, 19, '2025-06-26 01:11:58'),
(10, 1, 20, '2025-06-26 01:10:58'),
(11, 1, 21, '2025-06-26 01:10:58'),
(12, 1, 22, '2025-06-26 01:10:58'),
(13, 1, 23, '2025-06-26 01:10:58'),
(14, 1, 24, '2025-06-26 01:10:58'),
(15, 1, 25, '2025-06-26 01:10:58');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `section_char` varchar(5) NOT NULL,
  `schedule_time` varchar(50) DEFAULT NULL,
  `room` varchar(20) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `enrolled` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `course_id`, `semester_id`, `faculty_id`, `section_char`, `schedule_time`, `room`, `capacity`, `enrolled`) VALUES
(1, 1, 1, 5, 'A', 'M 08:00-09:00', 'D-101', 40, 38),
(2, 2, 1, 4, 'A', 'ST 08:30-10:00', 'D-202', 40, 35),
(3, 2, 1, 5, 'B', 'MW 11:30-01:00', 'D-203', 40, 40),
(4, 3, 1, 6, 'A', 'TTh 01:00-02:30', 'A-301', 40, 30),
(5, 6, 1, 3, 'A', 'ST 10:00-11:30', 'C-401', 40, 40),
(6, 6, 1, 4, 'B', 'MW 01:00-02:30', 'C-402', 40, 25),
(7, 9, 1, 3, 'A', 'TTh 10:00-11:30', 'D-502', 40, 1),
(8, 9, 1, 4, 'B', 'MW 02:00-03:30', 'D-503', 40, 0),
(9, 10, 1, 2, 'B', 'ST 12:00-01:30', 'C-303', 40, 1),
(10, 13, 1, 6, 'C', 'M 09:00-10:30', 'B-102', 30, 1),
(11, 14, 1, 3, 'H', 'TTh 02:30-04:00', 'Lab-7', 25, 0),
(12, 12, 1, 2, 'A', 'ST 02:30-04:00', 'C-501', 40, 0),
(13, 21, 1, 5, 'A', 'MW 04:00-05:30', 'D-404', 35, 1),
(14, 11, 1, 3, 'A', 'TTh 04:00-05:30', 'D-505', 35, 1),
(15, 15, 1, 2, 'A', 'M 02:30-04:00', 'C-502', 40, 0),
(16, 23, 1, 4, 'A', 'W 02:30-04:00', 'Lab-8', 25, 0),
(17, 25, 1, 2, 'A', 'F 10:00-11:30', 'D-501', 40, 0),
(18, 6, 2, 3, 'B', 'MW 08:30-10:00', 'C-401', 40, 1),
(19, 8, 2, 7, 'A', 'ST 10:00-11:30', 'E-301', 40, 1),
(20, 7, 2, 5, 'A', 'TTh 11:30-01:00', 'D-303', 40, 1),
(21, 5, 2, 4, 'C', 'MW 01:00-02:30', 'E-101', 30, 1),
(22, 1, 2, 5, 'B', 'F 09:00-10:00', 'D-102', 40, 1),
(23, 2, 2, 4, 'C', 'ST 01:00-02:30', 'D-204', 40, 1),
(24, 3, 2, 6, 'B', 'TTh 02:30-04:00', 'A-302', 40, 1),
(25, 4, 2, 6, 'A', 'MW 10:00-11:30', 'A-201', 40, 1),
(26, 10, 2, 2, 'A', 'TTh 08:30-10:00', 'C-301', 40, 1),
(27, 1, 3, 5, 'C', 'S 10:00-11:00', 'D-103', 40, 1),
(28, 2, 3, 5, 'D', 'MW 02:30-04:00', 'D-205', 40, 1),
(29, 5, 3, 4, 'A', 'ST 11:30-01:00', 'E-102', 30, 1),
(30, 18, 1, 8, 'A', 'F 11:30-01:00', 'FBA-201', 35, 1);

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` int(11) NOT NULL,
  `semester_key` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_active_registration` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `semester_key`, `name`, `is_active_registration`) VALUES
(1, 'spring2024', 'Spring 2023-2024', 1),
(2, 'fall2023', 'Fall 2022-2023', 0),
(3, 'summer2023', 'Summer 2022-2023', 0);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id_str` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `program` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `admission_date` varchar(50) DEFAULT NULL,
  `expected_grad` varchar(50) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id_str`, `password`, `full_name`, `email`, `program`, `phone`, `birthdate`, `address`, `admission_date`, `expected_grad`, `emergency_contact`) VALUES
(1, '22-48108-2', '1234', 'Ahbab Sakalan', 'sakalan.ahbab@student.aiub.edu', 'BSc in CS&E', '+880 1712 345678', '2000-01-31', '123/A, Road 7, Banani, Dhaka', 'Spring 2021', 'Fall 2024', 'Md kamal Uddin (Father) - +880 1711 987654'),
(2, '22-48091-2', '1234', 'John Doe', 'john.doe@student.aiub.edu', 'BBA in Marketing', '+880 1812 345678', '2000-01-20', '45/B, Road 3, Gulshan, Dhaka', 'Fall 2020', 'Summer 2024', 'Ms. Jane Doe (Mother) - +880 1811 987654'),
(3, '22-98765-3', '$2y$10$tJ0i3j1jV2.dFq4s.kFzIuR8e/7kE0.x3B.a/9eD8zG6yH2xG8yCq', 'Fatima Khan', 'fatima.khan@student.aiub.edu', 'BSc in EEE', '+880 1912 345678', '2002-11-30', '78/C, Sector 11, Uttara, Dhaka', 'Summer 2022', 'Spring 2026', 'Mr. Omar Khan (Father) - +880 1911 987654');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_calendar`
--
ALTER TABLE `academic_calendar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_date` (`event_date`);

--
-- Indexes for table `academic_results`
--
ALTER TABLE `academic_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `prerequisite_course_id` (`prerequisite_course_id`);

--
-- Indexes for table `curriculum`
--
ALTER TABLE `curriculum`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `exam_schedule`
--
ALTER TABLE `exam_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `financial_transactions`
--
ALTER TABLE `financial_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `semester_key` (`semester_key`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id_str` (`student_id_str`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_calendar`
--
ALTER TABLE `academic_calendar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `academic_results`
--
ALTER TABLE `academic_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `curriculum`
--
ALTER TABLE `curriculum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `exam_schedule`
--
ALTER TABLE `exam_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `financial_transactions`
--
ALTER TABLE `financial_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_results`
--
ALTER TABLE `academic_results`
  ADD CONSTRAINT `academic_results_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `academic_results_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
  ADD CONSTRAINT `academic_results_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`prerequisite_course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `curriculum`
--
ALTER TABLE `curriculum`
  ADD CONSTRAINT `curriculum_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_schedule`
--
ALTER TABLE `exam_schedule`
  ADD CONSTRAINT `exam_schedule_ibfk_1` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
  ADD CONSTRAINT `exam_schedule_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `financial_transactions`
--
ALTER TABLE `financial_transactions`
  ADD CONSTRAINT `financial_transactions_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `financial_transactions_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`);

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `notices_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `sections_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
  ADD CONSTRAINT `sections_ibfk_3` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
