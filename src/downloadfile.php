<?PHP
include 'config.php';
include 'db.php';
include 'control_logic.php';
include 'forms.php';

$link = db_connect();
session_start();
determine_user_credentials();
$id = $_GET['id'];
$fileinfo = db_file_for_download($id);
$filename = $fileinfo[0];
$ispublic = $fileinfo[1];
if (!$ispublic && (strlen($userid) == 0)) echo "<html><body>unauthorized access</body></html>";
else
{
	headers_for_export($filename);
	readfile($ERA . "files/$id");
}
$link->close();
?>
