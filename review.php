<?php
session_start();

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
$connect = oci_connect($username, $password, $db);
if (!$connect) {
    $e = oci_error();
    die("Database connection failed: " . htmlentities($e['message'], ENT_QUOTES));
}

// 테스트용 영화 ID
$movieId = 'M001';

// 영화 정보 가져오기
$sql = "SELECT * FROM Movie WHERE Movie_ID = :movieId";
$stid = oci_parse($connect, $sql);
oci_bind_by_name($stid, ":movieId", $movieId);
oci_execute($stid);
$movie = oci_fetch_assoc($stid);
oci_free_statement($stid);

if (!$movie) {
    echo "<p>영화 정보를 찾을 수 없습니다.</p>";
    exit;
}

// Oracle 연결 닫기
oci_close($connect);

// 로그인 상태 확인
$isLoggedIn = isset($_SESSION['username']) && !empty($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['TITLE']); ?> - 리뷰</title>
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

        /* 배너 */
        .banner {
            position: relative;
            background-image: url('<?php echo htmlspecialchars($movie['POSTER_PATH']); ?>');
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
        }

        .banner-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 20px;
        }

        .banner-title {
            font-size: 48px;
            font-weight: bold;
        }

        /* 영화 정보 */
        .movie-info {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
            margin: 20px;
            padding: 20px;
            background-color: #333;
            border-radius: 10px;
        }

        .movie-poster {
            width: 250px;
            height: 400px;
            border-radius: 10px;
            object-fit: cover;
        }

        .details-text {
            flex: 1;
            color: #ddd;
        }

        .details-text h2 {
            color: #e50914;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .details-text p {
            margin: 5px 0;
            font-size: 16px;
        }

        /* 푸터 */
        footer {
            text-align: center;
            background-color: #000;
            padding: 10px;
            color: #fff;
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
            <?php if ($isLoggedIn): ?>
                <a href="mypage.php" class="mypage">My Page</a>
                <a href="?logout=true" class="logout">Log Out</a>
            <?php else: ?>
                <a href="#" class="mypage" onclick="alertLoginRequired(event)">My Page</a>
                <a href="login.php" class="login">Log In</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="banner">
    <div class="banner-overlay"></div>
    <div class="banner-content">
        <h1 class="banner-title"><?php echo htmlspecialchars($movie['TITLE']); ?></h1>
    </div>
</section>

<section class="movie-info">
    <img src="<?php echo htmlspecialchars($movie['POSTER_PATH']); ?>" alt="영화 포스터" class="movie-poster">
    <div class="details-text">
        <h2><?php echo htmlspecialchars($movie['TITLE']); ?></h2>
        <p><strong>장르:</strong> <?php echo htmlspecialchars($movie['GENRE']); ?></p>
        <p><strong>감독:</strong> <?php echo htmlspecialchars($movie['DIRECTOR']); ?></p>
        <p><strong>출연:</strong> <?php echo htmlspecialchars($movie['ACTORS']); ?></p>
        <p><strong>개봉일:</strong> <?php echo htmlspecialchars($movie['RELEASEDATE']); ?></p>
        <p><strong>러닝타임:</strong> <?php echo htmlspecialchars($movie['DURATION']); ?>분</p>
    </div>
</section>


<footer>
    &copy; 2024 Movie Review
</footer>
</body>
</html>
