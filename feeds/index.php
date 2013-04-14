<?php

$starttime = microtime();

require("../include/include.php");

$output->subtitle = 'Subscriptions and News Feeds';

$output->addl("<h2 class=\"section\">Subscriptions &amp; News Feeds</h2>",2);

$output->addl("<p>Senseless Political Ramblings offers news feeds in RSS and Atom, for your own use, or to implement within your website.</p>",2);

$output->addl("<p><a href=\"/feeds/rss\" type=\"application/rss+xml\" class=\"indistinct\" title=\"Subscribe to this site's RSS news feed\"><img src=\"/images/icons/rss.gif\" alt=\"RSS\" /></a> <a href=\"/feeds/rss\" type=\"application/rss+xml\" title=\"Subscribe to this site's RSS news feed\"><b>Subscribe to this site's RSS news feed</b></a> - Gives you SPR's latest headlines via RSS.</p>",2);
$output->addl("<p><a href=\"/feeds/atom\" type=\"application/atom+xml\" class=\"indistinct\" title=\"Subscribe to this site's Atom news feed\"><img src=\"/images/icons/atom.gif\" alt=\"Atom\" /></a> <a href=\"/feeds/atom\" type=\"application/atom+xml\" title=\"Subscribe to this site's Atom news feed\"><b>Subscribe to this site's Atom news feed</b></a> - Gives you SPR's latest headlines via Atom, but I'm really not sure why we have this because we already support RSS. Oh wait, it's because a lot of you people love shitty \"alternative\" software formats, which usually consist of about 100 renamed tags and one additional unique one, making it completely frivolous to use. People who use Atom are worthless latte-sipping sheep.</p>",2);

$output->addl("<p><b><a href=\"$home_url/\">Senseless Political Ramblings home</a></b></p>",2);

$output->display();

?>