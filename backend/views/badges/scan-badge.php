<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Clubs
*/
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Scan Badge';
$this->params['breadcrumbs'][] = ['label' => 'Admin Menu', 'url' => ['/site/admin-menu']];
$this->params['breadcrumbs'][] = $this->title;

$csrfToken=Yii::$app->request->getCsrfToken();
?>

<div class="row">
  <div class="col-xs-12" id="ErrorInfo" style="display: none;">
      <div class="alert alert-danger alert-dismissable fade in" id="error_info">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      </div>
    </div>
</div>
<div class="row" > <!-- id="" style="display: none;"> -->
	<div class="col-md-12 ">
		<p>Use Scanner to read <b>Badge Info</b> OR <b>Credit Card Info</b></p>
		<p class="help-block help-block-error"></p>
	</div>
</div>

<ul id="ReaderInput">
</ul>

<script>  //FName.charAt(0).toUpperCase() + FName.substr(1).toLowerCase();
	function ProcessSwipe(newUPC) {

		if  ((cleanUPC.indexOf('ANSI 6360') > 0) || (cleanUPC.indexOf('AAMVA6360') > 0)) { // Matched Drivers Licence
			console.log('Drivers Licence Scanned: ', cleanUPC);
			var FName=false;

			if (cleanUPC.indexOf('DAC') > 0) {  //Parse Name
			  var fsName = cleanUPC.indexOf('DAC')+3;
			  var feName = cleanUPC.indexOf("ArrowDown",fsName);
			  var FName = cleanUPC.slice(fsName,feName);
			  FName = titleCase(FName);
			  var msName = cleanUPC.indexOf('DAD')+3;
			  var meName = cleanUPC.indexOf("ArrowDown",msName);
			  var MName = cleanUPC.slice(msName,meName);
			  MName = MName.charAt(0).toUpperCase();
			  var lsName = cleanUPC.indexOf('DCS')+3;
			  var leName = cleanUPC.indexOf("ArrowDown",lsName);
			  var LName = cleanUPC.slice(lsName,leName);
			  LName = titleCase(LName);
			} //Parse Name Second Try
			else if  (cleanUPC.indexOf('DAA') > 0) {
			  var nsName = cleanUPC.indexOf('DAA')+3;
			  var neName = cleanUPC.indexOf("ArrowDown",nsName);
			  var FullName = cleanUPC.slice(nsName,neName);
			  FullName = FullName.split(",");
			  var LName = titleCase(FullName[0]);
			  var FName = titleCase(FullName[1]);
			  var MName = FullName[2].charAt(0).toUpperCase();
			}
            console.log("Full Name: "+FName+' - '+MName+' - '+LName);

			if (cleanUPC.indexOf('DBB') > 0) {  //Parse Date of Birth
			  var fDOB = cleanUPC.indexOf('DBB')+3;
			  var lDOB = cleanUPC.indexOf("ArrowDown",fDOB);
			  var DOB = cleanUPC.slice(fDOB,lDOB);

			  var DOBtest=true;
			  var DOBy=DOB.substring(0,4);
			  var DOBm=DOB.substring(4,6);
			  var DOBd=DOB.substring(6);

			  if (DOBm > 12) {DOBtest=false;}
			  if (DOBd > 31) {DOBtest=false;}
			  if (DOBy < 1900) {DOBtest=false;}
			  if (!DOBtest) {
			    var DOBy=DOB.substring(4);
			    var DOBm=DOB.substring(0,2);
			    var DOBd=DOB.substring(2,4);
			  }
              console.log("DOB: m "+DOBm+" d "+DOBd+" y "+DOBy);
			}

			if (cleanUPC.indexOf('DAG') > 0) {  //Parse Address
			  var fAddr = cleanUPC.indexOf('DAG')+3;
			  var lAddr = cleanUPC.indexOf("ArrowDown",fAddr);
			  var Addr = cleanUPC.slice(fAddr,lAddr);
			  Addr = titleCase(Addr);
              console.log("Addr: "+Addr);
			}

			if (cleanUPC.indexOf('DAI') > 0) {  //Parse City
			  var fCty = cleanUPC.indexOf('DAI')+3;
			  var lCty = cleanUPC.indexOf("ArrowDown",fCty);
			  var Cty = cleanUPC.slice(fCty,lCty);
			  Cty = titleCase(Cty);
              console.log("City: "+Cty);
			}

			if (cleanUPC.indexOf('DAJ') > 0) {  //Parse State
			  var fST = cleanUPC.indexOf('DAJ')+3;
			  var lST = cleanUPC.indexOf("ArrowDown",fST);
			  var Stat = cleanUPC.slice(fST,lST);
              console.log("State: "+Stat);
            }

			if (cleanUPC.indexOf('DAK') > 0) {  //Parse ZIP
			  var fZIP = cleanUPC.indexOf('DAK')+3;
			  var lZIP = cleanUPC.indexOf("ArrowDown",fZIP);
			  var ZIP = cleanUPC.slice(fZIP,lZIP);
			  ZIP = ZIP.substring(0,5);
              console.log("ZIP: "+ZIP);
			}

			var node = document.createElement("LI");
			var textnode = document.createTextNode('Drivers Licence Scanned: '+ cleanUPC);
			node.appendChild(textnode);
			document.getElementById("ReaderInput").appendChild(node);

		}
		else if (cleanUPC.match(/B\d{16}/g)) {  // Matched Credit Card!
			console.log('Credit Card Scanned: ', cleanUPC);
			var ccNum = cleanUPC.substring(1,17);
			var fExp = cleanUPC.indexOf('^')+1;
			var fExp = cleanUPC.indexOf('^',fExp)+1;
			var ExpYr = cleanUPC.substring(fExp,fExp+2);
			var ExpMo = cleanUPC.substring(fExp+2,fExp+4);

			console.log("Num: "+ccNum+" Exp Yr: "+ExpYr+" Exp Mo: "+ExpMo);

			var node = document.createElement("LI");
			var textnode = document.createTextNode('Credit Card Scanned: '+ cleanUPC);
			node.appendChild(textnode);
			document.getElementById("ReaderInput").appendChild(node);

		}
		else if (cleanUPC.indexOf(' ') > 0) {  // Other
			console.log('Unknown Barcode scanned:141 ', cleanUPC);
			var node = document.createElement("LI");
			var textnode = document.createTextNode('Unknown Barcode scanned: '+ cleanUPC);
			node.appendChild(textnode);
			document.getElementById("ReaderInput").appendChild(node);

			var scanBadge = cleanUPC.split(" ");
			//document.getElementById("loginmemberform-barcode_c").value = scanBadge[0];

		} else  {
			console.log('Unknown Barcode scanned:151 ', cleanUPC);
		}
		cleanUPC = '';
	};
</script>