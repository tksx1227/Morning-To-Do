<?php
  session_start();

  if (empty($_SESSION['user_info']) && (!isset($_REQUEST['login']) && !isset($_REQUEST['regist']))) {
    header('Location: task.php?login');
    exit();
  } else {
    require('./dbconnect.php');

    $my_tasks_statement = $db->prepare('SELECT * FROM logs WHERE member_id=? AND endtime>NOW()  ORDER BY starttime ASC limit 3');
      $my_tasks_statement->execute(array(
      $_SESSION['user_info']['id']
    ));
    $tasks = $my_tasks_statement->fetchall();

    if(isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID'] == session_id()){
      //現在のセッションIDを 新しいものと置き換えます
      session_regenerate_id(TRUE);
    }
  }

  if (!empty($_POST) && $_POST['btn_check'] === "開始") {
    $time_diff = strtotime($_POST['end_time']) - strtotime($_POST['attend_time']);
    setcookie('task_attend_flag', 'attended', time()+$time_diff, $secure=TRUE);
    $attend_statement = $db->prepare('UPDATE logs SET do=1 WHERE id=?');
    $attend_statement->execute(array(
      $_POST['task_id']
    ));

    header('Location: timer.php');
    exit();
  }

?>
<!DOCTYPE html>
<html lang='ja'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Morning To Do【タイマー】</title>
  <meta name="description" content="朝用のTo Do リストアプリ">
  <meta name="keywords" content="朝活, TODOリスト, タイマー">

  <!-- css -->
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="./css/style.css">
  
  <!-- javascript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js" type="text/javascript"></script>
  <script src="./js/jquery-3.4.1.min.js"></script>
  
  <!-- favicon -->
  <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">

  <!-- manifest.json -->
  <link rel="manifest" href="./manifest.json">
</head>

<body>
<!-- コンテンツ -->
  <div class="datasets">
    <?php if (!empty($tasks)): ?>
    <?php foreach($tasks as $task): ?>
      <input type='hidden' data-task_id='<?php echo $task['id'] ?>' data-task_name='<?php echo $task['title'] ?>', data-task_start_time='<?php echo $task['starttime'] ?>', data-task_interval='<?php echo $task['fraction'] ?>', data-task_end_time='<?php echo $task['endtime'] ?>'>
    <?php endforeach ?>
    <?php endif ?>
  </div>

  <div class="out_of_time_mask">
      <div class="post">
        <h3>時間外だよ</h3>
        <p>次のタスクまで</p>
        <p></p>
      </div>
  </div>

  <div class="finish_task_display">
      <canvas id="canvas"></canvas>
      <h3>タスク達成</h3>
  </div>

  <main>
    <div class="container">
      <div class="timer_box">
        <div class="task_name">
            <h2></h2>
        </div>
        <p class="font">あと</p>
        <div class="count_down">
            <div class="minutes timer">
                <p>00</p>
            </div>
            <p class='timer_colon'>:</p>
            <div class="second timer">
                <p>00</p>
            </div>
        </div>
        <div class="attend_task">
          <form action="" method="POST">
            <input type="hidden" name="btn_check" value="">
            <input type="hidden" name="attend_time" value="">
            <input type="hidden" name="end_time" value="">
            <input type="hidden" name="task_id" value="">
            <?php if ($_COOKIE['task_attend_flag'] === 'attended'): ?>
              <p class="dummy_attend_btn">開始</p>
            <?php else: ?>
              <button type="submit">開始</button>
            <?php endif ?>
          </form>
        </div>
      </div>
    </div>
  </main>
    
  <footer class="footer">
    <a href='task.php' class='task_btn footer_btn'>
      <i class="fas fa-tasks"></i>
    </a>
    <a href='timer.php' class='home_btn footer_btn'>
      <i class="fas fa-stopwatch"></i>
    </a>
    <a href='log.php' class='log_btn footer_btn'>
      <i class="far fa-calendar-alt"></i>
    </a>
  </footer>  
<!-- コンテンツの終わりです -->

<!-- ServiceWorker -->
<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('./serviceworker.js').then(function(registration) {
    // Serviceworkerが機能出来ていると、開発者ツールのログは表示されるよ。アプリ上には表示されません。
    console.log('ServiceWorker registration successful with scope: ', registration.scope);
  }).catch(function(err) {
    // Serviceworkerが機能できなかった時に、開発者ツールのログでerror表示がされます。どこかにエラーが出ているから確認してください。
    console.log('ServiceWorker registration failed: ', err);
  });
}
</script>
</body>
<script src="./js/timer.js"></script>
</html>