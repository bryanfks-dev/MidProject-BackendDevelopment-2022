<?php
    // Make connection with database
    $db_checker = new mysqli("localhost", "root", "");

    // Search for users and tasks database
    $res_user = mysqli_query($db_checker, "SHOW DATABASES LIKE 'users'");
    $res_task = mysqli_query($db_checker, "SHOW DATABASES LIKE 'tasks'");

    // Create tasks database if there's no such database
    if (empty(mysqli_fetch_assoc($res_task))) mysqli_query($db_checker, "CREATE DATABASE tasks");

    // Create users database if there's no such database
    if (empty(mysqli_fetch_assoc($res_user))) {
        // Create database
        mysqli_query($db_checker, "CREATE DATABASE users");

        // Select current database
        mysqli_select_db($db_checker, "users");

        // Create user table
        mysqli_query($db_checker, "CREATE TABLE user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(20) NOT NULL,
            username VARCHAR(15) NOT NULL,
            password VARCHAR(255) NOT NULL
        )");
        
        mysqli_close($db_checker); // Close db_checker connection to database
    }

    // Start a new database connection
    // This database contains user datas
    $db_conn = new mysqli("localhost", "root", "", "users");
    // This database contains user tasks
    $db_conn_task = new mysqli("localhost", "root", "", "tasks");

    // Check for connection to database
    if (!$db_conn) {
        echo mysqli_error($db_conn);
    }

    if (!$db_conn_task) {
        echo mysqli_error($db_conn_task);
    }
?>