<?php

$this->title = 'Inventory';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['payment/inventory']];

echo $this->render('_view-tab-menu', ['confParams' => $confParams]).PHP_EOL;

   
echo $this->render('_inventory', [
       // 'dataService' => $dataService
    ]) ?>