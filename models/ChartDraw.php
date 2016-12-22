<?php
namespace app\models;

use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
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
    public static function drawGauge($title, $min, $max, $data, $suffix)
    {
        return Highcharts::widget([
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
                            'font-size' => '20px'
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
    public static function drawLineChart($title, $subtitle, $yText, $ySuffix, $data){
        return Highcharts::widget([
            'options' => [
                'chart' => [
                    'zoomType' => 'x'
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
                    ]
                ],
                'tooltip' => [
                    'dateTimeLabelFormats' => [
                        'millisecond' => '%H:%M:%S.%L',
                        'second' => '%H:%M:%S',
                        'minute' => '%H:%M',
                        'hour' => '%H:%M',
                        'day' => '%m-%d',
                        'week' => '%m-%d',
                        'month' => '%Y-%m',
                        'year' => '%Y'
                    ],
                    'valueSuffix' => $ySuffix
                ],
                'legend' => [
                    'layout' => 'vertical',
                    'align' => 'right',
                    'verticalAlign' => 'middle',
                    'borderWidth' => 0
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
}