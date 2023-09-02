<?php
class Post {
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




    function loadPosts() { // load all the posts
        $this->createConn();

        $SelectAllStr = "SELECT * FROM post LEFT JOIN user ON post.UserId=user.ID ORDER BY PostTimeStamp DESC";
        $ResultObj = $this->ConnectionObj->query($SelectAllStr);

        $this->endConn();

        // Check if any posts are found
        if ($ResultObj->num_rows > 0) {
            $ReturnArr = array();

            while ($RowArr = $ResultObj->fetch_assoc()) {
                $PostsArr = array(); 
                // preparing to use image
                $ImageData = base64_encode($RowArr["PostMedia"]);
                // Reformat date
                $PostTimeStampStr = $RowArr["PostTimeStamp"]; // saving PostTimeStamp from SQL to variable
                $DateInSec = strtotime($PostTimeStampStr); // convert date to seconds
                $DateStr = date('d M Y', $DateInSec); // save reformated date to variable
                // save the rest in variables
                $PostTextStr = $RowArr["PostText"];
                $PostTitleStr = $RowArr["PostTitle"];
                $UserIdStr = $RowArr["Username"];
                // Profile picture
                $Userpfp = base64_encode($RowArr["Userpfp"]);
                // post id
                $PostIdInt = $RowArr['ID'];

                array_push($PostsArr, $ImageData, $PostTitleStr, $PostTextStr, $DateStr, $UserIdStr, $Userpfp, $PostIdInt);
                array_push($ReturnArr, $PostsArr);
            }
            return $ReturnArr;
        } else {
            return null;
        }
    }

    function submitAltPost($UserIdInt, $PostTitleStr, $PostTextStr, $PostTimeStampStr) { // if user submitted a post that does not contain an image
        $this->createConn();

        $InsertStr = "INSERT INTO post (UserId, PostTitle, PostTimeStamp, PostText) VALUES (?, ?, ?, ?)";
        $PrepObj = $this->ConnectionObj->prepare($InsertStr);
        $PrepObj->bind_param("isss", $UserIdInt, $PostTitleStr, $PostTimeStampStr, $PostTextStr);

        // Execute the query
        if ($PrepObj->execute()) {
            $ReturnStateStr = "success";
        } else {
            $ReturnStateStr = "Error uploading post";
        }

        // Close the statement
        $PrepObj->close();

        $this->endConn();
        return $ReturnStateStr;
    }

    function submitPost($UserIdInt, $PostTitleStr, $PostTimeStampStr, $PostTextStr, $ReceivedFileArr) { // user submits post
        $ReturnStateStr = "";
        $AllowedTypesArr = array("jpg", "png", "jpeg", "gif");
        $Filename = basename($ReceivedFileArr["name"]);
        $FileType = pathinfo($Filename, PATHINFO_EXTENSION);

        $this->createConn();

        // Check if a file was uploaded without errors
        if (isset($ReceivedFileArr) && $ReceivedFileArr["error"] === 0 && $ReceivedFileArr['type'] == 'image/jpeg') {
            // checks if allowed filetype is given
            if (in_array($FileType, $AllowedTypesArr)) {
                $FileNameStr = $ReceivedFileArr["tmp_name"];

                $MAX_DIM_INT = 600;
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

                    $InsertStr = "INSERT INTO post (UserId, PostTitle, PostText, PostTimeStamp, PostMedia) VALUES (?, ?, ?, ?, ?)";
                    $PrepObj = $this->ConnectionObj->prepare($InsertStr);
                    $PrepObj->bind_param("issss", $UserIdInt, $PostTitleStr, $PostTimeStampStr, $PostTextStr, $ImageContentStr);

                    // Execute the query
                    if ($PrepObj->execute()) {
                        $ReturnStateStr = "success";
                    } else {
                        $ReturnStateStr = "Error uploading image: " . $PrepObj->error;
                    }

                    // Close the statement
                    $PrepObj->close();

                    imagedestroy( $NewImageResource );
                } else {
                    $ImageContentStr = file_get_contents($FileNameStr);
                    // Prepare the SQL statement
                    $InsertStr = "INSERT INTO post (UserId, PostTitle, PostText, PostTimeStamp, PostMedia) VALUES (?, ?, ?, ?, ?)";
                    $PrepObj = $this->ConnectionObj->prepare($InsertStr);
                    $PrepObj->bind_param("issss", $UserIdInt, $PostTitleStr, $PostTimeStampStr, $PostTextStr, $ImageContentStr);

                    // Execute the query
                    if ($PrepObj->execute()) {
                        $ReturnStateStr = "success";
                    } else {
                        $ReturnStateStr = "Error uploading image: " . $PrepObj->error;
                    }

                    // Close the statement
                    $PrepObj->close();
                }
            } else {
                $ReturnStateStr = "Incorrect filetype.";
            }
        } else {
            $ReturnStateStr = "Error uploading post";
        }
        $this->endConn();

        return $ReturnStateStr;
    }
}