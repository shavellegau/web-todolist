<?php
require 'config.php';
if (!empty($_SESSION["id"])) {
    $id = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM tb_user WHERE id = $id");
    $row = mysqli_fetch_assoc($result);
} else {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --base-clr: #A0937D;
            --line-clr: #000000;
            --hover-clr: #B7E0FF;
            --text-clr: #ffffff;
            --accent-clr: #FFCFB3;
            --secondary-text-clr: #000000;
            --form-bg: #F4F4F4;
            --form-border: #ccc;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--base-clr);
            color: var(--secondary-text-clr);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: var(--form-bg);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 500px;
            width: 100%;
        }

        .header h1 {
            color: var(--line-clr);
        }

        .header p {
            color: var(--secondary-text-clr);
        }

        .stats-container {
            padding: 20px;
            border-radius: 10px;
            background-color: var(--form-bg);
            margin-bottom: 20px;
            text-align: center;
        }

        #progressBar {
            width: 100%;
            height: 8px;
            background-color: var(--line-clr);
            border-radius: 5px;
            margin-top: 10px;
            overflow: hidden;
        }

        #progress {
            height: 100%;
            background-color: var(--accent-clr);
            transition: all 0.3s ease;
        }

        form {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: space-between;
        }

        input,
        select {
            flex: 1;
            padding: 10px;
            border: 1px solid var(--form-border);
            border-radius: 10px;
            outline: none;
            transition: border 0.3s;
        }

        input:focus,
        select:focus {
            border-color: var(--accent-clr);
        }

        button {
            width: 100px;
            border: none;
            border-radius: 10px;
            background-color: var(--line-clr);
            color: var(--text-clr);
            cursor: pointer;
            transition: background 0.3s;
            padding: 10px;
        }

        button:hover {
            background-color: var(--hover-clr);
        }

        .task-list {
            margin-top: 20px;
            list-style: none;
            padding: 0;
        }

        .taskItem {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--form-bg);
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, opacity 0.3s;
        }

        .task {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-grow: 1;
        }

        .task input {
            width: 20px;
            height: 20px;
        }

        .completed {
            text-decoration: line-through;
            color: var(--secondary-text-clr);
            opacity: 0.5; 
        }

        .taskItem span {
            flex-grow: 1;
            font-weight: 500;
        }

        .priority-low {
            color: green;
        }

        .priority-medium {
            color: orange;
        }

        .priority-high {
            color: red;
        }

        .icons {
            display: flex;
            gap: 10px;
        }

        .icons i {
            cursor: pointer;
            color: var(--line-clr);
            transition: color 0.3s;
        }

        .icons i:hover {
            color: var(--hover-clr);
        }

        .clear-completed {
            margin-top: 10px;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: var(--accent-clr);
            color: var(--line-clr);
            font-weight: bold;
            transition: background 0.3s;
        }

        .clear-completed:hover {
            background: var(--hover-clr);
        }

        .empty-state {
            text-align: center;
            color: rgba(0, 0, 0, 0.5);
        }

        .empty-state i {
            font-size: 3em;
            margin-bottom: 0.5em;
            color: var(--line-clr);
        }

        .filter-buttons {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .filter-button {
            margin: 0 5px;
            padding: 10px 15px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            background-color: var(--accent-clr);
            color: var(--line-clr);
            transition: background 0.3s;
        }

        .filter-button:hover {
            background-color: var(--hover-clr);
        }

        .popup {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: var(--form-bg);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 300px;
        }

        .popup-content input {
            width: calc(100% - 20px);
            margin-bottom: 10px;
        }

        .popup-content button {
            width: 100%;
        }

        .popup-content textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid var(--form-border);
            border-radius: 4px;
            resize: vertical;
        }

        .delete-confirmation {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .delete-confirmation-content {
            background: var(--accent-clr);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 300px;
        }

        .delete-confirmation-content button {
            width: 100%;
            margin-top: 10px;
        }

        #sidebar {
            height: 100vh;
            width: 250px;
            background-color: var(--base-clr);
            border-right: 1px solid var(--line-clr);
            position: fixed;
            left: 0;
            top: 0;
            transition: 300ms ease-in-out;
            overflow-y: auto;
            z-index: 1000;
        }

        #sidebar.close {
            left: -250px;
        }

        #sidebar ul {
            list-style: none;
            padding: 1em;
        }

        #sidebar > ul > li:first-child {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        #sidebar ul li.active a {
            color: var(--accent-clr);
        }

        #sidebar a, 
        #sidebar .dropdown-btn, 
        #sidebar .logo {
            border-radius: .5em;
            padding: .85em;
            text-decoration: none;
            color: var(--text-clr);
            display: flex;
            align-items: center;
            gap: 1em;
        }

        .dropdown-btn {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            font: inherit;
            cursor: pointer;
        }

        #sidebar i {
            flex-shrink: 0;
            width: 20px;
            text-align: center;
        }

        #sidebar a span, 
        #sidebar .dropdown-btn span {
            flex-grow: 1;
        }

        #sidebar a:hover, 
        #sidebar .dropdown-btn:hover {
            background-color: var(--hover-clr);
        }

        #sidebar .sub-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 300ms ease-in-out;
        }

        #sidebar .sub-menu.show {
            max-height: 200px;
        }

        #sidebar .sub-menu a {
            padding-left: 3em;
        }

        #toggle-btn {
            width: 25px;
            height: 25px;
            position: fixed;
            left: 10px;
            top: 10px;
            z-index: 1001;
            padding: .5em;
            border: none;
            border-radius: .5em;
            background: var(--hover-clr);
            color: var(--text-clr);
            cursor: pointer;
            transition: left 300ms ease-in-out;
        }

        #sidebar.close + main #toggle-btn {
            left: 10px;
        }

        main {
            flex-grow: 1;
            margin-left: 250px;
            padding: 2em;
            transition: margin-left 300ms ease-in-out;
        }

        main.full-width {
            margin-left: 0;
        }

        main h1 {
            margin-bottom: 10px;
        }

        main h1 span {
            color: var(--accent-clr);
        }

        main p {
            color: var(--secondary-text-clr);
            margin-bottom: 20px;
        }

        .container {
            border: 1px solid var(--line-clr);
            border-radius: 1em;
            padding: min(3em, 15%);
        }

        .container h2 {
            margin-bottom: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--accent-clr);
            color: var(--text-clr);
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #4a4dff;
        }

        @media(max-width: 800px) {
            #sidebar {
                left: -250px;
                transition: left 0.3s ease-in-out;
            }

            #sidebar.open {
                left: 0;
            }

            main {
                margin-left: 0;
                padding-top: 60px;
            }

            #toggle-btn {
                left: 10px;
            }
        }

        .profile-picture {
            width: 50px; 
            height: 50px; 
            border-radius: 50%; 
            object-fit: cover; 
            margin-bottom: 20px; 
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--form-border);
            border-radius: 8px;
            font-size: 16px;
            background-color: var(--form-bg);
            transition: border 0.3s ease;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        textarea:focus {
            border-color: var(--accent-clr);
            outline: none;
        }

        input[type="text"] {
            height: 40px;
        }

        textarea {
            height: 100px; 
            resize: none;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px; 
        }

        form button {
            width: 150px;
            padding: 10px;
            background-color: var(--line-clr);
            color: var(--text-clr);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: var(--hover-clr);
        }

    </style>
</head>
<body>
<nav id="sidebar">
        <ul>
            <li>
                <span class="logo"><i class="fas fa-tasks"></i> ToDoList</span>
            </li>
            <li class="active">
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>
            <li>
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="calendar.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Calendar</span>
                </a>
            </li>
            <li>
                <a href="profile.php">
                        <img src="<?php echo htmlspecialchars($row['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
                        <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
    <button onclick="toggleSidebar()" id="toggle-btn">
        <i class="fas fa-bars"></i>
    </button>
    
    <div class="container">
        <div class="header">
            <h1>ToDo App</h1>
            <p>Keep It Up</p>
        </div>
        
        <div class="stats-container">
            <div id="numbers">0 / 0</div>
            <div id="progressBar">
                <div id="progress"></div>
            </div>
        </div>

        <input type="text" id="searchInput" placeholder="Search Tasks..." onkeyup="searchTasks()">
        
        <form id="taskForm">
            <input type="text" id="taskTitleInput" placeholder="Task Title..." required>
            <textarea id="taskInput" placeholder="Write Your Task..." required></textarea>
            <select id="taskPriority">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
            <button id="newTask" type="submit">Add Task</button>
        </form>


        <div class="filter-buttons">
            <button class="filter-button" onclick="filterTasks('all')">All</button>
            <button class="filter-button" onclick="filterTasks('completed')">Done</button>
            <button class="filter-button" onclick="filterTasks('incomplete')">Incomplete</button>
        </div>

        <ul class="task-list"></ul>
        <button class="clear-completed">Clear Completed</button>

        <div class="popup" id="editPopup">
            <div class="popup-content">
                <span class="close-popup" onclick="closePopup()">X</span>
                <h3>Edit Task</h3>
                <input type="text" id="editTaskTitleInput" placeholder="Task Title" required>
                <textarea id="editTaskDescriptionInput" placeholder="Write ur tasks..." required></textarea>
                <select id="editTaskPriority">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
                <button id="saveEdit">Save</button>
            </div>
        </div>

        <div class="delete-confirmation" id="deleteConfirmation">
            <div class="delete-confirmation-content">
                <h3>Are you sure you want to delete this task?</h3>
                <button id="confirmDelete">Yes</button>
                <button id="cancelDelete">No</button>
            </div>
        </div>
    </div>

    <script>
        const toggleButton = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');
        const main = document.querySelector('main');

        function toggleSidebar() {
            sidebar.classList.toggle('close');
            sidebar.classList.toggle('open');
            main.classList.toggle('full-width');
        }

        function toggleSubMenu(button) {
            const subMenu = button.nextElementSibling;
            const isOpen = subMenu.classList.contains('show');

            const allSubMenus = document.querySelectorAll('.sub-menu');
            allSubMenus.forEach(menu => {
                if (menu !== subMenu) {
                    menu.classList.remove('show');
                }
            });

            subMenu.classList.toggle('show');
            
            const chevron = button.querySelector('.fa-chevron-down');
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggleButton = toggleButton.contains(event.target);
            const isSidebarOpen = sidebar.classList.contains('open');

            if (!isClickInsideSidebar && !isClickOnToggleButton && isSidebarOpen && window.innerWidth <= 800) {
                toggleSidebar();
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 800) {
                sidebar.classList.remove('close');
                sidebar.classList.remove('open');
                main.classList.remove('full-width');
            } else {
                sidebar.classList.add('close');
                main.classList.add('full-width');
            }
        });

        if (window.innerWidth <= 800) {
            sidebar.classList.add('close');
            main.classList.add('full-width');
        }

        if (window.innerWidth <= 800) {
            sidebar.classList.add('close');
            main.classList.add('full-width');
        }

        let tasks = JSON.parse(localStorage.getItem('tasks')) || [];
        let currentEditIndex = null;
        let currentDeleteIndex = null;
        let currentFilter = 'all';

        const addTask = () => {
            const taskTitleInput = document.getElementById('taskTitleInput').value;
            const taskDescription = document.getElementById('taskInput').value;
            const taskPriority = document.getElementById('taskPriority').value;

            tasks.push({
                title: taskTitleInput,
                description: taskDescription,
                priority: taskPriority,
                completed: false
            });

            updateTasksList();
            updateStats();
            saveTasks();

            document.getElementById('taskForm').reset();
        };

        const toggleTaskComplete = (index) => {
            tasks[index].completed = !tasks[index].completed;
            updateTasksList();
            updateStats();
            saveTasks();
        };

        const openEditPopup = (index) => {
            currentEditIndex = index;
            const task = tasks[index];
            document.getElementById('editTaskTitleInput').value = task.title;
            document.getElementById('editTaskDescriptionInput').value = task.description;
            document.getElementById('editTaskPriority').value = task.priority;
            document.getElementById('editPopup').style.display = 'flex';
        };

        const closePopup = () => {
            document.getElementById('editPopup').style.display = 'none';
            currentEditIndex = null;
        };

        const saveEditTask = () => {
            const newTitle = document.getElementById('editTaskTitleInput').value;
            const newDescription = document.getElementById('editTaskDescriptionInput').value;
            const newPriority = document.getElementById('editTaskPriority').value;

            if (currentEditIndex !== null) {
                tasks[currentEditIndex].title = newTitle;
                tasks[currentEditIndex].description = newDescription;
                tasks[currentEditIndex].priority = newPriority;
                updateTasksList();
                updateStats();
                saveTasks();
                closePopup();
            }
        };

        const openDeleteConfirmation = (index) => {
            currentDeleteIndex = index;
            document.getElementById('deleteConfirmation').style.display = 'flex';
        };

        const confirmDeleteTask = () => {
            if (currentDeleteIndex !== null) {
                tasks.splice(currentDeleteIndex, 1);
                updateTasksList();
                updateStats();
                saveTasks();
                closeDeleteConfirmation();
            }
        };

        const closeDeleteConfirmation = () => {
            document.getElementById('deleteConfirmation').style.display = 'none';
            currentDeleteIndex = null;
        };

        const clearCompletedTasks = () => {
            tasks = tasks.filter(task => !task.completed);
            updateTasksList();
            updateStats();
            saveTasks();
        };

        const filterTasks = (status) => {
            currentFilter = status;
            updateTasksList();
        };

        const updateTasksList = () => {
            const taskList = document.querySelector('.task-list');
            taskList.innerHTML = "";

            const filteredTasks = tasks.filter(task => {
                if (currentFilter === 'completed') {
                    return task.completed;
                } else if (currentFilter === 'incomplete') {
                    return !task.completed;
                }
                return true;
            });

            if (filteredTasks.length === 0) {
                taskList.innerHTML = `<li class="empty-state"><i class="fas fa-tasks"></i><p>No tasks available</p></li>`;
            } else {
                filteredTasks.forEach((task, index) => {
                    const taskItem = document.createElement('li');
                    taskItem.className = 'taskItem';

                    taskItem.innerHTML = `
                        <div class="task ${task.completed ? 'completed' : ''}">
                            <input type="checkbox" ${task.completed ? 'checked' : ''} onclick="toggleTaskComplete(${index})">
                            <span class="${task.priority === 'low' ? 'priority-low' : task.priority === 'medium' ? 'priority-medium' : 'priority-high'}">
                                <strong>${task.title}</strong>: ${task.description}
                            </span>
                        </div>
                        <div class="icons">
                            <i class="fas fa-edit" onclick="openEditPopup(${index})"></i>
                            <i class="fas fa-trash" onclick="openDeleteConfirmation(${index})"></i>
                        </div>
                    `;

                    taskList.appendChild(taskItem);
                });
            }
        };

        const updateStats = () => {
            const completedTasks = tasks.filter(task => task.completed).length;
            const totalTasks = tasks.length;
            document.getElementById("numbers").innerText = `${completedTasks} / ${totalTasks}`;
            const progress = (totalTasks > 0) ? (completedTasks / totalTasks) * 100 : 0;
            document.getElementById("progress").style.width = `${progress}%`;
        };

        const saveTasks = () => {
            localStorage.setItem('tasks', JSON.stringify(tasks));
        };

        document.getElementById('taskForm').addEventListener('submit', (e) => {
            e.preventDefault();
            addTask();
        });

        document.getElementById('saveEdit').addEventListener('click', saveEditTask);
        document.querySelector(".clear-completed").addEventListener("click", clearCompletedTasks);
        
        document.getElementById('confirmDelete').addEventListener('click', confirmDeleteTask);
        document.getElementById('cancelDelete').addEventListener('click', closeDeleteConfirmation);

        updateTasksList();
        updateStats();

        const searchTasks = () => {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const filteredTasks = tasks.filter(task => {
                const taskText = task.title.toLowerCase() + " " + task.description.toLowerCase();
                return taskText.includes(searchTerm);
            });

            const taskList = document.querySelector('.task-list');
            taskList.innerHTML = "";

            if (filteredTasks.length === 0) {
                taskList.innerHTML = `<li class="empty-state"><i class="fas fa-tasks"></i><p>No tasks found</p></li>`;
            } else {
                filteredTasks.forEach((task, index) => {
                    const taskItem = document.createElement('li');
                    taskItem.className = 'taskItem';

                    taskItem.innerHTML = `
                        <div class="task ${task.completed ? 'completed' : ''}">
                            <input type="checkbox" ${task.completed ? 'checked' : ''} onclick="toggleTaskComplete(${index})">
                            <span class="${task.priority === 'low' ? 'priority-low' : task.priority === 'medium' ? 'priority-medium' : 'priority-high'}">
                                <strong>${task.title}</strong>: ${task.description}
                            </span>
                        </div>
                        <div class="icons">
                            <i class="fas fa-edit" onclick="openEditPopup(${index})"></i>
                            <i class="fas fa-trash" onclick="openDeleteConfirmation(${index})"></i>
                        </div>
                    `;

                    taskList.appendChild(taskItem);
                });
            }
        };

        document.getElementById('searchInput').addEventListener('keyup', searchTasks);


    </script>
</body>
</html>
