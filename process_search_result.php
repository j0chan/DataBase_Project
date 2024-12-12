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

// 검색어 처리
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$results = [];

if (!empty($searchTerm)) {
    // 검색 쿼리 작성
    $sqlSearch = "
        SELECT 
            c.Content_ID, 
            c.Title, 
            c.Poster_Path, 
            NVL(ROUND(AVG(r.Rating), 2), 0) AS Average_Rating
        FROM 
            Content c
        LEFT JOIN
            Review r ON c.Content_ID = r.Content_ID
        WHERE 
            LOWER(c.Title) LIKE '%' || LOWER(:searchTerm) || '%'
        GROUP BY 
            c.Content_ID, c.Title, c.Poster_Path
        ORDER BY 
            Average_Rating DESC
    ";

    $stidSearch = oci_parse($conn, $sqlSearch);
    oci_bind_by_name($stidSearch, ":searchTerm", $searchTerm);
    oci_execute($stidSearch);

    // 결과 저장
    while ($row = oci_fetch_assoc($stidSearch)) {
        $results[] = [
            'content_id' => $row['CONTENT_ID'],
            'title' => $row['TITLE'],
            'poster_path' => $row['POSTER_PATH'],
            'average_rating' => $row['AVERAGE_RATING']
        ];
    }
    oci_free_statement($stidSearch);
}

// DB 연결 닫기
oci_close($conn);

// 검색 결과 반환
return $results;
?>
