<?php
	if ($_POST["submit"] == "Add category")
	{
		$art = strtolower($_POST["cat"]);
		$path = "./goods/".$art;
		if (!file_exists('goods'))
			mkdir('goods');
		if (!file_exists($path))
			touch($path);
		header("Location: http://localhost:8100/index.php?art=SUCCES&cat=".$art);
		exit();
	}
	else if ($_POST["submit"] == "Remove")
	{
		$art = strtolower($_POST["rmcat"]);
		$path = "./goods/".$art;
		unlink($path);
		header("Location: http://localhost:8100/index.php?art=DEL");
	}
	else if ($_GET["submit"] == "Delete")
	{
		$art = strtolower($_GET["cat"]);
		$path1 = "./goods/".$art;
		$path2 = "./goods/".$art."2";
		$file1 = fopen($path1, 'r');
		$file2 = fopen($path2, "w");
		while (($data = fgetcsv($file1, ',')) !== FALSE){
			if ($data[0] != $_GET["good"]){
				fputcsv($file2, $data);
			}
		}
		fclose($file1);
		fclose($file2);
		rename($path2, $path1);
		header("Location: http://localhost:8100/index.php?good=DEL&cat=".$art);
		exit();
	}
	else if ($_POST["submit"] == "Add position")
	{
		$art = strtolower($_POST["cat"]);
		$path = "./goods/".$art;
		if (file_exists($path))
		{
			$file = fopen($path, 'r');
			while (($data = fgetcsv($file, ',')) !== FALSE)
			{
				if ($data[0] == $_POST["good"]){
					fclose($file);
					header("Location: http://localhost:8100/index.php?good=EXIST&cat=".$art);
					exit();
				}
			}
			fclose($file);
		}
		$file = fopen($path, 'a');
		$good = [
			"good" => $_POST["good"],
			"price" => $_POST["price"],
			"size" => $_POST["size"],
			"count" => $_POST["count"],
			"source" => $_POST["source"],
		];
		fputcsv($file, $good);
		header("Location: http://localhost:8100/index.php?good=SUCCES&cat=".$art);
		fclose($file);
		exit();
	}
	else
		header("Location: http://localhost:8100/index.php?good=WINPUT");
		exit();
?>
