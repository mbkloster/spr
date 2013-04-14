<?php

$starttime = microtime();

require("../include/include_manager.php");

if (trim($action) == '')
{
	$action = 'viewcomments';
}

if ($action == 'viewcomments')
{
	$entry = $db->query_first("SELECT title, date FROM entry WHERE entryid = '" . addslashes($_GET['entryid']) . "' LIMIT 1");
	if (is_array($entry))
	{
		$output->addl("<h2>Comments for entry '" . htmlspecialchars($entry['title']) . "'</h2>",1);
		$db->query("SELECT commentid, date, ipaddress, author, email, body, isposter FROM comment WHERE entryid = '" . addslashes($_GET['entryid']) . "' ORDER BY date, commentid ASC");
		if ($db->num_rows())
		{
			$displaylen = 50;
			$output->addl("<ol>",1);
			while ($comment = $db->fetch_array())
			{
				if (strlen($comment['body']) > $displaylen)
				{
					$body = substr($comment['body'],0,$displaylen) . "...";
				}
				else
				{
					$body = $comment['body'];
				}
				$output->addl("<li><span class=\"timestamp\">[" . date('M d y H:i:s',$comment['date']) . "]</span> " . $comment['ipaddress'] . " " . htmlspecialchars($comment['email']) . " <b>" . htmlspecialchars($comment['author']) . "</b> <i>" . htmlspecialchars($body) . "</i><br /><a href=\"comment.php?action=edit&amp;commentid=" . $comment['commentid'] . "\">[E]</a> <a href=\"comment.php?action=delete&amp;commentid=" . $comment['commentid'] . "\">[X]</a></li>",2);
			}
			$output->addl("</ol>",1);
			$output->addl("<p><a href=\"comment.php?action=massdelete&amp;entryid=".$_GET['entryid']."\">Shitcan this entire fucking entry</a></p>",1);
		}
		else
		{
			$output->addl("<p>This entry has no comments!!!! oh jesus</p>",1);
		}
		$output->addl("<p><a href=\"entry.php?action=manage\">Manage entries...</a></p>",1);
		$output->display();
	}
	else
	{
		$output->addl("<p>The entry you specified does not seem to exist. Please try again!</p>",1);
		$output->display();
	}
}
elseif ($action == 'massdelete')
{
	$entry = $db->query_first("SELECT entryid, title FROM entry WHERE entryid = '".addslashes($_GET['entryid'])."' LIMIT 1");
	if (is_array($entry))
	{
		$output->addl("<h2>Shitcanning all of entry '".$entry['title']."'</h2>",1);
		$output->addl("<p>Are you really, really sure you want to pull this shit? There could be a really good comment there!</p>",1);
		$output->addl("<p><a href=\"comment.php?action=massdelete2&amp;entryid=".$_GET['entryid']."\">Do it</a> <a href=\"comments.php?action=viewcomments&amp;action=viewcomments\">NO WAIT</a></p>",1);
	}
	else
	{
		$output->addl("<p>This entry doesn't exist, Joe.</p>");
	}
	$output->addl("<p><a href=\"entry.php?action=manage\">Manage entries...</a></p>",1);
	$output->display();
}
elseif ($action == 'massdelete2')
{
	$db->query("UPDATE comment SET entryid = '" . addslashes($options['deleted_entryid']) . "' WHERE entryid = '".addslashes($_GET['entryid'])."'");
	$entries = $db->affected_rows();
	$db->query("UPDATE entry SET comments=0 WHERE entryid = '".addslashes($_GET['entryid'])."' LIMIT 1");
	$db->query("UPDATE entry SET comments=comments+$entries WHERE entryid = '".addslashes($options['deleted_entryid'])."' LIMIT 1");
	$output->addl("Purge completed. Thank you.");
	$output->addl("<p><a href=\"entry.php?action=manage\">Manage entries...</a></p>",1);
	$output->display();
}
elseif ($action == 'edit')
{
	$comment = $db->query_first("SELECT entryid, author, email, body FROM comment WHERE commentid = '" . $_GET['commentid'] . "' LIMIT 1");
	if (is_array($comment))
	{
		$output->subtitle = 'Edit Comment';
		$output->addl("<form action=\"comment.php\" method=\"post\">",1);
		$output->addl("<input type=\"hidden\" name=\"action\" value=\"edit2\" />",1);
		$output->addl("<input type=\"hidden\" name=\"commentid\" value=\"" . htmlspecialchars($_GET['commentid']) . "\" />",1);
		$output->addl("<table width=\"55%\" align=\"center\" cellspacing=\"0\">",1);
		$output->addl("<tr>",2);
		$output->addl("<td class=\"heading\" colspan=\"2\">Edit Comment by " . htmlspecialchars($comment['author']) . "</td>",3);
		$output->addl("</tr>",2);
		$output->addl("<tr>",2);
		$output->addl("<td style=\"width: 40%;\"><b>Name:</b></td>",3);
		$output->addl("<td style=\"width: 60%;\"><input type=\"text\" name=\"author\" size=\"25\" maxlength=\"20\" value=\"" . htmlspecialchars($comment['author']) . "\" /></td>",3);
		$output->addl("</tr>",2);
		$output->addl("<tr>",2);
		$output->addl("<td style=\"width: 40%;\"><b>Email:</b></td>",3);
		$output->addl("<td style=\"width: 60%;\"><input type=\"text\" name=\"email\" size=\"25\" maxlength=\"100\" value=\"" . htmlspecialchars($comment['email']) . "\" /></td>",3);
		$output->addl("</tr>",2);
		$output->addl("<tr>",2);
		$output->addl("<td style=\"width: 40%;\"><b>Body:</b></td>",3);
		$output->addl("<td style=\"width: 60%;\"><textarea name=\"body\" rows=\"9\" cols=\"25\">" . htmlspecialchars($comment['body']) . "</textarea></td>",3);
		$output->addl("</tr>",2);
		$output->addl("<tr>",2);
		$output->addl("<td style=\"text-align: center;\" colspan=\"2\"><input type=\"submit\" class=\"button\" value=\"Save Changes\" /></td>",3);
		$output->addl("</tr>",2);
		$output->addl("</table>",1);
		$output->addl("</form>",1);
		$output->display();
	}
	else
	{
		$output->addl("<p>Whoops! The comment you specified does not seem to exist. At all. Perhaps it was deleted, or there may have been some other catastrophic error.</p>",1);
		$output->display();
	}
}
elseif ($action == 'edit2')
{
	$comment = $db->query_first("SELECT entryid FROM comment WHERE commentid = '" . addslashes($_POST['commentid']) . "' LIMIT 1");
	if (is_array($comment))
	{
		$db->query("UPDATE comment SET author = '" . addslashes($_POST['author']) . "', email = '" . addslashes($_POST['email']) . "', body = '" . addslashes($_POST['body']) . "' WHERE commentid = '" . addslashes($_POST['commentid']) . "' LIMIT 1");
		$output->addl("<p>Comment successfully updated.<br /><a href=\"comment.php?action=viewcomments&amp;entryid=" . urlencode($comment['entryid']) . "\">Back to this entry's comments list</a></p>",1);
		$output->display();
	}
	else
	{
		$output->addl("<p>God damn it, it looks like the entry you are trying to edit does not exist. It may have been deleted. We apologize.</p>",1);
		$output->display();
	}
}
elseif ($action == 'delete')
{
	$comment = $db->query_first("SELECT entryid, author FROM comment WHERE commentid = '" . addslashes($_GET['commentid']) . "' LIMIT 1");
	if (is_array($comment))
	{
		if ($comment['entryid'] != $options['deleted_entryid'])
		{
			$entry = $db->query_first("SELECT title FROM entry WHERE entryid = '" . $comment['entryid'] . "' LIMIT 1");
			$output->addl("<p><b>Deleting " . htmlspecialchars($comment['author']) . "'s comment in entry <i>" . htmlspecialchars($entry['title']) . "</i></b></p>",1);
			$output->addl("<form action=\"comment.php\" method=\"post\">",1);
			$output->addl("<input type=\"hidden\" name=\"action\" value=\"delete2\" />",1);
			$output->addl("<input type=\"hidden\" name=\"commentid\" value=\"" . htmlspecialchars($_GET['commentid']) . "\" />",1);
			$output->addl("<input type=\"radio\" name=\"deletemethod\" value=\"0\" checked=\"checked\" /> Do not delete this comment.<br />",1);
			$output->addl("<input type=\"radio\" name=\"deletemethod\" value=\"1\" /> Send it to the deleted comments entry. Sort of the \"SoBe Lean\" of comment deletion, if you will.<br />",1);
			if ($spruser['isadmin'])
			{
				$output->addl("<input type=\"radio\" name=\"deletemethod\" value=\"2\" /> Delete it entirely from the database. Like doing shots of vodka off a hooker's chest.<br />",1);
			}
			$output->addl("<input type=\"submit\" class=\"button\" value=\"Proceed\" />",1);
			$output->addl("</form>",1);
			$output->display();
		}
		else
		{
			$output->addl("<p>This comment is already in the deleted comments entry. You can't delete a comment twice, you dumbshit.</p>",1);
			$output->display();
		}
	}
	else
	{
		$output->addl("<p>The comment you specified does not seem to exist. Make sure it was not deleted. Please try again!</p>",1);
		$output->display();
	}
}
elseif ($action == 'delete2')
{
	$comment = $db->query_first("SELECT entryid FROM comment WHERE commentid = '" . addslashes($_POST['commentid']) . "' LIMIT 1");
	if ($db->num_rows())
	{
		if ($comment['entryid'] != $options['deleted_entryid'] || $_POST['deletemethod'] != 1)
		{
			if ($_POST['deletemethod'] == 2 && $spruser['isadmin'])
			{
				$db->query("DELETE FROM comment WHERE commentid = '" . addslashes($_POST['commentid']) . "' LIMIT 1");
				$db->query("UPDATE entry SET comments=comments-1 WHERE entryid = '" . $comment['entryid'] . "' LIMIT 1");
				$output->addl("<p>Comment successfully deleted! <a href=\"comment.php?action=viewcomments&amp;entryid=" . $comment['entryid'] . "\">Back to entry's comment page.</a></p>",1);
			}
			elseif ($_POST['deletemethod'] == 1)
			{
				$db->query("UPDATE comment SET entryid = '" . addslashes($options['deleted_entryid']) . "' WHERE commentid = '" . addslashes($_POST['commentid']) . "' LIMIT 1");
				$db->query("UPDATE entry SET comments=comments-1 WHERE entryid = '" . $comment['entryid'] . "' LIMIT 1");
				$db->query("UPDATE entry SET comments=comments+1 WHERE entryid = '" . addslashes($options['deleted_entryid']) . "' LIMIT 1");
				$output->addl("<p>Comment successfully sent to the garbage bin. <a href=\"comment.php?action=viewcomments&amp;entryid=" . $comment['entryid'] . "\">Back to entry's comment page.</a></p>",1);
			}
			else
			{
				header("Location: http://" . $_SERVER['HTTP_HOST'] . "$manager_url/comment.php?action=viewcomments&entryid=" . urlencode($comment['entryid']));
				exit;
			}
			$output->display();
		}
		else
		{
			$output->addl("<p>Whoops! Looks like the entry you specified is already soft-deleted. Someone else may have gotten to it before you.</p>",1);
			$output->display();
		}
	}
	else
	{
		$output->addl("<p>The comment you specified does not seem to exist! Oh dear. It may have been deleted before you got to it.</p>",1);
		$output->display();
	}
}

?>