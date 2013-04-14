<?php

$starttime = microtime();

require('../include/include_manager.php');

if (!isset($_GET['ipaddress']) || trim($_GET['ipaddress']) == '')
{
	$output->subtitle = 'Visitor Stats'; 
	
	$output->addl("<h2>Site Visitor Stats</h2>",1);
	
	$totalvisitors = $db->query_first("SELECT count(*) FROM uniquevisitor");
	$totalvisitors = $totalvisitors['count(*)'];
	
	$recentvisitors = $db->query_first("SELECT count(*) FROM uniquevisitor WHERE lastrequest > " . (gmdate("U")-86400));
	$recentvisitors = $recentvisitors['count(*)'];
	
	$lastweekvisitors = $db->query_first("SELECT count(*) FROM uniquevisitor WHERE lastrequest > " . (gmdate("U")-604800));
	$lastweekvisitors = $lastweekvisitors['count(*)'];
	
	$lastmonthvisitors = $db->query_first("SELECT count(*) FROM uniquevisitor WHERE lastrequest > " . (gmdate("U")-2592000));
	$lastmonthvisitors = $lastmonthvisitors['count(*)'];
	
	$newvisitors = $db->query_first("SELECT count(*) FROM uniquevisitor WHERE firstrequest > " . (gmdate("U")-864000));
	$newvisitors = $newvisitors['count(*)'];
	
	$totalloggedhits = $db->query_first("SELECT sum(hits) FROM uniquevisitor");
	$totalloggedhits = $totalloggedhits['sum(hits)'];
	
	$output->addl("<p>Visitors in the last 24 hours: <b>$recentvisitors</b><br />Visitors in the last week: <b>$lastweekvisitors</b><br />Visitors in the last 30 days: <b>$lastmonthvisitors</b></p>",1);
	$output->addl("<p>New visitors in the last 10 days: <b>$newvisitors</b></p>",1);
	$output->addl("<p style=\"background-color: transparent; color: green;\">Total unique visitors: <b>$totalvisitors</b><br />Total logged hits: <b>$totalloggedhits</b></p>",1);
	
	$output->addl("<p><b>Recent Active Visitors:</b>",1);
	$db->query("SELECT ipaddress, useragent, lastrequest, lasturl FROM uniquevisitor ORDER BY lastrequest DESC LIMIT 5");
	
	while ($recentvisitor = $db->fetch_array())
	{
		$output->addl("<br />[" . date("M d H:i:s",$recentvisitor['lastrequest']+(3600*$options['gmt_offset'])) . "] <a href=\"uniquevisitor.php?ipaddress=" . $recentvisitor['ipaddress'] . "\">" . $recentvisitor['ipaddress'] . "</a> (<i>" . gethostbyaddr($recentvisitor['ipaddress']) . "</i>) accessing <i>" . htmlspecialchars($recentvisitor['lasturl']) . "</i>",1);
	}
	
	$output->addl("<p><b>Recent New Visitors:</b>",1);
	$db->query("SELECT ipaddress, useragent, firstrequest FROM uniquevisitor ORDER BY firstrequest DESC LIMIT 5");
	
	while ($newvisitor = $db->fetch_array())
	{
		$output->addl("<br />[" . date("M d H:i:s",$newvisitor['firstrequest']+(3600*$options['gmt_offset'])) . "] <a href=\"uniquevisitor.php?ipaddress=" . $newvisitor['ipaddress'] . "\">" . $newvisitor['ipaddress'] . "</a> (<i>" . gethostbyaddr($newvisitor['ipaddress']) . "</i>)",1);
	}
	$output->addl("</p>");
	
	$output->display();
}
else
{
	$ipaddress = $db->query_first("SELECT * FROM uniquevisitor WHERE ipaddress = '" . addslashes($_GET['ipaddress']) . "' LIMIT 1");
	if (is_array($ipaddress))
	{
		$output->subtitle = 'Visitor Information for ' . htmlspecialchars($_GET['ipaddress']);
		$output->addl('<h2>Visitor Information for ' . htmlspecialchars($_GET['ipaddress']) . '</h2>',1);
		$output->addl("<p><b>Hostname:</b> <i>" . gethostbyaddr($_GET['ipaddress']) . "</i></p>",1);
		$output->addl("<p>",1);
		$output->addl("<b>Total hits:</b> " . number_format($ipaddress['hits']) . "<br />",2);
		$output->addl("<b>Last page accessed:</b> <i>" . htmlspecialchars($ipaddress['lasturl']) . "</i> on " . date("M d H:i:s",$ipaddress['lastrequest']+(3600*$options['gmt_offset'])) . "<br />",2);
		$output->addl("<b>First page request:</b> " . date("M d H:i:s",$ipaddress['firstrequest']+(3600*$options['gmt_offset'])) . "<br />",2);
		$output->addl("<b>User Agent:</b> " . htmlspecialchars($ipaddress['useragent']),2);
		$output->addl("</p>",1);
		$output->addl("<p><a href=\"uniquevisitor.php\">Back to the stats page</a></p>",1);
		$output->display();
	}
	else
	{
		$output->addl("<p>Oh shit! It seems the IP address you specified does not exist. Please go back to the stats page and try again.</p>",1);
		$output->addl("<p><a href=\"uniquevisitor.php\">Back to the stats page</a></p>",1);
		$output->display();
	}
}

?>