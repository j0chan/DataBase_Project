<?php
session_start();
session_destroy(); // 세션 종료
header("Location: main.php"); // 메인 페이지로 리다이렉트
exit;
?>
