<?php
session_start();
include 'process_search_result.php'; // 검색 결과 처리 파일

// 로그인 상태 확인
$isLoggedIn = isset($_SESSION['username']) && !empty($_SESSION['username']);

// 검색 결과 가져오기
$results = isset($results) ? $results : [];
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
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

        .movie-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .movie-item {
            text-align: center;
            width: 200px;
            flex: 0 1 calc(20% - 20px);
            box-sizing: border-box;
        }

        .movie-item img {
            width: 200px;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }
        
        .movie-item img:hover {
            transform: scale(1.03);
        }

        .movie-title {
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            text-align: center;
        }

        .movie-rating {
            color: #ffcc00;
            font-size: 14px;
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
    <h2 class="section-title">검색 결과</h2>
    <div class="container">
        <?php if (!empty($results)): ?>
            <div class="movie-container">
                <?php foreach ($results as $result): ?>
                    <div class="movie-item">
                        <a href="content_review.php?content_id=<?php echo htmlspecialchars($result['content_id']); ?>">
                            <img src="<?php echo htmlspecialchars($result['poster_path']); ?>" alt="<?php echo htmlspecialchars($result['title']); ?>">
                            <div class="movie-title"><?php echo htmlspecialchars($result['title']); ?></div>
                            <div class="movie-rating">평점: <?php echo number_format($result['average_rating'], 2); ?> / 5</div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-results">검색 결과가 없습니다.</p>
        <?php endif; ?>
    </div>
</section>

<footer>
    &copy; 2024 ReviewFlix
</footer>

</body>
</html>
