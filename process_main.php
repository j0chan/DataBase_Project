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

// 영화 데이터 가져오기 (리뷰 수 기준 상위 5개)
$sqlMovies = "
    SELECT * FROM (
        SELECT 
            c.Content_ID, 
            c.Title, 
            c.Poster_Path, 
            NVL(ROUND(AVG(r.Rating), 2), 0) AS Average_Rating,
            COUNT(r.Review_ID) AS Review_Count
        FROM 
            Content c
        LEFT JOIN 
            Review r ON c.Content_ID = r.Content_ID
        WHERE 
            c.Content_Type = 'Movie'
        GROUP BY 
            c.Content_ID, c.Title, c.Poster_Path
        ORDER BY 
            Review_Count DESC
    )
    WHERE ROWNUM <= 5
";

$stidMovies = oci_parse($conn, $sqlMovies);
oci_execute($stidMovies);

$movies = [];
while ($row = oci_fetch_assoc($stidMovies)) {
    $movies[] = [
        'content_id' => $row['CONTENT_ID'],
        'title' => $row['TITLE'],
        'poster_path' => $row['POSTER_PATH'],
        'average_rating' => $row['AVERAGE_RATING']
    ];
}
oci_free_statement($stidMovies);

// 드라마 데이터 가져오기 (리뷰 수 기준 상위 5개)
$sqlDramas = "
    SELECT * FROM (
        SELECT 
            c.Content_ID, 
            c.Title, 
            c.Poster_Path, 
            NVL(ROUND(AVG(r.Rating), 2), 0) AS Average_Rating,
            COUNT(r.Review_ID) AS Review_Count
        FROM 
            Content c
        LEFT JOIN 
            Review r ON c.Content_ID = r.Content_ID
        WHERE 
            c.Content_Type = 'Drama'
        GROUP BY 
            c.Content_ID, c.Title, c.Poster_Path
        ORDER BY 
            Review_Count DESC
    )
    WHERE ROWNUM <= 5
";

$stidDramas = oci_parse($conn, $sqlDramas);
oci_execute($stidDramas);
$dramas = [];
while ($row = oci_fetch_assoc($stidDramas)) {
    $dramas[] = [
        'content_id' => $row['CONTENT_ID'],
        'title' => $row['TITLE'],
        'poster_path' => $row['POSTER_PATH'],
        'average_rating' => $row['AVERAGE_RATING']
    ];
}
oci_free_statement($stidDramas);

// 책 데이터 가져오기 (리뷰 수 기준 상위 5개)
$sqlBooks = "
    SELECT * FROM (
        SELECT 
            c.Content_ID, 
            c.Title, 
            c.Poster_Path, 
            NVL(ROUND(AVG(r.Rating), 2), 0) AS Average_Rating,
            COUNT(r.Review_ID) AS Review_Count
        FROM 
            Content c
        LEFT JOIN 
            Review r ON c.Content_ID = r.Content_ID
        WHERE 
            c.Content_Type = 'Book'
        GROUP BY 
            c.Content_ID, c.Title, c.Poster_Path
        ORDER BY 
            Review_Count DESC
    )
    WHERE ROWNUM <= 5
";

$stidBooks = oci_parse($conn, $sqlBooks);
oci_execute($stidBooks);
$books = [];
while ($row = oci_fetch_assoc($stidBooks)) {
    $books[] = [
        'content_id' => $row['CONTENT_ID'],
        'title' => $row['TITLE'],
        'poster_path' => $row['POSTER_PATH'],
        'average_rating' => $row['AVERAGE_RATING']
    ];
}
oci_free_statement($stidBooks);

// 웹툰 데이터 가져오기 (리뷰 수 기준 상위 5개)
$sqlWebtoons = "
    SELECT * FROM (
        SELECT 
            c.Content_ID, 
            c.Title, 
            c.Poster_Path, 
            NVL(ROUND(AVG(r.Rating), 2), 0) AS Average_Rating,
            COUNT(r.Review_ID) AS Review_Count
        FROM 
            Content c
        LEFT JOIN 
            Review r ON c.Content_ID = r.Content_ID
        WHERE 
            c.Content_Type = 'Webtoon'
        GROUP BY 
            c.Content_ID, c.Title, c.Poster_Path
        ORDER BY 
            Review_Count DESC
    )
    WHERE ROWNUM <= 5
";

$stidWebtoons = oci_parse($conn, $sqlWebtoons);
oci_execute($stidWebtoons);
$webtoons = [];
while ($row = oci_fetch_assoc($stidWebtoons)) {
    $webtoons[] = [
        'content_id' => $row['CONTENT_ID'],
        'title' => $row['TITLE'],
        'poster_path' => $row['POSTER_PATH'],
        'average_rating' => $row['AVERAGE_RATING']
    ];
}
oci_free_statement($stidWebtoons);

// 연결 닫기
oci_close($conn);
?>
