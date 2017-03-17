<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\models\Menu;
use yii\widgets\ActiveForm;
use app\models\Timezone;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="images/iptv.ico" rel="icon" type="image/x-icon" />
<link href="images/iptv.ico" rel="shortcut icon" type="image/x-icon" />
    <?= Html::csrfMetaTags() ?>
    <title>KuanHong IPTVM-<?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <div id="wrapper">
            <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="home-title" href="<?=Url::to(['/site/index']) ?>">KuanHong IPTV Content Management System</a>
            </div>
            <!-- /.navbar-header -->
    
            <ul class="nav navbar-top-links navbar-right">
            	<li class="dropdown">
            		<?php 
            		     $current = Timezone::getCurrentTimezone();
            		?>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="javascript::void(0)">
                        <svg class="icon" aria-hidden="true">
                        	<use xlink:href="#<?=$current->icon ?>"></use>
                        </svg> <?=$current->timezone ?> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-timezone">
                    	<?php 
                        	$dependency = [
                        	    'class' => 'yii\caching\DbDependency',
                        	    'sql' => 'SELECT COUNT(timezone),MAX(updateTime) FROM timezone where status=1',
                        	];
                        	if($this->beginCache('timezone', ['duration' => 86400, 'dependency' => $dependency])){
                        	    $timezones = Timezone::getAvailableTimezone();
                        	    foreach ($timezones as $timezone){
                        	        $url = Url::to(['/timezone/set-timezone', 'timezone' => $timezone->timezone]);
                        	        echo '<li>
                                        <a href="'. $url .'">
                                            <svg class="icon" aria-hidden="true">
                                            	<use xlink:href="#' . $timezone->icon . '"></use>
                                            </svg> ' . $timezone->timezone . '
                                        </a>
                                    </li>
                                    <li class="divider"></li>';
                        	    }
                        	    $this->endCache();
                        	}
                    	?>
                        <li>
                            <a class="text-center" href="#">
                                <strong>Read All Messages</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="<?=Url::to(['/profile/index'])?>"><i class="fa fa-user fa-fw"></i> <?=\Yii::$app->user->identity->userName ?></a>
                        </li>
                        <li><a href="<?=Url::to(['/profile/password-modify'])?>"><i class="fa fa-gear fa-fw"></i> Password Modify</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="<?=Url::to(['/admin/logout'])?>"><i class="fa fa-sign-out fa-fw"></i> Sign Out</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="dashboard">
                            <span><i class="fa fa-dashboard"></i>Dashboard</span>
                        </li>
                        <?php
                            $dependency = [
                                'class' => 'yii\caching\DbDependency',
                                'sql' => 'SELECT COUNT(id), MAX(updateTime) FROM menu',
                            ];
                            //Yii::$app->cache->flush();
                            if($this->beginCache('menu_sidebar', ['duration' => 86400, 'dependency' => $dependency])){
                                $model = new Menu();
                                $menus = $model->getAllMenus();
                                if(!empty($menus)){
                                    $first_level = $menus;
                                    foreach ($first_level as $fl){
                                        //如果一级菜单的子菜单为空
                                        if(empty($fl->children)){
                                            echo '<li>';
                                            if(strpos($fl->route, "http")!== false || strpos($fl->route, "www")!== false){
                                                 echo '<a href="'. $fl->route .'" target="_blank"><i class="'. $fl->icon .'"></i>'. $fl->menuName .'</a>';
                                            }else{
                                                echo Html::a('<i class="'. $fl->icon .'"></i>'. $fl->menuName .'', [$fl->route]);
                                            }
                                        }else{//一级菜单的子菜单不为空
                                            echo '<li>
                                            <a href="'. $fl->route .'"><i class="'. $fl->icon .'"></i>'. $fl->menuName .'<span class="fa arrow"></span></a>';
                                
                                            $second_level = $fl->children;//fl的二级子菜单
                                
                                            echo '<ul class="nav nav-second-level">';//二级菜单的ul开始
                                            foreach ($second_level as $sl){
                                                if(empty($sl->children)){//如果二级菜单的子菜单为空
                                                    echo '<li>';
                                                    echo Html::a('<i class="'. $sl->icon .'"></i>'. $sl->menuName .'', [$sl->route]);
                                                }else{//二级子菜单的子菜单不为空
                                                    echo '<li>
                                                    <a href="'. $sl->route .'"><i class="'. $sl->icon .'"></i>'. $sl->menuName .'<span class="fa arrow"></span></a>';
                                
                                                    $third_level = $sl->children;//sl的三级子菜单
                                                    echo '<ul class="nav nav-third-level">';//三级菜单的ul开始
                                
                                                    foreach ($third_level as $tl){
                                                        echo '<li>';
                                                        echo Html::a('<i class="'. $tl->icon .'"></i>'. $tl->menuName .'', [$tl->route]);
                                                        echo '</li>';
                                                    }
                                                    echo '</ul>';//三级菜单的ul结束
                                                }
                                                echo '</li>';//二级菜单的结束
                                            }
                                            echo '</ul>';//二级菜单的ul结束
                                        }
                                        echo  '</li>';//一级菜单的结束
                                    }
                                }
                                $this->endCache();
                            }
                            
                        ?>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <?= Breadcrumbs::widget([
                'homeLink' => array('label' => 'Home', 'url' => Yii::$app->homeUrl),
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <div id="page-container">
            	<div class="row">
            		<div class="col-lg-12">
            		</div>
            		<!-- /.col-lg-12 -->
            	</div>
            	<div class="row">
            		<div class="col-lg-12">
            			<div class="panel panel-default">
            				<div class="panel-heading" style="background-color: #eeeeee;">
            					<h4 style="font-weight: bold;"><?=Html::encode($this->title) ?></h4>
            				</div>
            				<!-- /.panel-heading -->
            				<div class="panel-body">
            					<div class="dataTable_wrapper">
            						<?= $content ?>
                        		</div>
            				</div>
            				<!-- /.panel-body -->
            			</div>
            			<!-- /.panel -->
            		</div>
            		<!-- /.col-lg-12 -->
            	</div>
            </div>
        </div> 
         
        <!-- /#page-wrapper -->
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
