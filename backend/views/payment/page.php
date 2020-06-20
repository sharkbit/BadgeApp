<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\WorkCredits */

$this->title = 'Company Info';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => 'Payment', 'url' => ['payment/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['payment/page']];

echo $this->render('_view-tab-menu', ['confParams' => $confParams]).PHP_EOL; 


$CompanyInfo = $dataService->getCompanyInfo();
$error = $dataService->getLastError();
	
if ($error) {
    echo "The Status code is: " . $error->getHttpStatusCode() . "<br />\n";
    echo "The Helper message is: " . $error->getOAuthHelperError() . "<br />\n";
    echo "The Response message is: " . $error->getResponseBody() . "<br />\n";
} else {
    $nameOfCompany = $CompanyInfo->CompanyName;
    echo "<br>Test for OAuth Complete. Company Name is {$nameOfCompany}. <br>Returned response body:\n\n<br>";
   
echo "<hr>var_dump: ";	
ob_start();
var_dump($CompanyInfo);
$result = ob_get_clean();

echo str_replace("\"\n","\"<br>",str_replace("L\n","L<br>",$result));
}

?>