<?php
require 'config.php';
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION["id"];

$stmt = $conn->prepare("SELECT * FROM tb_user WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "<script>alert('User not found.');</script>";
    header("Location: logout.php");
    exit();
}

if (isset($_POST["update"])) {
    $name = htmlspecialchars($conn->real_escape_string($_POST["name"]));
    $username = htmlspecialchars($conn->real_escape_string($_POST["username"]));
    $email = htmlspecialchars($conn->real_escape_string($_POST["email"]));
    $password = $_POST["password"];
    $confirmpassword = $_POST["confirm_password"];

    $stmt = $conn->prepare("SELECT * FROM tb_user WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->bind_param("ssi", $username, $email, $id);
    $stmt->execute();
    $duplicate = $stmt->get_result();

    if ($duplicate->num_rows > 0) {
        echo "<script>alert('Username or Email Has Already Been Taken');</script>";
    } else {
        $updateFields = [];
        $updateFields[] = "name = ?";
        $updateFields[] = "username = ?";
        $updateFields[] = "email = ?";

        $types = "sss";
        if (!empty($password)) {
            if ($password === $confirmpassword) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateFields[] = "password = ?";
                $types .= "s";
            } else {
                echo "<script>alert('Password baru tidak cocok'); window.history.back();</script>";
                return false;
            }
        }

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $file_tmp = $_FILES['profile_picture']['tmp_name'];
            $file_name = basename($_FILES['profile_picture']['name']);
            $file_size = $_FILES['profile_picture']['size'];
            $file_type = $_FILES['profile_picture']['type'];

            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

            if (in_array($file_type, $allowed_types) && $file_size < 2 * 1024 * 1024) {
                $upload_dir = 'uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_path = $upload_dir . uniqid() . '_' . $file_name;

                if (move_uploaded_file($file_tmp, $file_path)) {
                    if ($row['profile_picture'] && $row['profile_picture'] != 'default-profile.png') {
                        if (file_exists($row['profile_picture'])) {
                            unlink($row['profile_picture']);
                        }
                    }
                    $updateFields[] = "profile_picture = ?";
                    $types .= "s";
                } else {
                    echo "<script>alert('Error uploading file.');</script>";
                }
            } else {
                echo "<script>alert('Invalid file type or size exceeds limit.');</script>";
            }
        }

        if (!empty($updateFields)) {
            $updateQuery = "UPDATE tb_user SET " . implode(", ", $updateFields) . " WHERE id = ?";

            $types .= "i";

            $stmt = $conn->prepare($updateQuery);

            $params = [];
            foreach ([$name, $username, $email, $hashedPassword ?? null, $file_path ?? null] as $param) {
                if ($param !== null) {
                    $params[] = $param;
                }
            }
            $params[] = $id;

            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
                exit();
            } else {
                echo "<script>alert('Error updating profile: " . mysqli_error($conn) . "');</script>";
            }
        }
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

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
            font-family: 'Poppins', sans-serif;
            background-color: var(--base-clr);
            color: var(--secondary-text-clr);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .edit-profile-container {
            background-color: var(--form-bg);
            border-radius: 12px;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--base-clr);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: .5rem;
            font-weight: 500;
            color: var(--line-clr);
        }

        input[type="text"], 
        input[type="email"], 
        input[type="password"], 
        input[type="file"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--form-border);
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            background-color: #fff;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, 
        input[type="email"]:focus, 
        input[type="password"]:focus {
            border-color: var(--base-clr);
        }

        .profile-picture-wrapper {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .profile-picture {
            width: 120px; 
            height: 120px; 
            border-radius: 50%; 
            object-fit: cover; 
            margin-bottom: 20px; 
        }

        .upload-btn {
            margin-top: 1rem;
            display: block;
            text-align: center;
            cursor: pointer;
            background-color: var(--accent-clr);
            color: var(--text-clr);
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .upload-btn:hover {
            background-color: var(--hover-clr);
        }

        .submit-btn {
            display: block;
            width: 100%;
            background-color: var(--base-clr);
            color: var(--text-clr);
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: var(--hover-clr);
        }

        @media (max-width: 500px) {
            .edit-profile-container {
                padding: 1.5rem;
            }

            .profile-picture {
                width: 100px;
                height: 100px;
            }
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
    <div class="edit-profile-container">
        <h2>Edit Profile</h2>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="profile-picture-wrapper">
                <img src="<?php echo $row['profile_picture'] ? $row['profile_picture'] : 'default-profile.png'; ?>" alt="Profile Picture" class="profile-picture" id="profilePicture">
                <input type="file" id="fileInput" name="profile_picture" accept="image/*" style="display: none;" onchange="previewImage(event)">
                <a href="#" class="upload-btn" onclick="document.getElementById('fileInput').click(); return false;">
                    <i class="fas fa-upload"></i> Upload New Picture
                </a>
            </div>

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>

            <button type="submit" name="update" class="submit-btn">Save Changes</button>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profilePicture');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }


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
                sidebar.classList.remove('open');
                main.classList.add('full-width');
            }
        });

        if (window.innerWidth <= 800) {
            sidebar.classList.add('close');
            main.classList.add('full-width');
        }
    </script>
</body>
</html>