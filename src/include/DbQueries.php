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
        'query_Select_CustomerInfo', 
        'SELECT cu.CUSTOMER_ID, CUSTOMER_NAME, DOB, GENDER, 
        PHONE, ADDRESS, EMAIL, UISAVEDSTEP, ACTIVE, ut.SESSIONTOKEN 
        FROM ' . DB_NAME . '.tbl_customers cu
        INNER JOIN ' . DB_NAME . '.tbl_usertoken ut
        ON cu.CUSTOMER_ID = ut.CUSTOMER_ID
        WHERE cu.CUSTOMER_ID = ?
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
        'query_Select_CustomerId_FromUsername', 
        'SELECT cu.CUSTOMER_ID
        FROM ' . DB_NAME . '.tbl_customers cu 
        WHERE cu.LOGIN_ID = ? AND STATUS = 1'
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
        'query_Select_UiFillStep',
        'SELECT cu.UISAVEDSTEP 
        FROM ' . DB_NAME . '.tbl_customers cu
        WHERE cu.CUSTOMER_ID = ?'
    );

/* *
 * INSERT STATEMENTS
 * */

    define(
        'query_Insert_NewCustomer', 
        'INSERT INTO ' . DB_NAME . '.tbl_customers
        (LOGIN_ID, PASSWORD, SALT)
        VALUES(?, ?, ?)'
    );

    define(
        'query_Store_SessionToken', 
        'INSERT INTO ' . DB_NAME . '.tbl_usertoken
        (CUSTOMER_ID, SESSIONTOKEN)
        VALUES(?, ?)'
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
        SET cu.UISAVEDSTEP = \'basic\', CUSTOMER_NAME = ?, ADDRESS = ?, UPDATEDAT = ? 
        WHERE CUSTOMER_ID = ?'
    );

    define(
        'query_Update_BasicInformation',
        'UPDATE ' . DB_NAME . '.tbl_customers cu
        SET CUSTOMER_NAME = ?, ADDRESS = ?, UPDATEDAT = ? 
        WHERE CUSTOMER_ID = ?'
    );

?>  