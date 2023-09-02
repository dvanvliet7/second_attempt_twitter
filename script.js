//



///////////////////////////// Collapse //////////////////////
function redBorder(id) { // activate red border w/ id
    let elem = document.getElementById(id);
    elem.removeAttribute("class");
    elem.setAttribute("class", "form-control border-danger");
}

function removeRedBorder(id) { // remove red border w/ id
    let elem = document.getElementById(id);
    elem.removeAttribute("class");
    elem.setAttribute("class", "form-control");
}

function showMsg(id) { // show collapse msg w/ id
    let elementCol = document.getElementById(id);
    let myCollapse = new bootstrap.Collapse(elementCol);

    myCollapse.show();
}

function hideMsg(id) { // hide collapsed messages w/ id
    let element = document.getElementById(id);
    let myCollapse = new bootstrap.Collapse(element);

    myCollapse.hide();
}

function fillAllFieldsLogin() {
    hideSpinner("mySpinner");
    redBorder("login-user");
    redBorder("login-password");
    if(!$('#pass-collapse').is('.collapse.show')) {
        showMsg("pass-collapse");
    }
}

function clearAllFields() {
    removeRedBorder("login-user");
    removeRedBorder("login-password");
    // hideMsg("pass-collapse");
}



//////////////////////////// log in ////////////////////////////////
function signUp() {
    $("#myModal").modal('hide');
    $("#myModal2").modal('show');
}

function showModal() {
    $(document).ready(function(){
        $("#myModal").modal('show');
    });
}

function checkResponse(data) {
    if (!isNaN(data)) {
        redBorder("login-user");
        hideSpinner("mySpinner");
        redBorder("login-password");
        showMsg("pass-collapse");
        document.getElementById("btn-login").removeAttribute("disabled");
        document.getElementById('signup-link').removeAttribute("style");
        showAttempt(data);
    } else if (data == "Successful") {
        // document.getElementById("login-user").setAttribute("disabled", "");
        // document.getElementById("login-password").setAttribute("disabled", "");
        location.replace("http://localhost:3000/home.php");
    }
    else {
        console.log("No response.");
        errPopup("Something went wrong.");
        hideSpinner("mySpinner");
        document.getElementById("btn-login").removeAttribute("disabled");
        document.getElementById('signup-link').removeAttribute("style");
    }
    
}

function loadUser() { // second
    let userIn = document.getElementById("login-user").value
    let passIn = document.getElementById("login-password").value

    const postParameters =  new URLSearchParams();
    postParameters.append("function", "login");
    postParameters.append("user", userIn);
    postParameters.append("password", passIn);

    $.ajax({
        url: "http://localhost:3000/request.php",
        type: "POST",
        data: postParameters.toString(),
        success: function(result) {
            // console.log(result);
            const newResult = JSON.parse(result);
            if (newResult == "Blocked") {
                hideSpinner("mySpinner");
                errPopup("Unable to log in.");
            } else {
                checkResponse(newResult);
            }
            
        },
        error: function(xhr, status, error) {
            errPopup("Something went wrong. Please try again.")
            console.log("Error: " + error);
            errPopup("Something went wrong.");
        },
    });
}

function showSpinner(id) {
    let spinner = document.getElementById(id);
    spinner.removeAttribute("hidden");
}
function hideSpinner(id) {
    let spinner = document.getElementById(id);
    spinner.setAttribute("hidden", "");
}

function logIn() { // first
    showSpinner("mySpinner");
    clearAllFields();
    if (document.getElementById("login-user").value == "" && document.getElementById("login-password").value == "") {
        fillAllFieldsLogin();
    } else {
        if($('#pass-collapse').is('.collapse.show')) {
            showMsg("pass-collapse");
        }
        document.getElementById("btn-login").setAttribute("disabled", "");
        document.getElementById('signup-link').setAttribute("style", "pointer-events:none;");
        loadUser();
    }
}

function showPassword(id) {
    var elem = document.getElementById(id);
    if (elem.type === "password") {
        elem.type = "text";
    } else {
        elem.type = "password";
    }
}



//////////////// Sign up //////////////////////////////
function showLoginModal() {
    $("#myModal2").modal('hide');
    $("#myModal").modal('show');
}

function clearAllSignUpFields() {
    removeRedBorder("signup-firstName");
    removeRedBorder("signup-lastName");
    removeRedBorder("signup-mail");
    removeRedBorder("signup-username");
    removeRedBorder("signup-password");
}

function fillAllSignUp() {
    hideSpinner("mySpinner2");
    redBorder("signup-firstName");
    redBorder("signup-lastName");
    redBorder("signup-mail");
    redBorder("signup-username");
    redBorder("signup-password");
    if(!$('#fname-collapse').is('.collapse.show')) {
            showMsg("fname-collapse");
    }
    if(!$('#lname-collapse').is('.collapse.show')) {
        showMsg("lname-collapse");
    }
    if(!$('#username-collapse').is('.collapse.show')) {
        showMsg("username-collapse");
    }
    if(!$('#pass2-collapse').is('.collapse.show')) {
        showMsg("pass2-collapse");
    }
}

function checkSignUpResponse(data) {
    // console.log(data);
    switch (data) {
        case "nameErr":
            hideSpinner("mySpinner2");
            redBorder("signup-firstName");
            showMsg("fname-collapse");
            document.getElementById("btn-signup").removeAttribute("disabled");
        break;
        case "surnameErr":
            hideSpinner("mySpinner2");
            redBorder("signup-lastName");
            showMsg("lname-collapse");
            document.getElementById("btn-signup").removeAttribute("disabled");
        break;
        case "emailErr":
            hideSpinner("mySpinner2");
            redBorder("signup-mail");
            redBorder("signup-username");
            showMsg("username-collapse");
            document.getElementById("btn-signup").removeAttribute("disabled");
        break;
        case "passErr":
            hideSpinner("mySpinner2");
            redBorder("signup-password");
            showMsg("pass2-collapse");
            document.getElementById("btn-signup").removeAttribute("disabled");
        break;
        case "userErr":
            hideSpinner("mySpinner2");
            redBorder("signup-mail");
            redBorder("signup-username");
            showMsg("username-collapse");
            document.getElementById("btn-signup").removeAttribute("disabled");
        break;
        case 1:
            // loginSuccessSweetAlert();
            // document.getElementById("signup-firstName").setAttribute("disabled", "");
            // document.getElementById("signup-lastName").setAttribute("disabled", "");
            // document.getElementById("signup-mail").setAttribute("disabled", "");
            // document.getElementById("signup-username").setAttribute("disabled", "");
            // document.getElementById("signup-password").setAttribute("disabled", "");
            location.replace('http://localhost:3000/home.php');
            // document.getElementById("btn-back").setAttribute("style", "pointer-events: none;");
        break;
    }
}

function saveUser() {
    let fname = document.getElementById("signup-firstName").value
    let lname = document.getElementById("signup-lastName").value
    let email = document.getElementById("signup-mail").value
    let username = document.getElementById("signup-username").value
    let password = document.getElementById("signup-password").value

    const postParameters =  new URLSearchParams();
    postParameters.append("function", "signup");
    postParameters.append("firstName", fname);
    postParameters.append("lastName", lname);
    postParameters.append("email", email);
    postParameters.append("username", username);
    postParameters.append("password", password);

    $.ajax({
        url: "http://localhost:3000/request.php",
        type: "POST",
        data: postParameters.toString(),
        success: function(result) {
            const newResult = JSON.parse(result);
            if (newResult == "Blocked") {
                hideSpinner("mySpinner");
                errPopup("Unable to log in.");
            } else {
                checkSignUpResponse(newResult);
            }
            console.log(newResult);
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
            errPopup("Something went wrong.");
        },
    });
}

function signUpForm() {
    removeRedBorder("signup-firstName");
    removeRedBorder("signup-lastName");
    removeRedBorder("signup-mail");
    removeRedBorder("signup-username");
    removeRedBorder("signup-password");
    removeRedBorder("retype-password");

    const pass1 = document.getElementById('signup-password').value;
    const pass2 = document.getElementById('retype-password').value;
    if (pass1 == "" || pass1 == " ") {
        errPopup("Please type your password again.");
        redBorder("signup-password");
        redBorder("retype-password");
    } else if (pass1 !== pass2) {
        errPopup("Please type your password again.");
        redBorder("signup-password");
        redBorder("retype-password");
    } else {
        if($('#fname-collapse').is('.collapse.show')) {
            showMsg("fname-collapse");
        }
        if($('#lname-collapse').is('.collapse.show')) { 
            showMsg("lname-collapse");
        }
        if($('#email-collapse').is('.collapse.show')) { 
            showMsg("email-collapse");
        }
        if($('#username-collapse').is('.collapse.show')) {
            showMsg("username-collapse");
        }
        if($('#pass2-collapse').is('.collapse.show')) {
            showMsg("pass2-collapse");
        }
        document.getElementById("btn-signup").setAttribute("disabled", "");
        saveUser();
    }
}



///////////////// posts ///////////////////////////////
function showPostSpinner(bool) {
    const spinner = document.getElementById("postSpinner");
    if (bool) {
        spinner.removeAttribute('hidden');
    } else {
        spinner.setAttribute('hidden', '');
    }
}

function sendPost() {
    showPostSpinner(true);
    saveBtn = document.getElementById("saveBtn");
    saveBtn.setAttribute('disabled', '');

    const image = document.getElementById("post-img").files[0];
    const titlePost = document.getElementById("title-input").value;
    const msgPost = document.getElementById("msg-input").value;

    if (titlePost.length > 30) {
        errPopup("Too many characters for post Title");
        return;
    }
    if (msgPost.length > 100) {
        errPopup("Too many characters for post message.");
        return;
    }

    // create a new formData object to send the image data
    const formData = new FormData();
    formData.append("function", "submit");
    formData.append("title", titlePost);
    formData.append("message", msgPost);
    formData.append("image", image);

    // Send the image data to the PHP server using AJAX
    fetch("http://localhost:3000/request.php", {
        method: "POST",
        body: formData,
    })
    .then((response) => response.json())
    .then((data) => {
    // Display the uploaded image on the web page
    // console.log(data);
    if (data == "success") {
        showPostSpinner(false);
        document.getElementById("uploadform").reset();
        loadAllPosts();
        location.reload();
        // postSuccess();
    } else {
        errPopup(data);
        console.log(data);
    }
    })
    .catch((error) => {
        errPopup("Something went wrong.");
        console.error("Error uploading post: ", error);
    });
}

/**
 * fetches all rows for table
 */
function loadAllPosts() {
    $.ajax({
        url: 'http://localhost:3000/request.php?type=all',
        success: function(data) {
            document.getElementById("content").replaceWith(createTableFromObjects(JSON.parse(data)));
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}


function createCard(img, title, msg, time) {
    // Create card elements

    const cardRow = document.createElement("div");
    cardRow.classList.add('row');

    const card = document.createElement('div');
    card.classList.add('col', 'center-container');

    const innerCard = document.createElement('div');
    innerCard.classList.add('card', 'shadow-sm', 'text-light', 'bg-success');
    innerCard.setAttribute("style", "width: 35rem");

    if (img !== '') {
        const cardImage = document.createElement("img");
        cardImage.setAttribute("src", "data:image/jpeg;base64,".concat(img));
        cardImage.classList.add('rounded');
        innerCard.appendChild(cardImage);
    }

    const cardBody = document.createElement('div');
    cardBody.classList.add('card-body');

    const cardTitle = document.createElement('h5');
    cardTitle.classList.add('card-title');
    cardTitle.textContent = title;

    const cardContent = document.createElement('p');
    cardContent.classList.add('card-text');
    cardContent.textContent = msg;

    const cardTimeStamp = document.createElement('div');
    cardTimeStamp.classList.add('card-footer');
    cardTimeStamp.textContent = time;

    // Assemble card elements
    cardBody.appendChild(cardTitle);
    cardBody.appendChild(cardContent);
    cardBody.appendChild(cardTimeStamp);

    //
    innerCard.appendChild(cardBody);
    
    card.appendChild(innerCard);
    cardRow.appendChild(card);

    return cardRow;
}


/**
 * returns a table
 * @param {Array} data 
 * @returns 
 */
function createTableFromObjects(data) {
    table = document.createElement('table');
    table.setAttribute("id", "person");
    att = document.createAttribute("class");
    att.value = "table table-light table-hover";
    table.setAttributeNode(att);
    tableBody = document.createElement('tbody');
    headerRow = document.createElement('tr');
    headerRow.setAttribute("id", "headerRow");

    // Create table data rows
    if (Array.isArray(data) && data.length) {
        for (obj of data) {
            dataRow = document.createElement('tr');
            dataCell = document.createElement('td');
            myCard = createCard(obj[0], obj[1], obj[2], obj[3]);

            // create profile header
            profileHeader = document.createElement("a");
            profileHeader.setAttribute('href', '#');
            profileHeader.classList.add('p-2', 'd-flex', 'align-items-center', 'text-outline-warning', 'text-decoration-none');

            //create profile img 
            profilePic = document.createElement('img');
            profilePic.setAttribute("src", "data:image/jpeg;base64,".concat(obj[5]));
            profilePic.setAttribute('alt', 'profile_picture');
            profilePic.setAttribute('width', '60');
            profilePic.setAttribute('height', '60');
            profilePic.classList.add('rounded-circle');
            profilePic.setAttribute('style', 'object-fit: cover;');

            // create profile username
            profileName = document.createElement('span');
            profileName.classList.add('d-none', 'd-sm-inline', 'mx-1');
            let textNode= document.createTextNode(obj[4]);
            profileName.appendChild(textNode);

            //append photo and name
            profileHeader.appendChild(profilePic);
            profileHeader.appendChild(profileName);

            // append to table element
            
            dataCell.appendChild(profileHeader);
            dataCell.appendChild(myCard);
            dataRow.appendChild(dataCell);
            tableBody.appendChild(dataRow);
        }
    } else {
        dataRow = document.createElement('tr');
        dataCell = document.createElement('td');
        dataCell.textContent = "No Results found";
        dataCell.setAttribute("colspan","auto");
        dataRow.appendChild(dataCell);
        tableBody.appendChild(dataRow);
    }

    table.appendChild(tableBody);
    return table;
}



/////////////////////////// profile ///////////////////////////
function checkChangeFields(name, surname, email, user, password) {
    if (name.trim() == "" || surname.trim() == "" || email.trim() == "" || user.trim() == "" || password.trim() == "") {
        return true;
    } else {
        return false;
    }
}

function changeImageSource(id, newSrc) {
    img = document.getElementById(id);
    img.removeAttribute('src');
    img.setAttribute('src', newSrc);
}

function fetchProfilePicture(id) {
    const postParameters =  new URLSearchParams();
    postParameters.append("function", "getProfilePicture");

    $.ajax({
        url: "http://localhost:3000/request.php",
        type: "POST",
        data: postParameters.toString(),
        success: function(result) {
            const newResult = JSON.parse(result);
            changeImageSource(id, newResult);
        },
        error: function(xhr, status, error) {
            errPopup("There has been a problem loading your profile picture.");
            console.log("Error: " + error);
        },
    });
}

function saveProfile() { // change profile
    const image = document.getElementById("change-profileImage").files[0];
    
    const fname = document.getElementById("change-name").value;
    const lname = document.getElementById("change-surname").value;
    const email = document.getElementById("change-email-address").value;
    const username = document.getElementById("change-username").value;
    const password = document.getElementById("change-password").value;

    if (checkChangeFields(fname, lname, email, username, password)) {
        errPopup("Something went wrong. Please try again.");
    } else {
        // create a new formData object to send the image data
        const formData = new FormData();
        formData.append("function", "changeProfile");
        formData.append("name", fname);
        formData.append("surname", lname);
        formData.append("email", email);
        formData.append("username", username);
        formData.append("password", password);
        formData.append("image", image);

        // Send the image data to the PHP server using AJAX
        fetch("http://localhost:3000/request.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
            // Display the uploaded image on the web page
            if (data === "success") {
                location.replace("http://localhost:3000/home.php");
            } else if (data === "changeEmail") {
                // verify email address

            } else {
                errPopup(data);
                console.log(data);
            }
            })
            .catch((error) => console.error("Error uploading post: ", error));
    }
}

function dummyPfp() {
    //dumy function for using pfp
}



///////////////////////////// sweet alert //////////////////////////

function errPopup(msg) { // General
    Swal.fire({
        icon: 'error',
        title: "Sorry",
        text: msg
      })
}

function successPopup(msg) { // General
    text = "Success! ";
    newMsg = text.concat(msg);
    Swal.fire({
        icon: 'success',
        title: newMsg
      })
}

function signOutSwal() { // home
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
      
    swalWithBootstrapButtons.fire({
    title: 'Are you sure you want to Sign out?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: "Yes, I'm sure",
    cancelButtonText: 'Cancel',
    reverseButtons: false
    }).then((result) => {
        if (result.isConfirmed) {
            location.replace("http://localhost:3000/log_out.php");
        } 
    })
}

function discardChange() { // profile >>> home
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
      })
      
      swalWithBootstrapButtons.fire({
        title: 'Are you sure you want to cancel?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: "Yes, I'm sure",
        cancelButtonText: 'Cancel',
        reverseButtons: false
      }).then((result) => {
        if (result.isConfirmed) {
            location.replace("http://localhost:3000/home.php");
        } 
        })
}

function saveChange() { // profile
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
      })
      
      swalWithBootstrapButtons.fire({
        title: 'Are you sure you want to save changes?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: "Yes, I'm sure",
        cancelButtonText: 'Cancel',
        reverseButtons: false
      }).then((result) => {
        if (result.isConfirmed) {
            pass1 = document.getElementById('change-password').value;
            pass2 = document.getElementById('match-change-password').value;
            if (pass1 == pass2) {
                saveProfile();
            } else {
                errPopup("Please type your password again.");
            }
        }
      })
}

function forgotPass() { // login
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
      
    swalWithBootstrapButtons.fire({
    title: 'Should we email you an OTP?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: "Yes please",
    cancelButtonText: 'No thanks',
    reverseButtons: false
    }).then((result) => {
        if (result.isConfirmed) {
            location.replace("http://localhost:3000/Final/email.php");
        }
    })
}

function backToLogin() { // Pin >>> login
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
      
    swalWithBootstrapButtons.fire({
    title: 'Do you want to go back to the login form?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: "Yes",
    cancelButtonText: 'No',
    reverseButtons: false
    }).then((result) => {
        if (result.isConfirmed) {
            location.replace("http://localhost:3000/Final/login.php");
        }
    })
}

function deletePerson() {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
      
    swalWithBootstrapButtons.fire({
    title: 'Would you like to delete your account?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: "Yes",
    cancelButtonText: 'No',
    reverseButtons: false
    }).then((result) => {
        if (result.isConfirmed) {
            const postParameters =  new URLSearchParams();
            postParameters.append("function", "deletePerson");

            $.ajax({
                url: "http://localhost:3000/request.php",
                type: "POST",
                data: postParameters.toString(),
                success: function(result) {
                    const newResult = JSON.parse(result);
                    if (newResult === "deleted") {
                        location.replace("http://localhost:3000/final/log_out.php");
                    } else {
                        errPopup(newResult);
                    }
                    // console.log(newResult);
                },
                error: function(xhr, status, error) {
                    console.log("Error: " + error);
                },
            });
        }
    })
}

function showAttempt(number) {
    text = "You have ";
    lastText = " attempt(s) left";
    newMsg = text.concat(number);
    newMsg = newMsg.concat(lastText);

    if (number < 1) {
        newMsg = "Oops! You're on time out.";
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })

    Toast.fire({
        icon: 'warning',
        title: newMsg
    })
}

function postSuccess() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })

    Toast.fire({
        icon: 'success',
        title: 'New post uploaded.'
    })
}