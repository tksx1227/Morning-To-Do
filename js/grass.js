/* 端末別での草のサイズ調整 */

$(function() {
    let width = $(window).width()
    let height = $(window).height()

    if (width <= 380) {
        if (height > 800)
            $('.grass_warpper').css('transform', 'scale(1.2)');
        else if(height > 660)
            $('.grass_warpper').css('transform', 'scale(1.05)');
        else if (height > 630)
            $('.grass_warpper').css('transform', 'scale(1.0)');
        else if (height > 550)
            $('.grass_warpper').css('transform', 'scale(0.9)');
    } else {
        if (height < 740)
            $('.grass_warpper').css('transform', 'scale(1.15)');
        else if (height < 830)
            $('.grass_warpper').css('transform', 'scale(1.2)');
    }
})