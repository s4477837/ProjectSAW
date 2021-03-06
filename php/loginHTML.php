<?php
	require_once('funzione.php');
	myHeader('LOGIN', false);
	printMessage();

	if (isset($_COOKIE['id'])) {
		require_once('db/mysql_credentials.php');
		if ($stmt = mysqli_prepare($con,
						'SELECT idCliente, email, nome, cognome, telefono
							FROM cliente
							WHERE idCliente=? AND
								cookie>\''. time(). '\'')) {
			mysqli_stmt_bind_param($stmt, 'i', $_COOKIE['id']);
			$result = mysqli_stmt_execute($stmt);
			if ($result) {
				mysqli_stmt_store_result($stmt);
				$norows = mysqli_stmt_num_rows($stmt);
				if ($norows == 1) {
					mysqli_stmt_bind_result($stmt, $id, $em, $nome, $cognome, $tel);
					mysqli_stmt_fetch($stmt);
					mysqli_stmt_free_result($stmt);
					mysqli_stmt_close($stmt);
					sessionUtente($id, $em, $nome, $cognome, $tel);
					header('Location: index.php');
				}
			}
		}
		mysqli_close($con);
	}
?>
<div class='container-fluid'>
	<form action='login.php' method='POST'>
		<!--<label for='email'>email</label>-->
		<input type='email' name='email' placeholder='email' required>
		<!--<label for='password'>password</label>-->
		<input type='password' name='pass' placeholder='password' required>
		<input type='submit' value='Submit'>
		<div class='field-group'>
			<div><input type='checkbox' name='remember-me' value='remember-me'></div>
			<label for='remember-me'>Remember me</label>
		</div>
	</form>
	<a href='forgotPwdHTML.php'>Recupera la password</a>
</div>
<?php
	include('../html/footer.html');
?>