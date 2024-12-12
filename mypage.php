<?php
session_start();

// Oracle DB 연결 설정
$db = '(DESCRIPTION = 	
    (ADDRESS_LIST=
        (ADDRESS = (PROTOCOL = TCP)(HOST = 203.249.87.57)(PORT = 1521))
    )
    (CONNECT_DATA = (SID = orcl))
)';
$username = "DB502_PROJ_G3"; // Oracle DB 사용자명
$password = "1234"; // Oracle DB 비밀번호

// Oracle DB 연결
$connect = oci_connect($username, $password, $db);
if (!$connect) {
    $e = oci_error();
    die("Database connection failed: " . htmlentities($e['message'], ENT_QUOTES));
}

// 세션에서 사용자 ID 가져오기
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['username'];

// DB에서 사용자 정보 가져오기
$sql = "SELECT ID, USERLEVEL, NAME, NICKNAME, EMAIL, BIRTHDATE, PHONE FROM USERS WHERE ID = :id";
$stid = oci_parse($connect, $sql);
oci_bind_by_name($stid, ":id", $user_id);
oci_execute($stid);
$user = oci_fetch_assoc($stid);

if (!$user) {
    echo "<script>alert('User not found.'); window.location.href='main.php';</script>";
    exit;
}

oci_free_statement($stid);
oci_close($connect);
?>

<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Page</title>
        <style>
            /* 공통 스타일 */
            body {
                font-family: 'Arial', sans-serif;
                margin: 0;
                padding: 0;
                background-color: #1a1a1a;
                color: #fff;
            }

            a {
                text-decoration: none;
                color: inherit;
            }
            
            /* 헤더 */
            .main-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: #000;
                padding: 20px 40px;
                color: #fff;
            }

            .header-left {
                display: flex;
                align-items: center;
                gap: 20px;
            }

            .header-title-button {
                font-size: 24px;
                font-weight: bold;
                color: #e50914;
                background-color: transparent;
                border: none;
                cursor: pointer;
                padding: 0;
            }

            .header-title-button:hover {
                color: #fff;
                text-shadow: 0 0 5px #e50914;
            }

            .main-nav ul {
                list-style: none;
                display: flex;
                gap: 20px;
                margin: 0;
                padding: 0;
            }

            .main-nav a {
                font-size: 14px;
                color: #fff;
                text-decoration: none;
                font-weight: bold;
            }

            .main-nav a:hover {
                color: #e50914;
            }

            /* 헤더 오른쪽 정렬 */
            .header-right {
                display: flex;
                align-items: center;
                gap: 20px;
            }

            /* 검색창 스타일 */
            .search-box {
                display: flex;
                align-items: center;
                border: 1px solid #444;
                border-radius: 5px;
                overflow: hidden;
            }

            .search-box input {
                padding: 5px 10px;
                border: none;
                outline: none;
                background-color: #222;
                color: #fff;
                font-size: 14px;
            }

            .search-box button {
                background-color: #e50914;
                color: #fff;
                border: none;
                padding: 5px 10px;
                cursor: pointer;
                font-size: 14px;
                font-weight: bold;
                transition: background-color 0.3s ease;
            }

            .search-box button:hover {
                background-color: #d40813;
            }

            /* 사용자 버튼 (My Page, Log In) 스타일 */
            .user-buttons a {
                color: #fff;
                font-size: 14px;
                text-decoration: none;
                padding: 5px 10px;
                border: 1px solid #fff;
                border-radius: 5px;
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            .user-buttons a:hover {
                background-color: #e50914;
                color: #fff;
                border-color: #e50914;
            }

            /* 헤더 */
            .main-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: #000;
                padding: 20px 40px;
                color: #fff;
            }
            .header-left {
                display: flex;
                align-items: center;
                gap: 20px;
            }
            .header-title {
                font-size: 24px;
                font-weight: bold;
                color: #e50914;
            }
            .main-nav ul {
                list-style: none;
                display: flex;
                gap: 20px;
                margin: 0;
                padding: 0;
            }
            .main-nav a {
                font-size: 14px;
                color: #fff;
                text-decoration: none;
                font-weight: bold;
            }
            .main-nav a:hover {
                color: #e50914;
            }
            .header-right {
                display: flex;
                align-items: center;
                gap: 20px;
            }
            .search-box {
                display: flex;
                align-items: center;
                border: 1px solid #444;
                border-radius: 5px;
                overflow: hidden;
            }
            .search-box input {
                padding: 5px 10px;
                border: none;
                outline: none;
                background-color: #222;
                color: #fff;
                font-size: 14px;
            }
            .search-box button {
                background-color: #e50914;
                color: #fff;
                border: none;
                padding: 5px 10px;
                cursor: pointer;
                font-size: 14px;
                font-weight: bold;
                transition: background-color 0.3s ease;
            }
            .search-box button:hover {
                background-color: #d40813;
            }
            .user-info a {
                color: #fff;
                font-size: 14px;
                text-decoration: none;
                padding: 5px 10px;
                border: 1px solid #fff;
                border-radius: 5px;
                transition: background-color 0.3s ease, color 0.3s ease;
            }
            .user-info a:hover {
                background-color: #e50914;
                color: #fff;
                border-color: #e50914;
            }
            /* 메인 컨테이너 */
            .full-page-container {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
                box-sizing: border-box;
                width: 100%;
            }
            /* My Page 섹션 */
            .mypage {
                background-color: #2a2a2a;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
                max-width: 600px;
                width: 100%;
            }
            .mypage h2 {
                font-size: 28px;
                font-weight: bold;
                margin-bottom: 20px;
                text-align: center;
                border-bottom: 2px solid #444;
                padding-bottom: 10px;
            }
            .user-details p {
                margin: 10px 0;
                font-size: 16px;
                line-height: 1.5;
            }
            .user-details strong {
                color: #f1c40f;
            }
            .user-actions {
                margin-top: 20px;
                display: flex;
                gap: 20px;
                justify-content: center;
            }
            .user-actions .btn {
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
            }
            .user-actions .edit-btn {
                background-color: #444;
                color: #fff;
                transition: background-color 0.3s ease;
            }
            .user-actions .edit-btn:hover {
                background-color: #555;
            }
            .user-actions .delete-btn {
                background-color: #e50914;
                color: #fff;
                transition: background-color 0.3s ease;
            }
            .user-actions .delete-btn:hover {
                background-color: #d40813;
            }
            footer {
                background-color: #000;
                padding: 10px;
                text-align: center;
                font-size: 14px;
                color: #fff;
                position: fixed; /* 화면에 고정 */
                bottom: 0; /* 화면 아래에 위치 */
                left: 0; /* 화면의 왼쪽 끝에 고정 */
                width: 100%; /* 화면 전체 너비 */
            }
        </style>
    </head>
    <body>
        <header class="main-header">
    <div class="header-left">
        <form method="GET" action="main.php" style="margin: 0; padding: 0;">
            <button class="header-title-button" type="submit">ReviewFlix</button>
        </form>
        <nav class="main-nav">
            <ul>
                <li><a href="movie_list.php">영화</a></li>
                <li><a href="drama_list.php">드라마</a></li>
                <li><a href="webtoon_list.php">웹툰</a></li>
                <li><a href="book_list.php">책</a></li>
            </ul>
        </nav>
    </div>
    <div class="header-right">
        <div class="search-box">
            <form method="GET" action="search_result.php">
                <input type="text" name="search" placeholder="검색하세요">
                <button type="submit">검색</button>
            </form>
        </div>
        <div class="user-buttons">
            <a href="logout.php" class="logout">Log Out</a>
        </div>
    </div>
</header>
        <div class="full-page-container">
            <div class="mypage">
                <h2>My Page</h2>
                <div class="user-details">
                        <p><strong>ID:</strong> <?= htmlspecialchars($user['ID']) ?></p>
                        <p><strong>Name:</strong> <?= htmlspecialchars($user['NAME']) ?></p>
                        <p><strong>Nickname:</strong> <?= htmlspecialchars($user['NICKNAME']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['EMAIL']) ?></p>
                        <p><strong>Birthday:</strong> <?= htmlspecialchars($user['BIRTHDATE']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($user['PHONE']) ?></p>
                    </div>
                <div class="user-actions">
                <form method="POST" action="editmypage.php">
                    <button class="btn edit-btn" type="submit">정보 수정</button>
                </form>
                <form method="POST" action="process_withdraw.php">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['ID']) ?>">
                    <button class="btn delete-btn" type="submit" onclick="return confirm('정말 탈퇴하시겠습니까?');">회원 탈퇴</button>
                </form>
                </div>
            </div>
        </div>
        <footer>
            &copy; 2024 ReviewFlix
        </footer>
    </body>
</html>
