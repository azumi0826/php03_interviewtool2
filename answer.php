<?php
include("funcs.php");
$pdo = db_conn();

// designテーブルから質問を取得
$sql_questions = "SELECT * FROM design";
$stmt_questions = $pdo->prepare($sql_questions);
$status_questions = $stmt_questions->execute();

if ($status_questions == false) {
    sql_error($stmt_questions);
}

$questions = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);

// respondentテーブルから回答者を取得
$sql_respondents = "SELECT * FROM respondent";
$stmt_respondents = $pdo->prepare($sql_respondents);
$status_respondents = $stmt_respondents->execute();

if ($status_respondents == false) {
    sql_error($stmt_respondents);
}

$respondents = $stmt_respondents->fetchAll(PDO::FETCH_ASSOC);

// 既存の回答を取得
$sql_answers = "SELECT * FROM answers";
$stmt_answers = $pdo->prepare($sql_answers);
$stmt_answers->execute();
$existing_answers = $stmt_answers->fetchAll(PDO::FETCH_ASSOC);

// 既存の回答に簡単にアクセスするための検索用配列を作成
$answer_lookup = [];
foreach ($existing_answers as $answer) {
    $answer_lookup[$answer['question_id']][$answer['respondent_id']] = $answer;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>回答の入力</title>
    <style>
        div { padding: 10px; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>回答の入力</h1>
        <a href="home.php" class="btn btn-primary">ホーム画面へ戻る</a>

        <table>
            <tr>
                <th>把握したいこと</th>
                <th>質問</th>
                <?php foreach ($respondents as $respondent): ?>
                    <th><?= h($respondent['name']) ?> (<?= h($respondent['zokusei']) ?>)</th>
                <?php endforeach; ?>
            </tr>
            <?php foreach ($questions as $question): ?>
                <tr>
                    <td><?= h($question['purpose']) ?></td>
                    <td><?= h($question['question']) ?></td>
                    <?php foreach ($respondents as $respondent): ?>
                        <td>
                            <?php
                            $answer = $answer_lookup[$question['id']][$respondent['id']] ?? null;
                            $answer_id = $answer ? $answer['id'] : null;
                            $answer_text = $answer ? $answer['answer'] : '';
                            ?>
                            <form method="POST" action="<?= $answer_id ? 'answer.update.php' : 'answer.insert.php' ?>">
                                <textarea name="answer" rows="3" cols="30"><?= h($answer_text) ?></textarea>
                                <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                                <input type="hidden" name="respondent_id" value="<?= $respondent['id'] ?>">
                                <?php if ($answer_id): ?>
                                    <input type="hidden" name="id" value="<?= $answer_id ?>">
                                <?php endif; ?>
                                <input type="submit" value="<?= $answer_id ? '更新' : '保存' ?>" class="btn btn-sm btn-primary">
                                <?php if ($answer_id): ?>
                                    <a href="answer.delete.php?id=<?= $answer_id ?>" class="btn btn-sm btn-danger" onclick="return confirm('本当に削除しますか？')">削除</a>
                                <?php endif; ?>
                            </form>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>