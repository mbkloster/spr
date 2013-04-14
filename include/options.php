<?php

/*
	Senseless Political Ramblings: Options File
	
	Here you will find general options as to the
	functioning and setup of SPR, such as layout and
	behavior.
	This file does NOT have database and other critical
	settings. For those, see settings.php.
*/

$options = array(

	// StyleSheet File
	// Applied to all pages on SPR unless otherwise
	// specified (ie: $output->css_file[] = 'blah.css';)
	'css_file' => 'default.css',
	
	// Manager StyleSheet File
	// Same as above, except for the manager CSS file
	'manager_css_file' => 'manager-default.css',
	
	// Theme
	// Name of the 'theme' in use - logos, etc. will use this in the URL
	'theme' => 'default',
	
	// Minimum password length
	'min_password_len' => 6,
	// Maximum password length
	'max_password_len' => 100,
	
	// Login Attempt Duration
	// Amount of time that login attempts are counted (in minutes)
	'login_attempt_duration' => 20,
	// Max Login Attempts
	// Max amount of login attempts during this duration
	'max_login_attempts' => 4,
	
	// GMT timezone offset
	'gmt_offset' => -8,
	// Timezone name
	'timezone' => 'Pacific Time',
	
	// Number of days back the main page will go in the news
	'main_daysback' => 10,
	// Max number of entries on the main page
	'main_maxentries' => 30,
	
	// Max author name length
	'maxl_author' => 20,
	// Max comment length
	'maxl_body' => 8500,
	
	// Amount of minutes that a comment can be deleted within
	'deletetime_max' => 6,
	// Deleted comment entryid
	// When comments are soft-deleted, they will be thrown in this entry
	'deleted_entryid' => 2,
	
	// Days the cookie with the info will be stored
	'cookiestore_days' => (365*20),
	
	'recentupdate_icons' => array('canadabot','site'),
	
	'recentupdate_count' => 6,
	
	'comment_delay' => 30,
	
	'archives_entriesperday' => 5,
	
	'feed_entries' => 12,
	
	'feed_desclength' => 200

);

$links_urls = array();
$links_nams = array();

// CAPTCHA questions and answers
// separate possible answers with spaces

$captcha_options = array(
array("What is two plus one?","three 3"),
array("What is the second word of this sentence?","is"),
array("Are you human? (yes or no)","yes yeah yea yah yup"),
array("What is 20 divided by five?","four 4"),
array("How do you spell 'ridiculous'?","ridiculous"),
array("What is the opposite of good?","bad evil notgood nogood"),
array("What does National Security Agency abbreviate to?","nsa"),
array("What is the first letter of the alphabet?","a"),
array("What is the last letter of the alphabet?","z"),
array("If you remove 'Ama' from 'Amazing', what do you get?","zing"),
array("What language is this very website in?","english"),
array("What letter comes before h in the alphabet?","g"),
array("What language do they speak in France?","french")
);

?>
