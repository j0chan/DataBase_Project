<?php
// 데이터베이스 연결 정보 설정
$db = '(DESCRIPTION =
    (ADDRESS_LIST =
        (ADDRESS = (PROTOCOL = TCP)(HOST = 203.249.87.57)(PORT = 1521))
    )
    (CONNECT_DATA =
        (SID = orcl)
    )
)';
$username = "DB502_PROJ_G3";
$password = "1234";

// Oracle DB 연결
$conn = oci_connect($username, $password, $db, 'AL32UTF8');
if (!$conn) {
    $e = oci_error();
    die("Failed to connect to Oracle: " . $e['message']);
}

// 극장 데이터 가져오기
$sql = "SELECT Theater_ID, Name, Address, Contact_Number, Features, Theater_Path
        FROM Theater";
$stid = oci_parse($conn, $sql);

$r = oci_execute($stid); // SQL 실행
if (!$r) {
    $e = oci_error($stid);
    die("Failed to execute SQL: " . $e['message']);
}

// 결과 데이터를 배열로 저장
$theaters = [];
while ($row = oci_fetch_assoc($stid)) {
    $theaters[] = [
        'theater_id' => $row['THEATER_ID'],
        'name' => $row['NAME'],
        'address' => $row['ADDRESS'],
        'contact_number' => $row['CONTACT_NUMBER'],
        'features' => $row['FEATURES'],
        'theater_path' => $row['THEATER_PATH']
    ];
}

oci_free_statement($stid);
oci_close($conn);
?>
