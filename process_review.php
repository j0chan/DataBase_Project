<?php
// 데이터베이스 연결 설정
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
    die("Database Connection Error: " . htmlentities($e['message'], ENT_QUOTES));
}

/**
 * 특정 Content_ID의 콘텐츠 데이터를 가져오는 함수
 */
function getContentData($conn, $contentID) {
    // SQL 쿼리 작성 (큰따옴표 없이 대소문자 구분하지 않음)
    $sql = 'SELECT Content_ID, Content_Type, Title, Director, Author, 
                   TO_CHAR(ReleaseDate, \'YYYY-MM-DD\') AS ReleaseDate, 
                   Duration, POSTER_PATH, ACTORS, GENRE
            FROM Content
            WHERE CONTENT_ID = :content_id';

    // SQL 준비
    $stid = oci_parse($conn, $sql);
    if (!$stid) {
        $e = oci_error($conn);
        die("SQL 준비 중 오류 발생: " . htmlentities($e['message']));
    }

    // 변수 바인딩
    oci_bind_by_name($stid, ":content_id", $contentID, -1, SQLT_CHR);

    // SQL 실행
    if (!oci_execute($stid)) {
        $e = oci_error($stid);
        die("SQL 실행 중 오류 발생: " . htmlentities($e['message']));
    }

    // 결과 가져오기
    $contentData = oci_fetch_assoc($stid);
    oci_free_statement($stid);

    if (!$contentData) {
        die("ERROR: Content ID에 해당하는 데이터가 없습니다. Content ID: " . htmlspecialchars($contentID));
    }

    return $contentData;
}


function getAverageRating($conn, $contentID) {
    $sql = "SELECT AVG(Rating) AS average_rating
            FROM Review
            WHERE Content_ID = :content_id";

    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":content_id", $contentID);

    if (!oci_execute($stid)) {
        $e = oci_error($stid);
        die("SQL Error: " . htmlentities($e['message'], ENT_QUOTES));
    }

    $row = oci_fetch_assoc($stid);
    oci_free_statement($stid);

    return $row['AVERAGE_RATING'] ? round($row['AVERAGE_RATING'], 2) : null; // 소수점 둘째 자리까지 반올림
}

function getUserNickname($conn, $userID) {
    $sql = "SELECT Nickname FROM Users WHERE ID = :user_id";
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":user_id", $userID);
    oci_execute($stid);

    $nickname = null;
    if ($row = oci_fetch_assoc($stid)) {
        $nickname = $row['NICKNAME'];
    }

    oci_free_statement($stid);
    return $nickname;
}

function getReviewsWithNickname($conn, $contentID) {
    $sql = "SELECT r.REVIEW_ID, r.USERID, r.RATING, r.CONTENT, r.CREATEDDATE 
            FROM Review r
            WHERE r.CONTENT_ID = :content_id
            ORDER BY r.CREATEDDATE DESC";

    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":content_id", $contentID);
    oci_execute($stid);

    $reviewsWithNickname = [];
    while ($row = oci_fetch_assoc($stid)) {
        // 유저 닉네임 가져오기
        $row['NICKNAME'] = getUserNickname($conn, $row['USERID']);
        $reviewsWithNickname[] = $row;
    }

    oci_free_statement($stid);

    return $reviewsWithNickname; // 리뷰 데이터에 닉네임 포함
}


/**
 * 특정 Review_ID의 좋아요 수를 가져오는 함수
 */
function getLikeCount($conn, $reviewId) {
    $sql = 'SELECT COUNT(*) AS LIKE_COUNT FROM Review_Likes WHERE Review_ID = :review_id';
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ':review_id', $reviewId);
    oci_execute($stid);

    $likeCount = 0;
    if ($row = oci_fetch_assoc($stid)) {
        $likeCount = $row['LIKE_COUNT'];
    }
    oci_free_statement($stid);

    return $likeCount;
}
?>
