<script type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js">
</script>
<script type="text/javascript">
   (function(){
      emailjs.init("dh3lIULQdye2ZJFg3");
   })();
</script>

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
    $bookID = $_POST['bookID'];
    // 查詢對應的 stdID
    $sql = "SELECT `borrowStd`, `bookName` FROM `borrowlist` WHERE `bookID` = '$bookID'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stdID = $row['borrowStd'];
    $bookName = $row['bookName'];
    

    // 查詢對應的 email
    $sql = "SELECT `email`, `name` FROM `users` WHERE `stdID` = '$stdID'";
    $result = mysqli_query($conn, $sql);
    $row2 = mysqli_fetch_assoc($result);
    $email = $row2['email'];
    $name = $row2['name'];
    ob_start();
    var_dump($row['bookName']);
    var_dump($row2['email']);
    var_dump($row2['name']);
    $output = ob_get_clean();
    file_put_contents('output.txt', $output);

    echo "<script>
        function sendEmail(to_name, to_email) {
            // 使用 EmailJS API 將郵件發送给收件人
            emailjs.send('service_00rxzfl', 'template_ykfecr8', {
                'to_name': '" . $row2['name'] . "',
                'to_email': '" . $row2['email'] . "',
                'bookName': '" . $row['bookName'] . "',
            }).then(function(response) {
                console.log('郵件已寄出：', response);
            }, function(error) {
                console.log('郵件發送失敗：', error);
            });
        }
        sendEmail('" . $row2['name'] . "', '" . $row2['email'] . "');
    </script>";


    $conn->close();
}


?>
