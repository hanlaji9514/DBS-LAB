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

if (!isset($_SESSION['username']) || !$_SESSION['logintype'] == 'admin') {
    // 如果使用者未登入，則跳轉回登入頁面
    header("Location: index.php");
    exit;
}

if(isset($_SESSION['add_result'])) 
{
	if($_SESSION['add_result'] == 'duplicate_bookID')
	{
	  // 處理重複ISBN碼的情況
	  echo '<script>alert("新增失敗! ISBN碼重複!");</script>';
	}
	else if($_SESSION['add_result'] == 'successed')
	{
	 	echo '<script>alert("新增成功!");</script>';

	  	$sql = "SELECT `email`, `name` FROM users";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			echo "<script>
                function sendEmail(to_name, to_email) {
                    // 使用 EmailJS API 將郵件發送给收件人
                    emailjs.send('service_00rxzfl', 'template_j058i2s', {
                        'to_name': to_name,
                        'to_email': to_email,
                        'type': '" . $_SESSION['type'] . "',
                        'bookName': '" . $_SESSION['bookName'] . "',
                        'author': '" . $_SESSION['author'] . "'
                    }).then(function(response) {
                        console.log('郵件已寄出：', response);
                    }, function(error) {
                        console.log('郵件發送失敗：', error);
                    });
                }
                sendEmail('" . $row['name'] . "', '" . $row['email'] . "');
              	</script>";
		}
		} else {
		echo "0 結果";
		}
		unset($_SESSION['bookName']);
		unset($_SESSION['type']);
		unset($_SESSION['author']);
	  
	}
	// 重設 $_SESSION['add_result'] 的值
	unset($_SESSION['add_result']);
}

if(isset($_SESSION['edit_result'])) 
{
	if($_SESSION['edit_result'] == 'successed')
	{
	  echo '<script>alert("編輯書籍成功!");</script>';
	}
	// 重設 $_SESSION['add_result'] 的值
	unset($_SESSION['edit_result']);
}

$sql = "SELECT * FROM books";
$result = $conn->query($sql);

$borrowListsql = "SELECT * FROM borrowlist";
$borrowListresult = $conn->query($borrowListsql);

?>

<script type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js">
</script>


<!DOCTYPE html>
<html>
<head>

<script src="js/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<script src="js/bootstrap.min.js"></script>
    <meta charset="UTF-8">
    <title>小型圖書館(管理員用)</title>
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


	<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
	新增書籍
	</button>
	<!-- Modal -->
	<div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="addBookModalLabel">新增書籍</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
			<form action="add_book.php" method="POST">
				<div class="modal-body">
					<div class="form-group">
						<label for="bookID">ISBN編號:</label>
						<input type="text" class="form-control" id="bookID" name="bookID" required>
					</div>
					<div class="form-group">
						<label for="type">類別:</label>
						<input type="text" class="form-control" id="type" name="type" required>
					</div>
					<div class="form-group">
						<label for="bookName">書名:</label>
						<input type="text" class="form-control" id="bookName" name="bookName" required>
					</div>
					<div class="form-group">
						<label for="author">作者:</label>
						<input type="text" class="form-control" id="author" name="author" required>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
					<button type="submit" class="btn btn-primary" onclick="sendEmail()">確認</button>
				</div>
			</form>
			</div>
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
			<?php while($row = $result->fetch_assoc()) { ?>
				<tr>
					<td><?php echo $row["bookID"]; ?></td>
					<td><?php echo $row["type"]; ?></td>
					<td><?php echo $row["bookName"]; ?></td>
					<td><?php echo $row["author"]; ?></td>
					<td><?php echo $row["updateDate"]; ?></td>
					<td><?php if($row['status'] == 1)
								{
									echo "已借出";
								} 
								else 
								{
									echo "未借出";
								} ?>
					</td>
					<td>
					<div class = "btn-group">
					<button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#editBookModal" onclick="openEditModal(<?php echo $row['bookID']; ?>, '<?php echo $row['type']; ?>', '<?php echo $row['bookName']; ?>', '<?php echo $row['author']; ?>')" <?php if ($row['status'] == 1) echo "disabled"; ?>>編輯</button>
					<button class="btn btn-outline-danger" type="button" onclick="deleteRow(<?php echo $row['bookID']; ?>)" <?php if ($row['status'] == 1) echo "disabled"; ?>>刪除</button>
							</div>
					</td>   
				</tr>
			<?php } ?>
		</tbody>
    </table>

	<h1>管理所有借閱清單</h1>
	<table class="table table-striped table-hover">
        <tr>
            <th>借閱者學號</th>
			<th>ISBN編號</th>
            <th>書名</th>
            <th>借閱時間</th>
			<th>操作</th>
        </tr>
		<tbody id="borrowTable">
			<?php while($row_2 = $borrowListresult->fetch_assoc()) { ?>
				<tr>
					<td><?php echo $row_2["borrowStd"]; ?></td>
					<td><?php echo $row_2["bookID"]; ?></td>
					<td><?php echo $row_2["bookName"]; ?></td>
					<td><?php echo $row_2["borrowTime"]; ?></td>
					<td>
					<div class = "btn-group">
					<button class="btn btn-outline-success" type="button" onclick="returnBook('<?php echo $row_2['borrowStd']; ?>', '<?php echo $row_2['bookID']; ?>', '<?php echo $row_2['bookName']; ?>')">還書</button>				
					</div>
					</td>   
				</tr>
			<?php } ?>
		</tbody>
    </table>

	<!-- 定義編輯書籍Modal視窗 -->
	<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editBookModalLabel">編輯書籍</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form action="edit_book.php" method="POST">
					<div class="modal-body">
						<input type="hidden" id="editBookID" name="bookID">
						<div class="form-group">
							<label for="editType">類別:</label>
							<input type="text" class="form-control" id="editType" name="type" required>
						</div>
						<div class="form-group">
							<label for="editBookName">書名:</label>
							<input type="text" class="form-control" id="editBookName" name="bookName" required>
						</div>
						<div class="form-group">
							<label for="editAuthor">作者:</label>
							<input type="text" class="form-control" id="editAuthor" name="author" required>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
						<button type="submit" class="btn btn-primary">儲存</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<button type="button" class="btn btn-danger" onclick=confirmLogout()>登出管理員系統</button>



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
            }
        });
    });
});
</script>

<script>
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
    function deleteRow(bookID) 
    {
        if (confirm("確定要刪除這本書嗎？")) 
        {
            // 使用AJAX向後端發送請求
            $.ajax({
                url: "delete.php",
                type: "POST",
                data: {bookID: bookID},
                success: function(response) {
                    if(response == "success") {
                        alert("刪除成功!");
                        // 刷新頁面
                        location.reload();
                    } else {
                        alert("刪除失敗");
                    }
                }
            });
        }
    }
</script>

<script>
	function openEditModal(bookID, type, bookName, author) 
	{
		// 將書籍資料填入輸入框
		document.getElementById("editBookID").value = bookID;
		document.getElementById("editType").value = type;
		document.getElementById("editBookName").value = bookName;
		document.getElementById("editAuthor").value = author;
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

<script>
  function returnBook(borrowStd, bookID, bookName) {
    if (confirm("確定要還《" + bookName + "》嗎？")) {
      // 使用AJAX向後端發送請求
      $.ajax({
        url: "return.php",
        type: "POST",
        data: {bookID: bookID, bookName: bookName, borrowStd: borrowStd},
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