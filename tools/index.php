<?php

$starttime = microtime();

require("../include/include.php");

$output->subtitle = 'Tools and Utilities';

$output->addl("<h2 class=\"section\">Tools &amp; Utilities</h2>",2);

$output->addl("<p>Here are some amazing utilities to save you time and possibly money. All of these were developed in my spare time, so they probably have an assortment of bugs in them. If one of such bugs is so annoying you can't stand having it there, <a href=\"mailto:$webmaster_email\">drop me a line</a> and tell me what it is. Otherwise, enjoy.</p>",2);

$output->addl("<p><b><a href=\"algebra\">The Algebra Equation-o-Matic</a></b> - Instantly check your horrendously complicated algebraic equations in one easy keystroke! Or two, if you could the need to enter variables as an extra keystroke. Either way, it saves you time.</p>",2);

$output->addl("<p><b><a href=\"lookup\">Network Lookup Utility</a></b> - Are you, for some reason, unable to figure out the IP address of a particular hostname? Do you want to find out some details about an IP address you have? You could probably find something on your computer to do that, but failing that, you can use this.</p>",2);

$output->addl("<p><b><a href=\"$home_url/\">Senseless Political Ramblings home</a></b></p>",2);

$output->display();

?>