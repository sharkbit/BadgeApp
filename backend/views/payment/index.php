<?php

$this->title = 'Payment ';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['payment/index']];

echo $this->render('_view-tab-menu', ['confParams' => $confParams]).PHP_EOL;

 ?>

<?php
	if($confParams->qb_env == 'prod') { echo "<h2>Enviroment: Production </h2>"; } else { echo "<h2>Enviroment: Development  </h2>"; }

	if($confParams->conv_p_pin) {
		echo "Converge Production Pin Found<br>";
	}

	if($confParams->conv_d_pin) {
		echo "Converge Development Pin Found<br>";
	}
 ?>

