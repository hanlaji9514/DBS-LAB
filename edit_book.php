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

if(count($_POST)>0) 
{
	
	$current_time = date('Y-m-d H:i:s');
	$query = "UPDATE books SET `type` = '$_POST[type]', `bookName` = '$_POST[bookName]', `author` = '$_POST[author]', `updateDate` = '$current_time' WHERE `bookID` = '$_POST[bookID]'";	
	$query_run = mysqli_query($conn, $query);

	if($query_run)
	{
		echo '<script language="javascript">
		alert("編輯書籍成功!");
		</script>';
		$_SESSION['edit_result'] = 'successed';
		header("Location: administrator.php");
	}
	else
	{
		echo '<script>';
		echo 'alert("失敗")';
		echo '</script>';
	}
}	

?>
