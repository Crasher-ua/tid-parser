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

		<link rel="stylesheet" type="text/css" href="/lib/bootstrap.css">
		<script type="text/javascript" src="lib/jquery.js"></script>
		<script type="text/javascript" src="lib/bootstrap.js"></script>

		<link href="lib/css/material-wfont.min.css" rel="stylesheet">
		<link href="lib/css/ripples.min.css" rel="stylesheet">
		<script src="lib/js/material.min.js"></script>
		<script src="lib/js/ripples.min.js"></script>
		<script>$.material.init()</script>

		<!-- <link href="lib/jquery.dropdown.css" rel="stylesheet">
		<script src="lib/jquery.dropdown.js"></script>
		<script>$("#dropdown-menu select").dropdown()</script> -->

		<!-- <script type="text/javascript" src="core.js"></script> -->
		<!-- <link rel="stylesheet" type="text/css" href="style.css"> -->
	</head>
	<body>
		<div class="container">
			<h1 class="header">TID</h1>
			<p>
				Mode:
				<span class="btn-group" role="group" aria-label="...">
					<button type="button" class="btn btn-default">recheck holes</button>
					<button type="button" class="btn btn-default">incremental</button>
					<button type="button" class="btn btn-default">deep scan</button>
				</span>
			</p>
			<p>
				Actions:
				<span class="btn-group" role="group" aria-label="...">
					<button type="button" class="btn btn-success">start</button>
					<button type="button" class="btn btn-default disabled">stop</button>
				</span>
				<button type="button" class="btn btn-info">fire</button>
			</p>
			<p class="form-inline">
				Drop to deep scan after:
				<input type="text" class="form-control" placeholder="X">
				request <i>(for incremental only)</i>
			</p>
			<p><span class="glyphicon glyphicon-time"></span> <span id="time">0</span></p>
			<p><span class="glyphicon glyphicon-chevron-right"></span> 0</p>
			add ranges here
			add results box
			add log
		</div>
	</body>
</html>

<?php
	/*
	site/tid
	site/tid/stats

?>


<!-- <style>
	html,body,table{
		font-family:Tahoma;
		font-size:12px;
	}
	table{
		border-collapse:collapse;
	}
	td{
		border:1px solid #eee;
		padding:5px 10px;
	}
	button{
		border:1px solid #0a0;
		background:#0d0;
		padding:4px 10px;
		position:relative;
		top:0;
		left:0;
		-webkit-transition:all .2s;
		transition:all .2s;
	}
	button:disabled{
		background:#eee;
		color:#aaa;
		border-color:#ddd;
	}
	button:hover:enabled{
		top:-2px;
		left:-2px;
		box-shadow:2px 2px 2px rgba(0,0,0,0.2);
		cursor:pointer;
	}
	div{
		margin-bottom:15px;
	}
</style> -->

<div>
	<button id="start">start</button>
	<button id="stop" disabled>stop</button>
	<span id="number">0</span>
</div>
<!-- <div>
	<button id="forward" disabled>forward</button>
	<button id="backward">backward</button>
</div> -->
<div>
	offset: <span id="offset">0</span>, waiting: <span id="time">0</span>s
</div>
<hr/>
<div id="page"></div>

<script>
	var go=false,
		offset=0,
		backward=false;

	$(function(){
		$('#start').click(function(){
			$(this).prop('disabled',true);
			$('#stop').prop('disabled',false);
			go=true;
			action();
		});
		$('#stop').click(function(){
			$(this).prop('disabled',true);
			go=false;
		});

		setInterval(function(){
			if($('#start').is(':disabled'))
				$('#time').text(function(index,text){
					return text*1+1;
				});
		},1000);

		$('#forward').click(function(){
			backward=false;
			offset=0;
			$('#offset').text(offset);
			$(this).prop('disabled',true);
			$('#backward').prop('disabled',false);
		})
		$('#backward').click(function(){
			backward=true;
			offset=0;
			$('#offset').text(offset);
			$(this).prop('disabled',true);
			$('#forward').prop('disabled',false);
		})
	});

	function action(){
		if(go){
			$('#offset').text(offset);
			var url='get';
			if(offset)
				url+='?offset='+offset;
			if(backward)
				url+=(offset ? '&' : '?')+'backward';
			$('#page').load(url,function(){
				$('#time').text(0);
				if($('#page').text().indexOf('Fatal error')==-1){
					offset=0;
				}else{
					offset++;
					$('body').append('<div class="error">error</div>');
				}
				$('#number').text(function(index,text){
					return text*1+1;
				});
				action();
			});
		}else{
			$('#start').prop('disabled',false);
		}
	}
</script>
*/