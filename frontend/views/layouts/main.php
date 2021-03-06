<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\AppAsset;
use yii\helpers\{Html,Url};
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use common\widgets\docnotes\DocNotes;


AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language ?>">
<head>

    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="description" content="">
    <meta name="author" content="">
    
    <?php echo Html::csrfMetaTags() ?>
    
    <title><?php echo Html::encode($this->title) ?></title>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>
    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-brand" href="<?php echo Url::to(['/site/index']);?>"><?php echo Html::encode("Личный кабинет") ?></a>
            </div>
            <!-- /.navbar-header -->
            <?php if(!Yii::$app->user->isGuest){?>
            <ul class="nav navbar-top-links navbar-right">
                
                <!-- /.dropdown -->
                <?php if(isset($this->params['shift_start'])){?>
                    <li>
                        <?php echo "Начало смены: ",date("d.m.Y H:i",strtotime($this->params['shift_start']));?>
                    </li>
                <?php } ?>
                
                <?php if(isset(Yii::$app->params['notes'])){ ?>
                    <li class="shifttime">
                        <?php echo DocNotes::widget(['notes'=>Yii::$app->params['notes'],'count'=>Yii::$app->params['notes_count']]); ?>
                    </li>
                <?php } ?>

                <li class="dropdown">
                    <?php echo Html::a(Yii::$app->user->identity->name,['site/index']);?>
                </li>
                <li>
                    <?php 
                        echo Html::beginForm(['/site/logout'], 'post')
                                    . Html::submitButton(
                                            '<i class="fa fa-sign-out fa-fw"></i> Выход',
                                            ['class' => 'btn btn-link logout']
                                    )
                                    . Html::endForm()
                    ?>
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <?php echo $this->render("menu",[]);?>
            <?php } ?>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <?php
                    $show_backLink = isset($this->params['backlink']) && isset($this->params['backlink']['url']);
                ?>
                <div class="<?php echo $show_backLink ? 'col-lg-6' : 'col-lg-12';?>">
                    <h1 class="page-header"><?php echo $this->title;?></h1>
                </div>
                <?php
                    if($show_backLink){
                    ?>
                    <div class="col-lg-6 backlink">
                        <?php echo Html::a("X",$this->params['backlink']['url'],['id'=>'backLinkBtn','class'=>'btn btn-danger pull-right'])?>
                    </div>
                    <?php
                        $JS = <<<JS
                            $("#backLinkBtn").click(function(event){
                                if(!confirm("Подтвердите свои действия!"))
                                    event.preventDefault();
                            });
JS;
                        if(isset($this->params['backlink']['confirm']) && boolval($this->params['backlink']['confirm'])){
                           $this->registerJs($JS); 
                        }
                        
                    }
                ?>
            </div>
            
            <?php  echo Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) 
            ?>

            <?php  echo Alert::widget(); ?>
            
            <?php echo $content; ?>
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
