<?php
session_start();
include 'process_reaction.php'; // Reaction 관련 함수 포함

if (!isset($_SESSION['username'])) {
    echo "<script>alert('로그인이 필요합니다.'); history.back();</script>";
    exit;
}

$contentID = $_POST['content_id'] ?? null;
$reactionType = $_POST['reaction_type'] ?? null;
$userID = $_SESSION['username'];

if (!$contentID || !$reactionType) {
    echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
    exit;
}

// 기존 리액션 확인
$sqlCheckReaction = "
    SELECT * FROM Reaction WHERE Content_ID = :contentID AND Reaction_Type = :reactionType AND UserID = :userID
";
$stid = oci_parse($conn, $sqlCheckReaction);
oci_bind_by_name($stid, ':contentID', $contentID);
oci_bind_by_name($stid, ':reactionType', $reactionType);
oci_bind_by_name($stid, ':userID', $userID);
oci_execute($stid);

if (oci_fetch_assoc($stid)) {
    // 이미 존재하는 리액션 제거
    $sql = "DELETE FROM Reaction WHERE Content_ID = :contentID AND Reaction_Type = :reactionType AND UserID = :userID";
} else {
    // 리액션 추가
    $sql = "INSERT INTO Reaction (Content_ID, Reaction_Type, UserID) VALUES (:contentID, :reactionType, :userID)";
}

$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ':contentID', $contentID);
oci_bind_by_name($stid, ':reactionType', $reactionType);
oci_bind_by_name($stid, ':userID', $userID);
oci_execute($stid);

// 완료 후 이전 페이지로 리다이렉트
header("Location: content_review.php?content_id={$contentID}");
exit;
?>
