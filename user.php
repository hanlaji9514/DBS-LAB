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

if (!isset($_SESSION['username']) || !$_SESSION['logintype'] == 'user') {
    // 如果使用者未登入，則跳轉回登入頁面
    header("Location: index.php");
    exit;
}


$sql = "SELECT * FROM books";
$result = $conn->query($sql);

if($_SESSION['logintype'] == 'user')
{
  $borrowListsql = "SELECT * FROM borrowlist WHERE borrowStd = '{$_SESSION['userID']}'";
  $borrowListresult = $conn->query($borrowListsql);
  
  $borrowListsql2 = "SELECT * FROM borrowlist WHERE borrowStd = '{$_SESSION['userID']}'";
  $borrowListresult2 = $conn->query($borrowListsql2);
}


?>

<!DOCTYPE html>
<html>
<head>

<script src="js/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<script src="js/bootstrap.min.js"></script>
    <meta charset="UTF-8">
    <title>小型圖書館</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <p>You have successfully logged in.</p>
    <h1>書籍清單</h1>
    <div class="input-group mb-3">
	<h4>依書名查詢：</h4>
		<div class="col-sm-4">
			<input type="text" class="form-control" placeholder="書名" aria-label="Recipient's username" aria-describedby="basic-addon2" id="bookName" required>
		</div>
		<div class="input-group-append">
			<button class="btn btn-outline-secondary" type="button" id="searchBtn">查詢</button>
			<button class="btn btn-outline-secondary" type="button" id="searchAllBtn">查詢所有</button>
		</div>
	</div>

	<div class="input-group mb-3">
		<h4>依類別查詢：</h4>
		<div class="d-flex justify-content-between align-items-center mb-3">
			<div class="form-group mb-0 me-3">
				<select class="form-select" id="categorySelect">
				<option selected disabled>請選擇類別</option>
				<option value="">全部</option>
				<option value="心理">心理</option>
				<option value="輕小說">輕小說</option>
				<option value="電玩遊戲">電玩遊戲</option>
				<option value="電腦資訊">電腦資訊</option>
				</select>
			</div>
			<button type="button" class="btn btn-outline-secondary" id="categoryBtn">依類別查詢</button>
		</div>
	</div>

    <table class="table table-striped table-hover">
        <tr>
            <th>ISBN編號</th>
            <th>類別</th>
            <th>書名</th>
            <th>作者</th>
            <th>更新時間</th>
            <th>狀態</th>
			<th>操作</th>
        </tr>
		<tbody id="bookTable">
		<?php
// 將借閱清單轉換為關聯數組
$borrowedBooks = array();
while ($borrowedBook = $borrowListresult2->fetch_assoc()) {
    $borrowedBooks[$borrowedBook['bookID']] = true;
}

// 遍歷書籍結果
while ($row = $result->fetch_assoc()) 
{
    $bookID = $row['bookID'];
    $isBookBorrowed = isset($borrowedBooks[$bookID]);
    // 生成按鈕HTML
    $buttonHtml = '';
if (!$isBookBorrowed) 
{
    $buttonHtml = '<button class="btn btn-outline-info" type="button" ' . ($row['status'] == 0 ? 'disabled' : '') . ' onclick="notifyBorrower(' . $bookID . ')">我想借</button>';
}
else 
{
    $buttonHtml = '<button class="btn btn-outline-info" type="button" disabled>我想借</button>';
}
    // 生成表格HTML
    echo '<tr>';
    echo '<td>' . $bookID . '</td>';
    echo '<td>' . $row['type'] . '</td>';
    echo '<td>' . $row['bookName'] . '</td>';
    echo '<td>' . $row['author'] . '</td>';
    echo '<td>' . $row['updateDate'] . '</td>';
    echo '<td>' . ($row['status'] == 1 ? '已借出' : '未借出') . '</td>';
    echo '<td>';
    echo '<div class="btn-group">';
    echo '<button class="btn btn-outline-success" type="button" ' . ($row['status'] == 1 ? 'disabled' : '') . ' onclick="borrowBook(' . $bookID . ', \'' . $row['bookName'] . '\')">借閱</button>';
    echo $buttonHtml;
    echo '</div>';
    echo '</td>';
    echo '</tr>';
}
?>
	</tbody>
    </table>

	
	<h1>我的借閱清單</h1>
	<table class="table table-striped table-hover">
        <tr>
            <th>ISBN編號</th>
            <th>書名</th>
            <th>借閱時間</th>
			<th>操作</th>
        </tr>
		<tbody id="borrowTable">
			<?php while($row_2 = $borrowListresult->fetch_assoc()) { ?>
				<tr>
					<td><?php echo $row_2["bookID"]; ?></td>
					<td><?php echo $row_2["bookName"]; ?></td>
					<td><?php echo $row_2["borrowTime"]; ?></td>
					<td>
					<div class = "btn-group">
					<button class="btn btn-outline-success" type="button" onclick="returnBook(<?php echo $row_2['bookID']; ?>, '<?php echo $row_2['bookName']; ?>')">還書</button>				
					</div>
					</td>   
				</tr>
			<?php } ?>
		</tbody>
    </table>

	<button type="button" class="btn btn-danger" onclick=confirmLogout()>登出圖書館系統</button>



</body>
</html>

<script> //按下查詢按紐
	$(document).ready(function() {
    $("#searchBtn").click(function() {
        var bookName = $("#bookName").val();
        if (bookName.trim() === "") {
            alert("書名欄位不能為空");
            return;
        }
        $.ajax({
            url: "search.php",
            type: "POST",
            data: {bookName: bookName},
            success: function(response) {
                $("#bookTable").html(response);
            }
        });
    });
});
</script>

<script> //按下查詢所有按紐 重新以bookName為空來查詢SQL
	$(document).ready(function() {
    $("#searchAllBtn").click(function() {
        $.ajax({
            url: "search.php",
            type: "POST",
            data: {bookName: ''},
            success: function(response) {
                $("#bookTable").html(response);
            },
            please_retry: function(response)
            {
              alert("書本已被借出，請重試");
            },
        });
    });
});
</script>

<script> //按下按類別查詢按紐
	$(document).ready(function() {
    $("#categoryBtn").click(function() {
		var category = document.getElementById('categorySelect').value;
        $.ajax({
            url: "search.php",
            type: "POST",
            data: {type: category},
            success: function(response) {
                $("#bookTable").html(response);
            }
        });
    });
	});
</script>

<script>
  function borrowBook(bookID, bookName) {
    if (confirm("確定要借《" + bookName + "》嗎？")) {
      // 使用AJAX向後端發送請求
      $.ajax({
        url: "borrow.php",
        type: "POST",
        data: { bookID: bookID, bookName: bookName },
        success: function (response) {
          if (response == "success") {
            alert("借閱成功!");
            // 刷新頁面
            location.reload();
          } else {
            alert("借閱失敗");
          }
        }
      });
    }
  }
</script>

<script>
  function returnBook(bookID, bookName) {
    if (confirm("確定要還《" + bookName + "》嗎？")) {
      // 使用AJAX向後端發送請求
      $.ajax({
        url: "return.php",
        type: "POST",
        data: { bookID: bookID, bookName: bookName },
        success: function (response) {
          if (response == "success") {
            alert("還書成功!");
            // 刷新頁面
            location.reload();
          } else {
            alert("還書失敗");
          }
        }
      });
    }
  }
</script>

<script>
  function notifyBorrower(bookID) {
    if (confirm("幫您向借書者發送通知，請借書者盡快還書。")) {
      // 使用AJAX向後端發送請求
      $.ajax({
        url: "get_borrower_info.php",
        type: "POST",
        data: {bookID: bookID},
        success: function (response) {
          // 解析伺服器回傳的JSON數據
          var result = JSON.parse(response);
          if (result.status == "success") {
            //var userInfo = result.data;
            var templateParams = {
              to_name: result.username,
              to_email: result.email,
              bookName: result.bookName
            };
            // 使用EmailJS發送郵件
            emailjs.send('service_00rxzfl', 'template_ykfecr8', templateParams)
              .then(function(response) {
                alert("成功寄出通知！");
              }, function(error) {
                alert("寄出失敗");
              });
          } else {
            alert("獲取使用者資訊失敗");
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
          alert("獲取使用者資訊失敗2");
        }
      });
    }
  }
</script>

<script>
	function confirmLogout() 
	{
		if (confirm('確定要登出嗎？')) 
		{
			// 使用 AJAX 或表單提交等方式，清除 $_SESSION['username']
			$.ajax({
			type: "POST",
			url: "logout.php",
			success: function() {
				// 清除成功，重新導向
				window.location.href = "http://127.0.0.1";
			}
			});
		}
	}
</script>