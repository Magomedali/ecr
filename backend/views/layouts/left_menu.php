<?php


use yii\helpers\{Html,Url};

?>
<div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="<?php echo Url::to(['/site/index'])?>"><i class="fa fa-table fa-fw"></i>
                                <?php echo \Yii::t('site', 'Бригадиры')?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo Url::to(['/site/index'])?>"><i class="fa fa-gear fa-fw"></i>
                                <?php echo \Yii::t('site', 'Сменить пароль')?>
                            </a>
                        </li>
                        
                    <?php  if(!Yii::$app->user->isGuest){ ?>
                        
                        <?php if(\Yii::$app->hasModule("users")){ ?>
                            <li>
                                <a href="<?php echo Url::to(['/users/manager/index'])?>"><i class="fa fa-table fa-fw"></i> Пользователи</a>
                            </li>
                        <?php }else{ ?>
                            <li>
                                <a href="<?php echo Url::to(['/site/list'])?>"><i class="fa fa-table fa-fw"></i>
                                    <?php echo \Yii::t('site', 'Пользователи')?>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(\Yii::$app->hasModule("rbac") && \Yii::$app->user->can("admin")){ ?>
                        <li>
                            <a href="<?php echo Url::to(['/rbac/rbac/index'])?>"><i class="fa fa-sitemap fa-fw"></i> Роли и права</a>
                        </li>
                        <?php } ?>
                        <li>
                            <a href="<?php echo Url::to(['/organisation/index'])?>"><i class="fa fa-table fa-fw"></i> 
                                <?php echo \Yii::t('site', 'Организации')?>
                            </a>
                        </li>  

                        <li>
                            <a href="<?php echo Url::to(['/autotruck/index'])?>"><i class="fa fa-table fa-fw"></i> 
                                <?php echo \Yii::t('site', 'Управление заявками')?>
                            </a>  
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-table fa-fw"></i>
                                <?php echo Yii::t('site', 'Справочники')?> <span class="fa arrow"></span>
                            </a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?php echo Url::to(['/status/index'])?>"><i class="fa fa-table fa-fw"></i> 
                                        <?php echo Yii::t('site', 'Статус заявок')?>
                                    </a>  
                                </li>

                                <li>
                                    <a href="<?php echo Url::to(['/clientcategory/index'])?>"><i class="fa fa-table fa-fw"></i> 
                                        <?php echo Yii::t('site', 'Категория клиентов')?>
                                    </a>  
                                </li>

                                <li>
                                    <a href="<?php echo Url::to(['/suppliercountry/index'])?>"><i class="fa fa-table fa-fw"></i> 
                                        <?php echo Yii::t('site', 'Страны поставок')?>
                                    </a>  
                                </li>
                                <li>
                                    <a href="<?php echo Url::to(['/typepackaging/index'])?>"><i class="fa fa-table fa-fw"></i> 
                                        <?php echo Yii::t('site', 'Тип упаковки')?>
                                    </a>  
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="<?php echo Url::to(['/sender/index'])?>"><i class="fa fa-table fa-fw"></i> 
                                <?php echo Yii::t('site', 'Отправители')?>
                            </a>  
                        </li>
                        <li>
                            <a href="<?php echo Url::to(['/setting/index'])?>"><i class="fa fa-wrench fa-fw"></i> 
                                <?php echo Yii::t('site', 'Настройки системы')?>
                            </a>  
                        </li>
                        <?php if(Yii::$app->user->can("import/index")){?>
                            <li>
                                <a href="<?php echo Url::to(['/import/index'])?>"><i class="fa fa-wrench fa-fw"></i> 
                                    <?php echo Yii::t('site', 'Импорт')?>
                                </a>  
                            </li>
                        <?php } ?>
                    <?php } ?>
                    </ul>
                </div>
            </div>