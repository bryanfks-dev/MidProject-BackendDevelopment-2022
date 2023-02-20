<?php
    require_once "./db.php";

    class Cookie {
        function setCookie($username, $id, $password) {
            // Function to set cookies
            // Set cookies
            setcookie("belongs", hash('sha256', $username), time() + (3600 * 24 * 30));
            setcookie("key", hash('sha256', $id) , time() + (3600 * 24 * 30));
        }

        function validateCookie($cookie) {
            // Function to validate cookies
            // Check if cookies are exists
            if (isset($cookie["belongs"]) && isset($cookie["key"])) {
                // Check if cookie values are valid
                global $db_conn;

                // Select rows in a table
                $res = mysqli_query($db_conn, "SELECT * FROM user");

                // Run loop for all rows in table
                while ($row = mysqli_fetch_assoc($res)) {
                    // Check if username, id, and password match with row data
                    if (hash('sha256', $row["username"]) === $cookie["belongs"] && 
                        hash('sha256', $row["id"]) === $cookie["key"]) {
                        // Check if login session was made, if not create user login session
                        if (!isset($_SESSION["login"])) $_SESSION["login"] = $row["username"];

                        return true;
                    }
                }
            }

            return false;
        }

        function updateCookie($cookie) {
            // Function to update cookie
            // Check if cookies are exists
            if (isset($cookie["belongs"]) && isset($cookie["key"])) {
                // Update cookies
                setcookie("belongs", $cookie["belongs"], time() + (36000 * 24 * 30));
                setcookie("key", $cookie["key"], time() + (36000 * 24 * 30));
            }
        }

        function deleteCookie($cookie) {
            // Function to delete cookies
            // Check if cookies are exists
            if (isset($cookie["belongs"]) && isset($cookie["key"])) {
                setcookie("belongs", $cookie["belongs"], time());
                setcookie("key", $cookie["key"], time());
            }
        }
    }

    class User {
        function validateRegister($data) {
            // Function to validate user registration
            global $db_conn;
            global $db_conn_task;

            // Get name input
            $name = strtolower($data["name"]);

            // Check if name field is empty
            if (empty(trim($name))) return 'Name field can\\\'t be empty';

            // Formatting name
            for ($idx = 0; $idx < strlen($name); $idx++) {
                if (!$idx || $name[$idx - 1] === ' ') $name[$idx] = strtoupper($name[$idx]);
            }

            // Get username input
            $username = ucfirst(strtolower($data["username"]));

            // Check if name or username already exist in database
            if (mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM user WHERE name = '$name'"))) {
                return 'Name already regsitered';
            }
            else if (mysqli_num_rows(mysqli_query($db_conn, "SELECT * FROM user WHERE username = '$username'"))) {
                return 'Username already exist. Plese use other username';
            }

            // Get password and confirmation password input
            $password1 = mysqli_real_escape_string($db_conn, $data["password1"]);
            $password2 = mysqli_real_escape_string($db_conn, $data["password2"]);

            // Check if password and confirmation password filed is empty
            if (empty(trim($password1))) return 'Password field can\\\'t be empty';
            else if (empty(trim($password2))) return 'Confirmation password field can\\\'t be empty';

            // Check password and confirmation password matchesness
            if ($password1 !== $password2) return 'Password not match';

            // Encrpyt password
            $password1 = password_hash($password1, PASSWORD_DEFAULT);

            // Append data into database table
            mysqli_query($db_conn, "INSERT INTO user VALUES('', '$name', '$username', '$password1')");

            // Check whenever the data appended
            $isAppended = mysqli_affected_rows($db_conn);

            // Make a new task table for user
            mysqli_query($db_conn_task, "CREATE TABLE $username (
                id INT AUTO_INCREMENT PRIMARY KEY,
                task VARCHAR(255) NOT NULL,
                deadline VARCHAR(255) NOT NULL,
                isDone Int(1) NOT NULL
            )");

            // Return append data status message
            return $isAppended ? 'Account registered successfully' : 'Failed register account';
        }

        function validateLogin($data) {
            // Function to validate user login
            global $db_conn;

            // Get username and password input
            $username = ucfirst(strtolower($data["username"]));
            $password = mysqli_real_escape_string($db_conn, $data["password"]);

            // Get account datas from database
            $account = mysqli_fetch_assoc(mysqli_query($db_conn, "SELECT * FROM user WHERE username = '$username'"));

            // Check if account is exist
            if ($account === NULL) return 'Account doesn\\\'t exist';

            // Check if password is match
            if (!password_verify($password, $account["password"])) return 'Password wrong';

            // Make session to pass session checkin in todolist page
            $_SESSION["login"] = $username;

            // Check if remember me check box is checked
            if (isset($_POST["keep-me-logged-in"])) {
                $cookie_maker = new Cookie();

                // Set cookies
                $cookie_maker->setCookie($username, $account["id"], $password);
            }

            // Return sucess message
            return 'Login success';
        }
    }

    class Task {
        static function sortDate($date1, $date2) {
            // Fucntion to pick which date is the newest
            if (strtotime($date1["deadline"]) == strtotime($date2["deadline"])) return 0;
            
            return strtotime($date1["deadline"]) > strtotime($date2["deadline"]) ? 1 : -1;
        }

        static function taskTemplate($id, $taskName, $deadline, $isDone) {
            // Create element class, status, and mark status
            $status = $isDone ? "is-done" : "is-not-done";
            $actionStatus = $isDone ? "done-action" : "";

            $titleContainer = ($isDone) ? "
                <span>$taskName</span>
            " : "
                <input type='text' value='$taskName' name='task-title' disabled required>
            ";

            return "
                <form method='post' action=''>
                    <!-- Task contents -->
                    <div>
                        <!-- Check box button -->
                        <input type='checkbox' onClick='this.form.submit()' name='is-done-btn'>
                        <div class='content-wrapper'>
                            <div class='content'>
                                <div>
                                    <!-- Task title section -->
                                    <div>
                                        $titleContainer
                                        <input type='hidden' value='$id' name='task-id'>
                                        <input type='hidden' value='$isDone' name='task-status'>
                                        <button type='submit' name='rename-task-btn'>Rename</button>
                                    </div>
                                    <!-- Deadline section -->
                                    <div class='$status'>
                                        <box-icon name='calendar-event'></box-icon>
                                        <label for='calendar-event'>$deadline</label>
                                    </div>
                                </div>
                                <!-- Action icons -->
                                <div class='$actionStatus'>
                                    <!-- Rename icon -->
                                    <box-icon type='solid' name='edit'></box-icon>
                                    <!-- Remove button -->
                                    <button type='submit' name='delete-task-btn'>
                                        <box-icon name='trash'></box-icon>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            ";
        }

        function displayTasks($status) {
            // Function to display all user tasks from database
            global $db_conn_task;

            // Get username
            $username = strtolower($_SESSION["login"]);

            // Get tasks from username database table
            $res = mysqli_query($db_conn_task, "SELECT * FROM $username");

            $taskArr = []; // This array will hold user tasks

            while ($row = mysqli_fetch_assoc($res)) {
                // Check status to display
                if ($status === "done" && $row['isDone'] || 
                    $status === "not-done" && !$row['isDone']) {
                    $taskArr[] = $row; // Push task into array
                }
            }

            // Sort array using sortDate function
            usort($taskArr, "Task::sortDate");

            foreach ($taskArr as $task) {
                // Get array values
                $keys = array_values($task); // Get current array values

                // Get id, task name, deadline, and status
                $id = $keys[0];
                $task = $keys[1];
                $deadline = $keys[2];
                $isDone = $keys[3];

                echo Task::taskTemplate($id, $task, $deadline, $isDone);
            }

            // Print no task indicator as the task counter is 0
            if (empty($taskArr)) {
                if ($status === "not-done") echo "There's no task to do..";
                else if ($status === "done") echo "There's no completed task..";
            }
        }

        function validateTask($data) {
            // Function to validate task
            global $db_conn;
            global $db_conn_task;

            // Get task name and username
            $task = ucfirst(strtolower($data["task-title"]));

            // Select all username from user table
            $res = mysqli_query($db_conn, "SELECT username FROM user");

            // Run a loop to all username
            while ($row = mysqli_fetch_assoc($res)) {
                // Formating username to database title format
                $username = strtolower($row["username"]);

                // Search for same task name from other user task table
                $res = mysqli_query($db_conn_task, "SELECT task FROM $username WHERE task = '$task'");

                // Check if same task name exist in username task database
                if (mysqli_num_rows($res)) return $username;
            }

            // Return false value if there's no same task name
            return false;
        }

        function createTask($data) {
            // Function to create task
            global $db_conn_task;

            // Get username, task name, and deadline
            $username = strtolower($_SESSION["login"]);
            $taskName = ucfirst(strtolower($data["task-title"]));
            $deadline = $data["deadline"];

            // Push task into user task database
            mysqli_query($db_conn_task, "INSERT INTO $username VALUES('', '$taskName', '$deadline', 0)");
        }
        
        function markTask($data) {
            // Function to mark or unmark a task
            global $db_conn_task;

            // Get username and atask id
            $username = $_SESSION["login"];
            $id = $data["task-id"];
            $newStatus = (float)!$data["task-status"];

            // Update task status in user task table database
            mysqli_query($db_conn_task, "UPDATE $username SET isDone = $newStatus WHERE id = $id");
        }

        function renameTask($data) {
            // Function to rename task
            global $db_conn_task;

            // Get username, id, and task name
            $username = strtolower($_SESSION["login"]);
            $id = (int)$data["task-id"];
            $newName = ucfirst(strtolower($data["task-title"]));

            // Update task name in user task table
            mysqli_query($db_conn_task, "UPDATE $username SET task = '$newName' WHERE id = $id");
        }

        function deleteTask($data) {
            // Function to delete task
            global $db_conn_task;

            // Get username and task id
            $username = strtolower($_SESSION["login"]);
            $taskId = (int)$data["task-id"];

            // Delete task using it's id
            mysqli_query($db_conn_task, "DELETE FROM $username WHERE id = $taskId");
        }
    }
?>