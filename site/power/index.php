<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"> -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <title>TID</title>

        <link rel="stylesheet" type="text/css" href="lib/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="lib/sweetalert.css">
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="lib/jquery.js"></script>
        <script src="lib/bootstrap.js"></script>
        <script src="lib/sweetalert.js"></script>
        <script src="main.js"></script>
    </head>
    <body>
        <!-- http://d33yltq05bdjmc.cloudfront.net/graphics/1080/1080380__home_feature.png -->
        <?php include_once('config.php'); ?>
        <?php if($cache): ?>
            <?php include_once('cache.php'); ?>
        <?php else: ?>
            <?php include_once('releases.php'); ?>
        <?php endif; ?>
        <?php include_once('releases2genres.php'); ?>
        <div class="container">
            <h1 class="header">
                TID
                <small><button id="refresh" class="btn btn-link"><span class="glyphicon glyphicon-refresh"></span></button></small>
            </h1>

            <div class="row">
                <div class="col-xs-6">
                    <?php if($limit_days>0){ ?>
                        <p>Только релизы последних <?php echo $limit_days; ?> дней.</p>
                    <?php } ?>
                </div>
                <div class="col-xs-6">
                    <div class="alert alert-warning" id="alert-main">
                        <p><span class="glyphicon glyphicon-warning-sign"></span> Информация:</p>
                        <ul>
                            <li>Всё ещё в процессе разработки.</li>
                            <li>Больше информации — значит релиз подхвачен старой версией.</li>
                            <li>Что-то есть в старой версии, но нет здесь? Будет.</li>
                            <li>Хочется Яриконезависимой системы? Мне тоже. Сделаю тут автообновление в дальнейшем.</li>
                        </ul>
                        <hr/>
                        <p>Как думаешь, старая система отжила своё? Да? Нет? Напиши мнение в флудилку или Ярику в личку. Спасибо! (:</p>
                    </div>
                </div>
            </div>
            <br/>

            <ul id="myTab" class="nav nav-tabs" role="tablist">
                <?php for($i=0;$i<count($our_genres_tid);$i++): ?>
                    <?php if(isset($GLOBALS[$our_genres[$i]])): ?>
                        <li role="presentation" class="<?php if(!$i)echo 'active'; ?>"><a href="#<?php echo $our_genres[$i]; ?>" id="<?php echo $our_genres[$i]; ?>-tab" role="tab" data-toggle="tab" aria-controls="<?php echo $our_genres[$i]; ?>" aria-expanded="true"><?php echo $our_genres_tid[$i]; ?></a></li>
                    <?php endif; ?>
                <?php endfor; ?>
                <!-- <li role="presentation" class=""><a href="#settings" role="tab" id="settings-tab" data-toggle="tab" aria-controls="settings" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span> Настройки</a></li> -->
            </ul>

            <div class="tab-content">
                <?php for($i=0;$i<count($our_genres_tid);$i++): ?>
                    <?php if(isset($GLOBALS[$our_genres[$i]])): ?>
                        <div role="tabpanel" class="tab-pane fade<?php if(!$i)echo ' active in'; ?>" id="<?php echo $our_genres[$i]; ?>" aria-labelledby="<?php echo $our_genres[$i]; ?>-tab">
                            <?php table($GLOBALS[$our_genres[$i]]); ?>
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
                <!-- <div role="tabpanel" class="tab-pane fade" id="settings" aria-labelledby="settings-tab">
                    <br/>
                    <div class="row">
                        <div class="col-xs-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="show-only-new"> только новые релизы
                                </label>
                            </div>
                            <p id="labels-to-remove">Спрятанные лейблы: <span class="list"></span></p>
                        </div>
                        <div class="col-xs-4">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Статистика</h3>
                                </div>
                                <div class="panel-body">
                                    <dl class="dl-horizontal">
                                        <dt>Drum & Bass</dt>
                                        <dd><?php echo count($drum_and_bass); ?> (+...)</dd>
                                        <dt>Dubstep</dt>
                                        <dd><?php echo count($dubstep); ?> (+...)</dd>
                                        <dt>Breaks</dt>
                                        <dd><?php echo count($breaks); ?> (+...)</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </body>
</html>
