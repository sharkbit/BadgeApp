<?php

$this->title = 'Inventory';
$this->params['breadcrumbs'][] = ['label' => 'store', 'url' => ['/sales']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/payment/inventory']];

echo $this->render('_view-tab-menu').PHP_EOL;

   
echo $this->render('/payment/_inventory', [
        'dataService' => $dataService
    ]) ?>