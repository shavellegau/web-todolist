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
    <title>ToDo List App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');

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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background-color: var(--base-clr);
            color: var(--text-clr);
            display: flex;
            font-family: 'Poppins', sans-serif;
        }

        h1 {
            color: var(--text-clr);
            font-size: 2rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        h1 span {
            color: var(--accent-clr);
        }

        .container {
            background-color: var(--form-bg);
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            margin-bottom: 1rem;
            color: var(--base-clr);
        }

        .container p {
            margin-bottom: 1.5rem;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--accent-clr);
            color: var(--text-clr);
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: var(--hover-clr);
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

        .main-content {
            margin-left: 150px;
            width: calc(100% - 250px);
            padding: 2em;
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 20px;  
                width: calc(100% - 40px); 
                padding: 1em; 
            }

            .sidebar {
                display: none; 
            }
        }

        .main-content.full-width {
            margin-left: 0;
            width: 100%;
        }


        .todo-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 2em;
            margin-bottom: 2em;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            justify-content: center;
        }

        .todo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2em;
        }

        .todo-header h1 {
            color: var(--text-clr);
            font-size: 2em;
        }

        .create-todo-form {
            background: rgba(255, 255, 255, 0.15);
            padding: 1.5em;
            border-radius: 10px;
            margin-bottom: 2em;
        }

        .form-group {
            margin-bottom: 1em;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5em;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.8em;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: var(--text-clr);
        }

        .todo-lists-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5em;
            margin-top: 2em;
        }

        .todo-list-card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            padding: 1.5em;
            transition: all 0.3s ease;
        }

        .todo-list-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .todo-list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1em;
            padding-bottom: 0.5em;
            border-bottom: 2px solid var(--accent-clr);
        }

        .todo-list-title {
            font-size: 1.2em;
            font-weight: 600;
            color: var(--text-clr);
        }

        .btn {
            padding: 0.8em 1.5em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 0.5em;
        }

        .btn-primary {
            background-color: var(--accent-clr);
            color: var(--secondary-text-clr);
        }

        .btn-danger {
            background-color: var(--danger-clr);
            color: var(--text-clr);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1100;
            justify-content: center;
            align-items: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background-color: var(--base-clr);
            padding: 2em;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5em;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 1em;
            margin-top: 1.5em;
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
    <main>
        <h1>Welcome, <span><?php echo htmlspecialchars(strtoupper($row["name"])); ?></span>!</h1>
    
        <div class="main-content">
        <div class="todo-container">
            <div class="todo-header">
                <h1>My To-Do Lists</h1>
                <button class="btn btn-primary" onclick="showCreateTodoModal()">
                    <i class="fas fa-plus"></i> Create New List
                </button>
            </div>

            
            <div class="todo-lists-grid" id="todoListsContainer">
            </div>
        </div>
        </div>
    </div>

    <div class="modal" id="createTodoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create New To-Do List</h2>
                <button class="btn" onclick="closeCreateTodoModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="createTodoForm" onsubmit="createNewTodoList(event)">
                <div class="form-group">
                    <label for="todoListTitle">List Title</label>
                    <input type="text" id="todoListTitle" required placeholder="Enter list title...">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" onclick="closeCreateTodoModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create List</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="deleteConfirmModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Delete To-Do List</h2>
                <button class="btn" onclick="closeDeleteConfirmModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p>Are you sure you want to delete this to-do list? This action cannot be undone.</p>
            <div class="modal-footer">
                <button class="btn" onclick="closeDeleteConfirmModal()">Cancel</button>
                <button class="btn btn-danger" onclick="confirmDeleteTodoList()">Delete</button>
            </div>
        </div>
    </div>
    
    <div class="container">
        <h2>Quick Start</h2>
        <p>Get started by creating a new task or viewing your existing lists.</p>
        <a href="dashboard.php" class="btn"><i class="fas fa-tachometer-alt"></i> Go to Dashboard</a>
    </div>
    </main>
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

        let todoLists = JSON.parse(localStorage.getItem('todoLists')) || [];
        let currentDeleteId = null; 

        document.addEventListener('DOMContentLoaded', () => {
            renderTodoLists();
        });

        function showDeleteConfirmModal(id) {
            currentDeleteId = id; 
            document.getElementById('deleteConfirmModal').classList.add('show'); 
        }

        function closeDeleteConfirmModal() {
            document.getElementById('deleteConfirmModal').classList.remove('show'); 
            currentDeleteId = null; 
        }

        function confirmDeleteTodoList() {
            if (currentDeleteId !== null) {
                todoLists = todoLists.filter(list => list.id !== currentDeleteId); 
                saveTodoLists(); 
                renderTodoLists(); 
                closeDeleteConfirmModal(); 
            }
        }

        function createNewTodoList(event) {
            event.preventDefault();
            const title = document.getElementById('todoListTitle').value.trim();

            if (title) {
                const newList = {
                    id: Date.now(), 
                    title,
                    createdAt: new Date().toISOString(),
                    items: []
                };

                todoLists.push(newList); 
                saveTodoLists(); 
                renderTodoLists(); 
                closeCreateTodoModal(); 
            }
        }

        function showCreateTodoModal() {
            document.getElementById('createTodoModal').classList.add('show');
        }

        function closeCreateTodoModal() {
            document.getElementById('createTodoModal').classList.remove('show');
            document.getElementById('createTodoForm').reset();
        }

        function saveTodoLists() {
            localStorage.setItem('todoLists', JSON.stringify(todoLists));
        }

        function showDashboard(title) {
            const dashboard = document.getElementById('dashboard');
            const dashboardTitle = document.getElementById('dashboardTitle');
            const tasksContainer = document.getElementById('tasks');

            dashboardTitle.textContent = title;

            tasksContainer.innerHTML = '';

            const selectedList = todoLists.find(list => list.title === title);

            if (selectedList && selectedList.items.length > 0) {
                selectedList.items.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.textContent = item; 
                    tasksContainer.appendChild(itemElement);
                });
            } else {
                tasksContainer.textContent = 'No tasks available for this list.';
            }

            dashboard.style.display = 'block';
        }


        function renderTodoLists() {
            const container = document.getElementById('todoListsContainer');
            container.innerHTML = todoLists.map(list => `
                <div class="todo-list-card">
                    <div class="todo-list-header">
                        <h3 class="todo-list-title">
                            <a href="dashboard.php" onclick="showDashboard('${list.title}')">${list.title}</a>
                        </h3>
                        <button class="btn btn-danger" onclick="showDeleteConfirmModal(${list.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <p>Created: ${new Date(list.createdAt).toLocaleDateString()}</p>
                </div>
            `).join('');
        }

        function goToDashboard(listName) {
            const type = listName === 'School' ? 'school' : 'other'; 
            localStorage.setItem('currentList', listName);
            localStorage.setItem('listType', type); 
            window.location.href = 'dashboard.php?list=' + encodeURIComponent(listName) + '&type=' + encodeURIComponent(type); 
            
        }

    </script>
</body>
</html>