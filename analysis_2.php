<?php
// 必要な関数とデータベース接続を含むファイルを読み込む
include("funcs.php");
$pdo = db_conn();

// 質問を取得
$sql_questions = "SELECT * FROM design";
$stmt_questions = $pdo->prepare($sql_questions);
$stmt_questions->execute();
$questions = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);

// 回答者を取得
$sql_respondents = "SELECT * FROM respondent";
$stmt_respondents = $pdo->prepare($sql_respondents);
$stmt_respondents->execute();
$respondents = $stmt_respondents->fetchAll(PDO::FETCH_ASSOC);

// 回答を取得
$sql_answers = "SELECT * FROM answers";
$stmt_answers = $pdo->prepare($sql_answers);
$stmt_answers->execute();
$answers = $stmt_answers->fetchAll(PDO::FETCH_ASSOC);

// 回答へ素早くアクセスするための検索用配列を作成
// キー: [質問ID][回答者ID] = 回答内容
$answer_lookup = [];
foreach ($answers as $answer) {
    $answer_lookup[$answer['question_id']][$answer['respondent_id']] = $answer['answer'];
}

// DataTables用にデータを準備
// 各行は [把握したいこと, 質問, 回答者1の回答, 回答者2の回答, ...] の形式
$data = [];
foreach ($questions as $question) {
    $row = [
        $question['purpose'],
        $question['question']
    ];
    foreach ($respondents as $respondent) {
        // 回答がない場合は空文字を設定
        $row[] = $answer_lookup[$question['id']][$respondent['id']] ?? '';
    }
    $data[] = $row;
}

// データをJSON形式にエンコード（JavaScriptで使用するため）
$json_data = json_encode($data);
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>インタビュー結果分析</title>
    <!-- DataTablesのCSSを読み込む -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <!-- jQueryライブラリを読み込む -->
    <script src="https://code.jquery.com/jquery-2.1.3.js"></script>
    <!-- DataTablesのJavaScriptを読み込む -->
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <style>
        body { padding: 20px; }
        #results { width: 100%; }
        /* テーブルセル内のテキスト折り返しを有効にする */
        #results td { 
            white-space: normal; 
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <!-- Bootstrap のコンテナクラスを使用し、フルワイドスレイアウトにする -->
    <div class="container-fluid">
        <h1>インタビュー結果分析</h1>
        <a href="home.php" class="btn btn-primary mb-3">ホーム画面へ戻る</a>
        
        <!-- DataTablesで拡張される表 -->
        <table id="results" class="display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>把握したいこと</th>
                    <th>質問</th>
                    <?php foreach ($respondents as $respondent): ?>
                        <th><?= h($respondent['name']) ?> (<?= h($respondent['zokusei']) ?>)</th>
                    <?php endforeach; ?>
                </tr>
            </thead>
        </table>
    </div>

    <script>
    $(document).ready(function() {
        $('#results').DataTable({
            // PHP側で準備したデータをJavaScriptオブジェクトとして渡す
            data: <?= $json_data ?>,
            // 各列の定義
            columns: [
                { title: '把握したいこと' },
                { title: '質問' },
                <?php foreach ($respondents as $respondent): ?>
                { title: '<?= h($respondent['name']) ?> (<?= h($respondent['zokusei']) ?>)' },
                <?php endforeach; ?>
            ],
            // 横スクロールを有効にする
            scrollX: true,
            // 列の自動幅設定を無効にする
            autoWidth: false,
            // すべての列に対する設定
            columnDefs: [
                { 
                    targets: '_all', // すべての列を対象とする
                    width: '200px', // 列幅を200pxに設定
                    render: function(data, type, row) {
                        // 表示時のみ、100文字を超える場合は省略表示する
                        return type === 'display' && data.length > 100 ?
                            data.substr(0, 100) + '...' :
                            data;
                    }
                }
            ],
        
        });
    });
    </script>
</body>
</html>