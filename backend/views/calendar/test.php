<?php 
//  This is to test the calendar API, i.e:
//  https://tmp.agcrange.org/calendar/open-range?eDate=2026-09-19&start=04:00%20PM&stop=06:00%20PM&facility=[3]&id=&pattern=&e_status=2&tst=1
//
use yii\helpers\Html;

$this->title = 'Calendar test';
?>

<h2><?= Html::encode($this->title) ?></h2>

<?php
echo "My Pattern: $pattern (#$rng_pri) <hr/>";

echo json_encode($returnMsg, JSON_PRETTY_PRINT)."<br><br>";
print_r($returnMsg);
?>