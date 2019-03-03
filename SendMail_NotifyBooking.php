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

    //$EMAIL = $_POST['email'];
    $CUSTOMER_ID = $_POST['cus_id'];
    $CUSTOMER_NAME = $_POST['cus_name'];
    $CREATED_DAY = $_POST['created_day'];
    $BOOKING_ID;
    $BOOKING_ADDRESS = $_POST['location'];
    $BOOKING_VOUCHER = $_POST['voucher'];
    $BOOKING_TYPE = $_POST['type'];
    $BOOKING_STARTDATE = $_POST['start_date'];
    $BOOKING_ENDDATE = $_POST['expire_date'];
    $BOOKING_CODE = $_POST['code']; 
    $BOOKINGS = json_decode($_POST['bookings'], true);
    $BOOKINGS_tmp;
    foreach ($BOOKINGS as $value){
        $BOOKINGS_tmp .= $value . '<br>';
    }

    $sqlSelect = "SELECT APPOINTMENT_ID FROM tbl_appointments WHERE CUSTOMER_ID = " . $CUSTOMER_ID . " AND VERIFICATION_CODE = '" . $BOOKING_CODE . "'";

    if ($result = mysqli_query($con, $sqlSelect))
    {
        $row = mysqli_fetch_assoc($result);
        $BOOKING_ID = $row['APPOINTMENT_ID'];
    }else{
        echo "Query error";
    }

    $mail = new PHPMailer;
    $EMAIL = "drqmuller@gmail.com";
    //$mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'drqmuller@gmail.com';                 // SMTP username
    $mail->Password = 'tamlight';                           // SMTP password
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

    $mail->Subject = 'Lịch hẹn mới';
    if ($BOOKING_STARTDATE != '11/11/1111'){
        $mail->Body    = '
        <!DOCTYPE html>
        <html>
        <head>
        </head>
        <body>
        <p>Mã khách hàng: ' . $CUSTOMER_ID . '</p>
        <h2><b>Tên khách hàng: ' . $CUSTOMER_NAME . '</b></h2>
        <h3><b>Mã lịch hẹn: ' . $BOOKING_ID . '</b></h3>
        <h3><b>Liệu trình: ' . $BOOKING_VOUCHER . '</b></h3>
        <p>Loại: ' . $BOOKING_TYPE .'</p>
        <p>Trung tâm: ' . $BOOKING_ADDRESS . '</p>
        <h2><b>Ngày khởi tạo: ' . $CREATED_DAY . '</b></h2>
        <p>Ngày bắt đầu: ' . $BOOKING_STARTDATE . '&nbsp;' . 'Ngày kết thúc: ' . $BOOKING_ENDDATE . '</p>
        '
        . $BOOKINGS_tmp .
        '
        <br>
        <h1><b>Mã Xác Nhận: ' . $BOOKING_CODE . '</b></h1>
        </body>
        </html>
        ';
    }else{
        $mail->Body    = '
        <!DOCTYPE html>
        <html>
        <head>
        </head>
        <body>
        <p>Mã khách hàng: ' . $CUSTOMER_ID . '</p>
        <h2><b>Tên khách hàng: ' . $CUSTOMER_NAME . '</b></h2>
        <h3><b>Mã lịch hẹn: ' . $BOOKING_ID . '</b></h3>
        <h3><b>Liệu trình: ' . $BOOKING_VOUCHER . '</b></h3>
        <p>Loại: ' . $BOOKING_TYPE .'</p>
        <p>Trung tâm: ' . $BOOKING_ADDRESS . '</p>
        <h2><b>Ngày khởi tạo: ' . $CREATED_DAY . '</b></h2>
        <p>Ngày thực hiện: ' . $BOOKING_ENDDATE . '</p>
        '
        . $BOOKINGS_tmp .
        '
        <br>
        <h1><b>Mã Xác Nhận: ' . $BOOKING_CODE . '</b></h1>
        </body>
        </html>
        ';
    }

    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    while(!$mail->send()) {
        //echo json_encode(array("SendEmail_NotifyBooking" => "Message could not be sent", "ERROR" => $mail->ErrorInfo));
    }
    echo json_encode(array("SendEmail_NotifyBooking" => "Message has been sent"));
    
    // Close connections
    mysqli_close($con);
?>