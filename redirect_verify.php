
<!DOCTYPE html>
<html>
<head>
</head>
<body onload="document.getElementById('link').click()">
    <?php 
	$CUSTOMER_ID =$_GET['cus_id'];
    ?>
    <a id="link"  href="icare://210.211.109.180/drmuller/verify?cus_id=<?php echo $CUSTOMER_ID; ?>"></a>
</body>
</html>