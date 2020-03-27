<?php
  session_start();
  require('./dbconnect.php');

  /* 会員登録画面 */
  if (!empty($_POST) && $_POST['btn_check'] === "登録") {
    echo "ok!";
    $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $chech_user_info_statement = $db->prepare('SELECT name FROM members WHERE name=?');
    $chech_user_info_statement->execute(array(
      $_POST['name'],
    ));

    $chech_user_info = $chech_user_info_statement->fetch();

    if (empty($chech_user_info['name'])) {
      $regist_statement = $db->prepare('INSERT INTO members SET name=?, password=?');
        $regist_statement->execute(array(
        $_POST['name'],
        $hash
      ));

      $chech_user_info_statement = $db->prepare('SELECT id, name FROM members WHERE name=?');
      $chech_user_info_statement->execute(array(
        $_POST['name'],
      ));
      $chech_user_info = $chech_user_info_statement->fetch();

      $_SESSION['user_info'] = $chech_user_info;

      header('Location: task.php');
      exit();
    } else {
      $error['regist'] = 'error';
    }
  }



  /* ログイン画面 */
  if (!empty($_POST) && $_POST['btn_check'] === "ログイン") {
    $user_info_statement = $db->prepare('SELECT * FROM members WHERE name=?');
    $user_info_statement->execute(array(
      $_POST['name'],
    ));
    
    $user_info = $user_info_statement->fetch();

    if (password_verify($_POST['password'], $user_info['password'])) {
      $_SESSION['user_info'] = $user_info;
      header('Location: task.php');
      exit();
    } else {
      $error['login'] = 'error';
    }
  }


  if (empty($_SESSION['user_info']) && (!isset($_REQUEST['login']) && !isset($_REQUEST['regist']))) {
    header('Location: task.php?login');
    exit();
  } else {
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


  $now_hour = date('H', strtotime('+0 day'));

  /* タスクの登録 */
  if (!empty($_POST) && $_POST['btn_check'] === "タスクを追加") {
    
    $date = date('Y-m-d', strtotime('+1 day'));
    $start_time = $date.' '.$_POST['start_time'];
    $end_time = date('Y-m-d H:i', strtotime($start_time.'+'.$_POST['task_interval'].'minutes'));

    $create_task = $db->prepare('INSERT INTO logs SET title=?, description=?, starttime=?, endtime=?, fraction=?, day=NOW(), do=0, member_id=?');
    $create_task->execute(array(
      $_POST['task_title'],
      $_POST['task_description'],
      $start_time,
      $end_time,
      $_POST['task_interval'],
      $_SESSION['user_info']['id']
    ));

    header('Location: task.php');
    exit();
  }

  /* タスクの削除 */
  if (!empty($_POST) && $_POST['btn_check'] === '削除' && strtotime(date('Y-m-d H:i')) < strtotime($_POST['task_start_time'])) {
    $delete_task = $db->prepare('DELETE FROM logs WHERE id=? AND member_id=?');
    $delete_task->execute(array(
      $_POST['task_id'],
      $_SESSION['user_info']['id']
    ));

    header('Location: task.php');
    exit();
  }
?>
<!DOCTYPE html>
<html lang='ja'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  
  <title>Morning To Do【タスク】</title>
  <meta name="description" content="朝用のTo Do リストアプリ">
  <meta name="keywords" content="朝活, TODOリスト, タイマー">
  <link rel="apple-touch-icon" href="images/icons/icon1.png">

  <!-- css -->
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="./css/style.css">
  <link rel="stylesheet" type="text/css" href="./css/modal.css">
  
  <!-- javascript -->
  <script src="./js/jquery-3.4.1.min.js"></script>
  
  <!-- favicon -->
  <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico">

  <!-- manifest.json -->
  <link rel="manifest" href="./manifest.json">
</head>

<body>

<!-- コンテンツ -->
  <main>
    <!-- 会員登録画面 -->
    <?php if (empty($_SESSION['user_info']) && isset($_REQUEST['regist']) && !isset($_REQUEST['login'])): ?>
    <div class="user_regist_modal modal_base">
      <div class="user_regist_box">
        <form action="" method="POST">
          <input type="hidden" name="btn_check" value="">
          <h4>会員登録</h4>
          <?php if ($error['regist'] === 'error'): ?>
          <p class="error">＊このユーザー名はすでに登録されています</p>
          <?php endif ?>
          <label for="name">ユーザー名</label>
          <input type="text" name="name" id="name" required>
          <label for="password">パスワード</label>
          <input type="password" name="password" id="password" required>
          <button type="submit">登録</button>
          <p class="login_btn">ログインは<a href="task.php?login">こちら</a></p>
        </form>
      </div>
    </div>
    <?php endif ?>


    <!-- ログイン -->
    <?php if (empty($_SESSION['user_info']) && isset($_REQUEST['login']) && !isset($_REQUEST['regist'])): ?>
    <div class="user_login_modal modal_base">
      <div class="user_login_box">
        <form action="" method="POST">
          <input type="hidden" name="btn_check" value="">
          <h4>会員情報の入力</h4>
          <?php if ($error['login'] === 'error'): ?>
          <p class="error">＊入力情報に誤りがあります</p>
          <?php endif ?>
          <label for="name">ユーザー名</label>
          <input type="text" name="name" id="name" value="<?php echo $_POST['name'] ?>">
          <label for="password">パスワード</label>
          <input type="password" name="password" id="password">
          <button type="submit">ログイン</button>
          <p class="regist_btn">会員登録は<a href="task.php?regist">こちら</a></p>
        </form>
      </div>
    </div>
    <?php endif ?>


    <!-- タスク作成 -->
    <div class="create_task_modal modal_base">
        <div class="create_task_box">
          <p><i class="fas fa-plus"></i></p>
          <form action="" method="POST">
            <input type="hidden" name="btn_check" value="">
            <input type="text" name="task_title" id="task_title" placeholder="タスク名" required>
            <div class="task_time_setting">
              <div class="time input_start_time">
                <input type="time" min="04:00" max="09:00" name="start_time" id="start_time" placeholder="開始時刻" value="06:00" required>
                <span>から</span>
              </div>
              <div class="time input_interval">
                <input type="number" min=1 max=30 name="task_interval" id="task_interval" value="10" required>
                <span>分間</span>
              </div>
            </div>
            <textarea name="task_description" placeholder="コメント" maxlength=100></textarea>
            <button type="submit">タスクを追加</button>
          </form>
        </div>
    </div>


    <!-- タスクをクリックして詳細を確認する -->
    <div class="task_info_modal modal_base">
        <div class="task_info_box">
            <p><i class="fas fa-plus"></i></p>
            <div class="task_info">
                <h3></h3>
                <span></span>
                <textarea readonly></textarea>
            </div>
        </div>
    </div>

    <!-- タスク一覧 -->
    <div class="container">
      <?php if (!empty($tasks)): ?>
        <h3 class="aleat_function">右にスワイプしてタスクを削除できます >></h3>
      <?php foreach($tasks as $task): ?>
        <div class="task_list">
          <span class="before_tag"></span>
          <form action="" method="POST" name="delete_task">
            <input type="hidden" name="btn_check" value="">
            <input type="hidden" name="task_id" value="<?php echo $task['id'] ?>">
            <input type="hidden" name="task_start_time" value="<?php echo $task['starttime'] ?>">
          </form>
            <h3><?php echo $task['title']; ?></h3>
            <h4><?php echo substr($task['starttime'], -8, -3); ?> ~　<span><?php echo $task['fraction']; ?>分間</span></h4>
            <p><?php echo $task['description']; ?></p>
        </div>
      <?php endforeach ?>
      <?php else: ?>
        <h3 class="no_task">タスクがありません</h3>
        <?php if ($now_hour < 18): ?>
          <p class="no_task_outoftime">タスクの追加は18:00以降からできます</p>
        <?php endif ?>
      <?php endif ?>

      <div class="task_count">
        <div class="child_num"><?php echo count($tasks) ?></div>
        <hr>
        <div class='parent_num'>3</div>
      </div>

        <?php if ($now_hour/*  >= 18 */): ?>
          <div class="add_task_btn">
            <i class="fas fa-plus"></i>
          </div>
        <?php endif ?>
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
<script src="./js/task.js"></script>
<script src="./js/modal.js"></script>
<script src="./js/delete_task.js"></script>
</html>