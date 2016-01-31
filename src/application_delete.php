<?php  
function application_delete(){  

$id = $_GET['id'];
global $userrole;
include 'includes/form.php';
include 'includes/safe.php';

$link = db_connect();

//errors catching
$error = false;
$error_log = "";

global $year;
if($userrole === "admin"){
$sql = 'DELETE FROM STUDENTS
WHERE ID IN (SELECT ID_STUDENT FROM STUDENT_STUDY_PROGRAMS WHERE ID IN (SELECT ID_STUDENT_STUDY_PROGRAM FROM STUDENT_EXCHANGES WHERE ID="'.$id.'"))';
$query1 = mysqli_query($link,$sql) or die(mysqli_error($link));

$sql = 'DELETE FROM STUDY_PROGRAMS
WHERE ID IN (SELECT ID_STUDYPROGRAM FROM STUDENT_STUDY_PROGRAMS WHERE ID IN (SELECT ID_STUDENT_STUDY_PROGRAM FROM STUDENT_EXCHANGES WHERE ID="'.$id.'"))';
$query2 = mysqli_query($link,$sql) or die(mysqli_error($link));

$sql = 'DELETE FROM  STUDENT_STUDY_PROGRAMS
WHERE ID IN (SELECT ID_STUDENT_STUDY_PROGRAM FROM STUDENT_EXCHANGES WHERE ID="'.$id.'")';
$query3 = mysqli_query($link,$sql) or die(mysqli_error($link));

$sql = 'DELETE FROM STUDENT_EXCHANGES
WHERE ID="'.$id.'"';
$query4 = mysqli_query($link,$sql) or die(mysqli_error($link));
}

header( "Location: index.php?m=app_list" );
}

?>
