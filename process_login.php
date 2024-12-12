<?php
session_start();

// Oracle DB 연결 설정
$db = '(DESCRIPTION = 	
    (ADDRESS_LIST=
        (ADDRESS = (PROTOCOL = TCP)(HOST = 203.249.87.57)(PORT = 1521))
    )
    (CONNECT_DATA = (SID = orcl))
)';
$username = "DB502_PROJ_G3"; // Oracle DB 사용자명
$password = "1234"; // Oracle DB 비밀번호

// Oracle DB 연결
$connect = oci_connect($username, $password, $db);
if (!$connect) {
    $e = oci_error();
    die("Database connection failed: " . htmlentities($e['message'], ENT_QUOTES));
}

// 폼에서 입력받은 데이터 가져오기
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['username']; // 폼에서 입력한 사용자 ID
    $password = $_POST['password']; // 폼에서 입력한 비밀번호

    // SQL SELECT 쿼리 (바인드 변수 사용)
    $sql = "SELECT ID, Password FROM Users WHERE ID = :id";
    $stid = oci_parse($connect, $sql);

    // 바인드 변수 설정
    oci_bind_by_name($stid, ":id", $id);

    // SQL 실행
    oci_execute($stid);

    // 결과 가져오기
    $row = oci_fetch_assoc($stid);

    if ($row) {
        // 비밀번호 검증
        if (password_verify($password, $row['PASSWORD'])) {
            $_SESSION['username'] = $id; // 세션에 사용자 ID 저장
            header("Location: main.php"); // main.php로 리다이렉트
            exit;
        } else {
            echo "<script>alert('Invalid password.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid username.'); window.location.href='login.php';</script>";
    }

    // 결과 및 연결 해제
    oci_free_statement($stid);
    oci_close($connect);
}
?>
