<?php
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$result = $conn->query("
SELECT * FROM users 
WHERE username='$username' AND password='$password'
");

if($row = $result->fetch_assoc()){

    $_SESSION['user'] = $row['username'];
    $_SESSION['role'] = $row['role'];

    if($row['role'] == 'admin'){
        header("Location: dashboard.php");
    } else {
        header("Location: pos.php");
    }

} else {
    echo "Invalid login";
}
?>