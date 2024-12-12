set_time_limit(10);
<?php
// DB 접속 정보 설정
$db  = '(DESCRIPTION =
         (ADDRESS_LIST =
          (ADDRESS = (PROTOCOL = TCP)(HOST = 203.249.97.58)(PORT = 20022))
         )
         (CONNECT_DATA =
          (SID = orcl)
         )
        )';

// ID AND PASSWORD
$username = 'DB502_PROJ_G3';
$password = '1234';

// DB CONNECT TRY
$connect = oci_connect($username, $password, $db);

// if error
if (!$connect) {
    $e = oci_error();
    die("Database connection failed: " . htmlentities($e['message'], ENT_QUOTES));
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP-DB 연동</title>
</head>
<body>
    <h1>DB 연동 결과</h1>
    <?php
    // Debugging 활성화
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // SQL query
    $sql = "SELECT * FROM USERS WHERE ROWNUM <= 10"; // 제한 추가

    // SQL 구문 파싱
    $stid = oci_parse($connect, $sql);
    if (!$stid) {
        $e = oci_error($connect);
        die("SQL parse failed: " . htmlentities($e['message'], ENT_QUOTES));
    }

    // SQL 실행
    if (!oci_execute($stid)) {
        $e = oci_error($stid);
        die("SQL execution failed: " . htmlentities($e['message'], ENT_QUOTES));
    }

    // HTML 테이블 출력
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>";

    // 컬럼명 출력
    $num_fields = oci_num_fields($stid);
    for ($i = 1; $i <= $num_fields; $i++) {
        echo "<th>" . htmlentities(oci_field_name($stid, $i), ENT_QUOTES) . "</th>";
    }
    echo "</tr>";

    // 데이터 출력
    while (($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) !== false) {
        echo "<tr>";
        foreach ($row as $item) {
            echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";

    // 리소스 정리
    oci_free_statement($stid);
    oci_close($connect);
    ?>
</body>
</html>

