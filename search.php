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

if($_SESSION['logintype'] == 'user')
{
	$borrowListsql2 = "SELECT * FROM borrowlist WHERE borrowStd = '{$_SESSION['userID']}'";
	$borrowListresult2 = $conn->query($borrowListsql2);	
}


if (isset($_POST['bookName']) || isset($_POST['type'])) 
{
    if(isset($_POST['bookName']))
	{
		$search = $_POST['bookName'];
		$sql = "SELECT * FROM books WHERE bookName LIKE '%$search%'";
	}
	else if(isset($_POST['type']))
	{
		$search = $_POST['type'];
		$sql = "SELECT * FROM books WHERE type LIKE '%$search%'";
	}
    $result = $conn->query($sql);
    // 將搜尋結果存儲在 $bookTable 變量中
    $html = "";
	if($_SESSION['logintype'] == 'admin')
	{
		if ($result->num_rows > 0) 
		{
			while($row = $result->fetch_assoc()) 
			{
				$html .= "<tr>";
				$html .= "<td>".$row["bookID"]."</td>";
				$html .= "<td>".$row["type"]."</td>";
				$html .= "<td>".$row["bookName"]."</td>";
				$html .= "<td>".$row["author"]."</td>";
				$html .= "<td>".$row["updateDate"]."</td>";
				$html .= "<td>".($row['status'] == 1 ? "已借出" : "未借出")."</td>";
				$html .= "<td>";
				$html .= "<div class = 'btn-group'>";
				$html .= '<button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#editBookModal" onclick="openEditModal('.$row['bookID'].', \''.$row['type'].'\', \''.$row['bookName'].'\', \''.$row['author'].'\')" '.($row['status'] == 1 ? 'disabled' : '').'>編輯</button>';
				$html .= '<button class="btn btn-outline-danger" type="button" onclick="deleteRow('.$row['bookID'].')" '.($row['status'] == 1 ? 'disabled' : '').'>刪除</button>';
				$html .= "</div>";
				$html .= "</td>";
				$html .= "</tr>";
			}
		} 
		else 
		{
			$html .= "<tr><td colspan='7'>沒有找到符合條件的書籍！</td></tr>";
		}
		// 將html回傳給前端頁面
		echo $html;
	}
	else if($_SESSION['logintype'] == 'user')
	{
		if ($result->num_rows > 0) 
		{
			$borrowedBooks = array();
			while ($borrowedBook = $borrowListresult2->fetch_assoc()) 
			{
				$borrowedBooks[$borrowedBook['bookID']] = true;
			}
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
				$html .= '<tr>';
				$html .= '<td>' . $bookID . '</td>';
				$html .= '<td>' . $row['type'] . '</td>';
				$html .= '<td>' . $row['bookName'] . '</td>';
				$html .= '<td>' . $row['author'] . '</td>';
				$html .= '<td>' . $row['updateDate'] . '</td>';
				$html .= '<td>' . ($row['status'] == 1 ? '已借出' : '未借出') . '</td>';
				$html .= '<td>';
				$html .= '<div class="btn-group">';
				$html .= '<button class="btn btn-outline-success" type="button" ' . ($row['status'] == 1 ? 'disabled' : '') . ' onclick="borrowBook(' . $bookID . ', \'' . $row['bookName'] . '\')">借閱</button>';
				$html .= $buttonHtml;
				$html .= '</div>';
				$html .= '</td>';
				$html .= '</tr>';
			}
		} 
		else 
		{
			$html .= "<tr><td colspan='7'>沒有找到符合條件的書籍！</td></tr>";
		}
		// 將html回傳給前端頁面
		echo $html;
	}
	

	$conn->close();
}



?>

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
