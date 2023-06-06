<?php
session_start();

date_default_timezone_set('Asia/Taipei');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    // 如果使用者未登入，則跳轉回登入頁面
    header("Location: index.php");
    exit;
}



if (isset($_POST['bookID'])) 
{
    $bookID = $_POST["bookID"];
    $query = "DELETE FROM books WHERE bookID = '$bookID'";
    $query_run = mysqli_query($conn, $query);

    if($query_run)
    {
        echo "success";
    }
    else
    {
        echo "fail";
    }

    $conn->close();
}



?>
