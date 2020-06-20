<?php
use QuickBooksOnline\API\Core\OperationControlList;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Security\OAuthRequestValidator;

$this->title = 'payment page... ';
$this->params['breadcrumbs'][] = ['label' => 'Admin Function', 'url' => ['badge/admin-function']];
$this->params['breadcrumbs'][] = ['label' => 'Payment', 'url' => ['payment/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['payment/page']];

echo $this->render('_view-tab-menu', ['confParams' => $confParams]).PHP_EOL; 

/*
// Tell us whether to use your QBO vs QBD settings, from App.config
$serviceType = 'QBO'; //IntuitServicesType::QBO;

// Get App Config
//$realmId = ConfigurationManager::AppSettings('RealmID');
$realmId = $confParams->qb_realmId;
if (!$realmId)
	exit("Please add realm to App.Config before running this sample.\n");

// Prep Service Context
//$requestValidator = new OAuthRequestValidator(ConfigurationManager::AppSettings('AccessToken'),
 //                                             ConfigurationManager::AppSettings('AccessTokenSecret'),
 //                                             ConfigurationManager::AppSettings('ConsumerKey'),
 //                                             ConfigurationManager::AppSettings('ConsumerSecret'));
$token = unserialize($confParams->qb_token);
$requestValidator = new OAuthRequestValidator($token['oauth_token'],$token['oauth_token_secret'],
                                              $confParams->qb_oauth_cust_key,$confParams->qb_oauth_cust_sec);

$serviceContext = new ServiceContext($realmId, $serviceType, $requestValidator);
*/
$serviceContext = $dataService->getServiceContext();
if (!$serviceContext)
	exit("Problem while initializing ServiceContext.\n");

// Prep Platform Services
$platformService = new PlatformService($serviceContext);

// Get App Menu HTML
$Respxml = $platformService->Reconnect();

if ($Respxml->ErrorCode != '0') {
	echo "Error! Reconnection failed..";
	if ($Respxml->ErrorCode  == '270') {
		echo "OAuth Access Token Rejected! <br />";
	}
	else if($Respxml->ErrorCode  == '212') {
		echo "Token Refresh Window Out of Bounds! The request is made outside the 30-day window bounds. <br />";
	}
	else if($Respxml->ErrorCode  == '24') {
		echo "Invalid App Token! <br />";
	}
	
} else {
	echo "Reconnect successful! Please go back and update the app.config file with the new oAuth tokens.<br />";
	$token = new stdClass();
	$token->OAuthToken = $Respxml->OAuthToken;
	$token->OAuthTokenSecret = $Respxml->OAuthTokenSecret;
	
	$_SESSION['token'] = serialize( $token );
	$confParams->qb_token = $_SESSION['token'];
	$confParams->qb_token_date = yii::$app->controller->getNowTime();
	
	yii::$app->controller->createLog(false, 'trex Respxml', var_export($Respxml,true));
	//$_SESSION['token'] = serialize( $access_token );
	
}
echo "ResponseXML: ";
var_dump( $Respxml);


echo "<br /> <br /><a href=\"/payment/index?connectWithIntuitOpenId\">Go Back</a>";
echo '&nbsp;&nbsp;&nbsp;';
echo '<a target="_blank" href="payment/ReadMe.htm">Read Me</a><br />';


?>
<br />fin
