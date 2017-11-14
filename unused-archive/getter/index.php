<?php
	/*
	site/tid
	site/tid/stats
	site/tid/stats?genres
	*/
?>

<script src="jquery.js"></script>

<style>
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
</style>

<div>
	<button id="start">start</button>
	<button id="stop" disabled>stop</button>
	<span id="number">0</span>
</div>
<div>
	<button id="forward" disabled>forward</button>
	<button id="backward">backward</button>
</div>
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
			if(go)
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