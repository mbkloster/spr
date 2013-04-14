<?php

$starttime = microtime();

require("../include/include_manager.php");

if (trim($action) == '')
{
	$action = 'manage';
}

if ($action == 'manage')
{
	$output->subtitle = 'Recent Updates';
	$output->addl("<h2>Recent Updates</h2>",1);
	$output->addl("<ul>",1);
	$db->query("SELECT recentupdateid, title FROM recentupdate ORDER BY date DESC");
	while ($recentupdate = $db->fetch_array())
	{
		$output->addl("<li><b>" . htmlspecialchars($recentupdate['title']) . "</b> <a href=\"recentupdate.php?action=edit&amp;recentupdateid=" . $recentupdate['recentupdateid'] . "\">[edit]</a> <a href=\"recentupdate.php?action=delete&amp;recentupdateid=" . $recentupdate['recentupdateid'] . "\">[delete]</a></li>",2);
	}
	$output->addl("</ul>",1);
	$output->addl("<p><a href=\"recentupdate.php?action=add\">Add a new Recent Update</a></p>",1);
	$output->display();
}
elseif ($action == 'add')
{
	$output->subtitle = 'Add Recent Update';
	$output->addl("<form action=\"recentupdate.php\" method=\"post\">",1);
	$output->addl("<input type=\"hidden\" name=\"action\" value=\"add2\" />",1);
	$output->addl("<table width=\"45%\" align=\"center\" cellspacing=\"0\">",1);
	$output->addl("<tr>",2);
	$output->addl("<th colspan=\"2\">Add Recent Update</th>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td style=\"width: 50%;\"><b>Update URL:</b></td>",3);
	if (isset($_GET['url']))
	{
		$output->addl("<td style=\"width: 50%;\"><input type=\"text\" name=\"url\" size=\"30\" maxlength=\"125\" value=\"" . htmlspecialchars($_GET['url']) . "\" /></td>",3);
	}
	else
	{
		$output->addl("<td style=\"width: 50%;\"><input type=\"text\" name=\"url\" size=\"30\" maxlength=\"125\" /></td>",3);		
	}
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td style=\"width: 50%;\"><b>Icon:</b></td>",3);
	$output->addl("<td style=\"width: 50%;\"><select name=\"icon\"><option value=\"-1\">(none)</option>",3);
	for ($i = 0; $i < count($options['recentupdate_icons']); $i++)
	{
		$output->add("<option value=\"$i\" style=\"background: URL($images_url/icons/update-" . htmlspecialchars($options['recentupdate_icons'][$i]) . ".gif) no-repeat; padding-left: 19px;\">" . htmlspecialchars($options['recentupdate_icons'][$i]) . "</option>");
	}
	$output->add("</select></td>");
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td style=\"width: 50%;\"><b>Update:</b></td>",3);
	$output->addl("<td style=\"width: 50%;\"><input type=\"text\" name=\"title\" size=\"30\" maxlength=\"100\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("<td colspan=\"2\" style=\"text-align: center;\"><input type=\"submit\" class=\"button\" value=\"Add Update\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("</table>",1);
	$output->addl("</form>",1);
	$output->display();
}
elseif ($action == 'add2')
{
	if (trim($_POST['url']) != '' && trim($_POST['title']) != '' && $_POST['icon'] >= -1 && $_POST['icon'] < count($options['recentupdate_icons']))
	{
		$db->query("INSERT INTO recentupdate (date, userid, url, title, icon)"
		.        "\nVALUES ('" . gmdate("U") . "', '" . $spruser['userid'] . "', '" . addslashes($_POST['url']) . "', '" . addslashes($_POST['title']) . "', '" . addslashes($_POST['icon']) . "')");
		$output->subtitle = 'Add Recent Update';
		$output->addl("<p>Your crappy update has been added to the site. <a href=\"recentupdate.php\">Go see it.</a></p>",1);
		$output->display();
	}
	else
	{
		$output->addl("<p>You did not fill in one or more fields properly. Please do this and try again.</p>",1);
		$output->display();
	}
}
elseif ($action == 'edit')
{
	$recentupdate = $db->query_first("SELECT url, title, icon FROM recentupdate WHERE recentupdateid = '" . addslashes($_GET['recentupdateid']) . "' LIMIT 1");
	if (is_array($recentupdate))
	{
		$output->subtitle = 'Edit Recent Update';
		$output->addl("<form action=\"recentupdate.php\" method=\"post\">",1);
		$output->addl("<input type=\"hidden\" name=\"action\" value=\"edit2\" />",1);
		$output->addl("<input type=\"hidden\" name=\"recentupdateid\" value=\"" . htmlspecialchars($_GET['recentupdateid']) . "\" />",1);
		$output->addl("<table width=\"45%\" align=\"center\" cellspacing=\"0\">",1);
		$output->addl("<tr>",2);
		$output->addl("<th colspan=\"2\">Edit Recent Update</th>",3);
		$output->addl("</tr>",2);
		$output->addl("<tr>",2);
		$output->addl("<td style=\"width: 50%;\"><b>Update URL:</b></td>",3);
		$output->addl("<td style=\"width: 50%;\"><input type=\"text\" name=\"url\" size=\"30\" maxlength=\"125\" value=\"" . htmlspecialchars($recentupdate['url']) . "\" /></td>",3);
		$output->addl("</tr>",2);
		$output->addl("<tr>",2);
		$output->addl("<td style=\"width: 50%;\"><b>Icon:</b></td>",3);
		$output->addl("<td style=\"width: 50%;\"><select name=\"icon\"><option value=\"-1\">(none)</option>",3);
		for ($i = 0; $i < count($options['recentupdate_icons']); $i++)
		{
			if ($i == $recentupdate['icon'])
			{
				$output->add("<option value=\"$i\" style=\"background: URL($images_url/icons/update-" . htmlspecialchars($options['recentupdate_icons'][$i]) . ".gif) no-repeat; padding-left: 19px;\" selected=\"selected\">" . htmlspecialchars($options['recentupdate_icons'][$i]) . "</option>");
			}
			else
			{
				$output->add("<option value=\"$i\" style=\"background: URL($images_url/icons/update-" . htmlspecialchars($options['recentupdate_icons'][$i]) . ".gif) no-repeat; padding-left: 19px;\">" . htmlspecialchars($options['recentupdate_icons'][$i]) . "</option>");
			}
		}
		$output->add("</select></td>");
		$output->addl("</tr>",2);
		$output->addl("<tr>",2);
		$output->addl("<td style=\"width: 50%;\"><b>Update:</b></td>",3);
		$output->addl("<td style=\"width: 50%;\"><input type=\"text\" name=\"title\" size=\"30\" maxlength=\"100\" value=\"" . htmlspecialchars($recentupdate['title']) . "\" /></td>",3);
		$output->addl("</tr>",2);
		$output->addl("<td colspan=\"2\" style=\"text-align: center;\"><input type=\"submit\" class=\"button\" value=\"Edit Update\" /></td>",3);
		$output->addl("</tr>",2);
		$output->addl("</table>",1);
		$output->addl("</form>",1);
		$output->display();
	}
	else
	{
		$output->addl("<p>Well fuck, it looks like the recent update you specified does not exist. Sorry!</p>",1);
		$output->display();
	}
}
elseif ($action == 'edit2')
{
	if (trim($_POST['url']) != '' && trim($_POST['title']) != '' && $_POST['icon'] >= -1 && $_POST['icon'] < count($options['recentupdate_icons']))
	{
		$recentupdate = $db->query_first("SELECT count(*) FROM recentupdate WHERE recentupdateid = '" . addslashes($_POST['recentupdateid']) . "'");
		if ($recentupdate['count(*)'])
		{
			$db->query("UPDATE recentupdate SET url = '" . addslashes($_POST['url']) . "', title = '" . addslashes($_POST['title']) . "', icon = '" . addslashes($_POST['icon']) . "' WHERE recentupdateid = '" . addslashes($_POST['recentupdateid']) . "' LIMIT 1");
			$output->addl("<p>Update complete. It seems that the recent update has been recently updated. <a href=\"recentupdate.php\">Go see.</a></p>",1);
			$output->display();
		}
		else
		{
			$output->addl("<p>Well fuck, it looks like the recent update you specified no longer exists. Sorry!</p>",1);
			$output->display();
		}
	}
	else
	{
		$output->addl("<p>You did not fill in one or more fields properly. Please do this and try again.</p>",1);
		$output->display();
	}
}
elseif ($action == 'delete')
{
	$recentupdate = $db->query_first("SELECT title FROM recentupdate WHERE recentupdateid = '" . addslashes($_GET['recentupdateid']) . "' LIMIT 1");
	if (is_array($recentupdate))
	{
		$output->subtitle = 'Delete Recent Update';
		$output->addl("<form action=\"recentupdate.php\" method=\"post\">",1);
		$output->addl("<input type=\"hidden\" name=\"action\" value=\"delete2\" />",1);
		$output->addl("<input type=\"hidden\" name=\"recentupdateid\" value=\"" . htmlspecialchars($_GET['recentupdateid']) . "\" />",1);
		$output->addl("<h2>Confirm Deletion</h2>",1);
		$output->addl("<p>Are you sure you want to delete '" . htmlspecialchars($recentupdate['title']) . "'?</p>",1);
		$output->addl("<input type=\"submit\" class=\"button\" name=\"submit\" value=\"Absolutely\" /> <input type=\"submit\" class=\"button\" name=\"submit\" value=\"NO DON'\" />",1);
		$output->addl("</form>",1);
		$output->display();
	}
	else
	{
		$output->addl("<p>Whoops, it seems that this recent update no longer exists. It may have been deleted, or perhaps something more... <i>mysterious</i>. Move along.</p>",1);
		$output->display();
	}
}
elseif ($action == 'delete2')
{
	$recentupdate = $db->query_first("SELECT count(*) FROM recentupdate WHERE recentupdateid = '" . addslashes($_POST['recentupdateid']) . "'");
	if ($recentupdate['count(*)'])
	{
		if ($_POST['submit'] == 'Absolutely')
		{
			$db->query("DELETE FROM recentupdate WHERE recentupdateid = '" . addslashes($_POST['recentupdateid']) . "' LIMIT 1");
			$output->addl("<p>This recent update has successfully been purged from existence. Thank you for your time!</p>",1);
			$output->addl("<p><a href=\"recentupdate.php\">Back to the recent updates menu.</a></p>",1);
			$output->display();
		}
		else
		{
			header("Location: http://" . $_SERVER['HTTP_HOST'] . "/$manager_url/recentupdate.php");
			exit;
		}
	}
	else
	{
		$output->addl("<p>Whoops! It seems like the recent update you selected has already been deleted. Oh well.</p>",1);
		$output->display();
	}
}

?>