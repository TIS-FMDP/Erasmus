<?PHP

function process_agreements()
{
	print '<b>Bilateral Agreements</b><br /><br />';
	if (isset($_GET['act']))
	{
		if ($_POST['Edit'] === 'edit')
		{
			$agg = db_retrieve_agreements_data();
			edit_agreement($agg);
		}
		else if ($_POST['Add'] === 'add')
			add_agreement();
		else if ($_POST['Save'] === 'save')
			save_agreement();
		else if ($_POST['Remove'] === 'remove') 
		{
			$agg = db_retrieve_agreements_data();
			remove_record($agg);
		}
		else if ($_POST['Remove'] === 'yes')
			yes_remove_agreement();
		else if (isset($_POST['upload10']))
			upload_file_and_edit_agreement();
		else if ($_POST['DeleteFile'] === 'yes')
			yes_delete_file_and_edit_agreement();
		else if ($_POST['DeleteFile'] === 'no')
			no_delete_file_and_edit_agreement();
		else foreach($_POST as $var => $val)
		{
			if (strncmp($var, 'deletefile', 10) === 0)
			{
				delete_file_and_edit_agreement($var);
				break;
			}
		}
	}
	$columns = array('ID', 'University (country, code)', 'Valid FROM (YYYY/YYYY)', 'Valid TO (YYYY/YYYY)', 'Coordinator', 'Subject Area', 'Accepted levels', '# of students', 'Files');
	$agg = db_retrieve_agreements_data();
	$fagg = format_agreements_data($agg);
	show_table($columns, $fagg);
}

function yes_delete_file_and_edit_agreement()
{
	$fileid = $_POST['fileid'];
	logmsg("deletefile $fileid");
	db_delete_files(array($fileid));
	no_delete_file_and_edit_agreement();
}

function no_delete_file_and_edit_agreement()
{
	$id = $_POST['ID'];
	$aggr = db_retrieve_agreement_byID($id);
	addedit_agreement($aggr);
	logmsg('delete_file_aea leaves');
}

function delete_file_and_edit_agreement($var)
{
	$f = explode('_', $var);
	$fileid = $_POST['fileid_' . $f[1] . '_' . $f[2]];
	logmsg("deletefile $var; $fileid");
	save_agreement();
	$details = db_retrieve_file_details($fileid);
	$id = $_POST['0'];
	show_delete_file_form($fileid, $details[3], $details[4], array('ID' => $id));
	$aggr = db_retrieve_agreement_byID($id);
	addedit_agreement($aggr);
}

function upload_file_and_edit_agreement()
{
	save_agreement();
	upload_file(3, 10, $_POST['0'], 1);
	$id = $_POST['0'];
	$aggr = db_retrieve_agreement_byID($id);
	addedit_agreement($aggr);
}

function format_agreements_data($agg)
{
	$fagg = array();
	foreach($agg as $a)
	{
		$ff = format_files($a[10]);
		if (check_in_range($a[2], $a[3], time()))
		{
		   $decorate1 = '<b>';
		   $decorate2 = '</b>';
		}
		else $decorate1 = $decorate2 = "";
		$fa = array($a[0], $decorate1 . $a[1][1] . ' (' . $a[1][2] . ', ' . $a[1][3] . ')' . $decorate2, $a[2], $a[3], $a[4], $a[5][1], BSCMGRPHD($a[6], $a[7], $a[8]), $a[9], $ff);
		$fagg[] = $fa;
	}
	return $fagg;
}

function BSCMGRPHD($bc, $mgr, $phd)
{
	if ($bc === 1) $x = "BSc"; else $x = "";
	if ($mgr === 1) { if ($x === "") $x = "Mgr"; else $x = $x . ", Mgr"; }
	if ($phd === 1) { if ($x === "") $x = "PhD"; else $x = $x . ", PhD"; }
	return $x;
}

function check_in_range($start_date, $end_date, $actual)
{
  // Convert to timestamp
  $start_ts = strtotime(substr($start_date,4) . '-1-1');
  $end_ts = strtotime(substr($end_date, -4) . '-12-31');

  // Check that date is between start & end
  return (($actual >= $start_ts) && ($actual <= $end_ts));
}

function edit_agreement($agg)
{
	$aggr = retrieve_first_checked_record($agg);
	if ($aggr === NULL) infomsg("Please tick the record you want to edit");
	else addedit_agreement($aggr);
}

function addedit_agreement($aggr)
{
	$column_labels = array('ID', 'University (country)', 'Valid FROM (YYYY/YYYY)', 'Valid TO (YYYY/YYYY)', 'Coordinator', 'Subject Area', 'BC', 'Mgr', 'PhD', '# of students', 'Files');
	$column_types = array('RDONLY', '*', 'text', 'text', 'text', '*', 'checkbox', 'checkbox', 'checkbox', 'number', 'files');
	$unis = db_universities_with_countries();
	$areas = db_subject_areas(TRUE);
	show_edit_form($column_labels, $aggr, $column_types, array($unis, $areas));
}

function add_agreement()
{
	$unis = db_universities_with_countries();
	$said = db_get_first_subject_area();
	$newid = db_save_agreement('-', $unis[0][0], '2014/2015', '2020/2021', $said, 'enter coordinator name', 0, 0, 0, 0);
	$aggr = db_retrieve_agreement_byID($newid);
	addedit_agreement($aggr);
}

function yes_remove_agreement()
{
	db_remove_agreements_cascading($_POST['ids']);
}

function save_agreement()
{
	//logmsg("save_agreement " . $_POST['0'] . ', ' . $_POST['1'] . ', ' . $_POST['2'] . ', ' . $_POST['3'] . ', ' . $_POST['4'] . ', ' . $_POST['5'] . ', ' . $_POST['6'] . ', ' . $_POST['7'] . ', ' . $_POST['8'] . ', ' . $_POST['9']);
	$id = $_POST['0'];
	$uniID = $_POST['1'];
	$validFROM = htmlspecialchars($_POST['2']);
	$validTO = htmlspecialchars($_POST['3']);
	$subjareaID = $_POST['5'];
	$coord = htmlspecialchars($_POST['4']);
	$bc = isset($_POST['6']);
	$mgr = isset($_POST['7']);
	$phd = isset($_POST['8']);
	$totalnum = $_POST['9'];
	db_save_agreement($id, $uniID, $validFROM, $validTO, $subjareaID, $coord, $bc, $mgr, $phd, $totalnum);
}

?>
