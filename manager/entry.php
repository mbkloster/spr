<?php

$starttime = microtime();

require("../include/include_manager.php");

function entryform($actiontitle, $headingtitle, $subbutton, $action, $entryid, $author, $email, $date, $month, $day, $year, $hour, $minute, $second, $title, $body, $isvisible, $isopen, $priority)
{
	global $output;
	$months = array("", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
	$output->subtitle = $actiontitle . ' Entry';
	$output->addl("<form action=\"entry.php\" method=\"post\">",1);
	$output->addl("<input type=\"hidden\" name=\"action\" value=\"$action\" />",1);
	if ($entryid > 0)
	{
		$output->addl("<input type=\"hidden\" name=\"entryid\" value=\"" . htmlspecialchars($entryid) . "\" />",1);
	}
	$output->addl("<table width=\"70%\" align=\"center\" cellspacing=\"0\">",1);
	$output->addl("<tr>",2);
	$output->addl("<th colspan=\"2\">$headingtitle</th>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td width=\"20%\"><b>Author:</b></td>",3);
	$output->addl("<td width=\"80%\"><input type=\"text\" name=\"author\" size=\"30\" maxlength=\"100\" value=\"" . htmlspecialchars($author) . "\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td width=\"20%\"><b>Post Date:</b></td>",3);
	if ($date < 1)
	{
		$output->addl("<td width=\"80%\"><input type=\"radio\" class=\"box\" name=\"date\" value=\"0\" checked=\"checked\" /> Now <input type=\"radio\" class=\"box\" name=\"date\" value=\"1\" /> <select name=\"month\">",3);
	}
	else
	{
		$output->addl("<td width=\"80%\"><input type=\"radio\" class=\"box\" name=\"date\" value=\"0\" /> Now <input type=\"radio\" class=\"box\" name=\"date\" value=\"1\" checked=\"checked\" /> <select name=\"month\">",3);
	}
	for ($i = 1; $i <= 12; $i++)
	{
		if ($i == $month)
		{
			$output->add("<option value=\"$i\" selected=\"selected\">$months[$i]</option>");
		}
		else
		{
			$output->add("<option value=\"$i\">$months[$i]</option>");
		}
	}
	$output->add("</select> <select name=\"day\">");
	for ($i = 1; $i <= 31; $i++)
	{
		if ($i == $day)
		{
			$output->add("<option value=\"$i\" selected=\"selected\">$i</option>");
		}
		else
		{
			$output->add("<option value=\"$i\">$i</option>");
		}
	}
	$output->add("</select> <select name=\"year\">");
	for ($i = 1970; $i <= 2030; $i++)
	{
		if ($i == $year)
		{
			$output->add("<option value=\"$i\" selected=\"selected\">$i</option>");
		}
		else
		{
			$output->add("<option value=\"$i\">$i</option>");
		}
	}
	$output->add("</select> <select name=\"hour\">");
	for ($i = 0; $i <= 23; $i++)
	{
		if ($i == $hour)
		{
			$output->add("<option value=\"$i\" selected=\"selected\">" . str_pad($i,2,"0",STR_PAD_LEFT) . ":</option>");
		}
		else
		{
			$output->add("<option value=\"$i\">" . str_pad($i,2,"0",STR_PAD_LEFT) . ":</option>");
		}
	}
	$output->add("</select><select name=\"minute\">");
	for ($i = 0; $i <= 60; $i++)
	{
		if ($i == $minute)
		{
			$output->add("<option value=\"$i\" selected=\"selected\">" . str_pad($i,2,"0",STR_PAD_LEFT) . "</option>");
		}
		else
		{
			$output->add("<option value=\"$i\">" . str_pad($i,2,"0",STR_PAD_LEFT) . "</option>");
		}
	}
	$output->add("</select><input type=\"hidden\" name=\"second\" value=\"" . htmlspecialchars($second) . "\" /> (GMT)</td>");
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td width=\"20%\"><b>Email Address:</b></td>",3);
	$output->addl("<td width=\"80%\">" . htmlspecialchars($email) . "</td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td width=\"20%\"><b>Title:</b></td>",3);
	$output->addl("<td width=\"80%\"><input type=\"text\" name=\"title\" size=\"50\" maxlength=\"150\" value=\"" . htmlspecialchars($title) . "\"/></td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td width=\"20%\"><b>Entry:</b></td>",3);
	$output->addl("<td width=\"80%\"><textarea name=\"body\" style=\"width: 80%; height: 2.7in;\">" . htmlspecialchars($body) . "</textarea></td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td width=\"20%\"><b>Options:</b></td>",3);
	if ($isvisible)
	{
		$output->addl("<td width=\"80%\"><input type=\"checkbox\" name=\"isvisible\" value=\"1\" checked=\"checked\" /> <b>Visible Entry?</b> If unchecked, entry will be invisible to all but other posters.<br />",3);
	}
	else
	{
		$output->addl("<td width=\"80%\"><input type=\"checkbox\" name=\"isvisible\" value=\"1\" /> <b>Visible Entry?</b> If unchecked, entry will be invisible to all but other posters.<br />",3);
	}
	if ($isopen)
	{
		$output->add("<input type=\"checkbox\" name=\"isopen\" value=\"1\" checked=\"checked\" /> <b>Open for Comments?</b> If checked, other visitors will be able to post comments to this entry.");
	}
	else
	{
		$output->add("<input type=\"checkbox\" name=\"isopen\" value=\"1\" /> <b>Open for Comments?</b> If checked, other visitors will be able to post comments to this entry.");
	}
	if ($entryid > 0)
	{
		$output->add("<br /><input type=\"checkbox\" name=\"changedate\" value=\"1\" checked=\"checked\" /> <b>Update last change date?</b> If left unchecked, the \"last update\" value will be equal to whatever date this entry has.</td>");
	}
	else
	{
		$output->add("</td>");
	}
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td width=\"20%\"><b>Priority:</b><br /><span class=\"small\">Should be kept at 0 most of the time.</span></td>",3);
	$output->addl("<td width=\"80%\"><input type=\"text\" name=\"priority\" size=\"2\" maxlength=\"2\" value=\"" . htmlspecialchars($priority) . "\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td colspan=\"2\" style=\"text-align: center;\"><input type=\"submit\" class=\"button\" name=\"submit\" value=\"$subbutton\" accesskey=\"s\" title=\"Submit Entry (alt+S)\" /> <input type=\"submit\" class=\"button\" name=\"submit\" value=\"Preview Entry\" accesskey=\"p\" title=\"Preview Entry (alt+P)\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("</table>",1);
	$output->addl("</form>",1);
}

if ($action == "manage")
{
	$output->subtitle = 'Manage Entries';
	if (!isset($_GET['page']) || $_GET['page'] < 1)
	{
		$page = 1;
	}
	else
	{
		$page = $_GET['page'];
	}
	if (!isset($_GET['perpage']) || $_GET['perpage'] < 1)
	{
		$perpage = 25;
	}
	else
	{
		$perpage = $_GET['perpage'];
	}
	$start = ($page-1)*$perpage;
	$username = array();
	$db->query("SELECT user.userid, user.username FROM user INNER JOIN entry ON user.userid = entry.entryid");
	while ($user = $db->fetch_array())
	{
		// Get user/id information
		$username[$user['userid']] = $user['username'];
	}
	$db->query("SELECT entryid, userid, date, author, title, comments, priority FROM entry ORDER BY entryid DESC LIMIT $start, $perpage");
	$output->addl("<h2>Manage Entries</h2>",1);
	$output->addl("<table width=\"55%\" cellspacing=\"0\">",1);
	$output->addl("<tr>",2);
	$output->addl("<th style=\"width: 40%;\">Entry</th><th style=\"width: 25%\">Author</th><th style=\"width: 20%\">Date</th><th style=\"width: 10%\">Comments</th><th style=\"width: 5%\">Del</th>",3);
	$output->addl("</tr>",2);
	while ($entry = $db->fetch_array())
	{
		$output->addl("<tr>",2);
		$output->addl("<td width=\"40%\"><a href=\"entry.php?action=edit&amp;entryid=" . $entry['entryid'] . "\">" . htmlspecialchars($entry['title'])  . "</a></td>",3);
		$output->addl("<td width=\"25%\"><a href=\"user.php?action=edit&amp;userid=" . $entry['userid'] . "\" title=\"" . $username[$entry['userid']] . "\">" . htmlspecialchars($entry['author'])  . "</a></td>",3);
		$output->addl("<td width=\"20%\">" . date("n-j-y H:i",$entry['date'])  . "</td>",3);
		$output->addl("<td width=\"10%\"><a href=\"comment.php?action=viewcomments&amp;entryid=" . $entry['entryid'] . "\">" . $entry['comments'] . "</a></td>",3);
		$output->addl("<td width=\"5%\" style=\"text-align: center;\"><a href=\"entry.php?action=delete&amp;entryid=" . $entry['entryid'] . "\">X</a></td>",3);
		$output->addl("</tr>",2);
	}
	$db->free_result();
	$output->addl("</table>",1);
	$output->addl("<p><b>Pages:</b>");
	$entries = $db->query_first("SELECT count(*) FROM entry");
	$pages = $entries['count(*)'] / $perpage;
	if (is_float($pages))
	{
		$pages += 1;
	}
	for ($i = 1; $i <= $pages; $i++)
	{
		if ($i == $page)
		{
			$output->add(" <b>[$i]</b>");
		}
		else
		{
			$output->add(" <a href=\"entry.php?action=manage&amp;perpage=$perpage&amp;page=$i\">$i</a>");
		}
	}
	$output->add("</p>");
	$output->display();
}
elseif ($action == "new")
{
	entryform("Post", "Post New Entry", "Post Entry", "new2", 0, $spruser['authorname'], $spruser['email'], 0, gmdate("n"), gmdate("j"), gmdate("Y"), gmdate("G"), gmdate("i"), gmdate("s"), "", "", 1, 1, 0);
	$output->display();
}
elseif ($action == "new2")
{
	if ($_POST['submit'] == 'Post Entry')
	{
		if (trim($_POST['title']) != '' && trim($_POST['body']) != '' && trim($_POST['author']) != '')
		{
			if ($_POST['date'] == 0)
			{
				$date = gmdate("U");
			}
			else
			{
				$date = mktime($_POST['hour'],$_POST['minute'],$_POST['second'],$_POST['month'],$_POST['day'],$_POST['year']);
			}
			$db->query("INSERT INTO entry (userid, date, lastchangedate, author, email, title, body, comments, isvisible, isopen, priority)"
			.        "\nVALUES('" . $spruser['userid'] . "', '$date', '$date', '" . addslashes($_POST['author']) . "', '" . addslashes($spruser['email']) . "', '" . addslashes($_POST['title']) . "', '" . addslashes(entryparse_u2p($_POST['body'])) . "', '0', '" . addslashes($_POST['isvisible']) . "', '" . addslashes($_POST['isopen']) . "', '" . addslashes($_POST['priority']) . "')");
			$entryid = $db->insert_id();
			$entries = $db->query_first("SELECT count(*) FROM entry");
			$output->use_header = 0;
			$output->use_footer = 0;
			$output->addl("<p>Your entry (<a href=\"/entries/$entryid/\">entryid#$entryid</a>) has been posted.</p>",1);
			$output->addl("<p>There are currently <b>" . number_format($entries['count(*)']) . "</b> entries in the database.</p>",1);
			$output->addl("<p><b><a href=\"entry.php?action=new\">Post Another Entry</a> - <a href=\"entry.php?action=manage\">Manage Entries</a> - <a href=\"./\">Manager Home</a></b></p>",1);
			$output->display();
		}
		else
		{
			$output->use_header = 0;
			$output->use_footer = 0;
			$output->addl("<p>You did not fill in one or more fields correctly. Please do that and try again.</p>",1);
			$output->display();
		}
	}
	else
	{
		$output->subtitle = 'Post Entry';
		if (trim($_POST['title']) != '' && trim($_POST['body']) != '' && trim($_POST['author']) != '')
		{
			$output->addl("<div style=\"background-color: transparent; color: red;\">Note that this is only a preview! Your entry has not yet been saved.</div>",1);
			$output->addl("<h2 style=\"padding-bottom: 1px; margin-bottom: 1px;\">" . htmlspecialchars(trim($_POST['title'])) . "</h2>",1);
			if ($_POST['date'])
			{
				$output->addl("<div class=\"small\">Posted <b>" . date("h:i A",mktime($_POST['hour'],$_POST['minute'],$_POST['second'],$_POST['month'],$_POST['day'],$_POST['year'])+(3600*$options['gmt_offset'])) . "</b> by <b><a href=\"mailto:" . htmlspecialchars($spruser['email']) . "\">" . htmlspecialchars(trim($_POST['author'])) . "</a></b></div>",1);
			}
			else
			{
				$output->addl("<div class=\"small\">Posted <b>" . date("h:i A",gmdate("U")+(3600*$options['gmt_offset'])) . "</b> by <b><a href=\"mailto:" . htmlspecialchars($spruser['email']) . "\">" . htmlspecialchars(trim($_POST['author'])) . "</a></b></div>",1);
			}
			$output->addl(entryparse_u2p(trim($_POST['body'])));
		}
		entryform("Post", "Post New Entry", "Post Entry", "new2", 0, $_POST['author'], $spruser['email'], $_POST['date'], $_POST['month'], $_POST['day'], $_POST['year'], $_POST['hour'], $_POST['minute'], $_POST['second'], $_POST['title'], $_POST['body'], $_POST['isvisible'], $_POST['isopen'], $_POST['priority']);
		$output->display();
	}
}
elseif ($action == "edit")
{
	$entry = $db->query_first("SELECT date, author, email, title, body, isvisible, isopen, priority FROM entry WHERE entryid = '" . addslashes($_GET['entryid']) . "' LIMIT 1");
	if (is_array($entry))
	{
		entryform("Edit","Edit Entry ID " . htmlspecialchars($_GET['entryid']), "Save Changes", "edit2", $_GET['entryid'], $entry['author'], $entry['email'], 1, date("n",$entry['date']), date("j",$entry['date']), date("Y",$entry['date']), date("G",$entry['date']), date("i",$entry['date']),date("s",$entry['date']),$entry['title'],entryparse_p2u($entry['body']),$entry['isvisible'],$entry['isopen'],$entry['priority']);
		$output->display();
	}
	else
	{
		$output->addl("<p>Looks like the entry id you specified does not exist. Maybe it was deleted, or maybe you're just an idiot.</p>",1);
		$output->addl("<p><a href=\"entry.php?action=manage\">Back to Entry manager</a></p>",1);
		$output->display();
	}
}
elseif ($action == "edit2")
{
	$entry = $db->query_first("SELECT date, author, title, body, isvisible, isopen, priority FROM entry WHERE entryid = '" . addslashes($_POST['entryid']) . "' LIMIT 1");
	if (is_array($entry))
	{
		if ($_POST['submit'] == 'Save Changes')
		{
			if (trim($_POST['author']) != '' && trim($_POST['title']) != '' && trim($_POST['body']) != '')
			{
				$date = mktime($_POST['hour'],$_POST['minute'],$_POST['second'],$_POST['month'],$_POST['day'],$_POST['year']);
				$body = entryparse_u2p($_POST['body']);
				$changes = array(); // This array will hold fields with changed values
				if (!$_POST['date'])
				{
					$changes['date'] = time();
				}
				elseif ($_POST['date'] && $date != $entry['date'])
				{
					$changes['date'] = $date;
				}
				if ($entry['author'] != $_POST['author'])
				{
					$changes['author'] = $_POST['author'];
				}
				if ($entry['title'] != $_POST['title'])
				{
					$changes['title'] = $_POST['title'];
				}
				if ($entry['body'] != $body)
				{
					$changes['body'] = $body;
				}
				if ( (!$entry['isvisible'] && $_POST['isvisible']) || ($entry['isvisible'] && !$_POST['isvisible']) )
				{
					$changes['isvisible'] = $_POST['isvisible'];
				}
				if ( (!$entry['isopen'] && $_POST['isopen']) || ($entry['isopen'] && !$_POST['isopen']) )
				{
					$changes['isopen'] = $_POST['isopen'];
				}
				if ($entry['priority'] != $_POST['priority'])
				{
					$changes['priority'] = $_POST['priority'];
				}
				
				if ($_POST['changedate'] == 1 && count($changes) > 0)
				{
					$changes['lastchangedate'] = gmdate("U");
				}
				elseif (isset($changes['date']))
				{
					$changes['lastchangedate'] = $changes['date'];
				}
				
				if (count($changes) > 0)
				{
					$firstloop = 1;
					while (list($key,$val) = each($changes))
					{
						if (!$firstloop)
						{
							$setclause .= ", $key = '" . addslashes($val) . "'";
						}
						else
						{
							$setclause = "$key = '" . addslashes($val) . "'";
							$firstloop = 0;
						}
					}
					$db->query("UPDATE entry SET $setclause WHERE entryid = '" . addslashes($_POST['entryid']) . "' LIMIT 1");
					$output->use_header = 0;
					$output->use_footer = 0;
					$output->addl("<p>Your changes to this entry (<a href=\"/entries/"  . urlencode($_POST['entryid']) . "/\">entryid#" . htmlspecialchars($_POST['entryid']) . "</a>) have been made.</p>",1);
					$output->addl("<p><b><a href=\"entry.php?action=new\">Post Another Entry</a> - <a href=\"entry.php?action=manage\">Manage Entries</a> - <a href=\"./\">Manager Home</a></b></p>",1);
				}
				else
				{
					$output->use_header = 0;
					$output->use_footer = 0;
					$output->addl("<p>You did not make any changes to this entry (<a href=\"/entries/" . urlencode($_POST['entryid']) . "/\">entryid#" . htmlspecialchars($_POST['entryid']) . "</a>) and therefore nothing has been saved.</p>",1);
					$output->addl("<p><b><a href=\"entry.php?action=new\">Post Another Entry</a> - <a href=\"entry.php?action=manage\">Manage Entries</a> - <a href=\"./\">Manager Home</a></b></p>",1);
				}
				$output->display();
			}
			else
			{
				$output->use_header = 0;
				$output->use_footer = 0;
				$output->addl("<p>You did not fill in one or more fields correctly. Please do that and try again.</p>",1);
				$output->display();
			}
		}
		else
		{
			if (trim($_POST['title']) != '' && trim($_POST['body']) != '' && trim($_POST['author']) != '')
			{
				$output->addl("<div style=\"background-color: transparent; color: red;\">Note that this is only a preview! Your changes to this entry have not yet been saved.</div>",1);
				$output->addl("<h2 style=\"padding-bottom: 1px; margin-bottom: 1px;\">" . htmlspecialchars(trim($_POST['title'])) . "</h2>",1);
				if ($_POST['date'])
				{
					$output->addl("<div class=\"small\">Posted <b>" . date("h:i A",mktime($_POST['hour'],$_POST['minute'],$_POST['second'],$_POST['month'],$_POST['day'],$_POST['year'])+(3600*$options['gmt_offset'])) . "</b> by <b><a href=\"mailto:" . htmlspecialchars($spruser['email']) . "\">" . htmlspecialchars(trim($_POST['author'])) . "</a></b></div>",1);
				}
				else
				{
					$output->addl("<div class=\"small\">Posted <b>" . date("h:i A",gmdate("U")+(3600*$options['gmt_offset'])) . "</b> by <b><a href=\"mailto:" . htmlspecialchars($spruser['email']) . "\">" . htmlspecialchars(trim($_POST['author'])) . "</a></b></div>",1);
				}
				$output->addl(entryparse_u2p(trim($_POST['body'])));
				entryform("Edit","Edit Entry ID " . htmlspecialchars($_POST['entryid']), "Save Changes", "edit2", $_POST['entryid'], $_POST['author'], $spruser['email'], $_POST['date'], $_POST['month'], $_POST['day'], $_POST['year'], $_POST['hour'], $_POST['minute'], $_POST['second'], $_POST['title'], $_POST['body'], $_POST['isvisible'], $_POST['isopen'], $_POST['priority']);
				$output->display();
			}
			else
			{
				$output->use_header = 0;
				$output->use_footer = 0;
				$output->addl("<p>You did not fill in one or more fields correctly. Please do that and try again.</p>",1);
				$output->display();
			}
		}
	}
	else
	{
		$output->addl("<p>Looks like the entry id you specified does not exist. Maybe it was deleted while you were editing it, or maybe you're just an idiot.</p>",1);
		$output->addl("<p><a href=\"entry.php?action=manage\">Back to Entry manager</a></p>",1);
		$output->display();
	}
}
elseif ($action == 'delete')
{
	$entry = $db->query_first("SELECT userid, title, author FROM entry WHERE entryid = '" . addslashes($_GET['entryid']) . "' LIMIT 1");
	if (is_array($entry))
	{
		if ($spruser['isadmin'] || $entry['userid'] == $spruser['userid'])
		{
			$output->subtitle = 'Delete Entry';
			$output->addl("<form action=\"entry.php\" method=\"post\">",1);
			$output->addl("<input type=\"hidden\" name=\"action\" value=\"delete2\" />",1);
			$output->addl("<input type=\"hidden\" name=\"entryid\" value=\"" . $_GET['entryid'] . "\" />",1);
			$output->addl("<p><b>Are you sure you want to delete <i>" . htmlspecialchars($entry['title']) . "</i> (entry id: " . $_GET['entryid'] . ") by " . htmlspecialchars($entry['author']) . "?</b></p>",1);
			$output->addl("<input class=\"box\" type=\"radio\" name=\"confirmdelete\" value=\"0\" checked=\"checked\" /> No, I changed my mind or made a mistake. I also may or may not \"swing either way\", so to speak.<br />",1);
			$output->addl("<input class=\"box\" type=\"radio\" name=\"confirmdelete\" value=\"1\" /> Sure, and delete all the comments to this entry while you're at it.<br />",1);
			$output->addl("<input class=\"box\" type=\"radio\" name=\"confirmdelete\" value=\"2\" /> Sure, and move the comments to entry id: <input type=\"text\" name=\"newentryid\" size=\"5\" maxlength=\"5\" value=\"0\" /><br />",1);
			$output->addl("<input class=\"button\" type=\"submit\" value=\"Confirm\" />",1);
			$output->addl("</form>",1);
			$output->display();
		}
		else
		{
			$output->addl("<p>In order to delete entries, you must either be an admin or be the person that originally posted the entry.</p>",1);
			$output->display();
		}
	}
	else
	{
		$output->addl("<p>This entry does not appear to exist.</p>",1);
		$output->addl("<p>It may have been already deleted, or you could just be entering random entryids for the hell of it.</p>",1);
		$output->display();
	}
}

elseif ($action == 'delete2')
{
	$entry = $db->query_first("SELECT userid FROM entry WHERE entryid = '" . addslashes($_POST['entryid']) . "' LIMIT 1");
	if (is_array($entry))
	{
		if ($entry['userid'] == $spruser['userid'] || $spruser['isadmin'])
		{
			if ($_POST['confirmdelete'] < 1)
			{
				header("Location: $server_url$manager_url/entry.php?action=manage");
				exit;
			}
			elseif ($_POST['confirmdelete'] == 1)
			{
				$db->query("DELETE FROM entry WHERE entryid = '" . addslashes($_POST['entryid']) . "' LIMIT 1");
				$db->query("DELETE FROM comment WHERE entryid = '" . addslashes($_POST['entryid']) . "'");
				$rows = $db->affected_rows();
				if ($rows == 1)
				{
					$rowmsg = 'its 1 comment';
				}
				else
				{
					$rowmsg = "all of its $rows comments";
				}
				$output->addl("<p>Entry ID " . $_POST['entryid'] . " and $rowmsg have been sent straight to hell. Thank you very much!</p>",1);
				$output->addl("<p><b><a href=\"entry.php?action=new\">Post Another Entry</a> - <a href=\"entry.php?action=manage\">Manage Entries</a> - <a href=\"./\">Manager Home</a></b></p>",1);
				$output->display();
			}
			elseif ($_POST['confirmdelete'] > 1)
			{
				$newentry = $db->query_first("SELECT count(*) FROM entry WHERE entryid = '" . addslashes($_POST['newentryid']) . "' LIMIT 1");
				if ($newentry['count(*)'] > 0)
				{
					$db->query("DELETE FROM entry WHERE entryid = '" . addslashes($_POST['entryid']) . "' LIMIT 1");
					$db->query("UPDATE comment SET entryid = '" . addslashes($_POST['newentryid']) . "' WHERE entryid = '" . addslashes($_POST['entryid']) . "'");
					$rows = $db->affected_rows();
					$db->query("UPDATE entry SET comments = comments+" . $rows . " WHERE entryid = '" . addslashes($_POST['newentryid']) . "' LIMIT 1");
					if ($rows == 1)
					{
						$rowmsg = 'its 1 comment has';
					}
					else
					{
						$rowmsg = "all of its $rows comments have";
					}
					$output->addl("<p>Entry ID " . $_POST['entryid'] . " has been deleted and $rowmsg been moved to entry ID " . $_POST['newentryid'] . ". Thanks for playing.</p>",1);
					$output->addl("<p><b><a href=\"entry.php?action=new\">Post Another Entry</a> - <a href=\"entry.php?action=manage\">Manage Entries</a> - <a href=\"./\">Manager Home</a></b></p>",1);
					$output->display();
				}
				else
				{
					$output->addl("<p>The entry you are sending the comments to has to actually exist. Idiot.</p>",1);
					$output->display();
				}
			}
		}
		else
		{
			$output->addl("<p>You need to be an admin to edit entries that are not your own... dumbass.</p>",1);
			$output->display();
		}
	}
	else
	{
		$output->addl("<p>It would seem that this entry was deleted by someone else before you had the chance to. Nice try though.</p>",1);
		$output->display();
	}
}

?>