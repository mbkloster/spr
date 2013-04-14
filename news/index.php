<?php

$starttime = microtime();

require_once("../include/include.php");

$months = array('Nice Try','January','February','March','April','May','June','July','August','September','October','November','December');
$daysinmonth = array(0,31,28,31,30,31,30,31,31,30,31,30,31);

if (isset($_GET['mm']) && isset($_GET['dd']) && isset($_GET['yyyy']))
{
	$begintime = mktime(00,00,00,$_GET['mm'],$_GET['dd'],$_GET['yyyy'])+($options['gmt_offset']*-3600);
	$endtime = mktime(23,59,59,$_GET['mm'],$_GET['dd'],$_GET['yyyy'])+($options['gmt_offset']*-3600);
	if (is_array($spruser))
	{
		$db->query("SELECT entryid, date, title, author, email, comments FROM entry WHERE date > $begintime AND date < $endtime ORDER BY priority DESC, date DESC");
	}
	else
	{
		$db->query("SELECT entryid, date, title, author, email, comments FROM entry WHERE date > $begintime AND date < $endtime AND isvisible = '1' ORDER BY priority DESC, date DESC");
	}
	if ($db->num_rows())
	{
		$output->subtitle = 'Archive for ' . $months[$_GET['mm']] . ' ' . htmlspecialchars($_GET['dd']) . ' ' . htmlspecialchars($_GET['yyyy']);
		$output->addl("<h2 class=\"section\">Entries from " . $months[$_GET['mm']] . ' ' . htmlspecialchars($_GET['dd']) . ', ' . htmlspecialchars($_GET['yyyy']) . "</h2>",2);
		$output->addl("<ul>",2);
		while ($entry = $db->fetch_array())
		{
			$output->addl("<li><a href=\"/entries/" . $entry['entryid'] . "/\" title=\"" . $entry['comments'] . " comment(s)\">" . htmlspecialchars($entry['title']) . "</a> <span class=\"small\">(posted <b>" . date("h:i A",$entry['date']+($options['gmt_offset']*3600)) . "</b> by <a href=\"mailto:" . htmlspecialchars($entry['email']) . "\">" . htmlspecialchars($entry['author']) . "</a>)</span></li>",3);
		}
		$output->addl("</ul>",2);
		$output->addl("<p><b><a href=\"/entries/" . urlencode($_GET['mm']) . "-" . urlencode($_GET['yyyy']) . "/\">Archive for " . $months[$_GET['mm']] .' '. htmlspecialchars($_GET['yyyy']) . "</a> - <a href=\"/entries/\">Complete Archive</a> - <a href=\"$home_url/\">Senseless Political Ramblings home</a></b></p>",2);
		$output->display();
	}
	else
	{
		error_notfound($_SERVER['REQUEST_URI']);
	}
}
elseif (isset($_GET['mm']) && isset($_GET['yyyy']))
{
	$begintime = mktime(00,00,00,$_GET['mm'],1,$_GET['yyyy'])+($options['gmt_offset']*-3600);
	$endtime = mktime(23,59,59,$_GET['mm'],$daysinmonth[$_GET['mm']],$_GET['yyyy'])+($options['gmt_offset']*-3600);
	if (is_array($spruser))
	{
		$db->query("SELECT entryid, date, title, author, email, comments FROM entry WHERE date > $begintime AND date < $endtime ORDER BY priority DESC, date DESC");
	}
	else
	{
		$db->query("SELECT entryid, date, title, author, email, comments FROM entry WHERE date > $begintime AND date < $endtime AND isvisible = '1' ORDER BY priority DESC, date DESC");
	}
	if ($db->num_rows())
	{
		$output->subtitle = 'Archive for ' . $months[$_GET['mm']] . ' ' . htmlspecialchars($_GET['yyyy']);
		$output->addl("<h2 class=\"section\">Entries from " . $months[$_GET['mm']] . ' ' . htmlspecialchars($_GET['yyyy']) . "</h2>",2);
		$dayentrycount = array(); // keeps track of the number of entries listed for each date
		$lastdate = '00-00-0000';
		while ($entry = $db->fetch_array())
		{
			if (date("n-j-Y",$entry['date']+($options['gmt_offset']*3600)) != $lastdate)
			{
				if (count($dayentrycount)) // Entries listed, so close an existing list
				{
					$output->addl("</ul>",2);
				}
				$output->addl("<h4><a href=\"/entries/" . date("n-j-Y",$entry['date']+($options['gmt_offset']*3600)) . "/\" class=\"totalindistinct\" title=\"View all entries from this date\">" . date("l, F jS",$entry['date']+($options['gmt_offset']*3600)) . "</a></h4>",2);
				$output->addl("<ul>",2);
			}
			if ($dayentrycount[date("n-j-Y",$entry['date']+($options['gmt_offset']*3600))] == $options['archives_entriesperday'])
			{
				$output->addl("<li><i><a href=\"/entries/" . date("n-j-Y",$entry['date']+($options['gmt_offset']*3600)) . "/\">View all entries from " . date("F jS",$entry['date']+($options['gmt_offset']*3600)) . "...</a></i></li>",3);
			}
			elseif ($dayentrycount[date("n-j-Y",$entry['date']+($options['gmt_offset']*3600))] < $options['archives_entriesperday'])
			{
				$output->addl("<li><a href=\"/entries/" . $entry['entryid'] . "/\" title=\"" . $entry['comments'] . " comment(s)\">" . htmlspecialchars($entry['title']) . "</a> <span class=\"small\">(posted <b>" . date("h:i A",$entry['date']+($options['gmt_offset']*3600)) . "</b> by <a href=\"mailto:" . htmlspecialchars($entry['email']) . "\">" . htmlspecialchars($entry['author']) . "</a>)</span></li>",3);
				if (!isset($dayentrycount[date("n-j-Y",$entry['date']+($options['gmt_offset']*3600))]))
				{
					$dayentrycount[date("n-j-Y",$entry['date']+($options['gmt_offset']*3600))] = 1;
				}
				else
				{
					$dayentrycount[date("n-j-Y",$entry['date']+($options['gmt_offset']*3600))]++;
				}
			}
			$lastdate = date("n-j-Y",$entry['date']+($options['gmt_offset']*3600));
		}
		if (count($dayentrycount))
		{
			$output->addl("</ul>",2);
		}
		$output->addl("<p><b><a href=\"/entries/\">Complete Archive</a> - <a href=\"$home_url/\">Senseless Political Ramblings home</a></b></p>",2);
		$output->display();
	}
	else
	{
		error_notfound($_SERVER['REQUEST_URI']);
	}
}
else
{
	$output->subtitle = 'Entry Archives';
	$output->addl("<h2 class=\"section\">Entry Archives</h2>",2);
	if (is_array($spruser))
	{
		$db->query("SELECT date FROM entry WHERE priority >= 0 ORDER BY date DESC");
	}
	else
	{
		$db->query("SELECT date FROM entry WHERE priority >= 0 AND isvisible = '1' ORDER BY date DESC");
	}
	$lastyear = 0000;
	$lastdate = '0000-00';
	$first = 1;
	while ($entry = $db->fetch_array())
	{
		if (date("Y",$entry['date']+($options['gmt_offset']*3600)) != $lastyear)
		{
			if (!$first) // not the first thing listed, so close the existing list
			{
				$output->addl("</ul>",2);
			}
			$output->addl("<h3>" . date("Y",$entry['date']+($options['gmt_offset']*3600)) . "</h3>",2);
			$output->addl("<ul>",2);
		}
		if (date("Y-n",$entry['date']+($options['gmt_offset']*3600)) != $lastdate)
		{
			$output->addl("<li><a href=\"/entries/" . date("n-Y",$entry['date']+($options['gmt_offset']*3600)) . "/\">" . date("F Y",$entry['date']+($options['gmt_offset']*3600)) . "</a></li>",3);
		}
		$lastyear = date("Y",$entry['date']+($options['gmt_offset']*3600));
		$lastdate = date("Y-n",$entry['date']+($options['gmt_offset']*3600));
		$first = 0;
	}
	$output->addl("</ul>",2);
	$output->addl("<p><b><a href=\"$home_url/\">Senseless Political Ramblings home</a></b></p>",2);
	$output->display();
}

?>