<?php

class SignUp {
    public string $FirstNameStr;
    public string $LastNameStr;
    public string $UserNameStr;
    public string $EmailAddressStr;
    public string $PasswordStr;
    public int $GlobalIdInt;

    public string $EnteredPassStr;

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
     * Validates user's Name.
     * Returns a boolean expression. Returns false if invalid.
     */
    function validName() {
        if (strlen($this->FirstNameStr) < 100 && strlen($this->FirstNameStr) > 2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validates user's Surname.
     * Returns a boolean expression. Returns false if invalid.
     */
    function validSurname() {
        if (strlen($this->LastNameStr) < 100 && strlen($this->LastNameStr) > 2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if email exists in the DB.
     * Returns a boolean expression. Returns false if nothing found.
     */
    function checkEmailExists($EmailStr) {
        $IDInt = 0;
        $ReturnBool = false;
        $this->createConn();

        // prepare and bind
        $PrepObj = $this->ConnectionObj->prepare("SELECT ID FROM user WHERE EmailAddress=?");
        $PrepObj->bind_param("s", $EmailStr);
        // execute
        $PrepObj->execute();
        // Store the result so we can check if the email exists in the database.
        $PrepObj->store_result();

        if ($PrepObj->num_rows > 0) {
            // does exist
            $PrepObj->bind_result($IDInt);
            $PrepObj->fetch();
            $ReturnBool = true;
        } else {
            // does NOT exist
            $ReturnBool = false;
        }

        $PrepObj->close();
        $this->endConn();

        return $ReturnBool;
    }

    /**
     * checks if username exists in the DB.
     * Returns a boolean expression.
     * Returns false if nothing is found.
     */
    function checkUserExists($UserStr) {
        $IDInt = 0;
        $ReturnStr = false;
        $this->createConn();

        // prepare and bind
        $PrepObj = $this->ConnectionObj->prepare("SELECT ID FROM user WHERE Username=?");
        $PrepObj->bind_param("s", $UserStr);
        // execute
        $PrepObj->execute();
        // Store the result so we can check if the account exists in the database.
        $PrepObj->store_result();

        if ($PrepObj->num_rows > 0) {
            // does exist
            $PrepObj->bind_result($IDInt);
            $PrepObj->fetch();
            $ReturnStr = true;
        } else {
            // does NOT exist
            $ReturnStr = false;
        }

        $this->endConn();

        return $ReturnStr;
    }

    /**
     * Validates the username given.
     * Returns a boolean expression. Returns false if invalid.
     */
    function validateUsername($UserStr) {
        if ($UserStr == " " || $UserStr == "") {
            return false;
        } else {
            if (strlen($UserStr) > 6 && strlen($UserStr) < 60) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Checks if password is strong.
     * Returns a boolean expression. Returns false if not sufficient
     */
    function testPassword($PasswordStr) {
        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $PasswordStr);
        $lowercase = preg_match('@[a-z]@', $PasswordStr);
        $number    = preg_match('@[0-9]@', $PasswordStr);
        $specialChars = preg_match('/[!@#$%^&*()_+{}\[\]:;<>,.?~]/', $PasswordStr);

        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($PasswordStr) < 8) {
            // insufficient password
            return false;
        } else {
            // sufficient password
            return true;
        }
    }

    /**
     * Validates email address.
     * Returns a boolean expression. Returns false if not valid.
     */
    function validateEmail($EmailStr) {
        if (filter_var($EmailStr, FILTER_VALIDATE_EMAIL)) {
            $atPos = mb_strpos($EmailStr, '@');
            // Select the domain
            $DomainStr = mb_substr($EmailStr, $atPos + 1);
            if (checkdnsrr($DomainStr . '.', 'MX')) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Corrects the capitalisation.
     */
    function correctName($NameStr) {
        $NameStr = strtolower($NameStr);
        $NameStr = ucfirst($NameStr);
        return $NameStr;
    }



    /**
     * Checks the sign up inputs.
     * Returns 'none' if no faults are found.
     */
    function checkSignUpFields() {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $this->FirstNameStr) || $this->FirstNameStr == "" || $this->FirstNameStr == " " || !$this->validName()) {
            return "nameErr";
        } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $this->LastNameStr) || $this->LastNameStr == "" || $this->LastNameStr == " " || !$this->validSurname()) {
            return "surnameErr";
        } elseif (!$this->validateEmail($this->EmailAddressStr)) {
            error_log("here");
            return "emailErr";
        } elseif ($this->checkEmailExists($this->EmailAddressStr)) {
            error_log("No here");
            return "emailErr";
        } elseif ($this->checkUserExists($this->UserNameStr)) {
            return "userErr";
        } elseif (!$this->validateUsername($this->UserNameStr)) {
            return "userErr";
        } elseif (!$this->testPassword($this->PasswordStr)) {
            return "passErr";
        } else {
            $this->FirstNameStr = $this->correctName($this->FirstNameStr);
            $this->LastNameStr = $this->correctName($this->LastNameStr);
            return "none";
        }
    }


    /**
     * Adds the new entry to the DB.
     */
    function completeSignUp($PassStr, $UserpfpStr) {
        $FirstnameStr = $this->FirstNameStr;
        $LastnameStr = $this->LastNameStr;
        $EmailStr = $this->EmailAddressStr;
        $UsernameStr = $this->UserNameStr;

        $this->createConn();

        // insert
        $SignUpStr = "INSERT INTO user (FirstName, LastName, EmailAddress, Username, Password, Userpfp) VALUES (?, ?, ?, ?, ?, ?)";
        $PrepObj = $this->ConnectionObj->prepare($SignUpStr);
        $PrepObj->bind_param("ssssss", $FirstnameStr, $LastnameStr, $EmailStr, $UsernameStr, $PassStr, $UserpfpStr);
        $PrepObj->execute();
        $PrepObj->close();

        $IdInt = 0;
        $PrepObj = $this->ConnectionObj->prepare("SELECT ID FROM user WHERE EmailAddress=?");
        $PrepObj->bind_param("s", $EmailStr);
        $PrepObj->execute();
        $PrepObj->store_result();
        $PrepObj->bind_result($IdInt);
        $PrepObj->fetch();

        $this->endConn();

        $this->GlobalIdInt = $IdInt;
    }


    /**
     * Creates session when finished signing up.
     */
    function createSesson($ImageDataStr) {
        session_regenerate_id(true);
        $_SESSION['loggedin'] = true;
        $_SESSION['user'] = $this->UserNameStr;
        $_SESSION['email'] = $this->EmailAddressStr;
        $_SESSION['name'] = $this->FirstNameStr;
        $_SESSION['surname'] = $this->LastNameStr;
        $_SESSION['id'] = $this->GlobalIdInt;
        // $_SESSION['password'] = $this->PasswordStr;
        $_SESSION['pfp'] = $ImageDataStr; // remove
    }


    /**
     * Authenticates the signup process.
     */
    function checkSignUp($FNameStr, $LNameStr, $EmailStr, $UserStr, $PassStr) { // main
        $this->FirstNameStr = trim($FNameStr);
        $this->LastNameStr = trim($LNameStr);
        $this->EmailAddressStr = strtolower($EmailStr);
        $this->UserNameStr = trim($UserStr);
        $this->PasswordStr = trim($PassStr);

        $ResultStr = $this->checkSignUpFields();

        if ($ResultStr != "none") {
            error_log($ResultStr);
            return $ResultStr;
        } else {
            // get placeholder pfp
            $ImageDataStr = file_get_contents('C:\xampp\htdocs\stratuSolve_training\second_twitter_attempt\personpfp.png');
            $HashedPasswordStr = password_hash($this->PasswordStr, PASSWORD_BCRYPT, array('cost' => 11));
            
            $this->completeSignUp($HashedPasswordStr, $ImageDataStr);
            $this->createSesson($ImageDataStr);
            
            return 1;
        }
    }
}