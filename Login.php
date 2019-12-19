<?php
require 'password.php';   // password_verfy()はphp 5.5.0以降の関数のため、バージョンが古くて使えない場合に使用
// セッション開始
session_start();

$db['host'] = getenv('hostname');  // DBサーバのURL
$db['user'] = getenv('username');  // ユーザー名
$db['pass'] = getenv('password');  // ユーザー名のパスワード
$db['dbname'] = "fav-gazo";  // データベース名
// $db['dbname'] = getenv('dbname');  // データベース名

// $db['host'] = "heroku_7c1d8e027c03bf7";  // DBサーバのURL
// $db['user'] = "b1ba47a5f731ea";  // ユーザー名
// $db['pass'] = "fc2ba8de";  // ユーザー名のパスワード
// $db['dbname'] = "heroku_7c1d8e027c03bf7";  // データベース名

$db['host'] = "localhost";  // DBサーバのURL
$db['user'] = "root";  // ユーザー名
$db['pass'] = "";  // ユーザー名のパスワード
$db['dbname'] = "loginManagement";  // データベース名

// エラーメッセージの初期化
$errorMessage = "";

// ログインボタンが押された場合
if (isset($_POST["login"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["userid"])) {  // emptyは値が空のとき
        $errorMessage = 'ユーザーIDが未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    }

    if (!empty($_POST["userid"]) && !empty($_POST["password"])) {
        // 入力したユーザIDを格納
        $userid = $_POST["userid"];

        // 2. ユーザIDとパスワードが入力されていたら認証する
        $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
            $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

            $stmt = $pdo->prepare('SELECT * FROM userData WHERE name = ?');
            $stmt->execute(array($userid));

            $password = $_POST["password"];

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $row['password'])) {
                    session_regenerate_id(true);

                    // 入力したIDのユーザー名を取得
                    $id = $row['id'];
                    $sql = "SELECT * FROM userData WHERE id = $id";  //入力したIDからユーザー名を取得
                    $stmt = $pdo->query($sql);
                    foreach ($stmt as $row) {
                        $row['name'];  // ユーザー名
                    }
                    $_SESSION["NAME"] = $row['name'];
                    header("Location: Main.php");  // メイン画面へ遷移
                    exit();  // 処理終了
                } else {
                    // 認証失敗
                    $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
                }
            } else {
                // 4. 認証成功なら、セッションIDを新規に発行する
                // 該当データなし
                $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
            }
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            //$errorMessage = $sql;
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            // echo $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>ログイン</title>
</head>

<body>
    <div id="Login">
        <?php include('header.php') ?>
        <div class="container-wrapper gazo1">
            <div class="container">
                <main>
                    <h1>fav-gazoへようこそ！！</h1>
                    <p>投稿した画像を閲覧できるみんなのフォトギャラリー</p>
                    <div class="main-box">
                        <form id="loginForm" name="loginForm" action="" method="POST">
                            <fieldset>
                                <legend>ログインフォーム</legend>
                                <div>
                                    <font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font>
                                </div>
                                <label for="userid">ユーザー名</label><input type="text" id="userid" name="userid" placeholder="ユーザーIDを入力" value="<?php if (!empty($_POST["userid"])) {
                                                                                                                                                    echo htmlspecialchars($_POST["userid"], ENT_QUOTES);
                                                                                                                                                } ?>">
                                <br>
                                <label for="password">パスワード</label><input type="password" id="password" name="password" value="" placeholder="パスワードを入力">
                                <br><br>
                                <!-- <input type="submit" id="login" name="login" value="ログイン"> -->
                                <button  id="login" name="login" class="btn-shine">ログイン</button>
                            </fieldset>
                        </form>
                        <br>
                        <form action="SignUp.php">
                            <fieldset>
                                <legend>新規登録(無料)はこちら</legend>
                                <!-- <input type="submit" value="新規登録" id="submit"> -->
                                <button  id="submit" type="submit" class="btn-shine">新規登録</button>
                            </fieldset>
                        </form>
                    </div>
                </main>
            </div>
        </div>
        <?php include('footer.php') ?>
    </div>
</body>

</html>