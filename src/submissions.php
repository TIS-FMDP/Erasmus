<?PHP
function process_public_submissions()
{
	switch ($_GET['m']):
		case 'agreements': process_agreements(); break;
    case 'application': process_application(); break;
	endswitch;
}

function process_form_submissions()
{
	switch ($_GET['m']):
		case 'logout': process_logout(); break;
		case 'agreements': process_agreements(); break;
		case 'logs': process_logs(); break;
		case 'universities': process_universities(); break;
		case 'travels': process_travels(); break;
		case 'foreigncredits': process_foreign_credits(); break;
		case 'fmficredits': process_fmfi_credits(); break;
		case 'students': process_students(); break;
		case 'studyprogs': process_study_programs(); break;
		case 'fmficourses': process_fmfi_courses(); break;
		case 'subjectareas': process_subject_areas(); break;
		case 'export': process_export(); break;
    case 'app_list': get_applications(); break;
    case 'edit_form': edit_application(); break;
		default:
		case 'welcome': show_welcome(); break;
	endswitch;
}

function determine_year_filter_need()
{
	global $selected_year;
	$selected_year = $_GET['y'];
	if (strlen($selected_year) === 0)
		if (isset($_POST['select_year'])) $selected_year = $_POST['select_year'];
	$m = $_GET['m'];
	if ($m === 'travels') return true;
	if ($m === 'credits') return true;
	if ($m === 'students') return true;
	return false;
}
 
function process_logout()
{
	session_unset();
	$userid = ""; 
	echo '<br /><br />You have been logged off. You can <a href="index.php">login again</a>.<br /><br />';
}

function process_logs()
{
    global $userrole;
    if ($userrole === "admin") {
        
	print '<b>Logs</b><br /><br />';
	$logdata = db_retrieve_logs();
	show_table(array('date', 'ip', 'user', 'table', 'record', 'operation', 'description', 'new value'),
                   $logdata, FALSE);
}
else{return;}
}

function process_export()
{
 global $userrole;
 if ($userrole === "admin") {  
?>
<b>Export</b><br /><br />
Select one of the export options:<br /><br />
        <a href='exports.php?i=rector'>fmfi_erasmus.csv</a> - sheet for communication with the International Office of University<br /><br />
        <a href='exports.php?i=students'>students.csv</a> - list of all students and all their study programs<br />
        <a href='exports.php?i=agreements'>agreements.csv</a> - list of all agreements with other universities<br />
        <a href='exports.php?i=travels'>travels.csv</a> - list of all exchange trips<br />
        <a href='exports.php?i=files'>files.zip</a> - all attachments in one zip-file<br />
        <a href='exports.php?i=log'>log.csv</a> - log of all operations<br />
<br />
To import CSV file to Excel - select Data from the main menu then Import data from text file, choose UTF-8 encoding and comma and double-quotes as delimiters.<br />
To import it in OpenOffice, open it as a file and select the delimiters and encoding in the forthcoming dialog.
<?PHP
 }
 else{return;}
}

function remove_record($agg)
{
        $remrecs = retrieve_all_checked_records($agg);
        $remids = collectIDs($remrecs);
	if (count($remrecs) > 0)
        	show_remove_form($remrecs, $remids);
	else
		infomsg("Please tick the record(s) you wish to remove");
}

function recycle_files($filenames)
{
	global $ERA;

	foreach($filenames as $f)
	{
		rename($ERA . 'files/' . $f, $ERA . 'recycle/' . $f);
	}
	infomsg('file(s) recycled');
}

function upload_file($tab, $cnt, $idrecord)
{
	global $ERA;

	if(!isset($_POST['upload' . $cnt])) return;
	$target_dir = $ERA . 'uploads/';
	$original_filename = basename($_FILES['filename' . $cnt]['name']);
	$target_file = $target_dir . $original_filename;
	$tmp_filename = $_FILES['filename' . $cnt]['tmp_name'];
	logmsg('original_filename=' . $original_filename);
	logmsg('target_file=' . $target_file);
	logmsg('tmp_filename=' . $tmp_filename);

	// Check if file already exists
	if (file_exists($target_file)) 
		unlink($target_file);

    	if (move_uploaded_file($tmp_filename, $target_file)) 
	{
		$description = $_POST['desc' . $cnt];
		$fileid = db_insert_uploaded_file($tab, $idrecord, $original_filename, $description);
		rename($target_file, $ERA . 'files/' . $fileid);
        	infomsg('The file ' . $original_filename . ' has been uploaded.');
		db_append_to_log('FILES', $fileid, 'add', $description, $original_filename);
	}
	else infomsg('Sorry, there was an error uploading your file.');
}

function format_files($files)
{
	$fls = "";
	foreach($files as $f)
	{
		if (strlen($fls) > 0) $fls = $fls . ', ';
		$fls = $fls . '<a href="downloadfile.php?id=' . $f[0] . '" title="' . $f[1] . '" target="_blank">' . $f[2] . '</a>';
	}
	return $fls;
}

function collectIDs($recs)
{
	$ids = ""; 
	foreach($recs as $r)
	{
		if (strlen($ids) > 0) $ids = $ids . ', ';
		$ids = $ids . $r[0];
	}
	return $ids;
}

function retrieve_first_checked_record($data)
{
	foreach($data as $r)
		if (isset($_POST[$r[0]])) return $r;
	logmsg("no item checked");
	return NULL;
}

function retrieve_all_checked_records($data)
{
	$recs = array();
	foreach($data as $r)
		if (isset($_POST[$r[0]])) $recs[] = $r;
	return $recs;
}
?>
