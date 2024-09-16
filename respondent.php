<?php
include("funcs.php");
$pdo = db_conn();

//２．データ登録SQL作成
$sql = "SELECT * FROM respondent";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

//３．データ表示
$values = "";
if($status==false) {
  sql_error($stmt);
}

//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
$json = json_encode($values,JSON_UNESCAPED_UNICODE); //JSON化してJSに渡す場合

?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>回答者のの管理</title>
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

 <!-- 質問の新規作成［START］ -->
 <div class="section">
        <h2>回答者の新規登録</h2>
        <form method="POST" action="respondent.insert.php">
            <label>回答者名：<input type="text" name="name"></label><br>
            <label>属性：<input type="text" name="zokusei"></label><br>
            <input type="submit" value="新規作成" class="btn">
        </form>
 </div>
<!--  質問の新規作成[End] -->


<!--  作成済み質問の表示[START] -->
<div class="section">
        <h2>登録済み回答者</h2>
        <table>
            <?php foreach($values as $v){ ?>
                <tr>
                    <td><?=h($v["name"])?></td>
                    <td><?=h($v["zokusei"])?></td>
                    <td>
                        <a href="respondent.detail.php?id=<?=h($v["id"])?>" class="btn">編集</a>
                        <a href="respondent.delete.php?id=<?=h($v["id"])?>" class="btn">削除</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

<!--  作成済み質問の表示[End] -->




</body>
</html>
