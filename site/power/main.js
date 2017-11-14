$(function(){
	var join_number=3;
	$('td[rowspan]').each(function(){
		var $this=$(this),
			i,
			$row=$(this).parent(),
			rowspan=$this.attr('rowspan')*1,
			first_element=[];
		for(i=0;i<join_number;i++)
			first_element.push($row.children().eq(i+1));
		var should_join=[first_element];
		for(i=1;i<rowspan;i++){
			$row=$row.next();
			var el=[];
			for(j=0;j<join_number;j++)
				el.push($row.children().eq(j));
			should_join.push(el);
		}
		var info=['',''];
		should_join.forEach(function(list){
			for(i=0;i<join_number;i++)
				info[i]=info[i]||list[i].text().trim();
		});
		for(i=0;i<join_number;i++)
			should_join[0][i].text(info[i]).attr('rowspan',rowspan);
		for(i=1;i<should_join.length;i++){
			for(j=0;j<join_number;j++)
				should_join[i][j].remove();
		}
	});

	$('#refresh').click(function(){
		$.ajax({
			url:'refresh.php',
			complete:function(data){
				console.log('refresh.php response:',data);
				var text=data.responseText,
					type;
				switch(text){
					case 'cache doesn\'t exists':
							type='warning';
						break;
					case 'can not delete cache':
							type='error';
						break;
					case 'cache removed':
							type='success';
						break;
					default:
							type='error';
						break;
				}
				sweetAlert({type:type,title:text,text:'page will be refreshed after 3 seconds'});
				setTimeout(function(){
					window.location.href=window.location.href;
				},3000);
			}
		})
	})
})