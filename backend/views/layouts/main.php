<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);

header("Expires: Fri, 01 Jan 1990 00:00:00 GMT");
header("Pragma: no-store, no-cache");
header("Cache-Control:  max-age=0, private, no-cache, must-revalidate, no-store");
header("Vary: *");
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?= Html::csrfMetaTags() ?>
    <title>AGC <?= Html::encode($this->title) ?></title>
    <!--<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>  -->
    <script src="<?=yii::$app->params['rootUrl']?>/js/jquery-3.5.1.min.js"></script>
    <!--<script src="https://cdn.jsdelivr.net/jsbarcode/3.6.0/barcodes/JsBarcode.code128.min.js"></script> -->
    <script src="<?=yii::$app->params['rootUrl']?>/js/JsBarcode.code128.min.js"></script>
    <!--<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>  -->
    <script src="<?=yii::$app->params['rootUrl']?>/js/angular.min.js"></script>

    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">  -->
    <link rel="stylesheet" href="<?=yii::$app->params['rootUrl']?>/css/font-awesome.min.css" />

    <link rel="stylesheet" href="<?=yii::$app->params['rootUrl']?>/css/sweetalert.css" />
    <link rel="stylesheet" href="<?=yii::$app->params['rootUrl']?>/css/waitMe.css" />
    <link rel="stylesheet" href="<?=yii::$app->params['rootUrl']?>/css/chosen.min.css" />
    <link rel="stylesheet" type="text/css" href="<?=yii::$app->params['rootUrl']?>/font/flaticon.css" />
    <?php $this->head() ?>
</head>
<body ng-app="agc"  class="waitMe_body" id="wait-me">
<?php $ver = file_get_contents('version.php',TRUE); $srv_name = explode ('.',$_SERVER['HTTP_HOST'])[0];
$this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => "Associated Gun Clubs ($srv_name $ver)",
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems[] = '<li><a href="javascript:addTime()" id="ext_clock" >Clock</a></li>'."\n";
    $menuItems[] = ['label' => 'Home', 'url' => ['/site/index']];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
		$pg="?url=".base64_encode($_SERVER['REQUEST_URI']);
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'.$pg], 'post')
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . "</li>\n";
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?php if((yii::$app->controller->id.'/'.yii::$app->controller->action->id!='site/index') || ((yii::$app->controller->id.'/'.yii::$app->controller->action->id='site/index') && count($_SESSION['back'])>1)) {
			echo Html::a( '<i class="fa fa-arrow-left" aria-hidden="true"></i> Go Back', '?goBack=true', ['class' => 'btn btn-sm btn-primary pull-right', 'style' => 'margin-top:3px']);}  ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= skinka\widgets\gritter\AlertGritterWidget::widget() ?>
        <?= Alert::widget() ?>
        <?= $content ?>

    </div>
</div>

<footer class="footer">
  <div class="container">
    <div class="row">
      <div class="col-xs-6 col-sm-6">
        Please use <a href="https://github.com/sharkbit/BadgeApp/issues" target=_blank>GitHub</a> for issues or feature requests.
	  </div>
      <div class="col-xs-6 col-sm-6 pull-right">
        <p class="pull-right"> <button onclick="window.open('https://github.com/sharkbit/BadgeApp/commits/master')">About Version <?php include "version.php" ?></button>  Powered by <a target="_blank" href="http://www.yiiframework.com/" rel="external">Yii</a></p>
	  </div>
    </div>
  </div>
</footer>

<?php $this->endBody() ?>

<script src="<?=yii::$app->params['rootUrl']?>/js/sweetalert.min.js"></script>
<script src="<?=yii::$app->params['rootUrl']?>/js/waitMe.js"></script>
<script src="<?=yii::$app->params['rootUrl']?>/js/chosen.jquery.min.js"></script>
<style>
  .chosen-container-multi .chosen-choices {padding: 3px; }
</style>
<script type="text/javascript">
    var logsout;
    <?php if(isset($_SESSION['timeout'])) { ?>
    function addTime(){
        logsout = new Date();
        logsout.setMinutes(logsout.getMinutes() + <?=$_SESSION['timeout']?>);
    };
    <?php } ?>

    let newUPC = ''; let UPCnt = 1; let keyCnt = 0;
    //document.addEventListener("keydown", function(e) {
    $(window).keydown(function(e){
        const textInput = e.key || String.fromCharCode(e.keyCode);

    <?php if (!strpos($_SERVER['REQUEST_URI'],'password')) { ?>
        if  ((e.key == '%') || (newUPC.charAt(0)=='%')) {
            e.preventDefault();
            if (textInput != 'Shift') {
                newUPC += e.key;
            }

            if (e.key == '?') {
    <?php if (!strpos(' '.$_SERVER['REQUEST_URI'],'site/login-member')) { ?>
                    e.preventDefault();
    <?php } ?>
                if (newUPC.match(/B\d{16}/g)) { UPCnt = 2; } else { UPCnt = 1; }
                keyCnt ++;
                if (keyCnt == UPCnt) {
                    newUPC = newUPC.slice(1, -1);
                    ProcessSwipe(newUPC);
                    newUPC = '';
                    keyCnt = 0;
                }
            }
        }
    <?php } ?>

        // Prevent Enter from Submitting Forms, Must Click Buttons.
        if ((window.location.pathname.indexOf('create') >0) ||
            (window.location.pathname.indexOf('update')>0) ||
            (window.location.pathname.indexOf('sales')>0)) {
            console.log('NO Enter Button');
            if(e.keyCode == 13) {
                var el = document.activeElement;
                if (el.tagName.toLowerCase() != 'textarea') {
                    e.preventDefault();
                    return false;
                }
            }
        }
    });

    function titleCase(str) {
        return str.toLowerCase().split(' ').map(function(word) {
            return (word.charAt(0).toUpperCase() + word.slice(1));
        }).join(' ');
    }

    function ccFormat(input){
        // Strip all characters from the input except digits
        input = input.replace(/\D/g,'');
        // Trim the remaining input to ten characters, to preserve phone number format
        input = input.substring(0,16);
        // Based upon the length of the string, we add formatting as necessary
        var size = input.length;
        if(size == 0){
                input = input;
        }else if(size < 5){
                input = input;
        }else if(size < 9){
                input = input.substring(0,4)+'-'+input.substring(4,8);
        }else if(size < 13){
                input = input.substring(0,4)+'-'+input.substring(4,8)+'-'+input.substring(8,12);
        }else{
                input = input.substring(0,4)+'-'+input.substring(4,8)+'-'+input.substring(8,12)+'-'+input.substring(12,17);
        }
        return input;
    }

    function SwipeError(swipe,PageLoc) {
        console.log("b-l-m:200 Swipe Error! from -> " + PageLoc);
        //console.log(swipe);
        jQuery.ajax({
            method: 'POST',
            data: {'PageLoc':PageLoc,'ErrorData': swipe},
            url: '<?=yii::$app->params['rootUrl']?>/badge/log-error',
            crossDomain: false
        });
    }

    function run_waitMe(action){
        if(action=="show") {
            $("#wait-me").waitMe({
                effect: "win8",
                text: "Please wait...",
                bg: "rgba(255,255,255,0.7)",
                color: "#00aff0",
                sizeW: "",
                sizeH: "",
                source: "",
                onClose: function() {}
            });
        }
        else if (action=="hide") {
            $(".waitMe").hide();
        }
    }

    function getTimestamp(date) {
        var myDate = new Date(date);
        return myDate.getTime()/1000.0;
    }

    String.prototype.toProperCase = function () {
        return this.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
    };

var app = angular.module('agc', []);
<?php
if((strpos($_SERVER['REQUEST_URI'], 'badges/create')) || (strpos($_SERVER['REQUEST_URI'], 'badges/update')) || (strpos($_SERVER['REQUEST_URI'], 'site/new-member'))) {
?>

    $(".chosen_select").chosen({placeholder_text_multiple:'Choose Clubs',width: "100%"});

	function get_fees(memTypeId) {
		if (memTypeId!='') {
			run_waitMe('show');
			var responseData;
			jQuery.ajax({
				method: 'GET',
				url: '<?=yii::$app->params['rootUrl']?>/membership-type/fees-by-type?from=n&id='+memTypeId,
				crossDomain: false,
				success: function(responseData, textStatus, jqXHR) {
					responseData = JSON.parse(responseData);
					$("#badges-badge_fee").val(parseFloat(Math.round(responseData.badgeFee * 100) / 100).toFixed(2));

					$("#badges-discounts-disp").val(responseData.discount);
					$("#badges-discounts").val(responseData.discount);
					$("#badges-item_name").val(responseData.item_name);
					$("#badges-item_sku").val(responseData.item_sku);
					$("#badges-amt_due-disp").val(responseData.badgeSpecialFee);
					$("#badges-amt_due").val(responseData.badgeSpecialFee);
					console.log(responseData);
					doCalcNew();
				},
				error: function (responseData, textStatus, errorThrown) {
					console.log(responseData);
				},
			});
			run_waitMe('hide');
			fillBarcode()
		}
	};

    function fillBarcode() {
        var ranText="";
        var possible = "ABCDEFGHJKMNPQRSTUVWXYZ23456789";
        for (var ri = 0; ri < 2; ri++)
           ranText += possible.charAt(Math.floor(Math.random() * possible.length));
        var barcodeData = ("00" + $('#badges-club_id').val()).slice(-2)+' '+("00" + $('#badges-mem_type').val()).slice(-2)+' '+$('#badges-badge_number').val()+' '+ranText;
        $('#badges-qrcode').val(barcodeData);
        barcodeGenerate(barcodeData);
        $(".barcode").show(300);
    };

    function barcodeGenerate(newData) {
        $("svg.barcode").attr('jsbarcode-value',newData);
        JsBarcode(".barcode").init();
    };

    function family_badge_view(action) {
        if(action=='show') {
            $("#primary-badge-summary").show(500);
			$("#no-primary-error").show(500);
        }
        else if(action=='hide') {
            $("#primary-badge-summary").hide(500);
			$("#no-primary-error").hide(500);
        }
    }

    function getPrimaryBadger(req_badgeNumber,type) {
		family_badge_view('show');
//$("#no-primary-error").hide(500);
//        $("#primary-badge-summary").hide(500);

        if(req_badgeNumber) {
            $("#HideMySubmit").hide(500);
            $("#searchng-badge-animation").show(500);
            jQuery.ajax({
                method: 'GET',
                dataType:'json',
                url: '<?=yii::$app->params['rootUrl']?>/badges/api-request-family?badge_number='+req_badgeNumber,
                crossDomain: false,
                success: function(responseData, textStatus, jqXHR) {
                    $("#searchng-badge-animation").hide(500);
                    if(responseData.status=='success') {
                        if(responseData.mem_type==50 || responseData.mem_type==70 || responseData.mem_type==99 ) {
                            var PrimeExpTimestamp = getTimestamp(responseData.expires);
                            var resExpTimestamp = Math.floor(Date.now() / 1000);
                            if(PrimeExpTimestamp < resExpTimestamp) {
                                var bgren = '<?=yii::$app->params['rootUrl']?>/badges/update?badge_number='+responseData.badge_number;
                                $("h4#no-primary-error").html("Please Renew <a href='"+bgren+"' target='_blank'>"+responseData.first_name+" "+responseData.last_name+"'s</a> Badge First");
                                $("#no-primary-error").show(500);
                            } else {
                                var bgren = '<?=yii::$app->params['rootUrl']?>/badges/view?badge_number='+responseData.badge_number;
                                $("td#primary-block-badgeNumber").html(responseData.badge_number);
                                $("h4#no-primary-error").html("Found: <a href='"+bgren+"' target='_blank'>"+responseData.first_name+" "+responseData.last_name+"</a>");
                                document.getElementById("badges-city").value = responseData.city;
                                document.getElementById("badges-state").value = responseData.state;
                                document.getElementById("badges-zip").value = responseData.zip;
                                document.getElementById("badges-ice_contact").value =responseData.prefix+" "+responseData.first_name+" "+responseData.last_name+" "+responseData.suffix;
                                document.getElementById("badges-ice_phone").value = responseData.ice_phone;
                                document.getElementById("badges-address").value = responseData.address;
                                $("#no-primary-error").show(500);
                                $("#HideMySubmit").show(500);
                            }
                        } else {
                            $("h4#no-primary-error").html(responseData.first_name+" "+responseData.last_name+" is not a Primary Badge Holder");
                            $("#no-primary-error").show(500);
                        }
                    } else if(responseData.status=='error') {
                        $("h4#no-primary-error").html("Sorry! could not find a user");
                        if(type!='init') {
                            $("#no-primary-error").show(500);
                        }
                        $("#badges-primary").val('');
                    }
                },
                error: function (responseData, textStatus, errorThrown) {
                    $("#searchng-badge-animation").hide(500);
                   // $("#no-primary-error").hide(500);
                    console.log(responseData);
                },
            });
        }
    }

    $('#badges-zip').keyup(function(e) {
        zipcode = $("#badges-zip").val();
        if(zipcode.length==5) {
            console.log('Using '+zipcode);
            jQuery.ajax({
                method: 'GET',
                url: '<?=yii::$app->params['rootUrl']?>/badges/api-zip?zip='+zipcode,
                crossDomain: false,
                async: true,
                success: function(responseData, textStatus, jqXHR) {
                    responseData = JSON.parse(responseData);
                    if(responseData.City) {
					$mycity=responseData.City.toProperCase()
                    $("#badges-city").val($mycity);
                    $("#badges-state").val(responseData.State);
					}
                },
                error: function (responseData, textStatus, errorThrown) {
                    console.log(responseData);
                },
            });
        }
    });

    function phoneFormat(input){
        // Strip all characters from the input except digits
        input = input.replace(/\D/g,'');
        // Trim the remaining input to ten characters, to preserve phone number format
        input = input.substring(0,10);
        // Based upon the length of the string, we add formatting as necessary
        var size = input.length;
        if(size == 0){
                input = input;
        }else if(size < 4){
                input = '('+input;
        }else if(size < 7){
                input = '('+input.substring(0,3)+') '+input.substring(3,6);
        }else{
                input = '('+input.substring(0,3)+') '+input.substring(3,6)+' - '+input.substring(6,10);
        }
        return input;
    }

    function collectRenewFee(action,memTypeId,badgeYear) {
        if(action=='fill') {
            var myUrl = '<?=yii::$app->params['rootUrl']?>/membership-type/fees-by-type?from=r&id='+memTypeId;
			console.log('collectRenewFee: '+myUrl);
            jQuery.ajax({
                method: 'GET',
                url: myUrl,
                crossDomain: false,
                success: function(responseData, textStatus, jqXHR) {
                    responseData =  JSON.parse(responseData);
                    console.log(responseData);
                    $("#badgesubscriptions-badge_fee").val(parseFloat(Math.round(responseData.badgeFee * 100) / 100).toFixed(2));

                    var badgeNumber = $("#badgesubscriptions-badge_number").val();
                    var badgeFee = parseInt($("#badgesubscriptions-badge_fee").val());
                    var credit = $("#badgesubscriptions-total_credit").val();
                    var isCurent = $("#badgesubscriptions-isCurent").val();
					$("#badgesubscriptions-item_name").val(responseData.item_name);
					$("#badgesubscriptions-item_sku").val(responseData.item_sku);

                    jQuery.ajax({
                        method: 'POST',
                        url: '<?=yii::$app->params['rootUrl']?>/badges/api-generate-renaval-fee',
                        crossDomain: false,
                        data: {'badgeNumber':badgeNumber,'BadgeFee': badgeFee,'credit':credit,'isCurent':isCurent,'badgeYear':badgeYear},
                        success: function(responseData, textStatus, jqXHR) {
                            responseData =  JSON.parse(responseData);
                            if(responseData.redeemableCredit=='') {
                                responseData.redeemableCredit='0';
                            }
                            console.log(responseData);
                            $("#badgesubscriptions-redeemable_credit").val(responseData.redeemableCredit);
                            $("#badgesubscriptions-discount").val(parseFloat(Math.round(responseData.discount * 100) / 100).toFixed(2));
                            $("#badgesubscriptions-amount_due").val(responseData.amountDue);
	                        doCalcUp();
                        },
                        error: function (responseData, textStatus, errorThrown) {
                            console.log(responseData);
                        },
                    });
                },
                error: function (responseData, textStatus, errorThrown) {
                    console.log(responseData);
                },
            });
        }
         if(action=='remove') {
            $("#badgesubscriptions-badge_fee").val('');
            $("#badgesubscriptions-redeemable_credit").val('');
            $("#badgesubscriptions-discount").val('');
            $("#badgesubscriptions-amount_due").val('');
        }
    }

app.controller("MembershipTypeForm", function($scope) {

});

app.controller("CreateBadgeController", function($scope) {

    var qrcode = $("#badges-qrcode").val();

    if(qrcode=='') {
        $(".barcode").hide(300);
    }

    $("#badges-qrcode").change(function() {
        qrcode = $("#badges-qrcode").val();
        if(qrcode=='') {
            $(".barcode").hide(300);
        }
        else {
             $(".barcode").show(300);
        }
    });

    $("#badges-payment_method").change(function() {
        var pay_meth = document.getElementById("badges-payment_method");
        var selectedVal = pay_meth.options[pay_meth.selectedIndex].value;
        if(selectedVal=="creditnow") {
            $("#cc_form_div").show();
            $("#HideMySubmit").hide();
        } else {
            $("#cc_form_div").hide();
            var myTest = $("h4#no-primary-error")[0];
            if((myTest.style.display==='none') || (myTest.hidden==true)) {
                $("#HideMySubmit").show();
            }
        }
    });

    $( document ).ready(function() {

        if($('#badges-qrcode').val()) {
            barcodeGenerate($('#badges-qrcode').val());
        }

        $("#badges-Process_CC").click(function() {
			$("#badges-Process_CC").hide();
            $("p#cc_info").html("Processing...");

            var formData = $("#badgeCreate").serializeArray();
            jQuery.ajax({
                method: 'POST',
                crossDomain: false,
                data: formData,
                dataType: 'json',
                url: '<?=yii::$app->params['rootUrl']?>/payment/charge',
                success: function(responseData, textStatus, jqXHR) {
                    if(responseData.status=="success") {
                        console.log(responseData);
                        if(responseData.message.status=="CAPTURED") {
                            $("p#cc_info").html( "Card Captured, Auth Code: "+ responseData.message.authCode);
                            $("#badges-cc_x_id").val(responseData.message.id);
                            $("#HideMySubmit").show();
                            $("#badges-cc_num").val(responseData.message.cardNum);
                        } else {
                            $("p#cc_info").html( "Card: "+ responseData.message);
							$("#badges-Process_CC").show();
                        }
                    } else {
                        console.log("Data error " + JSON.stringify(responseData));
                        SwipeError(JSON.stringify(responseData),'b-v-l-m:531');
                        $("p#cc_info").html(responseData.message);
						$("#badges-Process_CC").show();
                    }

                },
                error: function (responseData, textStatus, errorThrown) {
                    $("p#cc_info").html("PHP error:<br>"+responseData.responseText);
                    SwipeError(JSON.stringify(responseData.responseText),'b-v-l-m:532');
                    console.log("error "+ responseData.responseText);
					$("#badges-Process_CC").show();
                },
            });
        });

        $("#badges-discounts").change(function() {
            doCalcNew();
        });

        $("#club-id").change(function() {
            $("#badges-club_id").val($("#club-id").val());
            fillBarcode();
        });

        family_badge_view('hide');
        $("div .field-primary-id").hide();
        $("#badges-mem_type").change(function() {
            run_waitMe('show');
            var memTypeId = $("#badges-mem_type").val();
            if(memTypeId=='51') {
                $("#HideMySubmit").hide(500);
                family_badge_view('show');
            } else {
                $("#HideMySubmit").show(500);
                family_badge_view('hide');
            }
            run_waitMe('hide');

            document.getElementById('badges-expires').readOnly = false;
            var myYear = (new Date($("#defDate").val())).getFullYear();
            if(memTypeId == '99') {
                $("#badges-expires").val('Jan 31, '+(myYear+29));
                $("#badges-sticker").val('9999');
            } else if(memTypeId == '70') {
                $("#badges-expires").val('Jan 31, '+(myYear+14));
                $("#badges-sticker").val('0');
            } else {
                $("#badges-expires").val($("#defDate").val());
                $("#badges-sticker").val('');
            }
            document.getElementById('badges-expires').readOnly = true;
            fillBarcode();
			get_fees(memTypeId);
            if (memTypeId!='') {
                run_waitMe('show');
                var responseData;
                jQuery.ajax({
                    method: 'GET',
                    url: '<?=yii::$app->params['rootUrl']?>/membership-type/fees-by-type?from=n&id='+memTypeId,
                    crossDomain: false,
                    success: function(responseData, textStatus, jqXHR) {
                        responseData = JSON.parse(responseData);
                        $scope.fee = responseData;
                        $("#badges-badge_fee").val(parseFloat(Math.round(responseData.badgeFee * 100) / 100).toFixed(2));

                        $("#badges-discounts-disp").val(responseData.discount);
                        $("#badges-discounts").val(responseData.discount);
						$("#badges-item_name").val(responseData.item_name);
						$("#badges-item_sku").val(responseData.item_sku);
                        $("#badges-amt_due-disp").val(responseData.badgeSpecialFee);
                        $("#badges-amt_due").val(responseData.badgeSpecialFee);
						if (parseInt(responseData.badgeSpecialFee) > 0) {
							 $("#div_friend_block").show(0);
						} else {
							 $("#div_friend_block").hide(0);
						}
                        console.log(responseData);
                        doCalcNew();
                    },
                    error: function (responseData, textStatus, errorThrown) {
                        console.log(responseData);
                    },
                });
                run_waitMe('hide');
                fillBarcode();
            }
        });
    });

    $("#badges-FriendHelp").change(function() {
        if (document.getElementById("badges-FriendHelp").checked == true){
            $("div#badges-firbadiv").show(500);
        } else  {$("div#badges-firbadiv").hide(500);}
    });

    $("#badges-FriendBadge").change(function() {
        var friendBadge = $("#badges-FriendBadge").val();
        if(friendBadge) {
            $("p#badges-FrendStatus").html("Searching for "+friendBadge);
            var BadgeFee = parseInt($("#badges-badge_fee").val());
            var badgeYear = $("#badges-expires").val();
			var friendurl = '<?=yii::$app->params['rootUrl']?>/badges/api-generate-renaval-fee?friend_badge='+friendBadge+'&BadgeFee='+BadgeFee+'&badgeYear='+badgeYear;
			console.log(friendurl);
            jQuery.ajax({
                method: 'POST',
                dataType:'json',
                url: friendurl,
                crossDomain: false,
                success: function(responseData, textStatus, jqXHR) {
                    console.log(responseData);
                    //$("#badges-badge_fee-disp").val(responseData.BadgeFee);
                    //$("#badges-badge_fee").val(responseData.badgeFee);
                    $("#badges-discounts-disp").val(responseData.discount);
                    $("#badges-discounts").val(responseData.discount);
                    $("#badges-amt_due-disp").val(responseData.amountDue);
                    $("#badges-amt_due").val(responseData.amountDue);

                    console.log("Is it? "+responseData.redeemableCredit);
                    //document.getElementById("badges-FriendCredits").value = responseData.redeemableCredit;
                    $("#badges-FriendCredits").val(responseData.redeemableCredit);

                    $("p#badges-FrendStatus").html("Using "+responseData.redeemableCredit+" Credits from "+friendBadge);
                   // console.log(responseData);
                    doCalcNew();
                    console.log('line 612');
                },
                error: function (responseData, textStatus, errorThrown) {
                    $("p#badges-FrendStatus").html("What happened?");
                    console.log(responseData);
                },
            });
        }
    });

    var primaryRequest = $("#badges-primary").val();
    if(primaryRequest!=null || primaryRequest !=0) {
        getPrimaryBadger(primaryRequest,'init');
    }
    else {
    }
    if(primaryRequest==0 || primaryRequest ==null) {
//        $("#primary-badge-summary").hide(500);
    }

    $("#badges-primary").change(function() {
        var primaryRequest = $("#badges-primary").val();
        if(primaryRequest!=null || primaryRequest !=0) {
            getPrimaryBadger(primaryRequest,'second');
        }
        else {
            //alert("error reporting");
        }
        if(primaryRequest==0 || primaryRequest ==null) {
 //           $("#primary-badge-summary").hide(500);
        }

    });

    document.getElementById('badges-phone').addEventListener('keyup',function(evt){
        var phoneNumber = document.getElementById('badges-phone');
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        phoneNumber.value = phoneFormat(phoneNumber.value);
    });

    document.getElementById('badges-phone_op').addEventListener('keyup',function(evt){
        var phoneNumber = document.getElementById('badges-phone_op');
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        phoneNumber.value = phoneFormat(phoneNumber.value);
    });

    document.getElementById('badges-ice_phone').addEventListener('keyup',function(evt){
        var phoneNumber = document.getElementById('badges-ice_phone');
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        phoneNumber.value = phoneFormat(phoneNumber.value);
    });

    document.getElementById('badges-cc_num').addEventListener('keyup',function(evt){
        var ccnum = document.getElementById('badges-cc_num');
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        ccnum.value = ccFormat(ccnum.value);
    });
});

app.controller('UpdateBadgeController', function($scope) {

    var renewActionPermission = false;
    var qrcode = $("#badges-qrcode").val();

    if(qrcode=='') {
        $(".barcode").hide(300);
    }
    $("#badges-qrcode").change(function() {
        qrcode = $("#badges-qrcode").val();
        if(qrcode=='') {
            $(".barcode").hide(300);
        }
        else {
            $(".barcode").show(300);
        }
    });

    var badge_status = $("#badges-status").val();
    if(badge_status=="revoked") {
        $("#purchases_block").hide(300);
    }

    $("#badges-qrcode").change(function() {
        var barcodeData = $("#badges-qrcode").val();
        barcodeGenerate(barcodeData);
    });

    document.getElementById('badgesubscriptions-cc_num').addEventListener('keyup',function(evt){
        var ccnum = document.getElementById('badgesubscriptions-cc_num');
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        ccnum.value = ccFormat(ccnum.value);
    });

    document.getElementById('badgecertification-cc_num').addEventListener('keyup',function(evt){
        var ccnum = document.getElementById('badgecertification-cc_num');
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        ccnum.value = ccFormat(ccnum.value);
    });

    $("#badgesubscriptions-payment_type").change(function() {
        var pay_meth = document.getElementById("badgesubscriptions-payment_type");
        var selectedVal = pay_meth.options[pay_meth.selectedIndex].value;
        if(selectedVal=="creditnow") {
            $("#cc_form_div").show();
            $("#renew_btn").hide();
            $("#online_search").hide();
        } else if(selectedVal=="online") {
            CheckOnline();
            $("#cc_form_div").hide();
            $("#renew_btn").show();
            $("#online_search").show();
        } else {
            $("#cc_form_div").hide();
            $("#renew_btn").show();
            $("#online_search").hide();
        }
    });

    $(document).ready(function () {
        var renewActionPermission = false;

        $("#badgesubscriptions-Process_CC").click(function(e) {
            e.preventDefault();
			$("#badgesubscriptions-Process_CC").hide();
            $("p#cc_info").html("Processing...");

            var formDataB = $("#badgeUpdate,#form_badge_renew").serializeArray();
            jQuery.ajax({
                method: 'POST',
                crossDomain: false,
                data: formDataB,
                dataType: 'json',
                url: '<?=yii::$app->params['rootUrl']?>/payment/charge',
                success: function(responseData, textStatus, jqXHR) {
                    if(responseData.status=="success") {
                        console.log("success " + JSON.stringify(responseData));
                        if(responseData.message.status=="CAPTURED") {
                            $("p#cc_info").html( "Card Captured, Auth Code: "+ responseData.message.authCode);
                            $("#badgesubscriptions-cc_x_id").val(responseData.message.id);
                            $("#renew_btn").show();
                        } else {
                            $("p#cc_info").html( "Card: "+ responseData.message);
							$("#badgesubscriptions-Process_CC").show();
                        }
                    } else {
                        console.log("Data error " + JSON.stringify(responseData));
                        SwipeError(JSON.stringify(responseData),'b-v-l-m:788');
                        $("p#cc_info").html(responseData.message);
						$("#badgesubscriptions-Process_CC").show();
                    }

                },
                error: function (responseData, textStatus, errorThrown) {
                    $("p#cc_info").html("PHP error:<br>"+responseData.responseText);
                    SwipeError(JSON.stringify(responseData.responseText),'b-v-l-m:802');
                    console.log("error "+ responseData.responseText);
					$("#badgesubscriptions-Process_CC").show();
                },
            });
        });

        $("#renew_btn").click(function (e) {
            e.preventDefault();
            document.getElementById("renew_btn").disabled = true;
            document.getElementById("mem_update_btn").disabled = true;

            var $form = $("#badgeUpdate"),
            data = $form.data("yiiActiveForm");
            $.each(data.attributes, function () {
                this.status = 3;
            });
            $form.yiiActiveForm("validate");

            var $formR = $("#form_badge_renew"),
            dataR = $formR.data("yiiActiveForm");
            $.each(dataR.attributes, function () {
                this.status = 3;
            });
            $formR.yiiActiveForm("validate");

            if ($("#badgeUpdate").find(".has-error").length || $("#form_badge_renew").find(".has-error").length) {
                document.getElementById("renew_btn").disabled = false;
                document.getElementById("mem_update_btn").disabled = false;
                alert("Please update errors in RED on Form");
                return false;
            }

            // Submit
            var badgeNumber = $("#badgesubscriptions-badge_number").val();
            var frm1_data = $("#badgeUpdate").serialize();
            var frm2_data = $("#form_badge_renew").serialize();

            $.ajax({
                type: "POST",
                url: "/badges/renew-membership?membership_id="+badgeNumber,
                data: frm1_data + "&" + frm2_data,
                cache: false,
                success: function(responseData, textStatus, jqXHR) {
                    console.log('renew success');
                    window.location = "/badges/view-renewal-history?badge_number="+badgeNumber;
                },

                error: function () {
                    $("#busy").hide('slow');
                    $("#div_busy").css({
                        'color': '#ff0000',
                        'font-weight': 'bold'
                    });
                    $("#div_busy").html('Request Error!!');
                    document.getElementById("renew_btn").disabled = false;
                    document.getElementById("mem_update_btn").disabled = false;
                }
            });
        });

        $("#form_badge_cert").on("beforeSubmit", function (event, messages) {
            var $form = $("#badgeUpdate"),
                data = $form.data("yiiActiveForm");
            $.each(data.attributes, function() {
                this.status = 3;
            });
            $form.yiiActiveForm("validate");
            if ($("#badgeUpdate").find(".has-error").length) {
                alert("Please update errors in RED on Form");
                return false;
            }

            var formData = $("#badgeUpdate").serializeArray();
            var ajaxRequestUrl = $("#badgeUpdate").attr('action');
            jQuery.ajax({
                method: 'POST',
                url:ajaxRequestUrl,
                crossDomain: false,
                data: formData,
                dataType: 'json',
                success: function(responseData, textStatus, jqXHR) {

                    if(responseData.status=='true') {
                        renewActionPermission = true;
                        var myCert = document.getElementById("badgecertification-certification_type");
                        var selectedText = myCert.options[myCert.selectedIndex].text;
                        $("#badges-remarks_temp").val(' * Add Cert: '+ selectedText);
                        //$("#form_badge_cert").submit();
                    } else {
                        console.log('Data Fail');
                    }
                },
                error: function (responseData, textStatus, errorThrown) {
                    console.log('Ajax error '+responseData.responseText);
                },
            });

        }).on('submit', function(e,messages){
            if ($("#badgeUpdate").find(".has-error").length) {
                e.preventDefault();
                return false;
            } else if  (renewActionPermission == false) {
                e.preventDefault();
                return false;
            }
        });

        function submitit(element) {
            $('#rebates-form').yiiActiveForm('submitForm')
        }

        var primaryRequest = $("#badges-primary").val();
        if(primaryRequest!=null || primaryRequest !=0) {
            getPrimaryBadger(primaryRequest,'init');
        }
        if(primaryRequest==0 || primaryRequest ==null) {
//            $("#primary-badge-summary").hide(500);
        }

        $("#badges-primary").change(function() {
            var primaryRequest = $("#badges-primary").val();
            if(primaryRequest!=null || primaryRequest !=0) {
                getPrimaryBadger(primaryRequest,'second');
            }
            else {
                //alert("error reporting");
            }
            if(primaryRequest==0 || primaryRequest ==null) {
//                family_badge_view('hide');
            }
        });

        $("#club-id").change(function() {
            $("#badges-club_id").val($("#club-id").val());
        });

        $("#badgesubscriptions-discount").change(function() {
            var badgeFee = parseInt($("#badgesubscriptions-badge_fee").val());
            var discount = parseInt($("#badgesubscriptions-discount").val());
            var amountDue = badgeFee - discount;
            if(amountDue<0) {
                amountDue = 0.00;
            }
            if(discount>badgeFee) {
                discount = badgeFee;
            }
            $("#badgesubscriptions-discount").val(parseFloat(Math.round(discount * 100) / 100).toFixed(2));
            $("#badgesubscriptions-amount_due").val(parseFloat(Math.round(amountDue * 100) / 100).toFixed(2));
        });

        family_badge_view('hide');
        $("div .field-primary-id").hide();

        var memTypeId = $("#badges-mem_type").val();
        if(memTypeId!='') {
            var badgeYear = $("#badgesubscriptions-expires").val();
            collectRenewFee('fill',memTypeId,badgeYear);
        } else { collectRenewFee('remove'); }

        if(memTypeId=='51') {
            family_badge_view('show');
        } else { family_badge_view('hide'); }

        $("#badges-mem_type").change(function() {
            var memTypeId = $("#badges-mem_type").val();
            var myYear = (new Date($("#defDate").val())).getFullYear();
            if(memTypeId == '99') {
                $("#badge_renual_form").hide();
                $("#badgesubscriptions-expires").val('Jan 31, '+(myYear+29));
                $("#badges-expires").val('Jan 31, '+(myYear+29));
                $("#badgesubscriptions-sticker").val('9999');
            } else if(memTypeId == '70') {
                $("#badge_renual_form").show();
                $("#badgesubscriptions-expires").val('Jan 31, '+(myYear+14));
                $("#badgesubscriptions-sticker").val('0');
                //$("#badges-expires").val('Jan 31, '+(myYear+15));
            } else {
                $("#badgesubscriptions-expires").val($("#defDate").val());
                $("#badgesubscriptions-sticker").val('');
                if($("#defDate").val() < $("#badges-expires").val()) {
                    $("#badges-expires").val($("#defDate").val()); }
                var sub_expires = new Date($("#badgesubscriptions-expires").val());
                var sub_exp = $("#badgesubscriptions-expires").val();
                var sell_date = $("#badges-sell_date").val();
                var check_date = new Date((parseInt(sub_exp.slice(-4))-1)+"-"+sell_date);
                if (sub_expires > check_date) {
                    $("#badge_renual_form").show(); }
            }

            run_waitMe('show');
            if(memTypeId=='51') {
                family_badge_view('show');
            } else { family_badge_view('hide'); }
            run_waitMe('hide');

            if(memTypeId!='') {
                if($("#badgesubscriptions-expires")) {
                    var badgeYear = $("#badgesubscriptions-expires").val();
                } else {
                    var badgeYear = $("#badges-expires").val();
                }
                collectRenewFee('fill',memTypeId,badgeYear);
            } else { collectRenewFee('remove'); }
        });

        document.getElementById('badges-phone').addEventListener('keyup',function(evt){
            var phoneNumber = document.getElementById('badges-phone');
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            phoneNumber.value = phoneFormat(phoneNumber.value);
        });

        document.getElementById('badges-phone_op').addEventListener('keyup',function(evt){
            var phoneNumber = document.getElementById('badges-phone_op');
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            phoneNumber.value = phoneFormat(phoneNumber.value);
        });

        document.getElementById('badges-ice_phone').addEventListener('keyup',function(evt){
            var phoneNumber = document.getElementById('badges-ice_phone');
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            phoneNumber.value = phoneFormat(phoneNumber.value);
        });

    });
});

<?php
}
if(strpos($_SERVER['REQUEST_URI'], 'work-credits')) {
?>

app.controller('WorkCreditFrom', function($scope) {
    var badgeNumber;
    badgeNumber =  $("#workcredits-badge_number").val();
    if((badgeNumber!='') && (badgeNumber!=0)) {
        changeBadgeName('fill',badgeNumber);
    }
    else {
        changeBadgeName('remove');
    }

    $("#workcredits-badge_number").change(function() {
        badgeNumber =  $("#workcredits-badge_number").val();
        if((badgeNumber!='') && (badgeNumber!=0)) {
            changeBadgeName('fill',badgeNumber);
        }
        else {
          changeBadgeName('remove');
        }
    });

    function changeBadgeName(action,badgeNumber) {
        $("#workcredits-badge_holder_name").readOnly = false;
        if(action=='fill') {
            jQuery.ajax({
                    method: 'GET',
                    url: '<?=yii::$app->params['rootUrl']?>/badges/get-badge-details?badge_number='+badgeNumber,
                    crossDomain: false,
                    success: function(responseData, textStatus, jqXHR) {
                        responseData =  JSON.parse(responseData);
                        console.log('trying ++'+responseData.first_name);
                        var PrimeExpTimestamp = getTimestamp(responseData.expires);
                        var resExpTimestamp = Math.floor(Date.now() / 1000);

                        if(PrimeExpTimestamp < resExpTimestamp) {
                            $("#workcredits-badge_holder_name").val('No Active Member Found');
                        } else {
                            $("#workcredits-badge_holder_name").val(responseData.first_name+' '+responseData.last_name);
                        }
                        //$("#workcredits-badge_holder_name").readOnly = true;
                    },
                    error: function (responseData, textStatus, errorThrown) {
                        $("#workcredits-badge_holder_name").val('Valid Badge holde not found');
                        console.log("fail "+responseData);
                    },
                });
        }
        else if(action=='remove') {
            $("#workcredits-badge_holder_name").val('');
        }
        $("#workcredits-badge_holder_name").readOnly = false;
    }


    $(".next-Credit").click(function(e) {
        e.preventDefault();
        jQuery.ajax({
            method: 'POST',
            url: '<?=yii::$app->params['rootUrl']?>/work-credits/sticky-form?type=true',
            crossDomain: false,
            success: function(responseData, textStatus, jqXHR) {
                $("form#creditEntryForm").submit();
            },
            error: function (responseData, textStatus, errorThrown) {
                console.log(responseData);
            },
        });
    });

    $(".done-Credit").click(function(e) {
        e.preventDefault();
        jQuery.ajax({
            method: 'GET',
            url: '<?=yii::$app->params['rootUrl']?>/work-credits/sticky-form?type=false',
            crossDomain: false,
            success: function(responseData, textStatus, jqXHR) {
                $("form#creditEntryForm").submit();
            },
            error: function (responseData, textStatus, errorThrown) {
                console.log(responseData);
            },
        });
    });


});

app.controller('WorkTransferForm', function($scope) {
   $(document).ready(function (e) {

        var badgeNumber = $("#cred_xfer-badge_number").val();
        if(badgeNumber=='') {
            changeBadgeName('remove');
            $("#credit-block_a").hide(500);
            $("#credit-block_b").hide(500);
        }

        var badgeNumber = $("#workcredits-badge_number").val();

        if(badgeNumber) {
            changeBadgeName(badgeNumber);
        }
    });

    $("#workcredits-badge_number").change(function() {
        var badgeNumber = $("#workcredits-badge_number").val();
        if(badgeNumber) {
            changeBadgeName(badgeNumber);
            $("#credit-block_a").show(500);
            $("#credit-block_b").show(500);
        } else {
            $("#credit-block_a").hide(500);
            $("#credit-block_b").hide(500);
            $("#cred_xfer-badge_name").val('');
        }
    });

    $("#cred_xfer-to_badge_number").change(function() {
        var toBadgeNumber = $("#cred_xfer-to_badge_number").val();

        if(toBadgeNumber!='') {
            changeBadgeNameTo(toBadgeNumber);
        }
        else {
           $("#cred_xfer-to_badge_name").val('');
        }
    });

    $("#cred_xfer-to_credits").change(function() {
        var toCredits = $("#cred_xfer-to_credits").val();
        if(toCredits>0){
            if($('input[name=wc-Radio]:checked').val()) {
                if($('input[name=wc-Radio]:checked').val() == 'this'){
                    if(toCredits > parseInt($("#cred_xfer-total_this").val())) {
                        //err too much
                        console.log(toCredits+" Process This TO MUCH");
                        $("#cred_xfer-to_credits").val('');
                        sweetAlert("Sorry...", "Trying to give to many credits!", "error");
                    } else {
                        console.log(toCredits+" Process This");
                    }

                } else if($('input[name=wc-Radio]:checked').val()=='last'){
                    if(toCredits > parseInt($("#cred_xfer-total_last").val())) {
                        //err too much
                        console.log(toCredits+" Process This TO MUCH");
                        $("#cred_xfer-to_credits").val('');
                        sweetAlert("Sorry...", "Trying to give to many credits!", "error");
                    } else {
                        console.log(toCredits+" Process Last ");
                    }
                }
            } else {
                sweetAlert("Almost...", "Please pick a year to transfer from!", "error");
                $("#cred_xfer-to_credits").val('');
            }
        }
    });

    $('input[name=wc-Radio]').change(function() {
        $("#cred_xfer-to_credits").val('');
    });

    function changeBadgeName(badgeNumber) {
        jQuery.ajax({
            method: 'GET',
            url: '<?=yii::$app->params['rootUrl']?>/badges/get-badge-details?badge_number='+badgeNumber,
            crossDomain: false,
            success: function(responseData, textStatus, jqXHR) {
                if(responseData) {
                    responseData = JSON.parse(responseData);
                    console.log(responseData);
                    var myEle = document.getElementById("work-credits-form");
                    if(myEle){
                        console.log("Add Work Credits");
                        $("#workcredits-badge_name").val(responseData.first_name+" "+responseData.last_name);
                    } else {
                        console.log("Transfer Work Credits");
                        $("#cred_xfer-badge_name").val(responseData.first_name+" "+responseData.last_name);
                        document.getElementById("cur_year_label").innerHTML = responseData.wcCurYr+ " Credits Available";
                        document.getElementById("las_year_label").innerHTML = responseData.wcLasYr+ " Credits Available";
                        document.getElementById("cred_xfer-total_this").readOnly = false;
                        document.getElementById("cred_xfer-total_last").readOnly = false;
                        $("#cred_xfer-total_this").val(responseData.wcCurHr);
                        $("#cred_xfer-total_last").val(responseData.wcLasHr);
                        document.getElementById("cred_xfer-total_this").readOnly = true;
                        document.getElementById("cred_xfer-total_last").readOnly = true;
                        $("#cred_xfer-to_credits").val('');
                    }
                } else {
                    var myEle = document.getElementById("work-credits-form");
                    if(myEle){
                        $("#workcredits-badge_name").val('Not Found');
                    } else {
                        $("#cred_xfer-badge_name").val('Not Found');
                    }
                }
            },
            error: function (responseData, textStatus, errorThrown) {
                console.log("Error:" +responseData);
            },
        });
    }

    function changeBadgeNameTo(badgeNumber) {
        jQuery.ajax({
            method: 'GET',
            url: '<?=yii::$app->params['rootUrl']?>/badges/get-badge-details?badge_number='+badgeNumber,
            crossDomain: false,
            success: function(responseData, textStatus, jqXHR) {
                if(responseData) {
                    responseData = JSON.parse(responseData);
                    $("#cred_xfer-to_badge_name").val(responseData.first_name+' '+responseData.last_name);
                } else { $("#cred_xfer-to_badge_name").val('Not Found'); }
            },
            error: function (responseData, textStatus, errorThrown) {
                console.log("Error:" +responseData);
            },
        });
    }

});

app.controller('ImportWorkCredits', function($scope) {

});

<?php
}
if(strpos($_SERVER['REQUEST_URI'], 'club')) {
?>
app.controller('clubRosterpage', function($scope) {
    $(".btn-group").hide();

    $(".single-csv").click(function(event) {
        event.preventDefault();
        exportClubRoster($( "#clubs-club_id" ).val(), '','');
    });

    $(".all-csv").click(function(event) {
        event.preventDefault();
        if(confirm("Are you sure you want to Export for all Clubs ?")) {
            exportClubRoster('', '','');
        }
    });

    $(".xls-export").click(function(event) {
        event.preventDefault();
        exportClubRoster('', 'true','');
    });

    $(".email-csv").click(function(event) {
        event.preventDefault();
        exportClubRoster($( "#clubs-club_id" ).val(), '','true');
    });

    function exportClubRoster(club_id, isXls, doEmail) {
        $('#uploadingInfo').show();
        $.ajax({
            url: '<?=yii::$app->params['rootUrl']?>/clubs/badge-rosters?club_id='+club_id+"&email="+doEmail+"&isXls="+isXls,
            type: 'GET',
            success: function(data) {
                $('#uploadingInfo').hide();
                if(data) {
                    var obj = jQuery.parseJSON( data );
                    var htm='';
                    $.each(obj, function(name, filename) {
                        console.log('na: '+name+' fi: '+filename);
                        htm += "<div class='col-sm-6'><div class='form-group'><a href='/files/rosters/"+filename+"'>"+name+"</a></div></div>";
                    });
                    roster_report.insertAdjacentHTML('afterbegin', htm);
                } else {
                    $("#modal-header").html('<h2 style="color: #a94442;">Error !</h2>');
                    $('#w0').modal('toggle');
                    $("#ajaxResult").html("<p>Sorry something went wrong, Please try again.</p>");
                }
            },
            error: function (responseData, textStatus, errorThrown) {
                console.log("Error:" +JSON.stringify(responseData));
                $('#uploadingInfo').hide();
            }
        });
    }
});

<?php
}
if(strpos($_SERVER['REQUEST_URI'], 'guest')) {
?>

app.controller('GuestFrom', function($scope) {
    // Prevent Enter from Submitting Forms, Must Click Buttons.
    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            var el = document.activeElement;
            if (el.tagName.toLowerCase() != 'textarea') {
                event.preventDefault();
                return false;
            }
        }
    });

    $('#guest-g_zip').keyup(function(e) {
        zipcode = $("#guest-g_zip").val();
        if(zipcode.length==5) {
			document.getElementById("zip_check").classList.remove('fa-thumbs-down');
			document.getElementById("zip_check").classList.remove('fa-thumbs-up');
			document.getElementById("zip_check").classList.add('fa-cogs');
			
            console.log('Using '+zipcode);
            jQuery.ajax({
                method: 'GET',
                url: '<?=yii::$app->params['rootUrl']?>/badges/api-zip?zip='+zipcode,
                crossDomain: false,
                async: true,
                success: function(responseData, textStatus, jqXHR) {
                    if(responseData.indexOf("rror")>0) {
						
						document.getElementById("zip_check").classList.remove('fa-cogs');
						document.getElementById("zip_check").classList.add('fa-thumbs-down');
								
					} else {
                        responseData = JSON.parse(responseData);
                        $mycity=responseData.City.toProperCase()
                        $("#guest-g_city").val($mycity);
                        $("#guest-g_state").val(responseData.State);
						document.getElementById("zip_check").classList.remove('fa-cogs');
						document.getElementById("zip_check").classList.add('fa-thumbs-up');
                    }
                },
                error: function (responseData, textStatus, errorThrown) {
					document.getElementById("zip_check").classList.remove('fa-cogs');
					document.getElementById("zip_check").classList.add('fa-thumbs-down');
					console.log(responseData);
                },
            });
        }
    });
});

<?php
}
if(strpos($_SERVER['REQUEST_URI'], 'violations/')) {
?>

app.controller('ViolationsRecFrom', function($scope) {
    $(document).ready(function (e) {
        $("#violations-vi_rules").chosen({placeholder_text_multiple:'Select Violations',width: "100%"}).change(function() {
            var cur_rules = $("#violations-vi_rules").val().toString();

            if(cur_rules) {
                if(cur_rules.indexOf(",") > 0) {
                    spl_rules = cur_rules.split(",");

                    var arrayLength = spl_rules.length;
                    var lvl=0;
                    for (var i = 0; i < arrayLength; i++) {
                        if (spl_rules[i].slice(-1) > lvl) {lvl = spl_rules[i].slice(-1);};
                    }
                    $("#violations-vi_type").val(lvl);

                } else {
                    $("#violations-vi_type").val(cur_rules.slice(-1));
                }
            } else {
                $("#violations-vi_type").val('1'); }
        });

        var rep_badge=$("#violations-badge_reporter").val();
        if (rep_badge) { getReporterName(rep_badge,'reporter_name'); }

        var rep_badge=$("#violations-badge_involved").val();
        if (rep_badge) { getReporterName(rep_badge,'involved_name'); }

        var rep_badge=$("#violations-badge_witness").val();
        if (rep_badge) { getReporterName(rep_badge,'witness_name'); }

    });

    $("#violations-badge_reporter").change(function() {
        var rep_badge=$("#violations-badge_reporter").val();
        if (rep_badge) { getReporterName(rep_badge,'reporter_name'); }
    });

    $("#violations-badge_involved").change(function() {
        var rep_badge=$("#violations-badge_involved").val();
        if (rep_badge) {
			document.getElementById("warm").src = "/files/badge_photos/"+("0000"+rep_badge).slice(-5)+".jpg"; //?dummy="+Math.random();
			var nam = getReporterName(rep_badge,'involved_name'); 
			document.getElementById("warm").alt = nam;
		}
    });

    $("#violations-badge_witness").change(function() {
        var rep_badge=$("#violations-badge_witness").val();
        if (rep_badge) { getReporterName(rep_badge,'witness_name'); }
    });

    function getReporterName(badgeNumber,field_name) {
        jQuery.ajax({
            method: 'GET',
            url: '<?=yii::$app->params['rootUrl']?>/badges/get-badge-details?badge_number='+badgeNumber,
            crossDomain: false,
            success: function(responseData, textStatus, jqXHR) {
                if(responseData) {
                    responseData = JSON.parse(responseData);
                    $("#violations-"+field_name).val(responseData.first_name+' '+responseData.last_name);
					return responseData.first_name+' '+responseData.last_name;
                } else { $("#violations-"+field_name).val('Not Found'); }
            },
            error: function (responseData, textStatus, errorThrown) {
                console.log("Error:" +responseData);
            },
        });
    };

});

<?php
}
  ?>

app.controller('BadgesDatabaseController', function($scope) {
    $("#badgesdatabase-clubs").chosen({placeholder_text_multiple:'Choose Clubs',width: "100%"});
});

app.controller('PostPrintTransactionForm', function($scope) {
    $(".btn-group").hide();
});

app.controller('ViolationsReport', function($scope) {
    $(".btn-group").hide();
});

$( document ).ready(function() {

    $("#badgessearch-expire_condition").change(function() {
        $("#viewPrintbadgeFilter").submit();
    });

    $("#badgessearch-club_id").change(function()  {
        $("#viewPrintbadgeFilter").submit();
    });

     $("#badgessearch-status").change(function()  {
        $("#viewPrintbadgeFilter").submit();
    });

    $("#guestsearch-atRange_condition").change(function() {
        $("#viewPrintGuestFilter").submit();
    });

    $("#badgesrostersearch-club_id_dummy").change(function() {
        $("#badgeRosterFormFilter").submit();
    });

    $("#BadgeRosterAll").click(function(event) {
        event.preventDefault();
        $("#badgesrostersearch-club_id_dummy").val('');
        $("#badgesrostersearch-is_all").val('true');
        $("#badgeRosterFormFilter").submit();
    });

    $("#customExportBtn").click(function(event) {
        event.preventDefault();
        $("#w0-xlsx").click();
    })

    $("#customExportCsv").click(function(event) {
        event.preventDefault();
        $("#w0-csv").click();
    })


<?php if(isset($_SESSION['timeout'])) { ?>
    var aTags = document.getElementsByTagName("a");
    var searchText = "Clock";
    var foundTag;
    for (var i = 0; i < aTags.length; i++) {
      if (aTags[i].textContent == searchText) {
        foundTag = aTags[i];
        break;
      }
    }

    // Set the date we're counting down to
    logsout = new Date();
    logsout.setMinutes(logsout.getMinutes() + <?=$_SESSION['timeout']?>);

    // Update the count down every 1 second
    var x = setInterval(function() {
      // Get todays date and time
      var now = new Date().getTime();

      // Find the distance between now an the count down date
      var distance = logsout - now;

      // Time calculations for days, hours, minutes and seconds
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

      // Display the result in the element with id="CountDownTimer"
      foundTag.innerHTML = minutes + ":" + ("00" + seconds).slice(-2);

      // If the count down is finished, write some text
      if (distance < 0) {
        var y = document.getElementsByClassName('logout');
        var logmeout = y[0];
        logmeout.click();
      }
    }, 1000);
<?php }?>

});
</script>
</body>
</html>
<?php $this->endPage() ?>
