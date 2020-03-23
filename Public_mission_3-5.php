<html>
	<head>
		<meta http-equiv="content-type" charset="utf-8">
	</head>
	<body>
		<?php
			$filename = "mission_3-1.txt";//ファイルの名前の変数を外に出す（ウェブページを表示するのに必要だから）
			$editnum = "";
			$editname = "";
			$editcom = "";
			if(isset($_POST['name']) and isset($_POST['comment']) and isset($_POST['pass'])){
				if($_POST['name'] != "" and $_POST['comment'] != "" and $_POST['pass'] != ""){//これがないとどれかが空欄でも送れてしまう
					$name = $_POST['name'];
					$comment = $_POST['comment'];
					$date = date("Y/m/d H:i:s");
					$pass = $_POST['pass'];
					if($_POST['number'] != ""){//編集の条件分岐
						$number = $_POST['number'];
						$sendText = "";//後で代入するための変数
						$lines = file($filename);
						foreach ($lines as $line){
							if(intval(substr($line,0,1)) == $number){
								$editdataArr = explode("<>",$line);
								$sendText .= "$editdataArr[0]<>$name<>$comment<>$editdataArr[3]<>$pass<>\n";
							} 
							else {
								$sendText .= "$line";
							}
						}
						$fp = fopen($filename,"w");
						fwrite($fp, $sendText);
						fclose($fp);
					}
					else {
					//$filename = "mission_3-1.txt";（移動）
					//if(file_exists($filename)){
						//$num = count(file($filename));//ファイルのデータの行数を数えて$numに代入
						//$num++;//投稿番号を取得←これだと削除後の投稿番号がおかしくなる
					//} else {
						//$num = 1;
					//}
						$lines = file($filename);
						if($lines != ""){//$linesがある場合
						//（DB用）echo "if文に入ったよ<br>";
						//（DB用）echo "受け取った中身：" . $_POST['comment'] . "<br>";
						//$line = explode("\n",$lines);//\n刻みで分割
						//array_pop($line);//\n刻みの分割によってできた最後の空の要素を消去
							$lastarr = explode("<>",$lines[count($lines) - 1]);//最後の行を分割
							$lastnum = intval($lastarr[0]);//最後の行の最初の要素＝投稿番号
					
					//(DB用)var_dump($lines[count($lines) - 1]);
					//（DB用）var_dump($lastarr);
						}
						else {//$linesがない時＝最初の書き込み時
							$lastnum = 0;
						}
						$num = $lastnum + 1;//最後の行＋１
						$sendText = "$num<>$name<>$comment<>$date<>$pass<>";
						$fp = fopen($filename,"a");
						fwrite($fp, $sendText . "\n");
						fclose($fp);
					}
				//header('Location:./mission_3-5.php');//書き込みしたらリダイレクト
				//ウェブページ表示をif文の外に出して常に表示されるようにする
				}
			}
			if(isset($_POST['delete']) and isset($_POST['pass2'])){
				$delete = $_POST['delete'];
				$pass2 = $_POST['pass2'];
				$sendText = "";
				$lines = file($filename);
				foreach ($lines as $line){
					if(intval(substr($line,0,1)) != $delete){
						$sendText .= "$line";
					}
					else {//削除番号の時はpassを探す
						$deldataArr = explode("<>",$line);
						$correctpass = $deldataArr[4];//正しいpassを発見
					}
				}
				if($correctpass == $pass2){//もしpassが一致するなら
					$fp = fopen($filename,"w");
					fwrite($fp, $sendText);
					fclose($fp);
				}
			}
			if(isset($_POST['edit']) and isset($_POST['pass3'])){
				$edit = $_POST['edit'];
				$pass3 = $_POST['pass3'];
				$sendText = "";
				$lines = file($filename);
				foreach ($lines as $line){
					if(intval(substr($line,0,1)) == $edit){
						$editArr = explode("<>",$line);
						if($editArr[4] == $pass3){//もしpassが一致するなら
							$editnum=$editArr[0];
							$editname=$editArr[1];
							$editcom=$editArr[2];
						}
					}
				}
			}
			if(file_exists($filename)){//もしもファイルが存在しているならウェブ上に表示
				$lines = file($filename);//ファイルを配列に格納し、さらに変数に格納
				foreach ($lines as $line){ //foreachでファイルの配列をループ処理
					$dataArr = explode("<>",$line);
					foreach($dataArr as $i => $data){//キーと値を取り出して表示
						if($i != 4){//4番目以外のキーと値を表示（passは非表示）
							echo "$data<br>";
						}
					}
					echo "<br>";
				}
			}
		?>
		<form method="POST" action="">
			<input type="hidden" name="number" value="<?php echo $editnum; ?>">
			名前：<input type="text" name="name" value="<?php echo $editname; ?>">
			コメント：<input type="text" name="comment" value="<?php echo $editcom; ?>">
			パスワード：<input type="password" name="pass">
			<input type="submit" value="送信">
		</form>
		<form method="POST" action="">
			削除番号（半角）：<input type="text" name="delete">
			パスワード認証：<input type="password" name="pass2">
			<input type="submit" value="削除">
		</form>
		<form method="POST" action="">
			編集番号（半角）：<input type="text" name="edit">
			パスワード認証：<input type="password" name="pass3">
			<input type="submit" value="編集">
		</form>
	</body>
</html>
