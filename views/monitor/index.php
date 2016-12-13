<?php

use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
$this->title = 'IPTV Monitor';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
    echo Highcharts::widget([
        'scripts' => [
            'highcharts-more',
            'modules/solid-gauge'
        ],
        'options' => [
            'chart' => [
                'type' => 'solidgauge'
            ],
            'title' => [],
            'pane' => [
                'center' => ['50%', '85%'],
                'size' => '140%',
                'startAngle' => -90,
                'endAngle' => 90,
                'background' => [
                    'backgroundColor' => new JsExpression('(Highcharts.theme && Highcharts.theme.background2) || "#EEE"'),
                    'innerRadius' => '60%',
                    'outerRadius' => '100%',
                    'shape' => 'arc',
                ]
            ],
            'tooltip' => [
                'enabled' => false
            ],
            'yAxis' => [
                'min' => 0,
                'max' => 100,
                'title' => [
                    'text' => 'CPU',
                    'y' => -150
                ],
                'stops' => [
                    [0.1, '#55BF3B'], // green
                    [0.5, '#DDDF0D'], // yellow
                    [0.9, '#DF5353'] // red
                ],
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
                'enabled' => false,
            ],
            'series' => [
                [
                    'name' => 'CPU',
                    'data' => [98.9],
                    'dataLabels' => [
                        'format' => '<div style="text-align:center"><span style="font-size:25px;color: black 
                        ">{y}</span><br/>
                        <span style="font-size:12px;color:silver">km/h</span></div>'
                    ],
                    'tooltip' => [
                        'valueSuffix' => ' km/h'
                    ]
                ]
            ]
        ]
    ]);
