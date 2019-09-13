<?php
	if (!file_exists('private'))
		mkdir('private');
	if (!file_exists('goods'))
		mkdir('goods');
	$log = strtolower($_POST["login"]);
	$file = fopen('./private/passwd', 'a');
	if ($_POST["submit"] == "Reg Admin")
	{
		$account = [
			"login" => $log,
			"passw" => hash('sha512', $_POST["passwd"]),
			"status" => "admin",
		];
		fputcsv($file, $account);
		fclose($file);
		header("Location: http://localhost:8100/index.php?login=".$_POST["login"]."&passwd=".$_POST["passwd"]."&submitl=Login");
		exit ;
	}
?>
<html>
	<body>
		<form action="install.php" method="post">
			ADMIN NAME:<input name="login" value="" required/><br />
			ADMIN PASS: <input name="passwd" value="" required/><br />
			<input type="submit" name="submit" value="Reg Admin"/>
		</form>
	</body>
</html>
