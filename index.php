<?PHP
include 'config.php';
include 'db.php';
include 'forms.php';
include 'submissions.php';
include 'agreements.php';
include 'universities.php';
include 'studyprograms.php';
include 'fmficourses.php';
include 'subjectareas.php';
include 'students.php';
include 'travels.php';
include 'foreigncredits.php';
include 'fmficredits.php';
include 'control_logic.php';
session_start();
$link = db_connect();
show_headers(determine_year_filter_need());
if (determine_user_credentials())
{
	show_main_menu();
	process_form_submissions();
}
else 
{
	show_public_menu();
	process_public_submissions();
}
show_footers();
$link->close();
?>
