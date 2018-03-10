var MODE,
    mode_changed=false,
    empty_number=0;

$(function(){
    $el=$('#max-empty,#log-size');

    $el.slider({
        ticks:[0,10,50,100,200,500,1000],
        // ticks_labels:['$0','$100','$200','$300','$400'],
        ticks_snap_bounds:10
    });
    $('#wait-time').slider({
        ticks:[30,60,100,120,600,1000],
        ticks_snap_bounds:10
    });
    $el.add('#wait-time').on('slide',function(slideEvt){
        $('#'+$(this).data('val-id')).text(slideEvt.value);
    });
    $el.add('#wait-time').each(function(){
        var $this=$(this);
        $('#'+$this.data('val-id')).text($this.val());
    });

    $('#mode .btn').click(function(){
        $('#mode .btn').removeClass('active');
        MODE=$(this).addClass('active').data('value');
        if(!$('#start,#stop,#fire').filter('.active').length){
            $('#offset').text(0);
        }else{
            mode_changed=true;
        }
        console.log(MODE);
    }).filter('.active').click();

    $('#start').click(function(){
        request();
        $(this).addClass('active');
        $('#stop').removeClass('disabled');
        $('#fire').addClass('disabled');
        $('#status').text('ongoing');
    });
    $('#stop').click(function(){
        $(this).addClass('active');
        $('#start').removeClass('active');
        $('#status').text('stopped, waiting response');
    })
    $('#fire').click(function(){
        request();
        $(this).addClass('active');
        $('#start').addClass('disabled');
        $('#status').text('fired, waiting response');
    })
})

function reset_time(){
    $('#time').text(function(index,val){
        $('#last-time').text(val);
        return '0';
    });
    $('#last-time-holder').show();
}

function return_value(){
    if(mode_changed){
        $('#offset').text(0);
        mode_changed=false;
    }else{
        $('#offset').text(function(index,val){
            return val*1+1;
        });
    }

    var fire=$('#fire'),
        start=$('#start'),
        stop=$('#stop');
    if(fire.is('.active')){
        fire.removeClass('active');
        start.removeClass('disabled');
        reset_time();
        $('#status').text('fired, got response');
    }else{
        if(stop.is('.active')){
            stop.removeClass('active').addClass('disabled');
            fire.removeClass('disabled');
            reset_time();
            $('#status').text('stopped, standby');
        }else{
            var max_empty_responses=$('#max-empty').val()*1;
            if(max_empty_responses && empty_number==max_empty_responses && $('#mode-incremental').is('.active')){
                $('#mode-recheck').click();
                log('Dropped to recheck holes mode, because limit='+max_empty_responses+' reached');
            }
            reset_time();
            request();
        }
    }
}

function request(){
    mode_changed=false;
    $('#requests-sent').text(function(index,val){
        return val*1+1;
    });
    $.ajax({
        url:'core.php',
        data:{
            'mode':     MODE,
            // 'drop':      $('#scan-drop').val(),
            'offset':   $('#offset').text()
        },
        success:function(data){
            try{
                var response=eval(data);
                $('#success-number').text(function(index,val){
                    return val*1+response.success_number;
                });
                function list(list){
                    if(!list.length)return '';
                    var urls=list.split(','),
                        i;
                    for(i=0;i<urls.length;i++){
                        urls[i]='<a href="'+urls[i]+'">'+/\/(\d+)\.html/.exec(urls[i])[1]+'</a>';
                    }
                    return urls.join(', ');
                }
                if(response.success_number)
                    empty_number=0;
                else
                    empty_number++;
                log(response.success_number+' ('+list(response.success_urls)+' / '+list(response.all_urls)+'), max='+response.max);
                if(response.offset_delta){
                    $('#offset').text(function(index,val){
                        return val*1+response.offset_delta-1;
                    });
                }
            }catch(e){
                console.log('error',e,data);
                log('error, logged to console');
            }
            return_value();
        }
    });

    $('#clean-log').click(function(){
        $('#log').empty();
    });
}

setInterval(function(){
    $('#time').text(function(index,val){
        return $('#start,#fire,#stop').filter('.active').length ? val*1+1 : 0;
    });
    if($('#time').text()*1 > $('#wait-time').val()){
        return_value();
    }
},1000);

function log(str){
    $('#log').prepend('<div>'+str+'</div>');
    var size=$('#log-size').val()*1;
    if(size)
        $('#log>div:gt('+(size-1)+')').remove();
}
