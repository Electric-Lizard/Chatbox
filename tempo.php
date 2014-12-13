<!DOCTYPE html>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
<meta charset="utf-8"/>
<title>Tempo</title>
</head>
<body>
<?php if (!empty($_POST)) {
	$ch = curl_init("http://www.google.com/recaptcha/api/verify");
	$options = array(
		"privatekey" => "6Le7_foSAAAAAC4XFcKuB_gOE3AB8hpSpT3BLTPX",
		"remoteip" => $_SERVER["REMOTE_ADDR"],
		"challenge" => $_POST["recaptcha_challenge_field"],
		"response" => $_POST["recaptcha_response_field"]
	);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $options);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	var_dump($response);
	var_dump($_POST);
}
?>
<form action="" method="POST">
	<div id="capcha"></div>
	<input type="checkbox" name="name" value="test">
	<input type="submit">
</form>
<script>
	Recaptcha.create("6Le7_foSAAAAAHrOJ650l-nET1jEad2lTxwwesio", "capcha",
    	{
     		theme: "red",
     		callback: Recaptcha.focus_response_field
   		}
  	);
</script>
</body>
</html>
