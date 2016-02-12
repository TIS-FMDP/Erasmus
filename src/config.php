<?PHP
$ERA = '';
$logON = false;
$year = "2016/2017";
$print_year = "2016/2017";
$deadline = "15-02-2016";
$infoo = getdate();
$date = $infoo['mday'];
$month = $infoo['mon'];
$year = $infoo['year'];
$current_date = "$date-$month-$year";
$dekan = 'prof. RNDr. Jozef Masarik, DrSc.';
setlocale(LC_COLLATE, "sk_SK");
$first_year = 2014;
$default_study_program = 93; //mIKV
$DB_USER='root';
$DB_NAME='erasmus';
$DB_PASS='';

function study_points($i){
  switch ($i) {
    case 0:
        $points = 25;
        break;
    case 1:
        $points = 50;
        break;
    case 2:
        $points = 75;
        break;
  }
  return $points;
}
?>
