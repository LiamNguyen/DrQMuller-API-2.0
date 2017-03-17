<?php
/**
 * Created by PhpStorm.
 * User: DoNguyen
 * Date: /17/03/2017
 * Time: 10:57
 */

namespace templates\email;

class EmailTemplate
{
    static function getNotifyBookingTemplate($data) {
        $customerId = $data->userId;
        $customerName = $data->userName;
        $createAt = $data->createdAt;
        $displayId = $data->displayId;
        $location = $data->location;
        $voucher = $data->voucher;
        $type = $data->type;
        $startDate = $data->startDate;
        $expiredDate = $data->expiredDate;
        $exactDate = $data->exactDate;
        $verificationCode = $data->verificationCode;
        $timeArray = $data->timeArray;
        $time = '';

        foreach ($timeArray as $item) {
            $timeObj = (object) $item;
            $time .= '
                <div class="row">
                    <span class="subject">
                '
                .
                $timeObj->day . '-' . $timeObj->time . ' ' . $timeObj->machine
                .
                '
                    </span>
                </div>
            ';
        }

        if ($startDate == '1111-11-11') {
            $changingPart = '
                <div class="row">
                    <span class="subject">
                        Ngày thực hiện 
                    </span>
                    <span class="value">
                        ' . $exactDate . '
                    </span>
                </div> 
                '
                .
                $time
                .
                '
            ';
        } else {
            $changingPart = '
                <div class="row">
                    <span class="subject">
                        Ngày bắt đầu  
                    </span>
                    <span class="value">
                        ' . $startDate . '
                    </span>
                </div>
                <div class="row">
                    <span class="subject">
                        Ngày kết thúc 
                    </span>
                    <span class="value">
                        ' . $expiredDate . '
                    </span>
                </div>
                '
                .
                $time
                .
                '
            ';
        }

        return '
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    .container {
                        width: 320px;
                        line-height: 35px;
                        padding-left: 15px;
                        padding-right: 15px;
                        box-shadow: 2px 2px 2px 2px gray;
                    }
            
                    .row {}
            
                    .subject {
                        float: left;
                    }
            
                    .value {
                        float: right;
                    }
            
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="row">
                        <span class="subject">
                            Mã khách hàng
                        </span>
                        <span class="value">
                            ' . $customerId . '
                        </span>
                    </div>
                    <div class="row">
                        <span class="subject">
                            Tên khách hàng 
                        </span>
                        <span class="value">
                            ' . $customerName . '
                        </span>
                    </div>
                    <div class="row">
                        <span class="subject">
                            Mã lịch hẹn 
                        </span>
                        <span class="value">
                            ' . $displayId . '
                        </span>
                    </div>
                   <div class="row">
                        <span class="subject">
                            Liệu trình 
                        </span>
                        <span class="value">
                            ' . $voucher . '
                        </span>
                    </div>
                    <div class="row">
                        <span class="subject">
                            Loại  
                        </span>
                        <span class="value">
                            ' . $type . '
                        </span>
                    </div>
                    <div class="row">
                        <span class="subject">
                            Trung tâm 
                        </span>
                        <span class="value">
                            ' . $location . '
                        </span>
                    </div>
                    <div class="row">
                        <span class="subject">
                            Ngày khởi tạo  
                        </span>
                        <span class="value">
                            ' . $createAt . '
                        </span>
                    </div>
                    '
                    .
                    $changingPart
                    .
                    '
                    <div class="row">
                        <span class="subject">
                            Mã xác nhận 
                        </span>
                        <span class="value">
                            ' . $verificationCode . '
                        </span>
                    </div>  
                </div>
            </body>
            </html> 
        ';
    }
}