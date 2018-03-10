<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <title>TID stats</title>
    </head>
    <body>
        <img src="img" id="img"/>
        <br/>
        <button id="regenerate">regenerate</button>

        <script src="../lib/jquery.js"></script>
        <script>
            $(function(){
                $('#regenerate').click(function(){
                    var $this=$(this);
                    $this.prop('disabled',true);
                    $.ajax({
                        url:'cache',
                        success:function(){
                            $this.prop('disabled',false);
                            $('#img').attr('src','img?'+Math.floor(Math.random()*100));
                        }
                    })
                })
            })
        </script>
    </body>
</html>
