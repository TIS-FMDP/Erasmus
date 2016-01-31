<?PHP
function process_foreign_credits()
{
  global $userrole;
  if ($userrole === "admin") {
	print '<b>Travel foreign courses</b><br /><br />';
	$filter_exchange = retrieve_filter_exchange();
	show_exchange_filter($filter_exchange);

	$tcdata = NULL;
	if (isset($_GET['act']))
        {
                if ($_POST['Edit'] === 'edit')
                {
			$tcdata = db_foreign_courses_for_an_exchange($filter_exchange, TRUE);
                        edit_travel_course($tcdata);
                }
                else if ($_POST['Add'] === 'add')
                        add_travel_course($filter_exchange);
                else if ($_POST['Save'] === 'save')
                        save_travel_course($filter_exchange);
                else if ($_POST['Remove'] === 'remove')
                {
			$tcdata = db_foreign_courses_for_an_exchange($filter_exchange, TRUE);
                        remove_record($tcdata);
                }
                else if ($_POST['Remove'] === 'yes')
                        yes_remove_travel_courses();
        }
	logmsg("retr");
	if ($tcdata == NULL) $tcdata = db_foreign_courses_for_an_exchange($filter_exchange, TRUE);
	logmsg("ieve");
	$tcf = format_travel_foreign_courses($tcdata);
	show_table(array('ID', 'Foreign course code', 'Foreign course name', 'Credits', 'Grade', 'Course type'), $tcf);
  }
else{
    return;
}
}

function retrieve_filter_exchange()
{
	global $filter_url;
	logmsg("trsel: " . $_POST['travel_selection']);
	if (isset($_POST['travel_selection'])) $filter_t = $_POST['travel_selection'];
	else $filter_t = $_GET['ft'];
	if (strlen($filter_t) == 0) $filter_t = $_POST['ft'];
	if (strlen($filter_t) == 0) $filter_t = db_get_first_travel();
	$filter_url = '&ft=' . $filter_t;
	logmsg("ft: " . $filter_t);
	return $filter_t;
}

function show_exchange_filter($filter_t)
{
	$sort_url = determine_sort_url();
	$menu = $_GET['m'];
	print '<form method="POST" action="index.php?m=' . $menu . $sort_url . '&setfilter">';
	print '<table><tbody><tr><th>Select student travel:</th></tr><tr><td>';
	//if (strlen($_POST['travel_selection']) > 0) $ft = $_POST['travel_selection']; else $ft = $filter_t;
	show_select('travel_selection', db_travels_list(), $filter_t, ' onchange="this.form.submit()"');
	print '</td></tr></tbody></table></form><br />';
}

function format_travel_foreign_courses($tcdata)
{
	$tcf = array();
	foreach($tcdata as $tc)
	{
		//$trav = db_travel_item_formatted($tc[1]);
		$tcf[] = array($tc[0], $tc[1], $tc[2], $tc[3], $tc[4], $tc[5][1]);
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
	$column_labels = array('ID', 'Foreign course code', 'Foreign course name', 'Credits', 'Grade', 'Course type');
	$column_types = array('RDONLY', 'text', '100text', 'number', 'text', '*');
	show_edit_form($column_labels, $tc, $column_types, array(array(array('c', 'compulsory'), array('e', 'elective'), array('o', 'optional'))));
}

function add_travel_course($ft)
{
	addedit_travel_course(array('-', 'enter code', 'enter name', '9', 'G', 'o'));
}

function save_travel_course($ft)
{
	$id = $_POST['0'];
	$code = htmlspecialchars($_POST['1']);
	$name = htmlspecialchars($_POST['2']);
	$credits = $_POST['3'];
	$grade = $_POST['4'];
	$ctype = $_POST['5'];
	db_save_travel_course($id, $ft, $code, $name, $credits, $grade, $ctype);
}

function yes_remove_travel_courses()
{
        db_remove_travel_courses($_POST['ids']);
}

?>
