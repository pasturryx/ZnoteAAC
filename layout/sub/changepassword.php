<?php require_once 'engine/init.php';
protect_page();

if (empty($_POST) === false) {
	/* Token used for cross site scripting security */
	if (!Token::isValid($_POST['token'])) {
		$errors[] = 'Token is invalid.';
	}
	
	$required_fields = array('current_password', 'new_password', 'new_password_again');
	
	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = 'You need to fill in all fields.';
			break 1;
		}
	}
	
	$pass_data = user_data($session_user_id, 'password');
	//$pass_data['password'];
	// $_POST['']
	
	// .3 compatibility
	if ($config['TFSVersion'] == 'TFS_03' && $config['salt'] === true) {
		$salt = user_data($session_user_id, 'salt');
	}
	if (sha1($_POST['current_password']) === $pass_data['password'] || $config['TFSVersion'] == 'TFS_03' && $config['salt'] === true && sha1($salt['salt'].$_POST['current_password']) === $pass_data['password']) {
		if (trim($_POST['new_password']) !== trim($_POST['new_password_again'])) {
			$errors[] = 'Your new passwords do not match.';
		} else if (strlen($_POST['new_password']) < 6) {
			$errors[] = 'Your new passwords must be at least 6 characters.';
		} else if (strlen($_POST['new_password']) > 12) {
			$errors[] = 'Your new passwords must be less than 12 characters.';
		}
	} else {
		$errors[] = 'Your current password is incorrect.';
	}
}

?>

<?php
if (isset($_GET['success']) && empty($_GET['success'])) {
	echo 'Your password has been changed.<br>You will need to login again with the new password.';
	session_destroy();
	header("refresh:2;url=index.php");
	exit();
} else {
	if (empty($_POST) === false && empty($errors) === true) {
		//Posted the form without errors
		if ($config['TFSVersion'] == 'TFS_02' || $config['TFSVersion'] == 'TFS_10') {
			user_change_password($session_user_id, $_POST['new_password']);
		} else if ($config['TFSVersion'] == 'TFS_03') {
			user_change_password03($session_user_id, $_POST['new_password']);
		}
		header('Location: changepassword.php?success');
	} else if (empty($errors) === false){
		echo '<font color="red"><b>';
		echo output_errors($errors);
		echo '</b></font>';
	}
	?>
	<head>
    <meta charset="UTF-8">
   <title>Change Password</title>
    <style>
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <p style="margin: 5px 5px 10px 5px;">Please enter your current password and a new password. Please verify your new password by entering it twice.</p>
    <form id="changePasswordForm" action="" method="post">
        <div class="RowsWithOverEffect" style="margin: 5px;">
            <div class="TableContainer"> 
                <div class="CaptionContainer">
                    <div class="CaptionInnerContainer">
                        <span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                        <span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                        <span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                        <span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                        <div class="Text">Change password</div>
                        <span class="CaptionVerticalRight" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                        <span class="CaptionBorderBottom" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                        <span class="CaptionEdgeLeftBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                        <span class="CaptionEdgeRightBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                    </div>
                </div>
                <table class="Table3" cellspacing="1">
                    <tr>
                        <td>
                            <div class="InnerTableContainer">
                                <div class="TableShadowContainerRightTop">
                                    <div class="TableShadowRightTop" style="background-image:url(layout/tibia_img/table-shadow-rt.gif);"></div>
                                </div>
                                <div class="TableContentAndRightShadow" style="background-image:url(layout/tibia_img/table-shadow-rm.gif);">
                                    <div class="TableContentContainer">
                                        <table class="TableContent" width="100%" style="border:1px solid #faf0d7;">
                                            <tr>
                                            	  <td style="height: 20px;">Current password:</td>
                                                <!-- <td>Current password:</td> -->
<td>
    <input type="password" id="current_password" name="current_password">
    <span id="current-password-message" class="error"></span>
</td>
                                            </tr>
                                            <tr>
                                                <td>New password:</td>
                                                <td>
                                                    <input type="password" id="new_password" name="new_password">
                                                    <span id="password-error" class="error"></span>
                                                    <span id="password-success" class="success"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>New password again:</td>
                                                <td>
                                                    <input type="password" id="new_password_again" name="new_password_again">
                                                    <span id="password_again-error" class="error"></span>
                                                    <span id="password_again-success" class="success"></span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="TableShadowContainer">
                                    <div class="TableBottomShadow" style="background-image:url(layout/tibia_img/table-shadow-bm.gif);">
                                        <div class="TableBottomLeftShadow" style="background-image:url(layout/tibia_img/table-shadow-bl.gif);"></div>
                                        <div class="TableBottomRightShadow" style="background-image:url(layout/tibia_img/table-shadow-br.gif);"></div>
                                    </div>
                                </div>
                                <br>
                                <center>
                                    <input name="submit" style="margin: 0 5px;display: inline-block;" class="BigButton btn" value="Change Password" type="submit" >
                                    <a href="myaccount.php">
                                        <div class="BigButton btn" style="margin: 0 5px;display: inline-block;background-image:url(layout/tibia_img/sbutton.gif)">Back</div>
                                    </a>
                                </center>
                                <br>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>

				


							
					</div>
				</div>
			<?php
				/* Form file */
				Token::create();
			?>
	</form>
<?php
}
?>


 <script>
        document.getElementById('new_password').addEventListener('input', checkPasswordMatch);
        document.getElementById('new_password_again').addEventListener('input', checkPasswordMatch);

        function checkPasswordMatch() {
            const password = document.getElementById('new_password').value;
            const passwordAgain = document.getElementById('new_password_again').value;

            const passwordError = document.getElementById('password-error');
            const passwordSuccess = document.getElementById('password-success');

            const passwordAgainError = document.getElementById('password_again-error');
            const passwordAgainSuccess = document.getElementById('password_again-success');

            // Clear any previous messages
            passwordError.textContent = '';
            passwordSuccess.textContent = '';
            passwordAgainError.textContent = '';
            passwordAgainSuccess.textContent = '';

            // Check if the password field is empty
            if (password.length === 0 && passwordAgain.length === 0) {
                passwordError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Password is required.</b>';
                passwordAgainError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Confirm password is required.</b>';
                return;
            }

            if (password.length === 0) {
                passwordError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Password is required.</b>';
                return;
            }

            if (passwordAgain.length === 0) {
                passwordAgainError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Confirm password is required.</b>';
                return;
            }

            // Check if the passwords are of valid length
            if (password.length < 6) {
                passwordError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Your password must be at least 6 characters.</b>';
                passwordAgainError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Your password must be at least 6 characters.</b>';
                return;
            }

            if (password.length > 12) {
                passwordError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Your password must be less than 12 characters.</b>';
                passwordAgainError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Your password must be less than 12 characters.</b>';
                return;
            }

            // Check if the passwords match
            if (password !== passwordAgain) {
                passwordError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Passwords do not match.</b>';
                passwordAgainError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Passwords do not match.</b>';
            } else {
                passwordSuccess.innerHTML = '<img src="layout/img/ok.gif" alt="Success"> <b>Passwords match.</b>';
                passwordAgainSuccess.innerHTML = '<img src="layout/img/ok.gif" alt="Success"> <b>Passwords match.</b>';
            }
        }

     document.getElementById('current_password').addEventListener('input', function() {
    const currentPassword = this.value;
    const messageSpan = document.getElementById('current-password-message');

    // Clear previous message
    messageSpan.textContent = '';

    if (currentPassword.length === 0) {
        messageSpan.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Current password is required.</b>';
        messageSpan.className = 'error';
        return; // Exit the function early if current password is empty
    }

    fetch('validate_password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'current_password=' + encodeURIComponent(currentPassword)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageSpan.innerHTML = '<img src="layout/img/ok.gif" alt="Success"> <b>' + data.success + '</b>';
            messageSpan.className = 'success';
        } else {
            messageSpan.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>' + data.error + '</b>';
            messageSpan.className = 'error';
        }
    })
    .catch(error => {
        messageSpan.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Error occurred while validating password.</b>';
        messageSpan.className = 'error';
    });
});
    </script>
    <style>
    #name-error, #name-success {
    height: 20px; /* Adjust the height as needed */
    line-height: 20px; /* Adjust the line-height to vertically center the text */
    overflow: hidden; /* Hide any content that exceeds the fixed height */
}
</style>