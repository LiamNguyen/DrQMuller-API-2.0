
<!DOCTYPE html>
<html>
<head>
</head>
<body onload="document.getElementById('link').click()">
    <?php 
	$LOGIN_ID =$_GET['login_id'];
    ?>
    <a id="link"  href="icare://210.211.109.180/drmuller/restore?login_id=<?php echo $LOGIN_ID; ?>"></a>
</body>
</html>