<?php

$starttime = microtime();

require("../include/include.php");

$output->subtitle = 'Network Lookup Utility';

$output->inline_css .= "span.ipaddress { color: blue; }";
$output->inline_css .= "\nspan.hostname { color: green; }";
$output->inline_css .= "\nspan.error { color: red; }";

$output->addl("<h2 class=\"section\">Network Lookup Utility</h2>",2);
$output->addl("<p>Network issues getting you down? Want an easy way to check a particular host's IP address, or find out the host of a particular IP? This will help you. This will help you a lot.</p>",2);

$output->addl("<form action=\"lookup\" method=\"get\">",2);

$output->addl("<table cellspacing=\"0\" style=\"width: 45%; margin: auto;\">",2);
$output->addl("<tr>",3);
$output->addl("<td style=\"width: 50%\" valign=\"top\" class=\"extrapadding\">",4);
$output->addl("<input type=\"text\" name=\"q\" size=\"40\" maxlength=\"80\" value=\"" . htmlspecialchars($_GET['q']) . "\" />",5);
$output->addl("<input type=\"submit\" class=\"button\" value=\"Look Up\" />",5);
$output->addl("<br /><span class=\"small\">Don't worry about specifying if it's an IP or a hostname; This utility will figure that out automatically.</span>",5);
$output->addl("</td>",4);
$output->addl("</tr>",3);
$output->addl("</table>",2);

$output->addl("</form>",2);

if (!isset($_GET['q']) || trim($_GET['q']) == '')
{
	$q = $_SERVER['REMOTE_ADDR'];
}
else
{
	$q = trim($_GET['q']);
}

if (preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/",$q))
{
	$resolve = gethostbyaddr($q);
	if ($resolve == $q)
	{
		$output->addl("<p><span class=\"error\">We cannot resolve the IP address '<b>" . htmlspecialchars($q) . "</b>' at this time.</span></p>",2);
	}
	else
	{
		$output->addl("<p><span class=\"ipaddress\"><b>" . htmlspecialchars($q) . "</b></span> resolves to <span class=\"hostname\"><b>" . htmlspecialchars($resolve) . "</b></span></p>",2);
	}
}
else
{
	$resolve = gethostbyname($q);
	if ($resolve == $q)
	{
		$output->addl("<p><span class=\"error\">We cannot resolve the hostname '<b>" . htmlspecialchars($q) . "</b>' at this time.</span></p>",2);
	}
	else
	{
		$output->addl("<p><span class=\"hostname\"><b>" . htmlspecialchars($q) . "</b></span> resolves to <span class=\"ipaddress\"><b>" . htmlspecialchars($resolve) . "</b></span></p>",2);
	}
}

$output->addl("<p><b><a href=\"/tools/\">More tools...</a></b></p>",2);

$output->display();

?>