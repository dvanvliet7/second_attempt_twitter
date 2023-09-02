<?php

class Block {
    private $ConnectionObj;


    /**
     * Create connection to DB 'php_exercise'
     */
    function createConn() {
        $this->ConnectionObj = new mysqli("localhost", "root", "", "php_exercise");
        if ($this->ConnectionObj->connect_error) {
            die("Connection failed: " . $this->ConnectionObj->connect_error);
        }
    }

    /** 
     * End the connection to DB 'php_exercise'
     */
    function endConn() {
        $this->ConnectionObj->close();
    }


    /**
     * Calculates the time passed since blocked.
     * Returns false if wait time is insufficient.
     */
    function calcTimePassed() {
        $ReturnStateBool = false;
        $CurrentTimeStampStr = date('Y-m-d H:i:s');
        $TimeStampStr = "";
        
        // $this->createConn();

        $PrepObj = $this->ConnectionObj->prepare("SELECT BlockTimeStamp FROM blocked WHERE IP=?");
        $PrepObj->bind_param("s", $_SERVER['REMOTE_ADDR']);
        $PrepObj->execute();
        $PrepObj->store_result();

        if ($PrepObj->num_rows > 0) {
            $PrepObj->bind_result($TimeStampStr);
            $PrepObj->fetch();

            $TimeInSecStr = strtotime($CurrentTimeStampStr) - strtotime($TimeStampStr);

            if ($TimeInSecStr >= 180) {
                $ReturnStateBool = true;
            } else {
                $ReturnStateBool = false;
            }
        } else {
            $ReturnStateBool = false;
            error_log("This IP Address does not exist.");
        }

        $PrepObj->close();
        // $this->endConn();
        return $ReturnStateBool;
    }

    /**
     * Resets the attempts made.
     */
    function resetAttempt() {
        // reset attempts when person is unblocked.
        $FuncObj = $this->ConnectionObj->prepare("UPDATE attempt SET AttemptAmount=0 WHERE IP=?");
        $FuncObj->bind_param("s", $_SERVER['REMOTE_ADDR']);
        $FuncObj->execute();
        $FuncObj->close();
    }

    /**
     * Remove person from blocked list.
     */
    function removeBlock() {
        $FuncObj = $this->ConnectionObj->prepare("DELETE FROM blocked WHERE IP=?");
        $FuncObj->bind_param("s", $_SERVER['REMOTE_ADDR']);
        $FuncObj->execute();
        $FuncObj->close();
    }

    /**
     * Checks if the current user is blocked.
     * Returns 'Blocked' if the IP is found.
     */
    function isBlocked() {
        $ReturnStr = "";
        $this->createConn();

        // get all blocked users
        $UserIpStr = $_SERVER['REMOTE_ADDR'];
        $PrepObj = $this->ConnectionObj->prepare("SELECT * FROM blocked where IP=?");
        $PrepObj->bind_param("s", $UserIpStr);
        $PrepObj->execute();
        $PrepObj->store_result();

        if ($PrepObj->num_rows > 0) {
            if ($this->calcTimePassed()) {
                // Time has passed.
                $this->resetAttempt();
                $this->removeBlock();
                $ReturnStr = "Not blocked";
            } else {
                // Time not passed yet.
                $ReturnStr = "Blocked";
            }
        } else {
            $ReturnStr = "Not blocked";
        }

        $this->endConn();

        return $ReturnStr;
    }

    /**
     * Blocks the cuurent IP address.
     */
    function blockPerson() { // block the current user
        $this->createConn();

        // insert a blocked person
        $UserIpStr = $_SERVER['REMOTE_ADDR'];
        $InsertBlockedPersonStr = "INSERT INTO blocked (IP) VALUES ('".$UserIpStr."')";
        $this->ConnectionObj->query($InsertBlockedPersonStr);

        $this->endConn();
    }
}