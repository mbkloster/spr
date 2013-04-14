<?php

if ($_SERVER['HTTP_HOST'] == 'www.senselesspoliticalramblings.com')
{
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://senselesspoliticalramblings.com" . $_SERVER['REQUEST_URI']);
	exit;
}

header("Content-Type: text/xml");
echo "<?xml version=\"1.0\"?>\n";
?>
<rss version="2.0">
	<channel>
		<title>Senseless Political Ramblings</title>
		<link>http://senselesspoliticalramblings.com/</link>
		<description>Your daily dose of vomit and diatribes from people whom you should not be listening to.</description>
		<language>en-us</language>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<generator>Senseless Political Ramblings</generator>
		<copyright>Copyright 2006 Senseless Political Ramblings</copyright>
		<managingEditor>genotoxin@gmail.com</managingEditor>
		<webMaster>genotoxin@gmail.com</webMaster><?php

require_once("../include/settings.php");
require_once("../include/database_mysql.php");
require_once("../include/options.php");

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

if (strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,6)) != 'msnbot' && substr_count($_SERVER['HTTP_USER_AGENT'],'; Yahoo! Slurp;') < 1 && strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,6)) != 'google' && strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,9)) != 'surveybot' && strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,7)) != 'gigabot' && strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4)) != 'w3c_' && strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,11)) != 'baiduspider' && substr($_SERVER['REMOTE_ADDR'],0,7) != '66.249.' && substr($_SERVER['REMOTE_ADDR'],0,6) != '72.30.' && $_SERVER['REMOTE_ADDR'] != '207.46.98.65' && $_SERVER['REMOTE_ADDR'] != '128.30.52.13' && substr($_SERVER['REMOTE_ADDR'],0,5) != '72.4.' && $_SERVER['REMOTE_ADDR'] != '66.154.103.156' && substr($_SERVER['REMOTE_ADDR'],0,7) != '207.46.' && substr($_SERVER['REMOTE_ADDR'],0,5) != '74.6.' and substr($_SERVER['REMOTE_ADDR'],0,10) != '65.214.44.')
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
}
else
{
	$spruser = 0;
}

if (is_array($spruser))
{
	$db->query("SELECT entryid, lastchangedate, title, author, email, body, isvisible FROM entry WHERE priority >= 0 ORDER BY date DESC LIMIT " . $options['feed_entries']);
}
else
{
	$db->query("SELECT entryid, lastchangedate, title, author, email, body, isvisible FROM entry WHERE priority >= 0 AND isvisible = '1' ORDER BY date DESC LIMIT " . $options['feed_entries']);
}

$first = 1;

while ($entry = $db->fetch_array())
{
	if ($first)
	{
		echo "\n\t\t<pubDate>" . date("D, d M Y H:i:s",$entry['lastchangedate']) . " GMT</pubDate>";
		$first = 0;
	}
	echo "\n\t\t<item>";
	if ($entry['isvisible'])
	{
		echo "\n\t\t\t<title>" . htmlspecialchars($entry['title']) . "</title>";
	}
	else
	{
		echo "\n\t\t\t<title>(INV) " . htmlspecialchars($entry['title']) . "</title>";
	}
	echo "\n\t\t\t<link>http://senselesspoliticalramblings.com/entries/" . $entry['entryid'] . "/</link>";
	echo "\n\t\t\t<guid>http://senselesspoliticalramblings.com/entries/" . $entry['entryid'] . "/</guid>";
	echo "\n\t\t\t<author>" . htmlspecialchars($entry['author']) . " &lt;" . htmlspecialchars($entry['email']) . "&gt;</author>";
	echo "\n\t\t\t<description>" . $entry['body'] . "</description>";
	echo "\n\t\t\t<pubDate>" . date("D, d M Y H:i:s",$entry['lastchangedate']) . " GMT</pubDate>";
	echo "\n\t\t</item>";
}
echo "\n";
?>
	</channel>
</rss>