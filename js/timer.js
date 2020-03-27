/* タイマーのカウントダウンなど */

let now_time
let task_end_time
let task_start_time
let task_id
let interval = 1000

// リロードするための関数
function reload() {
    location.reload()
}

// １桁の数字の頭にゼロを追加する関数
function addzero(num) {
    if (num > 9) {
        return num
    } else {
        return '0' + num
    }
}

// タスク終了画面
function finish_display() {
    var Canvas = document.getElementById('canvas');
    var ctx = Canvas.getContext('2d');

    var resize = function () {
        Canvas.width = Canvas.clientWidth;
        Canvas.height = Canvas.clientHeight;
    };
    window.addEventListener('resize', resize);
    resize();

    var elements = [];
    var presets = {};

    presets.o = function (x, y, s, dx, dy) {
        return {
            x: x,
            y: y,
            r: 12 * s,
            w: 5 * s,
            dx: dx,
            dy: dy,
            draw: function (ctx, t) {
                this.x += this.dx;
                this.y += this.dy;

                ctx.beginPath();
                ctx.arc(this.x + + Math.sin((50 + x + (t / 10)) / 100) * 3, this.y + + Math.sin((45 + x + (t / 10)) / 100) * 4, this.r, 0, 2 * Math.PI, false);
                ctx.lineWidth = this.w;
                ctx.strokeStyle = '#fff';
                ctx.stroke();
            }
        }
    };

    presets.x = function (x, y, s, dx, dy, dr, r) {
        r = r || 0;
        return {
            x: x,
            y: y,
            s: 22 * s,
            w: 5 * s,
            r: r,
            dx: dx,
            dy: dy,
            dr: dr,
            draw: function (ctx, t) {
                this.x += this.dx;
                this.y += this.dy;
                this.r += this.dr;

                var _this = this;
                var line = function (x, y, tx, ty, c, o) {
                    o = o || 0;
                    ctx.beginPath();
                    ctx.moveTo(-o + ((_this.s / 2) * x), o + ((_this.s / 2) * y));
                    ctx.lineTo(-o + ((_this.s / 2) * tx), o + ((_this.s / 2) * ty));
                    ctx.lineWidth = _this.w;
                    ctx.strokeStyle = c;
                    ctx.stroke();
                };

                ctx.save();

                ctx.translate(this.x + Math.sin((x + (t / 10)) / 100) * 20, this.y + Math.sin((10 + x + (t / 10)) / 100) * 20);
                ctx.rotate(this.r * Math.PI / 180);

                line(-1, -1, 1, 1, '#fff');
                line(1, -1, -1, 1, '#fff');

                ctx.restore();
            }
        }
    };

    for (var x = 0; x < Canvas.width; x++) {
        for (var y = 0; y < Canvas.height; y++) {
            if (Math.round(Math.random() * 8000) == 1) {
                var s = ((Math.random() * 5) + 1) / 10;
                if (Math.round(Math.random()) == 1)
                    elements.push(presets.o(x, y, s, 0, 0));
                else
                    elements.push(presets.x(x, y, s, 0, 0, ((Math.random() * 3) - 1) / 10, (Math.random() * 360)));
            }
        }
    }

    setInterval(function () {
        ctx.clearRect(0, 0, Canvas.width, Canvas.height);

        var time = new Date().getTime();
        for (var e in elements)
            elements[e].draw(ctx, time);
    }, 10);
}


// カウントダウン関連
function timer_count() {
    now_time = moment().format('YYYY-MM-DD HH:mm:ss')
    console.log(now_time)
    console.log(task_start_time)
    console.log(task_end_time)
    if (moment(now_time).isBetween(task_start_time, task_end_time, null, true)) {
        if ($('.out_of_time_mask').css('display') == 'block') {
            $('.out_of_time_mask').css('display', 'none')
        }
        
        diff_m = moment(task_end_time).diff(moment(), 'm')
        diff_s = moment(task_end_time).diff(moment(), 's')
        $('.minutes p').text(addzero(diff_m))
        $('.second p').text(addzero(diff_s % 60))

        if (diff_s <= 0) {
            setTimeout(reload, 5000)
            $('.finish_task_display').fadeIn('slow')
            $('.finish_task_display h3').fadeIn('slow')
            $('.finish_task_display h3').css('transform', 'translateY(-60px)')
            finish_display()
        } else {
            setTimeout(timer_count, interval)
        }
    } else {
        diff_m2 = moment(task_start_time).diff(moment(), 'm')
        diff_s2 = moment(task_start_time).diff(moment(), 's')

        if ($('.out_of_time_mask').css('display') == 'none') {
            $('.out_of_time_mask').css('display', 'block')
        }

        if (diff_m2 < 10 && diff_s2 >= 0) {
            $('.post p').eq(1).text(addzero(diff_m2) + ':' + addzero(diff_s2 % 60))
            setTimeout(timer_count, interval)
        } else {
            if (diff_s2 >= 1) {
                setTimeout(reload, diff_s2 * 1000)
            }
            $('.post p').eq(0).hide()
            $('.post p').eq(1).hide()
            $('.post h3').css('line-height', '260px')
        }
    }
}

$(function() {
    if ($('.attend_task').find('button')) {

        $('button').click(function () {
            now_time = moment().format('YYYY-MM-DD HH:mm:ss')
            $(this).parent().find('input').eq(1).val(now_time)
            $(this).parent().find('input').eq(2).val(task_end_time)
            $(this).parent().find('input').eq(3).val(task_id)

            let text = $(this).text()
            $(this).parent().find('input').eq(0).val(text)
        })
    }
    if ($('.datasets input').length > 0) {
        let task_data = $('.datasets input').eq(0).data()
        let task_title = task_data['task_name']
        task_id = task_data['task_id']
        // let task_interval = task_data['task_interval']
        task_start_time = moment(task_data['task_start_time']).format('YYYY-MM-DD HH:mm')
        task_end_time = moment(task_data['task_end_time']).format('YYYY-MM-DD HH:mm')
        $('.task_name h2').text(task_title)
        timer_count()
    } else {
        $('.out_of_time_mask').css('display', 'block')
        $('.post p').eq(0).hide()
        $('.post p').eq(1).hide()
        $('.post h3').text('NO TASKS.')
        $('.post h3').css('line-height', '260px')
    }
})