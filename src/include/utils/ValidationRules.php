<?php

class ValidationRules {

    private $usernameRegEx = '/^[A-Za-z0-9\-\_]{8,25}$/';
    private $passwordRegEx = '/^[A-Za-z0-9]{8,30}$/';
    private $customerNameRegEx = '/^[A-Za-zÀÁÂÃÈÉÊÌÍÒ
    ÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀ
    ỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồ
    ổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\\s\\-.]{0,50}$/';
    private $addressRegEx = '/^[A-Za-zÀÁÂÃÈÉÊÌÍÒ
    ÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀ
    ỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồ
    ổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ0-9\\s-.\\/]{0,150}$/';
    private $dobRegEx = '/^(19|20)\d\d[- \/.](0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])/';
    private $genderRegEx = '/^(?:Male|Female)$/';
    private $emailRegEx = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
    private $phoneRegEx = '/^\\+?[0-9]+[0-9\\-\\s]+\\(?[0-9]+\\)?[0-9\\-\\s]{0,12}$/';
    private $dateRegEx = '/^\d\d\d\d[- \/.](0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])/';
    private $typeIdRegex = '/^(?:1|2)$/';
    private $voucherIdRegEx = '/^(?:1|2)$/';
    private $verificationCodeRegEx = '/^[A-Z]{8}$/';
    private $locationIdRegEx = '/^(?:1)$/';
    private $dayIdRegEx = '/^[1-7]+$/';
    private $timeIdRegEx = '/^(?:((1|2|3|4|5)?\d)|((6)[0-6]))$/';
    private $machineIdRegEx = '/^(?:1|2)$/';
    private $versionRegEx = '/^[0-9\.]{1,10}$/';
    private $buildRegEx = '/^[0-9]{0,3}+$/';
    private $osRegEx = '/^(?:ios|android)$/';

/* *
* Type: Helper method
* Responsibility: Check session token along with customer ID for valid customer
* */

    function isValidCustomer($request, $requestName, $customerId = '') {
        $token = $request->getHeaderLine('Authorization');
        $validationResponse[$requestName] = array();
        $result = array();
        $db = new DbOperation();

        $data = (object) $request->getParsedBody();

        if (!empty($data->userId)) {
            $isValidTokenResult = $db->isValidTokenAndCustomerId($token, $data->userId);
        } else if (!empty($data->username)) {
            $isValidTokenResult = $db->isValidTokenAndUsername($token, $data->username);
        } else if (!empty($customerId)) {
            $isValidTokenResult = $db->isValidTokenAndCustomerId($token, $customerId);
        } else {
            $isValidTokenResult = $db->isValidToken($token);
        }

        if (!empty($token)) {
            if (!$isValidTokenResult) {
                $result['status'] = '0';
                $result['error'] = invalid_token_message;
                $result['errorCode'] = invalid_token_code;
            } 

        } else {
            $result['status'] = '0';
            $result['error'] = token_missing_message;
            $result['errorCode'] = token_missing_code;
        }

        array_push($validationResponse[$requestName], $result);

        return array('valid' => $isValidTokenResult, 'response' => $validationResponse);
    }

/* *
* Type: Helper method
* Responsibility: Check admin key
* */

    function isValidAdmin($request) {
        $key = $request->getHeaderLine('Authorization');
        $validationResponse['Admin'] = array();
        $result = array();
        $db = new DbOperation();

        $isValidAdminResult = $db->isValidAdmin($key);

        if (!empty($key)) {
            if (!$isValidAdminResult) {
                $result['status'] = '0';
                $result['error'] = invalid_token_message;
                $result['errorCode'] = invalid_token_code;
            }

        } else {
            $result['status'] = '0';
            $result['error'] = token_missing_message;
            $result['errorCode'] = token_missing_code;
        }

        array_push($validationResponse['Admin'], $result);

        return array('valid' => $isValidAdminResult, 'response' => $validationResponse);
    }

/* *
* Type: Helper method
* Responsibility: Verify compulsory field in request body
* */

    function verifyRequiredFieldsWithUsernameAndPassword($data, $requestName) {
        $response[$requestName] = array();
        $result = array();

        //** Check if required fields are empty
        if (empty($data->username) || empty($data->password)) {
            $result['error'] = required_fields_missing_message;
            $result['errorCode'] = required_fields_missing_code;

            array_push($response[$requestName], $result);

            return array('error' => true, 'response' => $response);
        }

        $dataArray = array(
            'username' => $data->username,
            'password' => $data->password
        );

//** Check if required fields's patterns are match
        $usernamePatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['username'], $this->usernameRegEx);
        $passwordPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['password'], $this->passwordRegEx);

        if (!$usernamePatternCheckResult['match'] || !$passwordPatternCheckResult['match']) {
            $result['status'] = '0';

            if (!empty($usernamePatternCheckResult['field'])) {
                $result['error'] = $usernamePatternCheckResult['field'] . pattern_fail_message;
            } else if (!empty($passwordPatternCheckResult['field'])) {
                $result['error'] = $passwordPatternCheckResult['field'] . pattern_fail_message;
            }
            $result['errorCode'] = pattern_fail_code;

            array_push($response[$requestName], $result);

            return array('error' => true, 'response' => $response);
        }

        return array('error' => false);

    }

/* *
* Type: Helper method
* Responsibility: Verify compulsory field in request body
* */

    function verifyRequiredFieldsForUpdateBasicInformation($data){
        $updateBasicInfo['Update_BasicInfo'] = array();
        $result = array();

//** Check if required fields are empty
        if (empty($data->userId) || empty($data->userName) || empty($data->userAddress)) {
            $result['status'] = '0';
            $result['error'] = required_fields_missing_message;
            $result['errorCode'] = required_fields_missing_code;

            array_push($updateBasicInfo['Update_BasicInfo'], $result);

            return array('error' => true, 'response' => $updateBasicInfo);
        }

        $dataArray = array(
            'customerName' => $data->userName,
            'address' => $data->userAddress
        );

//** Check if required fields's patterns are match
        $namePatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['customerName'], $this->customerNameRegEx);
        $addressPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['address'], $this->addressRegEx);

        if (!$namePatternCheckResult['match'] || !$addressPatternCheckResult['match']) {
            $result['status'] = '0';
            $result['errorCode'] = pattern_fail_code;

            if (!empty($namePatternCheckResult['field'])) {
                $result['error'] = $namePatternCheckResult['field'] . pattern_fail_message;
            } else if (!empty($addressPatternCheckResult['field'])) {
                $result['error'] = $addressPatternCheckResult['field'] . pattern_fail_message;
            }

            array_push($updateBasicInfo['Update_BasicInfo'], $result);

            return array('error' => true, 'response' => $updateBasicInfo);
        }

        return array('error' => false);

    }
    
/* *
* Type: Helper method
* Responsibility: Verify compulsory field in request body
* */

    function verifyRequiredFieldsForUpdateNecessaryInformation($data){
        $updateNecessaryInfo['Update_NecessaryInfo'] = array();
        $result = array();

//** Check if required fields are empty
        if (empty($data->userId) || empty($data->userDob) || empty($data->userGender)) {
            $result['status'] = '0';
            $result['error'] = required_fields_missing_message;
            $result['errorCode'] = required_fields_missing_code;

            array_push($updateNecessaryInfo['Update_NecessaryInfo'], $result);

            return array('error' => true, 'response' => $updateNecessaryInfo);
        }

        $dataArray = array(
            'dob' => $data->userDob,
            'gender' => $data->userGender
        );

//** Check if required fields's patterns are match
        $dobPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['dob'], $this->dobRegEx);
        $genderPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['gender'], $this->genderRegEx);

        if (!$dobPatternCheckResult['match'] || !$genderPatternCheckResult['match']) {
            $result['status'] = '0';
            $result['errorCode'] = pattern_fail_code;

            if (!empty($dobPatternCheckResult['field'])) {
                $result['error'] = $dobPatternCheckResult['field'] . pattern_fail_message;
            } else if (!empty($genderPatternCheckResult['field'])) {
                $result['error'] = $genderPatternCheckResult['field'] . pattern_fail_message;
            }

            array_push($updateNecessaryInfo['Update_NecessaryInfo'], $result);

            return array('error' => true, 'response' => $updateNecessaryInfo);
        }

        return array('error' => false);

    }
    
/* *
* Type: Helper method
* Responsibility: Verify compulsory field in request body
* */

    function verifyRequiredFieldsForUpdateImportantInformation($data){
        $updateImportantInfo['Update_ImportantInfo'] = array();
        $result = array();

//** Check if required fields are empty
        if (empty($data->userId) || empty($data->userEmail) || empty($data->userPhone)) {
            $result['status'] = '0';
            $result['error'] = required_fields_missing_message;
            $result['errorCode'] = required_fields_missing_code;

            array_push($updateImportantInfo['Update_ImportantInfo'], $result);

            return array('error' => true, 'response' => $updateImportantInfo);
        }

        $dataArray = array(
            'email' => $data->userEmail,
            'phone' => $data->userPhone
        );

//** Check if required fields's patterns are match
        $emailPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['email'], $this->emailRegEx);
        $phonePatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['phone'], $this->phoneRegEx);

        if (!$phonePatternCheckResult['match'] || !$emailPatternCheckResult['match']) {
            $result['status'] = '0';
            $result['errorCode'] = pattern_fail_code;

            if (!empty($emailPatternCheckResult['field'])) {
                $result['error'] = $emailPatternCheckResult['field'] . pattern_fail_message;
            } else if (!empty($phonePatternCheckResult['field'])) {
                $result['error'] = $phonePatternCheckResult['field'] . pattern_fail_message;
            }

            array_push($updateImportantInfo['Update_ImportantInfo'], $result);

            return array('error' => true, 'response' => $updateImportantInfo);
        }

        return array('error' => false);

    }

/* *
* Type: Helper method
* Responsibility: Verify compulsory field in request body
* */

    function verifyRequiredFieldsForUpdateCustomerEmail($data){
        $updateCustomerEmail['Update_CustomerEmail'] = array();
        $result = array();

//** Check if required fields are empty
        if (empty($data->userId) || empty($data->userEmail)) {
            $result['status'] = '0';
            $result['error'] = required_fields_missing_message;
            $result['errorCode'] = required_fields_missing_code;

            array_push($updateCustomerEmail['Update_CustomerEmail'], $result);

            return array('error' => true, 'response' => $updateCustomerEmail);
        }

        $dataArray = array(
            'email' => $data->userEmail
        );

//** Check if required fields's patterns are match
        $emailPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['email'], $this->emailRegEx);

        if (!$emailPatternCheckResult['match']) {
            $result['status'] = '0';
            $result['errorCode'] = pattern_fail_code;

            if (!empty($emailPatternCheckResult['field'])) {
                $result['error'] = $emailPatternCheckResult['field'] . pattern_fail_message;
            }

            array_push($updateCustomerEmail['Update_CustomerEmail'], $result);

            return array('error' => true, 'response' => $updateCustomerEmail);
        }

        return array('error' => false);

    }

/* *
* Type: Helper method
* Responsibility: Verify compulsory field in request body
* */

    function verifyRequiredFieldsWithCustomerIdAndAppointmentId($data, $requestName) {
        $response[$requestName] = array();
        $result = array();
        $db = new DbOperation();

//** Check if required fields are empty
        if (empty($data->userId) || empty($data->appointmentId)) {
            $result['error'] = required_fields_missing_message;
            $result['errorCode'] = required_fields_missing_code;

            array_push($response[$requestName], $result);

            return array('error' => true, 'response' => $response);
        }

        $isValidTokenResult = $db->isValidCustomerIdAndAppointmentId($data->userId, $data->appointmentId);
        if (!$isValidTokenResult) {
            $result['status'] = '0';
            $result['error'] = invalid_token_message;
            $result['errorCode'] = invalid_token_code;

            array_push($response[$requestName], $result);

            return array('error' => true, 'response' => $response);
        }

        return array('error' => false);

    }

/* *
* Type: Helper method
* Responsibility: Verify compulsory field in request body
* */

    function verifyRequiredFieldsForCreateNewAppointment($data) {
        $createAppointment['Insert_NewAppointment'] = array();
        $result = array();

//** Check if required fields are empty
        if (empty($data->startDate) || empty($data->expiredDate) || empty($data->typeId) ||
            empty($data->userId) || empty($data->voucherId) || empty($data->verificationCode) ||
            empty($data->locationId) || empty($data->time)) {

            $result['error'] = required_fields_missing_message;
            $result['errorCode'] = required_fields_missing_code;

            array_push($createAppointment['Insert_NewAppointment'], $result);

            return array('error' => true, 'response' => $createAppointment);
        }

        $requiredFieldsValidityResultForTimeData = $this->verifyRequiredFieldsForTimeData($data, 'Insert_NewAppointment');

        if ($requiredFieldsValidityResultForTimeData['error']) {
            return array('error' => true, 'response' => $requiredFieldsValidityResultForTimeData['response']);
        }

        $dataArray = array(
            'startDate' => $data->startDate,
            'expiredDate' => $data->expiredDate,
            'typeId' => $data->typeId,
            'voucherId' => $data->voucherId,
            'verificationCode' => $data->verificationCode,
            'locationId' => $data->locationId
        );

//** Check if required fields's patterns are match
        $startDatePatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['startDate'], $this->dateRegEx);
        $expireDatePatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['expiredDate'], $this->dateRegEx);
        $typeIdPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['typeId'], $this->typeIdRegex);
        $voucherIdPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['voucherId'], $this->voucherIdRegEx);
        $verificationCodePatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['verificationCode'], $this->verificationCodeRegEx);
        $locationIdPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['locationId'], $this->locationIdRegEx);

        if (!$startDatePatternCheckResult['match'] || !$expireDatePatternCheckResult['match'] || !$typeIdPatternCheckResult['match'] ||
            !$voucherIdPatternCheckResult['match'] || !$verificationCodePatternCheckResult['match'] || !$locationIdPatternCheckResult['match']) {
            $result['status'] = '0';
            $result['errorCode'] = pattern_fail_code;

            if (!empty($startDatePatternCheckResult['field'])) {
                $result['error'] = $startDatePatternCheckResult['field'] . pattern_fail_message;
            } else if (!empty($expireDatePatternCheckResult['field'])) {
                $result['error'] = $expireDatePatternCheckResult['field'] . pattern_fail_message;
            } else if (!empty($typeIdPatternCheckResult['field'])) {
                $result['error'] = $typeIdPatternCheckResult['field'] . pattern_fail_message;
            } else if (!empty($voucherIdPatternCheckResult['field'])) {
                $result['error'] = $voucherIdPatternCheckResult['field'] . pattern_fail_message;
            } else if (!empty($verificationCodePatternCheckResult['field'])) {
                $result['error'] = $verificationCodePatternCheckResult['field'] . pattern_fail_message;
            } else if (!empty($locationIdPatternCheckResult['field'])) {
                $result['error'] = $locationIdPatternCheckResult['field'] . pattern_fail_message;
            }

            array_push($createAppointment['Insert_NewAppointment'], $result);

            return array('error' => true, 'response' => $createAppointment);
        }

        return array('error' => false);

    }

/* *
* Type: Helper method
* Responsibility: Verify compulsory field in request body
* */

    function verifyRequiredFieldsForTimeData($data, $requestName) {
        $response[$requestName] = array();

        foreach ($data->time as $value) {
            $timeObj = (object) $value;

//** Check if required fields are empty
            if (empty($timeObj->dayId) || empty($timeObj->timeId) || empty($timeObj->machineId)) {
                $result['error'] = required_fields_missing_message;
                $result['errorCode'] = required_fields_missing_code;

                array_push($response[$requestName], $result);

                return array('error' => true, 'response' => $response);
            }

            $dataArray = array(
                'dayId' => $timeObj->dayId,
                'timeId' => $timeObj->timeId,
                'machineId' => $timeObj->machineId
            );

//** Check if required fields's patterns are match
            $dayIdPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['dayId'], $this->dayIdRegEx);
            $timeIdPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['timeId'], $this->timeIdRegEx);
            $machineIdPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['machineId'], $this->machineIdRegEx);

            if (!$dayIdPatternCheckResult['match'] || !$timeIdPatternCheckResult['match'] || !$machineIdPatternCheckResult['match']) {
                $result['status'] = '0';
                $result['errorCode'] = pattern_fail_code;

                if (!empty($dayIdPatternCheckResult['field'])) {
                    $result['error'] = $dayIdPatternCheckResult['field'] . pattern_fail_message;
                } else if (!empty($timeIdPatternCheckResult['field'])) {
                    $result['error'] = $timeIdPatternCheckResult['field'] . pattern_fail_message;
                } else if (!empty($machineIdPatternCheckResult['field'])) {
                    $result['error'] = $machineIdPatternCheckResult['field'] . pattern_fail_message;
                }

                array_push($response[$requestName], $result);

                return array('error' => true, 'response' => $response);
            }

        }

        return array('error' => false);
    }

/* *
* Type: Helper method
* Responsibility: Verify compulsory field in request body
* */

    function verifyRequiredFieldsForStoringNewVersion($data) {
        $response['StoreNewVersion'] = array();
        $result = array();

        //** Check if required fields are empty
        if (empty($data->version) || empty($data->build) || empty($data->os)) {
            $result['error'] = required_fields_missing_message;
            $result['errorCode'] = required_fields_missing_code;

            array_push($response['StoreNewVersion'], $result);

            return array('error' => true, 'response' => $response);
        }

        $dataArray = array(
            'version' => $data->version,
            'build' => $data->build,
            'os' => $data->os
        );

//** Check if required fields's patterns are match
        $versionPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['version'], $this->versionRegEx);
        $buildPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['build'], $this->buildRegEx);
        $osPatternCheckResult = $this->passedPatternCheck($dataArray, $dataArray['os'], $this->osRegEx);

        if (!$versionPatternCheckResult['match'] || !$buildPatternCheckResult['match'] || !$osPatternCheckResult['match']) {
            $result['status'] = '0';

            if (!empty($versionPatternCheckResult['field'])) {
                $result['error'] = $versionPatternCheckResult['field'] . pattern_fail_message;
            } else if (!empty($buildPatternCheckResult['field'])) {
                $result['error'] = $buildPatternCheckResult['field'] . pattern_fail_message;
            } else if (!empty($osPatternCheckResult['field'])) {
                $result['error'] = $osPatternCheckResult['field'] . pattern_fail_message;
            }
            $result['errorCode'] = pattern_fail_code;

            array_push($response['StoreNewVersion'], $result);

            return array('error' => true, 'response' => $response);
        }

        return array('error' => false);

    }

    //Method to check fields pattern if is is matched or not
    function passedPatternCheck($dataArray, $valueToBeChecked, $pattern) {
        $match = preg_match($pattern, $valueToBeChecked);
        if (!$match) {
            $mismatchFieldName = array_search($valueToBeChecked, $dataArray);
        } else {
            $mismatchFieldName = '';
        }
        return array('match' => $match, 'field' => $mismatchFieldName);
    }

}

?>