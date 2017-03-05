<?php

    // require_once ($_SERVER['DOCUMENT_ROOT'] . '/drmuller/src/include/DbQueries.php');

    class DataSourceDAO {
        private $con;

        function __contruct() {
            require_once ($_SERVER['DOCUMENT_ROOT'] . '/drmuller/src/include/DbConnect.php');
            $db = new DbConnect();
            $this->con = $db->connect();
        }

        //Method to get all countries
        public function getCountries() {
            $sql = "SELECT co.COUNTRY_ID, co.COUNTRY FROM icaredb.tbl_countries co";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $countries = $stmt->get_result();

            return $countries;
        }

        //Method to get all cities 
        public function getCities($countryId) {
            $sql = "SELECT ci.CITY_ID, ci.CITY FROM tbl_cities ci WHERE ci.COUNTRY_ID = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param('s', $countryId);
            $stmt->execute();
            $countries = $stmt->get_result();

            return $countries;
        }

        //Method to get all district 
        public function getDistrict($cityId) {
            $sql = "SELECT di.DISTRICT_ID, di.DISTRICT FROM tbl_districts di where di.CITY_ID = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param('s', $cityId);
            $stmt->execute();
            $countries = $stmt->get_result();

            return $countries;
        }

    }

?>