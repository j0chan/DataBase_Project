<?php
// 데이터베이스 연결
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

// 특정 콘텐츠 타입(영화) 데이터 가져오기
$contentType = 'Movie'; // 영화 리스트라면 'Movie'로 고정
$sql = "SELECT Content_ID, Title, Poster_Path, Author, Director, GENRE, ACTORS, Duration 
        FROM Content 
        WHERE Content_Type = :content_type";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ':content_type', $contentType);

if (!oci_execute($stid)) {
    $e = oci_error($stid);
    die("Failed to execute SQL: " . $e['message']);
}

// 결과 데이터를 배열로 저장
$contents = [];
while ($row = oci_fetch_assoc($stid)) {
    $contents[] = [
        'content_id' => $row['CONTENT_ID'],
        'title' => $row['TITLE'],
        'poster_path' => $row['POSTER_PATH'],
        'author' => $row['AUTHOR'],
        'director' => $row['DIRECTOR'],
        'genre' => $row['GENRE'],
        'actors' => $row['ACTORS'],
        'duration' => $row['DURATION']
    ];
}

oci_free_statement($stid);
oci_close($conn);
?>
