<?PHP
include 'config-production.php';
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
include 'application.php';
include 'application_list.php';  
include 'application_edit.php'; 
include 'application_preview.php'; 
include 'application_delete.php'; 
include 'edit_form.php';

error_reporting(E_WARNING);
session_start();
$link = db_connect();

global $filter_needed;
$filter_needed = determine_year_filter_need();
// print $filter_needed;

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
