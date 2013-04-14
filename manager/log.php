<?php

$starttime = microtime();

require('../include/include_manager.php');

if (array_search($spruser['userid'],$can_manage_logs) !== false)
{
}
else
{
	$output->addl("<p>Sorry, it seems like you are not on <i>the list</i>. That is, the list of people allowed to access the logs.</p>",1);
	$output->display();
}

?>