<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/drmuller/src/include/DbQueries.php');

    class TimeDAO {
        private $con;
        
        function __construct() {
            require_once ($_SERVER['DOCUMENT_ROOT'] . '/drmuller/src/include/DbConnect.php');
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

    }

?>