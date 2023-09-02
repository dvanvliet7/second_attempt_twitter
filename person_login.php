<?php
class LogIn {
    public string $FirstNameStr;
    public string $LastNameStr;
    public string $UserNameStr;
    public string $EmailAddressStr;
    public string $PasswordStr;
    public int $RemainingAttemptsInt;

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
     * Increment the attempts made to the DB.
     */
    function incrementAttempt() {
        //if password was a fail then increment to DB
        $FuncObj = $this->ConnectionObj->prepare("UPDATE attempt SET AttemptAmount=AttemptAmount+1 WHERE IP=?");
        $FuncObj->bind_param("s", $_SERVER['REMOTE_ADDR']);
        $FuncObj->execute();
        $FuncObj->close();
    }

    /**
     * Resets the attempts made.
     */
    function resetAttempt() {
        $FuncObj = $this->ConnectionObj->prepare("UPDATE attempt SET AttemptAmount=0 WHERE IP=?");
        $FuncObj->bind_param("s", $_SERVER['REMOTE_ADDR']);
        $FuncObj->execute();
        $FuncObj->close();
    }

    /**
     * Insert a new IP address to the DB.
     * Passes an integer for the attempt amount.
     */
    function insertAttempt($AttemptAmountInt) {
        $InsertObj = $this->ConnectionObj->prepare("INSERT INTO attempt (IP, AttemptAmount) VALUES (?, ?)");
        $InsertObj->bind_param("si", $_SERVER['REMOTE_ADDR'], $AttemptAmountInt);
        $InsertObj->execute();
        $InsertObj->close();
    }

    /**
     * Checks the amount of attempts made with the user's IP address.
     * Returns the validity of the attempt.
     * Returns false if invalid.
     */
    function checkAttempts() {
        //checks if attempting again is valid
        $ReturnStateBool = false;
        $IpStr = "";
        $AttemptInt = 0;

        $FuncObj = $this->ConnectionObj->prepare("SELECT AttemptAmount FROM attempt WHERE IP=?");
        $FuncObj->bind_param("s", $_SERVER['REMOTE_ADDR']);
        $FuncObj->execute();
        $FuncObj->store_result();

        if ($FuncObj->num_rows > 0) {
            $FuncObj->bind_result($AttemptInt);
            $FuncObj->fetch();
            $this->RemainingAttemptsInt = 5 - $AttemptInt;
            // comparison
            if ($AttemptInt < 5) {
                $ReturnStateBool = true;
            } else {
                $ReturnStateBool = false;
            }
        } else {
            // create row
            $this->insertAttempt(0);
            $ReturnStateBool = true;
        }

        $FuncObj->close();
        return $ReturnStateBool;
    }



    /**
     * Creates the session when successfuly logging in.
     */
    function createSession($UserStr, $EmailAddStr, $FNameStr, $LNameStr, $IDInt, $PassStr, $MyProfilePictureStr) { // creating a session
        session_regenerate_id(true);
        $_SESSION['loggedin'] = true;
        $_SESSION['user'] = $UserStr;
        $_SESSION['email'] = $EmailAddStr;
        $_SESSION['name'] = $FNameStr;
        $_SESSION['surname'] = $LNameStr;
        $_SESSION['id'] = $IDInt;
        // $_SESSION['password'] = $PassStr; 
        $_SESSION['pfp'] = $MyProfilePictureStr; // remove
    }

    /**
     * Checks if the Username exists in the DB and chekcs if the password matches.
     * Returns 'Successful' if Username exists and password is correct.
     */
    function saveUser($UserStr) { // attempts
        $IDInt = 0;
        $FNameStr = "";
        $LNameStr = "";
        $EmailAddStr = "";
        $MyPasswordStr = "";
        $ReturnStr = "";
        $MyProfilePictureStr = "";

        $this->createConn();

        // prepare and bind
        $PrepObj = $this->ConnectionObj->prepare("SELECT ID, FirstName, LastName, EmailAddress, Password, Userpfp FROM user WHERE Username=?");
        $PrepObj->bind_param("s", $UserStr);
        $PrepObj->execute();
        // Store the result so we can check if the account exists in the database.
        $PrepObj->store_result();

        if ($PrepObj->num_rows > 0) {
            // Correct username
            $PrepObj->bind_result($IDInt, $FNameStr, $LNameStr, $EmailAddStr, $MyPasswordStr, $MyProfilePictureStr); // require hashed password from DB!
            $PrepObj->fetch();
            // Now we verify the password.
            if (password_verify($this->EnteredPassStr, $MyPasswordStr)) {
                // let's Create a session
                $this->createSession($UserStr, $EmailAddStr, $FNameStr, $LNameStr, $IDInt, $this->EnteredPassStr, base64_encode($MyProfilePictureStr));
                // let's reset the login attempts
                $this->resetAttempt();

                $ReturnStr = "Successful";
            } else {
                // Incorrect password (increment attempt)
                $this->incrementAttempt();
                $this->RemainingAttemptsInt = $this->RemainingAttemptsInt - 1;
                $ReturnStr = strval( $this->RemainingAttemptsInt);
            }
        } else {
            // Incorrect username
            $ReturnStr = "userErr";
        }

        $PrepObj->close(); // close prepared statement
        $this->endConn(); // close DB connection

        return $ReturnStr;
    }

    /**
     * Checks if the Email address exists in the DB and chekcs if the password matches.
     * Returns 'Successful' if Email address exists and password is correct.
     */
    function saveEmail($EmailStr) {
        $IDInt = 0;
        $FNameStr = "";
        $LNameStr = "";
        $UserStr = "";
        $MyPasswordStr = "";
        $ReturnStr = "";
        $MyProfilePictureStr = "";
        $this->createConn();

        // prepare and bind
        $PrepObj = $this->ConnectionObj->prepare("SELECT ID, FirstName, LastName, Username, Password, Userpfp FROM user WHERE EmailAddress=?");
        $PrepObj->bind_param("s", $EmailStr);
        // execute
        $PrepObj->execute();
        // Store the result so we can check if the account exists in the database.
        $PrepObj->store_result();

        
        if ($PrepObj->num_rows > 0) {
            $PrepObj->bind_result($IDInt, $FNameStr, $LNameStr, $UserStr, $MyPasswordStr, $MyProfilePictureStr);
            $PrepObj->fetch();
            // Account exists, now we verify.
            if (password_verify($this->EnteredPassStr, $MyPasswordStr)) {
                // Verification success! User has logged-in!
                // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
                $this->createSession($UserStr, $EmailStr, $FNameStr, $LNameStr, $IDInt, $this->EnteredPassStr, $MyProfilePictureStr);
                // let's reset the login attempts
                $this->resetAttempt();
                // Success
                $ReturnStr = "Successful";
            } else {
                // Incorrect password
                $this->incrementAttempt();
                $this->RemainingAttemptsInt = $this->RemainingAttemptsInt - 1;
                $ReturnStr = strval( $this->RemainingAttemptsInt);
            }
        } else {
            // Incorrect username
            $this->incrementAttempt();
            $ReturnStr = "userErr";
        }

        $PrepObj->close();
        $this->endConn();

        return $ReturnStr;
    }

    /**
     * Manages the login attempt.
     * Returns the result.
     */
    function checkLogin($UsernameStr, $PassStr) { // main
        // Process the form data
        $UserStr = trim($UsernameStr);
        $this->EnteredPassStr = trim($PassStr);

        $this->createConn();
        $AttemptValidBool = $this->checkAttempts();
        $this->endConn();

        if ($AttemptValidBool) {
            $ResultStr = $this->saveUser($UserStr);
            if ($ResultStr != "userErr") {
                return $ResultStr;
            } else {
                $ResultStr = $this->saveEmail($UserStr);
                if ($ResultStr != "userErr") {
                    return $ResultStr;
                } else {
                    $this->RemainingAttemptsInt = $this->RemainingAttemptsInt - 1;
                    $RemainStr = strval( $this->RemainingAttemptsInt);
                    return $RemainStr;
                }
            }
        } else {
            return "Blocked";
        }
    }
}