/* タスク追加ボタンの色の調整 */

$(function() {
    task_num = $('.task_list').length

    if (task_num >= 3) {
        $('.add_task_btn i').css({
            'transform': 'rotate(45deg)',
            'color': 'rgb(255, 98, 98)',
            'box-shadow': '0 0 3px 2px rgb(255, 98, 98)'
        })
        $('.child_num').css('color', 'rgb(255, 98, 98)')
    } else {
        $('.add_task_btn i').css({
            'transform': 'rotate(0)',
            'color': 'rgb(98, 179, 255)',
            'box-shadow': '0 0 3px 2px rgb(98, 179, 255)'
        })
        $('.child_num').css('color', 'rgb(98, 179, 255)')
    }
})