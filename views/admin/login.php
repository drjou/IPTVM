<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\assets\AppAsset;

AppAsset::register($this);
$this->title = 'Login';
?>
<?php $this->beginPage()?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<meta charset="<?= Yii::$app->charset ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="images/iptv.ico" rel="icon" type="image/x-icon" />
<link href="images/iptv.ico" rel="shortcut icon" type="image/x-icon" />
    <?= Html::csrfMetaTags()?>
    <title>KuanHong IPTVM-<?= Html::encode($this->title) ?></title>
    <?php $this->head()?>
</head>
<body>
<?php $this->beginBody()?>

<div class="container">
		<div class="row">
			<div class="login-title">KuanHong IPTV Content Management System</div>
			<div class="col-md-iptv col-md-offset-iptv">
				<div class="login-panel panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Please Login</h3>
					</div>
					<div class="panel-body">

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => [
            'class' => ''
        ],
        'fieldConfig' => [
            'template' => "<div class=\"\">{input}</div>\n<div class=\"\">{error}</div>"
        ]
    ]);
    ?>
<fieldset>
        <?= $form->field($model, 'userName', 
            ['template' => '<div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        {input}
            </div><div>{error}</div>'])->textInput(['autofocus' => true, 'placeholder' => 'username'])?>

        <?= $form->field($model, 'password',
            ['template' => '<div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        {input}
            </div><div>{error}</div>'])->passwordInput(['placeholder' => 'password'])?>

		<?= $form->field($model,'captcha', ['options' => ['class' => 'form-group']])->widget(yii\captcha\Captcha::className(),
		    [
		        'template' => '<div class="captcha"><div class="captcha-img">{image}</div><div class="captcha-input">{input}</div><div style="clear:both"></div> </div>',
		        'captchaAction'=>'admin/captcha',
		        'imageOptions'=>['alt'=>'click to change', 'style'=>'cursor:pointer'],
		        'options' => ['class' => 'form-control', 'placeholder' => 'verify code'],
		]);?>
		
        <?=$form->field($model, 'rememberMe')->checkbox()?>
        
		<div class="form-group">
			<div class="">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-full', 'name' => 'login-button'])?>
            </div>
		</div>
</fieldset>
    <?php ActiveForm::end(); ?>

 </div>
 
				</div>
				<div style="text-align:center;font-size:16px;font-weight:bold;color:gray;">Copyright © <?=date('Y'); ?> KuanHong Inc., All Right Reserved</div>
			</div>
		</div>
	</div>
<?php $this->endBody()?>
</body>
</html>
<?php $this->endPage()?>
