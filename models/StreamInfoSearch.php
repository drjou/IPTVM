<?php
namespace app\models;
use yii\data\ActiveDataProvider;
use yii\base\Model;


class StreamInfoSearch extends StreamInfo{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [['server', 'streamName', 'status', 'sourceStatus', 'total','user', 'system', 'memory', 'rss', 'readByte', 'writeByte', 'recordTime'], 'safe'],
        ];
    }
    /**
     * 每个场景要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios()
    {
        return Model::scenarios();
    }
    /**
     * 检索过滤
     * @param string $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params, $streams, $serverName){
        if($streams===null){
            $query = StreamInfo::find();
        }else{
            $streamArr = explode(',', $streams);
            $where = implode('","', $streamArr);
            $where = 'streamName in ("'.$where.'") and server="'.$serverName.'"';
            $query = StreamInfo::find()->where($where);
        }
        $dataProvider  = new ActiveDataProvider([
           'query' => $query,
           'pagination' => [
               'pageSize' => 10
           ]
        ]);
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'stream_info.server', $this->server])
        ->andFilterWhere(['like', 'stream_info.streamName', $this->streamName])
        ->andFilterWhere(['=', 'status', $this->status])
        ->andFilterWhere(['=', 'sourceStatus', $this->sourceStatus])
        ->andFilterWhere(['=', 'total', $this->total])
        ->andFilterWhere(['=', 'user', $this->user])
        ->andFilterWhere(['=', 'system', $this->system])
        ->andFilterWhere(['=', 'memory', $this->memory])
        ->andFilterWhere(['=', 'rss', $this->rss])
        ->andFilterWhere(['=', 'readByte', $this->readByte])
        ->andFilterWhere(['=', 'writeByte', $this->writeByte])
        ->andFilterWhere(['like', 'recordTime', $this->recordTime]);
        return $dataProvider;
    }
}
