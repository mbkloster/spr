<?php

$starttime = microtime();

require('../include/include_manager.php');

if ($action == 'myprofile')
{
	$output->js_files[] = "$local_url/md5.js";
	$output->js_files[] = "$local_url/forms.js";
	$output->subtitle = 'My Profile';
	$output->addl("<form action=\"user.php\" method=\"post\">",1);
	$output->addl("<input type=\"hidden\" name=\"action\" value=\"myprofile2\" />",1);
	$output->addl("<table style=\"width: 55%;\" align=\"center\" cellspacing=\"0\">",1);
	$output->addl("<tr>",2);
	$output->addl("<th colspan=\"2\">Edit My Profile</th>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td style=\"width: 50%;\"><b>UserName:</b></td>",3);
	$output->addl("<td style=\"width: 50%;\">" . htmlspecialchars($spruser['username']) . "</td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td style=\"width: 50%;\"><b>Email Address:</b></td>",3);
	$output->addl("<td style=\"width: 50%;\"><input type=\"text\" name=\"email\" size=\"25\" maxlength=\"100\" value=\"" . htmlspecialchars($spruser['email']) . "\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td style=\"width: 50%;\"><b>Default Author Name:</b></td>",3);
	$output->addl("<td style=\"width: 50%;\"><input type=\"text\" name=\"authorname\" size=\"25\" maxlength=\"100\" value=\"" . htmlspecialchars($spruser['authorname']) . "\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td style=\"text-align: center;\" colspan=\"2\"><input type=\"submit\" class=\"button\" value=\"Update Profile\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("</table>",1);
	$output->addl("</form>",1);
	$output->addl("<form action=\"user.php\" method=\"post\" onsubmit=\"if (field_len_check(this.password,'Your original password'," . $options['min_password_len'] . "," . $options['max_password_len'] . ") && field_len_check(this.newpassword,'Your new password'," . $options['min_password_len'] . "," . $options['max_password_len'] . ") && field_len_check(this.newpassword2,'Your new password confirmation'," . $options['min_password_len'] . "," . $options['max_password_len'] . ")) { md5hash(this.password,this.hashedpassword); md5hash(this.newpassword,this.hashednewpassword); md5hash(this.newpassword2,this.hashednewpassword2); this.submit(); } else { void(0); return false; } \">",1);
	$output->addl("<input type=\"hidden\" name=\"action\" value=\"mypassword2\" />",1);
	$output->addl("<input type=\"hidden\" name=\"hashedpassword\" value=\"\" />",1);
	$output->addl("<input type=\"hidden\" name=\"hashednewpassword\" value=\"\" />",1);
	$output->addl("<input type=\"hidden\" name=\"hashednewpassword2\" value=\"\" />",1);
	$output->addl("<table style=\"width: 55%;\" align=\"center\" cellspacing=\"0\">",1);
	$output->addl("<tr>",2);
	$output->addl("<th colspan=\"2\">Change My Password</th>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td style=\"width: 50%;\"><b>Current Password:</b></td>",3);
	$output->addl("<td style=\"width: 50%;\"><input type=\"password\" name=\"password\" size=\"25\" maxlength=\"100\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td style=\"width: 50%;\"><b>New Password:</b></td>",3);
	$output->addl("<td style=\"width: 50%;\"><input type=\"password\" name=\"newpassword\" size=\"25\" maxlength=\"100\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td style=\"width: 50%;\"><b>Confirm New Password:</b></td>",3);
	$output->addl("<td style=\"width: 50%;\"><input type=\"password\" name=\"newpassword2\" size=\"25\" maxlength=\"100\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td style=\"text-align: center;\" colspan=\"2\"><input type=\"submit\" class=\"button\" value=\"Change Password\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("</table",1);
	$output->addl("</form>",1);
	$output->display();
}
elseif ($action == 'myprofile2')
{
	if (strpos($_POST['email'],'@') !== false && strpos($_POST['email'],'.') !== false && strlen($_POST['email']) >= 6 && strpos($_POST['email'],'@') < strrpos($_POST['email'],'.') && strpos($_POST['email'],'@') == strrpos($_POST['email'],'@') && trim($_POST['authorname']) != '')
	{
		$db->query("UPDATE user SET email = '" . addslashes($_POST['email']) . "', authorname = '" . addslashes($_POST['authorname']) . "' WHERE userid = '" . $spruser['userid'] . "' LIMIT 1");
		if ($spruser['email'] != $_POST['email'])
		{
			$db->query("UPDATE entry SET email = '" . addslashes($_POST['email']) . "' WHERE userid = '" . $spruser['userid'] . "'");
		}
		$output->addl("<p>Your information has been updated successfully. Thanks for playing, " . htmlspecialchars($spruser['username']) . "!</p>");
		$output->display();
	}
	else
	{
		$output->addl("<p>It seems that you did not fill in all fields correctly. Make sure that they're filled in, and that your email address is valid, and try again.</p>",1);
		$output->display();
	}
}
elseif ($action == 'mypassword2')
{
	if ($_POST['hashedpassword'] != '' && $_POST['password'] == '' && $_POST['hashednewpassword'] != '' && $_POST['newpassword'] == '' && $_POST['hashednewpassword2'] != '' && $_POST['newpassword2'] == '')
	{
		$password = $_POST['hashedpassword'];
		$newpassword = $_POST['hashednewpassword'];
		$newpassword2 = $_POST['hashednewpassword2'];
	}
	else
	{
		if (strlen($_POST['password']) < $options['min_password_len'] || strlen($_POST['newpassword']) < $options['min_password_len'] || strlen($_POST['newpassword2']) < $options['min_password_len'])
		{
			$output->addl("<p>It seems you haven't filled in all of the fields properly. Passwords must be at least ".$options['min_password_len']." and no more than ".$options['max_password_len']." characters long. Please correct this and try again.</p>",1);
			$output->display();
			exit;
		}
		else
		{
			$password = md5($_POST['password']);
			$newpassword = md5($_POST['newpassword']);
			$newpassword2 = md5($_POST['newpassword2']);
		}
	}
	$user = $db->query_first("SELECT count(*) FROM user WHERE userid = '" . $spruser['userid'] . "' AND password = md5(CONCAT(salt,'" . $password . "'))");
	if ($user['count(*)'])
	{
		if ($newpassword == $newpassword2)
		{
			if ($newpassword != $password)
			{
				$salt = chr(rand(1,255)).chr(rand(1,255)).chr(rand(1,255));
				$db->query("UPDATE user SET salt = '" . addslashes($salt) . "', password = md5('" . addslashes($salt) . "$newpassword') WHERE userid = '" . $spruser['userid'] . "' LIMIT 1");
				$output->subtitle = 'Changed Password';
				$output->addl("<p>Your password has been changed. Thanks for your commitment to <i>security</i>, " . htmlspecialchars($spruser['username']) . ".</p>",1);
				$output->addl("<p>You will now need to log in again to verify the password change.</p>",1);
				$output->addl("<p><a href=\"user.php?action=myprofile\">My Profile page.</a></p>",1);
				$output->display();
			}
			else
			{
				$output->addl("<p>Your new password seems to be indentical to your old password. That's kind of stupid. What kind of stupid faggot would change their password to the same thing as it was before?</p>",1);
				$output->addl("<p>Oh, that's right. <i>You.</i></p>",1);
				$output->display();
			}
		}
		else
		{
			$output->addl("<p>Your new password does not match your new password confirmation. Make sure they match and try again.</p>",1);
			$output->display();
		}
	}
	else
	{
		$output->addl("<p>The password you entered for your account does not seem to be correct. Make sure it was typed properly (capitalization matters) and try again.</p>",1);
		$output->display();
	}
}

?>