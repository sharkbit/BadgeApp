<?php

use QuickBooksOnline\API\PlatformService\PlatformService;
//use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
 
$this->title = 'payment page... ';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => 'Payment', 'url' => ['payment/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['payment/page']];

echo $this->render('_view-tab-menu', ['confParams' => $confParams]).PHP_EOL; 

	$serviceContext = $dataService->getServiceContext();
	if (!$serviceContext)
		exit("Problem while initializing ServiceContext.\n");

	// Prep Platform Services
	$platformService = new PlatformService($serviceContext);

	// Get App Menu HTML
	$Respxml = $platformService->Disconnect();

	if ($Respxml->ErrorCode == '0')	{
		unset($_SESSION['token']);
		$confParams->qb_realmId=null;
		$confParams->qb_token=null;
		$confParams->qb_token_date=null;
		$confParams->save();
		echo "Disconnect successful! <br />";
	} else {
		echo "Error! Disconnect failed..<br />";
		
		if ($Respxml->ErrorCode  == '270') {
			echo "OAuth Token Rejected! Recheck if you have the right OAuth tokens in theconfig .<br />";
		}
	}
	echo "ResponseXML: ";
	var_dump( $Respxml);

?>
<br />fin