<?php

/*
	Senseless Political Ramblings: Settings File
	
	Here you will find various settings which are core
	to SPR's functioning. This includes things like db
	connection info, etc.
	For more general options, see options.php
*/

/* ################################################## */

// Database type
$db_type = 'MySQL';

// Database server
// In most shared servers this should be 'localhost'
$db_server = 'localhost';

// Database login username
$db_username = '';
// Database login password
$db_password = '';

// Database name
$db_name = '';

// Use persistent connections?
// This may either improve or worsen performance.
// 0 = No, 1 = Yes
$use_pconnects = 0;

// Log database errors to file?
// 0 = No, 1 = Yes
$log_errors = 1;
// Log file to log errors to
$log_file = 'SPR_Errors.log';

/* ################################################## */

// Who can access logs of manager actions/uploads/logins?
// Specify userids
$can_manage_logs = array(1);

/* ################################################## */

// Technical email
// Users will be shown this email any time they encounter
// an error.
$tech_email = '';
// Technical name
// Name given to the person/group with the tech email above
// (eg: 'our technical staff')
$tech_name = '';

// Webmaster email
// Users will be shown this email in the footer
$webmaster_email = '';
// Webmaster name
$webmaster_name = '';

/* ################################################## */

// NOTE: For these URL directives, do NOT include the final slash!
// eg: /dir is correct, /dir/ is not.

// Base server URL
// Should include nothing but the server name.
$server_url = 'http://' . $_SERVER['HTTP_HOST'];

// Home URL
// Should include the URL of the site's home page, as a directory,
// not a file.
$home_url = '';

// Images directory URL
// Should include the URL to the image directory.
$images_url = '/images';
// Images raw dir
$images_dir = '/home/sense/public_html/images';

// Files directory URL
$files_url = '/files';
// Files raw dir
$files_dir = '/home/sense/public_html/files';

// Junk directory URL
$junk_url = '/junk';
// Junk raw dir
$junk_dir = '/home/sense/public_html/junk';

// Local includes URL
// Should include the URL to the directory with local includes (css, js, etc.)
$local_url = '/local';

// News URL
// Should include the URL to the directory with the news-related files. (entry.php, etc.)
$news_url = '/news';

// Manager URL
// Should include the URL to the manager.
$manager_url = '/manager';

// CAPTCHA question timeout (in hours)
$captcha_timeout_hours = 48;

// Enable debug mode?
$debug = 1;

?>
