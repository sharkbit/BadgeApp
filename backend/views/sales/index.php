<?php
use backend\models\Guest;
use backend\models\Params;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\sales */

$this->title = 'Store';
$this->params['breadcrumbs'][] = ['label'=>$this->title, 'url'=>['/sales']];

$confParams  = Params::findOne('1');

$is_dev=false;
if(yii::$app->controller->hasPermission('sales/all')) {
	$myList=['cash'=>'Cash','check'=>'Check','online'=>'On Line','other'=>'Other'];
	$pgLimited=false;
} else {
	$myList=[];
	$pgLimited=true;
	if(is_null($model->badge_number)) {
		$_REQUEST['badge']=$_SESSION['badge_number'];
	}
}

if(yii::$app->controller->hasPermission('payment/charge') && (strlen($confParams->conv_p_pin)>2 || strlen($confParams->conv_d_pin)>2))  {
	if(Yii::$app->params['env'] == 'prod') {
		$myList= array_merge($myList,['creditnow'=>'Credit Card Now!']);
	} else { $myList= array_merge($myList,['creditnow'=>'TEST CC (Do not use)']); $is_dev=true;}
}
if(yii::$app->controller->hasPermission('payment/charge') && (strlen($confParams->pp_id)>2 || strlen($confParams->pp_sec)>2))  {
	$myList= array_merge($myList,['paypal'=>'PayPal']);
}

// If post from Guest page Charge for All unprecessed Guests
if (isset($_REQUEST['badge'])) {
	$model->badge_number=$_REQUEST['badge'];
	echo '<input type="hidden" id="m_bn" value="'.$model->badge_number.'">'.PHP_EOL;

	$sql="SELECT count(*) as cnt from guest WHERE badge_number=".$model->badge_number." AND (g_paid='a' or g_paid ='h' or g_paid='0');";
	$guest_count = Yii::$app->getDb()->createCommand($sql)->queryAll();
	$guest_total = $guest_count[0]['cnt'];

	if (isset($_REQUEST['id'])) {
	//	yii::$app->controller->createLog(false, 'trex_C_S:36', var_export($_REQUEST,true));
		$pay_guest = Guest::find()->where( ['id'=>$_REQUEST['id'] ] )->one();
		echo '<input type="hidden" id="v_First" value="'.$pay_guest->g_first_name.'">'.PHP_EOL;
		echo '<input type="hidden" id="v_Last" value="'.$pay_guest->g_last_name.'">'.PHP_EOL;
		echo '<input type="hidden" id="v_City" value="'.$pay_guest->g_city.'">'.PHP_EOL;
		echo '<input type="hidden" id="v_State" value="'.$pay_guest->g_state.'">'.PHP_EOL;

		if ($pay_guest->g_paid=='a') {
			$model->payment_method='cash';
		} else if ($pay_guest->g_paid=='h') {
			$model->payment_method='check';
		}
	}
} else {$guest_total=0;}

$curYr = date('Y',strtotime(yii::$app->controller->getNowTime()));
$ccYear = range($curYr,$curYr+25);

echo $this->render('_view-tab-menu').PHP_EOL; ?>

<?php $form = ActiveForm::begin([ 'id'=>'SalesForm' ]); ?>
<style type="text/css"> .right { text-align:right; } </style>
<div class="sales-update">

   <!-- <h3><?= Html::encode($this->title) ?></h3> -->
   <div class="help-block" ></div>

	<div class="row">
		<div class="col-sm-6"><br />
			<div class="row">
				<div class="col-sm-3">
				<?php echo Html::checkbox('sales-ForGuest' ,'',['id'=>'sales-ForGuest']), PHP_EOL; ?> For a Guest?
				<?= $form->field($model, 'pgLimited')->hiddenInput(['id'=>'pgLimited','value'=>$pgLimited])->label(false).PHP_EOL; ?>
				</div>
				<div class="col-sm-3" id="div_PayCash" style="display:none">
				<?php echo Html::checkbox('sales-PayCash' ,'',['id'=>'sales-PayCash']), PHP_EOL; ?> Paying Cash?
				</div>
			</div>
			<div class="row">
				<div class="col-sm-3">
					<?= $form->field($model, 'badge_number')->textInput(['id'=>'sales-badge_number','readOnly'=>yii::$app->controller->hasPermission('sales/all') ? false : true]).PHP_EOL; ?>
				</div>
				<div class="col-sm-4">
					<?= $form->field($model, 'first_name')->textInput(['id'=>'sales-first_name','readOnly'=>yii::$app->controller->hasPermission('badges/rename') ? false : true]).PHP_EOL; ?>
				</div>
				<div class="col-sm-4">
					<?= $form->field($model, 'last_name')->textInput(['id'=>'sales-last_name','readOnly'=>yii::$app->controller->hasPermission('badges/rename') ? false : true]).PHP_EOL; ?>
				</div>
				<div class="col-sm-8">
					<?= $form->field($model, 'address')->textInput(['id'=>'sales-address']).PHP_EOL; ?>
				</div>
				<div class="col-sm-3">
					<?= $form->field($model, 'zip')->textInput(['id'=>'sales-zip']).PHP_EOL; ?>
				</div>
				<div class="col-sm-4">
					<?= $form->field($model, 'city')->textInput(['id'=>'sales-city']).PHP_EOL; ?>
				</div>
				<div class="col-sm-3">
					<?= $form->field($model, 'state')->textInput(['id'=>'sales-state']).PHP_EOL; ?>
				</div>
				<div class="col-sm-7">
					<?= $form->field($model, 'email')->textInput(['id'=>'sales-email']).PHP_EOL; ?>
				</div>
				<div class="col-xs-2 col-sm-1" id="email_check">
					<br /><i class="fa fa-thumbs-down" title="Email Not Verified"></i>
				</div>
			</div>
			<div class="help-block" ></div>
			<hr>
			<div class="row">
				<div class="col-sm-6">
					<?= $form->field($model, 'tax')->textInput(['readonly'=>true]).PHP_EOL; ?>
					<?= $form->field($model, 'cart')->hiddenInput(['id'=>'sales-cart'])->label(false).PHP_EOL; ?>
					<div id='totals'> </div>
				</div>
				<div class="col-sm-6">
					<?= $form->field($model, 'total')->textInput(['id'=>'sales-total','readonly'=>($pgLimited)?true : (($is_dev)?false : true)]).PHP_EOL; ?>
				</div>
			</div>
			<div class="help-block" ></div>
			<div class="row">
				<div class="col-sm-6">
				<?php if($is_dev) {?>
					Test Visa: 4159288888888882<br>Test ammounts:
					<table>
					<tr><td>$X.13 </td> <td> - Amount Error</td></tr>
					<tr><td>$X.19 </td> <td> - Decline</td></tr>
					<tr><td>$X.34 </td> <td> - Expired Card</td></tr>
					<tr><td>$X.41 </td> <td> - Pick Up Card</td></tr>
					<tr><td>$X.41 </td> <td> - Expired Card</td></tr>
					</table>
				<a href='https://developer.elavon.com/na/docs/commerce-sdk/1.0.0/test-cards' target=cc_number > Cards</a> -
				<a href="https://developer.elavon.com/na/docs/viaconex/1.0.0/integration-guide/api_reference/cvv2_cvc2_cid_response" target=cc_info >Other Test Codes</a>
				<?php } ?>
				</div>
				<div class="col-sm-6">
				<?= $form->field($model, 'payment_method')->dropdownList($myList,['id'=>'sales-payment_method','class'=>'form-control','prompt'=>'Payment Type'])?>
				<div id="paypal_form_div" style="display:none">
					<?= Html::Button('<i class="fa fa-credit-card"> Pay using PayPal</i>', ['id'=>'sales-Process_paypal','class' => 'btn btn-primary']), PHP_EOL ?>

				</div>
				<div id="cc_form_div" style="display:none">

					<div class="col-xs-12 col-sm-12">
						<div class="help-block" ></div>
						<?= $form->field($model, 'cc_num')->textInput(['id'=>'sales-cc_num']).PHP_EOL; ?>
						</div>
					<div class="col-xs-4 col-sm-4">
						<div class="help-block" ></div>
						<?= $form->field($model, 'cc_exp_mo')->dropDownList(['01'=>'01 Jan','02'=>'02 Feb','03'=>'03 Mar','04'=>'04 Apr','05'=>'05 May','06'=>'06 Jun','07'=>'07 Jul','08'=>'08 Aug','09'=>'09 Sep','10'=>'10 Oct','11'=>'11 Nov','12'=>'12 Dec'],['style'=>'padding:2px; min-width: 20px;']) ?>
						</div>
					<div class="col-xs-5 col-sm-5">
						<div class="help-block" ></div>
						<?= $form->field($model, 'cc_exp_yr')->dropDownList($ccYear,['style'=>'padding:2px;']) ?>
					</div>
					<div class="col-xs-3 col-sm-3">
						<div class="help-block" ></div>
						<?= $form->field($model, 'cc_cvc')->textInput(['style'=>'padding:2px;']).PHP_EOL; ?>
						<?= $form->field($model, 'cc_x_id')->hiddenInput()->label(false).PHP_EOL; ?>
					</div>
					<div class="col-xs-4 col-sm-4 form-group">
						<div class="help-block" ></div>
						<?= Html::Button('<i class="fa fa-credit-card"> Process</i>', ['id'=>'sales-Process_CC','class' => 'btn btn-danger']), PHP_EOL ?>
					</div>

				</div>

				<div class="col-xs-12">
					<p id="cc_info"> </p>
				</div>
				</div>
				<div class="help-block" ></div>

			</div>
			<div class="row">
				<div class="help-block" ></div>
				<div class="col-sm-12" id="HideMySubmit">
				<?= Html::submitButton('Purchase <i class="fa fa-dollar"> </i>',['disabled'=> true,'id'=>'sales-pur','class' => 'btn btn-primary pull-right']) ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<p class="pull-right"><a href="" class="badge_sales_div" > Extras </a></p>
			<div class="form-group" id="extras_sales_div" > <!-- style="display:none"  class="col-xs-12 col-sm-12"  -->
			<table id='sales_items' border=1 width="100%">
			<tr><th>Item</th><th>Stock</th><th>Ea</th><th>Qty #</th><th>Price</th></tr>
	<?php
		$sql="SELECT s1.item_id,s2.item AS cat,s1.item,s1.sku,s1.stock,s1.price,s1.tax_rate,s1.`type`".
			" FROM store_items AS s1 JOIN store_items AS s2 ON (s1.paren=s2.item_id)".
			" WHERE s1.active=1".
			" ORDER BY `cat`,`type`,item;";
			$command = Yii::$app->db->createCommand($sql);
			$ItemsList = $command->queryAll();
			$curCat='';
			foreach($ItemsList as $item){
				$guest_note='';
				if($curCat <> $item['cat']) {
					echo "<tr><td colspan=4><b>".$item['cat'].":</b></td></tr>"; $curCat = $item['cat'];
				}
				$colo ="bgcolor='#f3f3f3'";
				$item_qty =	'<input class="right" type="text" name="qty" size="3" value=0 onKeyUp="doCalcSale()" />';
				if ($item['sku']== $confParams->guest_sku ) {
					if ($guest_total>0) {
						$item_qty =	'<input class="right" type="text" name="qty" size="3" value='.$guest_total.' onKeyUp="doCalcSale()" />';
					} else {
						$item_qty='';
						$guest_note=' - <a href="/guest/index">  <B>Missing Guest Count</a>';
					}
				}

				if((int)$item['stock'] > 0) { $item_stock='<center>'.(int)$item['stock'].'</center>'; } else { $item_stock=''; }

				echo '<tr '.$colo.">\n\t<td>".'<input type="hidden" name="item" value="'.htmlspecialchars($item['item']).'" />'.$item['item'].$guest_note.
					"\n\t".'<input type=hidden name="sku" value="'.$item['sku'].'" />'.
					"\n\t".'<input type=hidden name="tax_rate" value="'.$item['tax_rate'].'" /></td>'.
					"\n\t".'<td>'.$item_stock.' </td>'.
					"\n\t".'<td><input class="right" type="text" name="ea" size="3" value='.$item['price'].' disabled /></td>'.
					"\n\t".'<td>'.$item_qty.'</td>'.
					"\n\t".'<td><input class="right" type="text" name="price" size="3" readonly /></td></tr>'."\n";
			} ?>
			</table>
			</div>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>

<script>
	doCalcSale();

	function doCalcSale() {
		var ContainerID = document.getElementById('sales_items');

		var arrItem = new Array();
		var arrSku = new Array();
		var arrEa = new Array();
		var arrQty = new Array();
		var arrTax = new Array();
		var arrPrice = new Array();
		var cart =  new Array();
		var ItemTotal = 0; var TaxTotal = 0; var TotalTotal = 0;
		var ContainerIDElements = new Array( 'input');

		for( var i = 0; i < ContainerIDElements.length; i++ ){
			els = ContainerID.getElementsByTagName( ContainerIDElements[i] );
			for( var j = 0; j < els.length; j++ ){
				if(els[j].name == 'item') arrItem.push(els[j]);
				if(els[j].name == 'sku') arrSku.push(els[j]);
				if(els[j].name == 'ea') arrEa.push(els[j]);
				if(els[j].name == 'tax_rate') arrTax.push(els[j]);
				if(els[j].name == 'qty') arrQty.push(els[j]);
				if(els[j].name == 'price') arrPrice.push(els[j]);
			}
		}

		for( var j = 0; j < arrEa.length; j++ ) {
			if(Number(arrQty[j].value)>0) {
				var itto = parseFloat(Math.round((Number(arrEa[j].value) * Number(arrQty[j].value)) * 100) / 100).toFixed(2);
				var tato = parseFloat(Math.round((Number(arrEa[j].value) * Number(arrQty[j].value) * Number(arrTax[j].value)) * 100) / 100).toFixed(2);
				arrPrice[j].value = parseFloat(Number(itto) + Number(tato)).toFixed(2);
				ItemTotal += Number(itto);
				TaxTotal += Number(tato);
				TotalTotal += Number(arrPrice[j].value);
				var item = { "item":arrItem[j].value, "sku":arrSku[j].value, "ea":arrEa[j].value, "qty":arrQty[j].value, "price":arrPrice[j].value };
				cart.push(item);
			} else { arrPrice[j].value=null; }
		}
		if(TotalTotal > 0) { document.getElementById("sales-pur").disabled = false; } else { document.getElementById("sales-pur").disabled = true; }
		$("#sales-cart").val(JSON.stringify(cart));
		$("#sales-tax").val(TaxTotal.toFixed(2));
		$("#sales-total").val(parseFloat(Math.round(TotalTotal * 100) / 100).toFixed(2));
		$("#totals").html('<table border=1 style="width:150px"><tr><td>Item Total:</td><td class="right"> '+ItemTotal.toFixed(2)+' </td></tr>'+
			'<tr><td> Tax Total: </td><td class="right">'+TaxTotal.toFixed(2)+'</td></tr><tr><td> Grand Total: </td><td class="right"> <b>'+ TotalTotal.toFixed(2)+"</b></td></tr></table>");
		console.log('Item Total: '+ItemTotal.toFixed(2)+', Tax Total: '+TaxTotal.toFixed(2)+', Grand total: '+ TotalTotal.toFixed(2));
		console.log(cart);
	}

	function getReporterName(badgeNumber) {
        jQuery.ajax({
            method: 'GET',
            url: '<?=yii::$app->params['rootUrl']?>/badges/get-badge-details?badge_number='+badgeNumber,
            crossDomain: false,
            success: function(responseData, textStatus, jqXHR) {
                if(responseData) {
                    responseData = JSON.parse(responseData);
                    $("#sales-first_name").val(responseData.first_name);
					$("#sales-last_name").val(responseData.last_name);
                    $("#sales-city").val(responseData.city);
					$("#sales-state").val(responseData.state);
                    $("#sales-zip").val(responseData.zip);
					$("#sales-address").val(responseData.address);
					$("#sales-email").val(responseData.email);
					if(responseData.email_vrfy==1) {
						$("#email_check").html('<br /><i class="fa fa-thumbs-up" title="Email Verified"></i>');
					} else {
						$("#email_check").html('<br /><i class="fa fa-thumbs-down" title="Email Not Verified"></i>');
					}
				} else { $("#sales-f_name").val('Not Found'); }
            },
            error: function (responseData, textStatus, errorThrown) {
                console.log("Error:" +responseData);
            },
        });
    };

    document.getElementById('sales-cc_num').addEventListener('keyup',function(evt){
        var ccnum = document.getElementById('sales-cc_num');
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        ccnum.value = ccFormat(ccnum.value);
    });

	$("#sales-payment_method").change(function(e) {
        var pay_meth = document.getElementById("sales-payment_method");
        var selectedVal = pay_meth.options[pay_meth.selectedIndex].value;
        if(selectedVal=="creditnow") {
            $("#cc_form_div").show();
			$("#paypal_form_div").hide();
            $("#HideMySubmit").hide();
		} else if (selectedVal=="paypal") {
			$("#cc_form_div").hide();
			$("#paypal_form_div").show();
			$("#HideMySubmit").hide();
        } else {
            $("#cc_form_div").hide();
			$("#paypal_form_div").hide();
            $("#HideMySubmit").show();
        }
    });

    $("#sales-badge_number").change(function() {
        var sales_badge=$("#sales-badge_number").val();
        if (sales_badge) { getReporterName(sales_badge); }
    });

	$("#sales-Process_CC").click(function(e) {
		e.preventDefault();
		 $("#sales-Process_CC").hide();
		if ($("#sales-badge_number").val() > 0) {
			$("p#cc_info").html("Processing...");

			var formData = $("#SalesForm").serializeArray();
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
							$("#sales-cc_x_id").val(responseData.message.id);
							$("#HideMySubmit").show();
							$("#sales-cc_num").val(responseData.message.cardNum);
						} else {
							$("p#cc_info").html( "Card: "+ responseData.message);
							$("#sales-Process_CC").show();
						}
					} else {
						console.log("Data error " + JSON.stringify(responseData));
						SwipeError(JSON.stringify(responseData.responseText),'b-v-l-m:532');
						$("p#cc_info").html(responseData.message);
						$("#sales-Process_CC").show();
					}

				},
				error: function (responseData, textStatus, errorThrown) {
					$("p#cc_info").html("PHP error:<br>"+responseData.message);
                    SwipeError(JSON.stringify(responseData.responseText),'b-v-l-m:532');
                    console.log("error "+ responseData.responseText);
					$("#sales-Process_CC").show();
				},
			});
		} else {
			$("p#cc_info").html('Badge number issue!  If non-badge member, Please Check box for guest.');
			$("#sales-Process_CC").show();
		}
		$("#sales-Process_CC").prop('disabled', false);
	});

	$("#sales-Process_paypal").click(function() {
		document.getElementById("sales-Process_paypal").disabled=true;
		if ($("#sales-badge_number").val() > 0) {
			$("p#cc_info").html("Processing...");

		form=document.getElementById('SalesForm');

		} else {
			$("p#cc_info").html('Badge number issue!  If non-badge member, Please Check box for guest.');
		}
		document.getElementById("sales-Process_paypal").disabled=false;
	});

	$('#sales-zip').keyup(function(e) {
        zipcode = $("#sales-zip").val();
        if(zipcode.length==5) {
            console.log('Using '+zipcode);
            jQuery.ajax({
                method: 'GET',
                url: '<?=yii::$app->params['rootUrl']?>/badges/api-zip?zip='+zipcode,
                crossDomain: false,
                async: true,
                success: function(responseData, textStatus, jqXHR) {
                    responseData = JSON.parse(responseData);
                    $mycity=responseData.City.toProperCase()
                    $("#sales-city").val($mycity);
                    $("#sales-state").val(responseData.State);
                },
                error: function (responseData, textStatus, errorThrown) {
                    console.log(responseData);
                },
            });
        }
    });

    $(document).ready(function (e) {
        var sales_badge=$("#sales-badge_number").val();
        if (sales_badge) { getReporterName(sales_badge); }
    });

	$("#sales-ForGuest").change(function() {
        if (document.getElementById("sales-ForGuest").checked == true){
			$("#div_PayCash").show();
			$("#sales-badge_number").val('99999');
			document.getElementById("sales-badge_number").readOnly  = true;
			document.getElementById("sales-first_name").readOnly  = false;
			document.getElementById("sales-last_name").readOnly  = false;
			var elementExists = document.getElementById("v_First");
			if (typeof(elementExists) != 'undefined' && elementExists != null) {
			document.getElementById("sales-first_name").value  = document.getElementById("v_First").value;
			document.getElementById("sales-last_name").value  = document.getElementById("v_Last").value;
			document.getElementById("sales-city").value  = 		document.getElementById("v_City").value;
			document.getElementById("sales-state").value  = 	document.getElementById("v_State").value;
			} else {
				document.getElementById("sales-first_name").value = '';
				document.getElementById("sales-last_name").value = '';
				document.getElementById("sales-city").value = '';
				document.getElementById("sales-state").value = '';
			}
			document.getElementById("sales-zip").value  = '';
			document.getElementById("sales-address").value  = '';
			document.getElementById("sales-email").value  = '';
		} else {
			document.getElementById("sales-PayCash").checked = false;
			$("#div_PayCash").hide();
			if (document.getElementById("pgLimited").value == true){
				document.getElementById("sales-badge_number").value = document.getElementById("m_bn").value;
			} else {
				document.getElementById("sales-badge_number").readOnly  = false;
			}
			document.getElementById("sales-first_name").readOnly  = true;
			document.getElementById("sales-last_name").readOnly  = true;
			document.getElementById("sales-first_name").value  = '';
			document.getElementById("sales-last_name").value  = '';
			document.getElementById("sales-city").value  = '';
			document.getElementById("sales-state").value  = '';
			document.getElementById("sales-zip").value  = '';
			document.getElementById("sales-address").value  = '';
			document.getElementById("sales-email").value  = '';
			var elementExists = document.getElementById("m_bn");
			if (typeof(elementExists) != 'undefined' && elementExists != null) {
			    sales_badge=document.getElementById("m_bn").value
			    $("#sales-badge_number").val(sales_badge);
			    getReporterName(sales_badge);
		    }
		}
    });

	$("#sales-PayCash").change(function() {
		if (document.getElementById("sales-PayCash").checked == true){
				document.getElementById("sales-payment_method").value  = 'cash';
				getReporterName(99999);
			} else {
				document.getElementById("sales-first_name").value  = '';
				document.getElementById("sales-last_name").value  = '';
				document.getElementById("sales-city").value  = '';
				document.getElementById("sales-state").value  = '';
				document.getElementById("sales-zip").value  = '';
				document.getElementById("sales-address").value  = '';
				document.getElementById("sales-email").value  = '';
			}
	});

	function ProcessSwipe(cleanUPC) {

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
			document.getElementById("sales-first_name").value = FName+' '+MName;
			document.getElementById("sales-last_name").value = LName;


			if (cleanUPC.indexOf('DAG') > 0) {  //Parse Address
			  var fAddr = cleanUPC.indexOf('DAG')+3;
			  var lAddr = cleanUPC.indexOf("ArrowDown",fAddr);
			  var Addr = cleanUPC.slice(fAddr,lAddr);
			  Addr = titleCase(Addr);
              console.log("Addr: "+Addr);
			  document.getElementById("sales-address").value = Addr;
			}

			if (cleanUPC.indexOf('DAI') > 0) {  //Parse City
			  var fCty = cleanUPC.indexOf('DAI')+3;
			  var lCty = cleanUPC.indexOf("ArrowDown",fCty);
			  var Cty = cleanUPC.slice(fCty,lCty);
			  Cty = titleCase(Cty);
              console.log("City: "+Cty);
			  document.getElementById("sales-city").value = Cty;
			}

			if (cleanUPC.indexOf('DAJ') > 0) {  //Parse State
			  var fST = cleanUPC.indexOf('DAJ')+3;
			  var lST = cleanUPC.indexOf("ArrowDown",fST);
			  var Stat = cleanUPC.slice(fST,lST);
              console.log("State: "+Stat);
			  document.getElementById("sales-state").value = Stat;
            }

			if (cleanUPC.indexOf('DAK') > 0) {  //Parse ZIP
			  var fZIP = cleanUPC.indexOf('DAK')+3;
			  var lZIP = cleanUPC.indexOf("ArrowDown",fZIP);
			  var ZIP = cleanUPC.slice(fZIP,lZIP);
			  ZIP = ZIP.substring(0,5);
              console.log("ZIP: "+ZIP);
			  document.getElementById("sales-zip").value = ZIP;
			}

		}
		else if (cleanUPC.match(/B\d{16}/g)) {  // Matched Credit Card!
			console.log('Credit Card Scanned: ', cleanUPC);
			var ccNum = cleanUPC.substring(1,17);
			var fExp = cleanUPC.indexOf('^')+1;
			var fExp = cleanUPC.indexOf('^',fExp)+1;
			var ExpYr = cleanUPC.substring(fExp,fExp+2);
			var ExpMo = cleanUPC.substring(fExp+2,fExp+4);
			ccNum = ccFormat(ccNum);
			console.log("Num: "+ccNum+" Exp Yr: "+ExpYr+" Exp Mo: "+ExpMo);
			ExpYr = ExpYr - (new Date().getFullYear().toString().substr(-2));

			document.getElementById("sales-cc_num").value = ccNum;
			document.getElementById("sales-cc_exp_mo").value = ExpMo;
			document.getElementById("sales-cc_exp_yr").value = ExpYr;
		} else { SwipeError(cleanUPC,'b-v-s-i:403'); }
		cleanUPC = '';
	};

</script>