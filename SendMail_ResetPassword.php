<?php
    require 'PHPMailer/PHPMailerAutoload.php';

    $server   = "127.0.0.1";
    $database = "prd_icaredb";
    $username = "longv";
    $password = "adminLongdoptrai69";

    // Create connection
    $con=mysqli_connect($server,$username,$password,$database);

    // Check connection
    if (mysqli_connect_errno())
    {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $LOGIN_ID = $_POST['login_id'];

    //Specifies the character set when sending data to and from database
    mysqli_set_charset($con,"utf8");

    $sql = "SELECT EMAIL FROM tbl_Customers WHERE LOGIN_ID = BINARY '" . $LOGIN_ID . "'";
    $result = mysqli_query($con, $sql);

    if ($data = mysqli_fetch_assoc($result)){
        $EMAIL = $data['EMAIL'];
        
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        $mail = new PHPMailer;
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'testingemail607@gmail.com';                 // SMTP username
        $mail->Password = 'adminlongdoptrai';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to
        $mail->CharSet = "UTF-8";

        $mail->setFrom('from@example.com', 'iCare Service');
        $mail->addAddress($EMAIL, 'Me');     // Add a recipient
        //$mail->addAddress('ellen@example.com');               // Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Khôi Phục Mật Khẩu';
        $mail->Body    = "
        <!DOCTYPE html>
        <html>
        <head>
        </head>
        <body>
        <a href=\"http://210.211.109.180/drmuller/redirect_reset.php?login_id=" . $LOGIN_ID ."\">Restore Link</a>
        </body>
        </html>
        ";

        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        while(!$mail->send()) {
        //echo json_encode(array("SendEmail_ResetPassword" => "Message could not be sent", "ERROR" => $mail->ErrorInfo));
        }
        echo json_encode(array("SendEmail_ResetPassword" => "Message has been sent"));
        
    }else{
        echo json_encode(array("SendEmail_ResetPassword" => "Could not find username or email"));
    }

    // Close connections
    mysqli_close($con);
?>