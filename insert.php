<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>
<html>
<head>
    <title>Users 테이블 데이터 추가</title>
</head>
<body>
    <h1>Users 테이블에 데이터 추가</h1>
    <form method="POST" action="">
        <label for="id">ID: </label>
        <input type="text" name="id" id="id" required><br>
        <label for="password">Password: </label>
        <input type="password" name="password" id="password" required><br>
        <label for="userlevel">User Level: </label>
        <input type="text" name="userlevel" id="userlevel"><br>
        <label for="name">Name: </label>
        <input type="text" name="name" id="name"><br>
        <label for="nickname">Nickname: </label>
        <input type="text" name="nickname" id="nickname"><br>
        <label for="email">Email: </label>
        <input type="email" name="email" id="email"><br>
        <label for="birthdate">Birthdate (YYYY-MM-DD): </label>
        <input type="date" name="birthdate" id="birthdate"><br>
        <label for="phone">Phone: </label>
        <input type="text" name="phone" id="phone"><br>
        <input type="submit" value="추가">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Oracle DB 연결 설정
        $db = '(DESCRIPTION = 	
        (ADDRESS_LIST=
            (ADDRESS = (PROTOCOL = TCP)(HOST = 203.249.87.57)(PORT = 1521))
        )
        (CONNECT_DATA = 	(SID = orcl)
        )
        )';
        // 사용자명과 비밀번호
        $username = "DB502_PROJ_G3"; // DB 사용자명 입력
        $password = "1234"; // DB 비밀번호 입력

        // Oracle DB 연결
        $connect = oci_connect($username, $password, $db);
        if (!$connect) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        // 폼에서 입력받은 데이터
        $id = $_POST['id'];
        $password = $_POST['password'];
        $userlevel = $_POST['userlevel'];
        $name = $_POST['name'];
        $nickname = $_POST['nickname'];
        $email = $_POST['email'];
        $birthdate = $_POST['birthdate'];
        $phone = $_POST['phone'];

        // SQL INSERT 쿼리
        $sql = "INSERT INTO Users (ID, Password, UserLevel, Name, Nickname, Email, Birthdate, Phone) 
                VALUES (:id, :password, :userlevel, :name, :nickname, :email, TO_DATE(:birthdate, 'YYYY-MM-DD'), :phone)";

        // SQL 구문 분석
        $stid = oci_parse($connect, $sql);

        // 바인드 변수 설정
        oci_bind_by_name($stid, ":id", $id);
        oci_bind_by_name($stid, ":password", $password);
        oci_bind_by_name($stid, ":userlevel", $userlevel);
        oci_bind_by_name($stid, ":name", $name);
        oci_bind_by_name($stid, ":nickname", $nickname);
        oci_bind_by_name($stid, ":email", $email);
        oci_bind_by_name($stid, ":birthdate", $birthdate);
        oci_bind_by_name($stid, ":phone", $phone);

        // SQL 실행
        $result = oci_execute($stid);

        if ($result) {
            echo "<p>데이터가 성공적으로 추가되었습니다.</p>";
        } else {
            $e = oci_error($stid);
            echo "<p>오류 발생: " . htmlentities($e['message'], ENT_QUOTES) . "</p>";
        }

        // 연결 해제
        oci_free_statement($stid);
        oci_close($connect);
    }
    ?>
</body>
</html>


