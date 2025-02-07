-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2021 at 08:50 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Database: `attmgsystem`

-- --------------------------------------------------------

-- Table structure for table `admininfo`
CREATE TABLE `admininfo` (
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `email` varchar(30) NOT NULL,
  `fname` varchar(20) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table `admininfo`
INSERT INTO `admininfo` (`username`, `password`, `email`, `fname`, `phone`, `type`) VALUES
('admin', 'admin', 'admin@gmail.com', 'admin', '2147483647', 'admin'),
('ibrahim', '123', 'dahir.idx@gmail.com', 'ibrahim', '7785945', 'teacher'),
('john', 'password', 'john@gmail.com', 'John Walker', '8541112450', 'student'),
('kevin', 'password', 'kevinm@gmail.com', 'Kevin Moore', '1247778540', 'teacher');

-- --------------------------------------------------------

-- Table structure for table `department`
CREATE TABLE `department` (
  `dept_id` varchar(20) NOT NULL,
  `dept_name` varchar(50) NOT NULL,
  PRIMARY KEY (`dept_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insert sample data into `department`
INSERT INTO `department` (`dept_id`, `dept_name`) VALUES
('CSE', 'Computer Science and Engineering'),
('HR', 'Human Resources'),
('ACC', 'Accounting');

-- --------------------------------------------------------



-- Table structure for table `students`
CREATE TABLE `students` (
  `st_id` varchar(20) PRIMARY KEY,
  `st_name` varchar(20) NOT NULL,
  `st_dept` varchar(20) NOT NULL,
  `st_batch` int(4) NOT NULL,
  `st_sem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table `students`
INSERT INTO `students` (`st_id`, `st_name`, `st_dept`, `st_batch`, `st_sem`) VALUES
('8871', 'Ruqiyo Omar Hirsi', 'CSE', 2024, 2),
('8872', 'Fadumo Mahdi Mohamed', 'CSE', 2024, 2),
('8873', 'Abdifitax Abdirahman Ali', 'CSE', 2024, 2),
('8875', 'Abdirabi Abdisalan Ahmed', 'CSE', 2024, 2),
('8876', 'AbdirahmanMohamud Abdullahi', 'CSE', 2024, 2),
('8877', 'Ali Farhan Muse', 'CSE', 2024, 2),
('8878', 'Abdulqadir Jamac Bixi', 'CSE', 2024, 2),
('8879', 'Ahmed Mohamed Ismail', 'CSE', 2024, 2),
('8880', 'Abdirahman Ahmed Abdalla', 'CSE', 2024, 2),
('8881', 'Abdiqani Ali Abdikarim', 'CSE', 2024, 2),
('8882', 'Mahamed Jamac Abdullahi', 'CSE', 2024, 2),
('8884', 'Farhan Abdinasir Mahamed', 'CSE', 2024, 2),
('8885', 'Abdifatax Yasin Jamac', 'CSE', 2024, 2),
('8886', 'Abdikhaliq Mohamud Mahamed', 'CSE', 2024, 2),
('8887', 'Sharmake Mohamed Abdulqadir', 'CSE', 2024, 2),
('8888', 'Mohamed Abdiasiz Adan', 'CSE', 2024, 2),
('8889', 'Said Abdirisaq Ali Shire', 'CSE', 2024, 2),
('8891', 'Abdulqadir Abshir Ali', 'CSE', 2024, 2),
('8890', 'Abdikarim Said Mohamed', 'CSE', 2024, 2),
('8892', 'Ahmed Ali Yusuf', 'CSE', 2024, 2),
('8893', 'Yasin Ahmed Mohamed', 'CSE', 2024, 2),
('8894', 'Abdiwadud Abdirishid Hirsi', 'CSE', 2024, 2),
('8895', 'Abdinur Ali Ahmed', 'CSE', 2024, 2),
('8896', 'Mahmed Mahamud Bille', 'CSE', 2024, 2),
('8897', 'Abdifatax Abdi Jamac', 'CSE', 2024, 2),
('8898', 'Kaltun Ibrahim Yusuf', 'CSE', 2024, 2),
('8899', 'Masbal Abdulqadir Farah', 'CSE', 2024, 2),
('8900', 'Bushro Mohamud Mahamed', 'CSE', 2024, 2),
('8901', 'Mustalifo Ahmed Mohamud', 'CSE', 2024, 2),
('8902', 'Kawsar Mohamed Yasin', 'CSE', 2024, 2),
('9015', 'Maqsud Abdirahman Hassan', 'CSE', 2024, 2),
('8914', 'FarhanAdan Ahmed', 'CSE', 2024, 2),
('9007', 'Mohamed abdirahman Elmi', 'CSE', 2024, 2),
('6778', 'sadio Hassan Mohamed', 'CSE', 2024, 2),
('9014', 'Mohamed Mohamud Abdullahi', 'CSE', 2024, 2),
('9006', 'Awil Ibrahim Jamac', 'CSE', 2024, 2),
('8954', 'Ibrahim Abdulkadir', 'CSE', 2024, 2),
('8966', 'Ayub Osman Isse', 'CSE', 2024, 2),
('9031', 'Abdullahi Isse Osman', 'CSE', 2024, 2),
('9011', 'Abdiwali Bashir Seed', 'CSE', 2024, 2),
('9027', 'Ayan Mohamud Ahmed', 'CSE', 2024, 2),
('9061', 'Fahmo Nur Ahmed', 'CSE', 2024, 2),
('9012', 'Mukhtar Hussein Farah', 'CSE', 2024, 2);

-- --------------------------------------------------------

-- Table structure for table `teachers`
CREATE TABLE `teachers` (
  `tc_id` varchar(20) NOT NULL,
  `tc_name` varchar(20) NOT NULL,
  `tc_dept` varchar(20) NOT NULL,
  `tc_email` varchar(30) NOT NULL,
  `tc_course` varchar(20) NOT NULL,
  PRIMARY KEY (`tc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table `teachers`
INSERT INTO `teachers` (`tc_id`, `tc_name`, `tc_dept`, `tc_email`, `tc_course`) VALUES
('1', 'Abdirahman maanka', 'CSE', 'Maanka@gmail.com', 'web design'),
('2', 'Ibrahim Dahir', 'CSE', 'dahir.idx@gmail.com', 'C #');

-- --------------------------------------------------------

-- Table structure for table `course`
CREATE TABLE `course` (
  `course_id` varchar(20) NOT NULL,
  `course_name` varchar(50) NOT NULL,
  `course_description` text,
  `credits` int(11) DEFAULT NULL,
  PRIMARY KEY (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table `course`
INSERT INTO `course` (`course_id`, `course_name`, `course_description`, `credits`) VALUES
('ENG101', 'English 101', 'Introduction to English Language and Literature', 3),
('CS112', 'Communication Skills', 'Developing effective communication skills', 3),
('SBU106', 'Introduction to Business', 'Basics of business and management', 3),
('IWb108', 'Introduction to Web Design', 'Fundamentals of web design and development', 3),
('P107', 'Python Programming', 'Introduction to programming using Python', 3);

-- --------------------------------------------------------
-- Table structure for table `attendance`
CREATE TABLE `attendance` (
  `stat_id` varchar(20) NOT NULL,
  `course` varchar(20) NOT NULL,
  `st_status` varchar(10) NOT NULL,
  `stat_date` date NOT NULL,
  FOREIGN KEY (`stat_id`) REFERENCES `students` (`st_id`),
  FOREIGN KEY (`course`) REFERENCES `course` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table `attendance`
INSERT INTO `attendance` (`stat_id`, `course`, `st_status`, `stat_date`) VALUES
('8873', 'ENG101', 'Present', '2021-04-10'),
('8875', 'ENG101', 'Present', '2021-04-10'),
('8876', 'ENG101', 'Present', '2021-04-11'),
('8877', 'SBU106', 'Present', '2021-04-10'),
('8878', 'SBU106', 'Present', '2021-04-10'),
('8879', 'SBU106', 'Present', '2021-04-10'),
('8880', 'SBU106', 'Present', '2021-04-10');



-- Table structure for table `reports`
CREATE TABLE `reports` (
  `st_id` varchar(30) NOT NULL,
  `course` varchar(30) NOT NULL,
  `st_status` varchar(30) NOT NULL,
  `st_name` varchar(30) NOT NULL,
  `st_dept` varchar(30) NOT NULL,
  `st_batch` int(11) NOT NULL,
  FOREIGN KEY (`st_id`) REFERENCES `students` (`st_id`),
  FOREIGN KEY (`course`) REFERENCES `course` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table for exam definitions
CREATE TABLE `exams` (
  `exam_id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_id` varchar(20) NOT NULL,
  `tc_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `exam_date` DATE NOT NULL,
  `status` ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'draft',
  `rejection_reason` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`),
  FOREIGN KEY (`tc_id`) REFERENCES `teachers` (`tc_id`)
);

-- Table for exam components and their weights
CREATE TABLE `exam_components` (
  `component_id` INT AUTO_INCREMENT PRIMARY KEY,
  `exam_id` INT NOT NULL,
  `component_type` ENUM('midterm', 'attendance', 'quiz_assignment', 'final') NOT NULL,
  `max_marks` DECIMAL(5,2) NOT NULL,
  FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE
);

-- Table for student exam scores
CREATE TABLE `exam_scores` (
  `score_id` INT AUTO_INCREMENT PRIMARY KEY,
  `exam_id` INT NOT NULL,
  `st_id` varchar(20) NOT NULL,
  `midterm_marks` DECIMAL(5,2) DEFAULT 0,
  `attendance_marks` DECIMAL(5,2) DEFAULT 0,
  `quiz_assignment_marks` DECIMAL(5,2) DEFAULT 0,
  `final_marks` DECIMAL(5,2) DEFAULT 0,
  `total_marks` DECIMAL(5,2) GENERATED ALWAYS AS 
    (midterm_marks + attendance_marks + quiz_assignment_marks + final_marks) STORED,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE,
  FOREIGN KEY (`st_id`) REFERENCES `students` (`st_id`),
  UNIQUE KEY `unique_student_exam` (`exam_id`, `st_id`)
);

-- Sample stored procedure to calculate attendance marks
DELIMITER //
CREATE PROCEDURE CalculateAttendanceMarks(
  IN p_st_id VARCHAR(20),
  IN p_course_id VARCHAR(20),
  IN p_exam_id INT
)
BEGIN
  DECLARE total_classes INT;
  DECLARE attended_classes INT;
  DECLARE attendance_percentage DECIMAL(5,2);
  
  -- Get total number of classes
  SELECT COUNT(*) INTO total_classes
  FROM attendance
  WHERE course = p_course_id;
  
  -- Get number of classes attended
  SELECT COUNT(*) INTO attended_classes
  FROM attendance
  WHERE stat_id = p_st_id 
    AND course = p_course_id 
    AND st_status = 'Present';
  
  -- Calculate attendance percentage and marks (10% of total)
  IF total_classes > 0 THEN
    SET attendance_percentage = (attended_classes / total_classes) * 10;
  ELSE
    SET attendance_percentage = 0;
  END IF;
  
  -- Update exam_scores with calculated attendance marks
  UPDATE exam_scores
  SET attendance_marks = attendance_percentage
  WHERE exam_id = p_exam_id AND st_id = p_st_id;
END //
DELIMITER ;

-- Sample trigger to update total marks when individual marks are updated
DELIMITER //
CREATE TRIGGER before_exam_score_update
BEFORE UPDATE ON exam_scores
FOR EACH ROW
BEGIN
    SET NEW.total_marks = NEW.midterm_marks + NEW.attendance_marks + 
                         NEW.quiz_assignment_marks + NEW.final_marks;
END //
DELIMITER ;

-- Insert sample exam component configuration
INSERT INTO exam_components (exam_id, component_type, max_marks) VALUES
(1, 'midterm', 20),
(1, 'attendance', 10),
(1, 'quiz_assignment', 20),
(1, 'final', 50);
-- Indexes for dumped tables


-- Indexes for table `students`
ALTER TABLE `students`
  ADD FOREIGN KEY (`st_dept`) REFERENCES `department` (`dept_id`);



-- Indexes for table `teachers`
ALTER TABLE `teachers`
  ADD FOREIGN KEY (`tc_dept`) REFERENCES `department` (`dept_id`);

-- Indexes for table `course`


-- Indexes for table `attendance`
ALTER TABLE `attendance`
  ADD KEY `stat_id` (`stat_id`),
  ADD KEY `course` (`course`);

-- Indexes for table `reports`
ALTER TABLE `reports`
  ADD KEY `st_id` (`st_id`),
  ADD KEY `course` (`course`);

-- Constraints for dumped tables

-- Constraints for table `attendance`
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`stat_id`) REFERENCES `students` (`st_id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`course`) REFERENCES `course` (`course_id`);

-- Constraints for table `reports`
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`st_id`) REFERENCES `students` (`st_id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`course`) REFERENCES `course` (`course_id`);


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;