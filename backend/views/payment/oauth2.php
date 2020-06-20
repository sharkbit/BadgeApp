<?php

use backend\components\Client;
use backend\controllers\PaymentController;
$configs = PaymentController::configs();

$authorizationRequestUrl = $configs['authorizationRequestUrl'];
$tokenEndPointUrl = $configs['tokenEndPointUrl'];
$scope = $configs['oauth_scope'];
$redirect_uri = $configs['oauth_redirect_uri'];

if($confParams->qb_env == 'prod') {
	$env = 'Production';
	$client_id = $confParams->qb_oa2_id;
	$client_secret = $confParams->qb_oa2_sec;
} else {
	$env = 'Development';
	$client_id = $confParams->qb_oauth_cust_key;
	$client_secret = $confParams->qb_oauth_cust_sec;
}
		
$response_type = 'code';
$state = 'RandomState';
$include_granted_scope = 'false';
$grant_type= 'authorization_code';
//$certFilePath = './Certificate/all.platform.intuit.com.pem';
//$certFilePath = './Certificate/cacert.pem';

$client = new Client($client_id, $client_secret); //, $certFilePath);

if (!isset($_GET["code"])) {
    /*Step 1
    /*Do not use Curl, use header so it can redirect. Curl just download the content it does not redirect*/
    //$json_result = $client->getAuthorizationCode($authorizationRequestUrl, $scope, $redirect_uri, $response_type, $state, $include_granted_scope);

    $authUrl = $client->getAuthorizationURL($authorizationRequestUrl, $scope, $redirect_uri, $response_type, $state);
    header("Location: ".$authUrl);
    exit();
}
else {
    $code = $_GET["code"];
    $responseState = $_GET['state'];
    if(strcmp($state, $responseState) != 0){
      throw new Exception("The state is not correct from Intuit Server. Consider your app is hacked.");
    }
    $result = $client->getAccessToken($tokenEndPointUrl,  $code, $redirect_uri, $grant_type);
//yii::$app->controller->createLog(false, 'trex oauth2', var_export($result,true));
//yii::$app->controller->createLog(false, 'trex id',$_REQUEST['realmId'] );
//yii::$app->controller->createLog(false, 'trex id', var_export($_SERVER,true));

	$confParams->qb_oa2_realmId = $_REQUEST['realmId'];
	$_SESSION['_access_token'] = $result['access_token'];
	//$confParams->qb_oa2_access_date = yii::$app->controller->getNowTime('PT'.($result['expires_in']-120).'S');
	$confParams->qb_oa2_refresh_token = $result['refresh_token'];
	$confParams->qb_oa2_refresh_date = yii::$app->controller->getNowTime('PT'.$result['x_refresh_token_expires_in'].'S');
	$confParams->save();

    // JS to close popup and refresh parent page
    echo '<script type="text/javascript">
                window.opener.location.href = window.opener.location.href;
                window.close();
              </script>';
}
?>
