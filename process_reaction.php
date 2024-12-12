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

function getReactionCountByType($conn, $contentID, $reactionType) {
    // SQL 쿼리 작성
    $sql = 'SELECT COUNT(*) AS REACTION_COUNT 
            FROM Reaction 
            WHERE Content_ID = :content_id 
              AND Reaction_Type = :reaction_type';

    // SQL 준비
    $stid = oci_parse($conn, $sql);
    if (!$stid) {
        $e = oci_error($conn);
        die("SQL 준비 중 오류 발생: " . htmlentities($e['message']));
    }

    // 변수 바인딩
    oci_bind_by_name($stid, ':content_id', $contentID, -1, SQLT_CHR);
    oci_bind_by_name($stid, ':reaction_type', $reactionType, -1, SQLT_CHR);

    // SQL 실행
    if (!oci_execute($stid)) {
        $e = oci_error($stid);
        die("SQL 실행 중 오류 발생: " . htmlentities($e['message']));
    }

    // 결과 가져오기
    $reactionCount = 0;
    if ($row = oci_fetch_assoc($stid)) {
        $reactionCount = $row['REACTION_COUNT'];
    }

    // 리소스 해제
    oci_free_statement($stid);

    return $reactionCount; // 특정 리액션 타입의 카운트 반환
}
?>