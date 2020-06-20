<?php

use backend\models\Badges;
use backend\models\clubs;
use backend\models\BadgeCertification;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\models\Badges */

$this->title = $model->badge_number;
$this->params['breadcrumbs'][] = ['label' => 'Range Badges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$urlStatus = yii::$app->controller->getCurrentUrl();
?>
<div class="badges-view">
    <div class="row" >
        <div class="col-xs-12">

    <?= $this->render('_view-tab-menu',['model'=>$model]) ?>

            <h3>Badge Holder Details </h3>
            <div class="col-xs-12 col-sm-4 pull-right">
            <?php if($model->qrcode!=null || $model->qrcode !='')  {
				if(yii::$app->controller->hasPermission('badges/barcode')) {?>
			<div class="row"><center>
                <svg class="barcode"
                    jsbarcode-value="<?=$model->qrcode?>"
                    jsbarcode-textmargin="0"
					jsbarcode-format="CODE128">
                </svg>
                <script type="text/javascript">
                    JsBarcode(".barcode").init();
                </script></center>
				<barcode code="<?=$model->qrcode?>" type="C128A" class="barcode" size="0.8" />
			</div>
			<?php } ?>
			<div class="row"><center>
      <?php if(file_exists("files/badge_photos/".str_pad($model->badge_number, 5, '0', STR_PAD_LEFT).".jpg")) {
				echo "<img src='/files/badge_photos/".str_pad($model->badge_number, 5, '0', STR_PAD_LEFT).".jpg?dummy=".rand(10000,99999)."' alt='".$model->badge_number."' width='260' height='340'><br><br>";
				if(yii::$app->controller->hasPermission('badges/print')) {
					echo "<b><a href='/badges/print?badge_number=".$model->badge_number."' target='_blank'>[ <span class='glyphicon glyphicon-print'></span> Print ]</a></b>";
				}
				if(yii::$app->controller->hasPermission('badges/photo-add')) {
					echo "<b><a href='/badges/photo-add?badge=".$model->badge_number."'>[ <span class='glyphicon glyphicon-camera'></span> Update ]</a></b>";
					echo "<b><a href='/badges/photo-crop?badge=".$model->badge_number."'>[ <span class='glyphicon glyphicon-modal-window'></span> Crop ]</a></b>";
				}
				echo "<br /><br />\n";
				$sql="select privilege from user where badge_number=".$model->badge_number;
				$connection = Yii::$app->getDb();
				$command = $connection->createCommand($sql);
				$ChkPriv = $command->queryAll();
				if (isset($ChkPriv[0]['privilege'])) {
					if($ChkPriv[0]['privilege']==3) {
						echo "<b><a href='/badges/print?badge_number=".$model->badge_number."&ty=r' target='_blank'>[ <span class='glyphicon glyphicon-print'></span> RSO ]</a></b>";
					} elseif($ChkPriv[0]['privilege']==6) {
						echo "<b><a href='/badges/print?badge_number=".$model->badge_number."&ty=c' target='_blank'>[ <span class='glyphicon glyphicon-print'></span> CRSO ]</a></b>";
					} elseif($ChkPriv[0]['privilege']==8) {
						echo "<b><a href='/badges/print?badge_number=".$model->badge_number."&ty=i' target='_blank'>[ <span class='glyphicon glyphicon-print'></span> CIO ]</a></b>";
					} else {
						//echo  "<br /> No Special Badge for Priv:".$ChkPriv[0]['privilege'];
					}
				}

				if($model->membershipType->id==99 || $model->membershipType->id==70) {
					$cert = BadgeCertification::find()->where(['badge_number' => $model->badge_number])->one();
					if($cert) {
						echo "<b><a href='/badges/print?badge_number=".$model->badge_number."&ty=m' target='_blank'>[ <span class='glyphicon glyphicon-print'></span> Cert Badge ]</a></b>";
					}
				}

			} else {
				echo "<b> No photo Found ";
				if(yii::$app->controller->hasPermission('badges/photo-add')) {
					echo "<b><a href='/badges/photo-add?badge=".$model->badge_number."'>[ <span class='glyphicon glyphicon-camera'></span> Click to add ]</a></b>";
				}
				echo "</b>".PHP_EOL;
			}?></center>
            </div>
			</div>
				<?php } ?>

            <div class="col-xs-12 col-sm-8">
                <div class="block-badge-view">

                   <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
						[
							'attribute'=>'badge_number',
                            'value'=>function($model,$attribute) {
                                return str_pad($model->badge_number, 5, '0', STR_PAD_LEFT);
							}
						],
						[
							'attribute'=>'status',
							'value'=>function($model,$attribute) {
								return (new Badges)->getMemberStatus($model->status);}
						],
                        'prefix',
                        'first_name',
                        'last_name',
                        'suffix',
                        'address:ntext',
                        'city',
                        'state',
                        'zip',
                        [
                            'attribute'=>'gender',
                            'value'=> function($model, $attribute) {
                                if($model->gender==0) return 'Male'; else return 'Female';
                            },
                        ],
                        'yob',
						[
                            'attribute'=>'email',
							'format' => 'raw',
                            'value'=> function($model, $attribute) {
								if($model->email_vrfy) {
									$vrfy='<i class="fa fa-thumbs-up" title="Email Verified"></i>';}
									else {$vrfy='<i class="fa fa-thumbs-down" title="Email Not Verified"></i>';}
                                return $model->email. ' &nbsp '.$vrfy;
                            },
                        ],
                        //'email:email',



						[
							'attribute'=>'phone',
							'value'=> function($model, $attribute) {
								if ($model->phone) {$myPhone="(".substr($model->phone,0,3).") ".substr($model->phone,3,3)." - ".substr($model->phone,6,4);} else { $myPhone='';}
								return $myPhone;
							},
						],
						[
							'attribute'=>'phone_op',
							'value'=> function($model, $attribute) {
								if ($model->phone_op) {$myPhone_op="(".substr($model->phone_op,0,3).") ".substr($model->phone_op,3,3)." - ".substr($model->phone_op,6,4);} else {$myPhone_op='';}
								return $myPhone_op;
							},
						],
                        'ice_contact',
						[
							'attribute'=>'ice_phone',
							'value'=> function($model, $attribute) {
								if ($model->ice_phone) {$myice_phone="(".substr($model->ice_phone,0,3).") ".substr($model->ice_phone,3,3)." - ".substr($model->ice_phone,6,4);} else {$myice_phone='';}
								return $myice_phone;
							},
						],
						[
							'attribute' => 'club_name',
							'value'=> function($model, $attribute) {
								return (new clubs)->getMyClubsNames($model->badge_number);
							},
						],
                        'membershipType.type',
                        [
                            'attribute'=>'incep',
                            'value'=>function($model,$attribute) {
                                return date('M d, Y h:i A',strtotime($model->incep));
                            },
                        ],
                        [
                            'attribute'=>'expires',
                            'value'=>function($model,$attribute) {
                                return date('M d, Y',strtotime($model->expires));
                            },
                        ],
                    ],
                ]) ?>

                </div>
            </div>
        </div>
    </div>
</div>
