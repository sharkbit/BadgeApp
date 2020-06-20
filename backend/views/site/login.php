<?php
//use Yii;
use yii\helpers\BaseUrl;
use yii\helpers\url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <div class="row">
        <div class="col-xs-12">
            <div class="login-box">
			<?= $this->render('_login-tab-menu',['model'=>$model]).PHP_EOL; ?>
			
                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                <?= $form->field($model, 'username')->textInput(['autofocus' => true]).PHP_EOL; ?>
                <?= $form->field($model, 'password')->passwordInput().PHP_EOL; ?>
                <?= $form->field($model, 'rememberMe')->checkbox().PHP_EOL; ?>
                <div class="form-group">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary pull-right', 'name' => 'login-button']).PHP_EOL; ?>
                </div>
                <div class="clearfix"></div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
