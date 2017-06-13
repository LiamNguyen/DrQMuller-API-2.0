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

require_once __DIR__ . '/../src/include/utils/ValidationRules.php';

require __DIR__ . '/../lib/Prs/RequestInterface.php';

require __DIR__ . '/../lib/Prs/ResponseInterface.php';

require __DIR__ . '/../lib/Prs/ServerRequestInterface.php';

$app->get('/test/running', function () {
	return 'running';
});

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
 * Parameters: dayId, locationId, machineId
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
 * Parameters: countryId
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
 * Parameters: cityId
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
 * URL: http://210.211.109.180/drmuller/api/datasource/locations/:districtId
 * Parameters: districtId
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
 * Parameters: locationId
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
 * Request body:
 * {
    "username": "username",
    "password": "password"
 * }
 * Authorization: none
 * Method: POST
 * */
$app->post('/user/login', function ($request, $response) {

    $data = (object) $request->getParsedBody();
    
    $username = $data->username;
    $password = $data->password;

    $db = new DbOperation();
    $result = array();

    $customerId = $db->customerLogin($username, $password);
    if (!empty($customerId)) {
        $result = $db->getCustomerByCustomerId($customerId);
        $statusCode = 200;

    } else {
        $result['error'] = invalid_username_or_password_message;
        $result['errorCode'] = invalid_username_or_password_code;
        $statusCode = 401;

    }
    return responseBuilder($statusCode, $response, $result);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/user/register
 * Parameters: none
 * Request body:
 *{
    "username": "username",
    "password": "password"
 * }
 * Authorization: none
 * Method: POST
 * */
$app->post('/user/register', function ($request, $response) {

    $data = (object) $request->getParsedBody();

    $validate = new ValidationRules();
    $requiredFieldsValidityResult = $validate->verifyRequiredFieldsWithUsernameAndPassword($data, 'Insert_NewCustomer');
    if ($requiredFieldsValidityResult['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResult['response']);
    }

    $username = $data->username;
    $password = $data->password;

    $db = new DbOperation();
    $result = array();

    $customerId = $db->customerRegister($username, $password);

    if ($registerResult == 2) {
        $result['error'] = customer_existed_error_message;
        $result['errorCode'] = customer_existed_error_code;
        $statusCode = 409;

    } else if (!empty($customerId)) {
        $result = $db->getCustomerByCustomerId($customerId);
        $statusCode = 201;

    } else {
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;

    }
    return responseBuilder($statusCode, $response, $result);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/user/basicinformation
 * Parameters: none
 * Request body:
 * {
      "userId": "1",
      "userName": "Test",
      "userAddress": "Test"
 * }
 * Authorization: Session Token to be matched with userId
 * Method: post
 * */
$app->post('/user/basicinformation', function ($request, $response) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Update_BasicInfo');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $data = (object) $request->getParsedBody();

    $requiredFieldsValidityResult = $validate->verifyRequiredFieldsForUpdateBasicInformation($data);
    if ($requiredFieldsValidityResult['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResult['response']);
    }

    $informationArray = array(
        'customerId' => $data->userId,
        'customerName' => $data->userName,
        'address' => $data->userAddress
    );


    $db = new DbOperation();

    $updateBasicInfo['Update_BasicInfo'] = array();
    $result = array();

    $updateBasicInformationSuccess = $db->updateBasicInformation($informationArray);
    
    if ($updateBasicInformationSuccess) {
        $customerInformation = $db->getCustomerByCustomerId($data->userId);
        $result = parseCustomerInformationToResponse($result, $customerInformation);
        $statusCode = 200;

    } else {
        $result['status'] = '0';
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;

    }

    array_push($updateBasicInfo['Update_BasicInfo'], $result);

    return responseBuilder($statusCode, $response, $updateBasicInfo);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/user/necessaryinformation
 * Parameters: none
 * {
      "userId": "3",
      "userDob": "1900-02-02",
      "userGender": "Female"
 * }
 * Authorization: Session Token to be matched with userId
 * Method: post
 * */
$app->post('/user/necessaryinformation', function ($request, $response) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Update_NecessaryInfo');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $data = (object) $request->getParsedBody();

    $requiredFieldsValidityResult = $validate->verifyRequiredFieldsForUpdateNecessaryInformation($data);
    if ($requiredFieldsValidityResult['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResult['response']);
    }

    $informationArray = array(
        'customerId' => $data->userId,
        'dob' => $data->userDob,
        'gender' => $data->userGender
    );

    $db = new DbOperation();

    $updateNecessaryInfo['Update_NecessaryInfo'] = array();
    $result = array();

    $updateNecessaryInformationSuccess = $db->updateNecessaryInformation($informationArray);
    
    if ($updateNecessaryInformationSuccess) {
        $customerInformation = $db->getCustomerByCustomerId($data->userId);
        $result = parseCustomerInformationToResponse($result, $customerInformation);
        $statusCode = 200;

    } else {
        $result['status'] = '0';
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;

    }

    array_push($updateNecessaryInfo['Update_NecessaryInfo'], $result);

    return responseBuilder($statusCode, $response, $updateNecessaryInfo);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/user/importantinformation
 * Parameters: none
 * Request body:
 * {
      "userId": "3",
      "userEmail": "test@test.com",
      "userPhone": "+138(04)-494498238"
 * }
 * Authorization: Session Token to be matched with userId
 * Method: post
 * */
$app->post('/user/importantinformation', function ($request, $response) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Update_ImportantInfo');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $data = (object) $request->getParsedBody();

    $requiredFieldsValidityResult = $validate->verifyRequiredFieldsForUpdateImportantInformation($data);
    if ($requiredFieldsValidityResult['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResult['response']);
    }

    $informationArray = array(
        'customerId' => $data->userId,
        'email' => $data->userEmail,
        'phone' => $data->userPhone
    );

    $db = new DbOperation();

    $updateImportantInfo['Update_ImportantInfo'] = array();
    $result = array();

    $updateImportantInformationSuccess = $db->updateImportantInformation($informationArray);

    if ($updateImportantInformationSuccess) {
        $customerInformation = $db->getCustomerByCustomerId($data->userId);
        $result = parseCustomerInformationToResponse($result, $customerInformation);
        $statusCode = 200;

    } else {
        $result['status'] = '0';
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;

    }

    array_push($updateImportantInfo['Update_ImportantInfo'], $result);

    return responseBuilder($statusCode, $response, $updateImportantInfo);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/user
 * Parameters: none
 * Request body:
 * {
      "userId": "3",
      "userName": "Test",
      "userAddress": "Test",
      "userDob": "1900-02-02",
      "userGender": "Female",
      "userEmail": "test@test.com",
      "userPhone": "+138(04)-494498238"
 * }
 * Authorization: Session Token to be matched with userId
 * Method: post
 * */
$app->post('/user', function ($request, $response) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Update_CustomerInformation');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $data = (object) $request->getParsedBody();

    $requiredFieldsValidityResultForBasicInformation = $validate->verifyRequiredFieldsForUpdateBasicInformation($data);
    $requiredFieldsValidityResultForNecessaryInformation = $validate->verifyRequiredFieldsForUpdateNecessaryInformation($data);
    $requiredFieldsValidityResultForImportantInformation = $validate->verifyRequiredFieldsForUpdateImportantInformation($data);

    if ($requiredFieldsValidityResultForBasicInformation['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResultForBasicInformation['response']);
    }

    if ($requiredFieldsValidityResultForNecessaryInformation['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResultForNecessaryInformation['response']);
    }

    if ($requiredFieldsValidityResultForImportantInformation['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResultForImportantInformation['response']);
    }

    $informationArray = array(
        'customerId' => $data->userId,
        'customerName' => $data->userName,
        'address' => $data->userAddress,
        'dob' => $data->userDob,
        'gender' => $data->userGender,
        'email' => $data->userEmail,
        'phone' => $data->userPhone
    );

    $db = new DbOperation();
    $updateCustomerInformation['Update_CustomerInformation'] = array();
    $result = array();

    $updateCustomerInformationSuccess = $db->updateCustomerInformation($informationArray);

    if ($updateCustomerInformationSuccess) {
        $customerInformation = $db->getCustomerByCustomerId($data->userId);
        $result = parseCustomerInformationToResponse($result, $customerInformation);
        $statusCode = 200;

    } else {
        $result['status'] = '0';
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;

    }

    array_push($updateCustomerInformation['Update_CustomerInformation'], $result);

    return responseBuilder($statusCode, $response, $updateCustomerInformation);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/user/email
 * Parameters: none
 * Request body:
 * {
      "userId": "3",
      "userEmail": "test@test.com"
 * }
 * Authorization: Session Token to be matched with userId
 * Method: post
 * */

$app->post('/user/email', function ($request, $response) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Update_CustomerEmail');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $data = (object) $request->getParsedBody();

    $requiredFieldsValidityResultForCustomerEmail = $validate->verifyRequiredFieldsForUpdateCustomerEmail($data);

    if ($requiredFieldsValidityResultForCustomerEmail['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResultForCustomerEmail['response']);
    }

    $informationArray = array(
        'customerId' => $data->userId,
        'email' => $data->userEmail
    );

    $db = new DbOperation();
    $updateCustomerEmail['Update_CustomerEmail'] = array();
    $result = array();

    $updateCustomerEmailSuccess = $db->updateCustomerEmail($informationArray);

    if ($updateCustomerEmailSuccess) {
        $result['status'] = '1';
        $result['message'] = update_email_success_message;
        $statusCode = 200;
    } else {
        $result['status'] = '0';
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;
    }

    array_push($updateCustomerEmail['Update_CustomerEmail'], $result);

    return responseBuilder($statusCode, $response, $updateCustomerEmail);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/user/confirm/:customerid
 * Parameters: customerId
 * Authorization: Session Token
 * Method: post
 * */

$app->post('/user/confirm/{customerId}', function ($request, $response, $args) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Update_ConfirmCustomer');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $db = new DbOperation();
    $confirmCustomer['Update_ConfirmCustomer'] = array();
    $result = array();

    $customerInformation = $db->getCustomerByCustomerId($args['customerId']);

    if (empty($customerInformation)) {
        $result['error'] = customer_not_found_message;
        $result['errorCode'] = customer_not_found_code;

        array_push($confirmCustomer['Update_ConfirmCustomer'], $result);

        return responseBuilder(404, $response, $confirmCustomer);
    } else {
        $confirmCustomerSuccess = $db->confirmCustomer($args['customerId']);

        if ($confirmCustomerSuccess) {
            $result['status'] = '1';
            $result['message'] = customer_confirm_success_message;
            $statusCode = 200;
        } else {
            $result['status'] = '0';
            $result['error'] = internal_error_message;
            $result['errorCode'] = internal_error_code;
            $statusCode = 501;
        }

        array_push($confirmCustomer['Update_ConfirmCustomer'], $result);

        return responseBuilder($statusCode, $response, $confirmCustomer);
    }
});

/* *
 * URL: http://210.211.109.180/drmuller/api/user/passwordreset
 * Parameters: none
 * Request body:
 * {
        "username": "pnguyen3",
        "password": "pnguyen3"
 * }
 * Authorization: Session Token to be matched with username
 * Method: post
 * */

$app->post('/user/passwordreset', function($request, $response) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Update_ResetPassword');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $data = (object) $request->getParsedBody();

    $requiredFieldsValidityResult = $validate->verifyRequiredFieldsWithUsernameAndPassword($data, 'Insert_NewCustomer');
    if ($requiredFieldsValidityResult['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResult['response']);
    }

    $username = $data->username;
    $password = $data->password;

    $db = new DbOperation();
    $resetPassword['Update_ResetPassword'] = array();
    $result = array();
    $resetPasswordSuccess = $db->resetPassword($username, $password);

    if ($resetPasswordSuccess) {
        $result['status'] = '1';
        $result['message'] = reset_password_success_message;
        $statusCode = 200;

    } else {
        $result['status'] = '0';
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;

    }

    array_push($resetPassword['Update_ResetPassword'], $result);

    return responseBuilder($statusCode, $response, $resetPassword);

});

/* *
 * URL: http://210.211.109.180/drmuller/api/appointment/create
 * Parameters: none
 *  * Request body:
 * {
        "startDate": "2016/12/02",
        "expiredDate": "2016/12/30",
        "typeId": "1",
        "userId": "87C5174E-A5CC-56F0-AE40-8E76CDFB0872",
        "voucherId": "1",
        "verificationCode": "DASFASDF",
        "locationId": "1",
        "time": [
            {
                "dayId": "1",
                "timeId": "48",
                "machineId": "2"
            },
            {
                "dayId": "1",
                "timeId": "8",
                "machineId": "2"
            },
            {
                "dayId": "1",
                "timeId": "10",
                "machineId": "2"
            }
        ]
 * }
 * Authorization: Session Token to be matched with customerId
 * Method: POST
 * */

$app->post('/appointment/create', function ($request, $response) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Insert_NewAppointment');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $data = (object) $request->getParsedBody();

    $requiredFieldsValidityResult = $validate->verifyRequiredFieldsForCreateNewAppointment($data);
    if ($requiredFieldsValidityResult['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResult['response']);
    }

    $db = new DbOperation();
    $createAppointment['Insert_NewAppointment'] = array();
    $result = array();

    $appointmentId = $db->createAppointment($data);

    if (!empty($appointmentId)) {
        $result['status'] = '1';
        $result['message'] = appointment_create_success_message;
        $result['appointmentId'] = $appointmentId;
        $statusCode = 200;

    } else {
        $result['status'] = '0';
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;
    }

    array_push($createAppointment['Insert_NewAppointment'], $result);

    return responseBuilder($statusCode, $response, $createAppointment);

});


/* *
 * URL: http://210.211.109.180/drmuller/api/appointment/:appointmentId
 * Parameters: appointmentId
 * Authorization: none
 * Method: GET
 * */

$app->get('/appointment/{appointmentId}', function($request, $response, $args) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Select_Appointment');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $db = new DbOperation();
    $getAppointment['Select_Appointment'] = array();
    $result = array();

    $appointmentInformation = $db->getAppointment($args['appointmentId']);
    if (empty($appointmentInformation)) {
        $result['error'] = appointment_not_found_message;
        $result['errorCode'] = appointment_not_found_code;

        array_push($getAppointment['Select_Appointment'], $result);

        return responseBuilder(404, $response, $getAppointment);
    } else {
        $result = parseAppointmentInformationToResponse($result, (object) $appointmentInformation);

        array_push($getAppointment['Select_Appointment'], $result);

        return responseBuilder(200, $response, $getAppointment);
    }
});


/* *
 * URL: http://210.211.109.180/drmuller/api/appointment/active/:customerId
 * Parameters: customerId
 * Authorization: Session Token to be matched with userId
 * Method: GET
 * */

$app->get('/appointment/active/{customerId}', function($request, $response, $args) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Select_CustomerActiveAppointment', $args['customerId']);
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $db = new DbOperation();
    $getCustomerActiveAppointment['Select_CustomerActiveAppointment'] = array();
    $result = array();

    $listOfActiveAppointments = $db->getCustomerActiveAppointment($args['customerId']);
    if (empty($listOfActiveAppointments)) {
        $result['error'] = no_active_appointment_message;
        $result['errorCode'] = no_active_appointment_code;

        array_push($getCustomerActiveAppointment['Select_CustomerActiveAppointment'], $result);

        return responseBuilder(404, $response, $getCustomerActiveAppointment);
    } else {
        foreach ($listOfActiveAppointments as $appointment) {
            $appointment = (object) $appointment;
            $result = parseAppointmentInformationToResponse($result, $appointment);
            array_push($getCustomerActiveAppointment['Select_CustomerActiveAppointment'], $result);
        }

        return responseBuilder(200, $response, $getCustomerActiveAppointment);
    }
});

/* *
 * URL: http://210.211.109.180/drmuller/api/appointment/schedule/:appointmentId
 * Parameters: appointmentId
 * Authorization: none
 * Method: GET
 * */

$app->get('/appointment/schedule/{appointmentId}', function($request, $response, $args) {
    $db = new DbOperation();
    $getAppointmentSchedule['Select_AppointmentSchedule'] = array();
    $result = array();

    $schedule = $db->getAppointmentSchedule($args['appointmentId']);
    if (empty($schedule)) {
        $result['error'] = wrong_appointment_id_message;
        $result['errorCode'] = wrong_appointment_id_code;

        array_push($getAppointmentSchedule['Select_AppointmentSchedule'], $result);

        return responseBuilder(404, $response, $getAppointmentSchedule);
    } else {
        foreach ($schedule as $time) {
            $time = (object) $time;
            array_push($getAppointmentSchedule['Select_AppointmentSchedule'], $time);
        }

        return responseBuilder(200, $response, $getAppointmentSchedule);
    }
});

/* *
 * URL: http://210.211.109.180/drmuller/api/appointment/confirm
 * Parameters: appointmentId
 * Request body:
 * {
        "userId": "AA4DDF37-8218-24BA-4515-7177298740AE",
        "appointmentId": "87C5174E-A5CC-56F0-AE40-8E76CDFB0872"
 * }
 * Authorization: Session Token
 * Method: post
 * */
$app->post('/appointment/confirm', function ($request, $response) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Update_ConfirmAppointment');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $data = (object) $request->getParsedBody();

    $requiredFieldsValidityResult = $validate->verifyRequiredFieldsWithCustomerIdAndAppointmentId($data, 'Update_ConfirmAppointment');
    if ($requiredFieldsValidityResult['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResult['response']);
    }

    $appointmentId = $data->appointmentId;

    $db = new DbOperation();
    $confirmAppointment['Update_ConfirmAppointment'] = array();
    $result = array();

    $confirmAppointmentSuccess = $db->confirmAppointment($appointmentId);

    if ($confirmAppointmentSuccess) {
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
 * URL: http://210.211.109.180/drmuller/api/appointment/cancel
 * Parameters: appointmentId
 *  * Request body:
 * {
        "userId": "87C5174E-A5CC-56F0-AE40-8E76CDFB0872",
        "appointmentId": "AA4DDF37-8218-24BA-4515-7177298740AE"
 * }
 * Authorization: Session Token
 * Method: post
 * */
$app->post('/appointment/cancel', function ($request, $response) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Update_CancelAppointment');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $data = (object) $request->getParsedBody();

    $requiredFieldsValidityResult = $validate->verifyRequiredFieldsWithCustomerIdAndAppointmentId($data, 'Update_CancelAppointment');
    if ($requiredFieldsValidityResult['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResult['response']);
    }

    $appointmentId = $data->appointmentId;

    $db = new DbOperation();
    $cancelAppointment['Update_CancelAppointment'] = array();
    $result = array();

    $cancelAppointmentSuccess = $db->cancelAppointment($appointmentId);

    if ($cancelAppointmentSuccess) {
        $result['status'] = '1';
        $result['message'] = appointment_cancel_success_message;
        $statusCode = 200;

    } else {
        $result['status'] = '0';
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;

    }

    array_push($cancelAppointment['Update_CancelAppointment'], $result);

    return responseBuilder($statusCode, $response, $cancelAppointment);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/appointment/validate
 * Parameters: none
 * Authorization: none
 * Method: post
 * */

$app->post('/appointment/validate', function ($request, $response) {
    $db = new DbOperation();
    $updateValidateAppointments['Update_ValidateAppointments'] = array();
    $result = array();

    $validateAppointmentSuccess = $db->validateAppointments();

    if ($validateAppointmentSuccess) {
        $result['message'] = appointments_validated_message;
        $statusCode = 200;
    } else {
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;
    }

    array_push($updateValidateAppointments['Update_ValidateAppointments'], $result);

    return responseBuilder($statusCode, $response, $updateValidateAppointments);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/time/book
 * Parameters: none
 * Request body:
 * {
      "locationId": "1",
      "time": [
        {
            "dayId": "1",
            "timeId": "48",
            "machineId": "1"
        },
        {
            "dayId": "1",
            "timeId": "8",
            "machineId": "1"
        }
      ]
 * }
 * Authorization: Session Token
 * Method: post
 * */

$app->post('/time/book', function ($request, $response) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'BookingTransaction');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $data = (object) $request->getParsedBody();

    $requiredFieldsValidityResultForTimeData = $validate->verifyRequiredFieldsForTimeData($data, 'BookingTransaction');

    if ($requiredFieldsValidityResultForTimeData['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResultForTimeData['response']);
    }

    $db = new DbOperation();
    $bookTime['BookingTransaction'] = array();
    $result = array();

    $bookTimeResult = $db->bookTime($data);

    if ($bookTimeResult['existed']) {
        $result['existed'] = true;
        $statusCode = 409;
    } else if ($bookTimeResult['error']) {
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;
    } else if ($bookTimeResult['success']) {
        $result['existed'] = false;
        $statusCode = 200;
    } else {
        $statusCode = 501;
    }

    array_push($bookTime['BookingTransaction'], $result);

    return responseBuilder($statusCode, $response, $bookTime);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/time/release
 * Parameters: none
 * Request body:
 * {
      "locationId": "1",
      "time": [
        {
            "dayId": "1",
            "timeId": "48",
            "machineId": "1"
        },
        {
            "dayId": "1",
            "timeId": "8",
            "machineId": "1"
        }
      ]
 * }
 * Authorization: Session Token
 * Method: post
 * */

$app->post('/time/release', function ($request, $response) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidCustomer($request, 'Update_ReleaseTime');
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $data = (object) $request->getParsedBody();

    $requiredFieldsValidityResultForTimeData = $validate->verifyRequiredFieldsForTimeData($data, 'Update_ReleaseTime');

    if ($requiredFieldsValidityResultForTimeData['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResultForTimeData['response']);
    }

    $db = new DbOperation();
    $updateReleaseTime['Update_ReleaseTime'] = array();
    $result = array();

    $releaseTimeSuccess = $db->releaseTime($data);

    if ($releaseTimeSuccess) {
        $result['status'] = '1';
        $result['message'] = time_release_success_message;
        $statusCode = 200;
    } else {
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;
    }

    array_push($updateReleaseTime['Update_ReleaseTime'], $result);

    return responseBuilder($statusCode, $response, $updateReleaseTime);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/version/release
 * Parameters: none
 * Request body:
 * {
      "version": "demoversion",
      "build": 1111,
      "os": "ios/android"
 * }
 * Authorization: Session Token
 * Method: post
 * */

$app->post('/version/release', function ($request, $response) {
    $validate = new ValidationRules();
    $validityResult = $validate->isValidAdmin($request);
    if (!$validityResult['valid']) {
        return responseBuilder(401, $response, $validityResult['response']);
    }

    $data = (object) $request->getParsedBody();

    $requiredFieldsValidityResultForStoringNewVersion = $validate->verifyRequiredFieldsForStoringNewVersion($data);

    if ($requiredFieldsValidityResultForStoringNewVersion['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResultForStoringNewVersion['response']);
    }

    $db = new DbOperation();
    $storeNewVersion['StoreNewVersion'] = array();
    $result = array();

    $buildExited = $db->buildExisted($data);

    if ($buildExited) {
        $result['error'] = build_existed_error_message;

        array_push($storeNewVersion['StoreNewVersion'], $result);

        return responseBuilder(400, $response, $storeNewVersion);
    }

    $storeNewVersionSuccess = $db->storeNewVersion($data);

    if ($storeNewVersionSuccess) {
        $result['status'] = '1';
        $result['message'] = store_version_success_message;
        $statusCode = 200;
    } else {
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;
    }

    array_push($storeNewVersion['StoreNewVersion'], $result);

    return responseBuilder($statusCode, $response, $storeNewVersion);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/admin/login
 * Parameters: none
 * Request body:
 * {
    "username": "username",
    "password": "password"
 * }
 * Authorization: none
 * Method: POST
 * */
$app->post('/admin/login', function ($request, $response) {

    $data = (object) $request->getParsedBody();

    $validate = new ValidationRules();
    $requiredFieldsValidityResult = $validate->verifyRequiredFieldsWithUsernameAndPassword($data, 'Admin Login');
    if ($requiredFieldsValidityResult['error']) {
        return responseBuilder(400, $response, $requiredFieldsValidityResult['response']);
    }

    $username = $data->username;
    $password = $data->password;

    $db = new DbOperation();

    $adminLogin['Admin Login'] = array();
    $result = array();

    $key = $db->adminLogin($username, $password);
    if (!empty($key)) {
        $result['key'] = $key;
        $statusCode = 200;

    } else {
        $result['error'] = invalid_username_or_password_message;
        $result['errorCode'] = invalid_username_or_password_code;
        $statusCode = 401;

    }

    array_push($adminLogin['Admin Login'], $result);

    return responseBuilder($statusCode, $response, $adminLogin);
});

/* *
 * URL: http://210.211.109.180/drmuller/api/version/:os
 * Parameters: none
 * Authorization: none
 * Method: GET
 * */

$app->get('/version/{os}', function($request, $response, $args) {
    $db  = new DbOperation();

    $buildResponse['Select_LatestBuild'] = array();

    $build = $db->getLatestBuild($args['os']);

    if (!empty($build)) {
        $result['build'] = $build;
        $statusCode = 200;
    } else {
        $result['error'] = internal_error_message;
        $result['errorCode'] = internal_error_code;
        $statusCode = 501;
    }

    array_push($buildResponse['Select_LatestBuild'], $result);
    return responseBuilder($statusCode, $response, $buildResponse);
});

/* *
 * Type: Helper method
 * Responsibility: Parsing data from customerInformation array to response array
 * */

function parseCustomerInformationToResponse($resultResponse, $customerInformation) {
    $resultResponse['customerId'] = $customerInformation['CUSTOMER_ID'];
    $resultResponse['customerName'] = $customerInformation['CUSTOMER_NAME'];
    $resultResponse['dob'] = $customerInformation['DOB'];
    $resultResponse['gender'] = $customerInformation['GENDER'];
    $resultResponse['phone'] = $customerInformation['PHONE'];
    $resultResponse['address'] = $customerInformation['ADDRESS'];
    $resultResponse['email'] = $customerInformation['EMAIL'];
    $resultResponse['authorizationToken'] = $customerInformation['SESSIONTOKEN'];
    $resultResponse['jwt'] = $customerInformation['JWT'];

    return $resultResponse;
 }

/* *
* Type: Helper method
* Responsibility: Parsing data from appointmentInformation array to response array
* */

function parseAppointmentInformationToResponse($resultResponse, $appointmentInformation) {
    $resultResponse['appointmentId'] = $appointmentInformation->APPOINTMENT_ID;
    $resultResponse['displayId'] = $appointmentInformation->DISPLAY_ID;
    $resultResponse['voucher'] = $appointmentInformation->VOUCHER;
//    $resultResponse['startDate'] = $appointmentInformation->START_DATE;
    $resultResponse['expiredDate'] = $appointmentInformation->EXPIRED_DATE;
    $resultResponse['type'] = $appointmentInformation->TYPE;
    $resultResponse['location'] = $appointmentInformation->LOCATION_NAME;
    $resultResponse['customerName'] = $appointmentInformation->CUSTOMER_NAME;
    $resultResponse['createdAt'] = $appointmentInformation->CREATEDAT;
    $resultResponse['isConfirmed'] = $appointmentInformation->ISCONFIRMED;
    $resultResponse['active'] = $appointmentInformation->ACTIVE;

    return $resultResponse;
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

// Run app
$app->run();
