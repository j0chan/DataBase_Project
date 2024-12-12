<?php
session_start();
include 'process_review.php'; // 데이터베이스에서 책 데이터를 가져오는 파일

// 로그인 상태 확인
$isLoggedIn = isset($_SESSION['username']) && !empty($_SESSION['username']);
$userNickname = $isLoggedIn ? $_SESSION['nickname'] : null; // 닉네임 세션 값

$contentID = $_GET['content_id']; // Content_ID로 변경
$contentData = getContentData($conn, $contentID);

// 로그아웃 처리
if (isset($_GET['logout'])) {
    // 세션 파기
    session_unset(); // 세션 데이터 제거
    session_destroy(); // 세션 종료
    header("Location: main.php"); // 메인 페이지로 리다이렉트
    exit;
}

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Review - Home</title>
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
        /* 배너 스타일 */
        .banner {
            position: relative;
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
            background: rgba(0, 0, 0, 0.6); /* 어두운 오버레이 */
            z-index: 1;
        }

        .banner-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 20px;
            color: #fff;
        }

        .banner-title {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        }

        .movie-meta {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 16px;
            color: #ddd;
        }

        .movie-meta li {
            margin: 5px 0;
        }

        /* 책 정보 섹션 */
/* 영화 정보 섹션 */
.book-info {
    display: flex;
    justify-content: center;
    padding: 40px;
    background: linear-gradient(145deg, #3a3a3a, #505050); /* 그라데이션 배경 */
    color: #fff;
    border-radius: 15px; /* 더 부드러운 모서리 */
    max-width: 1200px;
    margin: 30px auto;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); /* 더 강한 그림자 */
    transition: transform 0.3s, box-shadow 0.3s; /* hover 애니메이션 추가 */
}


.book-details {
    display: flex;
    align-items: flex-start;
    gap: 30px; /* 포스터와 텍스트 간 간격 */
}

.book-poster {
    width: 260px;
    height: 400px;
    border-radius: 15px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.5); /* 포스터에 더 깊은 그림자 */
    transition: transform 0.3s; /* hover 효과 */
    object-fit: cover; /* 이미지 잘림 방지 */
}

.book-poster:hover {
    transform: scale(1.05); /* 호버 시 포스터 확대 */
}

.details-text {
    max-width: 650px;
    flex-grow: 1;
    background: rgba(255, 255, 255, 0.1); /* 약간의 투명 배경 */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(5px); /* 유리 효과 */
}

.details-text h2 {
    font-size: 32px; /* 타이틀을 더 크게 */
    color: #e50914;
    margin-bottom: 15px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6); /* 텍스트에 그림자 */
}

.details-text p {
    font-size: 18px;
    line-height: 1.8;
    margin: 8px 0;
    color: rgb(240, 240, 240);
}

.details-text strong {
    color: skyblue; /* 강조 텍스트에 색상 변경 */
}

/* 반응형 디자인 */
@media screen and (max-width: 768px) {
    .movie-info {
        flex-direction: column;
        align-items: center;
    }

    .movie-details {
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .movie-poster {
        width: 80%;
        max-width: 300px;
        margin-bottom: 20px;
    }

    .details-text {
        text-align: center;
    }
}

.review-list {
    margin: 30px auto;
    padding: 40px;
    max-width: 1200px; /* 최대 너비를 설정하여 정렬 유지 */
}
.review-list-title{
    text-align : center;
}

.review-list ul {
    list-style: none;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3열 고정 */
    gap: 20px; /* 카드 간격 */
}

.review-list li {
    background: #444; /* 배경색 회색 */
    padding: 20px;
    height: 250px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column; /* 수직 정렬 */
    justify-content: space-between;
    overflow: hidden; /* 내부 텍스트 오버플로우 숨기기 */
}

.review-card {
    height: 250px; /* 카드 고정 높이 */
    display: flex;
    flex-direction: column;
    padding: 15px;
    background: #444; /* 회색 배경 */
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    position: relative;
}

/* 상단 요소: 스트롱, 레이팅, 라이크 */
.review-card-header {
    display: flex;
    justify-content: space-between; /* 좌우로 요소 정렬 */
    align-items: center; /* 수직 정렬 */
    color: #fff;
    margin-bottom: 10px; /* 아래 여백 */
}

.review-card-header strong {
    font-size: 16px;
}

.review-card-header .rating,
.review-card-header .likes {
    font-size: 14px;
    color: #ccc;
}

/* 중간 텍스트 내용 */
.review-card-content {
    flex-grow: 1; /* 남는 공간을 채움 */
    margin: 10px 0; /* 상하 여백 */
    font-size: 14px;
    color: #ccc; /* 연한 회색 텍스트 */
    overflow-y: auto; /* 텍스트 스크롤 */
}

/* 하단 버튼 고정 */
.review-card-footer {
    display: flex;
    justify-content: flex-end; /* 버튼을 오른쪽에 정렬 */
    gap: 10px; /* 버튼 간격 */
}

.review-card-footer button {
    background: #e50914;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.review-card-footer button:hover {
    background: #d40813;
}


/* 반응형 디자인 */
@media screen and (max-width: 768px) {
    .review-list ul {
        grid-template-columns: 1fr; /* 모바일에서는 한 열 */
    }
}


.review-form {
    margin: 30px auto;
    padding: 40px;
    max-width: 600px; /* 폼의 최대 너비 */
    background: linear-gradient(145deg, #2a2a2a, #1e1e1e); /* 부드러운 배경 그라데이션 */
    border-radius: 15px; /* 둥근 모서리 */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3); /* 그림자 */
}

.review-form h3 {
    text-align: center;
    font-size: 24px;
    color: #e50914; /* 강조된 제목 색상 */
    margin-bottom: 20px;
}

.review-form label {
    display: block;
    font-size: 16px;
    margin-bottom: 10px;
    color: #fff;
    font-weight: bold;
}

.review-form input,
.review-form textarea,
.review-form select {
    width: 100%; /* 동일한 너비 */
    padding: 10px;
    border: 1px solid #444;
    border-radius: 5px;
    background: #222;
    color: #fff;
    font-size: 16px;
    box-sizing: border-box; /* 패딩과 보더를 포함한 크기 계산 */
}

.review-form input:focus,
.review-form textarea:focus,
.review-form select:focus {
    border-color: #e50914; /* 포커스 시 강조 색상 */
    outline: none;
}

.review-form button {
    background: #e50914;
    color: #fff;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    width: 100%; /* 버튼 너비를 전체로 */
}

.review-form button:hover {
    background: #d40813;
    transform: scale(1.05); /* 버튼 확대 */
}

.review-form button:active {
    transform: scale(0.98); /* 버튼 클릭 시 효과 */
}

.review-form a {
    color: #e50914;
    font-weight: bold;
    text-decoration: none;
}

.review-form a:hover {
    text-decoration: underline;
}



        /* 반응형 디자인 */
        @media screen and (max-width: 768px) {
            .review-grid {
                grid-template-columns: 1fr;
            }
        }

        /* 푸터 */
        footer {
            text-align: center;
            background-color: #000;
            padding: 10px;
            font-size: 14px;
        }
 </style>
</head>
<body>
<header class="main-header">
    <div class="header-left">
        <form method="GET" action="main.php">
            <button class="header-title-button">ReviewFlix</button>
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
                <a href="?logout=true">Log Out</a>
            <?php else: ?>
                <a href="login.php">Log In</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- 배너 -->
<section class="banner" style="background-image: url('<?php echo htmlspecialchars($contentData['POSTER_PATH'] ?? 'images/default_banner.jpg'); ?>');">
    <div class="banner-overlay"></div>
    <div class="banner-content">
        <h1><?php echo htmlspecialchars($contentData['TITLE'] ?? '제목 없음'); ?></h1>
        <ul class="movie-meta">
            <li>
                <?php echo htmlspecialchars($contentData['RELEASEDATE'] ?? '출시일 정보 없음'); ?> · 
                <?php echo htmlspecialchars($contentData['GENRE'] ?? '장르 정보 없음'); ?>
            </li>
            <?php if ($contentData['DURATION']): ?>
                <li>
                    <?php echo htmlspecialchars($contentData['DURATION']); ?>
                    <?php echo ($contentData['CONTENT_TYPE'] === 'Movie' || $contentData['CONTENT_TYPE'] === 'Drama') ? '분' : ($contentData['CONTENT_TYPE'] === 'Webtoon' ? '화' : '쪽'); ?>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</section>

<!-- 콘텐츠 정보 섹션 -->
<section class="movie-info">
    <div class="movie-details">
        <img src="<?php echo htmlspecialchars($contentData['POSTER_PATH'] ?? 'images/default_poster.jpg'); ?>" alt="포스터">
        <div class="details-text">
            <h2><?php echo htmlspecialchars($contentData['TITLE'] ?? '제목 없음'); ?></h2>
            <p><strong>장르:</strong> <?php echo htmlspecialchars($contentData['GENRE'] ?? '장르 없음'); ?></p>
            <?php if ($contentData['CONTENT_TYPE'] === 'Movie' || $contentData['CONTENT_TYPE'] === 'Drama'): ?>
                <p><strong>감독:</strong> <?php echo htmlspecialchars($contentData['DIRECTOR'] ?? '감독 정보 없음'); ?></p>
                <p><strong>출연:</strong> <?php echo htmlspecialchars($contentData['ACTORS'] ?? '출연진 정보 없음'); ?></p>
            <?php elseif ($contentData['CONTENT_TYPE'] === 'Book' || $contentData['CONTENT_TYPE'] === 'Webtoon'): ?>
                <p><strong>저자:</strong> <?php echo htmlspecialchars($contentData['AUTHOR'] ?? '저자 정보 없음'); ?></p>
            <?php endif; ?>
            <p><strong>출시일:</strong> <?php echo htmlspecialchars($contentData['RELEASEDATE'] ?? '출시일 정보 없음'); ?></p>
        </div>
    </div>
</section>

<!-- 리뷰 목록 -->
<h3 class="review-list-title">리뷰 목록</h3>
<div class="review-list" style="max-height: 400px; overflow-y: auto;">
    <?php
    $reviews = getReviews($conn, $contentData['CONTENT_ID']);
    if (!empty($reviews)): ?>
    <ul>
        <?php foreach ($reviews as $review): ?>
            <li>
                <div class="review-card">
                    <div class="review-card-header">
                        <strong><?php echo htmlspecialchars($review['USERID']); ?></strong>
                        <span class="rating">(평점: <?php echo htmlspecialchars($review['RATING']); ?>/5)</span>
                        <span class="likes">👍 <?php echo getLikeCount($conn, $review['REVIEW_ID']); ?></span>
                    </div>
                    <div class="review-card-content">
                        <p><?php echo nl2br(htmlspecialchars($review['CONTENT'])); ?></p>
                    </div>
                    <div class="review-card-footer">
                        <form method="POST" action="count_review_action.php">
                            <input type="hidden" name="review_id" value="<?php echo $review['REVIEW_ID']; ?>">
                            <input type="hidden" name="action" value="like">
                            <button type="submit">좋아요</button>
                        </form>
                        <?php if ($_SESSION['username'] === $review['USERID']): ?>
                            <form method="POST" action="count_review_action.php">
                                <input type="hidden" name="review_id" value="<?php echo $review['REVIEW_ID']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" onclick="return confirm('리뷰를 삭제하시겠습니까?')">삭제</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
        <p>아직 작성된 리뷰가 없습니다. 첫 번째 리뷰를 작성해 보세요!</p>
    <?php endif; ?>
</div>

<!-- 리뷰 입력 폼 -->
<div class="review-form">
    <h3>리뷰 작성하기</h3>
    <?php if ($isLoggedIn): ?>
        <form method="POST" action="submit_review.php">
            <input type="hidden" name="content_id" value="<?php echo htmlspecialchars($contentID); ?>">
            <div>
                <label for="rating">평점</label>
                <select id="rating" name="rating" required>
                    <option value="" disabled selected>선택하세요</option>
                    <option value="5">5점</option>
                    <option value="4">4점</option>
                    <option value="3">3점</option>
                    <option value="2">2점</option>
                    <option value="1">1점</option>
                </select>
            </div>
            <div>
                <label for="content">리뷰 내용</label>
                <textarea id="content" name="content" rows="4" required></textarea>
            </div>
            <div>
                <button type="submit">리뷰 제출</button>
            </div>
        </form>
    <?php else: ?>
        <p>리뷰를 작성하려면 <a href="login.php">로그인</a>이 필요합니다.</p>
    <?php endif; ?>
</div>

<footer>
    &copy; 2024 ReviewFlix
</footer>

</body>
</html>
