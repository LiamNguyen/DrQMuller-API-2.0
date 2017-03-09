<?php

require_once dirname(__FILE__) . '/DbQueries.php';
require $_SERVER['DOCUMENT_ROOT'] . '/drmuller/lib/Firebase/src/BeforeValidException.php';
require $_SERVER['DOCUMENT_ROOT'] . '/drmuller/lib/Firebase/src/ExpiredException.php';
require $_SERVER['DOCUMENT_ROOT'] . '/drmuller/lib/Firebase/src/SignatureInvalidException.php';
require $_SERVER['DOCUMENT_ROOT'] . '/drmuller/lib/Firebase/src/JWT.php';
use Firebase\JWT\JWT;

class DbOperation
{
    private $con;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }

    //Method to get all the time
    public function getAllTime() {
        $sql = query_Select_AllTime;
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $allTime = $stmt->get_result();
        $stmt->close();

        return $allTime;        
    }

    //Method to get eco time
    public function getEcoTime() {
        $sql = query_Select_EcoTime;
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $ecoTime = $stmt->get_result();
        $stmt->close();

        return $ecoTime;
    }

    //Method to get all countries
    public function getCountries() {
        $sql = query_Select_Countries;
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $countries = $stmt->get_result();
        $stmt->close();

        return $countries;
    }

    //Method to get all cities 
    public function getCities($countryId) {
        $sql = query_Select_Cities;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $countryId);
        $stmt->execute();
        $cities = $stmt->get_result();
        $stmt->close();

        return $cities;
    }

    //Method to get all district 
    public function getDistricts($cityId) {
        $sql = query_Select_Districts;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $cityId);
        $stmt->execute();
        $countries = $stmt->get_result();
        $stmt->close();

        return $countries;
    }

    //Method to get all days in week 
    public function getWeekDays() {
        $sql = query_Select_DaysOfWeek;
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $weekdays = $stmt->get_result();
        $stmt->close();

        return $weekdays;
    }

    //Method to get locations
    public function getLocations($districtId) {
        $sql = query_Select_Locations;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $districtId);
        $stmt->execute();
        $locations = $stmt->get_result();
        $stmt->close();

        return $locations;
    }

    //Method to get machines
    public function getMachines($locationId) {
        $sql = query_Select_Machines;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $locationId);
        $stmt->execute();
        $machines = $stmt->get_result();
        $stmt->close();

        return $machines;
    }

    //Method to get types
    public function getTypes() {
        $sql = query_Select_Types;
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $types = $stmt->get_result();
        $stmt->close();

        return $types;
    }    

    //Method to get vouchers
    public function getVouchers() {
        $sql = query_Select_Vouchers;
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $vouchers = $stmt->get_result();
        $stmt->close();

        return $vouchers;
    } 

    //Method to get machines
    public function getSelectedTime($dayId, $locationId, $machineId) {
        $sql = query_Select_SelectedTime;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ssssss', $dayId, $locationId, $machineId, $dayId, $locationId, $machineId);
        $stmt->execute();
        $selectedTime = $stmt->get_result();
        $stmt->close();

        return $selectedTime;
    }

    //Method to get customer 
    public function getCustomer($customerId) {
        // $token = $this->getGUID();
        // $this->updateSessionToken($customerId, $token);

        $sql = query_Select_CustomerInfo;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $resultArray = $result->fetch_assoc();
        $stmt->close();
        
        $jwt = $this->createJwt($result);
        $resultArray['JWT'] = $jwt;

        if (empty($resultArray['CUSTOMER_ID'])) {
            return array();
        } else {
            return $resultArray;
        }

    }

    //Method to let customer login 
    public function customerLogin($username, $password) {
        $decodedPassword = $this->saltDecode($username, $password);

        $sql = query_Select_CustomerId;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ss', $username, $decodedPassword);
        $stmt->execute();
        $customerId = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $customerId['CUSTOMER_ID'];
    }

    //Method to let customer register
    public function customerRegister($username, $password) {
        if ($this->isCustomerExist($username)) {
            return 2;
        }
        $registerResult = $this->insertNewCustomer($username, $password);
        $this->storeSessionToken($username, $password);
        return $registerResult;
    }

    //Method to update basic information of user 
    public function updateBasicInformation($informationArray) {
        $customerId = $informationArray['customerId'];
        $customerName = $informationArray['customerName'];
        $address = $informationArray['address'];
        $updatedAt = $this->getCurrentDateTime();
        
//**
//Decide to rewriting UiSavedStep flag in database or not, if it is first time for basic information to be saved
        if ($this->getUiFillStep($customerId) == 'none') {
        //** Rewriting flag
            $sql = query_Update_BasicInformation_FirstTime;
        } else {
        //** Not rewriting flag
            $sql = query_Update_BasicInformation;
        }
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ssss', $customerName, $address, $updatedAt, $customerId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            //Confirm success: 0 -> No error
            return 0;
        } else {
            //Confirm failed: 1 -> There is error
            return 1;
        }
    }

    //Method to update basic information of user 
    public function updateNecessaryInformation($informationArray) {
        $customerId = $informationArray['customerId'];
        $dob = $informationArray['dob'];
        $gender = $informationArray['gender'];
        $updatedAt = $this->getCurrentDateTime();
        
//**
//Decide to rewriting UiSavedStep flag in database or not, if it is first time for basic information to be saved
        if ($this->getUiFillStep($customerId) == 'basic') {
        //** Rewriting flag
            $sql = query_Update_NecessaryInformation_FirstTime;
        } else {
        //** Not rewriting flag
            $sql = query_Update_NecessaryInformation;
        }
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ssss', $dob, $gender, $updatedAt, $customerId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            //Confirm success: 0 -> No error
            return 0;
        } else {
            //Confirm failed: 1 -> There is error
            return 1;
        }
    }

//Method to update basic information of user 
    public function updateImportantInformation($informationArray) {
        $customerId = $informationArray['customerId'];
        $email = $informationArray['email'];
        $phone = $informationArray['phone'];
        $updatedAt = $this->getCurrentDateTime();

        $sql = query_Update_ImportantInformation;

        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ssss', $email, $phone, $updatedAt, $customerId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            //Confirm success: 0 -> No error
            return 0;
        } else {
            //Confirm failed: 1 -> There is error
            return 1;
        }
    }

//Method to verify customer after email verification process
    public function confirmCustomer($customerId) {
        $sql = query_Update_ConfirmCustomer;

        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $customerId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            //Confirm sucess: 0 -> No error
            return 0;
        } else {
            //Confirm failed: 1 -> There is error
            return 1;
        }
    }

//Method to confirm appointment
    public function confirmAppointment($appointmentId) {
        $sql = query_Update_ConfirmAppointment;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $appointmentId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            //Confirm success: 0 -> No error
            return 0;
        } else {
            //Confirm failed: 1 -> There is error
            return 1;
        }
    }

    //Method to validate appointments
    public function validateAppointments() {
        $sql = query_Update_ValidateAppointments;
        $stmt = $this->con->prepare($sql);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            //Confirm success: 0 -> No error
            return 0;
        } else {
            //Confirm failed: 1 -> There is error
            return 1;
        }
    }

    //Method to get appointment
    public function getAppointment($appointmentId) {

        $sql = query_Select_Appointment;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $appointmentId);
        $stmt->execute();
        $resultArray = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (empty($resultArray['APPOINTMENT_ID'])) {
            return array();
        } else {
            return $resultArray;
        }

    }

    //Method to check sessionToken is valid
    public function isValidToken($token) {
        $sql = query_Select_CustomerId_FromToken;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

        return $num_rows > 0;
    }

    //Method to check sessionToken and customer ID is valid
    public function isValidTokenAndCustomerId($token, $customerId) {
        $sql = query_Select_CustomerId_FromTokenAndCustomerId;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ss', $token, $customerId);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

        return $num_rows > 0;
    }

    //Method to check userame existence
    private function isCustomerExist($username) {
        $sql = query_Select_CustomerId_FromUsername;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

        return $num_rows > 0;
    }

    //Method to insert new customer
    private function insertNewCustomer($username, $password) {
        $saltAndPassword = $this->saltEncode($password);
        $salt = $saltAndPassword['salt'];
        $encodedPassword = $saltAndPassword['password'];

        $sql = query_Insert_NewCustomer;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('sss', $username, $encodedPassword, $salt);
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            //Register success: 0 -> No error
            return 0;
        } else {
            //Register failed: 1 -> There is error
            return 1;
        }
    }

    //Method to encode salt and password for registration
    private function saltEncode($password) {
        //Create random salt
        $salt = bin2hex(random_bytes(32));

        //Prepend salt to password
        $password = $salt . $password;

        //Hash password
        $password = hash('sha256', $password);

        return array(
            'salt' => $salt, 
            'password' => $password
        );
    }

    //Method to decode salt and password for login
    private function saltDecode($username, $password) {
        $salt = $this->selectSaltFromUsername($username);

        //Prepend salt to password
        $password = $salt . $password;

        //Hash password
        $password = hash('sha256', $password);

        return $password;
    }

    //Method to select salt from username
    private function selectSaltFromUsername($username) {
        $sql = query_Select_Salt;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $salt = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $salt['SALT'];
    }

    //Method to store sessionToken to database
    private function storeSessionToken($username, $password) {
        $customerId = $this->customerLogin($username, $password);
        $token = $this->getGUID();

        $sql = query_Store_SessionToken;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ss', $customerId, $token);
        $stmt->execute();
        $stmt->close();
    }

    //Method to update sessionToken
    private function updateSessionToken($customerId, $token) {
        $currentDateTime = $this->getCurrentDateTime();

        $sql = query_Update_SessionToken;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('sss', $token, $currentDateTime, $customerId);
        $stmt->execute();
        $stmt->close();
    }

    //Method to get user jwt
    private function createJwt($result) {
        if (mysqli_num_rows($result) <= 0) {
            return '';
        }

        $row = mysqli_fetch_assoc($result);
        $tokenId    = base64_encode(mcrypt_create_iv(32));
        $issuedAt   = time();
        $notBefore  = $issuedAt + 10;             //Adding 10 seconds
        $expire     = $notBefore + 60;            // Adding 60 seconds
        $serverName = 'drmuller'; // Retrieve the server name from config file

        /*
        * Create the token as an array
        */
        $data = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss'  => $serverName,       // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expire,           // Expire
            'data' => [                  // Data related to the signer user
                'userId'   => $row['CUSTOMER_ID'], // userid from the users table
                'userName' => $row['CUSTOMER_NAME'], // User name
                'userDob'  => $row['DOB'],
                'userGender' => $row['GENDER'],
                'userPhone'=> $row['PHONE'],
                'userAddress' => $row['ADDRESS'],
                'userEmail' => $row['EMAIL'],
                'step' => $row['UISAVEDSTEP'],
                'active' => $row['ACTIVE'],
                'sessionToken' => $row['SESSIONTOKEN']
            ]
        ];
        $secretKey = 'drmuller';
        
        $jwt = JWT::encode(
        $data,      //Data to be encoded in the JWT
        $secretKey, // The signing key
        'HS512'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );
        
        return $jwt;
    }

    //Method to select UiFillStep
    private function getUiFillStep($customerId) {
        $sql = query_Select_UiFillStep;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $customerId);
        $stmt->execute();
        $uiFillStep = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $uiFillStep['UISAVEDSTEP'];
    }

    //Method to get current date time in string format
    private function getCurrentDateTime() {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $now = new DateTime('now');
        return date_format($now, 'Y-m-d H:i:s');
    }

    //Method to generate GUID
    private function getGUID(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        } else {
            $charId = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            $uuid = substr($charId, 0, 8).$hyphen
                .substr($charId, 8, 4).$hyphen
                .substr($charId,12, 4).$hyphen
                .substr($charId,16, 4).$hyphen
                .substr($charId,20,12);
            return $uuid;
        }
    }
}