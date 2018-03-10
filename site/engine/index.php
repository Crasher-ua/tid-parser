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
        <script type="text/javascript" src="lib/jquery.js"></script>
        <script type="text/javascript" src="lib/bootstrap.js"></script>

        <link rel="stylesheet" type="text/css" href="lib/bootstrap-slider.css">
        <script type="text/javascript" src="lib/bootstrap-slider.js"></script>
        <!-- seiyria.github.io/bootstrap-slider -->

        <script type="text/javascript" src="core.js"></script>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div class="container">
            <h1 class="header">TID</h1>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Settings</h3>
                </div>
                <div class="panel-body" id="panel-main">
                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-xs-5 settings-hor-title">Mode</div>
                        <div class="col-lg-9 col-md-8 col-xs-7">
                            <span id="mode" class="btn-group" role="group">
                                <button type="button" class="btn btn-default" id="mode-recheck" data-value="recheck">recheck holes</button>
                                <button type="button" class="btn btn-default active" id="mode-incremental" data-value="incremental">incremental</button>
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-xs-5 settings-hor-title">Actions</div>
                        <div class="col-lg-9 col-md-8 col-xs-7">
                            <span class="btn-group" role="group">
                                <button id="start" type="button" class="btn btn-success">start</button>
                                <button id="stop" type="button" class="btn btn-danger disabled">stop</button>
                            </span>
                            <button id="fire" type="button" class="btn btn-info">fire</button>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-xs-5 settings-hor-title">Maximum empty responses on incremental</div>
                        <div class="col-lg-9 col-md-8 col-xs-7">
                            <input id="max-empty" data-slider-id="max-empty-slider" data-val-id="max-empty-val" type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="10"/>
                            <span id="max-empty-val"></span>
                            <span class="hidden-xs">requests, then drop to recheck</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-xs-5 settings-hor-title">Maximum log rows</div>
                        <div class="col-lg-9 col-md-8 col-xs-7">
                            <input id="log-size" data-slider-id="log-size-slider" data-val-id="log-size-val" type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="100"/>
                            <span id="log-size-val"></span>
                            <span class="hidden-xs">, older will be cleaned <a id="clean-log" class="btn btn-link">(force clean)</a></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-xs-5 settings-hor-title">Maximum waiting time</div>
                        <div class="col-lg-9 col-md-8 col-xs-7">
                            <input id="wait-time" data-slider-id="wait-time-slider" data-val-id="wait-time-val" type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="100"/>
                            <span id="wait-time-val"></span>
                            <span class="hidden-xs"> seconds, then retry</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row text-center">
                <div class="col-xs-2">
                    <span class="glyphicon glyphicon-time"></span> <span id="time">0</span>s
                    <div id="last-time-holder">(<span class="hidden-xs">last time: </span><span id="last-time"></span>s)</div>
                </div>
                <div class="col-xs-2">
                    <span class="glyphicon glyphicon-chevron-right"></span> <span id="offset">0</span>
                </div>
                <div class="col-xs-2">
                    <span class="glyphicon glyphicon-arrow-up"></span> <span id="requests-sent">0
                </span>
                </div>
                <div class="col-xs-2">
                    <span class="glyphicon glyphicon-ok"></span> <span id="success-number">0</span>
                </div>
                <div class="col-xs-4">
                    <!-- <button class="btn btn-link pull-right" id="clean-log"><span class="glyphicon glyphicon-trash"></span></button> -->
                    <span class="glyphicon glyphicon-flash"></span> <span id="status">standby</span>
                </div>
            </div>
            <div class="panel panel-default">
                <div id="log" class="panel-body"></div>
            </div>
            <!-- <div id="result">results will be here</div> -->

            <!-- <button class="btn btn-info" onclick="return_value()">return value</button> -->
            <!-- for test only -->
        </div>
    </body>
</html>
