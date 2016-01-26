<?PHP
function process_travels()
{
	print '<b>Travels</b><br /><br />';

	list($filter_a, $filter_d1, $filter_d2) = configure_travel_filters();
	show_travel_filters($filter_a, $filter_d1, $filter_d2);

	if (isset($_GET['act']))
        {
                if ($_POST['Edit'] === 'edit')
                {
        		$ts = db_travels($filter_a, $filter_d1, $filter_d2);
                        edit_travel($ts);
                }
                else if ($_POST['Add'] === 'add')
                        add_travel();
		else if ($_POST['Print'] === 'print')
		{
        		$ts = db_travels($filter_a, $filter_d1, $filter_d2);
			print_link_travel($ts);
		}
                else if ($_POST['Save'] === 'save')
                        save_travel();
		else if (isset($_POST['upload13']))
                        upload_file_and_edit_travel();
                else if ($_POST['Remove'] === 'remove')
                {
                        $ts = db_travels($filter_a, $filter_d1, $filter_d2);
                        remove_record($ts);
                }
                else if ($_POST['Remove'] === 'yes')
                        yes_remove_travels();
                else if ($_POST['DeleteFile'] === 'yes')
                        yes_delete_file_and_edit_travel();
                else if ($_POST['DeleteFile'] === 'no')
                        no_delete_file_and_edit_travel();
                else if ($_POST['AddOption'] === 'add')
                        add_course_to_travel_and_edit_travel();
                else if ($_POST['Remove'] === 'yes')
                        yes_remove_travels();
                else foreach($_POST as $var => $val)
                {
                        if (strncmp($var, 'deletefile', 10) === 0)
                        {
                                delete_file_and_edit_travel($var);
                                break;
                        }
                }
        }
        
	$tsdata = db_travels($filter_a, $filter_d1, $filter_d2);
	$fts = format_travel_data($tsdata);
	show_table(array('ID', 'Level', 'Agreement', 'Semester', 'Date FROM (Y-m-d)', 'Date TO (Y-m-d)', 'Student', 'Study program', 'Language:', 'Has', 'Expected', 'Soc.stip.', 'Handicap', 'Files', 'Courses', 'Notes', 'Cancelled'), $fts, TRUE, TRUE);
    echo "<script>$('table>tbody>tr>td:nth-child(15)>article').each(function(index){\$(this).readmore({collapsedHeight: 45, moreLink: '<a href=\"#\">More</a>',lessLink: '<a href=\"#\">Less</a>'})})</script>"; //oshitoshit
}

function print_link_travel($ts)
{
	$tv = retrieve_first_checked_record($ts);
	print 'Print: <a href="printtravel.php?id=' . $tv[0] . '" target="_blank">document for record ' . $tv[0] . '</a><br /><br />';
}

function configure_travel_filters()
{
	global $filter_url;

	if ($_POST['filter'] === 'apply filter')
	{
		if (strlen($_POST['filterbyagr']) > 0) $filter_a = $_POST['agreement_selection'];
		else $filter_a = "";

		if (strlen($_POST['filterbydates']) > 0)
		{
			$filter_d1 = $_POST['filter_from'];
			$filter_d2 = $_POST['filter_to'];
		}
	}
	else
	{
		$filter_a = $_GET['fa'];
		$filter_d1 = $_GET['fdf'];
		$filter_d2 = $_GET['fdt'];
	}
	if (strlen($filter_a) > 0) $filter_url = '&fa=' . $filter_a;
	else $filter_url = '';
	if (strlen($filter_d1) > 0) $filter_url = $filter_url . '&fdf=' . $filter_d1 . '&fdt=' . $filter_d2;

	return array($filter_a, $filter_d1, $filter_d2);
}

function show_travel_filters($filter_a, $filter_d1, $filter_d2)
{
	$sort_url = determine_sort_url();
	print '<form method="POST" action="index.php?m=travels&' . $sort_url . '&setfilter">';
	print '<table id="tblTravels"><tbody><tr><th colspan="3">Show only travels:</th></tr><tr><td>';
	print '<input type="checkbox" name="filterbyagr"';
	print ((strlen($filter_a) > 0)?' checked="checked"':'') . '" /> for agreement: </td><td colspan="2">';
	if (strlen($_POST['agreement_selection']) > 0) $fa = $_POST['agreement_selection']; else $fa = $filter_a;
	show_select('agreement_selection', db_retrieve_agreements_data(TRUE), $fa);
	print '</td></tr><tr><td><input type="checkbox" name="filterbydates"' . ((strlen($filter_d1) > 0)?'checked="checked"':'') . '" />';
	if (strlen($_POST['filter_from']) > 0) $fromval = $_POST['filter_from'];
	else if (strlen($filter_d1) > 0) $fromval=$filter_d1; else $fromval="2000-12-24";
        print 'overlapping period </td><td>from (Y-M-D): <input type="date" name="filter_from" value="' . $fromval . '" />';
	if (strlen($_POST['filter_to']) > 0) $toval = $_POST['filter_to'];
	else if (strlen($filter_d2) > 0) $toval=$filter_d2; else $toval="2010-12-24";
        print ' to (Y-M-D): <input type="date" name="filter_to" value="' . $toval . '" /></td><td>';
        print '<input type="submit" name="filter" value="apply filter" /></td></tr></tbody></table></form><br />';
}

function format_travel_data($tsdata)
{
	$ftv = array();
	foreach ($tsdata as $t)

	{	
		$rw = array($t[0], studylevel($t[1]), $t[2][1], semester($t[7], $t[17]), $t[3], $t[4], $t[5][1], $t[6][1], $t[8][1], $t[9], $t[10], YesOrNothing($t[11]), YesOrNothing($t[12]), format_files($t[13]), "<article class='special'>".$t[14]."</article>", $t[15], YesOrNothing($t[16]));
		$ftv[] = $rw;
	}
	return $ftv;
}

function studylevel($l)
{
	switch($l):
		case 1: return "Bachelor"; break;
		case 2: return "Master"; break;
		case 3: return "Doctoral"; break;
	endswitch;
}

function semester($s, $y)
{
	if ($s === 'W') return $y . ' winter';
	return $y . ' summer';
}

function YesOrNothing($x)
{
	if ($x === 1) return "yes";
	return "";
}

function edit_travel($ts)
{
	$tv = retrieve_first_checked_record($ts);
	if ($tv === NULL) infomsg("Please tick the record you want to edit");
	else addedit_travel($tv);
}

function addedit_travel($tv)
{
	$column_labels = array('ID', 'Level', 'Agreement', 'Date FROM (Y-m-d)', 'Date TO (Y-m-d)', 'Student', 'Study program', 'Semester', 'Language:', 'Has level', 'Expected level', 'Soc.stip.', 'Handicap', 'Files', 'Courses', 'Notes', 'Cancelled', 'Year');
	$column_types = array('RDONLY', '*', '*', 'date', 'date', '*', '*', '*', '*', 'text', 'text', 'checkbox', 'checkbox', 'files', 'RDONLY', '100text', 'checkbox', '*');
	$aggr = db_retrieve_agreements_data(TRUE);
	$stds = db_students(TRUE);
	$sps = db_study_programs(TRUE);
	$langs = db_languages();
	//$cors = db_courses_for_an_exchange($tv[0]);
	$levels = array(array(1, "Bachelor"), array(2, "Master"), array(3, "Doctoral"));
	$years = selection_of_years();
	show_edit_form($column_labels, $tv, $column_types, array($levels, $aggr, $stds, $sps, array(array('W', 'winter'), array('S', 'summer')), $langs, $years));
}

function add_travel()
{
	global $default_study_program;
	$agid = db_get_first_agreement();
	$stid = db_get_first_student();
	$yr = date('Y');
	$yr = $yr . '/' . ($yr + 1);
	$tvid = db_save_travel("-", 2, $agid, date('Y-m-d'), date('Y-m-d'), $stid, $default_study_program, 'W', 9, '-', 'B2', 0, 0, '', 0, $yr);
	$tv = db_retrieve_travel_byID($tvid);
	addedit_travel($tv);
}

function save_travel()
{
	$id = $_POST['0'];
	$year = htmlspecialchars($_POST['1']);
	$agid = $_POST['2'];
	$datefrom = htmlspecialchars($_POST['3']);
	$dateto = htmlspecialchars($_POST['4']);
	$stid = $_POST['5'];
	$spid = $_POST['6'];
	$semester = $_POST['7'];
	$lang = $_POST['8'];
	$lhas = htmlspecialchars($_POST['9']);
	$lexp = htmlspecialchars($_POST['10']);
	$socs = isset($_POST['11'])?1:0;
	$hcap = isset($_POST['12'])?1:0;
	$notes = $_POST['15'];
	$cancelled = isset($_POST['16']);
	$acyear = $_POST['17'];
	db_save_travel($id, $year, $agid, $datefrom, $dateto, $stid, $spid, $semester, $lang, $lhas, $lexp, $socs, $hcap, $notes, $cancelled, $acyear);
}

function yes_remove_travels()
{
        db_remove_travel_cascading($_POST['ids']);
}

function yes_delete_file_and_edit_travel()
{
        $fileid = $_POST['fileid'];
        logmsg("deletefile $fileid");
        db_delete_files(array($fileid));
        no_delete_file_and_edit_travel();
}

function no_delete_file_and_edit_travel()
{
        $id = $_POST['ID'];
        $aggr = db_retrieve_travel_byID($id);
        addedit_travel($aggr);
        logmsg('delete_file_aea leaves');
}

function delete_file_and_edit_travel($var)
{
        $f = explode('_', $var);
        $fileid = $_POST['fileid_' . $f[1] . '_' . $f[2]];
        logmsg("deletefile $var; $fileid");
        save_travel();
        $details = db_retrieve_file_details($fileid);
        $id = $_POST['0'];
        show_delete_file_form($fileid, $details[3], $details[4], array('ID' => $id));
        $tv = db_retrieve_travel_byID($id);
        addedit_travel($tv);
}

function upload_file_and_edit_travel()
{
        save_travel();
        upload_file(7, 13, $_POST['0'], 0);
        $id = $_POST['0'];
        $tv = db_retrieve_travel_byID($id);
        addedit_travel($tv);
}

?>
