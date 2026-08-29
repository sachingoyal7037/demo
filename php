========================database.php===========================
 
<?php

$conn = mysqli_connect("localhost", "root", "");

$sql = "CREATE DATABASE sachin";

if(mysqli_query($conn,$sql)){
    echo"database created";
}

?>

==========================connection.php========================

<?php

$conn = mysqli_connect("localhost", "root", "", "sachin");

if (!$conn) {
    die("database connection failed: " . mysqli_connect_error());
}

?>


=============================table.php==============================

<?php 

required "connection.php";

$sql = "CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    login_time DATETIME NULL,
    logout_time DATETIME NULL
	);"
	
if(mysqli_query($conn,$sql)){
	echo"table created";
}

==========================register.php============================

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
</head>
<body>

<h2>Registration Form</h2>

<form method="POST" action="register_insert.php">

    <label>Name</label>
    <input type="text" name="name" required>
    <br><br>

    <label>Email</label>
    <input type="email" name="email" required>
    <br><br>

    <label>Phone</label>
    <input type="text" name="phone" required>
    <br><br>

    <label>Password</label>
    <input type="password" name="password" required>
    <br><br>

    <button type="submit">Register</button>

</form>

<br>

<a href="login.php">Already have an account? Login</a>

</body>
</html>

================================regester_insert.php=====================

<?php

required "connection.php";

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];

$password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, phone, password)
        VALUES ('$name', '$email', '$phone', '$password')";

if (mysqli_query($conn, $sql)) {

    echo "Registration successful!";
    echo "<br>";
    echo "<a href='login.php'>Go to Login</a>";

} else {

    echo "Registration failed: " . mysqli_error($conn);

}

?>

===================================login.php============================

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<form method="POST" action="login_check.php">

    <label>Email</label>
    <input type="email" name="email" required>
    <br><br>

    <label>Password</label>
    <input type="password" name="password" required>
    <br><br>

    <button type="submit">Login</button>

</form>

<br>

<a href="register.php">Create New Account</a>

</body>
</html>

==================================login_check.php=============================

<?php

session_start();

required "connection.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

    $row = mysqli_fetch_assoc($result);

    if (password_verify($password, $row['password'])) {

        $login_time = date("Y-m-d H:i:s");

        $id = $row['id'];

        $update = "UPDATE users 
                   SET login_time='$login_time'
                   WHERE id='$id'";

        mysqli_query($conn, $update);

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['phone'] = $row['phone'];

        header("Location: dashboard.php");
        exit();

    } else {

        echo "Invalid password";

    }

} else {

    echo "Email not registered";

}

?>

================================dashboard.php============================

<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h2>Welcome <?php echo $_SESSION['name']; ?></h2>

<p>Name: <?php echo $_SESSION['name']; ?></p>

<p>Email: <?php echo $_SESSION['email']; ?></p>

<p>Phone: <?php echo $_SESSION['phone']; ?></p>

<br>

<a href="logout.php">Logout</a>

</body>
</html>

==================================logout.php=============================

<?php

session_start();

required "connection.php";

$user_id = $_SESSION['user_id'];

$logout_time = date("Y-m-d H:i:s");

$sql = "UPDATE users SET logout_time='$logout_time' WHERE id='$user_id'";

mysqli_query($conn, $sql);

session_unset();
session_destroy();

header("Location: login.php");
exit();

?>
