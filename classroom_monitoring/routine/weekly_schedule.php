<?php


$conn = new mysqli("localhost", "root", "", "classroom_monitoring");

$classroom_id = $_POST['classroom_id'];
$instructor_id = $_POST['instructor_id'];
$course_id = $_POST['course_id'];
$day_of_week = $_POST['day_of_week'];
$session_number = $_POST['session_number'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];

$stmt = $conn->prepare("INSERT INTO weekly_schedule (classroom_id, instructor_id, course_id, day_of_week, session_number, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiisiss", $classroom_id, $instructor_id, $course_id, $day_of_week, $session_number, $start_time, $end_time);
$stmt->execute();

echo "<script>alert('Schedule added successfully!'); window.location.href='index.php';</script>";
?>


