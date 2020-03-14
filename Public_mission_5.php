<html>
	<head>
		<meta http-equiv="content-type" charset="utf-8">
		<title>mission_5</title>
	</head>
	<body>
		<?php
			$editnum = "";
			$editname = "";
			$editcom = "";

			// DB接続(mission_4-1)
			$dsn = '*****';// データベース名
			$user = '*****';// ユーザー名
			$password = '*****';// パスワード
			$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

			// テーブル作成(2回目以上は必要ないが、if文であった場合は作らない処理をしているので実際は大丈夫)(mission_4-2)
			$dbname = "mission_5";
			$sql = "CREATE TABLE IF NOT EXISTS $dbname"// もしもtbtestというテーブルがなければ作成
				." ("
				. "id INT AUTO_INCREMENT PRIMARY KEY,"// id列は整数(INT)
				. "name char(32),"// name列は32文字までの文字列
				. "comment TEXT,"// comment列はテキスト
				. "date datetime,"
				. "pass char(32)"
				.");";// ここまですべてSQL文
			$stmt = $pdo->query($sql);// SQLを実行する

			if(isset($_POST['name']) and isset($_POST['comment']) and isset($_POST['pass'])){
				if($_POST['name'] != "" and $_POST['comment'] != "" and $_POST['pass'] != ""){//これがないとどれかが空欄でも送れてしまう
				$name = $_POST['name'];
				$comment = $_POST['comment'];
				$pass = $_POST['pass'];
				$date = date("Y/m/d H:i:s");

				// 内容の変更
				if($_POST['number'] != ""){//編集の条件分岐
						$number = $_POST['number'];
						$sql = "update $dbname set name=:name,comment=:comment,date=:date,pass=:pass where id=:id";
						$stmt = $pdo->prepare($sql);// 実行準備
						$stmt->bindParam(':name', $name, PDO::PARAM_STR);// :nameを$nameに変更
						$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);// :commentを$commentに変更
						$stmt->bindParam(':date', $date, PDO::PARAM_STR);// :dateを$dateに変更
						$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);// :passを$passに変更
						$stmt->bindParam(':id', $number, PDO::PARAM_INT);// :idを$idに変更
						$stmt->execute();// 準備してたSQLを実行
					}
					else{// 内容の追加
						$sql = $pdo -> prepare("INSERT INTO $dbname (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
						$sql -> bindParam(':name', $name, PDO::PARAM_STR);// SQL文の:nameを$nameに変更
						$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);// SQL文の:commentを$commentに変更
						$sql -> bindParam(':date', $date, PDO::PARAM_STR);// SQL文の:dateを$dateに変更
						$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);// SQL文の:passを$passに変更
						$sql -> execute();// 準備してたSQLを実行
					}
					header('Location: ./mission_5.php');// 書き込みしたらリダイレクト
				}
			}

			// 内容の削除
			if(isset($_POST['delete']) and isset($_POST['pass2'])){
				$delete = $_POST['delete'];
				$pass2 = $_POST['pass2'];
				$correctpass = "";// 正解パスワード
				// 正解パスワード探し
				$sql = "SELECT * FROM $dbname where id=:id";// idが:idとなるものを選ぶ
				$stmt = $pdo->prepare($sql);// 実行準備
				$stmt->bindParam(':id', $delete, PDO::PARAM_INT);// :idに$deleteをバインド（結びつける）
				$stmt->execute();// 準備してたSQLを実行
				$results = $stmt->fetch();// 結果データ（削除番号での投稿）を配列で取得
				$correctpass = $results['pass'];// 正解パスワードの取得
				if($correctpass == $pass2){// もしパスワードが合っているなら
					$id = $delete;
					$sql = "delete from $dbname where id=:id";// idが:idとなるものを消す
					$stmt = $pdo->prepare($sql);// 実行準備
					$stmt->bindParam(':id', $id, PDO::PARAM_INT);// :idに$idをバインド（結びつける）
					$stmt->execute();// 準備してたSQLを実行
				}
				header('Location: ./mission_5.php');// 削除したらリダイレクト
			}

			// 内容の編集
			if(isset($_POST['edit']) and isset($_POST['pass3'])){
				$edit = $_POST['edit'];
				$pass3 = $_POST['pass3'];
				$correctpass = "";// 正解パスワード
				// 正解パスワード探し
				$sql = "SELECT * FROM $dbname where id=:id";// idが:idとなるものを選ぶ
				$stmt = $pdo->prepare($sql);// 実行準備
				$stmt->bindParam(':id', $edit, PDO::PARAM_INT);// :idに$editをバインド（結びつける）
				$stmt->execute();// 準備してたSQLを実行
				$results = $stmt->fetch();// 結果データ（編集番号での投稿）を配列で取得
				$correctpass = $results['pass'];// 正解パスワードの取得
				if($correctpass == $pass3){// もしパスワードが合っているなら
					$editnum = $edit;
					$editname = $results['name'];
					$editcom = $results['comment'];
				}
			}
		?>
		<p>＜投稿フォーム＞</p>
		<form method="POST" action="">
			<input type="hidden" name="number" value="<?php echo $editnum; ?>">
			名前：<input type="text" name="name" value="<?php echo $editname; ?>">
			コメント：<input type="text" name="comment" value="<?php echo $editcom; ?>">
			パスワード：<input type="password" name="pass">
			<input type="submit" value="送信">
		</form>
		<p>＜削除フォーム＞</p>
		<form method="POST" action="">
			削除番号（半角）：<input type="text" name="delete">
			パスワード認証：<input type="password" name="pass2">
			<input type="submit" value="削除">
		</form>
		<p>＜編集フォーム＞</p>
		<form method="POST" action="">
			編集番号（半角）：<input type="text" name="edit">
			パスワード認証：<input type="password" name="pass3">
			<input type="submit" value="編集">
		</form>
       		 <p>掲示板</p>
		<hr>
        	<?php
			// DBの情報をウェブページに表示
			$sql = "SELECT * FROM $dbname";
			$stmt = $pdo->query($sql);// 単純にデータを一回だけ取り出したい時に「queryメソッド」を使う
			$results = $stmt->fetchAll();// 結果データを全件まとめて配列で取得
			foreach ($results as $row){// $rowの中にはテーブルのカラム名が入る
				echo $row['id'].',';
				echo $row['name'].',';
				echo $row['comment'].',';
				echo $row['date'].'<br>';
			}
		?>
	</body>
</html>