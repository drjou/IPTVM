<?php
namespace app\models;

use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
class ChartDraw
{

    /**
     * 
     * @param 标题 $title
     * @param 最小值 $min
     * @param 最大值 $max
     * @param 当前值 $data
     * @param 单位 $suffix
     * @return 仪表图的配置数组
     */
    public static function drawGauge($id, $title, $min, $max, $data, $suffix)
    {
        return Highcharts::widget([
            'id' => $id,
            'scripts' => [
                'highcharts-more',
                'modules/solid-gauge'
            ],
            'options' => [
                'chart' => [
                    'type' => 'solidgauge',
                    'height' => 200
                ],
                'title' => [],
                'pane' => [
                    'center' => [
                        '50%',
                        '80%'
                    ],
                    'size' => '90%',
                    'startAngle' => - 90,
                    'endAngle' => 90,
                    'background' => [
                        'backgroundColor' => new JsExpression('(Highcharts.theme && Highcharts.theme.background2) || "#EEE"'),
                        'innerRadius' => '60%',
                        'outerRadius' => '100%',
                        'shape' => 'arc'
                    ]
                ],
                'tooltip' => [
                    'enabled' => false
                ],
                'yAxis' => [
                    'min' => $min,
                    'max' => $max,
                    'title' => [
                        'text' => $title,
                        'y' => - 70,
                        'style' => [
                            'font-size' => '20px',
                            'color' => '#337ab7',
                            'font-family' => 'Racing Sans One, cursive'
                        ]
                    ],
                    'stops' => [
                        [
                            0.1,
                            '#55BF3B'
                        ], // green
                        [
                            0.5,
                            '#DDDF0D'
                        ], // yellow
                        [
                            0.9,
                            '#DF5353'
                        ]
                    ], // red
                    'lineWidth' => 0,
                    'minorTickInterval' => [],
                    'tickPixelInterval' => 600,
                    'tickWidth' => 0,
                    'labels' => [
                        'y' => 16
                    ]
                ],
                'plotOptions' => [
                    'solidgauge' => [
                        'dataLabels' => [
                            'y' => 5,
                            'borderWidth' => 0,
                            'useHTML' => true
                        ]
                    ]
                ],
                'credits' => [
                    'enabled' => false
                ],
                'series' => [
                    [
                        'name' => $title,
                        'data' => [
                            $data
                        ],
                        'dataLabels' => [
                            'format' => '<div style="text-align:center"><span style="font-size:15px;color: black
                        ">{y}</span><br/>
                        <span style="font-size:12px;color:silver">' . $suffix . '</span></div>'
                        ],
                        'tooltip' => [
                            'valueSuffix' => $suffix
                        ]
                    ]
                ]
            ]
        ]);
    }
    /**
     * 
     * @param string $title 折线图标题
     * @param string $subtitle 副标题
     * @param date $xCategories 横轴数据
     * @param string $yText y轴表示
     * @param string $ySuffix y轴单位
     * @param array $data 折线图数据
     */
    public static function drawLineChart($id, $file, $title, $yText, $ySuffix, $data, $subtitle='Click and drag to zoom in.Hold down shift key to pan.'){
        $file->registerJs("
            Highcharts.setOptions({
            global:{
                useUTC:false
            }
        });");
        return Highcharts::widget([
            'id' => $id,
            'scripts' => [
                'highcharts-more',
                'modules/boost'
            ],
            'options' => [
                'chart' => [
                    'type' => 'spline',
                    'zoomType' => 'x',
                    'panning' => true,
                    'panKey' => 'shift'
                ],
                'title' => [
                    'text' => $title,
                    'x' => - 20
                ] // center
                ,
                'subtitle' => [
                    'text' => $subtitle,
                    'x' => - 20
                ],
                'xAxis' => [
                    'type' => 'datetime',
                    'dateTimeLabelFormats' => [
                        'millisecond' => '%H:%M:%S.%L',
                        'second' => '%H:%M:%S',
                        'minute' => '%H:%M',
                        'hour' => '%H:%M',
                        'day' => '%m-%d',
                        'week' => '%m-%d',
                        'month' => '%Y-%m',
                        'year' => '%Y'
                    ]
                ],
                'yAxis' => [
                    'title' => [
                        'text' => $yText
                    ],
                    'plotLines' => [
                        [
                            'value' => 0,
                            'width' => 1,
                            'color' => '#808080'
                        ]
                    ],
                    'lineWidth' => 1,
                ],
                'tooltip' => [
                    'dateTimeLabelFormats' => [
                        'millisecond' => '<b>%H:%M:%S.%L</b>',
                        'second' => '<b>%m-%d %H:%M:%S</b>',
                        'minute' => '<b>%m-%d %H:%M</b>',
                        'hour' => '<b>%m-%d %H:%M</b>',
                        'day' => '<b>%m-%d</b>',
                        'week' => '<b>%m-%d</b>',
                        'month' => '<b>%Y-%m</b>',
                        'year' => '<b>%Y</b>'
                    ],
                    'valueSuffix' => $ySuffix,
                    'shared' => true
                ],
                'legend' => [
                    'layout' => 'vertical',
                    'align' => 'right',
                    'verticalAlign' => 'middle',
                    'borderWidth' => 0
                ],
                'loading' => [
                    'labelStyle' => [
                        'display' => 'inline-block',
                        'width' => '100px',
                        'height' => '100px',
                        'backgroundImage' => 'url("images/loading/facebook.gif")',
                        'backgroundRepeat' => 'no-repeat',
                        'backgroundPosition' => 'center',
           	            'fontSize' => '0px',
                        'top' => '30%'
                    ],
                    'style' => [
                        'opacity' => '0.8'
                    ]
                ],
                'plotOptions' => [
                    'line' => [
                        'dataLabels' => [
                            'enabled' => false
                        ],
                        'enableMouseTracking' => true
                    ],
                    'series' => [
                        'marker' => [
                            'enabled' => false
                        ]
                    ]
                ],
                'credits' => [
                    'enabled' => false
                ],
                'series' => $data
            ]
        ]);
    }
    
    public static function drawDateRange($defaultValue, $minDate, $operation, $id='date-range')
    {
        $range = explode(' - ', $defaultValue);
        $start = $range[0];
        $end = $range[1];
        echo '<div class="drp-container left calendar">';
        echo DateRangePicker::widget([
            'id' => $id,
            'name'=>'date_range',
            'value'=>$defaultValue,
            'presetDropdown'=>true,
            'hideInput'=>true,
            'containerTemplate'=>
            '<span class="input-group-addon">
                    <i class="glyphicon glyphicon-calendar iconfont-blue"></i>
                </span>
                <span class="form-control text-right">
                    <span class="pull-left">
                        <span class="range-value">{value}</span>
                    </span>
                    <b class="caret"></b>
                    {input}
                </span>',
            'pluginEvents' => [
                'apply.daterangepicker' => "function(){ $operation }"
            ],
            'convertFormat'=>true,
            'pluginOptions'=>[
                'timePicker'=>true,
                'timePickerIncrement'=>5,
                'locale'=>['format'=>'Y-m-d H:i:s'],
                'startDate' => $start,
                'endDate' => $end,
                'minDate' => $minDate,
                'maxDate' => new JsExpression('moment()')
            ],
        ]);
        echo '</div>';
    }
    
    public static function operation($type, $dateRangId, $chartId){
        return 
        '
                var time = $("#'.$dateRangId.'").val().split(" - ");
                var startTime = Date.parse(new Date(time[0]));
                var endTime = Date.parse(new Date(time[1]));
                $("#'.$chartId.'").highcharts().showLoading();
                $.get("index.php?r=monitor/update-warning-line&type='.$type.'&startTime="+startTime+"&endTime="+endTime,
                        function(data,status){
                             var obj = eval(data);
                             for(var i=0;i<obj.length;i++){
                                var series=$("#'.$chartId.'").highcharts().series[i];
                                series.setData(obj[i].data,false);
                             }
                            $("#'.$chartId.'").highcharts().redraw();
                            $("#'.$chartId.'").highcharts().hideLoading();
                        });
            ';
    }
}