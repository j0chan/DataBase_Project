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

        /* 사용자 버튼 */
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

        /* 메인 컨테이너 */
        .container {
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

        .user-details-form label {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .user-details-form input {
            padding: 12px;
            border: none;
            border-radius: 5px;
            background-color: #444;
            color: #fff;
            font-size: 16px;
            margin-top: 8px;
            width: 95%;
        }

        .user-details-form input:disabled {
            background-color: #333;
            color: #aaa;
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

        .user-actions .save-btn {
        background-color: #4caf50;
        color: #fff;
        }

        .user-actions .save-btn:hover {
            background-color: #45a049;
        }

        .user-actions .cancel-btn {
            background-color: #f44336;
            color: #fff;
        }

        .user-actions .cancel-btn:hover {
            background-color: #d32f2f;
        }

        /* 풋터 스타일 */
        footer {
            background-color: #000;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            color: #fff;
            margin-top: auto;
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
</header>

    <!-- 메인 컨테이너 -->
    <div class="container">
        <div class="mypage">
            <h2>Edit My Page</h2>
            <form class="user-details-form" method="post" action="update_profile.php">
                <label>
                    <strong>User ID:</strong>
                    <input type="text" id="user_id" name="user_id" value="<?= htmlspecialchars($user['ID']) ?>" disabled>
                </label>
                <label>
                    <strong>User Level:</strong>
                    <input type="text" id="user_level" name="user_level" value="<?= htmlspecialchars($user['USERLEVEL']) ?>" disabled>
                </label>
                <label>
                    <strong>Name:</strong>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['NAME']) ?>">
                </label>
                <label>
                    <strong>Nickname:</strong>
                    <input type="text" id="nickname" name="nickname" value="<?= htmlspecialchars($user['NICKNAME']) ?>">
                </label>
                <label>
                    <strong>Email:</strong>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['EMAIL']) ?>">
                </label>
                <?php
                    $birthdate = !empty($user['BIRTHDATE']) ? date('Y-m-d', strtotime($user['BIRTHDATE'])) : '';
                ?>
                <label>
                    <strong>Birthday:</strong>
                    <input type="date" id="birthdate" name="birthdate" value="<?= htmlspecialchars($birthdate) ?>">
                </label>
                <label>
                    <strong>Phone:</strong>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['PHONE']) ?>">
                </label>
                <div class="user-actions">
                    <button type="submit" class="btn save-btn">저장</button>
                    <button type="button" class="btn cancel-btn" onclick="location.href='mypage.php';">취소</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 푸터 -->
    <footer>
    &copy; 2024 ReviewFlix
</footer>
</body>
</html>
