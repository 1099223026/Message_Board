$( document ).ready( function(){
    /* 数据提交到后台，先验证长度 */
    $( '#submit' ).bind( 'click', function () {
        var len = $( '#tesxtArea' ).val().length;
            // 判断用户是否有输入
            if( len < 5 ){
                alert( '最少输入5个！！');
                return false;
            }
    })
    /* 点赞单击事件 */
    var len = $( '.thumb' ).length;
    var thumb = $( '.thumb' );
    for ( var i = 0; i < len; i++ ){
        thumb[ i ].i = i;
        // 设置点赞标识
        thumb[ i ].sign = 0;
        $( thumb[ i ] ).bind( 'click', function () {
            var thumbNum = 0;
            var index = 0;
            if ( this.sign == 0 ){
                // 未点赞操作
                // 获取当前的点赞数
                thumbNum    = $( '.thumb-num' ).eq( this.i ).html();
                // 点赞数在数据库中的位置 = thumb-num元素长度-1-当前元素的下标
                index       = len - 1 - this.i;
                console.log( thumbNum);
                // 赞数加1
                thumbNum++;
                $( '.thumb' ).eq( this.i ).find( '.glyphicon-thumbs-up' ).css( 'color', '#000' );
                $( '.thumb-num' ).eq( this.i ).html( thumbNum );
                this.sign = 1;
            } else {
                // 点赞的操作
                // 获取当前的点赞数
                thumbNum    = $( '.thumb-num' ).eq( this.i ).html();
                // 点赞数在数据库中的位置 = thumb-num元素长度-1-当前元素的下标
                index       = len - 1 - this.i;
                console.log( thumbNum);
                // 赞数减1
                thumbNum--;
                $( '.thumb-num' ).eq( this.i ).html( thumbNum );
                $( '.thumb' ).eq( this.i ).find( '.glyphicon-thumbs-up' ).css( 'color', '#777' );
                this.sign = 0;
            }
            // 用ajax提交到后台
            $.post( "index.php?r=site/thumb-post", { 'thumbNum': thumbNum, 'index': index },function(data,status){
                console.log( "Data: " + data + "nStatus: " + status );
            }, 'json' );
        })
    }

});
