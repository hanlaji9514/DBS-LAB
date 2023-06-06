<?php
	session_start(); // 啟動 Session

	// 刪除 Session 中的使用者名稱
	if (isset($_SESSION['username'])) 
	{
		unset($_SESSION['username']);
	}

	if (isset($_SESSION['logintype'])) 
	{
		unset($_SESSION['logintype']);
	}

	if (isset($_SESSION['userID'])) 
	{
		unset($_SESSION['userID']);
	}

	// 將 Session 刪除並關閉 Session
	session_unset();
	session_destroy();

	// 導向首頁
	header("Location: http://127.0.0.1");
	exit();
?>