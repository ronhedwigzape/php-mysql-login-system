<?php
// Initialize the session
session_start();

//Include connection
require_once 'partials/conn.php';

//echo "<pre>";
//echo var_dump($_COOKIE);
//echo "</pre>";
//
//echo "<pre>";
//echo var_dump($_SESSION);
//echo "</pre>";

if(isset($_COOKIE['username'])){
    header("location: welcome.php");
}

if(isset($_POST['logout'])) {
    setcookie('username', $username, 0, '/');
    setcookie('id', $id, 0, '/');
    setcookie('reset_pass_status', $reset_status, 0, '/');
    $conn->close();
    session_destroy();
    session_abort();
}

// Include config file
require_once "partials/conn.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // store result
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
//                $stmt->close();
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ss", $param_username, $param_password);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash


            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to login page
                echo "success";
                header("location: login.php");
            } else {
                echo "error";
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
                $stmt->close();
        }
    }

    // Close connection
        $conn->close();
}
require_once 'partials/header.php'
?>
<body>
<div id="preloader"></div>
<!--    Main Content    -->
<div class="container">

    <!--    Header    -->
    <div class="d-flex justify-content-center mt-5" style=" margin-bottom:5rem;">
        <h1 class="d-flex justify-content-center text-center tracking-in-expand" style="font-size: 6rem;">Hello World.</h1>
    </div>

    <!--      Dark Mode Toggle Switch     -->
    <div class="d-flex justify-content-center form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="toggle">
        <label class="form-check-label ms-3" for="toggle">Switch to Dark Mode</label>
    </div>

    <!--    Sign Up Component   -->
    <div class="" id="register-component">
        <?php require_once 'partials/register.php' ?>
    </div>

</div>
<script src="https://unpkg.com/jquery-cookie-bubble@1.0.0/dist/cookieBubble.min.js"/>
<script>
    //---------------Code-for-Cookie-Bubble-JS-----------------//
    // Source: https://github.com/CookieBubble/jquery-cookie-bubble
    (function($){
        $.cookieBubble(
            {
                messageTextColor:'rgb(44,88,162)',
                buttonText:'Got It',
                messageFontSize:'15',
                iconColor:'rgba(166, 118, 72, 1)',
                buttonColor:'rgba(0, 0, 0, 1)',
                iconVisibility:true,
                cookiePolicyButtonText:'Read our cookie Policy',
                cookiePolicyButtonUrl:'http://allaboutcookies.org',
                cookiePolicyButtonTarget:'_blank',
                boxPosition:'bottom-left',
                messageText:'We use cookies to personalize your experience. By continuing to visit this website you agree to our use of cookies.',
            });
    })(jQuery);
</script>
<?php require_once 'partials/footer.php'; ?>