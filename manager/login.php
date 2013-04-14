<?php

$starttime = microtime();
$skiplogin = 1;

require("../include/include_manager.php");

if ($action == 'login')
{
	if (trim($_POST['username']) != '' && (trim($_POST['password']) != '' || $_POST['hashedpassword'] != '') && $_POST['hashedpassword'] != 'd41d8cd98f00b204e9800998ecf8427e')
	{
		if ($_POST['hashedpassword'] == '')
		{
			$password = md5($_POST['password']);
		}
		else
		{
			$password = $_POST['hashedpassword'];
		}
		$attempts = $db->query_first("SELECT count(*) FROM loginlog WHERE ipaddress = '" . $_SERVER['REMOTE_ADDR'] . "' AND successful = 0 AND date > " . (gmdate("U")-($options['login_attempt_duration']*60)) . " LIMIT 1"); //FINISH THIS
		if ($attempts['count(*)'] < $options['max_login_attempts'])
		{
			$user = $db->query_first("SELECT userid, username, password, salt, isactive FROM user WHERE username = '" . addslashes($_POST['username']) . "' LIMIT 1");
			if (is_array($user)) // User exists, check password and activity...
			{
				if (md5($user['salt'] . $password) == $user['password'])
				{
					if ($user['isactive'])
					{
						$db->query("INSERT INTO loginlog (date, ipaddress, userid, username, successful, useragent)"
						.        "\nVALUES ('" . gmdate("U") . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $user['userid'] . "', '" . addslashes($user['username']) . "', '1', '" . addslashes($_SERVER['HTTP_USER_AGENT']) . "')");
						$db->query("UPDATE user SET lastlogindate = '" . gmdate("U") . "' WHERE userid = '" . $user['userid'] . "' LIMIT 1");
						setcookie("userid",$user['userid'],(time()+788400000),"/",".senselesspoliticalramblings.com");
						setcookie("password",$password,(time()+788400000),"/",".senselesspoliticalramblings.com");
						// Now forward them to where they wanted to go.
						header("Location: " . $server_url . $_POST['destination']);
						exit;
					}
					else
					{
						$db->query("INSERT INTO loginlog (date, ipaddress, userid, username, successful, useragent)"
						.        "\nVALUES ('" . gmdate("U") . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $user['userid'] . "', '" . addslashes($user['username']) . "', '2', '" . addslashes($_SERVER['HTTP_USER_AGENT']) . "')");
						$output->use_header = 0;
						$output->use_footer = 0;
						$output->addl("<p>User " . htmlspecialchars($user['username']) . " is not active and cannot be logged into. Please consider why.</p>",1);
						$output->display();
					}
				}
				else
				{
					$db->query("INSERT INTO loginlog (date, ipaddress, userid, username, successful, useragent)"
					.        "\nVALUES ('" . gmdate("U") . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $user['userid'] . "', '" . addslashes($user['username']) . "', '0', '" . addslashes($_SERVER['HTTP_USER_AGENT']) . "')");
					$output->use_header = 0;
					$output->use_footer = 0;
					$output->addl("<p>Bad password for " . htmlspecialchars($user['username']) . ". Idiot.</p>",1);
					$output->addl("<p>You have used up " . ($attempts['count(*)']+1) . " of your " . $options['max_login_attempts'] . " attempts for this " . $options['login_attempt_duration'] . "-minute duration. After you reach or exceed this limit, you will be unable to login until the duration passes.</p>",1);
					$output->display();
				}
			}
			else
			{
				$db->query("INSERT INTO loginlog (date, ipaddress, userid, username, successful, useragent)"
				.        "\nVALUES ('" . gmdate("U") . "', '" . $_SERVER['REMOTE_ADDR'] . "', '0', '" . addslashes($_POST['username']) . "', '0', '" . addslashes($_SERVER['HTTP_USER_AGENT']) . "')");
				$output->use_header = 0;
				$output->use_footer = 0;
				$output->addl("<p>Huh? I have no idea who the fuck \"" . htmlspecialchars($_POST['username']) . "\" is.</p>",1);
				$output->addl("<p>You have used up " . ($attempts['count(*)']+1) . " of your " . $options['max_login_attempts'] . " attempts for this " . $options['login_attempt_duration'] . "-minute duration. After you reach or exceed this limit, you will be unable to login until the duration passes.</p>",1);
				$output->display();
			}
		}
		else
		{
			$output->use_header = 0;
			$output->use_footer = 0;
			$output->addl("<p>You have used up your " . $options['max_login_attempts'] . " login attempts in this " . $options['login_attempt_duration'] . "-minute duration. Please check back later and try again.</p>",1);
			$output->display();
		}
	}
	else
	{
		$output->use_header = 0;
		$output->use_footer = 0;
		$output->addl("<p>You did not fill in your username or password correctly. Please do that and try again.</p>",1);
		$output->display();
	}
}
elseif ($action == 'logout')
{
	if (isset($_COOKIE['userid']) && isset($_COOKIE['password']))
	{
		setcookie('userid','',(time()+1),"/",".senselesspoliticalramblings.com");
		setcookie('password','',(time()+1),"/",".senselesspoliticalramblings.com");
		$output->use_header = 0;
		$output->use_footer = 0;
		$output->addl("<p>All cookies cleared. You have been logged out!</p>",1);
		$output->addl("<p><a href=\"./\">Back to Manager Home</a></p>",1);
		$output->display();
	}
	else
	{
		$output->use_header = 0;
		$output->use_footer = 0;
		$output->addl("<p>You're either a bad liar or a naughty, naughty person. You don't appear to be logged in.</p>",1);
		$output->display();
	}
}

?>