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
    <title>Calendar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
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

        .calendar {
            max-width: 100%;
            margin: 0 auto;
            padding: 2rem;
            background-color: var(--form-bg);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .calendar .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .calendar .header h1 {
            color: var(--base-clr);
            font-size: 2rem;
        }

        .calendar .header .btn {
            padding: 0.5rem 1rem;
            background-color: var(--accent-clr);
            color: var(--text-clr);
            border-radius: 5px;
            text-decoration: none;
        }

        .calendar .header .btn:hover {
            background-color: var(--hover-clr);
        }

        .calendar .header .btn:hover, 
        .calendar-nav button:hover {
            background-color: var(--hover-clr); 
            color: var(--secondary-text-clr);
        }


        .calendar .days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1rem;
            text-align: center;
            padding: 1rem;
            background-color: var(--form-bg);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .calendar .days div {
            font-weight: bold;
            color: var(--secondary-text-clr);
        }

        .calendar .dates {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1rem;
            text-align: center;
            padding: 1rem;
        }

        .calendar .dates div {
            color: #000000;
            padding: 1rem;
            border-radius: 50%;
            transition: background-color 0.3s;
            cursor: pointer;
        }

        .calendar .dates div:hover {
            background-color: var(--hover-clr);
        }

        @media (max-width: 800px) {
            main {
                margin-left: 0;
            }
            
            #sidebar {
                left: -250px;
            }
            
            #sidebar.open {
                left: 0;
            }

            .calendar .days,
            .calendar .dates {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .profile-picture {
            width: 50px; 
            height: 50px; 
            border-radius: 50%; 
            object-fit: cover; 
            margin-bottom: 20px; 
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

        main {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 300ms ease-in-out;
        }

        .calendar-container {
            max-width: 1000px;
            margin: 0 auto;
            background: var(--base-clr);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header h2 {
            margin: 0;
            font-size: 24px;
        }

        .calendar-nav {
            display: flex;
            gap: 10px;
        }

        .calendar-nav button {
            background: var(--accent-clr);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .calendar-nav button:hover{
            color: var(---hover-clr);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: bold;
            padding: 10px;
            background: var(--hover-clr);
            border-radius: 5px;
        }

        .calendar-day {
            min-height: 100px;
            padding: 10px;
            border: 1px solid var(--line-clr);
            border-radius: 5px;
            cursor: pointer;
        }

        .calendar-day:hover {
            background: var(--hover-clr);
        }

        .calendar-day.other-month {
            background: #f9f9f9;
            color: #999;
        }

        .calendar-day.today {
            background: #e6f3ff;
        }

        .event {
            background: var(--accent-clr);
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            margin: 2px 0;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .event-form {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .event-form.show {
            display: block;
        }

        .event-form input,
        .event-form textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid var(--line-clr);
            border-radius: 5px;
        }

        .event-form button {
            background: var(--accent-clr);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .overlay.show {
            display: block;
        }

        @media(max-width: 800px) {
            main {
                margin-left: 0;
                padding: 60px 20px 20px;
            }
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
        <div class="calendar-container">
            <div class="calendar-header">
                <h2 id="currentMonth">September 2024</h2>
                <div class="calendar-nav">
                    <button onclick="previousMonth()"><i class="fas fa-chevron-left"></i></button>
                    <button onclick="currentMonthView()">Today</button>
                    <button onclick="nextMonth()"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="calendar-grid" id="calendarGrid">
            </div>
        </div>
    </main>

    <div class="overlay" id="overlay"></div>
    
    <div class="event-form" id="eventForm">
        <h3>Add Event</h3>
        <input type="text" id="eventTitle" placeholder="Event Title">
        <textarea id="eventDescription" placeholder="Event Description"></textarea>
        <input type="time" id="eventTime">
        <button onclick="saveEvent()">Save</button>
        <button onclick="closeEventForm()">Cancel</button>        
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

        let currentDate = new Date();
        let selectedDate = null;
        let events = JSON.parse(localStorage.getItem('events')) || {};

        function generateCalendar(date) {
            const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            const startingDay = firstDay.getDay();
            const monthLength = lastDay.getDate();
            
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                              'July', 'August', 'September', 'October', 'November', 'December'];
            
            document.getElementById('currentMonth').textContent = 
                `${monthNames[date.getMonth()]} ${date.getFullYear()}`;

            const calendarGrid = document.getElementById('calendarGrid');
            calendarGrid.innerHTML = '';

            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            days.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.className = 'calendar-day-header';
                dayHeader.textContent = day;
                calendarGrid.appendChild(dayHeader);
            });

            const prevMonthDays = startingDay;
            const prevMonthLength = new Date(date.getFullYear(), date.getMonth(), 0).getDate();
            
            for (let i = prevMonthDays - 1; i >= 0; i--) {
                const day = document.createElement('div');
                day.className = 'calendar-day other-month';
                day.textContent = prevMonthLength - i;
                calendarGrid.appendChild(day);
            }

            for (let i = 1; i <= monthLength; i++) {
                const day = document.createElement('div');
                day.className = 'calendar-day';
                
                if (date.getFullYear() === new Date().getFullYear() &&
                    date.getMonth() === new Date().getMonth() &&
                    i === new Date().getDate()) {
                    day.classList.add('today');
                }

                day.textContent = i;
                day.onclick = () => openEventForm(date.getFullYear(), date.getMonth(), i);
                
                if (events[`${date.getFullYear()}-${date.getMonth() + 1}-${i}`]) {
                events[`${date.getFullYear()}-${date.getMonth() + 1}-${i}`].forEach((event, index) => {
                    const eventElement = document.createElement('div');
                    eventElement.className = 'event';
                    eventElement.innerHTML = `
                        <div class="event-title">${event.title}</div>
                        <div class="event-time">${event.time}</div>
                        <button class="delete-btn" onclick="confirmDeleteEvent('${date.getFullYear()}-${date.getMonth() + 1}-${i}', ${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    day.appendChild(eventElement);
                });
            }
            calendarGrid.appendChild(day);
        }

            function deleteEvent(button) {
                const eventDiv = button.parentElement;

                eventDiv.remove();
            }

            const nextMonthDays = 42 - (prevMonthDays + monthLength);
            for (let i = 1; i <= nextMonthDays; i++) {
                const day = document.createElement('div');
                day.className = 'calendar-day other-month';
                day.textContent = i;
                calendarGrid.appendChild(day);
            }
        }

        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            generateCalendar(currentDate);
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            generateCalendar(currentDate);
        }

        function currentMonthView() {
            currentDate = new Date();
            generateCalendar(currentDate);
        }

        function openEventForm(year, month, day) {
            selectedDate = { year, month, day };
            document.getElementById('eventForm').classList.add('show');
            document.getElementById('overlay').classList.add('show');
        }

        function closeEventForm() {
            document.getElementById('eventForm').classList.remove('show');
            document.getElementById('overlay').classList.remove('show');
            document.getElementById('eventTitle').value = '';
            document.getElementById('eventDescription').value = '';
            document.getElementById('eventTime').value = '';
        }

        function saveEvent() {
            const title = document.getElementById('eventTitle').value;
            const description = document.getElementById('eventDescription').value;
            const time = document.getElementById('eventTime').value;

            const eventKey = `${selectedDate.year}-${selectedDate.month + 1}-${selectedDate.day}`;
            if (!events[eventKey]) {
                events[eventKey] = [];
            }

            events[eventKey].push({ title, description, time });
            localStorage.setItem('events', JSON.stringify(events));
            closeEventForm();
            generateCalendar(currentDate);
        }

        async function confirmDeleteEvent(eventKey, index) {
            event.stopPropagation();
            
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'swal2-confirm',
                    cancelButton: 'swal2-cancel'
                }
            });

            if (result.isConfirmed) {
                deleteEvent(eventKey, index);
                await Swal.fire(
                    'Deleted!',
                    'Your event has been deleted.',
                    'success'
                );
            }
        }

        function deleteEvent(eventKey, index) {
            events[eventKey].splice(index, 1);
            
            if (events[eventKey].length === 0) {
                delete events[eventKey];
            }
            
            localStorage.setItem('events', JSON.stringify(events));
            generateCalendar(currentDate);
        }

        generateCalendar(currentDate);
    </script>
</body>
</html>
