<?php

use yii\helpers\Url;

$this->title = 'Home';
?>
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="iconfont iconfont-fw icon-admin icon-index"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= $counts['admin']?></div>
                        <div>Administrators</div>
                    </div>
                </div>
            </div>
            <a href="<?= Url::to(['admin/index']) ?>">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="iconfont iconfont-fw icon-user icon-index"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= $counts['account']?></div>
                        <div>STB Accounts</div>
                    </div>
                </div>
            </div>
            <a href="<?= Url::to(['account/index']) ?>">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="iconfont iconfont-fw icon-product icon-index"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= $counts['product']?></div>
                        <div>Products</div>
                    </div>
                </div>
            </div>
            <a href="<?= Url::to(['product/index']) ?>">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="iconfont iconfont-fw icon-channel icon-index"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= $counts['channel']?></div>
                        <div>Channels</div>
                    </div>
                </div>
            </div>
            <a href="<?= Url::to(['channel/index']) ?>">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="iconfont iconfont-fw icon-directory icon-index"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= $counts['directory']?></div>
                        <div>Directories</div>
                    </div>
                </div>
            </div>
            <a href="<?= Url::to(['directory/index']) ?>">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="iconfont iconfont-fw icon-productcard icon-index"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= $counts['productcard']?></div>
                        <div>Productcards</div>
                    </div>
                </div>
            </div>
            <a href="<?= Url::to(['productcard/index']) ?>">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="iconfont iconfont-fw icon-language icon-index"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= $counts['language']?></div>
                        <div>Languages</div>
                    </div>
                </div>
            </div>
            <a href="<?= Url::to(['language/index']) ?>">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="iconfont iconfont-fw icon-log icon-index"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?= $counts['log']?></div>
                        <div>Logs</div>
                    </div>
                </div>
            </div>
            <a href="<?= Url::to(['log/index']) ?>">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
