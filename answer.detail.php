<?php
include("funcs.php");
$pdo = db_conn();

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $sql = "SELECT a.*, d.purpose, d.question, r.name, r.zokusei 
            FROM answers a
            JOIN design d ON a.question_id = d.id
            JOIN respondent r ON a.respondent_id = r.id
            WHERE a.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();

    if ($status == false) {
        sql_error($stmt);
    } else {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>回答の詳細</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>div { padding: 10px; font-size: 16px; }</style>
</head>
<body>
    <div class="container">
        <h1>回答の詳細</h1>
        <a href="answer.php" class="btn btn-primary">回答一覧へ戻る</a>

        <form method="POST" action="answer.update.php">
            <div class="form-group">
                <label>把握したいこと：</label>
                <p><?= h($row["purpose"]) ?></p>
            </div>
            <div class="form-group">
                <label>質問：</label>
                <p><?= h($row["question"]) ?></p>
            </div>
            <div class="form-group">
                <label>回答者：</label>
                <p><?= h($row["name"]) ?> (<?= h($row["zokusei"]) ?>)</p>
            </div>
            <div class="form-group">
                <label for="answer">回答：</label>
                <textarea id="answer" name="answer" class="form-control" rows="4"><?= h($row["answer"]) ?></textarea>
            </div>
            <input type="hidden" name="id" value="<?= $row["id"] ?>">
            <input type="submit" value="更新" class="btn btn-primary">
        </form>
    </div>
</body>
</html>