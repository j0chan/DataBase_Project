<?php
session_start();
include 'process_main.php'; // 데이터베이스에서 영화 데이터를 가져오는 파일

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
            background: #444;
            height: 600px;
            display: flex;
            background-image: url('/2_team/2_team3/images/banner2.png');
            background-size: cover;
            background-position: center;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #ffd700;
            overflow: hidden;
        }

        .banner-title,
        .banner-subtitle,
        .banner-buttons {
            opacity: 0; /* 초기에는 보이지 않음 */
            transition: opacity 1s ease-in-out;
        }

        .banner-title {
            font-size: 48px;
            font-weight: bold;
            color: #fff;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }

        .banner-subtitle {
            font-size: 20px;
            margin-top: 10px;
            color: #ffd700;
        }

        .banner-buttons {
            margin-top: 20px;
        }

        .banner-btn {
            background-color: #e50914;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .banner-btn:hover {
            background-color: #d40813;
            transform: scale(1.05);
        }

        /* 영화 리스트 섹션 */
        #popular {
            padding: 20px;
            background-color: #1a1a1a;
            color: #fff;
            border-top: 2px solid gray; /* 섹션 밑에 회색 실선 추가 */
        }

        #popular .section-title {
            text-align: center; /* 타이틀 가운데 정렬 */
            font-size: 24px;
            margin-bottom: 20px;
            color: white;
        }

        #popular .movie-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }

        .movie-item {
            text-align: center;
            width: 200px; /* 포스터와 동일한 폭 */
        }

        .movie-item img {
            width: 200px; /* 포스터 폭 */
            height: 300px; /* 포스터 높이 */
            object-fit: cover;
            border-radius: 8px; /* 둥근 모서리 */
            transition: transform 0.3s ease;
        }

        .movie-item img:hover {
            transform: scale(1.06); /* 호버 시 확대 */
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

<section class="banner">
    <h1 class="banner-title">최고의 작품를 만나보세요!</h1>
    <p class="banner-subtitle">최신 리뷰와 함께 더 나은 선택을!</p>
    <div class="banner-buttons">
        <button class="banner-btn" onclick="window.location.href='movie_list.php'">영화</button>
        <button class="banner-btn" onclick="window.location.href='drama_list.php'">드라마</button>
        <button class="banner-btn" onclick="window.location.href='webtoon_list.php'">웹툰</button>
        <button class="banner-btn" onclick="window.location.href='book_list.php'">책</button>
    </div>
</section>

<section id="popular">
    <h2 class="section-title">인기 영화</h2>
    <div class="movie-container">
        <?php foreach ($movies as $movie): ?>
            <div class="movie-item">
                <a href="content_review.php?content_id=<?php echo htmlspecialchars($movie['content_id']); ?>">
                    <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    <div class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></div>
                    <?php if (isset($movie['average_rating']) && $movie['average_rating'] > 0): ?>
                        <div class="movie-rating">
                            평점: <?php echo number_format($movie['average_rating'], 2); ?> / 5
                        </div>
                    <?php else: ?>
                        <div class="movie-rating" style="color: #bbb; font-size: 14px;">
                            평점 없음
                        </div>
                    <?php endif; ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>


<section id="popular">
    <h2 class="section-title">인기 드라마</h2>
    <div class="movie-container">
        <?php foreach ($dramas as $drama): ?>
            <div class="movie-item">
                <a href="content_review.php?content_id=<?php echo htmlspecialchars($drama['content_id']); ?>">
                    <img src="<?php echo htmlspecialchars($drama['poster_path']); ?>" alt="<?php echo htmlspecialchars($drama['title']); ?>">
                    <div class="movie-title"><?php echo htmlspecialchars($drama['title']); ?></div>
                    <?php if (isset($drama['average_rating']) && $drama['average_rating'] > 0): ?>
                        <div class="movie-rating" style="color: #ffcc00; font-size: 14px;">
                            평점: <?php echo number_format($drama['average_rating'], 2); ?> / 5
                        </div>
                    <?php else: ?>
                        <div class="movie-rating" style="color: #bbb; font-size: 14px;">
                            평점 없음
                        </div>
                    <?php endif; ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="popular">
    <h2 class="section-title">인기 웹툰</h2>
    <div class="movie-container">
        <?php foreach ($webtoons as $webtoon): ?>
            <div class="movie-item">
                <a href="content_review.php?content_id=<?php echo htmlspecialchars($webtoon['content_id']); ?>">
                    <img src="<?php echo htmlspecialchars($webtoon['poster_path']); ?>" alt="<?php echo htmlspecialchars($webtoon['title']); ?>">
                    <div class="movie-title"><?php echo htmlspecialchars($webtoon['title']); ?></div>
                    <?php if (isset($webtoon['average_rating']) && $webtoon['average_rating'] > 0): ?>
                        <div class="movie-rating" style="color: #ffcc00; font-size: 14px;">
                            평점: <?php echo number_format($webtoon['average_rating'], 2); ?> / 5
                        </div>
                    <?php else: ?>
                        <div class="movie-rating" style="color: #bbb; font-size: 14px;">
                            평점 없음
                        </div>
                    <?php endif; ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="popular">
    <h2 class="section-title">베스트셀러 책</h2>
    <div class="movie-container">
        <?php foreach ($books as $book): ?>
            <div class="movie-item">
                <a href="content_review.php?content_id=<?php echo htmlspecialchars($book['content_id']); ?>">
                    <img src="<?php echo htmlspecialchars($book['poster_path']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                    <div class="movie-title"><?php echo htmlspecialchars($book['title']); ?></div>
                    <?php if (isset($book['average_rating']) && $book['average_rating'] > 0): ?>
                        <div class="movie-rating" style="color: #ffcc00; font-size: 14px;">
                            평점: <?php echo number_format($book['average_rating'], 2); ?> / 5
                        </div>
                    <?php else: ?>
                        <div class="movie-rating" style="color: #bbb; font-size: 14px;">
                            평점 없음
                        </div>
                    <?php endif; ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<footer>
    &copy; 2024 ReviewFlix
</footer>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const bannerTitle = document.querySelector(".banner-title");
        const bannerSubtitle = document.querySelector(".banner-subtitle");
        const bannerButtons = document.querySelector(".banner-buttons");
        let textArray = ["최고의 작품을 만나보세요!", "지금 바로 선택하세요!", "새로운 리뷰가 기다리고 있어요!"];
        let currentIndex = 0;

        function updateText() {
            bannerTitle.style.opacity = 0;
            setTimeout(() => {
                currentIndex = (currentIndex + 1) % textArray.length;
                bannerTitle.textContent = textArray[currentIndex];
                bannerTitle.style.opacity = 1;
            }, 500);
        }

        setTimeout(() => {
            bannerTitle.style.opacity = 1;
            bannerSubtitle.style.opacity = 1;
            bannerButtons.style.opacity = 1;
        }, 500);

        setInterval(updateText, 3000);
    });
</script>

<script>
    function alertLoginRequired(event) {
        event.preventDefault(); // 링크 이동 막기
        alert("로그인이 필요합니다!");
    }
</script>

</body>
</html>
