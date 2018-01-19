<?php
	include_once __DIR__."/../config.php";
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="icon" type="image/png" href="<?= PATH ?>assets/images/logo-dark.png" />

		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>Echelon Install Package</title>
		<link rel="stylesheet" href="<?= PATH ?>assets/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="<?= PATH ?>assets/styles/error.css">
	</head>

	<body>
		<main class="error-page">
			<div class="error">
				<h1>Fatal Error!</h1>
				<p><?= base64_decode($_GET['m']) ?></p>

				<a class="btn btn-success" href="<?= PATH ?>">Find Home</a>
				<a class="btn btn-primary" href="javascript:history.back();">Go back</a>
			</div>
		</main>
	</body>
</html>
