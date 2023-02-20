<?php
    /* Todo list page system */
    session_start();

    require_once "./classes.php";

    $cookie = new Cookie();

    // Check if cookie was made
    if ($cookie->validateCookie($_COOKIE)) {
        // Update cookie
        $cookie->updateCookie($_COOKIE);
    }

    // Check session was made
    if (!isset($_SESSION["login"])) {
        // Redirect user back to login page
        header("Refresh: 0; url = login.php");

        exit; // End
    }

    /* Todo list page functional system starts here */
    $task = new Task();

    // Check if logout button is pressed
    if (isset($_POST["logout-btn"])) {
        session_destroy(); // Delete session

        // Delete cookies
        $cookie->deleteCookie($_COOKIE);

        // Redirect user to login page
        header("Refresh: 0; url = login.php");

        exit; // End
    }
    else echo mysqli_error($db_conn); // Print connection error

    // Check if add task button is pressed
    if (isset($_POST["add-task-btn"])) {
        // Validate task name
        $existingTaskUsername = $task->validateTask($_POST);

        // Check if task name already exist in other account task list
        if ($existingTaskUsername) {
            // Set alert message
            $message = ($existingTaskUsername === strtolower($_SESSION["login"])) ? "Task already exist in your task list" : "Task already exist in $existingTaskUsername task list";

            // Redirect user back to todo list page
            header("Refresh: 0; url = app.php");

            // Display alert message
            echo "
                <script>
                    alert('{$message}');
                </script>
            ";

            exit; // End
        }
        else $task->createTask($_POST); // Create task
    }
    else echo mysqli_error($db_conn); // Print connection error

    // Check if is done button is pressed, if pressed mark / unmark task
    if (isset($_POST["is-done-btn"])) $task->markTask($_POST);
    else echo mysqli_error($db_conn); // Print connection error

    // Check if rename task button is pressed
    if (isset($_POST["rename-task-btn"])) {
         // Validate task name
        $existingTaskUsername = $task->validateTask($_POST);

        // Check if task name already exist in other account task list
        if ($existingTaskUsername !== strtolower($_SESSION["login"]) && !empty($existingTaskUsername)) {
            // Redirect user back to todo list page
            header("Refresh: 0; url = app.php");

            // Display alert message
            echo "
                <script>
                    alert('Task already exist in $existingTaskUsername task table');
                </script>
            ";

            exit; // End
        }
        else $task->renameTask($_POST); // Rename task
    }
    else echo mysqli_error($db_conn); // Print connection error

    // Check if delete task button is pressed, if pressed delete task
    if (isset($_POST["delete-task-btn"])) $task->deleteTask($_POST);
    else echo mysqli_error($db_conn); // Print connection error
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To do list</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <!-- Todo-app page -->
    <section id="todo-app">
        <!-- Top nav -->
        <div class="top-nav">
            <!-- Menu button -->
            <div class="menu-btn">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <!-- Logo -->
            <div class="logo">
                <box-icon name='check-square'></box-icon>
                <h1>Todo List!</h1>
            </div>
            <!-- Add new task -->
            <button class="add-task-btn">
                <box-icon name='plus-circle'></box-icon>
                <span>Add New Task</span>
            </button>
        </div>
        <!-- Menu modal -->
        <nav class="menu-modal">
            <div>
                <!-- User indentifier -->
                <form method="post" action="">
                    <div>
                        <span>Hello,&nbsp;</span>
                        <div>
                            <box-icon type='solid' name='user'></box-icon>
                            <span><?= $_SESSION["login"] ?></span>
                        </div>
                    </div>
                    <!-- Logout button -->
                    <button type="submit" name="logout-btn">
                        <box-icon name='exit'></box-icon>
                        <span>Logout</span>
                    </button>
                </form>
                <!-- Main content -->
                <div>
                    <!-- Add new task -->
                    <button class="add-task-btn">
                        <box-icon name='plus-circle'></box-icon>
                        <span>Add New Task</span>
                    </button>
                    <!-- Theme -->
                    <div class="theme">
                        <span>Theme: </span>
                        <!-- Selected theme -->
                        <div>
                            <div class="current-theme">
                                <box-icon name='sun'></box-icon>
                                <div>
                                    <span>Light</span>
                                    <box-icon name='chevron-down' type='solid'></box-icon>
                                </div>
                            </div>
                            <!-- Theme modal -->
                            <div class="theme-modal">
                                <div class="light-theme">
                                    <box-icon name='sun'></box-icon>
                                    <span>Light</span>
                                </div>
                                <div class="dark-theme">
                                    <box-icon name='moon'></box-icon>
                                    <span>Dark</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Main content -->
        <div class="card-wrapper">
            <!-- Task card -->
            <div>
                <div class="card-title">
                    <box-icon name='task-x'></box-icon>
                    <span>Task</span>
                </div>
                <hr>
                <div class="card-content">
                    <!-- Task content here -->
                    <?= $task->displayTasks("not-done") ?>
                </div>
            </div>
            <!-- Complete task card -->
            <div>
                <div>
                    <div class="card-title">
                        <box-icon name='task'></box-icon>
                        <span>Complete Task</span>
                    </div>
                    <hr>
                    <div class="card-content">
                        <!-- Complete task content here -->
                        <?= $task->displayTasks("done") ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dim background -->
        <div class="dim-bg"></div>
        <!-- Add task modal -->
        <div class="add-task-modal">
            <!-- Top section -->
            <div class="top-section">
                <span>Add New Task</span>
                <box-icon name='x' class="x-btn"></box-icon>
            </div>
            <!-- Content section -->
            <form class="content-section" method="post" action="">
                <div>
                    <!-- Task name input -->
                    <div>
                        <label for="task_name">Task Name:</label>
                        <input type="text" required placeholder="What to do?" name="task-title" autocomplete="off" pattern="[A-Za-z\s]+">
                    </div>
                    <!-- Deadline input -->
                    <div>
                        <label for="deadline">Deadline:</label>
                        <input type="date" required name="deadline" placeholder="mm/dd/yyyy">
                    </div>
                </div>
                <!-- Add button -->
                <button type="submit" name="add-task-btn">Add</button>
            </form>
        </div>
    </section>

    <!-- Boxicons js -->
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <script type="text/javascript">
        // Prevent for multiple form post action on refresh
        if (history.replaceState) {
            history.replaceState(null, null, location.href);
        }

        const menuButton = document.querySelector(".menu-btn");
        const menuModal = document.querySelector(".menu-modal");

        const currentTheme = document.querySelector(".current-theme");
        const themeModal = document.querySelector(".theme-modal");

        /* Menu button event listener */
        menuButton.addEventListener("click", () => {
            // Add / remove menu button class
            menuButton.classList.toggle("active");

            // Set menu modal height
            menuModal.style.height = menuButton.classList.contains("active") ? `${menuModal.scrollHeight}px` : "0px";

            let overflowTimeout; // This variable contains timeout fucntion of changing overflow y value

            // Check if menu button is clicked
            if (menuButton.classList.contains("active")) {
                // Set menu modal overflow y to hidden
                menuModal.style.overflowY = "hidden";

                // Set menu modal overflow y to visible with 400ms delay
                overflowTimeout = setTimeout(() => {
                    menuModal.style.overflowY = "visible";
                }, 200);
            }
            else {
                // Clear timeout function
                clearTimeout(overflowTimeout);
                // Set menu modal overflow y to hidden
                menuModal.style.overflowY = "hidden";
            }

            // Remove current theme open class
            currentTheme.classList.remove("open");

            // Close theme modal
            themeModal.style.height = "0px";
        });

        /* Theme event listener */
        currentTheme.addEventListener("click", () => {
            // Add / remove current theme class 
            currentTheme.classList.toggle("open");

            // Set theme modal height
            themeModal.style.height = (currentTheme.classList.contains("open")) ? `${themeModal.scrollHeight}px` : "0px";
        });

        const addTaskBtn = document.querySelectorAll(".add-task-btn");
        const dimmedBg = document.querySelector(".dim-bg");
        const addTaskModal = document.querySelector(".add-task-modal");

        const xBtn = document.querySelector(".x-btn");

        for (let counter = 0; counter < addTaskBtn.length; counter++) {
            /* Add new task button event listener */
            addTaskBtn[counter].addEventListener("click", () => {
                // Dim background and open add task modal
                dimmedBg.classList.add("dimmed");
                addTaskModal.classList.add("open");

                // Close theme modal
                themeModal.style.height = "0px";
                currentTheme.classList.remove("open");

                // Close menu modal
                menuModal.style.height = "0px";
                menuButton.classList.remove("active");
            });
        }

        /* Close modal button event listener */
        xBtn.addEventListener("click", () => {
            // Close dim background and add task modal
            dimmedBg.classList.remove("dimmed");
            addTaskModal.classList.remove("open");
        });

        const renameInput = document.getElementsByName("task-title");
        const renameBtn = document.getElementsByName("rename-task-btn");
        const renameIcon = document.getElementsByName("edit");

        // Check if rename input, rename button, and rename icon is exists
        if (renameInput !== null && renameBtn !== null && renameIcon !== null) {
            for (let counter = 0; counter < renameIcon.length; counter++) {
                /* Rename icon event listener */
                renameIcon[counter].addEventListener("click", () => {
                    renameInput[counter].toggleAttribute("disabled");
                    renameBtn[counter].classList.toggle("show");
                })
            }
        }
    </script>

    <!-- Theme script -->
    <script type="text/javascript" src="./js/theme.js"></script>
</body>
</html>