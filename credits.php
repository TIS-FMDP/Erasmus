<?PHP
function process_credits()
{
	print '<b>Travel courses</b><br /><br />';
	$tcdata = NULL;
	if (isset($_GET['act']))
        {
                if (strcmp($_POST['Edit'], 'edit') === 0)
                {
                        $tcdata = db_travel_courses();
                        edit_travel_course($tcdata);
                }
                else if (strcmp($_POST['Add'], 'add') === 0)
                        add_travel_course();
                else if (strcmp($_POST['Save'], 'save') === 0)
                        save_travel_course();
                else if (strcmp($_POST['Remove'], 'remove') === 0)
                {
                        $tcdata = db_travel_courses(); 
                        remove_record($tcdata);
                }
                else if (strcmp($_POST['Remove'], 'yes') === 0)
                        yes_remove_travel_courses();
        }
	if ($tcdata == NULL) $tcdata = db_travel_courses();
	$tcf = format_travel_courses($tcdata);
	show_table(array('ID', 'Associated travel', 'Foreign course code', 'Foreign course name', 'Credits', 'Grade', 'Course type', 'FMFI course'), $tcf);
}

function format_travel_courses($tcdata)
{
	$tcf = array();
	foreach($tcdata as $tc)
	{
		$trav = db_travel_item_formatted($tc[1]);
		$tcf[] = array($tc[0], $trav, $tc[2], $tc[3], $tc[4], $tc[5], $tc[6][1], $tc[7][1]);
	}
	return $tcf;
}


function edit_travel_course($fcs)
{
	$tc = retrieve_first_checked_record($fcs);
	if ($tc === NULL) infomsg("Please tick the record you want to edit");
	else addedit_travel_course($tc);
}

function addedit_travel_course($tc)
{
	$column_labels = array('ID', 'Associated travel', 'Foreign course code', 'Foreign course name', 'Credits', 'Grade', 'Course type', 'FMFI course');
	$column_types = array('RDONLY', '*', 'text', 'text', 'number', 'text', '*', '*');
	show_edit_form($column_labels, $tc, $column_types, array(db_travels_list(), array(array('c', 'compulsory'), array('e', 'elective'), array('o', 'optional')), db_fmfi_courses(TRUE)));
}

function add_travel_course()
{
	$tvid = db_get_first_travel();
	$idfmfi = db_get_first_fmfi();
	addedit_travel_course(array('-', $tvid, 'enter code', 'enter name', '9', 'G', 'o', $idfmfi));
}

function save_travel_course()
{
	$id = $_POST['0'];
	$tvid = $_POST['1'];
	$code = htmlspecialchars($_POST['2']);
	$name = htmlspecialchars($_POST['3']);
	$credits = $_POST['4'];
	$grade = $_POST['5'];
	$ctype = $_POST['6'];
	$idfmfi = $_POST['7'];
	db_save_travel_course($id, $tvid, $code, $name, $credits, $grade, $ctype, $idfmfi);
}

function yes_remove_travel_courses()
{
        db_remove_travel_courses($_POST['ids']);
}

?>
