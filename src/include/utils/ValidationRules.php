<?php

class ValidationRules {

    private $usernameRegEx = '/^[A-Za-z0-9]{8,25}$/';
    private $passwordRegEx = '/^[A-Za-z0-9]{8,30}$/';
    private $customerNameRegEx = '/^[A-Za-zÀÁÂÃÈÉÊÌÍÒ
    ÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀ
    ỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồ
    ổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\\s\\-.]{0,50}$/';
    private $addressRegEx = '/[A-Za-z0-9\\s-.\\/]{0,150}$/';
    private $dobRegEx = '/^(19|20)\d\d[- \/.](0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])/';
    private $genderRegEx = '/^(?:Male|Female)$/';
    private $emailRegEx = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
    private $phoneRegEx = '/^\\+?[0-9]+[0-9\\-\\s]+\\(?[0-9]+\\)?[0-9\\-\\s]{0,12}$/';

/* *
* Type: Helper method
* Responsibility: Check session token along with customer ID for valid customer
* */

    function isValidCustomer($request, $requestName) {
        $token = $request->getHeaderLine('Authorization');
        $validationResponse[$requestName] = array();
        $result = array();
        $db = new DbOperation();

        $data = (object) $request->getParsedBody();

        if (empty($data->userId)) {
            $isValidTokenResult = $db->isValidToken($token);
        } else {
            $isValidTokenResult = $db->isValidTokenAndCustomerId($token, $data->userId);
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
* Responsibility: Verify compulsory field in request body
* */

    function verifyRequiredFieldsForLogin($data) {
        $customerLogin['Select_ToAuthenticate'] = array();
        $result = array();

        //** Check if required fields are empty
        if (empty($data->username) || empty($data->password)) {
            $result['error'] = required_fields_missing_message;
            $result['errorCode'] = required_fields_missing_code;

            array_push($customerLogin['Select_ToAuthenticate'], $result);

            return array('errorCode' => required_fields_missing_code, 'response' => $customerLogin);
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
            $result['errorCode'] = pattern_fail_code;

            if (!empty($usernamePatternCheckResult['field'])) {
                $result['error'] = $usernamePatternCheckResult['field'] . pattern_fail_message;
            } else if (!empty($passwordPatternCheckResult['field'])) {
                $result['error'] = $passwordPatternCheckResult['field'] . pattern_fail_message;
            }

            array_push($customerLogin['Select_ToAuthenticate'], $result);

            return array('errorCode' => required_fields_missing_code, 'response' => $customerLogin);
        }

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

            return array('errorCode' => required_fields_missing_code, 'response' => $updateBasicInfo);
        }

        $dataArray = array(
            'customerId' => $data->userId,
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

            return array('errorCode' => required_fields_missing_code, 'response' => $updateBasicInfo);
        }
    }
    
/* *
* Type: Helper method
* Responsibility: Verify compulsory field in request body
* */

    function verifyRequiredFieldsForUpdateNecessaryInformation($data){
        $UpdateNecessaryInfo['Update_NecessaryInfo'] = array();
        $result = array();

//** Check if required fields are empty
        if (empty($data->userId) || empty($data->userDob) || empty($data->userGender)) {
            $result['status'] = '0';
            $result['error'] = required_fields_missing_message;
            $result['errorCode'] = required_fields_missing_code;

            array_push($UpdateNecessaryInfo['Update_NecessaryInfo'], $result);

            return array('errorCode' => required_fields_missing_code, 'response' => $UpdateNecessaryInfo);
        }

        $dataArray = array(
            'customerId' => $data->userId,
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

            array_push($UpdateNecessaryInfo['Update_NecessaryInfo'], $result);

            return array('errorCode' => required_fields_missing_code, 'response' => $UpdateNecessaryInfo);
        }
    }
    
/* *
* Type: Helper method
* Responsibility: Verify compulsory field in request body
* */

    function verifyRequiredFieldsForUpdateImportantInformation($data){
        $UpdateImportantInfo['Update_ImportantInfo'] = array();
        $result = array();

//** Check if required fields are empty
        if (empty($data->userId) || empty($data->userEmail) || empty($data->userPhone)) {
            $result['status'] = '0';
            $result['error'] = required_fields_missing_message;
            $result['errorCode'] = required_fields_missing_code;

            array_push($UpdateImportantInfo['Update_ImportantInfo'], $result);

            return array('errorCode' => required_fields_missing_code, 'response' => $UpdateImportantInfo);
        }

        $dataArray = array(
            'customerId' => $data->userId,
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

            array_push($UpdateImportantInfo['Update_ImportantInfo'], $result);

            return array('errorCode' => required_fields_missing_code, 'response' => $UpdateImportantInfo);
        }
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