<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="checkmark.png" type="image/x-icon">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" id="theme-styles">
    <!-- end -->

    <script src="script.js"></script>

    <!----==== CSS ====-->
    <link rel="stylesheet" href="style.css">
    
    <!----==== Icounscout Link ====-->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">


    <!-- css -->
    <style>
        .material-symbols-outlined {
            text-align: center;
            vertical-align: middle;
        }
        button {
            margin: 2%;
        }
        .modal-backdrop {
            background-color: transparent;
        }
    </style>

    
</head>
<body>
    <script>
        showModal();
    </script>

    <!-- Login form -->
    <div id="myModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-success bg-light">
                <div class="modal-header">
                    <h4 class="modal-title p-1" id="headermodal"><i class="fa-solid fa-circle-check"></i></i>&nbsp;Login</h4>
                    <div class="clearfix" id="mySpinner" hidden>
                        <div class="spinner-border float-end" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="container">
                        <div class="input-box">
                            <input type="text" spellcheck="false" class="form-control bg-light" id="login-user" maxlength="64">
                            <label class="col-form-label">Username or e-mail</label>
                            <i class="material-symbols-outlined text-success">person</i>
                        </div>
                    </div>
                    <div class="container">
                        <div class="input-box show-password">
                            <input type="password" spellcheck="false" class="form-control bg-light" id="login-password" name="pass" maxlength="100">
                            <label class="col-form-label">Password</label>
                            <i class="uil uil-eye-slash toggle"></i>
                        </div>
                        <div class="col">
                            <div class="collapse" id="pass-collapse" aria-expanded="false">
                                <div class="card card-body text-white bg-danger">
                                Invalid username or password.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-success rounded-pill" id="btn-login" onclick="logIn()">Login</button>
                    </div>
                </div>
                <div class="modal-footer" id="modalFoot">Don't have an account?&nbsp;<a href="#" class="text-success" id="signup-link" onclick="signUp()">Sign up</a></div>
            </div>
        </div>
    </div>
    <!-- end -->

    <!-- Sign up form -->
    <div id="myModal2" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-success bg-light">
                <div class="modal-header">
                    <h4 class="modal-title p-1" id="headermodal2"><i class="fa-solid fa-circle-check"></i>&nbsp;Sign up</h4>
                    <button id="back-btn" class="btn btn-outline-danger float-end" type="button" onclick="showLoginModal()">Back</button>
                    <div class="clearfix" id="mySpinner2" hidden>
                        <div class="spinner-border float-end" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="container">
                        <div class="input-box">
                            <input type="text" class="form-control bg-light" id="signup-firstName" maxlength="300">
                            <label class="col-form-label">First name</label>
                            <i class="material-symbols-outlined">person</i>
                        </div>
                        <div class="col">
                            <div class="collapse" id="fname-collapse">
                                <div class="card card-body text-white bg-danger">
                                    Invalid name. It should be more than 1 character long and not contain any symbols or numbers.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="input-box">
                            <input type="text" class="form-control bg-light" id="signup-lastName" maxlength="300">
                            <label class="col-form-label">Last name</label>
                            <i class="material-symbols-outlined">person</i>
                        </div>
                        <div class="col">
                            <div class="collapse" id="lname-collapse">
                                <div class="card card-body text-white bg-danger">
                                    Invalid last name. It should be more than 1 character long and not contain any symbols or numbers.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="input-box">
                            <input type="email" class="form-control bg-light" id="signup-mail" maxlength="64">
                            <label class="col-form-label">E-mail address</label>
                            <i class="material-symbols-outlined">alternate_email</i>
                        </div>
                    </div>
                    <div class="container">
                        <div class="input-box">
                            <input type="text" class="form-control bg-light" id="signup-username" maxlength="31">
                            <label class="col-form-label">Username</label>
                            <i class="material-symbols-outlined">remember_me</i>
                        </div>
                        <div class="col">
                            <div class="collapse" id="username-collapse">
                                <div class="card card-body text-white bg-danger">
                                There is a problem with the username or email.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="input-box">
                            <input type="password" class="form-control bg-light" id="signup-password" name="pass" maxlength="100">
                            <label class="col-form-label">Password</label>
                            <i class="uil uil-eye-slash toggle2"></i>
                        </div>
                        <div class="col">
                            <div class="collapse" id="pass2-collapse"  aria-expanded="false">
                                <div class="card card-body text-white bg-danger">
                                    Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="input-box">
                            <input type="password" class="form-control bg-light" id="retype-password" maxlength="100">
                            <label class="col-form-label">Type password again</label>
                            <i class="uil uil-eye-slash toggle3"></i>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-success rounded-pill" id="btn-signup" onclick="signUpForm()">Sign up</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- end -->


    <script>
        // login show password
        const toggle = document.querySelector(".toggle"),
              input = document.getElementById("login-password");

        toggle.addEventListener("click", () =>{
            if(input.type ==="password"){
            input.type = "text";
            toggle.classList.replace("uil-eye-slash", "uil-eye");
            }else{
            input.type = "password";
            toggle.classList.replace("uil-eye", "uil-eye-slash");
            }
        })

        // signup show password
        const toggle2 = document.querySelector(".toggle2"),
              input2 = document.getElementById("signup-password");

        toggle2.addEventListener("click", () =>{
            if (input2.type === "password") {
                input2.type = "text";
                toggle2.classList.replace("uil-eye-slash", "uil-eye");
            } else {
                input2.type = "password";
                toggle2.classList.replace("uil-eye", "uil-eye-slash");
            }
        })

        const toggle3 = document.querySelector(".toggle3"),
              input3 = document.getElementById("retype-password");

              toggle3.addEventListener("click", () =>{
            if (input3.type === "password") {
                input3.type = "text";
                toggle3.classList.replace("uil-eye-slash", "uil-eye");
            } else {
                input3.type = "password";
                toggle3.classList.replace("uil-eye", "uil-eye-slash");
            }
        })
        
        // login
        $("#login-user").keypress(function (event) {
            if (event.keyCode === 13) {
                $("#btn-login").click();
            }
        });
        $("#login-password").keypress(function (event) {
            if (event.keyCode === 13) {
                $("#btn-login").click();
            }
        });

        // sign up
        $("#signup-firstName").keypress(function (event) {
            if (event.keyCode === 13) {
                $("#btn-signup").click();
            }
        });
        $("#signup-lastName").keypress(function (event) {
            if (event.keyCode === 13) {
                $("#btn-signup").click();
            }
        });
        $("#signup-mail").keypress(function (event) {
            if (event.keyCode === 13) {
                $("#btn-signup").click();
            }
        });
        $("#signup-username").keypress(function (event) {
            if (event.keyCode === 13) {
                $("#btn-signup").click();
            }
        });
        $("#signup-password").keypress(function (event) {
            if (event.keyCode === 13) {
                $("#btn-signup").click();
            }
        });
        $("#retype-password").keypress(function (event) {
            if (event.keyCode === 13) {
                $("#btn-signup").click();
            }
        });
    </script>
</body>
</html>