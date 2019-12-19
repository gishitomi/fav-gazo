<?php
session_start();

// ログイン状態チェック
if (!isset($_SESSION["NAME"])) {
    header("Location: Logout.php");
    exit;
}
?>

<?php
try {
    $user = getenv('username');
    $pass = getenv('password');
    $hostname = getenv('hostname');
    $dbname = getenv('dbname');

    $dsn = "mysql:host=$host;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $pass);
    //ファイルアップロードがあったとき
    if (isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']['error']) && $_FILES["upfile"]["name"] !== "") {
        //エラーチェック
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK: // OK
                break;
            case UPLOAD_ERR_NO_FILE:   // 未選択
                throw new RuntimeException('ファイルが選択されていません', 400);
            case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
                throw new RuntimeException('ファイルサイズが大きすぎます', 400);
            default:
                throw new RuntimeException('その他のエラーが発生しました', 500);
        }

        //画像・動画をバイナリデータにする．
        $raw_data = file_get_contents($_FILES['upfile']['tmp_name']);

        //拡張子を見る
        $tmp = pathinfo($_FILES["upfile"]["name"]);
        $extension = $tmp["extension"];
        if ($extension === "jpg" || $extension === "jpeg" || $extension === "JPG" || $extension === "JPEG") {
            $extension = "jpeg";
        } elseif ($extension === "png" || $extension === "PNG") {
            $extension = "png";
        } elseif ($extension === "gif" || $extension === "GIF") {
            $extension = "gif";
        } elseif ($extension === "mp4" || $extension === "MP4") {
            $extension = "mp4";
        } else {
            echo "非対応ファイルです．<br/>";
            echo ("<a href=\"index.php\">戻る</a><br/>");
            exit(1);
        }

        //DBに格納するファイルネーム設定
        //サーバー側の一時的なファイルネームと取得時刻を結合した文字列にsha256をかける．
        $date = getdate();
        $fname = $_FILES["upfile"]["tmp_name"] . $date["year"] . $date["mon"] . $date["mday"] . $date["hours"] . $date["minutes"] . $date["seconds"];
        $fname = hash("sha256", $fname);
        //画像・動画をDBに格納．
        $sql = "INSERT INTO media(fname, extension, raw_data) VALUES (:fname, :extension, :raw_data);";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":fname", $fname, PDO::PARAM_STR);
        $stmt->bindValue(":extension", $extension, PDO::PARAM_STR);
        $stmt->bindValue(":raw_data", $raw_data, PDO::PARAM_STR);
        $stmt->execute();
    }
} catch (PDOException $e) {
    echo ("<p>500 Inertnal Server Error</p>");
    exit($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <div id="post">
        <?php include('myHeader.php') ?>
        <div class="container-wrapper gazo4">
            <div class="container">
                <h1>画像をアップロード</h1>
                <form id="main_form" action="Main.php" enctype="multipart/form-data" method="post">
                    <!-- <label>画像/動画アップロード</label> -->
                    <div id="fileUp">
                        <input type="file" name="upfile">
                    </div>
                    <br>
                    <p> ※画像はjpeg方式，png方式，gif方式に対応しています．動画はmp4方式のみ対応しています。</p>
                    <br>
                    <!-- <label for="upload"> 投稿！<input id="upload" type="submit" value="アップロード"></label> -->
                    <button id="upload" type="submit" class="btn-shine">アップロード</button>
                </form>
                <form action="Main.php">
                    <!-- <input type="submit" value="戻る"> -->
                    <button id="back" type="submit" class="btn-shine">戻る</button>
                </form>
            </div>
        </div>
        <?php include('footer.php') ?>
    </div>
</body>

</html>