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
        $data = (object) $data;

        $customerName = $data->CUSTOMER_NAME;
        $createAt = $data->CREATEDAT;
        $displayId = $data->DISPLAY_ID;
        $location = $data->LOCATION_NAME;
        $voucher = $data->VOUCHER;
        $type = $data->TYPE;
        $startDate = $data->START_DATE;
        $expiredDate = $data->EXPIRED_DATE;
        $verificationCode = $data->VERIFICATION_CODE;
        $timeArray = $data->timeArray;
        $time = '';

        foreach ($timeArray as $item) {
            $timeObj = (object) $item;
            $time .= '
                <div class="row" style="font-size: 20px">
                    <div>
                '
                .
                $timeObj->DAY . ' - ' . $timeObj->TIME . ' ' . $timeObj->MACHINE_NAME
                .
                '
                    </div>
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
                        ' . $expiredDate . '
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
            
                    .row {
                        height: 50px;
                    }
            
                    .subject {
                        width: 50%;
                        float: left;
                    }
            
                    .value {
                        width: 50%;
                        float: right;
                    }
            
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="row">
                        <span class="subject">
                            <b>Tên khách hàng</b> 
                        </span>
                        <span class="value">
                            <b>' . $customerName . '</b>
                        </span>
                    </div>
                    <div class="row">
                        <span class="subject">
                            <b>Mã lịch hẹn</b> 
                        </span>
                        <span class="value">
                            <b>' . $displayId . '</b>
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
                    <div class="row" style="color: blue">
                        <span class="subject">
                            <b>Mã xác nhận</b> 
                        </span>
                        <span class="value" style="font-size: 15px">
                            <b>' . $verificationCode . '</b>
                        </span>
                    </div>  
                </div>
            </body>
            </html> 
        ';
    }
}