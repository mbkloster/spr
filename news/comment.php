<?php

$starttime = microtime();
require("../include/include.php");

if ($action == 'clearcookies')
{
	@setcookie('author','',(time()+1),"/",$_SERVER['HTTP_HOST']);
	@setcookie('email','',(time()+1),"/",$_SERVER['HTTP_HOST']);
	@setcookie('homepage','',(time()+1),"/",$_SERVER['HTTP_HOST']);
	header("Location: " . $_SERVER['HTTP_REFERER']);
	exit;
}
elseif ($action == 'post')
{
	// erase all the old captchas
	$db->query("DELETE FROM captcha WHERE date < ".(gmdate("U")-3600*$captcha_timeout_hours));
	$captcha_exists = $db->query_first("SELECT count(*) FROM captcha WHERE ipaddress = '".$_SERVER["REMOTE_ADDR"]."'");
	if ($captcha_exists["count(*)"])
		$db->query("DELETE FROM captcha WHERE ipaddress = '".$_SERVER["REMOTE_ADDR"]."' LIMIT 1");
	if (isset($_GET['commentid']))
	{
		$comment = $db->query_first("SELECT entryid, author, body FROM comment WHERE commentid = '" . addslashes($_GET['commentid']) . "' LIMIT 1");
		if (!is_array($comment))
		{
			$output->addl("<p>It seems that the comment you specified does not exist. Perhaps it was deleted, or maybe you're just making shit up.</p>",2);
			$output->display();
			exit;
		}
		$entryid = $comment['entryid'];
	}
	else
	{
		$entryid = addslashes($_GET['entryid']);
	}
	$entry = $db->query_first("SELECT title, author, email, isopen, isvisible FROM entry WHERE entryid='$entryid' LIMIT 1");
	if (!is_array($entry))
	{
		$output->addl("<p>It seems that the entry you specified does not exist. Perhaps it was deleted, or maybe you're just making shit up.</p>",2);
		$output->display();
		exit;
	}
	if (!$entry['isvisible'] && !is_array($spruser))
	{
		$output->addl("<p>It seems that the entry you specified does not exist. Perhaps it was deleted, or maybe you're just making shit up.</p>",2);
		$output->display();
		exit;
	}
	if (!$entry['isopen'] && !is_array($spruser))
	{
		$output->addl("<p>Sorry! It seems that the entry you specified is now closed from comments.</p>",2);
		$output->display();
		exit;
	}
	
	$captcha = array_rand($captcha_options);
	// now insert new captcha:
	$db->query("INSERT INTO captcha (ipaddress, captchaid, date) VALUES ('".$_SERVER["REMOTE_ADDR"]."', '".$captcha."','".gmdate("U")."')");
	
	$output->subtitle = "Post Comment on '" . htmlspecialchars($entry['title']) . "'";
	$output->addl("<h2 class=\"title\">" . htmlspecialchars($entry['title']) . "</h2>",2);
	$output->addl("<div class=\"subtext\"><a href=\"/entries/" . htmlspecialchars($entryid) . "/\">Posted</a> by <b><a href=\"mailto:" . htmlspecialchars($entry['email']) . "\">" . htmlspecialchars($entry['author']) . "</a></b></div>",2);
	if (isset($comment))
	{
		postcomment("---------------------\n" . $comment['author'] . " went on record as saying:\n" . $comment['body'] . "\n---------------------\n",$captcha);
	}
	else
	{
		postcomment('',$captcha);
	}
	$output->display();
}	
elseif ($action == 'post2')
{
	if (isset($_POST['entryid']) && trim($_POST['author']) != '' && strlen(trim($_POST['email'])) >= 6 && strpos($_POST['email'],"@") < strrpos($_POST['email'],".") && strpos($_POST['email'],".") !== false && strpos($_POST['email'],"@") !== false && trim($_POST['body']) != '')
	{
		$nametaken = $db->query_first("SELECT count(*) FROM user WHERE username = '" . addslashes($_POST['author']) . "' LIMIT 1");
		$isposter = 0;
		if (strlen($_POST['body']) > $options['maxl_body'])
		{
			standard_error("Post Comment","Your comment may not exceed " . $options['maxl_body'] . " characters in length.");
			exit;
		}
		if (trim($_POST['homepage']) == 'http://')
		{
			$homepage = '';
		}
		else
		{
			$homepage = addslashes(htmlspecialchars(trim($_POST['homepage'])));
		}
		if ($nametaken['count(*)'])
		{
			if (!is_array($spruser) || $spruser['username'] != $_POST['author'])
			{
				standard_error("Post Comment","Whoops! Looks like the author name you picked is reserved. Nice try anyway.");
				exit;
			}
			else
			{
				$isposter = 1;
			}
		}
		if (strlen($_POST['author']) > $options['maxl_author'] && !$isposter)
		{
			standard_error("Your name is too goddamn long! It must be " . $options['maxl_author'] . " characters long or less.");
			exit;
		}
		$entry = $db->query_first("SELECT isopen, isvisible FROM entry WHERE entryid = '" . addslashes($_POST['entryid']) . "' LIMIT 1");
		if ($db->num_rows())
		{
			if ($entry['isvisible'] || is_array($spruser))
			{
				if ($entry['isopen'] || is_array($spruser))
				{
					//$lastcomment = $db->query_first("SELECT count(*) FROM comment WHERE ipaddress = '" . $_SERVER['REMOTE_ADDR'] . "' AND date > " . (gmdate("U")-$options['comment_delay']));
					$lastcomment=0;
					if (!$lastcomment['count(*)'])
					{
						// commment is within the time limit
						$captchaid = $db->query_first("SELECT captchaid FROM captcha WHERE ipaddress = '".$_SERVER['REMOTE_ADDR']."' LIMIT 1");
						if (is_array($captchaid))
						{
							$answers = $captcha_options[$captchaid['captchaid']][1];
							$answers = explode(" ",$answers);
							$answer = str_replace(" ","",$_POST['answer']);
							$answer = str_replace(".","",$answer);
							$answer = str_replace(",","",$answer);
							$answer = str_replace("?","",$answer);
							$answer = str_replace("!","",$answer);
							$answer = str_replace("-","",$answer);
							$answer = str_replace("\"","",$answer);
							$answer = str_replace("'","",$answer);
							$answer = str_replace("(","",$answer);
							$answer = str_replace(")","",$answer);
							$answer = strtolower($answer);
							$answer_correct = 0;
							for ($i = 0; $i < count($answers); $i++)
							{
								if ($answers[$i] == $answer)
								{
									$answer_correct = 1;
									break;
								}
							}
							if ($answer_correct)
							{
								if (!isset($_COOKIE['author']) || !isset($_COOKIE['email']) || !isset($_COOKIE['homepage']))
								{
									if ($_POST['saveinfo'] == 1)
									{
										// set the info cookies
										@setcookie('author',$_POST['author'],time()+(3600*24*$options['cookiestore_days']),"/",$_SERVER['HTTP_HOST']);
										@setcookie('email',$_POST['email'],time()+(3600*24*$options['cookiestore_days']),"/",$_SERVER['HTTP_HOST']);
										@setcookie('homepage',$_POST['homepage'],time()+(3600*24*$options['cookiestore_days']),"/",$_SERVER['HTTP_HOST']);
									}
								}
								else
								{
									if ($_POST['saveinfo'] == 2)
									{
										// update the info cookies
										@setcookie('author',$_POST['author'],time()+(3600*24*$options['cookiestore_days']),"/",$_SERVER['HTTP_HOST']);
										@setcookie('email',$_POST['email'],time()+(3600*24*$options['cookiestore_days']),"/",$_SERVER['HTTP_HOST']);
										@setcookie('homepage',$_POST['homepage'],time()+(3600*24*$options['cookiestore_days']),"/",$_SERVER['HTTP_HOST']);
									}
									elseif ($_POST['saveinfo'] == 0)
									{
										// clear the info cookies to death
										@setcookie('author','',(time()+1),"/",$_SERVER['HTTP_HOST']);
										@setcookie('email','',(time()+1),"/",$_SERVER['HTTP_HOST']);
										@setcookie('homepage','',(time()+1),"/",$_SERVER['HTTP_HOST']);
									}
								}
								$db->query("INSERT INTO comment (entryid, date, isposter, ipaddress, useragent, email, homepage, author, body)"
								.        "\nVALUES ('" . addslashes($_POST['entryid']) . "', '" . gmdate("U") . "', '$isposter', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" . addslashes($_POST['email']) . "', '" . addslashes($homepage) . "', '" . addslashes($_POST['author']) . "', '" . addslashes($_POST['body']) . "')");
								$commentid = $db->insert_id();
								$db->query("UPDATE entry SET comments=comments+1 WHERE entryid = '" . addslashes($_POST['entryid']) . "' LIMIT 1");
								header("Location: " . $server_url . "/comments/" . $commentid . "/#c" . $commentid);
								exit;
							}
							else
							{
								standard_error("Post Comment","Looks like you didn't answer our wonderful question properly. Make sure you filled it in right (spelling counts!) and try again. Sorry for this terrible inconvenience.");
							}
						}
						else
							standard_error("Post Comment","You do not have a verification question. This may be due to you waiting too long to submit your comment, or it could be some error on the part of our servers. Either way, try posting your comment again. Sorry about that.");
					}
					else
					{
						standard_error("Post Comment","Not so fast! You can only post one comment every " . $options['comment_delay'] . " seconds. Please wait until this duration expires and try again.");
					}
				}
				else
				{
					standard_error("Post Comment","Sorry, but the entry you specified has now been closed from new comments. Your comment was probably crap anyway, so just drop it.");
				}
			}
			else
			{
				standard_error("Post Comment","Whoops! Looks like the entry you specified does not exist. It may have been deleted while you were typing your comment, or something equally dreadful.");
			}
		}
		else
		{
			standard_error("Post Comment","Whoops! Looks like the entry you specified does not exist. It may have been deleted while you were typing your comment, or something equally dreadful.");
		}
	}
	else
	{
		standard_error("Post Comment","Oh no! Looks like you forgot to fill in one or more required fields properly. Just in case you thought you could get around it, your email address <i>is</i> required. Anyway, go back and fill those in and try again.");
	}
}
elseif ($action == 'delete')
{
	$comment = $db->query_first("SELECT entryid, date, ipaddress FROM comment WHERE commentid = '" . addslashes($_GET['commentid']) . "' LIMIT 1");
	if (is_array($comment))
	{
		$entry = $db->query_first("SELECT title, isvisible, isopen FROM entry WHERE entryid = '" . $comment['entryid'] . "' LIMIT 1");
		if ((($entry['isopen'] && $entry['isvisible']) || is_array($spruser)) && $comment['ipaddress'] == $_SERVER['REMOTE_ADDR'] && $comment['date'] > (gmdate("U")-($options['deletetime_max']*60)))
		{
			$output->subtitle = 'Delete Comment';
			$output->addl("<h3>Delete Comment From Entry '" . htmlspecialchars($entry['title']) . "'</h3>",2);
			$output->addl("<p>Are you sure you want to delete this comment?</p>",2);
			$output->addl("<form action=\"/comments/confirmdelete/\" method=\"post\">",2);
			$output->addl("<input type=\"hidden\" name=\"action\" value=\"delete2\" />",2);
			$output->addl("<input type=\"hidden\" name=\"commentid\" value=\"" . htmlspecialchars($_GET['commentid']) . "\" />",2);
			$output->addl("<input class=\"button\" type=\"submit\" name=\"submit\" value=\"Hell Yes\" /> <input class=\"button\" type=\"submit\" name=\"submit\" value=\"God No\" />",2);
			$output->addl("</form",2);
			$output->display();
		}
		else
		{
			standard_error('Delete Comment',"This comment cannot be deleted. The entry that it's in may be closed, or it may have been posted by a different person, or it may be past the amount of time you had to delete it.");
			exit;
		}
	}
	else
	{
		error_notfound($_SERVER['REQUEST_URI']);
	}
}
elseif ($action == 'delete2')
{
	if ($_POST['submit'] == 'Hell Yes')
	{
		$comment = $db->query_first("SELECT entryid, date, ipaddress FROM comment WHERE commentid = '" . addslashes($_POST['commentid']) . "' LIMIT 1");
		if (is_array($comment))
		{
			$entry = $db->query_first("SELECT title, isvisible, isopen FROM entry WHERE entryid = '" . $comment['entryid'] . "' LIMIT 1");
			if ((($entry['isopen'] && $entry['isvisible']) || is_array($spruser)) && $comment['ipaddress'] == $_SERVER['REMOTE_ADDR'] && $comment['date'] > (gmdate("U")-($options['deletetime_max']*60)))
			{
				$db->query("UPDATE comment SET entryid = '" . addslashes($options['deleted_entryid']) . "' WHERE commentid = '" . addslashes($_POST['commentid']) . "' LIMIT 1");
				$db->query("UPDATE entry SET comments = comments-1 WHERE entryid = '" . $comment['entryid'] . "' LIMIT 1");
				$db->query("UPDATE entry SET comments = comments+1 WHERE entryid = '" . addslashes($options['deleted_entryid']) . "' LIMIT 1");
				header("Location: $server_url/entries/" . $comment['entryid'] . "/");
				exit;
			}
			else
			{
				standard_error('Delete Comment',"This comment cannot be deleted. The entry that it's in may be closed, or it may have been posted by a different person, or it may be past the amount of time you had to delete it.");
				exit;
			}
		}
		else
		{
			standard_error('Delete Comment',"This comment no longer seems to exist.");
			exit;
		}
	}
	else
	{
		header("Location: $server_url/comments/" . $_POST['commentid'] . "/");
		exit;
	}
}

?>