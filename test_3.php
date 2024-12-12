<?php
// Oracle DB 연결 설정
$db = '(DESCRIPTION =
    (ADDRESS_LIST=
        (ADDRESS = (PROTOCOL = TCP)(HOST = 203.249.87.57)(PORT = 1521))
    )
    (CONNECT_DATA = (SID = orcl))
)';
$username = "DB502_PROJ_G3";
$password = "1234";

// Oracle DB 연결
$connect = oci_connect($username, $password, $db, 'AL32UTF8');
if (!$connect) {
    $e = oci_error();
    die("Database connection failed: " . htmlentities($e['message'], ENT_QUOTES));
}

// 테스트용 영화 ID
$movieId = 'M001';

// 영화 정보 가져오기
$sql = "SELECT Movie_ID, Director, Actors, Title, 
               TO_CHAR(ReleaseDate, 'YYYY-MM-DD') AS ReleaseDate,
               Duration, Genre, Poster_Path 
        FROM Movie 
        WHERE Movie_ID = :movieId";

$stid = oci_parse($connect, $sql);
oci_bind_by_name($stid, ":movieId", $movieId);
oci_execute($stid);

$movie = [];
while ($row = oci_fetch_assoc($stid)) {
    // CLOB 필드 직접 읽기
    $actorsClob = $row['ACTORS'];
    $genreClob = $row['GENRE'];

    if (is_a($actorsClob, 'OCI-Lob')) {
        $row['ACTORS'] = $actorsClob->load();
    }
    if (is_a($genreClob, 'OCI-Lob')) {
        $row['GENRE'] = $genreClob->load();
    }

    $movie = $row; // 단일 행만 필요
}

oci_free_statement($stid);
oci_close($connect);

// 출력
if (!$movie) {
    echo "<p>영화 정보를 찾을 수 없습니다.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['TITLE']); ?></title>
</head>
<body>
    <h1>영화 정보</h1>
    <p><strong>제목:</strong> <?php echo htmlspecialchars($movie['TITLE']); ?></p>
    <p><strong>감독:</strong> <?php echo htmlspecialchars($movie['DIRECTOR']); ?></p>
    <p><strong>출연:</strong> <?php echo htmlspecialchars($movie['ACTORS']); ?></p>
    <p><strong>개봉일:</strong> <?php echo htmlspecialchars($movie['RELEASEDATE']); ?></p>
    <p><strong>러닝타임:</strong> <?php echo htmlspecialchars($movie['DURATION']); ?>분</p>
    <p><strong>장르:</strong> <?php echo htmlspecialchars($movie['GENRE']); ?></p>
    <p><img src="<?php echo htmlspecialchars($movie['POSTER_PATH']); ?>" alt="포스터" style="width:200px;"></p>
</body>
</html>