<?php

/*
	Senseless Political Ramblings: Core Include File
	
	Used in ALL scripts. The contents of this file should be
	relatively "lean" - keep things like options and functions
	in their respective scripts.
*/

if ($_SERVER['HTTP_HOST'] == "www.senselesspoliticalramblings.com")
{
	// going to www.senselesspoliticalramblings.com is not allowed - forward
	if (preg_match('/(.)*\/index\.(php|html|htm)(\?.*)?$/i',$_SERVER['REQUEST_URI']))
	{
		$uri = preg_replace('/(.)*\/index\.(php|html|htm)((\?|\/).*)?$/i',"/\\1\\3",$_SERVER['REQUEST_URI']);
	}
	else
	{
		$uri = $_SERVER['REQUEST_URI'];
	}
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://senselesspoliticalramblings.com" . $uri);
	exit;
}
elseif (preg_match('/(.*)\/index\.(php|html|htm)((\?|\/).*)?$/i',$_SERVER['REQUEST_URI']))
{
	// index.php/html etc. urls are not allowed - forward
	$uri = preg_replace('/(.*)\/index\.(php|html|htm)((\?|\/).*)?$/i',"\\1\\3",$_SERVER['REQUEST_URI']);
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://" . $_SERVER['HTTP_HOST'] . $uri);
	exit;
}

require("settings.php");
require("options.php");

require("database_mysql.php");
require("output.php");

if (ini_get("magic_quotes_gpc")) // check if magic_quotes_gpc is on and if so, strip slashes from gpc data
{
	while (list($key,$value) = each($_GET))
	{
		$_GET[$key] = stripslashes($value);
	}
	while (list($key,$value) = each($_POST))
	{
		$_POST[$key] = stripslashes($value);
	}
	while (list($key,$value) = each($_COOKIE))
	{
		$_COOKIE[$key] = stripslashes($value);
	}
	reset($_GET);
	reset($_POST);
	reset($_COOKIE);
}

set_magic_quotes_runtime(0);

if (isset($_POST['action']))
{
	$action = $_POST['action'];
}
elseif (isset($_GET['action']))
{
	$action = $_GET['action'];
}
elseif (isset($action))
{
	unset($action);
}

if ($debug && (isset($_GET['scriptinfo']) || isset($_POST['scriptinfo'])))
{
	echo "<html><head><title>Script Info</title></head><body>"
	.  "\n<h2>Scipt Info: " . $_SERVER['PHP_SELF'] . "</h2>"
	.  "\n<p><b>Time:</b> " . gmdate("r") . "</p>";
}

// Set up the database shit
$db = new sql_db;

$db->app_name = 'Senseless Political Ramblings';
$db->app_short_name = 'SPR';

$db->server = $db_server;
$db->username = $db_username;
$db->password = $db_password;
$db->database = $db_name;

$db->tech_name = $tech_name;
$db->tech_email = $tech_email;

$db->log_errors = $log_errors;
$db->log_file = $log_file;

if ($debug && (isset($_GET['scriptinfo']) || isset($_POST['scriptinfo'])))
{
	$db->debug_mode = 2;
}
elseif ($debug)
{
	$db->debug_mode = 1;
}

$db->connect($use_pconnects);

unset($db_password);
unset($db->password);

// Done setting up the database shit

// Set up the output shit
$output = new output_handler;

if ($debug && (isset($_GET['scriptinfo']) || isset($_POST['scriptinfo'])))
{
	$output->show_info = 1;
}

function error_msg($title,$header,$body)
{
	global $local_url, $home_url, $options;
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">"
	.    "\n<html lang=\"en\" dir=\"ltr\">"
	.    "\n<head>"
	.    "\n\t<title>$title</title>"
	.    "\n\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />"
	.    "\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"$local_url/" . $options['css_file'] . "\" />"
	.    "\n</head>"
	.    "\n<body>"
	.    "\n\t<div class=\"error\"><h2 class=\"error\">$header</h2>"
	.    "\n\t\t$body"
	.    "\n\t\t<p><b><a href=\"$home_url\">Senseless Political Ramblings home</a></b></p>"
	.    "\n\t</div>"
	.    "\n</body>"
	.    "\n</html>";
}

$output->title = 'Senseless Political Ramblings';

$output->css_files[] = "$local_url/" . $options['css_file'];

$output->add_link_tag('alternate','application/rss+xml','Senseless Political Ramblings','/feeds/rss'); // add feed link
$output->add_link_tag('icon','image/x-icon','','/favicon.ico');
$output->add_link_tag('shortcut icon','image/x-icon','','/favicon.ico');

// Output display:
// $output->display($starttime,array_sum($db->time),count($db->sql));

// Unique visitor crap

if (strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,7)) != 'nbepviu' && substr($_SERVER['REMOTE_ADDR'],0,10) != '65.214.45.' && strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,6)) != 'msnbot' && substr_count($_SERVER['HTTP_USER_AGENT'],'; Yahoo! Slurp;') < 1 && strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,6)) != 'google' && strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,9)) != 'surveybot' && strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,7)) != 'gigabot' && strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4)) != 'w3c_' && strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,11)) != 'baiduspider' && substr($_SERVER['REMOTE_ADDR'],0,7) != '66.249.' && substr($_SERVER['REMOTE_ADDR'],0,6) != '72.30.' && $_SERVER['REMOTE_ADDR'] != '207.46.98.65' && $_SERVER['REMOTE_ADDR'] != '128.30.52.13' && substr($_SERVER['REMOTE_ADDR'],0,5) != '72.4.' && $_SERVER['REMOTE_ADDR'] != '66.154.103.156' && substr($_SERVER['REMOTE_ADDR'],0,7) != '207.46.' && substr($_SERVER['REMOTE_ADDR'],0,5) != '74.6.' and substr($_SERVER['REMOTE_ADDR'],0,10) != '65.214.44.' && strpos($_SERVER['HTTP_USER_AGENT'],'Googlebot') === FALSE && strpos($_SERVER['HTTP_USER_AGENT'],'Ask Jeeves') === FALSE && $_SERVER['HTTP_USER_AGENT'] != 'ia_archiver')
{
	$isvisitor = $db->query_first("SELECT count(*) FROM uniquevisitor WHERE ipaddress = '" . $_SERVER['REMOTE_ADDR'] . "'");
	if ($isvisitor['count(*)'])
	{
		$db->query("UPDATE uniquevisitor SET lastrequest = '" . gmdate("U") . "', hits=hits+1, lasturl = '" . addslashes($_SERVER['REQUEST_URI']) . "', useragent = '" . addslashes($_SERVER['HTTP_USER_AGENT']) . "' WHERE ipaddress = '" . $_SERVER['REMOTE_ADDR'] . "' LIMIT 1");
	}
	else
	{
		$db->query("INSERT INTO uniquevisitor (ipaddress, firstrequest, lastrequest, hits, lasturl, useragent) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "', '" . gmdate("U") . "', '" . gmdate("U") . "', '1', '" . addslashes($_SERVER['REQUEST_URI']) . "', '" . addslashes($_SERVER['HTTP_USER_AGENT']) . "')");
	}
}

if (isset($_COOKIE['userid']) && isset($_COOKIE['password']))
{
	$spruser = $db->query_first("SELECT userid, username, email, registrationdate, lastlogindate, isadmin, authorname FROM user WHERE userid = '" . $_COOKIE['userid'] . "' AND password = md5(CONCAT(salt,'" . $_COOKIE['password'] . "')) AND isactive = '1' LIMIT 1");
	if (!$spruser)
	{
		setcookie("userid","",(time()+1),"/",".senselesspoliticalramblings.com");
		setcookie("password","",(time()+1),"/",".senselesspoliticalramblings.com");
	}
}
else
{
	$spruser = 0;
}
if (is_array($spruser))
{
	$output->header_addl("<!-- USER LOGGED IN: " . htmlspecialchars($spruser['username']) . " -->",1);
}
$output->header_addl("<a href=\"$home_url/\"><img src=\"$images_url/" . $options['theme'] . "-logo-l.png\" alt=\"Senseless Political Ramblings\" title=\"Senseless Political Ramblings\" style=\"float: left;\" /></a>",1);
$output->header_addl("<div id=\"rightlogo\"></div>",1);
$output->header_addl("<ul class=\"navmenu\">",1);
$output->header_addl("<li class=\"firstnavitem\"><b>Site:</b></li>",2);
$output->header_addl("<li class=\"firstnavitem\"><a class=\"navitem\" href=\"$home_url/\">Home</a></li>",2);
$output->header_addl("<li class=\"navitem\"><a class=\"navitem\" href=\"/entries/\">Archives</a></li>",2);
$output->header_addl("<li class=\"navitem\"><a class=\"navitem\" href=\"/search/\">Search</a></li>",2);
$output->header_addl("<li class=\"navitem\"><a class=\"navitem\" href=\"/tools/\">Tools</a></li>",2);
$output->header_addl("<li class=\"navitem\"><a class=\"navitem\" href=\"/feeds/\">Subscribe</a></li>",2);
$output->header_addl("</ul>",1);
$output->header_addl("<ul class=\"navmenu\">",1);
$output->header_addl("<li class=\"firstnavitem\"><b>Hosted:</b></li>",2);
$output->header_addl("<li class=\"firstnavitem\"><a class=\"navitem\" href=\"http://canadabot.senselesspoliticalramblings.com/\">CanadaBot</a></li>",2);
$output->header_addl("</ul>",1);
$output->header_addl("<div id=\"linksbox\">",1);
$output->header_addl("<h4>Hit List</h4>",2);
$output->header_addl("<ul>",2);
$first = 1;
for ($i = 0; $i < count($links_urls); $i++)
{
	if ($first)
	{
		$output->header_addl("<li>",3);
		$first = 0;
	}
	else
	{
		$output->header_addl("<li>",3);
	}
	$output->header_add("<a href=\"" . $links_urls[$i] . "\">" . $links_nams[$i] . "</a></li>");
}
$output->header_addl("</ul>",2);
$output->header_addl("</div>",1);
$output->header_addl("<div id=\"main\">",1);
if (!$norecentupdates)
{
	$db->query("SELECT date, icon, url, title FROM recentupdate ORDER BY date DESC LIMIT " . $options['recentupdate_count']);
	if ($db->num_rows())
	{
		$output->header_addl("<div id=\"recentupdates\">",2);
		$output->header_addl("<h3 class=\"tinyheader\">Features of Note</h3>",3);
		$output->header_addl("<ul>",3);
		$first = 1;
		while ($recentupdate = $db->fetch_array())
		{
			if ($first)
			{
				$output->header_addl("<li class=\"firstitem\">",4);
				$first = 0;
			}
			else
			{
				$output->header_addl("<li>",4);
			}
			if ($recentupdate['icon'] != -1)
			{
				$output->header_add("<a href=\"" . htmlspecialchars($recentupdate['url']) . "\" class=\"noborder\"><img src=\"$images_url/icons/update-" . htmlspecialchars($options['recentupdate_icons'][$recentupdate['icon']]) . ".gif\" alt=\"[" . htmlspecialchars($options['recentupdate_icons'][$recentupdate['icon']]) . "]\" title=\"\" /></a> <a href=\"" . htmlspecialchars($recentupdate['url']) . "\">" . htmlspecialchars($recentupdate['title']) . " (" . date("m-d",$recentupdate['date']+(3600*$options['gmt_offset'])) . ")</a></li>");
			}
			else
			{
				$output->header_add("<a href=\"" . htmlspecialchars($recentupdate['url']) . "\">" . htmlspecialchars($recentupdate['title']) . " (" . date("m-d",$recentupdate['date']+(3600*$options['gmt_offset'])) . ")</a></li>");
			}
		}
		$output->header_addl("</ul>",3);
		$output->header_addl("</div>",2);
	}
}

$output->footer_addl("</div>",1);
$output->footer_addl("<p id=\"disclaimer\">All layout and content Copyright © 2006 Senseless Political Ramblings. Some materials may be quoted or otherwise used from other sources. You may quote or link to content on Senseless Political Ramblings, provided that you give us, or the respective author, credit for the work. Questions and comments should be directed to <a href=\"mailto:$webmaster_email\">$webmaster_name's dungeon of death and destruction</a>. We value your feedback!!! Oh, and even though I can't stand those godawful \"W3C VALID XHTML!\" images people shove onto their pages, this site is indeed all valid XHTML Transitional. You can <a href=\"http://validator.w3.org/check?uri=http%3A%2F%2F" . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . "\">validate it</a> in case you're unsure.</p>",1);

function postcomment($postcontent = "", $captcha = 0, $isopen = 1)
{
	global $output, $entryid, $_COOKIE, $captcha_options;
	$output->addl("<form name=\"postcomment\" action=\"/comments/post/\" method=\"post\">",2);
	$output->addl("<input type=\"hidden\" name=\"action\" value=\"post2\" />",2);
	$output->addl("<input type=\"hidden\" name=\"entryid\" value=\"" . htmlspecialchars($entryid) . "\" />",2);
	$output->addl("<table style=\"width: 60%; margin: auto; margin-top: 15px;\" cellspacing=\"0\">",2);
	$output->addl("<tr>",3);
	$output->addl("<th colspan=\"2\"><a name=\"postcomment\"></a>Post Comment</th>",4);
	$output->addl("</tr>",3);
	$output->addl("<tr>",3);
	$output->addl("<td style=\"width: 40%;\" class=\"alt1\"><b>Name:</b><br /><span class=\"small\">The name that will appear on your comment.</span></td>",4);
	$output->addl("<td style=\"width: 60%;\" class=\"alt1\"><input type=\"text\" name=\"author\" size=\"25\" maxlength=\"50\" value=\"" . htmlspecialchars($_COOKIE['author']) . "\" /></td>",4);
	$output->addl("</tr>",3);
	$output->addl("<tr>",3);
	$output->addl("<td style=\"width: 40%;\" class=\"alt2\"><b>Email Address:</b><br /><span class=\"small\">Your email address will NEVER be given out.</span></td>",4);
	$output->addl("<td style=\"width: 60%;\" class=\"alt2\"><input type=\"text\" name=\"email\" size=\"25\" maxlength=\"100\" value=\"" . htmlspecialchars($_COOKIE['email']) . "\" /></td>",4);
	$output->addl("</tr>",3);
	$output->addl("<tr>",3);
	$output->addl("<td style=\"width: 40%;\" class=\"alt1\"><b>Homepage:</b><br /><span class=\"small\">Your exciting web site which we want to see! Optional.</span></td>",4);
	if (isset($_COOKIE['homepage']))
	{
		$output->addl("<td style=\"width: 60%;\" class=\"alt1\"><input type=\"text\" name=\"homepage\" size=\"25\" maxlength=\"100\" value=\"" . htmlspecialchars($_COOKIE['homepage']) . "\" /></td>",4);
	}
	else
	{
		$output->addl("<td style=\"width: 60%;\" class=\"alt1\"><input type=\"text\" name=\"homepage\" size=\"25\" maxlength=\"100\" value=\"http://\" /></td>",4);
	}
	$output->addl("</tr>",3);
	$output->addl("<tr>",3);
	$output->addl("<td style=\"width: 40%;\" class=\"alt2\"><b>Save Info?</b><br /><span class=\"small\">Save your name/email/homepage settings.</span></td>",4);
	if (isset($_COOKIE['author']) && isset($_COOKIE['email']) && isset($_COOKIE['homepage']))
	{
		$output->addl("<td style=\"width: 60%;\" class=\"alt2\"><input class=\"box\" type=\"radio\" name=\"saveinfo\" value=\"1\" checked=\"checked\" /> Keep as-is <input class=\"box\" type=\"radio\" name=\"saveinfo\" value=\"2\" /> Update <input class=\"box\" type=\"radio\" name=\"saveinfo\" value=\"0\" /> <a href=\"/clearcookies/\">Clear</a></td>",4);
	}
	else
	{
		$output->addl("<td style=\"width: 60%;\" class=\"alt2\"><input class=\"box\" type=\"checkbox\" name=\"saveinfo\" value=\"1\" /> Yes (requires cookies)</td>",4);
	}
	$output->addl("</tr>",3);
	$output->addl("<tr>",3);
	$output->addl("<td style=\"width: 40%;\" class=\"alt1\"><b>Comment:</b><br /><span class=\"small\">Your comment will appear exactly as you type it.</span></td>",4);
	$output->addl("<td style=\"width: 60%;\" class=\"alt1\"><textarea name=\"body\" rows=\"11\" cols=\"40\">" . htmlspecialchars($postcontent) . "</textarea></td>",4);
	$output->addl("</tr>",3);
	$output->addl("<tr>",3);
	$output->addl("<td style=\"width: 40%;\" class=\"alt2\"><b>Spam Prevention:</b><br /><span class=\"small\">Just answer this amazingly simple question to prove you're not a bot. [<a href=\"/spamprevention/\" title=\"What is this thing?\">?</a>]</span></td>",4);
	$output->addl("<td style=\"width: 60%;\" class=\"alt2\">".$captcha_options[$captcha][0]." <input type=\"text\" name=\"answer\" size=\"10\" maxlength=\"100\" value=\"\" /></td>",4);
	$output->addl("</tr>",3);
	$output->addl("<tr>",3);
	if ($isopen)
	{
		$output->addl("<td colspan=\"2\" style=\"text-align: center;\" class=\"alt2\"><input class=\"button\" type=\"submit\" name=\"submit\" value=\"Post Comment\" accesskey=\"s\" /></td>",4);
	}
	else
	{
		$output->addl("<td colspan=\"2\" style=\"text-align: center;\" class=\"alt2\"><input class=\"button\" type=\"submit\" name=\"submit\" value=\"Post Comment\" accesskey=\"s\" /> (<b>Note:</b> Entry is closed for comments normally)</td>",4);
	}
	$output->addl("</tr>",3);
	$output->addl("</table>",2);
	$output->addl("</form>",2);
}

function str_complete_replace($find, $replace, $string)
{
	// Completely replaces all instances of $find with $replace - as many replaces as it takes to do so
	if (is_array($find))
	{
		$noredo = array(); // array to store which keys don't need re-replacing (used to prevent stupidity with some repeating replacements)
		$perfect = 0; // the number of finds that need no replacing
		while ($perfect < count($find))
		{
			$perfect = 0;
			for ($i = 0; $i < count($find); $i++)
			{
				$findl = $find[$i];
				if ($findl == "") { $findl = " "; }
				if (is_array($replace))
				{
					$replacel = $replace[$i];
				}
				else
				{
					$replacel = $replace;
				}
				if (strpos($string,$findl) !== false)
				{
					if (strpos($replacel,$findl) === false)
					{
						do
						{
							$string = str_replace($findl,$replacel,$string);
						}
						while (strpos($string,$findl) !== false);
					}
					else
					{
						if ($noredo[$i] != true)
						{
							$string = str_replace($findl,$replacel,$string);
							$noredo[$i] = true;
						}
						$perfect++; // The amount of unreplaced finds must go up; otherwise it starts
							    // an indefinitely loop with the search string
					}
				}
				else
				{
					// No replacing needed, increment the unreplaced finds
					$perfect++;
				}
			}
		}
	}
	else
	{
		if ($find == "") { $find = " "; }
		if (strpos($replace,$find) === false)
		{
			echo strpos($string,$findl);
			while (strpos($string,$find) !== false)
			{
				$string = str_replace($find,$replace,$string);
			}
		}
		else
		{
			// What it finds is in what it replaces things as; hence, it will create
			// an infinite loop under normal circumstances. Just replace it once to avoid
			// this.
			$string = str_replace($find,$replace,$string);
		}
	}
	return $string;
}

function basic_error($pagetitle, $heading, $body)
{
	// Barebones error message, with no header
	global $output, $home_url;
	$output->title = $pagetitle;
	$output->subtitle = '';
	$output->use_header = 0;
	$output->addl("<div id=\"error\">",1);
	$output->addl("<h2 class=\"error\">$heading</h2>",2);
	$output->addl($body,2);
	$output->addl("<p><b><a href=\"$home_url/\">Senseless Political Ramblings home page</a></b></p>",2);
	$output->display();
}

function standard_error($pagesubtitle, $body)
{
	// More luxurious-looking error message, complete with header
	global $output;
	$output->subtitle = $pagesubtitle;
	$output->addl("<table style=\"width: 60%; margin: auto;\" cellspacing=\"0\">",2);
	$output->addl("<tr>",3);
	$output->addl("<th class=\"leftheading\">Error</td>",4);
	$output->addl("</tr>",3);
	$output->addl("<tr>",3);
	$output->addl("<td>$body</td>",4);
	$output->addl("</tr>",3);
	$output->addl("</table>",2);
	$output->display();
}

function error_notfound($url)
{
	global $tech_name, $tech_email, $images_url;
	header("HTTP/1.0 404 Not Found");
	header("HTTP/1.1 404 Not Found");
	basic_error("404 File not Found","Error 404: File not Found!","<p>It seems that the file you are looking for, <b>" . htmlspecialchars($url) . "</b>, does not exist.</p><p>Some possibilities as to why this error may have occured:</p>\n\t\t<ul><li>It may have been moved or deleted.</li><li>You may have typed the address incorrectly.</li><li>It may be some horrible error on our part.</li></ul>\n\t\t<p>If you got this error from a page on our site, please drop <a href=\"mailto:$tech_email\">$tech_name</a> a line with the address of this page, and the address of the page it was linked from. Thank you for your patience! People like you make us glad we exist.</p><p style=\"text-align: center\"><img src=\"$images_url/error-notfound.jpg\" alt=\"Not Found\" title=\"HULLO\" /></p>");
	exit;
}

function error_forbidden($url)
{
	global $tech_name, $tech_email, $images_url;
	header("HTTP/1.0 403 Forbidden");
	header("HTTP/1.1 403 Forbidden");
	basic_error("403 Forbidden","Error 403: Forbidden!","<p>It would seem that you're not permitted to access <b>" . htmlspecialchars($url) . "</b>. There are a few reasons as to why this may have happened:</p>\n\t\t<ul><li>We did something stupid and screwed something up.</li><li>You were snooping in a page you should not have been snooping in.</li></ul>\n\t\t<p>If you believe this to be a terrible mistake, send an email to <a href=\"mailto:$tech_email\">$tech_name</a> with the address of this page, and the address of the page that this page was linked from.</p><p style=\"text-align: center\"><img src=\"$images_url/error-forbidden.jpg\" alt=\"Forbidden\" title=\"lol, nazis\" /></p>");
	exit;
}

?>