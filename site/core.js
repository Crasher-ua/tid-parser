var labels_to_remove=[],
	button='<a><i class="remove glyphicon glyphicon-remove glyphicon-white"></i></a> ';

function build_remove_list(){
	console.log('building');
	var box=$('#labels-to-remove');
	box.hide();
	if(labels_to_remove.length){
		var labels_with_buttons=[];
		for(var i=0;i<labels_to_remove.length;i++)
			labels_with_buttons.push('<span class="mlabel label label-default tag" data-id="'+i+'" data-name="'+labels_to_remove[i]+'">'+labels_to_remove[i]+button+'</span>');
		box.show().children().html(labels_with_buttons.join(' '));
		$('.list .mlabel .remove').click(function(){
			var label=$(this).closest('.mlabel');
			labels_to_remove.splice(label.data('id')*1,1);
			show_label(label.data('name'));
			remember_cookie();
			label.remove();
			if(!$('.list .mlabel').length)
				box.hide();
		});
	}
}

function operate_label(label,method){
	$('table .mlabel').filter(function(){
		return $(this).children('.name').text()==label;
	}).closest('tr')[method]();
	return label;
}
function hide_label(label){
	return operate_label(label,'hide');
}
function show_label(label){
	return operate_label(label,'show');
}

function remember_cookie(){
	$.cookie('labels-to-remove',labels_to_remove.join(','));
}

$(function(){
	$('table .mlabel').wrapInner('<span class="name"/>').append(button);

	var labels=$.cookie('labels-to-remove');
	if(typeof(labels)!='undefined' && labels.length){
		labels_to_remove=labels.split(',');
		for(var i=0;i<labels_to_remove.length;i++){
			hide_label(labels_to_remove[i]);
		}
		build_remove_list();
	}

	$('table .mlabel .remove').click(function(){
		labels_to_remove.push(hide_label($(this).closest('.mlabel').children('.name').text()));
		remember_cookie();
		build_remove_list();
	});

	$('#show-only-new').click(function(){
		var checked=$(this).is(':checked');
		$.cookie('only-new',checked);
		$('table')[checked ? 'addClass' : 'removeClass']('only-new');
	});
	var only_new=$.cookie('only-new');
	if(typeof(only_new)!='undefined' && only_new=='true'){
		$('#show-only-new').prop('checked',true);
		$('table').addClass('only-new');
	}
})