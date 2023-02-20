<?php
    /* Register page system */
    session_start();

    require_once "./classes.php";

    $cookie = new Cookie();

    // Check if cookie was made
    if ($cookie->validateCookie($_COOKIE)) {
        // Redirect user to todo list page
        header("Refresh: 0; url = app.php");

        exit; // End
    }

    // Check session was made
    if (isset($_SESSION["login"])) {
        // Redirect user to todo list page
        header("Refresh: 0; url = app.php");


        exit; // End
    }

    /* Register page functional system starts here */
    $user = new User();

    if (isset($_POST["register-btn"])) {
        // Redirect user back to register page
        header("Refresh: 0; url = register.php");

        // Alert message
        $message = $user->validateRegister($_POST);

        // Display alert message
        echo "
            <script type='text/javascript'>
                alert('$message');
            </script>
        ";

        // Check if registration process is success
        if ($message === 'Account registered successfully') {
            // Redirect user to login page
            header("Refresh: 0; url = login.php");

            exit; // End
        }
    }
    else echo mysqli_error($db_conn); // Print connection error
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <!-- Register page -->
    <section id="register">
        <!-- Form -->
        <form method="post" action="">
            <!-- Title -->
            <h1>Register</h1>
            <br>
            <!-- Inputs -->
            <div class="input-content">
                <!-- User input -->
                <div>
                    <box-icon type='solid' name='user-circle'></box-icon>
                    <input type="text" placeholder="Name" name="name" required minlength="1" maxlength="20" pattern="[A-Za-z0-9\s]+">
                </div>
                <!-- Username input -->
                <div>
                    <box-icon type='solid' name='user'></box-icon>
                    <input type="text" placeholder="Username" name="username" required minlength="3" maxlength="15" pattern="[A-Za-z0-9\S]+">
                </div>
                <!-- Password input -->
                <div>
                    <box-icon type='solid' name='lock-alt'></box-icon>
                    <input type="password" placeholder="Password" name="password1" required minlength="8" maxlength="20">
                </div>
                <!-- Re-enter password input -->
                <div>
                    <box-icon type='solid' name='lock-alt'></box-icon>
                    <input type="password" placeholder="Re-enter Password" name="password2" required minlength="8" maxlength="20">
                </div>
            </div>
            <!-- Register button -->
            <button type="submit" name="register-btn" id="register-btn">Register</button>
            <!-- Ask for account -->
            <p>
                Already have an account?
                <a href="./login.php">Click here</a>
            </p>
        </form>
    </section>

    <!-- Boxicons js -->
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <!-- Set existing theme design -->
    <script type="text/javascript" src="./js/setExistTheme.js"></script>
</body>
</html>