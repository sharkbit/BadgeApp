<?php 

use yii\helpers\Html;

$this->title = 'Calendar test';
?>

<h2><?= Html::encode($this->title) ?></h2>

<?php
echo "My Pattern: $pattern (#$rng_pri) <hr/>";

echo json_encode($returnMsg, JSON_PRETTY_PRINT)."<br><br>";
print_r($returnMsg);
?>
