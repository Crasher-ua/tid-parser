jQuery(function($){
	var box=$('#track-it-down').appendTo($('#dashboard-widgets-wrap').parent());
	box.find('.hndle').removeClass('hndle');

	$('#tid-show-only-new').click(function(){
		var checked=$(this).is(':checked');
		$.cookie('only-new',checked);
		$('#tid-table-main')[checked ? 'addClass' : 'removeClass']('only-new');
	});
	var only_new=$.cookie('only-new');
	if(typeof(only_new)!='undefined' && only_new=='true'){
		$('#tid-show-only-new').prop('checked',true);
		$('#tid-table-main').addClass('only-new');
	}

	$('#tid-show-only-new').prop('disabled',false);
	if(!$('#tid-table-main .recent').length){
		$('#tid-show-only-new').prop('checked',true).click().prop('disabled',true);;
	}

	$('.tid-genre-title').click(function(){
		var new_tab=$('.tid-table-'+$(this).children().data('genre'));
		if(new_tab.length){
			$('.tid-table').hide();
			new_tab.show();
		}
	});

	$('.tid-table').each(function(){
		$(this).addClass('tid-table-outer').wrapInner('<div class="tid-table-inner">');
	})
	$('.tid-table th').html(function(index,val){
		return val+' <div>'+val+'</div>';
	})
})