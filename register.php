<?php
require_once 'engine/init.php';
logged_in_redirect();
include 'layout/overall/header.php';
require_once 'engine/function/users.php';
require_once 'config.countries.php';

if (empty($_POST) === false) {
	// $_POST['']
	$required_fields = array('username', 'password', 'password_again', 'email', 'selected');
	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = 'You need to fill in all fields.';
			break 1;
		}
	}

	// check errors (= user exist, pass long enough
	if (empty($errors) === true) {
		/* Token used for cross site scripting security */
		if (!Token::isValid($_POST['token'])) {
			$errors[] = 'Token is invalid.';
		}

		if ($config['use_captcha']) {
			if(!verifyGoogleReCaptcha($_POST['g-recaptcha-response'])) {
				$errors[] = "Please confirm that you're not a robot.";
			}
		}

		if (user_exist($_POST['username']) === true) {
			$errors[] = 'Sorry, that username already exist.';
		}

		// Don't allow "default admin names in config.php" access to register.
		$isNoob = in_array(strtolower($_POST['username']), $config['page_admin_access']) ? true : false;
		if ($isNoob) {
			$errors[] = 'This account name is blocked for registration.';
		}
		if ($config['ServerEngine'] !== 'OTHIRE' && $config['client'] >= 830) {
			//if (preg_match("/^[a-zA-Z0-9]+$/", $_POST['username']) == false) {
			//	$errors[] = 'Your account name can only contain characters a-z, A-Z and 0-9.';
						if (preg_match("/^[0-9]+$/", $_POST['username']) == false) {
				$errors[] = 'Your account name can only contain numbers 0-9. DO NOT USE MORE THAN 8 DIGITS.';
			}
		} else {
			if (preg_match("/^[0-9]+$/", $_POST['username']) == false) {
				$errors[] = 'Your account can only contain numbers 0-9.';
			}
			//if ((int)$_POST['username'] < 100000 || (int)$_POST['username'] > 999999999) {
			//	$errors[] = 'Your account number must be a value between 6-8 numbers long. DO NOT USE MORE THAN 8 DIGITS.';
			  if ((int)$_POST['username'] < 10000000 || (int)$_POST['username'] > 99999999) {
              $errors[] = 'Your account name must be an 8 digit long number. NO MORE THAN THAT.';
			}
		}
		// name restriction
		$resname = explode(" ", $_POST['username']);
		foreach($resname as $res) {
			if(in_array(strtolower($res), $config['invalidNameTags'])) {
				$errors[] = 'Your username contains a restricted word.';
			}
			else if(strlen($res) == 1) {
				$errors[] = 'Too short words in your name.';
			}
		}
		if (strlen($_POST['username']) > 8) {
			$errors[] = 'Do not use more than 8 digits, in account section.';
		}
		// end name restriction
		if (strlen($_POST['password']) < 6) {
			$errors[] = 'Your password must be at least 6 characters.';
		}
		if (strlen($_POST['password']) > 12) {
			$errors[] = 'Your password must be less than 12 characters.';
		}
		if ($_POST['password'] !== $_POST['password_again']) {
			$errors[] = 'Your passwords do not match.';
		}
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
			$errors[] = 'A valid email address is required.';
		}
		if (user_email_exist($_POST['email']) === true) {
			$errors[] = 'That email address is already in use.';
		}
		if ($_POST['selected'] != 1) {
			$errors[] = 'You are only allowed to have an account if you accept the rules.';
		}
		if ($config['validate_IP'] === true) {
			if (validate_ip(getIP()) === false) {
				$errors[] = 'Failed to recognize your IP address. (Not a valid IPv4 address).';
			}
		}
		if (strlen($_POST['flag']) < 1) {
			$errors[] = 'Please choose country.';
		}
	}
}

?>

<!--  -->
<!-- <h1>Register Account</h1> -->
<?php
if (isset($_GET['success']) && empty($_GET['success'])) {
	if ($config['mailserver']['register']) {
		?>
		<h1>Email authentication required</h1>
		<p>We have sent you an email with an activation link to your submitted email address.</p>
		<p>If you can't find the email within 5 minutes, check your <strong>junk/trash inbox (spam filter)</strong> as it may be mislocated there.</p>
		<?php
	} else echo 'Congratulations! Your account has been created. You may now login to create a character.';
} elseif (isset($_GET['authenticate']) && empty($_GET['authenticate'])) {
	// Authenticate user, fetch user id and activation key
	$auid = (isset($_GET['u']) && (int)$_GET['u'] > 0) ? (int)$_GET['u'] : false;
	$akey = (isset($_GET['k']) && (int)$_GET['k'] > 0) ? (int)$_GET['k'] : false;
	// Find a match
	$user = mysql_select_single("SELECT `id`, `active`, `active_email` FROM `znote_accounts` WHERE `account_id`='$auid' AND `activekey`='$akey' LIMIT 1;");
	if ($user !== false) {
		$user = (int) $user['id'];
		$active = (int) $user['active'];
		$active_email = (int) $user['active_email'];
		// Enable the account to login
		if ($active == 0 || $active_email == 0) {
			mysql_update("UPDATE `znote_accounts` SET `active`='1', `active_email`='1' WHERE `id`= $user LIMIT 1;");
		}
		echo '<h1>Congratulations!</h1> <p>Your account has been created. You may now login to create a character.</p>';
	} else {
		echo '<h1>Authentication failed</h1> <p>Either the activation link is wrong, or your account is already activated.</p>';
	}
} else {
	if (empty($_POST) === false && empty($errors) === true) {
		if ($config['log_ip']) {
			znote_visitor_insert_detailed_data(1);
		}

		//Register
		if ($config['ServerEngine'] !== 'OTHIRE') {
			$register_data = array(
				'name'		=>	$_POST['username'],
				'password'	=>	$_POST['password'],
				'email'		=>	$_POST['email'],
				'created'	=>	time(),
				'ip'		=>	getIPLong(),
				'flag'		=> 	$_POST['flag']
			);
		} else {
			$register_data = array(
				'id'		=>	$_POST['username'],
				'password'	=>	$_POST['password'],
				'email'		=>	$_POST['email'],
				'created'	=>	time(),
				'ip'		=>	getIPLong(),
				'flag'		=> 	$_POST['flag']
			);
		}

		user_create_account($register_data, $config['mailserver']);
		if (!$config['mailserver']['debug']) header('Location: register.php?success');
		exit();
		//End register

	} else if (empty($errors) === false){
		echo '<font color="red"><b>';
		echo output_errors($errors);
		echo '</b></font>';
	}
?>
<!-- <div class="Rows]WithOverEffect" style="margin: 5px;"> -->
	 <div class="TableContainer" style="margin-top: 1cm; margin-bottom: 1cm;">

				<div class="TableContainer"> 
				<div class="CaptionContainer">
					<div class="CaptionInnerContainer">
						<span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
						<span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
							<div class="Text">Create Account</div>
						<span class="CaptionVerticalRight" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
						<span class="CaptionBorderBottom" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
						<span class="CaptionEdgeLeftBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
						<span class="CaptionEdgeRightBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
					</div>
				</div>
				<table class="Table3" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<div class="InnerTableContainer">
								<table style="width:100%;">
									<tr>
										<td>
											<div class="TableShadowContainerRightTop">
												<div class="TableShadowRightTop" style="background-image:url(layout/tibia_img/table-shadow-rt.gif);"></div>
											</div>
											<div class="TableContentAndRightShadow" style="background-image:url(layout/tibia_img/table-shadow-rm.gif);"> 
												<div class="TableContentContainer">
													<table class="TableContent" width="100%" style="border:1px solid #faf0d7;">
	<!-- <form action="" method="post"> -->
  <form action="" method="post">
  <ul>
    <li>
      <b>Account:<br></b>
      <input type="text" name="username" id="username" onblur="checkUsername()">
      <span id="username-error" style="color: red;"></span>
      <span id="username-success" style="color: green;"></span>
    </li>
    <li>
      <b>Password:<br></b>
      <input type="password" name="password" id="password" onblur="checkPasswordMatch()">
      <span id="password-error" style="color: red;"></span>
      <span id="password-success" style="color: green;"></span>
    </li>
    <li>
      <b>Password again:<br></b>
      <input type="password" name="password_again" id="password_again" onblur="checkPasswordMatch()">
      <span id="password_again-error" style="color: red;"></span>
      <span id="password_again-success" style="color: green;"></span>
    </li>
    <li>
      <b>Email:<br></b>
      <input type="text" name="email" id="email" onblur="checkEmail()">
      <span id="email-error" style="color: red;"></span>
      <span id="email-success" style="color: green;"></span>
    </li>
    <!-- Rest of your form fields -->

                  <!-- <b>Country:<br></b> -->
        <!-- <select name="flag"> -->
        	<!-- <select name="flag" id="countrySelect"> -->
            <!-- <option value="">(Please choose)</option> -->
            <!-- <php -->
            <!-- foreach(array('pl', 'se', 'br', 'us', 'gb', ) as $c) -->
                <!-- echo '<option value="' . $c . '">' . $config['countries'][$c] . '</option>'; -->

                <!-- echo '<option value="">----------</option>'; -->
                <!-- foreach($config['countries'] as $code => $c) -->
                    <!-- echo '<option value="' . $code . '">' . $c . '</option>'; -->
            <!-- > -->
        <!-- </select> -->
            <!-- </li> -->
 <li>
  
    <b>Country:<br></b>
<select name="flag" id="countrySelect" style="width: 180px;">
    <option value="">(Please wait, detecting your country...)</option>
</select>
</li>

			<?php
			if ($config['use_captcha']) {
				?>
				<li>
					 <div class="g-recaptcha" data-sitekey="<?php echo $config['captcha_site_key']; ?>"></div>
				</li>
				<?php
			}
			?>
			<li>
				<h2>Server Rules</h2>
				<p>The golden rule: Have fun.</p>
				<p>If you get pwn3d, don't hate the game.</p>
				<p>No <a href='https://en.wikipedia.org/wiki/Cheating_in_video_games' target="_blank">cheating</a> allowed.</p>
				<p>No <a href='https://en.wikipedia.org/wiki/Video_game_bot' target="_blank">botting</a> allowed.</p>
				<p>The staff can delete, ban, do whatever they want with your account and your <br>
					submitted information. (Including exposing and logging your IP).</p>
			</li>
			<li>
				Do you agree to follow the server rules?<br>
				<select name="selected">
				  <option value="0">Umh...</option>
				  <option value="1">Yes.</option>
				  <option value="2">No.</option>
				</select>
			</li>
			<?php
				/* Form file */
				Token::create();
			?>
			<li>
					<input type="submit" value="Create Account" div class="BigButton btn" style="margin: 0 5px;display: inline-block;background-image:url(layout/tibia_img/button_green.gif)">
				<!-- <input type="submit" value="Create Account"> -->
			</li>
		</ul>
	</form>
	 </tr>
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
								</td>
							 </tr>
					   </table>
					</div>
					</td>
					</tr>
					
				</table>
			</div>
		</div>
			</form>       
			<!-- <script> -->
				<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
				<!-- <script src="https://cdn.jsdelivr.net/npm/slidesjs@3/dist/jquery.slides.min.js"></script> -->
				<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/slidesjs/3.0/jquery.slides.min.js"></script> -->
				<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slidesjs/3.0/jquery.slides.min.js"></script>
<script>
    $(document).ready(function() {
        // Your existing code

        // Initialize SlidesJS
        $("#slides").slidesjs({
            width: 940,
            height: 528,
            play: {
                active: true,
                auto: true,
                interval: 4000,
                swap: true
            }
        });

        // Function to get user's country based on IP
        function getUserCountry() {
            $.ajax({
                url: 'layout/sub/check_country.php', // Updated to the correct path
                type: 'GET',
                success: function(data) {
                    console.log('Response:', data); // Log the response data

                    // No need to parse the response, it's already a JSON object
                    if (data.country_code) {
                        // Add an option for the detected country and select it by default
                        $('#countrySelect').html('<option value="' + data.country_code + '" selected="selected">' + data.country_name + '</option>');
                    } else {
                        // If country code is not detected, display a default option
                        $('#countrySelect').html('<option value="">(Country detection failed, please select)</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    // If an error occurs during detection, display a default option
                    $('#countrySelect').html('<option value="">(Country detection failed, please select)</option>');
                }
            });
        }

        // Call the function to get user's country on page load
        getUserCountry();
    });

    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const passwordAgain = document.getElementById('password_again').value;

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

    function checkUsername() {
        const username = document.getElementById('username').value;
        const usernameError = document.getElementById('username-error');
        const usernameSuccess = document.getElementById('username-success');

        // Clear previous messages
        usernameError.textContent = '';
        usernameSuccess.textContent = '';

        if (username.length === 0) {
            usernameError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Username is required.</b>';
            return;
        }

        if (/\D/.test(username)) {
            usernameError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Your account cannot contain letters or symbols.</b>';
            return;
        }

        if (!/^\d{1,8}$/.test(username)) {
            usernameError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Your account name must be between 1 and 8 digits.</b>';
            return;
        }

        console.log('Checking username:', username); // Debugging: Log username

        fetch('layout/sub/check_username.php?username=' + encodeURIComponent(username))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Response:', data); // Debugging: Log response data
                if (data.error) {
                    usernameError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>' + data.error + '</b>';
                } else if (data.exists !== undefined) {
                    if (data.exists) {
                        usernameError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Username is already in use.</b>';
                    } else {
                        usernameSuccess.innerHTML = '<img src="layout/img/ok.gif" alt="Success"> <b>Username is available.</b>';
                    }
                } else {
                    usernameError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Unexpected response format.</b>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                usernameError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Error checking username.</b>';
            });
    }

    function checkEmail() {
        const email = document.getElementById('email').value;
        const emailError = document.getElementById('email-error');
        const emailSuccess = document.getElementById('email-success');

        // Clear any previous error or success messages
        emailError.textContent = '';
        emailSuccess.textContent = '';

        if (email.length === 0) {
            emailError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Email is required.</b>';
            return;
        }

        fetch('layout/sub/check_email.php?email=' + encodeURIComponent(email))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Response:', data); // Debugging: Log response data
                if (data.error) {
                    emailError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> ' + data.error;
                } else if (data.exists !== undefined) {
                    if (data.exists) {
                        emailError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Email is already in use.</b>';
                    } else {
                        emailSuccess.innerHTML = '<img src="layout/img/ok.gif" alt="Success"> <b>Email is available.</b>';
                    }
                } else {
                    emailError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Unexpected response format.</b>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                emailError.innerHTML = '<img src="layout/img/nok.gif" alt="Error"> <b>Error checking email.</b>';
            });
    }


</script>

<?php
}
include 'layout/overall/footer.php';
?>
