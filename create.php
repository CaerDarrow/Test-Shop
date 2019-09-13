<?php
	if ($_POST["submit"] == "Register")
	{
		$log = strtolower($_POST["login"]);
		if (file_exists('./private/passwd'))
		{
			$file = fopen('./private/passwd', 'r');
			while (($data = fgetcsv($file, ',')) !== FALSE)
			{
				if ($data[0] == $log){
					fclose($file);
					header("Location: http://localhost:8100/index.php?reg=EXIST");
					exit();
				}
			}
			fclose($file);
		}
		$file = fopen('./private/passwd', 'a');
		$account = [
			"login" => $log,
			"passw" => hash('sha512', $_POST["passwd"]),
			"status" => "user",
			"name" => $_POST["name"],
			"adr" => $_POST["adr"],
			"phone" => $_POST["phone"],
			"email" => $_POST["email"],
		];
		fputcsv($file, $account);
		header("Location: http://localhost:8100/index.php?reg=SUCCES");
		fclose($file);
		exit();
	}
	else
		header("Location: http://localhost:8100/index.php?reg=WINPUT");
		exit();
?>
