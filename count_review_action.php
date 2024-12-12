<?php
session_start(); // 세션 시작 (항상 최상단)

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
    echo "<script>
            alert('로그인이 필요한 기능입니다.');
            window.history.back(); // 이전 페이지로 돌아가기
          </script>";
    exit; // PHP 코드 실행 중단
}

$loggedInUserName = $_SESSION['username']; // 세션에서 username 가져오기

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $reviewId = $_POST['review_id'];

    if ($action === 'like') {
        // 좋아요 처리
        $sqlCheck = "SELECT * FROM Review_Likes WHERE Review_ID = :review_id AND UserID = :user_id";
        $stidCheck = oci_parse($conn, $sqlCheck);
        oci_bind_by_name($stidCheck, ':review_id', $reviewId);
        oci_bind_by_name($stidCheck, ':user_id', $loggedInUserName);
        oci_execute($stidCheck);

        if (oci_fetch($stidCheck)) {
            $sqlDelete = "DELETE FROM Review_Likes WHERE Review_ID = :review_id AND UserID = :user_id";
            $stidDelete = oci_parse($conn, $sqlDelete);
            oci_bind_by_name($stidDelete, ':review_id', $reviewId);
            oci_bind_by_name($stidDelete, ':user_id', $loggedInUserName);
            oci_execute($stidDelete);
        } else {
            $sqlInsert = "INSERT INTO Review_Likes (Review_ID, UserID) VALUES (:review_id, :user_id)";
            $stidInsert = oci_parse($conn, $sqlInsert);
            oci_bind_by_name($stidInsert, ':review_id', $reviewId);
            oci_bind_by_name($stidInsert, ':user_id', $loggedInUserName);
            oci_execute($stidInsert);
        }
        oci_free_statement($stidCheck);

        // 이전 페이지로 리다이렉트
        echo "<script>
                alert('좋아요 상태가 변경되었습니다.');
                window.history.back(); // 이전 페이지로 돌아가기
              </script>";
        exit;
    } else if ($action === 'delete') {
        // 리뷰 작성자 확인
        $sqlCheckReviewOwner = "SELECT UserID FROM Review WHERE Review_ID = :review_id";
        $stidCheckReviewOwner = oci_parse($conn, $sqlCheckReviewOwner);
        oci_bind_by_name($stidCheckReviewOwner, ':review_id', $reviewId);
        oci_execute($stidCheckReviewOwner);

        $reviewOwner = null;
        if ($row = oci_fetch_assoc($stidCheckReviewOwner)) {
            $reviewOwner = $row['USERID']; // 리뷰 작성자 ID 가져오기
        }
        oci_free_statement($stidCheckReviewOwner);

        if ($reviewOwner !== $loggedInUserName) {
            echo "<script>
                    alert('해당 리뷰를 삭제할 권한이 없습니다.');
                    window.history.back(); // 이전 페이지로 돌아가기
                  </script>";
            exit;
        }

        // 리뷰에 대한 좋아요 데이터 삭제
        $sqlDeleteLikes = "DELETE FROM Review_Likes WHERE Review_ID = :review_id";
        $stidDeleteLikes = oci_parse($conn, $sqlDeleteLikes);
        oci_bind_by_name($stidDeleteLikes, ':review_id', $reviewId);
        oci_execute($stidDeleteLikes);
        oci_free_statement($stidDeleteLikes);

        // 리뷰 삭제
        $sqlDeleteReview = "DELETE FROM Review WHERE Review_ID = :review_id AND UserID = :user_id";
        $stidDeleteReview = oci_parse($conn, $sqlDeleteReview);
        oci_bind_by_name($stidDeleteReview, ':review_id', $reviewId);
        oci_bind_by_name($stidDeleteReview, ':user_id', $loggedInUserName);
        oci_execute($stidDeleteReview);
        oci_free_statement($stidDeleteReview);

        echo "<script>
                alert('리뷰가 성공적으로 삭제되었습니다.');
                window.history.back(); // 이전 페이지로 돌아가기
              </script>";
        exit;
    }
}

oci_close($conn);
?>
