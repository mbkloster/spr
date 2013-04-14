<?php

/*
	Senseless Political Ramblings: MySQL Database Wrapper
	
	SPR's database class. The functions in this class are used
	in EVERY script. Don't mess with this unless you know what
	you are doing.
*/

class sql_db
{
	
	// Application name
	var $app_name = 'Application name';
	// Short application name
	var $app_short_name = 'Short application name';
	
	// Database server
	var $server = 'localhost';
	// Database username
	var $username = 'root';
	// Database password
	var $password = '';
	// Database name
	var $database = 'db';
	
	// Link ID
	// Stores the database connection link
	var $link_id = 0;
	// Database ID
	// Stores the database selection
	var $db_id = 0;
	// Query ID
	// Stores the data for the last query used
	var $query_id = 0;
	// SQL queries used
	// Array of all SQL queries used
	var $sql = array();
	
	// DB actions list
	var $actions = array();
	// DB action time list
	var $time = array();
	
	// Error number
	var $error_num = 0;
	// Error message
	var $error_msg = 'No error';
	
	// Debug mode enabled?
	// 0 = No debug mode enabled
	// 1 = Normal debug mode enabled
	// 2 = full query info display mode enabled
	var $debug_mode = 0;
	
	// Technical staff name
	// (eg: 'our technical staff')
	var $tech_name = 'our technical staff';
	// Technical email for error reports
	// Listed on every error page
	var $tech_email = 'email@website.com';
	// Log errors to file?
	var $log_errors = 1;
	// File to log errors to.
	var $log_file = 'dberrors.log';
	
	// Number of rows in resultset
	var $num_rows = -1;
	// Number of affected rows from last query
	var $affected_rows = -1;
	
	function start_action($action,$sql = "")
	{
		$this->actions[] = $action;
		if ($this->debug_mode > 1)
		{
			// Display queries is on. Show full info.
			if ($sql == "")
			{
				echo "<p><b>Database:</b> $action. Performing... ";
			}
			else
			{
				echo "<p><b>Database:</b> $action. SQL:<br />" . nl2br(htmlspecialchars($sql)) . "<br />Performing... ";
			}
		}
	}
	
	function end_action($starttime,$endtime)
	{
		$starttime = explode(" ",$starttime);
		$endtime = explode(" ",$endtime);
		$this->time[] = ( ($endtime[0]+$endtime[1]) - ($starttime[0]+$starttime[1]) );
		if ($this->debug_mode > 1)
		{
			// Display queries is on. Show full info.
			echo "done. Took " . (($endtime[0]+$endtime[1]) - ($starttime[0]+$starttime[1])) . " seconds.</p>";
		}
	}
	
	function get_error_num()
	{
		$this->error_num = @mysql_errno();
		return $this->error_num;
	}
	
	function get_error_msg()
	{
		$this->error_msg = @mysql_error();
		return $this->error_msg;
	}
	
	function error($action)
	{
		// Displays an error message and exits the script.
		// $action should be a verb in the base form.
		// (ie: "connect to the database")
		global $_SERVER, $local_url, $home_url, $options;
		$this->get_error_num();
		$this->get_error_msg();
		$lastquery = array_pop($this->sql);
		if ($this->log_errors)
		{
			$file = fopen($this->log_file,"a");
			if ($file)
			{
				$write = fwrite($file,"[" . gmdate("d M y H:i:s") . " GMT] $this->app_short_name\nIP:\t\t" . $_SERVER['REMOTE_ADDR'] . "\nURI:\t\t" . $_SERVER['REQUEST_URI'] . "\nError #$this->error_num:\t$this->error_msg\nLast SQL used:\n$lastquery\n\n");
				if (!$write)
				{
					$file_error = 1;
				}
			}
			else
			{
				$file_error = 1;
			}
		}
		if (!headers_sent())
		{
			echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">"
			.    "\n<html lang=\"en\" dir=\"ltr\">"
			.    "\n<head>"
			.    "\n\t<title>Database Error</title>"
			.    "\n\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />"
			.    "\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"$local_url/" . $options['css_file'] . "\" />"
			.    "\n</head>"
			.    "\n<body>"
			.    "\n\t<div id=\"error\">"
			.    "\n\t\t<h2 class=\"error\">Database Error!</h2>"
			.    "\n\t\t<p>Uh oh, it seems something went awry while attempting to $action.</p>"
			.    "\n\t\t<p>More than likely, this is the result of us performing routine maintenance, or we are adjusting something on the site that may cause a temporary error. Please reload this page and try again in a few moments.</p>"
			.    "\n\t\t<p>If this error persists, you should email <a href=\"mailto:$this->tech_email?subject=" . rawurlencode($this->app_short_name) . "%20database%20error\">$this->tech_name</a> with the page you get the error on, what page you got linked to it from, and what kind of error it is. (ie: connecting/querying the database)</p>"
			.    "\n\t\t<p>We apologize if this dreaded error causes you an inconvenience. Thank you for choosing $this->app_name!!!</p>";
			if ($file_error)
			{
				echo "\n\t\t<p>In addition, an error occured while trying to log this error to a file.</p>";
			}
			echo "\n\t\t<p><b><a href=\"$home_url\">$this->app_name home page</a></b></p>"
			.    "\n\t</div>"
			.    "\n</body>"
			.    "\n</html>";
			if ($this->debug_mode > 0)
			{
				echo "\n<!--"
				.    "\n== Database Error Details =="
				.    "\nAction: " . array_pop($this->actions)
				.    "\nError #: $this->error_num"
				.    "\nError: $this->error_msg"
				.    "\nLast SQL used:\n" . $lastquery
				.    "\n-->";
			}
		}
		else
		{
			if ($this->debug_mode == 0)
			{
				echo "<b>Database error!!!!!</b>";
			}
			elseif ($this->debug_mode == 1)
			{
				echo "<b>Database error! #$this->error_num: $this->error_msg</b>";
			}
			else
			{
				echo "failed. Encountered error #$this->error_num: $this->error_msg</p>";
			}
		}
		exit;
	}
	
	function connect($pconnect = 0)
	{
		// Connects to the database
		if (!$this->link_id) // Only connect if a link id is not already established
		{
			if ($pconnect)
			{
				$this->start_action("pconnection");
				$starttime = microtime();
				$this->link_id = @mysql_pconnect($this->server,$this->username,$this->password);
				$endtime = microtime();
			}
			else
			{
				$this->start_action("connection");
				$starttime = microtime();
				$this->link_id = @mysql_connect($this->server,$this->username,$this->password);
				$endtime = microtime();
			}
			
			if ($this->link_id) // Check if the connection was successful
			{
				$this->end_action($starttime,$endtime);
				$this->start_action("db selection");
				$starttime = microtime();
				$this->db_id = @mysql_select_db($this->database);
				$endtime = microtime();
				$this->end_action($starttime,$endtime);
				if ($this->db_id) // Check if the db selection was successful
				{
					return $this->link_id;
				}
				else
				{
					$this->error("connect to the database");
				}
			}
			else
			{
				$this->error("connect to the database");
			}
		}
	}
	
	function query($sql)
	{
		// Executes a query on the database server.
		if ($this->link_id && $this->db_id) // A link and db id must be established before performing queries
		{
			$this->sql[] = $sql; // Add in SQL query to SQL array
			$this->start_action("query",$sql);
			$starttime = microtime();
			$this->query_id = @mysql_query($sql);
			$endtime = microtime();
			if ($this->query_id) // Check if query was successful
			{
				$this->end_action($starttime,$endtime);
				return $this->query_id;
			}
			else
			{
				$this->error("perform a database query");
			}
		}
	}
	
	function num_rows($query = 'NONE')
	{
		// Get the number of rows in a resultset
		if ($query == 'NONE') // Query id is not otherwise set. Use current query id.
		{
			$query = $this->query_id;
		}
		$this->start_action("num rows");
		$starttime = microtime();
		$this->num_rows = @mysql_num_rows($query);
		$endtime = microtime();
		$this->end_action($starttime,$endtime);
		return $this->num_rows;
	}
	
	function affected_rows()
	{
		// Get the number of rows affected by the last query
		$starttime = microtime();
		$this->affected_rows = mysql_affected_rows($this->link_id);
		$endtime = microtime();
		$this->end_action($starttime,$endtime);
		return $this->affected_rows;
	}
	
	function result($row,$field,$query = 'NONE')
	{
		// Return a specific row/field from the resultset
		if ($query == 'NONE') // Query id not otherwise set. Use current query id.
		{
			$query = $this->query_id;
		}
		if ($query) { return @mysql_result($query,$row,$field); }
		else { return false; }
	}
	
	function fetch_array($query = 'NONE')
	{
		// Fetches a result row and puts it in array format.
		if ($query == 'NONE') // Query id not otherwise set. Use current query id.
		{
			$query = $this->query_id;
		}
		if ($query) { return @mysql_fetch_array($query,MYSQL_ASSOC); }
		else { return 0; }
	}
	
	function query_first($sql)
	{
		// Performs an SQL query and returns the first row from it.
		$this->query($sql);
		return $this->fetch_array($this->query_id);
	}
	
	function insert_id()
	{
		// Gets the ID of the last INSERT query.
		if ($this->link_id && $this->db_id)
		{
			return @mysql_insert_id($this->link_id);
		}
		else
		{
			return -1;
		}
	}
	
	function data_seek($row, $query = 'NONE')
	{
		if ($query == 'NONE') // Query id not otherwise set. Use current query id.
		{
			$query = $this->query_id;
		}
		if ($query)
		{
			$this->start_action("data seek");
			$starttime = microtime();
			$seek = @mysql_data_seek($query,$row);
			$endtime = microtime();
			if ($seek)
			{
				$this->end_action($starttime,$endtime);
				return 1;
			}
			else
			{
				$this->error("sort through database results");
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	
	function free_result($query = 'NONE')
	{
		if ($query == 'NONE') // Query id not otherwise set. Use current query id.
		{
			$query = $this->query_id;
		}
		if ($query)
		{
			$this->start_action("free result");
			$starttime = microtime();
			@mysql_free_result($query);
			$endtime = microtime();
			$this->end_action($starttime,$endtime);
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	function close()
	{
		if ($this->link_id)
		{
			$this->start_action("connection close");
			$starttime = microtime();
			@mysql_close($this->link_id);
			$endtime = microtime();
			$this->end_action($starttime,$endtime);
			return 1;
		}
		else
		{
			return 0;
		}
	}
}

?>