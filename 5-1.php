<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>3-02</title>
</head>
<body>

<form action="" method="post">
    <label for="name">名前:</label>
    <input type="text" id="name" name="name"><br>
    <label for="comment">コメント:</label>
    <input type="text" id="comment" name="comment"><br>
    <label for="password">パスワード:</label>
    <input type="password" id="password" name="password"><br> 
    <input type="submit" value="送信">
</form>

<form action="" method="post">
    <label for="delete_number">削除対象番号:</label>
    <input type="number" id="delete_number" name="delete_number">
    <label for="delete_password">パスワード:</label>
    <input type="password" id="delete_password" name="delete_password"><br> 
    <input type="submit" value="削除">
</form>

<form action="" method="post">
    <label for="edit_number">編集対象番号:</label>
    <input type="number" id="edit_number" name="edit_number">
    <label for="edit_password">パスワード:</label>
    <input type="password" id="edit_password" name="edit_password"><br> 
    <input type="submit" value="編集">
</form>

<?php
$dsn = 'mysql:dbname=db;host=localhost';
$user = 'tb';
$password = 'H';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

// テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    comment TEXT NOT NULL,
    password VARCHAR(50) NOT NULL,
    posted_at DATETIME
)";
$pdo->exec($sql);



if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['password'])) {
    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("INSERT INTO posts (name, comment, password, posted_at) VALUES (:name, :comment, :password, :posted_at)");
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $posted_at = date("Y-m-d H:i:s");
    $stmt->bindParam(':posted_at', $posted_at, PDO::PARAM_STR);
    $stmt->execute();
    echo "投稿完了!";
}

// 削除機能
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['delete_number']) && !empty($_POST['delete_password'])) {
    $delete_number = $_POST['delete_number'];
    $delete_password = $_POST['delete_password'];

    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id AND password = :password");
    $stmt->bindParam(':id', $delete_number, PDO::PARAM_INT);
    $stmt->bindParam(':password', $delete_password, PDO::PARAM_STR);
    $stmt->execute();
    echo "削除完了!";
}

// 編集機能
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['edit_number']) && !empty($_POST['edit_password'])) {
    $edit_number = $_POST['edit_number'];
    $edit_password = $_POST['edit_password'];

    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id AND password = :password");
    $stmt->bindParam(':id', $edit_number, PDO::PARAM_INT);
    $stmt->bindParam(':password', $edit_password, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo '<form action="" method="post">';
        echo '<input type="hidden" name="edit_postNumber" value="' . $row['id'] . '">';
        echo '名前: <input type="text" name="edit_name" value="' . $row['name'] . '"><br>';
        echo 'コメント: <input type="text" name="edit_comment" value="' . $row['comment'] . '"><br>';
        echo 'パスワード: <input type="password" name="edit_password"><br>';
        echo '<input type="submit" value="更新">';
        echo '</form>';
    } else {
        echo '編集対象が見つかりませんでした。';
    }
}

// 更新機能
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['edit_postNumber']) && !empty($_POST['edit_name']) && !empty($_POST['edit_comment']) && !empty($_POST['edit_password'])) {
    $edit_postNumber = $_POST['edit_postNumber'];
    $edit_name = $_POST['edit_name'];
    $edit_comment = $_POST['edit_comment'];
    $edit_password = $_POST['edit_password'];

    $stmt = $pdo->prepare("UPDATE posts SET name = :name, comment = :comment WHERE id = :id AND password = :password");
    $stmt->bindParam(':name', $edit_name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $edit_comment, PDO::PARAM_STR);
    $stmt->bindParam(':id', $edit_postNumber, PDO::PARAM_INT);
    $stmt->bindParam(':password', $edit_password, PDO::PARAM_STR);
    $stmt->execute();
    echo "更新完了!";
}

// 投稿一覧表示
$sql = "SELECT * FROM posts";
$stmt = $pdo->query($sql);
foreach ($stmt as $row) {
    echo "投稿番号: " . $row['id'] . "<br>";
    echo "名前: " . $row['name'] . "<br>";
    echo "コメント: " . $row['comment'] . "<br>";
    echo "投稿日時: " . $row['posted_at'] . "<br><br>";
}
?>

</body>
</html>
