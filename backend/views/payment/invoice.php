<?php

use QuickBooksOnline\API\Facades\SalesReceipt;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;

/* @var $this yii\web\View */
/* @var $dataService backend\controllers\paymentcontroller */

$this->title = 'Process ';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['payment/process']];

echo $this->render('_view-tab-menu', ['confParams' => $confParams]).PHP_EOL;
 


$salesReceiptObj = SalesReceipt::create([
  "Line" => [[
       "Id" => "1",
       "LineNum" => 1,
       "Description" => "Pest Control Services",
       "Amount" => 35.0,
       "DetailType" => "SalesItemLineDetail",
       "SalesItemLineDetail" => [
           "ItemRef" => [
               "value" => "1",
               "name" => "Pest Control"
           ],
           "UnitPrice" => 35,
           "Qty" => 1
       ]
   ]]
]);

$resultingSalesReceiptObj = $dataService->Add($salesReceiptObj);
$error = $dataService->getLastError();
if ($error) {
    echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
    echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
    echo "The Response message is: " . $error->getResponseBody() . "\n";
} else {
    # code...
    // Echo some formatted output
    echo "Created Sales Id={$resultingSalesReceiptObj->Id}. Reconstructed response body:\n\n";
    $xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingSalesReceiptObj, $urlResource);
    echo $xmlBody . "\n";
}
 

//********************************************
// Invoice stuff		
	/*	//Add a new Invoice
		$invoiceToCreate = Invoice::create([
		  "DocNumber" => rand(99999,1000000),
		  "Line" => [[
			  "Description" => "Family Badge 2016",
			  "Amount" => 120.00,
			  "DetailType" => "SalesItemLineDetail",
			  "SalesItemLineDetail" => [
				"ItemRef" => [
				  "value" => "1",
				  "name" => "FULL YEAR FAMILY 2018"
				],
			    "Qty" => 1,
			  ]
			]
		  ],
		  "CustomerRef" => [
			  "value" => "27",
			  //"name" => "Badge Holder"

		  ],
		  "BillEmail" => ["Address" => "Familiystore@intuit.com"],
		  "BillEmailCc" => ["Address" => "a@intuit.com"],
		]);

		$resultObj = $dataService->Add($invoiceToCreate);
		$error = $dataService->getLastError();
		if ($error) {
			echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
			echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
			echo "The Response message is: " . $error->getResponseBody() . "\n";
		}else {
			echo "Created Id={$resultObj->Id}. Reconstructed response body:<br />\n\n";
			$xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultObj, $urlResource);
			print "<pre>";
			print_r($resultObj);
			print "</pre>";

		}  */
		echo "Fin <br />\n";
		
?>