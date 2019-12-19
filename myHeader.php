<header>
    <div class="logo">
        <h1>fav-gazo</h1>
        <p>画像投稿サイト</p>
    </div>
    <div class="header-right">
    <p>ようこそ<u><?php echo htmlspecialchars($_SESSION["NAME"], ENT_QUOTES); ?></u>さん</p> <!-- ユーザー名をechoで表示 -->
        <a href="./Login.php">ログアウト</a>
    </div>
</header>