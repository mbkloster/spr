<?php

/*
	Senseless Political Ramblings: Manager Include File
	
	Used in ALL manager scripts. The contents of this file should be
	relatively "lean" - keep things like options and functions
	in their respective scripts - except for the login function, which
	has no use outside of the manager.
*/

if ($_SERVER['HTTP_HOST'] == "www.senselesspoliticalramblings.com")
{
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://senselesspoliticalramblings.com" . $_SERVER["REQUEST_URI"]);
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

$db->app_name = 'Senseless Manager';
$db->app_short_name = 'SPR Manager';

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

$output->title = 'Senseless Manager';

$output->css_files[] = "$local_url/" . $options['manager_css_file'];

$output->add_link_tag('icon','image/x-icon','','/favicon.ico');
$output->add_link_tag('shortcut icon','image/x-icon','','/favicon.ico');

// Output display:
// $output->display($starttime,array_sum($db->time),count($db->sql));

function adminlogin($destination)
{
	global $output, $starttime, $db, $local_url;
	$output->subtitle = 'Login';
	$output->cleardata();
	$output->use_header = 0;
	$output->use_footer = 0;
	$output->js_files[] = "$local_url/md5.js";
	$output->addl("<form action=\"login.php\" method=\"post\" onsubmit=\"md5hash(this.password,this.hashedpassword); this.submit();\">",1);
	$output->addl("<input type=\"hidden\" name=\"action\" value=\"login\" />",1);
	$output->addl("<input type=\"hidden\" name=\"destination\" value=\"" . htmlspecialchars($destination) . "\" />",1);
	$output->addl("<input type=\"hidden\" name=\"hashedpassword\" value=\"\" />",1);
	$output->addl("<table style=\"width: 35%;\" align=\"center\" cellspacing=\"0\">",1);
	$output->addl("<tr>",2);
	$output->addl("<th colspan=\"2\">Manager Login</th>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td width=\"50%\"><b>UserName:</b></td>",3);
	$output->addl("<td width=\"50%\"><input type=\"text\" name=\"username\" size=\"20\" maxlength=\"50\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td width=\"50%\"><b>Password:</b></td>",3);
	$output->addl("<td width=\"50%\"><input type=\"password\" name=\"password\" size=\"20\" maxlength=\"100\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("<tr>",2);
	$output->addl("<td colspan=\"2\" style=\"text-align: center;\"><input type=\"submit\" class=\"button\" value=\"Log In\" /></td>",3);
	$output->addl("</tr>",2);
	$output->addl("</table>",1);
	$output->addl("<p><b>Note:</b> Logging in requires cookies. Before attempting to log in, make sure cookies are enabled!</p>",1);
	$output->addl("</form>",1);
	$output->display($starttime,array_sum($db->time),count($db->sql));
}

function entryparse_u2p($text)
{
	// Parse unparsed text to entry-ready text
	// This function is so convoluted I don't really understand it. Good luck messing with it.
	$text = explode("\n",trim(str_replace("\r","",$text)));
	$finaltext = '';
	// $specialtags sub-keys: closetag, allowbrs
	$specialtags = array();
	// 0 = No paragraph, 1 = Open paragraph, -1 = Just finished with special tag
	$paragraph = 0;
	
	// $tagslist sub-keys: tag (DO NOT INCLUDE <>'s), allowbrs
	$tagslist = array(
	array('tag'=>'p','allowbrs'=>1),
	array('tag'=>'div','allowbrs'=>1),
	array('tag'=>'ol','allowbrs'=>0),
	array('tag'=>'ul','allowbrs'=>0),
	array('tag'=>'li','allowbrs'=>1),
	array('tag'=>'textarea','allowbrs'=>0),
	array('tag'=>'form','allowbrs'=>1),
	array('tag'=>'blockquote','allowbrs'=>1),
	array('tag'=>'table','allowbrs'=>0),
	array('tag'=>'tr','allowbrs'=>0),
	array('tag'=>'td','allowbrs'=>1),
	array('tag'=>'th','allowbrs'=>1),
	array('tag'=>'dl','allowbrs'=>0),
	array('tag'=>'dt','allowbrs'=>1),
	array('tag'=>'dd','allowbrs'=>1),
	array('tag'=>'h1','allowbrs'=>1),
	array('tag'=>'h2','allowbrs'=>1),
	array('tag'=>'h3','allowbrs'=>1),
	array('tag'=>'h4','allowbrs'=>1),
	array('tag'=>'h5','allowbrs'=>1),
	);
	
	for ($i = 0; $i < count($text); $i++)
	{
		if (trim($text[$i]) != '')
		{
			for ($c = 0; $c < strlen($text[$i]);) // Do not auto-increment this. Should be done manually for tech reasons
			{
				$notags = 1; // Indicates that no special tags were detected for this character
				for ($x = 0; $x < count($tagslist); $x++)
				{
					
					if (substr($text[$i],$c,(strlen($tagslist[$x]['tag'])+2)) == "<" . $tagslist[$x]['tag'] . ">" || substr($text[$i],$c,(strlen($tagslist[$x]['tag'])+2)) == "<" . $tagslist[$x]['tag'] . " ")
					{
						if ($paragraph > 0)
						{
							$finaltext .= "</p>";
							$paragraph = 0;
						}
						$finaltext .= "\n" . substr($text[$i],$c,(strlen($tagslist[$x]['tag'])+2));
						$key = count($specialtags);
						$specialtags[$key]['closetag'] = "</" . $tagslist[$x]['tag'] . ">";
						$specialtags[$key]['allowbrs'] = $tagslist[$x]['allowbrs'];
						$c += strlen($tagslist[$x]['tag']) + 2;
						$notags = 0;
						break;
					}
				}
				if ($notags) // if no special tags were present, continue with this
				{
					for ($x = count($specialtags)-1; $x >= 0; $x--)
					{
						if (substr($text[$i],$c,strlen($specialtags[$x]['closetag'])) == $specialtags[$x]['closetag'])
						{
							$notags = 0;
							if (!$specialtags[$x]['allowbrs']) { $finaltext .= "\n"; }
							$finaltext .= $specialtags[$x]['closetag'];
							$c += strlen($specialtags[$x]['closetag']);
							for ($a = $x; $a < count($specialtags); $a++)
							{
								if ($a == (count($specialtags)-1))
								{
									unset($specialtags[$a]);
								}
								else
								{
									$specialtags[$a] = $specialtags[$a + 1];
								}
							}
							if (count($specialtags) < 1)
							{
								$paragraph = -1;
							}
							break;
						}
					}
					if ($notags) // Once again, check for no special *closing* tags
					{
						if ($paragraph < 1 && (count($specialtags) < 1 || $specialtags[count($specialtags)-1]['allowbrs'] == -1))
						{
							$finaltext .= "\n<p>";
							$paragraph = 1;
						}
						elseif ( ($paragraph > 0 || (count($specialtags) > 0 && $specialtags[count($specialtags)-1]['allowbrs'])) && $c == 0)
						{
							$finaltext .= "<br />\n";
						}
						$nextchar = substr($text[$i],$c,1);
						if ($nextchar == '&' && substr($text[$i],$c,5) != '&amp;' && substr($text[$i],$c,4) != '&lt;' && substr($text[$i],$c,4) != '&gt;' && substr($text[$i],$c,6) != '&quot;' && substr($text[$i],$c,2) != '&#')
						{
							// contains an ampersand which is not parsed out... add in the HTML code for it
							$finaltext .= '&amp;';
						}
						else
						{
							$finaltext .= $nextchar;
						}
						$c++;
					}
				}
			}
		}
		else
		{
			if ($paragraph > 0)
			{
				$finaltext .= "</p>";
				$paragraph = 0;
			}
			elseif ($paragraph < 1 && count($specialtags) > 0)
			{
				if ($specialtags[count($specialtags)-1]['allowbrs'])
				{
					$finaltext .= "\n<br />";
				}
				else
				{
					$finaltext .= "\n";
				}
			}
			elseif ($paragraph < 0)
			{
				$paragraph = 0;
			}
			else
			{
				$finaltext .= "\n<br />";
			}
		}
	}
	if ($paragraph > 0)
	{
		$finaltext .= "</p>";
	}
	return ltrim($finaltext);
}

function entryparse_p2u($text)
{
	// Take parsed text and unparse it, making it editable again.
	$finaltext = '';
	// $tagslist sub-keys: tag (DO NOT INCLUDE <>'s), allowbrs
	$tagslist = array(
	array('tag'=>'div','allowbrs'=>1),
	array('tag'=>'ol','allowbrs'=>0),
	array('tag'=>'ul','allowbrs'=>0),
	array('tag'=>'li','allowbrs'=>1),
	array('tag'=>'textarea','allowbrs'=>0),
	array('tag'=>'form','allowbrs'=>1),
	array('tag'=>'blockquote','allowbrs'=>1),
	array('tag'=>'table','allowbrs'=>0),
	array('tag'=>'tr','allowbrs'=>0),
	array('tag'=>'td','allowbrs'=>1),
	array('tag'=>'th','allowbrs'=>1),
	array('tag'=>'dl','allowbrs'=>0),
	array('tag'=>'dt','allowbrs'=>1),
	array('tag'=>'dd','allowbrs'=>1),
	array('tag'=>'h1','allowbrs'=>1),
	array('tag'=>'h2','allowbrs'=>1),
	array('tag'=>'h3','allowbrs'=>1),
	array('tag'=>'h4','allowbrs'=>1),
	array('tag'=>'h5','allowbrs'=>1)
	);
	
	$specialtags = array();
	for ($i = 0; $i < strlen($text);)
	{
		$notags = 1;
		for ($x = 0; $x < count($tagslist); $x++)
		{
			if (substr($text,$i,strlen($tagslist[$x]['tag'])+2) == "<" . $tagslist[$x]['tag'] . ">" || substr($text,$i,strlen($tagslist[$x]['tag'])+2) == "<" . $tagslist[$x]['tag'] . " ")
			{
				$key = count($specialtags);
				$specialtags[$key]['closetag'] = "</" . $tagslist[$x]['tag'] . ">";
				$specialtags[$key]['allowbrs'] = $tagslist[$x]['allowbrs'];
				$finaltext .= substr($text,$i,strlen($tagslist[$x]['tag'])+2);
//				if (!$tagslist[$x]['allowbrs']) { $finaltext .= "\n"; }
				$notags = 0;
				$i += strlen($tagslist[$x]['tag'])+2;
				break;
			}
		}
		if ($notags)
		{
			for ($x = count($specialtags)-1; $x >= 0; $x--)
			{
				if (substr($text,$i,strlen($specialtags[$x]['closetag'])) == $specialtags[$x]['closetag'])
				{
					$finaltext .= $specialtags[$x]['closetag'];
					$i += strlen($specialtags[$x]['closetag']);
					for ($a = $x; $a < count($specialtags); $a++)
					{
						if ($a == (count($specialtags)-1))
						{
							unset($specialtags[$a]);
						}
						else
						{
							$specialtags[$a] = $specialtags[$a + 1];
						}
					}
					if (count($specialtags) < 1)
					{
						$finaltext .= "\n\n";
					}
					/*elseif (count($specialtags) > 0 && !$specialtags[count($specialtags)-1]['allowbrs'])
					{
						$finaltext .= "\n";
					}*/
					$notags = 0;
					break;
				}
			}
			if ($notags)
			{
				if ((substr($text,$i,3) == "<p>" && count($specialtags) > 0) || substr($text,$i,3) == "<p ")
				{
					$specialparas += 1;
					$finaltext .= substr($text,$i,3);
					$i += 3;
				}
				elseif (substr($text,$i,3) == "<p>")
				{
					$i += 3;
				}
				elseif (substr($text,$i,6) == "<br />")
				{
					if ((count($specialtags) > 0 && $specialtags[count($specialtags)-1]['allowbrs']) || count($specialtags) < 1)
					{
						$finaltext .= "\n";
					}
					else
					{
						$finaltext .= "<br />\n";
					}
					$i += 6;
				}
				elseif (substr($text,$i,4) == "</p>")
				{
					if ($specialparas > 0)
					{
						$finaltext .= "</p>";
						if (count($specialtags) < 1 || (count($specialtags) > 0 && !$specialtags[count($specialtags)-1]['allowbrs']))
						{
							$finaltext .= "\n";
						}
						$specialparas--;
					}
					else
					{
						$finaltext .= "\n\n";
					}
					$i += 4;
				}
				else
				{
					if (substr($text,$i,1) == "\n" && count($specialtags) > 0 && !$specialtags[count($specialtags)-1]['allowbrs'])
					{
						$finaltext .= "\n";
					}
					if (substr($text,$i,1) != "\n" && substr($text,$i,1) != "\r")
					{
						$finaltext .= substr($text,$i,1);
					}
					$i++;
				}
			}
		}
	}
	return rtrim($finaltext);
		
}

if (!$skiplogin)
{
	if (isset($_COOKIE['userid']) && isset($_COOKIE['password']))
	{
		$spruser = $db->query_first("SELECT userid, username, email, registrationdate, lastlogindate, isadmin, authorname, uploadname FROM user WHERE userid = '" . $_COOKIE['userid'] . "' AND password = md5(CONCAT(salt,'" . $_COOKIE['password'] . "')) AND isactive = '1' LIMIT 1");
		if (!is_array($spruser))
		{
			setcookie("userid","",(time()+1),"/",".senselesspoliticalramblings.com");
			setcookie("password","",(time()+1),"/",".senselesspoliticalramblings.com");
			adminlogin($_SERVER['REQUEST_URI']);
			exit;
		}
		
	}
	else
	{
		adminlogin($_SERVER['REQUEST_URI']);
		exit;
	}
	reset($_GET);
	reset($_POST);
	$getvars = '';
	$postvars = '';
	while (list($key,$val) = each($_GET))
	{
		$getvars .= $key.'='.$val."\n";
	}
	while (list($key,$val) = each($_POST))
	{
		$postvars .= "$key (" . strlen($val) . "b)\n";
	}
	reset($_GET);
	reset($_POST);
	$getvars = substr($getvars,0,strlen($getvars)-1);
	$postvars = substr($postvars,0,strlen($postvars)-1);
	$db->query("INSERT INTO managerlog (date, ipaddress, userid, requesturi, getvars, postvars, isadmin)"
	.        "\nVALUES ('" . gmdate('U') . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $spruser['userid'] . "', '" . addslashes($_SERVER['REQUEST_URI']) . "', '" . addslashes($getvars) . "', '" . addslashes($postvars) . "', '" . $spruser['isadmin'] . "')");
}

$output->header_addl("<h1 style=\"text-align: center; margin-top: 1px;\">Senseless Political Ramblings Manager</h1>",1);
$output->header_addl("<p style=\"text-align: center;\"><a href=\"./\">Home</a> - <a href=\"./user.php?action=myprofile\">My Profile</a> - <a href=\"./entry.php?action=new\">Post Entry</a> - <a href=\"./upload.php\">Upload Crap</a> - <a href=\"./recentupdate.php\">Recent Updates</a> - <a href=\"user.php\">Users</a> - <a href=\"log.php\">Logs</a> - <a href=\"uniquevisitor.php\">Stats</a> - <a href=\"./login.php?action=logout\">Log Out</a></p>",1);
$output->footer_addl("<p><a href=\"./\">[Manager Home]</a></p>",1);

?>