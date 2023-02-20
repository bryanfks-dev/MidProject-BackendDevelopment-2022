<?php
    // Index page system
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
    if (!isset($_SESSION["login"])) {
        // Redirect user to login page
        header("Refresh: 0; url = login.php");

        exit; // End
    }
    else {
        // Redirect user to todo list page
        header("Refresh: 0; url = app.php");

        exit; // End
    }
?>