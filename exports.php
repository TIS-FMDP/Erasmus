<?PHP
include 'config.php';
include 'db.php';
include 'control_logic.php';
include 'forms.php';
include 'export_db.php';

$link = db_connect();
session_start();
determine_user_credentials();
if (strlen($userid) == 0) echo "<html><body>unauthorized access</body></html>";
else
{
	switch ($_GET['i']):
	case 'rector': export_rector(); break;
	case 'students': export_students(); break;
	case 'agreements': export_agreements(); break;
	case 'travels': export_travels(); break;
	case 'files': export_files(); break;
	case 'log': export_log(); break;
	endswitch;
}
$link->close();

function export_rector()
{
	headers_for_export('fmfi_erasmus.csv');
	db_export_rector();
}

function export_students()
{
	headers_for_export('students.csv');
	db_export_students();	
}

function export_agreements()
{
	headers_for_export('agreements.csv');
	db_export_agreements();
}

function export_travels()
{
	headers_for_export('travels.csv');
	db_export_travels();
}

function export_files()
{
	headers_for_export('files.zip');
	db_export_files();
}

function export_log()
{
	headers_for_export('log.csv');
	db_export_log();
}



?>
