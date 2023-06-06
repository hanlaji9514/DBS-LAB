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
	$result = mysqli_query($conn, "SELECT bookID FROM books WHERE bookID='" . $_POST["bookID"] . "'");
	$row  = mysqli_fetch_array($result);
	//處理查詢結果
	if(is_array($row)) 
	{
		echo '<script>';
		echo 'alert("失敗! ISBN碼重複!")';
		echo '</script>';
		$_SESSION['add_result'] = 'duplicate_bookID';
		header("Location: administrator.php");
	} 
	else 
	{
		$current_time = date('Y-m-d H:i:s');
		$query = "INSERT INTO books (`bookID`, `type`, `bookName`, `author`, `updateDate`, `status`) VALUES('$_POST[bookID]','$_POST[type]','$_POST[bookName]', '$_POST[author]', '$current_time', '0')";
    	$query_run = mysqli_query($conn, $query);

		if($query_run)
		{
			echo '<script language="javascript">
			alert("建立書籍成功!");
			</script>';
			$_SESSION['add_result'] = 'successed';
			$_SESSION['bookName'] = $_POST["bookName"];
			$_SESSION['type'] = $_POST["type"];
			$_SESSION['author'] = $_POST["author"];

			header("Location: administrator.php");
		}
		else
		{
			echo '<script>';
			echo 'alert("失敗")';
			echo '</script>';
		}
	}
}	

?>
