<?php

require 'password.php';   // password_hash()はphp 5.5.0以降の関数のため、バージョンが古くて使えない場合に使用
// セッション開始
session_start();

$db['host'] = getenv('hostname');  // DBサーバのURL
$db['user'] = getenv('username');  // ユーザー名
$db['pass'] = getenv('password');  // ユーザー名のパスワード
$db['dbname'] = getenv('dbname');  // データベース名



// エラーメッセージ、登録完了メッセージの初期化
$errorMessage = "";
$signUpMessage = "";

// ログインボタンが押された場合
if (isset($_POST["signUp"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["username"])) {  // 値が空のとき
        $errorMessage = 'ユーザーIDが未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    } else if (empty($_POST["password2"])) {
        $errorMessage = 'パスワードが未入力です。';
    }

    if (!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["password2"]) && $_POST["password"] === $_POST["password2"]) {
        // 入力したユーザIDとパスワードを格納
        $username = $_POST["username"];
        $password = $_POST["password"];

        // 2. ユーザIDとパスワードが入力されていたら認証する
        $dsn = sprintf('mysql:host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);
        // $dsn = sprintf('mysql:charset=utf8', $db['host'], $db['dbname'], $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
            var_dump(123);
            // var_dump($db['user']);die;
            $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            var_dump($pdo);die;

            $stmt = $pdo->prepare("INSERT INTO userData(name, password) VALUES (?, ?)");
            
            $stmt->execute(array($username, password_hash($password, PASSWORD_DEFAULT)));  // パスワードのハッシュ化を行う（今回は文字列のみなのでbindValue(変数の内容が変わらない)を使用せず、直接excuteに渡しても問題ない）
            $userid = $pdo->lastinsertid();  // 登録した(DB側でauto_incrementした)IDを$useridに入れる
            
            $signUpMessage = '登録が完了しました。あなたのユーザーネームは ' . $username . ' です。パスワードは ' . $password . ' です。';  // ログイン時に使用するIDとパスワード
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            echo $e->getMessage();
        }
    } else if ($_POST["password"] != $_POST["password2"]) {
        $errorMessage = 'パスワードに誤りがあります。';
    }
}
?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>新規登録</title>
</head>

<body>
    <div id="Signup">
        <?php include('header.php') ?>
        <div class="container-wrapper gazo2">
            <div class="container">
                <main>
                    <!-- <h1>新規登録フォーム</h1> -->
                    <form id="loginForm" name="loginForm" action="" method="POST">
                        <fieldset>
                            <legend>新規登録フォーム</legend>
                            <div>
                                <font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font>
                            </div>
                            <div>
                                <font color="#0000ff"><?php echo htmlspecialchars($signUpMessage, ENT_QUOTES); ?></font>
                            </div>
                            <label for="username">ユーザー名     </label><input type="text" id="username" name="username" placeholder="ユーザー名を入力" value="<?php if (!empty($_POST["username"])) {
                                                                                                                                                    echo htmlspecialchars($_POST["username"], ENT_QUOTES);
                                                                                                                                                } ?>">
                            <br>
                            <label for="password">パスワード     </label><input type="password" id="password" name="password" value="" placeholder="パスワードを入力">
                            <br>
                            <label class="password2" for="password2">パスワード(確認用)</label><input type="password" id="password2" name="password2" value="" placeholder="再度パスワードを入力">
                            <br><br><br><br>
                            <!-- <input type="submit" id="signUp" name="signUp" value="新規登録"> -->
                            <button  id="signUp" name="signUp" type="submit" class="btn-shine">新規登録</button>
                        </fieldset>
                    </form>
                    <br>
                    <form action="Login.php">
                        <!-- <input type="submit" value="戻る"> -->
                        <button  id="back" type="submit" class="btn-shine">戻る</button>
                    </form>
                </main>
            </div>
        </div>
        <?php include('footer.php') ?>
    </div>
</body>

</html>