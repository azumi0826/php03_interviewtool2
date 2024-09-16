<!-- design.phpで更新を押したら -->

<?php
//1. idで紐付け
$id = $_GET["id"];


//2. DB接続します
include("funcs.php");
$pdo = db_conn();

//３．データ登録SQL作成
$sql = "SELECT * FROM design WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();


$values = "";
if($status==false) {
  sql_error($stmt);
}

// 1つのレコードを取得
$v =  $stmt->fetch(); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>インタビュー設計</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        margin: 0;
        padding: 20px;
    }
    .btn {
        display: inline-block;
        padding: 10px 15px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }
    .btn:hover {
        background-color: #0056b3;
    }
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        resize: vertical;
    }
    .section {
        margin-bottom: 30px;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
</style>
</head>
<body>

<a href="home.php" class="btn">ホーム画面へ戻る</a>

 <!-- 質問の新規作成［START］ --> -
<h2>更新画面</h2>
<form method="POST" action="design.update.php">
  <div class="jumbotron">
     <label>把握したいこと：<input type="text" name="purpose" value="<?=$v["purpose"]?>"></label><br>
     <label>質問<textArea name="question" rows="4" cols="40"><?=$v["question"]?></textArea></label><br>
     <input type="submit" value="更新">
     <input type = "hidden" name ="id" value="<?=$v["id"]?>"> 
     <!-- idを送る -->
  </div>
</form>
<!--  質問の新規作成[End] -->




</body>
</html>
