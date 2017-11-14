<?php
	$path='/wp-content/plugins/track-it-down/includes/';
	$content=file_get_contents('/tid/api/index.php');
	if(preg_match_all('/\/\/(dnb|dubstep)\/\/\((.+)\)/',$content,$matches)){
		$dnb_data		=$matches[2][0];
		$dubstep_data	=$matches[2][1];
	}
?>

<label>
	<input type="checkbox" id="tid-show-only-new" disabled/> только новые
</label>

<label class="tid-genre-title">
	<input type="radio" name="tid-genre" data-genre="dnb" checked/> drum'n'bass (<?php echo $dnb_data; ?>)
</label>
<label class="tid-genre-title">
	<input type="radio" name="tid-genre" data-genre="dubstep"/> dubstep (<?php echo $dubstep_data; ?>)
</label>

<div id="tid-table-main">
	<?php echo $content; ?>
</div>

<script src="/tid/lib/jquery.cookie.js"></script>
<script src="<?php echo $path; ?>core.js?119"></script>
<link rel="stylesheet" href="<?php echo $path; ?>style.css?119">
