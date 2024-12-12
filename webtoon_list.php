<?php
// 데이터베이스 연결
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
    die("Failed to connect to Oracle: " . $e['message']);
}

$sql = "
    SELECT 
        c.Content_ID, 
        c.Title, 
        c.Poster_Path, 
        c.Director, 
        c.GENRE, 
        c.ACTORS, 
        c.Duration, 
        c.Author, -- 저자 필드 추가
        NVL(ROUND(AVG(r.Rating), 2), 0) AS Average_Rating
    FROM 
        Content c
    LEFT JOIN 
        Review r ON c.Content_ID = r.Content_ID
    WHERE 
        c.Content_Type = 'Webtoon'
    GROUP BY 
        c.Content_ID, c.Title, c.Poster_Path, c.Director, c.GENRE, c.ACTORS, c.Duration, c.Author -- 저자 필드 추가
";
$stid = oci_parse($conn, $sql);

if (!oci_execute($stid)) {
    $e = oci_error($stid);
    die("Failed to execute SQL: " . $e['message']);
}

$webtoon = [];
while ($row = oci_fetch_assoc($stid)) {
    $webtoon[] = [
        'content_id' => $row['CONTENT_ID'],
        'title' => $row['TITLE'],
        'poster_path' => $row['POSTER_PATH'],
        'director' => $row['DIRECTOR'],
        'genre' => $row['GENRE'],
        'actors' => $row['ACTORS'],
        'duration' => $row['DURATION'],
        'author' => $row['AUTHOR'], // 저자 필드 추가
        'average_rating' => $row['AVERAGE_RATING'] // 평균 평점 추가
    ];
}

oci_free_statement($stid);
oci_close($conn);
?>
<?php
session_start();

// 로그인 상태 확인
$isLoggedIn = isset($_SESSION['username']) && !empty($_SESSION['username']);

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Review</title>
    <style>
        /* 공통 스타일 */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #1a1a1a;
            color: #fff;
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

        /*영화 리스트 css */
        .movie-list-section {
            padding: 40px 20px;
            background-color: #1a1a1a;
            max-width: 1200px;
            margin: 0 auto;
        }

        .movie-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .movie-grid a {
            text-decoration: none;
            color: inherit;
        }

        .movie-card {
            background: #222;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 720px;
        }

        .movie-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.5);
        }

        .movie-card img {
            width: 100%;
            height: 500px;
            object-fit: cover;
        }

        .movie-info {
            padding: 10px;
            color: #fff;
        }

        .movie-info h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #e50914;
        }

        .movie-info p {
            font-size: 14px;
            margin-bottom: 5px;
        }

        @media screen and (max-width: 768px) {
            .movie-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media screen and (max-width: 480px) {
            .movie-grid {
                grid-template-columns: 1fr;
            }
        }

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
                <a href="mypage.php">My Page</a>
                <a href="logout.php">Log Out</a>
            <?php else: ?>
                <a href="login.php">Log In</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="movie-list-section">
    <h2 class="section-title">웹툰 리스트</h2>
    <div class="movie-grid">
    <?php foreach ($webtoon as $content): ?>
        <a href="content_review.php?content_id=<?php echo htmlspecialchars($content['content_id']); ?>">
            <div class="movie-card">
                <img src="<?php echo htmlspecialchars($content['poster_path']); ?>" alt="<?php echo htmlspecialchars($content['title']); ?>">
                <div class="movie-info">
                    <h3><?php echo htmlspecialchars($content['title']); ?></h3>
                    <?php if (isset($content['average_rating']) && $content['average_rating'] > 0): ?>
                        <div class="movie-rating" style="color: #ffcc00; font-size: 14px; margin-bottom: 5px;">
                            평점: <?php echo number_format($content['average_rating'], 2); ?> / 5
                        </div>
                    <?php else: ?>
                        <div class="movie-rating" style="color: #bbb; font-size: 14px; margin-bottom: 5px;">
                            평점 없음
                        </div>
                    <?php endif; ?>
                    <p><strong>작가:</strong> <?php echo htmlspecialchars($content['author']); ?></p>
                    <p><strong>장르:</strong> <?php echo htmlspecialchars($content['genre']); ?></p>
                    <p><strong>분량:</strong> <?php echo htmlspecialchars($content['duration']); ?>화</p>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>
</section>


<footer>
    &copy; 2024 ReviewFlix
</footer>

<script>
    function alertLoginRequired(event) {
        event.preventDefault(); // 링크 이동 막기
        alert("로그인이 필요합니다!");
    }
</script>

</body>
</html> 