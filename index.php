<?php

$starttime = microtime();

require("include/include.php");

$output->title = "Senseless Political Ramblings: You'll Be Sorry You Were Born, But Glad You Found This Website";

$output->addl("<div class=\"subtext\" style=\"text-align: center;\"><b>Current Time:</b> " . date("M d Y h:i A", gmdate("U")+($options['gmt_offset']*3600)) . " " . $options['timezone'],2);
if ($options['gmt_offset'] > -1)
{
	$output->add(" (GMT +" . $options['gmt_offset'] . ")</div>");
}
else
{
	$output->add(" (GMT " . $options['gmt_offset'] . ")</div>");
}

if (is_array($spruser))
{
	$db->query("SELECT entryid, date, author, email, title, body, comments, isvisible, isopen FROM entry ORDER BY priority DESC, date DESC, entryid DESC LIMIT " . $options['main_maxentries']);
}
else
{
	$db->query("SELECT entryid, date, author, email, title, body, comments, isvisible, isopen FROM entry WHERE isvisible > 0 ORDER BY priority DESC, date DESC, entryid DESC LIMIT " . $options['main_maxentries']);
}

$mmdd = "00-00";
$yy = date("Y",gmdate("U")+($options['gmt_offset']*3600));
$days = 0;

while ($entry = $db->fetch_array())
{
	if (date("Y",$entry['date']+($options['gmt_offset']*3600)) != $yy)
	{
		$days++;
		if ($days > $options['main_daysback'])
		{
			break;
		}
		$output->addl("<h1 class=\"date\">" . date("l, F jS, Y",$entry['date']+($options['gmt_offset']*3600)) . "</h1>",2);
	}
	elseif (date("m-d",$entry['date']+($options['gmt_offset']*3600)) != $mmdd)
	{
		$days++;
		if ($days > $options['main_daysback'])
		{
			break;
		}
		$output->addl("<h1 class=\"date\">" . date("l, F jS",$entry['date']+($options['gmt_offset']*3600)) . "</h1>",2);
	}
	$output->addl("<h2 class=\"title\">" . htmlspecialchars($entry['title']) . "</h2>",2);
	if ($entry['isvisible'])
	{
		$output->addl("<div class=\"subtext\">Posted <b>" . date("h:i A",$entry['date']+($options['gmt_offset']*3600)) . "</b> by <b><a href=\"mailto:" . htmlspecialchars($entry['email']) . "\">" . htmlspecialchars($entry['author']) . "</a></b></div>",2);
	}
	else
	{
		$output->addl("<div class=\"subtext\"><b>(INVISIBLE)</b> Posted <b>" . date("h:i A",$entry['date']+($options['gmt_offset']*3600)) . "</b> by <b><a href=\"mailto:" . htmlspecialchars($entry['email']) . "\">" . htmlspecialchars($entry['author']) . "</a></b></div>",2);
	}
	$tooltiptext = "Read the rest of this entry";
	$defaulttitle = "Read more...";
	$entry['body'] = preg_replace("/\<cut id=\"(.*)\" title=\"(.*)\"\>(.*)\<\/cut\>/s","<b><a href=\"/storyonly/" . $entry['entryid'] . "/#cut\\1\" title=\"$tooltiptext\">\\2</a></b>",$entry['body']);
	$entry['body'] = preg_replace("/\<cut title=\"(.*)\" id=\"(.*)\"\>(.*)\<\/cut\>/s","<b><a href=\"/storyonly/" . $entry['entryid'] . "/#cut\\2\" title=\"$tooltiptext\">\\1</a></b>",$entry['body']);
	$entry['body'] = preg_replace("/\<cut\>(.*)\<\/cut\>/s","<b><a href=\"/storyonly/" . $entry['entryid'] . "/#cut1\" title=\"$tooltiptext\">$defaulttitle</a></b>",$entry['body']);
	$entry['body'] = preg_replace("/\<cut title=\"(.*)\"\>(.*)\<\/cut\>/s","<b><a href=\"/storyonly/" . $entry['entryid'] . "/#cut1\" title=\"$tooltiptext\">\\1</a></b>",$entry['body']);
	$entry['body'] = preg_replace("/\<cut id=\"(.*)\"\>(.*)\<\/cut\>/s","<b><a href=\"/storyonly/" . $entry['entryid'] . "/#cut\\1\" title=\"$tooltiptext\">$defaulttitle</a></b>",$entry['body']);
	$output->addl(str_replace("\n","\n\t\t",$entry['body']),2);
	$output->addl("<div class=\"subtext\"><b><a href=\"/storyonly/" . $entry['entryid'] . "/\" title=\"Create a permanent link to this entry (with no comments)\">Permalink</a>",2);
	if ($entry['comments'] == 1)
	{
		$output->add(" - <a href=\"/entries/" . $entry['entryid'] . "/#comments\" title=\"View this entry's comments\">1 comment</a>");
	}
	elseif ($entry['comments'] > 1)
	{
		$output->add(" - <a href=\"/entries/" . $entry['entryid'] . "/#comments\" title=\"View this entry's comments\">" . $entry['comments'] . " comments</a>");
	}
	if ($entry['isopen'])
	{
		$output->add(" - <a href=\"/entries/" . $entry['entryid'] . "/#postcomment\" title=\"Post a comment on this entry\">Post Comment</a>");
	}
	$output->add("</b></div>");
	$yy = date("Y",$entry['date']+($options['gmt_offset']*3600));
	$mmdd = date("m-d",$entry['date']+($options['gmt_offset']*3600));
	$mmyy = date("n-Y",$entry['date']+($options['gmt_offset']*3600));
}
$output->addl("<p><b><a href=\"/entries/$mmyy/\">All Entries from This Month</a> - <a href=\"/entries/\">Complete Entry Archive</a></b></p>",2);

$output->display();

?>