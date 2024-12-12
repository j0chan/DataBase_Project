<?php
session_start();

// 로그인 여부 확인
if (isset($_SESSION['username'])) {
    // 이미 로그인된 상태일 경우 main.php로 리다이렉션
    header("Location: main.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        /* 기본 스타일 */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #1a1a1a;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            text-transform: none;
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

        /* 컨테이너 스타일 */
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            max-width: 400px;
            margin: 0 auto;
        }

        /* 폼 섹션 스타일 */
        .form-section {
            background-color: #2a2a2a;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            width: 100%;
        }

        .form-section h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
            font-size: 24px;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
            background-color: #333;
            color: #fff;
        }

        input::placeholder {
            color: #aaa;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #e50914;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-transform: uppercase;
        }

        button:hover {
            background-color: #d40813;
        }

        /* 추가 옵션 */
        .extra-options p {
            text-align: center;
            font-size: 14px;
            color: #ccc;
            margin-top: 10px;
        }

        .extra-options a {
            color: #e50914;
            text-decoration: none;
        }

        .extra-options a:hover {
            text-decoration: underline;
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
    <div class="container">
        <div class="form-section">
            <h2>Welcome Back</h2>
            <form action="process_login.php" method="POST">
                <label for="username">ID</label>
                <input type="text" id="username" name="username" placeholder="Enter your ID" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <button type="submit">Login</button>
            </form>
            <div class="extra-options">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            </div>
        </div>
    </div>

    <footer>
    &copy; 2024 ReviewFlix
</footer>
</body>
</html>
