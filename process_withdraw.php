<?php
session_start();

// Oracle DB 연결 설정
$db = '(DESCRIPTION =
    (ADDRESS_LIST =
        (ADDRESS = (PROTOCOL = TCP)(HOST = 203.249.87.57)(PORT = 1521))
    )
    (CONNECT_DATA = (SID = orcl))
)';
$username = "DB502_PROJ_G3";
$password = "1234";

// Oracle DB 연결
$conn = oci_connect($username, $password, $db, 'AL32UTF8');
if (!$conn) {
    $e = oci_error();
    die("Database connection failed: " . htmlentities($e['message'], ENT_QUOTES));
}

// 사용자 세션 확인
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
$session_user_id = $_SESSION['username'];

// POST로 전달된 사용자 ID 확인
if (!isset($_POST['user_id'])) {
    echo "<script>alert('잘못된 요청입니다.'); window.location.href='main.php';</script>";
    exit;
}
$user_id = $_POST['user_id'];

// 세션 사용자와 요청 사용자 확인
if ($session_user_id !== $user_id) {
    echo "<script>alert('권한이 없습니다.'); window.location.href='main.php';</script>";
    exit;
}

try {
    // 트랜잭션 시작
    oci_execute(oci_parse($conn, 'BEGIN'));

    // 1. Review_Likes 데이터 삭제
    $deleteLikesSql = "DELETE FROM Review_Likes WHERE Review_ID IN (SELECT Review_ID FROM Review WHERE UserID = :user_id)";
    $deleteLikesStid = oci_parse($conn, $deleteLikesSql);
    oci_bind_by_name($deleteLikesStid, ":user_id", $user_id);
    if (!oci_execute($deleteLikesStid, OCI_NO_AUTO_COMMIT)) {
        $e = oci_error($deleteLikesStid);
        throw new Exception("리뷰 좋아요 삭제 오류: " . $e['message']);
    }
    oci_free_statement($deleteLikesStid);

    // 2. Review 데이터 삭제
    $deleteReviewsSql = "DELETE FROM Review WHERE UserID = :user_id";
    $deleteReviewsStid = oci_parse($conn, $deleteReviewsSql);
    oci_bind_by_name($deleteReviewsStid, ":user_id", $user_id);
    if (!oci_execute($deleteReviewsStid, OCI_NO_AUTO_COMMIT)) {
        $e = oci_error($deleteReviewsStid);
        throw new Exception("리뷰 삭제 오류: " . $e['message']);
    }
    oci_free_statement($deleteReviewsStid);

    // 3. USERS 데이터 삭제
    $deleteUserSql = "DELETE FROM USERS WHERE ID = :user_id";
    $deleteUserStid = oci_parse($conn, $deleteUserSql);
    oci_bind_by_name($deleteUserStid, ":user_id", $user_id);
    if (!oci_execute($deleteUserStid, OCI_NO_AUTO_COMMIT)) {
        $e = oci_error($deleteUserStid);
        throw new Exception("회원 삭제 오류: " . $e['message']);
    }
    oci_free_statement($deleteUserStid);

    // 트랜잭션 커밋
    oci_commit($conn);

    // 세션 종료
    session_unset();
    session_destroy();

    echo "<script>alert('회원탈퇴가 완료되었습니다.'); window.location.href='main.php';</script>";
} catch (Exception $e) {
    // 오류 발생 시 롤백
    oci_rollback($conn);
    echo "<script>alert('탈퇴 중 오류 발생: " . htmlspecialchars($e->getMessage()) . "'); window.location.href='mypage.php';</script>";
} finally {
    oci_close($conn);
}
?>
