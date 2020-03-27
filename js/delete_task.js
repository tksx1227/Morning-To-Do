/* タスク削除時のコード */

$(function () {
    let startX, startY, diffX, diffY, endX, endY, opacity
    
    $('.task_list').bind('touchstart', function () {
        startX = event.changedTouches[0].pageX;//フリック開始時のX軸の座標
        startY = event.changedTouches[0].pageY;
    });
    $('.task_list').bind('touchmove', function (e) {
        endX = event.changedTouches[0].pageX;//フリック終了時のX軸の座標
        diffX = Math.round(startX - endX);//フリック開始時の座標-終了時の座標=フリックの移動距離
        endY = event.changedTouches[0].pageY;
        diffY = Math.round(startY - endY);

        if (diffX < 0 && diffY > -100 && diffY < 100) {
            opacity = 1 + diffX / 300
            $(this).children('span').css('display', 'none')
            $(this).css('opacity', opacity)
        }
    })
    $('.task_list').bind('touchend', function (e) {
        if (diffX < -150 && diffY > -100 && diffY < 100) {
            $(this).find('input').eq(0).val('削除')
            $(this).find('form').submit()
        } else {
            $(this).children('span').css('display', 'inline-block')
            $(this).css('opacity', 1)
        }
    })
})