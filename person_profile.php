<?php
/**
 * Changes to existing profile is made.
 */
class Profile {
    public string $FirstNameStr;
    public string $LastNameStr;
    public string $UserNameStr;
    public string $EmailAddressStr;
    public string $PasswordStr;

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



    // verification functions
    /**
     * Validates email address.
     * Returns a boolean expression. Returns false if not valid.
     */
    function validateEmail() {
        if (filter_var($this->EmailAddressStr, FILTER_VALIDATE_EMAIL)) {
            $atPos = mb_strpos($this->EmailAddressStr, '@');
            // Select the domain
            $DomainStr = mb_substr($this->EmailAddressStr, $atPos + 1);
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
     * Checks if email exists in the DB.
     * Returns a boolean expression. Returns false if nothing found.
     */
    function checkEmailExists() {
        $IDInt = 0;
        $ReturnBool = false;
        $this->createConn();

        // prepare and bind
        $PrepObj = $this->ConnectionObj->prepare("SELECT ID FROM user WHERE EmailAddress=?");
        $PrepObj->bind_param("s", $this->EmailAddressStr);
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
     * Checks if email has changed: checks if given email address is valid.
     * Returns a boolean expression.
     * Returns false if invalid
     */
    function isEmailChanged() {
        if ($_SESSION['email'] != $this->EmailAddressStr) {
            if (!$this->checkEmailExists()) {
                // NOT valid
                return false;
            } else {
                if (!$this->validateEmail()) {
                    // NOT valid
                    return false;
                } else {
                    // valid
                    return true;
                }
            }
        } else {
            return true;
        }
    }

    /**
     * Validates the username given.
     * Returns a boolean expression. Returns false if invalid.
     */
    function validateUsername() {
        if ($this->UserNameStr == " " || $this->UserNameStr == "") {
            return false;
        } else {
            $UserStringArr = str_split($this->UserNameStr);
            if (count($UserStringArr) > 100) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * checks if username exists in the DB.
     * Returns a boolean expression.
     * Returns false if nothing is found.
     */
    function checkUserExists() {
        $IDInt = 0;
        $ReturnStr = false;
        $this->createConn();

        // prepare and bind
        $PrepObj = $this->ConnectionObj->prepare("SELECT ID FROM user WHERE User=?");
        $PrepObj->bind_param("s", $this->UserNameStr);
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
     * Checks if the username has changed.
     * Returns a boolean expression. Returns false if invalid.
     */
    function isUsernameChanged() {
        if ($_SESSION['user'] != $this->UserNameStr) {
            if (!$this->checkUserExists()) {
                // valid
                if ($this->validateUsername()) {
                    // valid
                    return true;
                } else {
                    // not valid
                    return false;
                }
            } else {
                // not valid
                return false;
            }
        } else {
            // valid
            return true;
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
     * Corrects the capitalisation.
     */
    function correctName($NameStr) {
        $NameStr = strtolower($NameStr);
        $NameStr = ucfirst($NameStr);
        return $NameStr;
    }



    function changeFields() {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $this->FirstNameStr) || $this->FirstNameStr == "" || $this->FirstNameStr == " ") {
            return "nameErr";
        } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $this->LastNameStr) || $this->LastNameStr == "" || $this->LastNameStr == " ") {
            return "surnameErr";
        } elseif (!$this->isEmailChanged()) {
            return "emailErr";
        } elseif (!$this->isUsernameChanged()) {
            return "userErr";
        } elseif (!$this->testPassword($this->PasswordStr)) {
            return "passErr";
        }
        $this->FirstNameStr = $this->correctName($this->FirstNameStr);
        $this->LastNameStr = $this->correctName($this->LastNameStr);
        return "none";
    }

    function getProfile($IdInt) { // not in use
        // show all information about user
        $ReturnArr = array();
        $this->createConn();

        $ProfileInfoStr = "SELECT FirstName, LastName, EmailAddress, Username, Password, Userpfp FROM user WHERE ID=$IdInt";
        $ResultObj = $this->ConnectionObj->query($ProfileInfoStr);
        $RowArr = $ResultObj->fetch_assoc();

        $this->endConn();

        array_push($ReturnArr, $RowArr["FirstName"], $RowArr["LastName"], $RowArr["EmailAddress"], $RowArr["User"], $RowArr["Password"], base64_encode($RowArr["Userpfp"]));
        return $ReturnArr;
    }

    function changeAltProfile($IdInt, $FNameStr, $LNameStr, $EmailStr, $UserStr, $PasswordStr) {
        $this->FirstNameStr = $FNameStr;
        $this->LastNameStr = $LNameStr;
        $this->EmailAddressStr = $EmailStr;
        $this->UserNameStr = $UserStr;
        $this->PasswordStr = $PasswordStr;

        $ResultStr = $this->changeFields();
        if ($ResultStr == "none") {
            $HashedPasswordStr = password_hash($this->PasswordStr, PASSWORD_BCRYPT, array('cost'=>11));
            $this->createConn();

            // Update
            $SignUpStr = "UPDATE user SET FirstName=?, LastName=?, Username=?, Password=? WHERE ID=?";
            $PrepObj = $this->ConnectionObj->prepare($SignUpStr);
            $PrepObj->bind_param('ssssi',$this->FirstNameStr, $this->LastNameStr, $this->UserNameStr, $HashedPasswordStr, $IdInt); // $this->EmailAddressStr
            $PrepObj->execute();
            $PrepObj->close();

            $this->endConn();

            $_SESSION['name'] = $this->FirstNameStr;
            $_SESSION['surname'] = $this->LastNameStr;
            $_SESSION['email'] = $this->EmailAddressStr;
            $_SESSION['user'] = $this->UserNameStr;
            $_SESSION['password'] = $this->PasswordStr;

            return "success";
        } else {
            return "something went wrong.";
        }
    }

    function changeProfile($IdInt, $FNameStr, $LNameStr, $EmailStr, $UserStr, $PasswordStr, $ReceivedFileArr) { // any change to the user's information will be changed here
        $this->FirstNameStr = $FNameStr;
        $this->LastNameStr = $LNameStr;
        $this->EmailAddressStr = $EmailStr;
        $this->UserNameStr = $UserStr;
        $this->PasswordStr = $PasswordStr;

        $ResultStr = $this->changeFields();
        error_log($ResultStr);
        if ($ResultStr == "none") {
            $AllowedTypesArr = array("jpg", "png", "jpeg", "gif", "heic", "webp");
            $Filename = basename($ReceivedFileArr["name"]);
            $FileType = pathinfo($Filename, PATHINFO_EXTENSION);

            if (isset($ReceivedFileArr)) {
                if (is_array($ReceivedFileArr)) {
                    if (in_array($FileType, $AllowedTypesArr)) {
                        $FileNameStr = $ReceivedFileArr["tmp_name"];
                        $HashedPasswordStr = password_hash($this->PasswordStr, PASSWORD_BCRYPT, array('cost'=>11));

                        $MAX_DIM_INT = 200;
                        list($WidthInt, $HeightInt, $TypeStr, $AttrStr) = getimagesize($FileNameStr);

                        if ( $WidthInt > $MAX_DIM_INT || $HeightInt > $MAX_DIM_INT ) {
                            $TargetFileNameStr = $FileNameStr;
                            $RatioFlt = $WidthInt/$HeightInt;
                            if( $RatioFlt > 1) {
                                $NewWidthInt = $MAX_DIM_INT;
                                $NewHeightInt = $MAX_DIM_INT/$RatioFlt;
                            } else {
                                $NewWidthInt = $MAX_DIM_INT*$RatioFlt;
                                $NewHeightInt = $MAX_DIM_INT;
                            }
                            $FileNameResource = imagecreatefromstring(file_get_contents($FileNameStr));
                            $NewImageResource = imagecreatetruecolor($NewWidthInt, $NewHeightInt);
                            imagecopyresampled( $NewImageResource, $FileNameResource, 0, 0, 0, 0, $NewWidthInt, $NewHeightInt, $WidthInt, $HeightInt );
                            imagedestroy( $FileNameResource );
                            imagepng( $NewImageResource, $TargetFileNameStr ); // adjust format as needed
                            $ImageContentStr = file_get_contents($TargetFileNameStr);

                            $this->createConn();
                            $SignUpStr = "UPDATE user SET FirstName=?, LastName=?, EmailAddress=?, Username=?, Password=?, Userpfp=? WHERE ID=?";
                            $PrepObj = $this->ConnectionObj->prepare($SignUpStr);
                            $PrepObj->bind_param('ssssssi',$this->FirstNameStr, $this->LastNameStr, $this->EmailAddressStr, $this->UserNameStr, $HashedPasswordStr, $ImageContentStr, $IdInt);
                            $PrepObj->execute();
                            $PrepObj->close();
                            
                            $this->endConn();

                            imagedestroy( $NewImageResource );
                        } else {
                            $ImageContentStr = file_get_contents($FileNameStr);
                            // Prepare the SQL statement
                            $this->createConn();

                            $SignUpStr = "UPDATE user SET FirstName=?, LastName=?, EmailAddress=?, Username=?, Password=?, Userpfp=? WHERE ID=?";
                            $PrepObj = $this->ConnectionObj->prepare($SignUpStr);
                            $PrepObj->bind_param('ssssssi',$this->FirstNameStr, $this->LastNameStr, $this->EmailAddressStr, $this->UserNameStr, $HashedPasswordStr, $ImageContentStr, $IdInt);
                            $PrepObj->execute();
                            $PrepObj->close();

                            $this->endConn();
                        }
        
                        
                        $_SESSION['name'] = $this->FirstNameStr;
                        $_SESSION['surname'] = $this->LastNameStr;
                        $_SESSION['email'] = $this->EmailAddressStr;
                        $_SESSION['user'] = $this->UserNameStr;

                        return "success";
                    } else {
                        return "Incorrect filetype";
                    }
                } else {
                    return "File error";
                }
            } else {
                return "Error uploading image: " . $_FILES["image"]["error"];
            }
        } else {
            return "something went wrong.";
        }
    }

    function returnProfilePicture($IdInt) { // not in use
        $this->createConn();

        $SelectPfpStr = "SELECT Userpfp FROM  user WHERE ID = $IdInt";
        $ResultObj = $this->ConnectionObj->query($SelectPfpStr);
        $RowArr = $ResultObj->fetch_assoc();

        $this->endConn();
        // error_log($RowArr['Userpfp']);
        return $RowArr['Userpfp'];
    }

    function deleteThisProfile($IdInt) {
        // delete the current user's profile
        $this->createConn();

        $DeleteProfileStr = "DELETE FROM user WHERE ID=$IdInt";
        $this->ConnectionObj->query($DeleteProfileStr);

        $DeleteAllPostsStr = "DELETE FROM post WHERE UserId=$IdInt";
        $this->ConnectionObj->query($DeleteAllPostsStr);

        $DeleteAttemptStr = "DELETE FROM attempt WHERE User=$IdInt";
        $this->ConnectionObj->query($DeleteAttemptStr);

        $this->endConn();
    }
}