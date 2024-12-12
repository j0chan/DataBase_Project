<?php
// DB 접속 정보 설정
$db  = '(DESCRIPTION =
	 (ADDRESS_LIST =
	  (ADDRESS = (PROTOCOL = TCP)(HOST = 203.249.97.58)(PORT = 1521))
	 )
	 (CONNECT_DATA =
	  (SID = orcl)
	 )
	)';

// ID AND PASSWORD
$username = '502_team3';
$password = '502team3';

// DB CONNECT TRY
$connect = oci_connect($username, $password, $db);

// if  error
if(!$connect) {
	$e = oci_error();
	trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>PHP-DB 연동</title>
</head>
<body>
	<h1>DB 연동 결과</h1>
	<?php
	// sql query
	$sql = "SELECT * FROM USERS";
	
	// sql 구문 피싱
	$stid = oci_parse($connect, $sql);

	oci_execute($stid);

	echo "<table border='1' cellpadding='5' cellspacing='0'>";
	echo "<tr>";

	$num_fields = oci_num_fields($stid);
	for ($i = 1; $i <= $num_fields; $i++) {
		echo "<th>" . htmlentities(oci_field_name($stid, $i), ENT_QUOTES) . "</th>";
	}
	echo "</tr>";

	while($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
		echo "<tr>";
		foreach($row as $item) {
			echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";

	oci_free_statement($stid);
	oci_close($connect);
?>
</body>
</html>
