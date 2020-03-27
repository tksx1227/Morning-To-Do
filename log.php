<?php
  session_start();

  if (empty($_SESSION['user_info']) && (!isset($_REQUEST['login']) && !isset($_REQUEST['regist']))) {
    header('Location: task.php?login');
    exit();
  } else {
    require('./dbconnect.php');

    $my_tasks_statement = $db->prepare("SELECT DATE_FORMAT(endtime, '%Y-%m-%d') AS time, COUNT(*) AS count FROM logs WHERE member_id=? AND do>0 GROUP BY DATE_FORMAT(endtime, '%Y%m%d') LIMIT 365");
      $my_tasks_statement->execute(array(
      $_SESSION['user_info']['id']
    ));
    $tasks_date_count = $my_tasks_statement->fetchall();

    if(isset($_COOKIE['PHPSESSID']) && $_COOKIE['PHPSESSID'] == session_id()){
      //現在のセッションIDを 新しいものと置き換えます
      session_regenerate_id(TRUE);
    }
  }

  $task_date_list = [];
  $task_count_list = [];
  foreach($tasks_date_count as $task) {
    array_push($task_date_list, $task['time']);
    array_push($task_count_list, $task['count']);
  }

  function getMonthRange($startUnixTime, $endUnixTime = null) {
    if ($endUnixTime === null) {
        $endUnixTime = time();
    }
    $ymList = array();
    for ($utime = $startUnixTime; $utime < $endUnixTime; $utime = strtotime('+1 day', $utime)) {
        $ymList[] = date('Y-m-d', $utime);
    }
    return $ymList;
  }

  $today = time();
  $one_year_before = date(strtotime("-1 year"));
  $date_list = getMonthRange($one_year_before, $today);
?>
<!DOCTYPE html>
<html lang='ja'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Morning To Do【ログ】</title>
  <meta name="description" content="朝用のTo Do リストアプリ">
  <meta name="keywords" content="朝活, TODOリスト, タイマー">

  <!-- css -->
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="./css/style.css">
  
    <!-- javascript -->
  <script src="./js/jquery-3.4.1.min.js"></script>
  <script src="./js/grass.js"></script>
  <!-- favicon -->
  <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">

  <!-- manifest.json -->
  <link rel="manifest" href="./manifest.json">
</head>

<body>

<!-- コンテンツ -->
  <main>
    <div class="container">
      <div class="log_grass">
          <div class="grass_warpper">
              <?php foreach($date_list as $index => $date): ?>
                <?php if (in_array($date, $task_date_list)): ?>
                  <div class="block grass<?php echo $task_count_list[array_search($date, $task_date_list)] ?>" data-date='<?php echo $date; ?>'></div>
                <?php else: ?>
                  <div class="block grass0" data-date='<?php echo $date; ?>'></div>
                <?php endif ?>
              <?php if (($index+1) % 14 == 0): ?>
              <div class="clear"></div>
            <?php endif ?>
            <?php endforeach ?>
            <div class="clear"></div>
          </div>
          <div class="logout_btn">
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
    </div>
          <div class="grass_desc">
            <ul>
              <li style="background-color: #ebedf0" class="sample_grass first_li"></li>
              <li style="background-color: #c6e48b" class="sample_grass"></li>
              <li style="background-color: #4da140" class="sample_grass"></li>
              <li style="background-color: #196127" class="sample_grass lask_li"></li>
            </ul>
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

</html>