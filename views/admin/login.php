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
			<div class="col-md-4 col-md-offset-4">
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
            'template' => "<div class=\"\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>\n"
        ]
    ]);
    ?>
<fieldset>
        <?= $form->field($model, 'userName')->textInput(['autofocus' => true, 'placeholder' => 'username', 'maxlength' => 20])?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'password'])?>

        <?=$form->field($model, 'rememberMe')->checkbox(['template' => "\n<div class=\"\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>"])?>

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
