<?php
    /* Login page system */
    session_start();

    require_once "./classes.php";

    $cookie = new Cookie();

    // Check if cookie was made
    if ($cookie->validateCookie($_COOKIE)) {
        // Redirect user to todolist page
        header("Refresh: 0; url = app.php");

        exit; // End
    }

    // Check session was made
    if (isset($_SESSION["login"])) {
        // Redirect user to todo list page
        header("Refresh: 0; url = app.php");

        exit; // End
    }

    /* Login page functional system starts here */
    $user = new User();

    if (isset($_POST["login-btn"])) {
        // Redirect user back to login page
        header("Refresh: 0; url = login.php");

        // Alert message
        $message = $user->validateLogin($_POST);

        // Display alert message
        echo "
            <script type='text/javascript'>
                alert('$message');
            </script>
        ";

        // Check if login process is success
        if ($message === 'Login success') {
            // Redirect user to todo list page
            header("Refresh: 0; url = app.php");

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
    <title>Login</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <!-- Login page -->
    <section id="login">
        <!-- Form -->
        <form method="post" action="">
            <!-- Title -->
            <h1>Login</h1>
            <br>
            <!-- Inputs -->
            <div class="input-content">
                <!-- Username input -->
                <div>
                    <box-icon type='solid' name='user'></box-icon>
                    <input type="text" placeholder="Username" name="username" required minlength="3" maxlength="15" pattern="[A-Za-z0-9\S]+">
                </div>
                <!-- Password input -->
                <div>
                    <box-icon type='solid' name='lock-alt'></box-icon>
                    <input type="password" placeholder="Password" name="password" required minlength="8" maxlength="20">
                    <box-icon name='hide' id="show-password"></box-icon>
                </div>
            </div>
            <!-- Show password box -->
            <div class="keep-me-login-box">
                <input type="checkbox" name="keep-me-logged-in">
                <span>Remember me for 30 days</span>
            </div>
            <!-- Login button -->
            <button type="submit" name="login-btn" id="login-btn">Login</button>
            <!-- Ask for account -->
            <p>
                Don't have account? 
                <a href="./register.php">Click here</a>
            </p>
        </form>
    </section>

    <!-- Boxicons js -->
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <script type="text/javascript">
        const showPassword = document.querySelector("#show-password");
        const passwordInputBox = document.getElementsByName("password")[0];

        showPassword.addEventListener("click", () => {
            // Update show password icons
            showPassword.setAttribute("name", showPassword.getAttribute("name") === "hide" ? "show" : "hide");

            // Update password input box attribute
            passwordInputBox.setAttribute("type", passwordInputBox.getAttribute("type") === "password" ? "text" : "password");
        });
    </script>

    <!-- Set existing theme design -->
    <script type="text/javascript" src="./js/setExistTheme.js"></script>
</body>
</html>