<?php 
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Utility\Configuration\ConfigurationManager;

define('OAUTH_REQUEST_URL', 'https://oauth.intuit.com/oauth/v1/get_request_token');
define('OAUTH_ACCESS_URL', 'https://oauth.intuit.com/oauth/v1/get_access_token');
define('OAUTH_AUTHORISE_URL', 'https://appcenter.intuit.com/Connect/Begin');

// The url to this page. it needs to be dynamic to handle runnable's dynamic urls
define('CALLBACK_URL','https://'.$_SERVER['HTTP_HOST'].'/payment/oauth');

// cleans out the token variable if comming from
// connect to QuickBooks button
if ( isset($_GET['start'] ) ) {
	unset($_SESSION['token']);
}

try {
	$oauth = new OAuth( $confParams->qb_oauth_cust_key, $confParams->qb_oauth_cust_sec, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
	$oauth->enableDebug();
	$oauth->disableSSLChecks(); //To avoid the error: (Peer certificate cannot be authenticated with given CA certificates)
	if (!isset( $_GET['oauth_token'] ) && !isset($_SESSION['token']) ){

		// step 1: get request token from Intuit
		$request_token = $oauth->getRequestToken( OAUTH_REQUEST_URL, CALLBACK_URL );
		$_SESSION['secret'] = $request_token['oauth_token_secret'];
		
		// step 2: send user to intuit to authorize 
		header('Location: '. OAUTH_AUTHORISE_URL .'?oauth_token='.$request_token['oauth_token']);
		exit;
	}

	if ( isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) ){
		//yii::$app->controller->createLog(false, 'trex_oauth_post','x003a');
		// step 3: request a access token from Intuit
		$oauth->setToken($_GET['oauth_token'], $_SESSION['secret']);
		$access_token = $oauth->getAccessToken( OAUTH_ACCESS_URL );
		
		$_SESSION['token'] = serialize( $access_token );
		$_SESSION['realmId'] = $_REQUEST['realmId'];  // realmId is legacy for customerId
		$_SESSION['dataSource'] = $_REQUEST['dataSource'];

		$token = $_SESSION['token'] ;
		$realmId = $_SESSION['realmId'];
		$dataSource = $_SESSION['dataSource'];  //default 'QBO';
		$secret = $_SESSION['secret'] ;

		$confParams->qb_realmId = $realmId;
		$confParams->qb_token = $_SESSION['token'];
		$confParams->qb_token_date = yii::$app->controller->getNowTime();
		$confParams->save();

		// write JS to pup up to refresh parent and close popup
		echo '<script type="text/javascript">
            window.opener.location.href = window.opener.location.href;
            window.close();
          </script>';
	}
} catch(OAuthException $e) {
	echo "Got auth exception";
	echo '<pre>';
	print_r($e);
}
?>
