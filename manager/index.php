<?php

$starttime = microtime();

require("../include/include_manager.php");

$output->subtitle = 'Home';

$output->addl("<p>Well hello there <b>" . htmlspecialchars($spruser['username']) . "</b>. Or should I say... <b>" . htmlspecialchars($spruser['authorname']) . "</b>.</p>",1);

$output->addl("<h3>Manager Functions</h3>",1);

$output->addl("<p>",1);
$output->addl("<b>Entries:</b> <a href=\"entry.php?action=new\">Post</a> - <a href=\"entry.php?action=manage\">Manage</a><br />",2);
$output->addl("<b>Users:</b> <a href=\"user.php?action=myprofile\">My Profile</a> - <a href=\"user.php?action=add\">Add</a> - <a href=\"user.php?action=manage\">Manage</a><br />",2);
$output->addl("<b>Recent Updates:</b> <a href=\"recentupdate.php\">Manage</a><br />",2);
$output->addl("<b>Logs:</b> <a href=\"log.php?type=login\">Logins</a> - <a href=\"log.php?type=manager\">Manager Actions</a> - <a href=\"log.php?type=upload\">Uploads</a><br />",2);
$output->addl("<b>Session:</b> <a href=\"login.php?action=logout\">Log Out</a>",2);
$output->addl("</p>",1);

$output->display();

?>