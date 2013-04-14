<?php

$starttime = microtime();

require("include/include.php");

// This file does little other than redirect people to their download
// ergo: no output shit is used unless it's an invalid download

if (isset($_GET['title']) and $_GET['title'] != "")
{
	$downloadid = $db->query_first("SELECT downloadid FROM downloadalias where title = '".addslashes($_GET['title'])."' LIMIT 1");
	if (is_array($downloadid))
	{
		$download = $db->query_first("SELECT domain, path FROM download WHERE downloadid = '".$downloadid['downloadid']."' LIMIT 1");
		$downloadid = $downloadid['downloadid'];
	}
	else
	{
		$download = $db->query_first("SELECT domain, path FROM download WHERE downloadid = '".addslashes($_GET['title'])."' LIMIT 1");
		$downloadid = addslashes($_GET['title']);
	}
	if (is_array($download)) // looks like it's a working download
	{
		$downloadpath = "";
		if ($download['domain'] == "")
		{
			$downloadpath = "http://".$_SERVER['HTTP_HOST'];
		}
		else
		{
			$downloadpath = $download['domain'];
		}
		if (substr($download['path'],0,1) != "/" && substr($downloadpath,-1) != "/") // no / at the beginning of path, add it
		{
			$downloadpath .= "/";
		}
		if (substr($download['path'],0,1) == "/" && substr($downloadpath,-1) == "/")
		{
			$downloadpath .= substr($download['path'],1);
		}
		else
		{
			$downloadpath .= $download['path'];
		}
		$db->query("UPDATE download SET downloads=downloads+1 WHERE downloadid = '$downloadid' LIMIT 1");
		header("Location: $downloadpath");
		exit();
	}
	else // NOT a working download. 404!!!
	{
		error_notfound($_SERVER['REQUEST_URI']);
	}
}
else // no download given - give 403
{
	error_forbidden($_SERVER['REQUEST_URI']);
}