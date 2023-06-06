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
    $bookName = $_POST["bookName"];
    $current_time = date('Y-m-d H:i:s');

    $query = "UPDATE books SET `status` = 1 WHERE bookID = '$bookID'";
    $query_run = mysqli_query($conn, $query);

    if($query_run)
    {
        if(mysqli_affected_rows($conn) > 0)
        {
            $borrowListQuery = "INSERT INTO borrowlist (`bookID`, `bookName`, `borrowStd`, `borrowTime`) VALUES('$bookID', '$bookName', '{$_SESSION['userID']}', '$current_time')";
            $borrowListQuery_run = mysqli_query($conn, $borrowListQuery);
            if($borrowListQuery_run)
            {
                echo "success";
            }
            else
            {
                echo "fail";
            }
        }
        else
        {
            echo "please_retry";
        }
    }
    else
    {
        echo "fail";
    }

    $conn->close();
}



?>
