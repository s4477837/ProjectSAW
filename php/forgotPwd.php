<?php
    if (!isset($_POST['email'])) {
        header('Location: forgotPwdHTML.php?err=1');
        exit();
    }

    $email = trim($_POST['email']);

    if (empty($email)) {
        header('Location: forgotPwdHTML.php?err=1');
        exit();
    }

    require_once('db/mysql_credentials.php');

    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    function update_pwd($email, $db_connection) {
        $pwdOriginal = generateRandomString(8);
        $pwd = sha1($pwdOriginal);
        if ($stmt = mysqli_prepare($db_connection, "UPDATE cliente
                    SET pword=?
                    WHERE email=?")) {
            mysqli_stmt_bind_param($stmt, "ss", $pwd, $email);
            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            if ($result)
                return $pwdOriginal;
        }
        return null;
    }

    $success = update_pwd($email, $con);

    mysqli_close($con);

    if ($success) {
        // Success message
        $sub = "Site - Reimpostazione password";
        $msg = "Gentile utente,\n
                la sua nuova password è la seguente: ". $success. "\n
                Consigliamo di cambiarla al prossimo accesso per
                aumentare la sicurezza del suo account\n\nStaff";
        $header = "From: Site <lorenzo.pagnoni.majo@gmail.com>\r\n";
        $s = mail($email, $sub, $msg, $header);
        VAR_DUMP($s);
        /*    header("Location: forgotPwdHTML.php?msg=1");
        else
            header("Location: forgotPwdHTML.php?err=6");*/
    } else {
        // Error message
        header("Location: forgotPwdHTML.php?err=5");
    }
?>