<?php

require_once dirname(__FILE__) . '/DbQueries.php';

require dirname(__FILE__) . '/MailSender/templates/Template_NotifyBooking.php';
require dirname(__FILE__) . '/MailSender/NotifyBookingMailConfig.php';

require dirname(__FILE__) . '/../.././lib/Firebase/src/BeforeValidException.php';
require dirname(__FILE__) . '/../.././lib/Firebase/src/ExpiredException.php';
require dirname(__FILE__) . '/../.././lib/Firebase/src/SignatureInvalidException.php';
require dirname(__FILE__) . '/../.././lib/Firebase/src/JWT.php';
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

    //Method to get customer by Id
    public function getCustomerByCustomerId($customerId) {
        $token = $this->getGUID();
        if (!$this->updateSessionToken($customerId, $token)) {
            return array();
        }

        $sql = query_Select_CustomerInfoByCustomerId;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $customerId);
        $stmt->execute();
        $resultArray = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (empty($resultArray['CUSTOMER_ID'])) {
            return array();
        } else {
            $jwt = $this->createJwt($resultArray);
            $resultArray['JWT'] = $jwt;

            return $resultArray;
        }

    }

    //Method to get customer by username
    public function getCustomerByUsername($username) {
        // $token = $this->getGUID();
        // $this->updateSessionToken($customerId, $token);

        $sql = query_Select_CustomerInfoByUsername;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $username);
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

        $this->con->autocommit(false);

        $registerResult = $this->insertNewCustomer($username, $password);
        $storeTokenSuccess = $this->storeSessionToken($username, $password);

        if (!empty($registerResult) && $storeTokenSuccess) {
            $this->con->commit();
            $this->con->autocommit(true);

            return $registerResult;
        } else {
            $this->con->rollback();
            $this->con->autocommit(true);

            return '';
        }
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

        if ($result == 1) {
            return true;
        } else {
            return false;
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

        if ($result == 1) {
            return true;
        } else {
            return false;
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

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }

    //Method to update customer's all information
    public function updateCustomerInformation($informationArray) {
        $customerId = $informationArray['customerId'];
        $customerName = $informationArray['customerName'];
        $address = $informationArray['address'];
        $dob = $informationArray['dob'];
        $gender = $informationArray['gender'];
        $email = $informationArray['email'];
        $phone = $informationArray['phone'];
        $updatedAt = $this->getCurrentDateTime();

        $sql = query_Update_CustomerInformation;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ssssssss', $customerName, $address, $dob, $gender, $phone, $email, $updatedAt, $customerId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }

    //Method to update customer's email
    public function updateCustomerEmail($informationArray) {
        $customerId = $informationArray['customerId'];
        $email = $informationArray['email'];
        $updatedAt = $this->getCurrentDateTime();

        $sql = query_Update_CustomerEmail;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('sss', $email, $updatedAt, $customerId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }

    //Method to reset password
    public function resetPassword($username, $password) {
        $saltAndPassword = $this->saltEncode($password);
        $salt = $saltAndPassword['salt'];
        $encodedPassword = $saltAndPassword['password'];

        $sql = query_Update_ResetPassword;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('sss', $encodedPassword, $salt, $username);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }

    //Method to verify customer after email verification process
    public function confirmCustomer($customerId) {
        $sql = query_Update_ConfirmCustomer;

        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $customerId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }

    //Method to create new appointment
    public function createAppointment($data) {
        $this->con->autocommit(false);

        $appointmentId = $this->insertNewAppointment($data);
        $insertBookingScheduleSuccess = $this->insertNewBookingSchedule($data, $appointmentId);
        $releaseTimeSuccess = $this->releaseTime($data);

        if ($insertBookingScheduleSuccess && $releaseTimeSuccess) {
            $this->con->commit();
            $this->con->close();

            return $appointmentId;
        } else {
            $this->con->rollback();
            $this->con->autocommit(true);

            return '';
        }
    }

    //Method to confirm appointment
    public function confirmAppointment($appointmentId) {
        $sql = query_Update_ConfirmAppointment;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $appointmentId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }

    //Method to cancel appointment
    public function cancelAppointment($appointmentId) {
        $sql = query_Update_CancelAppointment;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $appointmentId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }

    //Method to validate appointments
    public function validateAppointments() {
//        $array = $this->getAppointmentSchedule('455B5066-B054-05A1-0517-5AF7CA894998');
//        $time = '';
//
//        echo '\n' . $array;
//
//        foreach ($array as $item) {
//            $timeObj = (object) $item;
//            $time .= '
//                <div class="row">
//                    <span class="subject">
//                '
//                .
//                $timeObj->DAY . ' - ' . $timeObj->TIME . ' ' . $timeObj->MACHINE_NAME
//                .
//                '
//                    </span>
//                </div>
//            ';
//        }
//
//        echo '\n' . $time;

        $sql = query_Update_ValidateAppointments;
        $stmt = $this->con->prepare($sql);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
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

    //Method to book time
    public function bookTime($data) {
        $existedResponse = array('existed' => true, 'error' => false, 'success' => false);
        $errorResponse = array('existed' => false, 'error' => true, 'success' => false);
        $successResponse = array('existed' => false, 'error' => false, 'success' => true);

        $this->con->autocommit(false);

        if ($this->timeExisted($data)) {
            $this->con->rollback();
            $this->con->autocommit(true);

            return $existedResponse;
        }

        $temporaryTimeResult = $this->temporaryTimeExisted($data);

        if (!$temporaryTimeResult['success']) {
            $this->con->rollback();
            $this->con->autocommit(true);

            return $errorResponse;
        }

        if ($temporaryTimeResult['existed']) {
            $setActiveTemporaryTimeResult = $this->setActiveTemporaryTime($data);

            if (!$setActiveTemporaryTimeResult['success']) {
                $this->con->rollback();
                $this->con->autocommit(true);

                return $errorResponse;
            }

            if ($setActiveTemporaryTimeResult['existed']) {
                $this->con->rollback();
                $this->con->autocommit(true);

                return $existedResponse;
            } else {
                $this->con->commit();

                return $successResponse;
            }
        } else {
            $insertNewTemporaryTimeSuccess = $this->insertNewTemporaryTime($data);

            if ($insertNewTemporaryTimeSuccess) {
                $this->con->commit();

                return $successResponse;
            } else {
                $this->con->rollback();
                $this->con->autocommit(true);

                return $errorResponse;
            }
        }
    }

    //Method to release time
    public function releaseTime($data) {

        $whereStmtToReleaseTime = $this->formStringForWhereStatementToReleaseTime($data);
        //**Example: LOCATION_ID = 1 AND DAY_ID = 1 AND TIME_ID = 2 AND MACHINE_ID = 2
        // OR LOCATION_ID = 1 AND DAY_ID = 1 AND TIME_ID = 2 AND MACHINE_ID = 2

        $sql = query_Update_ReleaseTime . $whereStmtToReleaseTime;
        $stmt = $this->con->prepare($sql);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }

    //Method to check time existence
    public function timeExisted($data) {
        $time = $data->time;

        foreach ($time as $value) {
            $timeObj = (object) $value;
            $selectedTimeArray = $this->getSelectedTime($timeObj->dayId, $data->locationId, $timeObj->machineId);

            while ($selectedTimeObj = $selectedTimeArray->fetch_object()) {
                if ($selectedTimeObj->TIME_ID == $timeObj->timeId) {
                    return true;
                }
            }
        }

        return false;
    }

    //Method to send email notifying new appointment
    public function notifyBooking($appointmentId) {
        $appointmentInfo = $this->getAppointment($appointmentId);
        $appointmentSchedule = $this->getAppointmentSchedule($appointmentId);
        $appointmentInfo['timeArray'] = $appointmentSchedule;

        $emailBody = \templates\email\EmailTemplate::getNotifyBookingTemplate($appointmentInfo);

        $mailSender = new NotifyBookingMailConfig();
        $mailSender = $mailSender->getMailSender();

        $mailSender->Body = $emailBody;
        while (!$mailSender->send()) {}

        return true;
    }

    //Method to let customer login
    public function adminLogin($username, $password) {
        $decodedPassword = $this->saltDecode($username, $password);

        if (!$this->updateKey($username, $this->getGUID())) {
            return '';
        }

        $sql = query_Select_AdminKey;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ss', $username, $decodedPassword);
        $stmt->execute();
        $key = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $key['SECRETKEY'];
    }

    //Method to get latest build number
    public function getLatestBuild($os) {
        $sql = query_Select_LatestBuildNumber;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ss',$os, $os);
        $stmt->execute();
        $build = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $build['BUILD'];
    }

    //Method to get build number
    public function buildExisted($data) {
        $version = $data->version;
        $build = $data->build;
        $os = $data->os;

        $sql = query_Select_BuildNumber;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('sis',$version, $build, $os);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

        return $num_rows > 0;
    }

    //Method to store new version
    public function storeNewVersion($data) {
        $this->con->autocommit(false);

        if ($this->setInactiveLatestVersion($data) && $this->insertNewVersion($data)) {
            $this->con->commit();

            return true;
        } else {
            $this->con->rollback();
            $this->con->autocommit(true);

            return false;
        }

    }

    //Method to insert new release version
    private function insertNewVersion($data) {
        $version = $data->version;
        $build = $data->build;
        $os = $data->os;

        $sql = query_Insert_NewVersion;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('sis', $version, $build, $os);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }

    //Method to set latest version to be inactive
    private function setInactiveLatestVersion($data) {
        $sql = query_Update_VersionSetInactive;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ss', $data->os, $data->os);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }

    //Method to get appointment schedule for email template, ex: dayId, timeId, machineId
    private function getAppointmentSchedule($appointmentId) {
        $sql = query_Select_AppointmentScheduleForEmail;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $appointmentId);
        $result = $stmt->execute();
        $resultArray = $stmt->get_result();
        $array = array();

        if ($result == 1) {
            while($row = $resultArray->fetch_object()) {
                array_push($array, $row);
            }
            return $array;
        } else {
            return '';
        }
    }

    //Method to check if temporary time has existed already
    private function temporaryTimeExisted($data) {
        $convertedData = $this->getTimeDataArray($data);

        $sql = query_Select_TemporarySelectedTime;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ssss', $convertedData['dayId'], $convertedData['timeId'], $convertedData['locationId'], $convertedData['machineId']);
        $result = $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

        if ($result == 1) {
            $success = true;
        } else {
            $success = false;
        }

        return array('success' => $success, 'existed' => $num_rows > 0);
    }

    //Method to set active temporary time
    private function setActiveTemporaryTime($data) {
        $convertedData = $this->getTimeDataArray($data);

        $sql = query_Update_TemporaryTimeSetActive;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ssss', $convertedData['dayId'], $convertedData['timeId'], $convertedData['locationId'], $convertedData['machineId']);
        $result = $stmt->execute();
        $stmt->store_result();
        $affectedRow = $stmt->affected_rows;
        $stmt->close();

        if ($result == 1) {
            $success = true;
        } else {
            $success = false;
        }

        return array('success' => $success, 'existed' => $affectedRow < 0);
    }

    //Method to insert new temporary time
    private function insertNewTemporaryTime($data) {
        $convertedData = $this->getTimeDataArray($data);

        $sql = query_Insert_NewTemporaryTime;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ssss', $convertedData['dayId'], $convertedData['timeId'], $convertedData['locationId'], $convertedData['machineId']);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }

    //Method to get locationId, dayId, timeId, machineId from data
    private function getTimeDataArray($data) {
        $locationId = $data->locationId;
        $time = $data->time;

        foreach ($time as $value) {
            $timeObj = (object) $value;

            $dayId = $timeObj->dayId;
            $timeId = $timeObj->timeId;
            $machineId = $timeObj->machineId;

            return array(
                'locationId' => $locationId,
                'dayId' => $dayId,
                'timeId' => $timeId,
                'machineId' => $machineId
            );
        }

        return array();
    }

    //Method to build a where clause from array of object to release time
    private function formStringForWhereStatementToReleaseTime($data) {
        $time = $data->time;
        $locationId = $data->locationId;
        $sqlWhereStmt = '';
        $index = 0;
        foreach ($time as $value) {
            $timeObj = (object) $value;

            $sqlWhereStmt = $sqlWhereStmt
                . 'LOCATION_ID = '
                . $locationId
                . ' AND '
                . 'DAY_ID = '
                . $timeObj->dayId
                . ' AND '
                . 'TIME_ID = '
                . $timeObj->timeId
                . ' AND '
                . 'MACHINE_ID = '
                . $timeObj->machineId;

            $index++;

            if ($index == count($time)) {
                break;
            }
            $sqlWhereStmt = $sqlWhereStmt . ' OR ';

        }

        return $sqlWhereStmt;
    }

    //Method to build a values clause from array of object to insert
    private function formStringToInsertNewBookingSchedule($data, $appointmentId) {
        $time = $data->time;
        $locationId = $data->locationId;
        $sqlInsertStmt = '';
        $index = 0;
        foreach ($time as $value) {
            $timeObj = (object) $value;

            $sqlInsertStmt = $sqlInsertStmt
                . '('
                . $timeObj->dayId
                . ', '
                . $timeObj->timeId
                . ', '
                . $timeObj->machineId
                . ', '
                . $locationId
                . ', '
                . '\''
                . $appointmentId
                . '\''
                . ')';

            $index++;

            if ($index == count($time)) {
                break;
            }
            $sqlInsertStmt = $sqlInsertStmt . ', ';
        }

        return $sqlInsertStmt;
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

    //Method to check sessionToken and username is valid
    public function isValidTokenAndUsername($token, $username) {
        $sql = query_Select_CustomerId_FromTokenAndUsername;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ss', $token, $username);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

        return $num_rows > 0;
    }

    //Method to check customer Id and appointment Id is valid
    public function isValidCustomerIdAndAppointmentId($customerId, $appointmentId) {
        $sql = query_Select_CustomerId_FromCustomerIdAndAppointmentId;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ss', $customerId, $appointmentId);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

        return $num_rows > 0;
    }

    //Method to check admin key is valid
    public function isValidAdmin($key) {
        $sql = query_Select_AdminId_FromKey;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('s', $key);
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
        $customerId = $this->getGUID();

        $sql = query_Insert_NewCustomer;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ssss', $customerId, $username, $encodedPassword, $salt);
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result == 1) {
            //Register success: 0 -> No error
            return $customerId;
        } else {
            //Register failed: 1 -> There is error
            return '';
        }
    }

    //Method to insert new appointment
    private function insertNewAppointment($data) {
        $appointmentId = $this->getGUID();
        $startDate = $data->startDate;
        $expiredDate = $data->expiredDate;
        $typeId = $data->typeId;
        $userId = $data->userId;
        $voucherId = $data->voucherId;
        $verificationCode = $data->verificationCode;
        $locationId = $data->locationId;

        $sql = query_Insert_NewAppointment;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ssssssss', $appointmentId, $startDate, $expiredDate, $typeId, $userId, $voucherId, $verificationCode, $locationId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return $appointmentId;
        } else {
            return '';
        }
    }

    //Method to insert new booking schedule
    private function insertNewBookingSchedule($data, $appointmentId) {
        $valuesToInsert = $this->formStringToInsertNewBookingSchedule($data, $appointmentId);
        $sql = query_Insert_BookingSchedule . $valuesToInsert;

        $stmt = $this->con->prepare($sql);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
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

        if (empty($customerId)) {
            return false;
        }

        $token = $this->getGUID();
        $sql = query_Store_SessionToken;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ss', $customerId, $token);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }

    //Method to update sessionToken
    private function updateSessionToken($customerId, $token) {
        $currentDateTime = $this->getCurrentDateTime();

        $sql = query_Update_SessionToken;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('sss', $token, $currentDateTime, $customerId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }

    //Method to update admin secret key
    private function updateKey($username, $key) {
        $sql = query_Update_AdminKey;
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param('ss', $key, $username);
        $result = $stmt->execute();
        $stmt->close();

        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }

    //Method to get user jwt
    private function createJwt($result) {
        $tokenId    = base64_encode(mcrypt_create_iv(32));
        $issuedAt   = time();
        //$notBefore  = $issuedAt + 10;             //Adding 10 seconds
        $expire     = $issuedAt + 60;            // Adding 60 seconds
        $serverName = 'drmuller'; // Retrieve the server name from config file

        /*
        * Create the token as an array
        */
        $data = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss'  => $serverName,       // Issuer
            //'nbf'  => $notBefore,        // Not before
            'exp'  => $expire,           // Expire
            'data' => [                  // Data related to the signer user
                'userId'   => $result['CUSTOMER_ID'], // userid from the users table
                'userName' => $result['CUSTOMER_NAME'], // User name
                'userDob'  => $result['DOB'],
                'userGender' => $result['GENDER'],
                'userPhone'=> $result['PHONE'],
                'userAddress' => $result['ADDRESS'],
                'userEmail' => $result['EMAIL'],
                'step' => $result['UISAVEDSTEP'],
                'active' => $result['ACTIVE'],
                'sessionToken' => $result['SESSIONTOKEN']
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

    static function
    Prettify($msg) {
        echo '<br>' . $msg;
    }
}