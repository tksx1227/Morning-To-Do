/* モーダル関係のコード */

$(function() {
    $('button').click(function() {
        let text = $(this).text()
        $(this).parent().find('input').eq(0).val(text)
    })

    // タスク確認モーダルの操作
    $('.task_info_box p').click(function() {
        $('.task_info_modal').fadeOut('fast')
    });
    $('.task_list').click(function() {
        let task_title = $(this).find('h3').text()
        let task_interval = $(this).find('h4').text()
        let task_description = $(this).find('p').text()

        $('.task_info').find('h3').text(task_title)
        $('.task_info').find('span').text(task_interval)
        $('.task_info').find('textarea').text(task_description)
        console.log(task_title)
        $('.task_info_modal').fadeIn('fast')
    })

    // タスク追加モーダルの
    task_num = $('.task_list').length
    if (task_num <= 2) {
        $('.add_task_btn i').click(function () {
            $('.create_task_modal').fadeIn('fast')
        })
    }
    $('.create_task_box p').click(function() {
        $('.create_task_modal').fadeOut('fast')
    })
})