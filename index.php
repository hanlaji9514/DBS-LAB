<?php

session_start();

$message="";
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "test";

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);
// 檢查連線
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}






if(count($_POST)>0) 
{
	$result = mysqli_query($conn,"SELECT * FROM admins WHERE account='" . $_POST["user_name"] . "' and pwd = '". $_POST["pwd"]."'");
	$row  = mysqli_fetch_array($result);
	//處理查詢結果
	if(is_array($row)) 
	{
		if($row['name']!='')
		{
			// $message = "管理模式 -- " .$row['name'];
			$_SESSION['username'] = $row['name']; // 設置 session 以表示使用者已登入
      $_SESSION['logintype'] = 'admin'; //管理者模式
			header("Location: administrator.php");  // 跳轉到管理者頁面
			exit;
		}
		else
		{
			$message = "請輸入使用者名稱和密碼";
		}
	} 
	else 
	{
		//下查詢指令
		$result = mysqli_query($conn,"SELECT * FROM users WHERE stdID='" . $_POST["user_name"] . "' and pwd = '". $_POST["pwd"]."'");
		$row  = mysqli_fetch_array($result);
		//處理查詢結果
		if(is_array($row)) 
		{
			if($row['stdID']!='')
			{
				// $message = "成功登入" .$row['name'];
        $_SESSION['username'] = $row['name']; // 設置 session 以表示使用者已登入
        $_SESSION['userID'] = $_POST["user_name"];
        $_SESSION['logintype'] = 'user'; //管理者模式
			  header("Location: user.php");  // 跳轉到使用者頁面
			}
			else
			{
				$message = "請輸入使用者名稱和密碼";
			}
		} 
		else 
		{
			$message = "使用者名稱或密碼錯誤";
		}
	}
		
	
}	
?>

<!doctype html>
<html lang="zh-hant">
    <head>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }
    </style>
	
    

    <link href="signin.css" rel="stylesheet">
    <title>小型圖書館-登入</title>
    </head>
    <body class="text-center">
    <main class="form-signin w-100 m-auto">
      <form method="POST" action="">
        <h1 class="h3 mb-3 fw-normal">使用者登入</h1>

        <div class="form-floating">
          <input type="text" class="form-control" id="floatingInput" placeholder="使用者名稱" name="user_name">
          <label for="floatingInput">使用者名稱</label>
        </div>
        <div class="form-floating">
          <input type="password" class="form-control" id="floatingPassword" placeholder="密碼" name="pwd">
          <label for="floatingPassword">密碼</label>
        </div>
		    <!-- 印出結果 -->
        <?php if($message!="") { echo $message; }?><br><br>
        
        <button class="w-100 btn btn-lg btn-primary" type="submit">登入</button>

      </form>
      <div class="text-center mt-3">
        <a href="register.php">使用者註冊</a>
      </div>
    </main>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script><script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script><script src="dashboard.js"></script>
    </body>
</html>