<?php


use yii\helpers\{Html,Url};

?>
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <?php  if(!Yii::$app->user->isGuest){ ?>
                <li>
                    <a href="<?php echo Url::to(['/site/index'])?>"><i class="fa fa-table fa-fw"></i>
                        <?php echo Yii::t('site', 'Бригадиры')?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo Url::to(['/site/archive'])?>"><i class="fa fa-table fa-fw"></i>
                        <?php echo Yii::t('site', 'Архив бригадиров')?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo Url::to(['/site/reset-password'])?>"><i class="fa fa-gear fa-fw"></i>
                        <?php echo Yii::t('site', 'Сменить пароль')?>
                    </a>
                </li>

                <?php if(Yii::$app->user->can("superadmin")){?>
                <li>
                     <a href="<?php echo Url::to(['/requests/index'])?>"><i class="fa fa-gear fa-fw"></i>
                        <?php echo Yii::t('site', 'Журнал запросов в 1С')?>
                    </a>
                </li>
                <?php } ?> 
            <?php } ?>
        </ul>
    </div>
</div>