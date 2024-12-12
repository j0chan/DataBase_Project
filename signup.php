<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign Up</title>
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


            .container {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
                max-width: 400px;
                margin: 0 auto;
            }

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
                font-size: 24px;
            }

            label {
                display: block;
                margin-bottom: 5px;
                font-size: 14px;
            }

            input {
                width: 95%;
                padding: 10px;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
                background-color: #333;
                color: #fff;
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
        </div>
    </header>

        <div class="container">
            <div class="form-section">
                <h2>Create an Account</h2>
                <form action="process_signup.php" method="POST">
                    <label for="id">ID</label>
                    <input type="text" id="id" name="id" placeholder="Enter your ID" required>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>

                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required>

                    <label for="nickname">Nickname</label>
                    <input type="text" id="nickname" name="nickname" placeholder="Enter your nickname" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>

                    <label for="birthdate">Birthdate</label>
                    <input type="date" id="birthdate" name="birthdate" required>

                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>

                    <button type="submit">Sign Up</button>
                </form>
            </div>
        </div>

        <footer>
            &copy; 2024 ReviewFlix
        </footer>
    </body>
</html>
