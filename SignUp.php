<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Sign Up and Login</title>
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
        <link rel="stylesheet" href="CSS/styles.css">
    </head>
    <body>

        <h1>Sign Up and Sign In</h1>
        
        <div class="container" id="container">
            <div class="form-container sign-up-container">
                <form action="" method="POST">
                    <h1>Create Account</h1>
                    <input type="text" name="name" placeholder="Name" required />
                    <input type="email" name="email" placeholder="Email" required />
                    <input type="password" name="password" placeholder="Password" required />
                    <input type="submit" name="signup" value="Sign Up">
                </form>
            </div>

            <div class="form-container sign-in-container">
                <form action="" method="POST">
                    <h1>Sign in</h1>
                    <input type="email" name="email" placeholder="Email" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" />
                    <input type="password" name="password" placeholder="Password" required />
                    <input type="submit" name="login" value="Sign In">
                </form>
            </div>

            <div class="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <h1>Welcome Back! Hihihihi</h1>
                        <p>To keep connected with us, please log in with your credentials.</p>
                        <button class="ghost" id="signIn">Sign In</button>
                    </div>
                    <div class="overlay-panel overlay-right">
                        <h1>Hellodesuwa!!!</h1>
                        <p>Are you new here? Click the SIGN UP button to join! hehehehe</p>
                        <button class="ghost" id="signUp">Sign Up</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="JavaScript/styles.js"></script>
    </body>
</html>

<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Sign Up
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        echo "<div style='position: relative; top: 30px;'>All fields are required.</div>";
    } elseif (!preg_match("/^[a-zA-Z]*$/", $name)) {
        echo "<div style='position: relative; top: 30px;'>Name can only contain letters and spaces.</div>";
    } else {

        $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<div style='position: relative; top: 30px;'>Email is already registered.</div>";
        } else {

            $stmt = $conn->prepare("INSERT INTO tbl_users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password);
            
            if ($stmt->execute()) {
                echo "<div style='position: relative; top: 30px;'>Registration successful.</div>";
            } else {
                echo "<div style='position: relative; top: 30px;'>Error: " . $stmt->error . "</div>";
            }
        }
    }
}

// Sign In
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "<div style='position: relative; top: 30px;'>Email and password are required.</div>";
    } else {
        
        
        if (isset($_SESSION['failed_attempts']) && $_SESSION['failed_attempts'] >= 3) {
            $last_failed_time = $_SESSION['last_failed_time'];
            $current_time = time();
            $cooldown_time = 5; 
            
            if (($current_time - $last_failed_time) < $cooldown_time) {
                $remaining_time = $cooldown_time - ($current_time - $last_failed_time);
               echo "<div style='position: relative; top: 30px;'>You have reached the maximum number of login attempts. Please try again in " . ceil($remaining_time) . " seconds.</div>";
        exit;
                exit;
            } else {
            
                $_SESSION['failed_attempts'] = 0;
            }
        }

        $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($password === $user['password']) {
                unset($_SESSION['failed_attempts']);
                unset($_SESSION['last_failed_time']);
                
                echo "<div style='position: relative; top: 30px;'>Login successful.</div>";

                echo "<script>document.location='index.php';</script>";
            } else {
                $_SESSION['failed_attempts'] = isset($_SESSION['failed_attempts']) ? $_SESSION['failed_attempts'] + 1 : 1;
                $_SESSION['last_failed_time'] = time();
                
                echo "<div style='position: relative; top: 30px;'>Incorrect password.</div>";
            }
        } else {
            echo "<div style='position: relative; top: 30px;'>No user found with that email.</div>";
        }
    }
}

$conn->close();
?>
