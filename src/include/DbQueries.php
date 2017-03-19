<?php
    include_once dirname(__FILE__) . '/Constants.php';

/* *
 * SELECT STATEMENTS
 * */

    define(
        'query_Select_AllTime', 
        'SELECT t.TIME_ID, t.TIME 
        FROM ' . DB_NAME . '.tbl_time t 
        WHERE t.TIME_ID < 66'
    );

    define(
        'query_Select_EcoTime', 
        'SELECT t.TIME_ID, t.TIME 
        FROM ' . DB_NAME . '.tbl_time t 
        WHERE (t.TIME_ID < 46 AND t.TIME_ID > 28) 
        OR (t.TIME_ID < 24 AND t.TIME_ID > 6)'
    );

    define(
        'query_Select_Countries', 
        'SELECT co.COUNTRY_ID, co.COUNTRY 
        FROM ' . DB_NAME . '.tbl_countries co'
    );
                                        
    define(
        'query_Select_Cities', 
        'SELECT ci.CITY_ID, ci.CITY 
        FROM ' . DB_NAME . '.tbl_cities ci 
        WHERE ci.COUNTRY_ID = ?'
    );

    define(
        'query_Select_Districts', 
        'SELECT di.DISTRICT_ID, di.DISTRICT 
        FROM ' . DB_NAME . '.tbl_districts di 
        WHERE di.CITY_ID = ?'
    );

    define(
        'query_Select_DaysOfWeek', 
        'SELECT wd.DAY_ID, wd.DAY 
        FROM ' . DB_NAME . '.tbl_weekdays wd 
        WHERE DAY_ID < 8'
    );

    define(
        'query_Select_Locations', 
        'SELECT lo.LOCATION_ID, lo.ADDRESS 
        FROM ' . DB_NAME . '.tbl_locations lo 
        WHERE DISTRICT_ID = ?'
    );

    define(
        'query_Select_Machines', 
        'SELECT MACHINE_ID, MACHINE_NAME 
        FROM ' . DB_NAME . '.tbl_machines 
        WHERE LOCATION_ID = ?'
    );

    define(
        'query_Select_Types', 
        'SELECT ty.TYPE_ID, ty.TYPE 
        FROM ' . DB_NAME . '.tbl_types ty'
    );

    define(
        'query_Select_Vouchers', 
        'SELECT VOUCHER_ID, VOUCHER, PRICE 
        FROM ' . DB_NAME . '.tbl_vouchers'
    );

    define(
        'query_Select_SelectedTime', 
        'SELECT t.TIME_ID, t.TIME 
        FROM ' . DB_NAME . '.tbl_appointmentschedule a 
        INNER JOIN ' . DB_NAME . '.tbl_weekdays d ON a.DAY_ID = d.DAY_ID 
        INNER JOIN ' . DB_NAME . '.tbl_time t ON a.TIME_ID = t.TIME_ID
        INNER JOIN ' . DB_NAME . '.tbl_appointments ap ON a.APPOINTMENT_ID = ap.APPOINTMENT_ID
        WHERE d.DAY_ID = ? 
            AND a.LOCATION_ID = ?
            AND a.MACHINE_ID = ? 
            AND ap.ACTIVE = 1 
        UNION
        SELECT t.TIME_ID, t.TIME 
        FROM ' . DB_NAME . '.tbl_temporarybooked tem 
        INNER JOIN ' . DB_NAME . '.tbl_time t 
        ON tem.TIME_ID = t.TIME_ID
        WHERE tem.ACTIVE = 1
            AND tem.DAY_ID = ? 
            AND tem.LOCATION_ID = ?
            AND tem.MACHINE_ID = ?
        ORDER BY TIME ASC'
    );

    define(
        'query_Select_CustomerInfoByCustomerId',
        'SELECT cu.CUSTOMER_ID, CUSTOMER_NAME, DOB, GENDER, 
        PHONE, ADDRESS, EMAIL, UISAVEDSTEP, ACTIVE, ut.SESSIONTOKEN 
        FROM ' . DB_NAME . '.tbl_customers cu
        INNER JOIN ' . DB_NAME . '.tbl_usertoken ut
        ON cu.CUSTOMER_ID = ut.CUSTOMER_ID
        WHERE cu.CUSTOMER_ID = ?
        AND STATUS = 1'
    );

    define(
        'query_Select_CustomerInfoByUsername',
        'SELECT cu.CUSTOMER_ID, CUSTOMER_NAME, DOB, GENDER, 
            PHONE, ADDRESS, EMAIL, UISAVEDSTEP, ACTIVE, ut.SESSIONTOKEN 
            FROM ' . DB_NAME . '.tbl_customers cu
            INNER JOIN ' . DB_NAME . '.tbl_usertoken ut
            ON cu.CUSTOMER_ID = ut.CUSTOMER_ID
            WHERE cu.LOGIN_ID = ?
            AND STATUS = 1'
    );

    define(
        'query_Select_Salt', 
        'SELECT cu.SALT 
        FROM ' . DB_NAME . '.tbl_customers cu
        WHERE cu.LOGIN_ID = BINARY ?'
    );

    define(
        'query_Select_CustomerId', 
        'SELECT cu.CUSTOMER_ID
        FROM ' . DB_NAME . '.tbl_customers cu 
        WHERE cu.LOGIN_ID = ? 
        AND cu.PASSWORD = ? AND STATUS = 1'
    );

    define(
        'query_Select_AdminKey',
        'SELECT ad.SECRETKEY
            FROM ' . DB_NAME . '.tbl_admins ad 
            WHERE ad.LOGIN_ID = ? 
            AND ad.PASSWORD = ? AND ACTIVE = 1'
    );

    define(
        'query_Select_CustomerId_FromUsername', 
        'SELECT cu.CUSTOMER_ID
        FROM ' . DB_NAME . '.tbl_customers cu 
        WHERE cu.LOGIN_ID = ? AND STATUS = 1'
    );

    define(
        'query_Select_AdminId_FromKey',
        'SELECT ad.ADMIN_ID
        FROM ' . DB_NAME . '.tbl_admins ad
        WHERE ad.SECRETKEY = ? AND ACTIVE = 1'
    );

    define(
        'query_Select_CustomerId_FromToken',
        'SELECT ut.CUSTOMER_ID
        FROM ' . DB_NAME . '.tbl_usertoken ut 
        WHERE ut.SESSIONTOKEN = ?'
    );

    define(
        'query_Select_CustomerId_FromTokenAndCustomerId',
        'SELECT ut.CUSTOMER_ID
        FROM ' . DB_NAME . '.tbl_usertoken ut 
        WHERE ut.SESSIONTOKEN = ? 
        AND ut.CUSTOMER_ID = ?'
    );

    define(
        'query_Select_CustomerId_FromTokenAndUsername',
        'SELECT cu.LOGIN_ID 
        FROM ' . DB_NAME . '.tbl_usertoken ut 
        INNER JOIN ' . DB_NAME . '.tbl_customers cu ON ut.CUSTOMER_ID = cu.CUSTOMER_ID
        WHERE ut.SESSIONTOKEN = ? 
        AND cu.LOGIN_ID = ?'
    );

    define(
        'query_Select_CustomerId_FromCustomerIdAndAppointmentId',
        'SELECT ap.CUSTOMER_ID 
        FROM ' . DB_NAME . '.tbl_appointments ap
        WHERE ap.CUSTOMER_ID = ?
        AND ap.APPOINTMENT_ID = ?
        AND ap.ACTIVE = 1'
    );

    define(
        'query_Select_UiFillStep',
        'SELECT cu.UISAVEDSTEP 
        FROM ' . DB_NAME . '.tbl_customers cu
        WHERE cu.CUSTOMER_ID = ?'
    );

    define(
        'query_Select_Appointment',
        'SELECT ap.APPOINTMENT_ID
            , ap.DISPLAY_ID
            , vc.VOUCHER
            , ap.START_DATE
            , ap.EXPIRED_DATE
            , ty.TYPE
            , lo.LOCATION_NAME
            , ap.VERIFICATION_CODE
            , cu.CUSTOMER_NAME
            , ap.CREATEDAT
            , ap.ISCONFIRMED
            , ap.ACTIVE
        FROM ' . DB_NAME . '.tbl_appointments ap 
        INNER JOIN ' . DB_NAME . '.tbl_vouchers vc ON ap.VOUCHER_ID = vc.VOUCHER_ID
        INNER JOIN ' . DB_NAME . '.tbl_types ty ON ap.TYPE_ID = ty.TYPE_ID
        INNER JOIN ' . DB_NAME . '.tbl_locations lo ON ap.LOCATION_ID = lo.LOCATION_ID
        INNER JOIN ' . DB_NAME . '.tbl_customers cu ON ap.CUSTOMER_ID = cu.CUSTOMER_ID
        WHERE ap.APPOINTMENT_ID = ?
        ORDER BY ap.CREATEDAT DESC'
    );

    define(
        'query_Select_TemporarySelectedTime',
        'SELECT temp.ID
        FROM ' . DB_NAME . '.tbl_temporarybooked temp
        WHERE DAY_ID = ? 
        AND TIME_ID = ?
        AND LOCATION_ID = ?
        AND MACHINE_ID = ?'
    );

    define(
        'query_Select_AppointmentScheduleForEmail',
        'SELECT wd.DAY, ti.TIME, ma.MACHINE_NAME
        FROM ' . DB_NAME . '.tbl_appointmentschedule aps
        INNER JOIN ' . DB_NAME . '.tbl_appointments ap ON aps.APPOINTMENT_ID = ap.APPOINTMENT_ID
        INNER JOIN ' . DB_NAME . '.tbl_weekdays wd ON aps.DAY_Id = wd.DAY_Id
        INNER JOIN ' . DB_NAME . '.tbl_time ti ON aps.TIME_ID = ti.TIME_ID
        INNER JOIN ' . DB_NAME . '.tbl_machines ma ON aps.MACHINE_ID = ma.MACHINE_ID
        WHERE aps.APPOINTMENT_ID = ? 
        AND ap.ACTIVE = 1'
    );

    define(
        'query_Select_BuildNumber',
        'SELECT v.BUILD
        FROM ' . DB_NAME . '.tbl_versions v
        WHERE (v.VERSION = ? OR v.BUILD = ?) AND v.OS = ?'
    );

    define(
        'query_Select_LatestBuildNumber',
        'SELECT v.BUILD 
        FROM uat_icaredb.tbl_versions as v
        Where RELEASEDATE = 
        (  
            SELECT MAX(RELEASEDATE)     
            FROM 
            (
                SELECT RELEASEDATE 
                FROM uat_icaredb.tbl_versions as vn
                WHERE vn.OS = ?
            ) as vsn
        ) AND v.OS = ?'
    );

/* *
 * INSERT STATEMENTS
 * */

    define(
        'query_Insert_NewCustomer', 
        'INSERT INTO ' . DB_NAME . '.tbl_customers
        (CUSTOMER_ID, LOGIN_ID, PASSWORD, SALT)
        VALUES(?, ?, ?, ?)'
    );

    define(
        'query_Store_SessionToken', 
        'INSERT INTO ' . DB_NAME . '.tbl_usertoken
        (CUSTOMER_ID, SESSIONTOKEN)
        VALUES(?, ?)'
    );

    define(
        'query_Insert_NewAppointment',
        'INSERT INTO ' . DB_NAME . '.tbl_appointments 
        (APPOINTMENT_ID, START_DATE, EXPIRED_DATE, TYPE_ID, CUSTOMER_ID, VOUCHER_ID, VERIFICATION_CODE, LOCATION_ID) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );

    define(
        'query_Insert_BookingSchedule',
        'INSERT INTO ' . DB_NAME . '.tbl_appointmentschedule
        (DAY_ID, TIME_ID, MACHINE_ID, LOCATION_ID, APPOINTMENT_ID) 
        VALUES '
    );

    define(
        'query_Insert_NewTemporaryTime',
        'INSERT INTO ' . DB_NAME . '.tbl_temporarybooked 
        (DAY_ID, TIME_ID, LOCATION_ID, MACHINE_ID) 
        VALUES (?, ?, ?, ?)'
    );

    define(
        'query_Insert_NewVersion',
        'INSERT INTO ' . DB_NAME . '.tbl_versions
        (VERSION, BUILD, OS)
        VALUES (?, ?, ?)'
    );

/* *
 * UPDATE STATEMENTS
 * */


    define(
        'query_Update_SessionToken', 
        'UPDATE  ' . DB_NAME . '.tbl_usertoken ut
        SET ut.SESSIONTOKEN = ?, ut.UPDATEDAT = ? 
        WHERE ut.CUSTOMER_ID = ?'
    );

    define(
        'query_Update_ConfirmAppointment',
        'UPDATE ' . DB_NAME . '.tbl_appointments ap
        SET ap.ISCONFIRMED = 1
        WHERE ap.APPOINTMENT_ID = ?'
    );

    define(
        'query_Update_BasicInformation_FirstTime',
        'UPDATE ' . DB_NAME . '.tbl_customers cu
        SET cu.UISAVEDSTEP = \'basic\', cu.CUSTOMER_NAME = ?, cu.ADDRESS = ?, cu.UPDATEDAT = ? 
        WHERE cu.CUSTOMER_ID = ?'
    );

    define(
        'query_Update_BasicInformation',
        'UPDATE ' . DB_NAME . '.tbl_customers cu
        SET cu.CUSTOMER_NAME = ?, cu.ADDRESS = ?, cu.UPDATEDAT = ? 
        WHERE cu.CUSTOMER_ID = ?'
    );

    define(
        'query_Update_NecessaryInformation_FirstTime',
        'UPDATE ' . DB_NAME . '.tbl_customers cu
        SET cu.UISAVEDSTEP = \'necessary\', cu.DOB = ?, cu.GENDER = ?, cu.UPDATEDAT = ? 
        WHERE cu.CUSTOMER_ID = ?'
    );

    define(
        'query_Update_NecessaryInformation',
        'UPDATE ' . DB_NAME . '.tbl_customers cu
        SET cu.DOB = ?, cu.GENDER = ?, cu.UPDATEDAT = ? 
        WHERE cu.CUSTOMER_ID = ?'
    );

    define(
        'query_Update_ImportantInformation',
        'UPDATE ' . DB_NAME . '.tbl_customers cu
        SET cu.UISAVEDSTEP = \'important\', cu.EMAIL = ?, cu.PHONE = ?, cu.UPDATEDAT = ? 
        WHERE cu.CUSTOMER_ID = ?'
    );

    define(
        'query_Update_ValidateAppointments',
        'UPDATE ' . DB_NAME . '.tbl_appointments a 
        SET a.ACTIVE = 0 
        WHERE a.ACTIVE = 1 
        AND a.EXPIRED_DATE < CURDATE()'
    );

    define(
        'query_Update_ConfirmCustomer',
        'UPDATE ' . DB_NAME . '.tbl_customers c 
        SET c.ACTIVE = 1 
        WHERE c.CUSTOMER_ID = ?'
    );

    define(
        'query_Update_ReleaseTime',
        'UPDATE ' . DB_NAME . '.tbl_temporarybooked 
        SET ACTIVE = 0 
        WHERE '
    );

    define(
        'query_Update_ResetPassword',
        'UPDATE ' . DB_NAME . '.tbl_customers cu 
        SET cu.PASSWORD = ?, cu.SALT = ? 
        WHERE cu.LOGIN_ID  = ?'
    );

    define(
        'query_Update_CustomerInformation',
        'UPDATE ' . DB_NAME . '.tbl_customers cu
        SET cu.CUSTOMER_NAME = ?, cu.ADDRESS = ?, cu.DOB = ?, cu.GENDER = ?, cu.PHONE = ?, cu.EMAIL = ?, cu.UPDATEDAT = ? 
        WHERE cu.CUSTOMER_ID = ?'
    );

    define(
        'query_Update_CustomerEmail',
        'UPDATE ' . DB_NAME . '.tbl_customers cu
        SET cu.EMAIL = ?, cu.UPDATEDAT = ? WHERE cu.CUSTOMER_ID = ?'
    );

    define(
        'query_Update_CancelAppointment',
        'UPDATE ' . DB_NAME . '.tbl_appointments ap
        SET ap.ACTIVE = 0, ap.ISCANCELLED = 1 
        WHERE APPOINTMENT_ID = ?'
    );

    define(
        'query_Update_TemporaryTimeSetActive',
        'UPDATE ' . DB_NAME . '.tbl_temporarybooked temp 
        SET temp.ACTIVE = 1 
        WHERE temp.ACTIVE = 0 
        AND temp.DAY_ID = ? 
        AND temp.TIME_ID = ? 
        AND temp.LOCATION_ID = ?
        AND temp.MACHINE_ID = ?'
    );

    define(
        'query_Update_VersionSetInactive',
        'UPDATE ' . DB_NAME . '.tbl_versions 
        SET ACTIVE = 0 
        WHERE ID = (
            SELECT ID 
            FROM (SELECT * FROM ' . DB_NAME . '.tbl_versions) as v
            Where RELEASEDATE = 
            (  
                select MAX(RELEASEDATE)     
                FROM 
                (
                    SELECT * 
                    FROM ' . DB_NAME . '.tbl_versions vn
                    WHERE vn.OS = ? 
                ) as vsn
            )
        ) AND OS = ?'
    );

    define(
        'query_Update_AdminKey',
        'UPDATE ' . DB_NAME . '.tbl_admins ad
        SET ad.SECRETKEY = ?
        WHERE ad.LOGIN_ID = ?'
    );

?>  