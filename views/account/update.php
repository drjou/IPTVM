<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Update Account';
$this->params['breadcrumbs'][] = ['label' => 'Account List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$states = [
    '1002' => 'need activate',
    '1003' => 'need recharge',
];
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'accountId')->textInput() ?>

<?= $form->field($model, 'state')->dropDownList($states) ?>

<?= $form->field($model, 'enable')->checkbox()?>

<?= $form->field($model, 'products', ['template' => 
    '{label}
    <div class="checkgroup">
        <input type="checkbox" class="all" Name="CheckAll"><label for="all" class="label-all">Check All</label><br />
        {input}
    </div>
    {error}',
])->checkboxList($products, [ 'separator'=>'&nbsp;&nbsp;']) ?>

<div class="form-group">
    <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-warning']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php 
    $this->registerJs("
        if($('#account-state').val() == 1003){
            $('.field-account-products').hide();
        }
        $('#account-state').change(function(){
    		if($(this).val() == 1003){
    			$('.field-account-products').hide();
                $('#account-products input').each(function(){
                    $(this).prop('checked', false);
                });
    		}else{
    			$('.field-account-products').show();
    		}
	    });
        
        $('.all').change(function(){
    		if(this.checked){
    			$('.label-all').html('Deselect All');
    		}else{
    			$('.label-all').html('Check All');
    		}
            $('#account-products input').prop('checked', this.checked);
	    });
        
        $('#account-products input').change(function(){
    		var checkAll = true;
        	$('#account-products input').each(function(){
        		if(!$(this).prop('checked')){
        			checkAll = false;
        			$('.all').prop('checked', false);
        			$('.label-all').html('Check All');
        			return false;
        		}
        	});
        	if(checkAll){
        		$('.all').prop('checked', true);
        		$('.label-all').html('Deselect All');
        	}
	    });
    ");
?>