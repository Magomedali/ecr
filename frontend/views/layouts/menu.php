<?php


use yii\helpers\{Html,Url};

?>
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <?php  if(!Yii::$app->user->isGuest){ ?>
                <li>
                    <a href="<?php echo Url::to(['/site/index'])?>"><i class="fa fa-file fa-fw"></i>
                        <?php echo Yii::t('site', 'Мой кабинет')?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo Url::to(['/raport/index'])?>"><i class="fa fa-list fa-fw"></i>
                        <?php echo Yii::t('site', 'Мои рапорта')?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo Url::to(['/material/index'])?>"><i class="fa fa-list fa-fw"></i>
                        <?php echo Yii::t('site', 'Мои материалы')?>
                    </a>
                </li>
                 <li>
                    <a href="<?php echo Url::to(['/site/reset-password'])?>"><i class="fa fa-gear fa-fw"></i>
                        <?php echo Yii::t('site', 'Сбросить пароль')?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>