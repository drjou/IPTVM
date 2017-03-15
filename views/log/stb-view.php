<?php
use yii\widgets\DetailView;
use yii\helpers\Html;
use app\models\Account;
use app\models\Timezone;
$this->title = 'STB Log Detail';
$this->params['breadcrumbs'][] = ['label' => 'STB Log List', 'url' => ['stb']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= DetailView::widget([
    'model' => $model,
    'template' => function ($attribute, $index, $widget){
         if($index%2 == 0){
             return '<tr class="label-white"><th>' . $attribute['label'] . '</th><td>' . $attribute['value'] . '</td></tr>';
         }else{
             return '<tr class="label-grey"><th>' . $attribute['label'] . '</th><td>' . $attribute['value'] . '</td></tr>';
         }
    },
    'attributes' => [
        [
            'attribute' => 'prefix',
            'format' => 'raw',
            'value' => empty(Account::findOne($model->prefix)) ? $model->prefix : Html::a($model->prefix, ['account/view', 'accountId' => $model->prefix], ['title' => 'view']),
        ],
        [
            'attribute' => 'log_time',
            'value' => Timezone::date($model->log_time),
        ],
        'message',
        'category',
    ],
]) ?>
<?= Html::a('Back', \Yii::$app->request->referrer, ['class' => 'btn btn-warning cancel']) ?>
<?php 
    $this->registerJs("
        $(document).on('click', '.cancel', function(){
            window.history.back();
        });
    ");
?>