<?php  
function get_applications(){ 
include 'includes/paginator.class.php';  
$link = db_connect();

$sql = 'SELECT a.ID, a.SEMESTER,a.state, b.FIRSTNAME, b.LASTNAME FROM STUDENT_EXCHANGES a INNER JOIN STUDENT_STUDY_PROGRAMS c ON a.ID_STUDENT_STUDY_PROGRAM = c.ID
        INNER JOIN STUDENTS b ON c.ID_STUDENT = b.ID'.$pagination->limit.';';    
$query = mysqli_query($link,$sql) or die(mysqli_error($link));
$total = mysqli_num_rows($query);
$pagination->items_total = $total;
$pagination->paginate();
$application_row = '';
$bg = false;
while ($row = mysqli_fetch_array($query))
        {
          $row_bg = ($bg) ? 'row2' : 'row1';
          $application_row .= '<tr class="' . $row_bg . '">
	                <td>' . $row['ID'] . '</td>
                  <td>' . $row['FIRSTNAME'] . ' ' . $row['LASTNAME'] . '</td>
	                <td>' . $row['SEMESTER'] . '</td>
                  <td>' . $row['state'] . '</td>
                </tr>';
                  $bg = !$bg;
        }
?>
<style>
table {
	width: 100%;
	border: 1px solid #CCCFD3;
	background-color: #FFFFFF;
	padding: 1px;
}

th {
	padding: 3px 4px;
	color: #FFFFFF;
	background: #70AED3 url("../images/gradient2b.gif") bottom left repeat-x;
	border-top: 1px solid #6DACD2;
	border-bottom: 1px solid #327AA5;
	text-align: left;
	font-size: 0.75em;
	text-transform: uppercase;
}

td {
	text-align: left;
	font-size: 0.85em;
	padding: 4px;
	line-height: 1.20em;
}

table.type2 {
	border: none;
	background: none;
	padding: 0;
}

table.type2 th {
	background: none;
	border-top: none;
	text-align: center;
	color: #115098;
	padding: 2px 0;
}

table.type2 td {
	padding: 0;
	font-size: 1em;
}

table.type2 td.name {
	padding: 2px;
	vertical-align: middle;
}

table.type3  {
	float: right;
	width: 300px;
	border: none;
	background-color: transparent;
	padding: 0;
}

table.type3 thead th {
	background-color: transparent;
	border-top: none;
	text-align: center;
	color: #115098;
	padding: 0 3px;
	font-size: 0.85em;
	font-weight: normal;
	text-transform: none;
}

table.type3 tbody th {
	border-top: none;
	text-align: left;
	text-transform: none;
	padding: 0;
	border: none;
	font-size: 0.90em;
	font-weight: normal;
	width: 100%;
}

table.type3 td {
	text-align: center;
	padding: 1px;
}

th.name {
	text-align: left;
	width: auto;
}

td.name {
	text-align: left;
	font-weight: bold;
}

.entry {
	text-align: left;
	font-weight: normal;
}

.row1 { background-color: #F9F9F9; }
.row2 { background-color: #DCEBFE; }
.row3 { background-color: #DBDFE2; }
.row4 { background-color: #E4E8EB; }
.col1 { background-color: #DCEBFE; }
.col2 { background-color: #F9F9F9; }

.spacer {
	background-color: #DBDFE2;
	height: 1px;
	line-height: 1px;
}
</style>
<h1>Zoznam prihlášok</h1>
<p>Zoznam všetkých podaných prihlášok</p>

                                                          
<table width="100%" cellpadding="3" cellspacing="1" class="table1">
	<thead>
		<tr>
			<th>ID</th>
			<th>Meno</th>
			<th>Semester</th>
      <th>Stav prihlášky</th>
		</tr>
	</thead>
	<tbody>
		<?=$application_row?>
	</tbody>
</table>
<div class="pagination">
  <?=sprintf('Celkovo prihlášok', $pagination->items_total)?> &bull; <?=sprintf("Stránka <strong>%d</strong> z <strong>%d</strong>", $pagination->current_page, $pagination->num_pages)?> &bull; <span><?=$pagination->display_pages()?></span>
</div>
<?php
}
?>
