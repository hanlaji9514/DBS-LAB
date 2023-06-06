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
    $sql1 = "SELECT `borrowStd`, `bookName` FROM borrowlist WHERE `bookID` = '" . $_POST['bookID'] . "'";
    $result1 = mysqli_query($conn, $sql1);

    // 如果查詢失敗則停止執行
    if (!$result1) 
    {
        die("Error: " . $sql1 . "<br>" . mysqli_error($conn));
    }

    // 從 users 中取得 name 和 email
    $row1 = mysqli_fetch_assoc($result1);
    $stdID = $row1['borrowStd'];
    $bookName = $row1['bookName'];
    $sql2 = "SELECT `name`, `email` FROM users WHERE stdID = '" . $stdID . "'";
    $result2 = mysqli_query($conn, $sql2);

    // 如果查詢失敗則停止執行
    if (!$result2) {
        die("Error: " . $sql2 . "<br>" . mysqli_error($conn));
    }

    // 將所需資訊以 JSON 格式返回給前端
    $row2 = mysqli_fetch_assoc($result2);
    $response = array(
        "status" => "success",
        "username" => $row2['name'],
        "email" => $row2['email'],
        "bookName" => $bookName
    );
    echo json_encode($response);

    $conn->close();
}



?>
