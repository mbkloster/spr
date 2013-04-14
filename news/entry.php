<?php

$starttime = microtime();

require("../include/include.php");

// erase all the old captchas
$db->query("DELETE FROM captcha WHERE date < ".(gmdate("U")-3600*$captcha_timeout_hours));
$captcha_exists = $db->query_first("SELECT count(*) FROM captcha WHERE ipaddress = '".$_SERVER["REMOTE_ADDR"]."'");
if ($captcha_exists["count(*)"])
	$db->query("DELETE FROM captcha WHERE ipaddress = '".$_SERVER["REMOTE_ADDR"]."' LIMIT 1");

if (trim($_GET['entryid']) != '' || trim($_GET['commentid']) != '')
{
	if (isset($_GET['commentid']))
	{
		$comment = $db->query_first("SELECT entryid FROM comment WHERE commentid = '" . addslashes($_GET['commentid']) . "' LIMIT 1");
		if (is_array($comment))
		{
			$entryid = $comment['entryid'];
		}
		else
		{
			$entryid = 0;
		}
	}
	else
	{
		$entryid = addslashes($_GET['entryid']);
	}
	if (is_array($spruser))
	{
		$entry = $db->query_first("SELECT date, author, email, title, body, comments, isvisible, isopen FROM entry WHERE entryid = '$entryid' LIMIT 1");
		$preventry = $db->query_first("SELECT entryid, title, comments FROM entry WHERE entryid < $entryid ORDER BY entryid DESC LIMIT 1");
		$nextentry = $db->query_first("SELECT entryid, title, comments FROM entry WHERE entryid > $entryid ORDER BY entryid ASC LIMIT 1");
	}
	else
	{
		$entry = $db->query_first("SELECT date, author, email, title, body, comments, isvisible, isopen FROM entry WHERE entryid = '$entryid' AND isvisible > 0 LIMIT 1");
		$preventry = $db->query_first("SELECT entryid, title, comments FROM entry WHERE entryid < $entryid AND isvisible > 0 ORDER BY entryid DESC LIMIT 1");
		$nextentry = $db->query_first("SELECT entryid, title, comments FROM entry WHERE entryid > $entryid AND isvisible > 0 ORDER BY entryid ASC LIMIT 1");
	}
	if (is_array($entry))
	{
		$captcha = array_rand($captcha_options);
		// now insert new captcha:
		$db->query("INSERT INTO captcha (ipaddress, captchaid, date) VALUES ('".$_SERVER["REMOTE_ADDR"]."', '".$captcha."','".gmdate("U")."')");
		if (isset($_GET['storyonly']))
		{
			$entrysection = 'storyonly';
		}
		else
		{
			$entrysection = 'entries';
		}
		$output->js_files[] = "$local_url/misc.js";
		$output->subtitle = htmlspecialchars($entry['title']);
		if (is_array($nextentry))
		{
			$output->addl("<div id=\"righttop\"><a href=\"/$entrysection/" . $nextentry['entryid'] . "/\" title=\"" . $nextentry['comments'] . " comment(s)\">" . htmlspecialchars($nextentry['title']) . " »</a></div>",2);
		}
		else
		{
			$output->addl("<div id=\"righttop\"> &nbsp;</div>",2);
		}
		if (is_array($preventry))
		{
			$output->addl("<div id=\"lefttop\"><a href=\"/$entrysection/" . $preventry['entryid'] . "/\" title=\"" . $preventry['comments'] . " comment(s)\">« " . htmlspecialchars($preventry['title']) . "</a></div>",2);
		}
		else
		{
			$output->addl("<div id=\"lefttop\"> &nbsp;</div>",2);
		}
		$output->addl("<h2 class=\"title\">" . htmlspecialchars($entry['title']) . "</h2>",2);
		if ($entry['isvisible'])
		{
			$output->addl("<div class=\"subtext\">",2);
		}
		else
		{
			$output->addl("<div class=\"subtext\"><b>(INVISIBLE)</b> ",2);
		}
		$output->add("Entry posted on <b>" . date("M jS Y h:i A",$entry['date']+($options['gmt_offset']*3600)) . "</b> by <b><a href=\"mailto:" . htmlspecialchars($entry['email']) . "\">" . htmlspecialchars($entry['author']) . "</a></b></div>");
		$entry['body'] = preg_replace("/\<cut(.*)id=\"(.\s*)\"(.*)\>(.*)\<\/cut\>/s","<a name=\"cut\\2\"></a>\\4",$entry['body']);
		$entry['body'] = preg_replace("/\<cut(.*)\>(.\s*)\<\/cut\>/s","<a name=\"cut1\"></a>\\2",$entry['body']);
		$output->addl(str_replace("\n","\n\t\t",$entry['body']),2);
		if (isset($_GET['storyonly']))
		{
			$output->addl("<div class=\"split\">",2);
			$comments = $db->query_first("SELECT count(*) FROM comment WHERE entryid = '" . addslashes($entryid) . "' LIMIT 1");
			if ($comments['count(*)'] > 0 || $entry['isopen'])
			{
				if ($comments['count(*)'] == 1)
				{
					$output->add("<b><a href=\"/entries/" . urlencode($entryid) . "/#comments\">View the 1 comment on this entry</a>");
				}
				else
				{
					$output->add("<b><a href=\"/entries/" . urlencode($entryid) . "/#comments\">View the " . $comments['count(*)'] . " comments on this entry</a>");
				}
				if ($entry['isopen'])
				{
					$output->add(" - <a href=\"/entries/" . urlencode($entryid) . "/#postcomment\">Post your lovely feedback on this entry</a>");
				}
				$output->add("</b><br /><a href=\"$home_url/\">Senseless Political Ramblings</a> - <a href=\"/entries/\">Archives</a> - <a href=\"mailto:" . htmlspecialchars($entry['email']) . "\">Contact " . htmlspecialchars($entry['author']) . "</a></div>");
			}
			else
			{
				$output->add("<a href=\"$home_url/\">Senseless Political Ramblings</a> - <a href=\"/entries/\">Archives</a> - <a href=\"mailto:" . htmlspecialchars($entry['email']) . "\">Contact " . htmlspecialchars($entry['author']) . "</a></div>");
			}
		}
		else
		{
			$output->addl("<div class=\"split\"><a href=\"$home_url/\">Senseless Political Ramblings</a> - <a href=\"/entries/\">Archives</a> - <a href=\"mailto:" . htmlspecialchars($entry['email']) . "\">Contact " . htmlspecialchars($entry['author']) . "</a>",2);
			$db->query("SELECT commentid, date, isposter, ipaddress, homepage, author, body FROM comment WHERE entryid = '" . addslashes($entryid) . "' ORDER BY date ASC, commentid ASC");
			if ($db->num_rows() == 1)
			{
				$output->add("<br /><a name=\"comments\"></a><b>Showing this entry's 1 comment</b>");
			}
			else
			{
				$output->add("<br /><a name=\"comments\"></a><b>Showing this entry's " . $db->num_rows . " comments</b>");
			}
			$output->add(" (<a href=\"/storyonly/" . urlencode($entryid) . "/\">Show Story Only</a>)");
			if ($entry['isopen'])
			{
				$output->add(" (<a href=\"#postcomment\">Post Comment</a>)");
			}
			$output->add("</div>");
			$c = 1;
			while ($comment = $db->fetch_array())
			{
				$output->addl("<hr class=\"noshow\" />",2);
				if ($comment['homepage'] == '')
				{
					$output->addl("<div class=\"commentheading\"><div class=\"r\"><a name=\"c" . $comment['commentid'] . "\"></a>" . date("m-d-Y",$comment['date']+(3600*$options['gmt_offset'])) . " <b>" . date("H:i",$comment['date']+(3600*$options['gmt_offset'])) . "</b></div><a href=\"/comments/" . $comment['commentid'] . "/#c" . $comment['commentid'] . "\">#$c</a> by ",2);
				}
				else
				{
					$output->addl("<div class=\"commentheading\"><div class=\"r\"><a name=\"c" . $comment['commentid'] . "\"></a>" . date("m-d-Y",$comment['date']+(3600*$options['gmt_offset'])) . " <b>" . date("H:i",$comment['date']+(3600*$options['gmt_offset'])) . "</b></div><a href=\"/comments/" . $comment['commentid'] . "/#c" . $comment['commentid'] . "\">#$c</a> by <a href=\"" . htmlspecialchars($comment['homepage']) . "\" class=\"indistinct\" title=\"" . htmlspecialchars($comment['author']) . "'s crappy GeoCities site\">",2);
				}
				
				if ($comment['isposter'])
				{
					$output->add("<b><i>" . htmlspecialchars($comment['author']) . "</i></b>");
				}
				else
				{
					$output->add("<b>" . htmlspecialchars($comment['author']) . "</b>");
				}
				
				if ($comment['homepage'] != '')
				{
					$output->add("</a>");
				}
				$output->add("</div>");
				$output->addl("<div class=\"comment\">" . nl2br(htmlspecialchars($comment['body'])) . "</div>",2);
				if (gmdate("U") < ($comment['date']+$options['deletetime_max']*60) && $comment['ipaddress'] == $_SERVER['REMOTE_ADDR'] && $entryid != $options['deleted_entryid'])
				{
					$output->addl("<div class=\"commentbottom\"><a href=\"/comments/post/comment/" . $comment['commentid'] . "/\" onclick=\"void(0); quotecomment('" . addslashes($comment['author']) . "','" . $comment['commentid'] . "','" . addslashes(str_replace("\n","§",str_replace("\r","",$comment['body']))) . "',document.postcomment.body); return false;\"><img src=\"$images_url/icons/quote.gif\" alt=\"Quote\" title=\"Give this comment more attention than it deserves, frankly\" /></a> <a href=\"/comments/delete/" . $comment['commentid'] . "/\"><img src=\"$images_url/icons/delete.gif\" alt=\"Delete\" title=\"Delete this textual garbage\" /></a></div>",2);
				}
				else
				{
					$output->addl("<div class=\"commentbottom\"><a href=\"/comments/post/comment/" . $comment['commentid'] . "/\" onclick=\"void(0); quotecomment('" . addslashes($comment['author']) . "','" . $comment['commentid'] . "','" . addslashes(str_replace("\n","§",str_replace("\r","",$comment['body']))) . "',document.postcomment.body); return false;\"><img src=\"$images_url/icons/quote.gif\" alt=\"Quote\" title=\"Give this comment more attention than it deserves, frankly\" /></a></div>",2);
				}
				$c++;
			}
			if ($entry['isopen'] || is_array($spruser))
			{
				postcomment('', $captcha, $entry['isopen']);
			}
			else
			{
				$output->addl("<table width=\"60%\" align=\"center\" style=\"margin-top: 15px;\" cellspacing=\"0\">",2);
				$output->addl("<tr>",3);
				$output->addl("<th>Post Comment</th>",4);
				$output->addl("</tr>",3);
				$output->addl("<tr>",3);
				$output->addl("<td class=\"alt1\">Sorry, this entry is not open for comments.</td>",4);
				$output->addl("</tr>",3);
				$output->addl("</table>",2);
			}
		}
		$output->reverse_subtitle = 1; // display entry titles first in title bar
		$output->display();
	}
	else
	{
		error_notfound($_SERVER['REQUEST_URI']);
	}
}
else
{
	$output->addl("<h2>No entry specified!</h2>",2);
	$output->addl("<p>No news entry specified. If you followed a link from elsewhere on this site to this page, something has clearly gone dreadfully wrong. Drop an email to <a href=\"mailto:$tech_email\">$tech_name</a> with the URL of the page that referred you to here.</p>",2);
	$output->addl("<p><b><a href=\"$home_url/\">Senseless Political Ramblings home</a> - <a href=\"archive/\">News Archives</a></b></p>",2);
	$output->display();
}

?>