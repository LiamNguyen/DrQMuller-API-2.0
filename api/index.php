<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
// require __DIR__ . '/../src/routes.php';

require_once __DIR__ . '/../src/include/DbOperation.php';

require_once __DIR__ . '/../src/include/Locale.php';

require __DIR__ . '/../lib/Prs/RequestInterface.php';

require __DIR__ . '/../lib/Prs/ResponseInterface.php';

require __DIR__ . '/../lib/Prs/ServerRequestInterface.php';

/* *
 * URL: http://210.211.109.180/drmuller/api/time/alltime
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */
$app->get('/time/alltime', function ($request, $response) {
    $db = new DbOperation();
    $result = $db->getAllTime();
    $allTime['Select_AllTime'] = array();
    $temp = array();

    while($row = $result->fetch_object()) {
        $temp = $row;
        array_push($allTime['Select_AllTime'], $temp);
    }

    return responseBuilder(200, $response, $allTime);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/time/ecotime
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */
$app->get('/time/ecotime', function ($request, $response) {
    $db = new DbOperation();
    $result = $db->getEcoTime();
    $ecoTime['Select_EcoTime'] = array();
    $temp = array();

    while($row = $result->fetch_object()) {
        $temp = $row;
        array_push($ecoTime['Select_EcoTime'], $temp);
    }

    return responseBuilder(200, $response, $ecoTime);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/time/selectedtime/:dayId/:locationId/:machineId
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */
$app->get('/time/selectedtime/{dayId}/{locationId}/{machineId}', function ($request, $response, $args) {
    $db = new DbOperation();

    $dayId = $args['dayId'];
    $locationId = $args['locationId'];
    $machineId = $args['machineId'];

    $result = $db->getSelectedTime($dayId, $locationId, $machineId);
    $selectedTime['Select_SelectedTime'] = array();
    $temp = array();

    while($row = $result->fetch_object()) {
        $temp = $row;
        array_push($selectedTime['Select_SelectedTime'], $temp);
    }

    return responseBuilder(200, $response, $selectedTime);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/datasource/countries
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */
$app->get('/datasource/countries', function ($request, $response) {
    $db = new DbOperation();
    $result = $db->getCountries();
    $countries['Select_Countries'] = array();
    $temp = array();

    while($row = $result->fetch_object()) {
        $temp = $row;
        array_push($countries['Select_Countries'], $temp);
    }

    return responseBuilder(200, $response, $countries);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/datasource/cities/:countryId
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */
$app->get('/datasource/cities/{countryId}', function ($request, $response, $args) {
    $db = new DbOperation();
    $result = $db->getCities($args['countryId']);
    $cities['Select_Cities'] = array();
    $temp = array();

    while($row = $result->fetch_object()) {
        $temp = $row;
        array_push($cities['Select_Cities'], $temp);
    }

    return responseBuilder(200, $response, $cities);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/datasource/districts/:cityId
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */
$app->get('/datasource/districts/{cityId}', function ($request, $response, $args) {
    $db = new DbOperation();
    $result = $db->getDistricts($args['cityId']);
    $districts['Select_Districts'] = array();
    $temp = array();

    while($row = $result->fetch_object()) {
        $temp = $row;
        array_push($districts['Select_Districts'], $temp);
    }

    return responseBuilder(200, $response, $districts);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/datasource/daysofweek
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */
$app->get('/datasource/daysofweek', function ($request, $response) {
    $db = new DbOperation();
    $result = $db->getWeekDays();
    $daysOfWeek['Select_DaysOfWeek'] = array();
    $temp = array();

    while($row = $result->fetch_object()) {
        $temp = $row;
        array_push($daysOfWeek['Select_DaysOfWeek'], $temp);
    }

    return responseBuilder(200, $response, $daysOfWeek);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/datasource/locations/:districtId
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */
$app->get('/datasource/locations/{districtId}', function ($request, $response, $args) {
    $db = new DbOperation();
    $result = $db->getLocations($args['districtId']);
    $locations['Select_Locations'] = array();
    $temp = array();

    while($row = $result->fetch_object()) {
        $temp = $row;
        array_push($locations['Select_Locations'], $temp);
    }

    return responseBuilder(200, $response, $locations);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/datasource/machines/:locationId
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */
$app->get('/datasource/machines/{locationId}', function ($request, $response, $args) {
    $db = new DbOperation();
    $result = $db->getMachines($args['locationId']);
    $machines['Select_Machines'] = array();
    $temp = array();

    while($row = $result->fetch_object()) {
        $temp = $row;
        array_push($machines['Select_Machines'], $temp);
    }

    return responseBuilder(200, $response, $machines);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/datasource/types
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */
$app->get('/datasource/types', function ($request, $response) {
    $db = new DbOperation();
    $result = $db->getTypes();
    $types['Select_Types'] = array();
    $temp = array();

    while($row = $result->fetch_object()) {
        $temp = $row;
        array_push($types['Select_Types'], $temp);
    }

    return responseBuilder(200, $response, $types);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/datasource/vouchers
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */
$app->get('/datasource/vouchers', function ($request, $response) {
    $db = new DbOperation();
    $result = $db->getVouchers();
    $vouchers['Select_Vouchers'] = array();
    $temp = array();

    while($row = $result->fetch_object()) {
        $temp = $row;
        array_push($vouchers['Select_Vouchers'], $temp);
    }

    return responseBuilder(200, $response, $vouchers);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/user/login
 * Parameters: none
 * Authorization: none
 * Method: POST
 * */
$app->post('/user/signin', function ($request, $response) {

    $data = (object) $request->getParsedBody();

    $username = $data->username;
    $password = $data->password;

    $db = new DbOperation();

    $customerLogin['Select_ToAuthenticate'] = array();
    $result = array();
    $statusCode;

    $customerId = $db->customerLogin($username, $password);
    if (!empty($customerId)) {
        $customerInfo = $db->getCustomer($customerId);
        $result['customerId'] = $customerInfo['CUSTOMER_ID'];
        $result['customerName'] = $customerInfo['CUSTOMER_NAME'];
        $result['dob'] = $customerInfo['DOB'];
        $result['gender'] = $customerInfo['GENDER'];
        $result['phone'] = $customerInfo['PHONE'];
        $result['address'] = $customerInfo['ADDRESS'];
        $result['email'] = $customerInfo['EMAIL'];
        $result['sessonToken'] = $customerInfo['SESSIONTOKEN']; 
        $result['jwt'] = $customerInfo['JWT'];
        $statusCode = 200;

    } else {
        $result['error'] = invalid_username_or_password_message;
        $result['errorCode'] = invalid_username_or_password_code;
        $statusCode = 401;

    }

    array_push($customerLogin['Select_ToAuthenticate'], $result);

    return responseBuilder($statusCode, $response, $customerLogin);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/user/register
 * Parameters: none
 * Authorization: none
 * Method: POST
 * */
$app->post('/user/register', function ($request, $response) {

    $data = (object) $request->getParsedBody();

    $username = $data->username;
    $password = $data->password;

    $db = new DbOperation();

    $customerRegister['Insert_NewCustomer'] = array();
    $result = array();
    $statusCode;

    $registerResult = $db->customerRegister($username, $password);

    if ($registerResult == 0) {
        $result['status'] = '1';
        $result['message'] = register_success_message;
        $statusCode = 201;

    } else if ($registerResult == 1) {
        $result['status'] = '0';
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;

    } else if ($registerResult == 2) {
        $result['status'] = '2';
        $result['error'] = customer_existed_error_message;
        $result['errorCode'] = customer_existed_error_code;
        $statusCode = 409;
    }

    array_push($customerRegister['Insert_NewCustomer'], $result);

    return responseBuilder($statusCode, $response, $customerRegister);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/appointment/confirm/
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */
$app->get('/appointment/confirm/{appointmentId}', function ($request, $response, $args) {
    $validityResult = isValidCustomer($request, $response, 'Update_ConfirmAppointment');
    if (!$validityResult['valid']) {
        $authenticateResponse = array();
        return responseBuilder(401, $response, $validityResult['response']);

    }

    $db = new DbOperation();
    $confirmAppointment['Update_ConfirmAppointment'] = array();
    $result = array();
    $statusCode;

    $confirmResult = $db->confirmAppointment($args['appointmentId']);

    if ($confirmResult == 0) {
        $result['status'] = '1';
        $result['message'] = appointment_confirm_success_message;
        $statusCode = 200;

    } else {
        $result['status'] = '0';
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;

    }

    array_push($confirmAppointment['Update_ConfirmAppointment'], $result);

    return responseBuilder($statusCode, $response, $confirmAppointment);
});

/* *
 * Type: Helper method
 * Responsibility: Check session token for valid customer
 * */

function isValidCustomer($request, $response, $requestName) {
    $token = $request->getHeaderLine('Authorization');
    $authenticateResponse[$requestName] = array();
    $result = array();
    $isValid = true;

    if (!empty($token)) {
        $db = new DbOperation();

        if (!$db->isValidToken($token)) {
            $result['error'] = invalid_token_message;
            $result['errorCode'] = invalid_token_code;

            $isValid = false;
        } 

    } else {
        $result['error'] = token_missing_message;
        $result['errorCode'] = token_missing_code;

        $isValid = false;
    }

    array_push($authenticateResponse[$requestName], $result);

    return array('valid' => $isValid, 'response' => $authenticateResponse);
}

/* *
 * Type: Helper method
 * Responsibility: Build response with contentType and httpStatusCode
 * */

function responseBuilder($status_code, $response, $responseObj) {
    return $response->withStatus($status_code)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($responseObj));
}

$app->run();