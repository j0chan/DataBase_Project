<?php
session_start();

// Oracle DB 연결 설정
$db = '(DESCRIPTION = (ADDRESS_LIST=(ADDRESS = (PROTOCOL = TCP)(HOST = 203.249.87.57)(PORT = 1521))) (CONNECT_DATA = (SID = orcl)))';
$username = "DB502_PROJ_G3";
$password = "1234";
$connect = oci_connect($username, $password, $db);
if (!$connect) {
    $e = oci_error();
    die("Database connection failed: " . htmlentities($e['message'], ENT_QUOTES));
}

// 세션에서 사용자 ID 가져오기
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['username'];

// 입력값 가져오기
$name = $_POST['name'];
$nickname = $_POST['nickname'];
$email = $_POST['email'];
$birthdate = $_POST['birthdate'];
$phone = $_POST['phone'];

// SQL 업데이트
$sql = "UPDATE USERS 
        SET NAME = :name, 
            NICKNAME = :nickname, 
            EMAIL = :email, 
            BIRTHDATE = TO_DATE(:birthdate, 'YYYY-MM-DD'), 
            PHONE = :phone
        WHERE ID = :id";

$stid = oci_parse($connect, $sql);

// 바인드 변수
oci_bind_by_name($stid, ":name", $name);
oci_bind_by_name($stid, ":nickname", $nickname);
oci_bind_by_name($stid, ":email", $email);
oci_bind_by_name($stid, ":birthdate", $birthdate);
oci_bind_by_name($stid, ":phone", $phone);
oci_bind_by_name($stid, ":id", $user_id);

// 실행
if (oci_execute($stid)) {
    echo "<script>alert('정보가 성공적으로 업데이트되었습니다.'); window.location.href='mypage.php';</script>";
} else {
    $e = oci_error($stid);
    echo "<script>alert('업데이트에 실패했습니다: " . htmlentities($e['message'], ENT_QUOTES) . "'); history.back();</script>";
}

// 리소스 해제
oci_free_statement($stid);
oci_close($connect);
?>