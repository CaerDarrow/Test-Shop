<?php
	session_start();
	$_SESSION["guest"] = TRUE;
	if ($_GET["submitl"] == "Login") {
		$log = strtolower($_GET["login"]);
		if (file_exists('./private/passwd'))
		{
			$file = fopen('./private/passwd', 'r');
			while (($data = fgetcsv($file, ',')) !== FALSE)
			{
				if (($data[0] == $log) && ($data[1] == hash('sha512', $_GET["passwd"]))) {
					$_SESSION["login"] = $_GET["login"];
					$_SESSION["passwd"] = $data[1];
					$_SESSION["name"] = $data[3];
					$_SESSION["adr"] = $data[4];
					$_SESSION["phone"] = $data[5];
					$_SESSION["email"] = $data[6];
					$_SESSION["user"] = TRUE;
					if ($data[2] == "admin")
						$_SESSION["admin"] = TRUE;
					unset($_GET["submitl"]);
					unset($_GET["login"]);
					unset($_GET["passwd"]);
					fclose($file);
					header("Location: http://localhost:8100/index.php?log=GINPUT");
					exit ;
				}
			}
			fclose($file);
		}
		header("Location: http://localhost:8100/index.php?log=WINPUT");
		exit ;
	}
	else if ($_GET["submitl"] == "Logout") {
		unset($_SESSION["login"]);
		unset($_SESSION["passwd"]);
		unset($_SESSION["admin"]);
		unset($_SESSION["user"]);
		setcookie("intra42cart", null, time() - 1);
		header("Location: http://localhost:8100/index.php");
		exit ;
	}
?>
<?php
if (preg_match("/Add to cart /", $_GET["submit"])) {
	$product = str_replace("Add to cart ", "", $_GET["submit"]);
	$flag = false;
	$file = scandir("./goods");
	foreach ($file as $key => $value) {
		$f = fopen("./goods/" . $value, "r");
		$f = fread($f, filesize("./goods/" . $value));
		$prods = preg_split("/\n+/", $f);
		foreach ($prods as $ind => $item) {
			$it = preg_split("/,/", $item);
			if (str_replace("\"", "", $it[0]) == $product) {
				if (!isset($_COOKIE["intra42cart"])) {
					$it[3] = 1;
					$cook = array(implode(",", $it));
				} else {
					$cook = array();
					foreach (preg_split("/\n+/", $_COOKIE["intra42cart"]."\n") as $a) {
						if ($a !== "")
						$cook[] = preg_split("/,/", $a); }
						foreach ($cook as $i => $a) {
							if ($a[0] == $it[0]) {
								$cook[$i][3] = (int)$cook[$i][3] + 1;
								$flag = true;
								break;
							}
						}
						if (!$flag) {
							$it[3] = 1;
							$cook[] = $it;
						}
						foreach ($cook as $i => $a) {
							$cook[$i] = implode(",", $a);
						}
					}
					setcookie("intra42cart", implode("\n", $cook), time() + 3600 * 12);
					header("Location: http://localhost:8100/index.php?cat=".$_GET["cat"]);
				}
			}
		}
}
?>
<?php
if (preg_match("/Remove from cart /", $_POST["submit"])) {
	$product = str_replace("Remove from cart ", "", $_POST["submit"]);
	unset($_POST["submit"]);
	$cook = preg_split("/\n+/", $_COOKIE["intra42cart"]);
	foreach ($cook as $i => $item) {
		$item = preg_split("/,/", $item);
		if ($item[0] == $product) {
			if ($item[3] > 1) {
				$item[3] -= 1;
				$cook[$i] = implode(",", $item);
			} else {
				$cook[$i] = "";
			}
		} else {
			$cook[$i] = implode(",", $item);
		}
	}
	setcookie("intra42cart", implode("\n", $cook), time() + 3600 * 12);
	header("Location: http://localhost:8100/index.php".(isset($_GET["cat"]) ? "?cat=".$_GET["cat"] : ""));
}
?>
<html>
	<head>
		<title>Test - shop</title>
		<link rel="stylesheet" href="style.css">
	</head>
	<? if($_GET["art"] == "SUCCES"){ ?>
	<body onload="alert('Категория успешно добавлена!')" ><?php unset($_GET["art"]); ?>
	<? }else if($_GET["art"] == "DEL"){ ?>
	<body onload="alert('Категория успешно удалена!')" ><?php unset($_GET["art"]); ?>
	<? }else if($_GET["good"] == "SUCCES"){ ?>
	<body onload="alert('Товар успешно добавлен! 🍔')" ><?php unset($_GET["good"]); ?>
	<? }else if($_GET["good"] == "EXIST"){ ?>
	<body onload="alert('Позиция уже есть в каталоге!')" ><?php unset($_GET["good"]); ?>
	<? }else if($_GET["good"] == "DEL"){ ?>
	<body onload="alert('Позиция удалена!')" ><?php unset($_GET["good"]); ?>
	<? }else if($_GET["good"] == "WINPUT"){ ?>
	<body onload="alert('Что-то не так!')" ><?php unset($_GET["good"]); ?>
	<? }else if($_GET["reg"] == "SUCCES"){ ?>
	<body onload="alert('Акаунт успешно создан!')" ><?php unset($_GET["reg"]); ?>
	<?}else if($_GET["reg"] == "EXIST"){?>
	<body onload="alert('Пользователь с таким именем уже существует! :(')" ><?php unset($_GET["reg"]); ?>
	<? }else if($_GET["log"] == "WINPUT"){?>
	<body onload="alert('Неправильное имя пользователя или пароль! :(')" ><?php unset($_GET["log"]); ?>
	<? }else if($_GET["log"] == "GINPUT"){?>
	<body onload="alert('Приветствуем, <?php echo($_SESSION["login"]); ?>!')" ><?php unset($_GET["log"]); ?>
	<? }else{?>
	<body>
	<?}?>
		<div calss="container">
			<header class="box" style="background-color: #4CAF50">
				<div class="dropdown">
					<form style="display: block; margin: 0 0;" action="index.php" method="get">
						<input class="dropbtn" style="background-color: inherit;" type="submit" name="submitl" value="Home"/>
					</form>
				</div>
				<p style="color: white; float: right"> <?php echo$_GET["cat"];?></p>
				<div style="align-self: flex-end;">
					<?php if(!isset($_SESSION["login"])){ ?>
					<div class="dropdown">
						<button class="dropbtn" style="margin: 0 0;">Login</button>
						<div class="dropdown-content">
							<form action="index.php" method="get" style="margin: 0.5em 1em; min-width: 12.25em;">
								Username:<input name="login" value="" required/><br />
								Password: <input name="passwd" value="" required/><br />
								<input type="submit" name="submitl" value="Login"/>
							</form>
						</div>
					</div>
					<div class="dropdown">
						<button class="dropbtn" style="margin: 0 0;">Register</button>
						<div style="right: 0;" class="dropdown-content" >
							<form action="create.php" method="post" style="margin: 0.5em 1em; min-width: 12.25em;">
								Username:<input name="login" value="" required/>
								Name: <input name="name" value="" required/><br />
								Adress:<input name="adr" value=""/>
								Phone: <input name="phone" value=""/><br />
								E-mail: <input name="email" value=""/>
								Password: <input name="passwd" value="" required/><br />
								<input type="submit" name="submit" value="Register"/>
							</form>
						</div>
					</div>
				<?php }else{ ?>
					<div class="dropdown">
						<form style="display: block; margin: 0 0;" action="index.php" method="get">
							<input class="dropbtn" style="background-color: inherit;" type="submit" name="submitl" value="Logout"/>
						</form>
					</div>
					<div class="dropdown">
						<button class="dropbtn" style="margin: 0 0;">Acc info</button>
						<div style="right: 0;" class="dropdown-content" >
							<form action="modif.php" method="post" style="margin: 0.5em 1em; min-width: 12.25em;">
								Username:<input name="login" value="<?php echo$_SESSION["login"] ?>"/><br />
								Name: <input name="name" value="<?php echo$_SESSION["name"] ?>"/><br />
								Adress:<input name="adr" value="<?php echo$_SESSION["adr"] ?>"/><br />
								Phone: <input name="phone" value="<?php echo$_SESSION["phone"] ?>"/><br />
								E-mail: <input name="email" value="<?php echo$_SESSION["email"] ?>"/><br />
								New pass: <input name="npasswd" value=""/><br />
								New Username:<input name="nlogin" value=""/><br />
								<?php if($_SESSION["admin"]){ ?>
									Old pass: <input name="opasswd" placeholder="Required"/><br />
									<input type="hidden" name="admin" value="TRUE"/>
									<input type="submit" name="submit" value="Modify"/>
									<input type="submit" name="submit" value="Delete"/>
								<?}else{?>
									Old pass: <input name="opasswd" placeholder="Required" required/><br />
									<input type="submit" name="submit" value="Modify"/>
								<?}?>
							</form>
						</div>
					</div>
				<?php } ?>
				</div>
			</header>
			<div class="box">
				<aside class="col" style="align-self: flex-start; background-color: #4CAF50; width: 7em; flex-direction: column">
					<center>
						<div class="dropdown">
							<button class="dropbtn">Categories: </button>
								<div class="dropdown-content" style="left: 100%; top: 0;">
								<?php
								$files = scandir("./goods");
								foreach ($files as $category){
									if ($category=="." || $category==".."){}else{?>
									<form action="index.php" method="GET">
									<input class="btn" type="submit" name="cat" value="<?php echo$category;?>"></form>
									<?
									if($_SESSION["admin"]){ ?>
										<form action="goods.php" method="POST">
											<input type="hidden" name="rmcat" value="<?php echo$category;?>"/>
											<input type="submit" name="submit" value="Remove"></form>
									<?}?>
								<?}
								}?>
								<?php if($_SESSION["admin"]){ ?>
									<form action="goods.php" method="POST">
									NEW Category: <input name="cat" value=""/>
									<input type="submit" name="submit" value="Add category"></form>
								<?}?>
								</div>
						</div>
					</center>
				</aside><article class="goodsbox" style="width: 75%">
				<?php if (isset($_GET["cat"])) {
					$path = "./goods/".$_GET["cat"];
					$file = fopen($path, 'r');
					while (($data = fgetcsv($file, ',')) !== FALSE) {?>
					<div class=good>
						<form method="get">
							<? if($data[4] != "") {?>
							<img class="goodimg" src="<?php echo $data[4];?>"/>
							<?}else{ ?>
							<img class="goodimg" src="https://img.mvideo.ru/Pdb/30040032b.jpg"/>
							<?}?>
							<div class=goodtxt>
								<?php echo$data[0];?>: <span><?php echo$data[1];?><span><br />
								SIZE: <?php echo$data[2];?><br />
								COUNT: <?php echo$data[3];?><br />
							<?php if($_SESSION["admin"]){ $_POST['submit']=[]?>
								<input type="hidden" name="cat" value="<?php echo($_GET["cat"]);?>">
								<input type="hidden" name="good" value="<?php echo($data[0]);?>">
								<input type="submit" name="submit" value="Delete" formaction="goods.php"/>
							<?}else{ ?>
								<input type="hidden" name="cat" value="<?php echo($_GET["cat"]);?>">
								<input type="submit" name="submit" value="Add to cart <?php echo $data[0];?>" formaction="index.php?cat=<?php echo $_GET["cat"];?>"/>
							<?} ?>
							</div>
						</form>
					</div>
			 	<?}
				fclose($file);
				if($_SESSION["admin"]){ ?>
				<div class=good>
					<form action="goods.php" method="post" style="width: 15em;">
						<div style="text-align: center">
							<p>ADD NEW POSITION:<p>
						</div>
						<input type="hidden" name="cat" value="<?php echo$_GET["cat"];?>"/>
						Name: <input name="good" value="" required/>
						Price: <input name="price" value="" required/>
						Size: <input name="size" value="" required/>
						Count: <input name="count" value="" required/>
						Img source: <input name="source" value=""/>
						<br />
						<input type="submit" name="submit" value="Add position"/>
					</form>
				</div>
				<?}} else {?>
                    <blockquote style="background: brown">
                        <p style="padding: 1.5em; font-size: 2em" aria-setsize="">Добро пожаловать в магазин всего на свете для всех на свете.</p>
                    </blockquote>
                <?php }?>
				</article><aside class="col" style="min-width: 7em;">
					<center>
						<div class="dropdown">
							<button class="dropbtn">Cart</button>
								<div class="dropdown-content" style="right: 100%; top: 0;">
									<?php if (!isset($_COOKIE["intra42cart"]))
									echo "<p>Ваша корзина пуста</p>";
									else {
										$cart = preg_split("/\n+/", $_COOKIE["intra42cart"]);
										$cart_sum = 0;
										foreach ($cart as $prod) {
											$prod = preg_split("/,/", $prod);
											if ($prod[3])
										echo "<p>$prod[0]($prod[1]$/шт) : $prod[3]шт".
										"<form action='index.php?cat=".$_GET['cat']."' method='post'>".
										"<input  type='hidden' name='submit' value='Remove from cart $prod[0]'>".
										"<input type='submit' value='X'>".
										"</form>".
										"</p>";
										$cart_sum += (int)$prod[1] * (int)$prod[3];
									}
									echo "<p>Общая стоимость : $cart_sum$</p>";
								}?>
							</div>
						</div>
					</center>
				</div>
			</aside>
		</div>
	</body>
</html>
