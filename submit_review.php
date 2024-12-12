<?php
session_start(); // 세션 시작

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

// 로그인된 유저 확인
if (!isset($_SESSION['username'])) {
    die("로그인 정보가 없습니다. 로그인 후 다시 시도하세요."); // 로그인 여부 확인
}

$loggedInUserName = $_SESSION['username']; // 세션에서 user_name 가져오기

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentId = $_POST['content_id']; // `Content_ID`로 변경
    $rating = $_POST['rating'];
    $content = $_POST['content'];

    // 데이터 검증
    if (empty($contentId) || empty($rating) || empty($content)) {
        die("필수 데이터가 누락되었습니다.");
    }

    // 데이터베이스에 데이터 삽입
    $sql = "INSERT INTO Review (Review_ID, UserID, Content_ID, Rating, Content, CreatedDate) 
            VALUES (review_seq.NEXTVAL, :username, :content_id, :rating, :content, SYSDATE)";
    
    $stid = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ':username', $loggedInUserName); // 로그인된 유저의 이름 사용
    oci_bind_by_name($stid, ':content_id', $contentId); // `Content_ID` 사용
    oci_bind_by_name($stid, ':rating', $rating);
    oci_bind_by_name($stid, ':content', $content);

    if (oci_execute($stid, OCI_COMMIT_ON_SUCCESS)) {
        oci_free_statement($stid);
        oci_close($conn);

        // 리다이렉트 URL 설정
        $redirectUrl = "content_review.php?content_id=" . urlencode($contentId); // 리다이렉트 URL 수정
        header("Location: " . $redirectUrl);
        exit;
    } else {
        $e = oci_error($stid);
        die("SQL Error: " . htmlentities($e['message']));
    }
}
?>
