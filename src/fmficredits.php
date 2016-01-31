<?PHP
function process_fmfi_credits()
{
    global $userrole;
    if ($userrole === "admin") {
	
    print '<b>Travel FMFI courses</b><br /><br />';
	$filter_exchange = retrieve_filter_exchange();
	show_exchange_filter($filter_exchange);

	$tcdata = NULL;
	if (isset($_GET['act']))
        {
                if ($_POST['Edit'] === 'edit')
                {
                        $tcdata = db_fmfi_courses_for_an_exchange($filter_exchange, TRUE);
                        edit_fmfi_travel_course($tcdata);
                }
                else if ($_POST['Add'] === 'add')
                        add_fmfi_travel_course($filter_exchange);
                else if ($_POST['Save'] === 'save')
                        save_fmfi_travel_course($filter_exchange);
                else if ($_POST['Remove'] === 'remove')
                {
                        $tcdata = db_fmfi_courses_for_an_exchange($filter_exchange, TRUE); 
                        remove_record($tcdata);
                }
                else if ($_POST['Remove'] === 'yes')
                        yes_remove_fmfi_travel_courses();
        }
	logmsg("retr");
	if ($tcdata == NULL) $tcdata = db_fmfi_courses_for_an_exchange($filter_exchange, TRUE);
	logmsg("ieve $filter_exchange");
	$tcf = format_travel_fmfi_courses($tcdata);
	show_table(array('ID', 'FMFI course', 'Grade'), $tcf);
}
else{
    return;
}
}

function format_travel_fmfi_courses($tcdata)
{
	$tcf = array();
	foreach($tcdata as $tc)
		$tcf[] = array($tc[0], $tc[1][1] . ': ' . $tc[1][2] . ', ' . $tc[1][3] . ' credits', $tc[2]);
	return $tcf;
}

function edit_fmfi_travel_course($fcs)
{
	$tc = retrieve_first_checked_record($fcs);
	if ($tc === NULL) infomsg("Please tick the record you want to edit");
	else addedit_fmfi_travel_course($tc);
}

function addedit_fmfi_travel_course($tc)
{
	$column_labels = array('ID', 'FMFI course', 'Grade');
	$column_types = array('RDONLY', '*', 'text');
	show_edit_form($column_labels, $tc, $column_types, array(db_fmfi_courses(TRUE)));
}

function add_fmfi_travel_course($ft)
{
	addedit_fmfi_travel_course(array('-', db_get_first_fmfi(), ''));
}

function save_fmfi_travel_course($ft)
{
	$id = $_POST['0'];
	$idfmfi = $_POST['1'];
	$grade = $_POST['2'];
	db_save_fmfi_travel_course($id, $ft, $idfmfi, $grade);
}

function yes_remove_fmfi_travel_courses()
{
        db_remove_fmfi_travel_courses($_POST['ids']);
}

?>
