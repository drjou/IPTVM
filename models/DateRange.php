<?php
namespace app\models;

use yii\base\Model;

class DateRange extends Model
{
    public $dateStart;
    public $dateEnd;
    public function rules(){
        return [['dateStart','dateEnd'],'required'];
    }
}

?>