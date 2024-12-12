<?php
// 에러 표시 활성화
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Oracle 데이터베이스 연결 설정
$db_host = "203.249.87.57"; // 호스트 주소
$db_port = "1521"; // 포트
$db_sid = "orcl"; // SID
$db_username = "DB502_PROJ_G3"; // 사용자명
$db_password = "1234"; // 비밀번호

// Oracle 데이터베이스 연결 문자열 생성
$conn_str = "(DESCRIPTION=
    (ADDRESS_LIST=
        (ADDRESS=(PROTOCOL=TCP)(HOST=$db_host)(PORT=$db_port))
    )
    (CONNECT_DATA=(SID=$db_sid))
)";

// 데이터베이스 연결 시도
$conn = oci_connect($db_username, $db_password, $conn_str);
if (!$conn) {
    $e = oci_error();
    die("Database connection failed: " . htmlentities($e['message'], ENT_QUOTES));
}

// POST 데이터 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'], $_POST['password'], $_POST['name'], $_POST['nickname'], $_POST['email'], $_POST['birthdate'], $_POST['phone'])) {
        die("Missing required form fields.");
    }

    // 폼 데이터 가져오기
    $id = $_POST['id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // 비밀번호 암호화
    $name = $_POST['name'];
    $nickname = $_POST['nickname'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $phone = $_POST['phone'];

    // ID 중복 확인
    $check_sql_id = "SELECT COUNT(*) AS ID_COUNT FROM Users WHERE ID = :id";
    $check_stmt_id = oci_parse($conn, $check_sql_id);

    if (!$check_stmt_id) {
        $e = oci_error($conn);
        die("SQL Prepare failed: " . htmlentities($e['message'], ENT_QUOTES));
    }

    oci_bind_by_name($check_stmt_id, ":id", $id);
    oci_execute($check_stmt_id);
    $row_id = oci_fetch_assoc($check_stmt_id);

    if ($row_id['ID_COUNT'] > 0) {
        echo "<script>
                alert('The ID is already in use. Please choose another one.');
                window.location.href = 'signup.php';
              </script>";
        exit;
    }

    // 이메일 중복 확인
    $check_sql_email = "SELECT COUNT(*) AS EMAIL_COUNT FROM Users WHERE Email = :email";
    $check_stmt_email = oci_parse($conn, $check_sql_email);

    if (!$check_stmt_email) {
        $e = oci_error($conn);
        die("SQL Prepare failed: " . htmlentities($e['message'], ENT_QUOTES));
    }

    oci_bind_by_name($check_stmt_email, ":email", $email);
    oci_execute($check_stmt_email);
    $row_email = oci_fetch_assoc($check_stmt_email);

    if ($row_email['EMAIL_COUNT'] > 0) {
        echo "<script>
                alert('The email is already in use. Please use another email.');
                window.location.href = 'signup.php';
              </script>";
        exit;
    }

    // 전화번호 중복 확인
    $check_sql_phone = "SELECT COUNT(*) AS PHONE_COUNT FROM Users WHERE Phone = :phone";
    $check_stmt_phone = oci_parse($conn, $check_sql_phone);

    if (!$check_stmt_phone) {
        $e = oci_error($conn);
        die("SQL Prepare failed: " . htmlentities($e['message'], ENT_QUOTES));
    }

    oci_bind_by_name($check_stmt_phone, ":phone", $phone);
    oci_execute($check_stmt_phone);
    $row_phone = oci_fetch_assoc($check_stmt_phone);

    if ($row_phone['PHONE_COUNT'] > 0) {
        echo "<script>
                alert('The phone number is already registered. Please use another phone number.');
                window.location.href = 'signup.php';
              </script>";
        exit;
    }

    // 데이터베이스에 데이터 삽입
    $sql = "INSERT INTO Users (ID, Password, UserLevel, Name, Nickname, Email, Birthdate, Phone) 
            VALUES (:id, :password, '1', :name, :nickname, :email, TO_DATE(:birthdate, 'YYYY-MM-DD'), :phone)";
    $stmt = oci_parse($conn, $sql);

    if (!$stmt) {
        $e = oci_error($conn);
        die("SQL Prepare failed: " . htmlentities($e['message'], ENT_QUOTES));
    }

    // 바인드 변수 설정
    oci_bind_by_name($stmt, ":id", $id);
    oci_bind_by_name($stmt, ":password", $password);
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":nickname", $nickname);
    oci_bind_by_name($stmt, ":email", $email);
    oci_bind_by_name($stmt, ":birthdate", $birthdate);
    oci_bind_by_name($stmt, ":phone", $phone);

    // SQL 실행 및 오류 처리
    try {
        $result = oci_execute($stmt);
        if ($result) {
            echo "<script>
                    alert('Sign up successful!');
                    window.location.href = 'login.php';
                  </script>";
        }
    } catch (Exception $e) {
        $error = oci_error($stmt);
        if (strpos($error['message'], 'ORA-00001') !== false) {
            echo "<script>
                    alert('Duplicate entry detected (Email or Phone). Please try again.');
                    window.location.href = 'signup.php';
                  </script>";
        } else {
            die("SQL Error: " . htmlentities($error['message'], ENT_QUOTES));
        }
    }

    // 연결 해제
    oci_free_statement($stmt);
    oci_close($conn);
}
?>
