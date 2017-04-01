<?php

require_once __DIR__ . '/../src/include/DbOperation.php';
require_once __DIR__ . '/../src/include/Locale.php';

$appointmentId = $_GET['appointmentId'];

$db = new DbOperation();

$notifyBooking['SendMail_NotifyBooking'] = array();

if (empty($appointmentId)) {
    $result['message'] = appointment_id_empty_message;
    array_push($notifyBooking['SendMail_NotifyBooking'], $result);

    echo json_encode($notifyBooking);
    return;
}

$emailSentSuccess= $db->notifyBooking($appointmentId);

if ($emailSentSuccess) {
    $result['messageCode'] = notify_booking_sent_code;
    $result['message'] = notify_booking_sent_message;
} else {
    $result['messageCode'] = notify_booking_fail_code;
    $result['message'] = notify_booking_fail_message;
}

array_push($notifyBooking['SendMail_NotifyBooking'], $result);

echo json_encode($notifyBooking);

?>