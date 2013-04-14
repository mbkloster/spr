<?php

/*
	Senseless Political Ramblings: Output Generation File
	
	This file contains the class/functions for handling output
	in SPR. The output is kept in a variable before being outputted
	to allow for greater flexibility with headers.
*/

class output_handler
{
	
	// Enable base header/footer HTML tags?
	// Should be enabled in almost all cases.
	var $use_base_html = 1;
	// Use header content?
	var $use_header = 1;
	// Use footer content?
	var $use_footer = 1;
	
	// Header content
	var $header = '';
	// Footer content
	var $footer = '';
	// Page body
	var $body = '';
	
	// HT Doctype
	var $doctype = 'html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"';
	
	// Page language
	var $lang = 'en';
	
	// Page reading direction
	var $dir = 'ltr';
	
	// Page title
	var $title = 'Website';
	// Page sub-title (optional)
	// If this is specified, it will appear after the
	// normal page title by default
	var $subtitle = '';
	// Reverse subtitle order?
	// If enabled, subtitle will come first, rather than last
	var $reverse_subtitle = 0;
	
	// Meta content-type
	var $content_type = 'text/html';
	// Meta charset
	var $charset = 'ISO-8859-1';
	
	// Inline StyleSheet (appears in head)
	var $inline_css = '';
	// Inline JavaScript (appears in head)
	var $inline_js = '';
	
	// List of CSS files
	var $css_files = array();
	// List of JS files
	var $js_files = array();
	// Extra meta tags
	// Sub-keys: 'http_equiv' and 'content'
	var $meta_tags = array();
	// Extra link tags
	// Sub-keys: 'rel' 'type' 'title' and 'href'
	var $link_rags = array();
	
	// Show output info?
	// If enabled, data on the output will be echoed rather than
	// the output itself.
	var $show_info = 0;
	
	function base_header()
	{
		// Returns a basic, raw, no frills header, with title/meta tags/doctype
		$header = "<!DOCTYPE $this->doctype>"
		.       "\n<html lang=\"$this->lang\" dir=\"$this->dir\">"
		.       "\n<head>";
		if (trim($this->subtitle) != '' && $this->reverse_subtitle)
		{
			$header .= "\n\t<title>$this->subtitle - $this->title</title>";
		}
		elseif (trim($this->subtitle) != '')
		{
			$header .= "\n\t<title>$this->title - $this->subtitle</title>";
		}
		else
		{
			$header .= "\n\t<title>$this->title</title>";
		}
		$header .= "\n\t<meta http-equiv=\"content-type\" content=\"$this->content_type; charset=$this->charset\" />";
		for ($i = 0; $i < count($this->meta_tags); $i++)
		{
			$header .= "\n\t<meta http-equiv=\"" . $this->meta_tags[$i]['http_equiv'] . "\" content=\"" . $this->meta_tags[$i]['content'] . "\" />";
		}
		for ($i = 0; $i < count($this->css_files); $i++)
		{
			$header .= "\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $this->css_files[$i] . "\" />";
		}
		for ($i = 0; $i < count($this->link_tags); $i++)
		{
			if ($this->link_tags[$i]['title'] == '')
			{
				$header .= "\n\t<link rel=\"" . $this->link_tags[$i]['rel'] . "\" type=\"" . $this->link_tags[$i]['type'] . "\" href=\"" . $this->link_tags[$i]['href'] . "\" />";
			}
			else
			{
				$header .= "\n\t<link rel=\"" . $this->link_tags[$i]['rel'] . "\" type=\"" . $this->link_tags[$i]['type'] . "\" title=\"" . $this->link_tags[$i]['title'] . "\" href=\"" . $this->link_tags[$i]['href'] . "\" />";
			}
		}
		if (trim($this->inline_css) != '')
		{
			$header .= "\n\t<style type=\"text/css\">"
			.          "\n\t<!--"
			.          "\n\t" . str_replace("\n","\n\t",$this->inline_css)
			.          "\n\t-->"
			.          "\n\t</style>";
		}
		for ($i = 0; $i < count($this->js_files); $i++)
		{
			$header .= "\n\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $this->js_files[$i] . "\"></script>";
		}
		if (trim($this->inline_js) != '')
		{
			$header .= "\n\t<script language=\"javascript\" type=\"text/javascript\">"
			.          "\n\t<!--"
			.          "\n\t" . str_replace("\n","\n\t",$this->inline_js)
			.          "\n\t-->"
			.          "\n\t</script>";
		}
		$header .= "\n</head>"
		.          "\n<body>";
		return $header;
	}
	
	function base_footer()
	{
		// Returns a basic, no frills footer
		$footer = "\n</body>"
		.         "\n</html>";
		return $footer;
	}
	
	function cleardata($header = 1, $footer = 1, $body = 1)
	{
		// Resets header/footer/body data to a blank state
		if ($header)
		{
			$this->header = '';
		}
		if ($footer)
		{
			$this->footer = '';
		}
		if ($body)
		{
			$this->body = '';
		}
	}
	
	function add_meta_tag($http_equiv,$content)
	{
		// Adds a meta tag to the meta tags list
		// Done through a function since the normal [] method doesn't work
		if ($this->show_info) { echo "<p><b>Output:</b> Adding '$http_equiv' meta tag.</p>"; }
		$key = count($this->meta_tags);
		$this->meta_tags[$key]['http_equiv'] = $http_equiv;
		$this->meta_tags[$key]['content'] = $content;
		return $key;
	}
	
	function add_link_tag($rel, $type, $title, $href)
	{
		if ($this->show_info) { echo "<p><b>Output:</b> Adding '$rel' link tag.</p>"; }
		$key = count($this->link_tags);
		$this->link_tags[$key]['rel'] = $rel;
		$this->link_tags[$key]['type'] = $type;
		$this->link_tags[$key]['title'] = $title;
		$this->link_tags[$key]['href'] = $href;
	}
	
	// add, addbr and addl functions:
	// add: Adds raw text to the header/footer/body
	// addbr: Adds a linebreak (no text) to the header/footer/body
	// addl: Adds a line of text (with a newline) to the header/footer/body
	
	function header_addl($content,$tabs = 0)
	{
		if ($this->show_info) { echo "<p><b>Output:</b> Adding line to header. (" . ($tabs+strlen($content)+1) . " bytes)</p>"; }
		$this->header .= "\n";
		for ($i = 0; $i < $tabs; $i++)
		{
			$this->header .= "\t";
		}
		$this->header .= $content;
	}
	
	function header_addbr($tabs = 0)
	{
		if ($this->show_info) { echo "<p><b>Output:</b> Adding newline to header. (" . ($tabs+1) . " bytes)</p>"; }
		$this->header .= "\n";
		for ($i = 0; $i < $tabs; $i++)
		{
			$this->header .= "\t";
		}
	}
	
	function header_add($content)
	{
		if ($this->show_info) { echo "<p><b>Output:</b> Adding data to header. (" . strlen($content) . " bytes)</p>"; }
		$this->header .= $content;
	}
	
	function footer_addl($content,$tabs = 0)
	{
		if ($this->show_info) { echo "<p><b>Output:</b> Adding line to footer. (" . ($tabs+strlen($content)+1) . " bytes)</p>"; }
		$this->footer .= "\n";
		for ($i = 0; $i < $tabs; $i++)
		{
			$this->footer .= "\t";
		}
		$this->footer .= $content;
	}
	
	function footer_addbr($tabs = 0)
	{
		if ($this->show_info) { echo "<p><b>Output:</b> Adding newline to footer. (" . ($tabs+1) . " bytes)</p>"; }
		$this->footer .= "\n";
		for ($i = 0; $i < $tabs; $i++)
		{
			$this->footer .= "\t";
		}
	}
	
	function footer_add($content)
	{
		if ($this->show_info) { echo "<p><b>Output:</b> Adding data to footer. (" . strlen($content) . " bytes)</p>"; }
		$this->footer .= $content;
	}
	
	function addl($content,$tabs = 0)
	{
		if ($this->show_info) { echo "<p><b>Output:</b> Adding line to body. (" . ($tabs+strlen($content)+1) . " bytes)</p>"; }
		$this->body .= "\n";
		for ($i = 0; $i < $tabs; $i++)
		{
			$this->body .= "\t";
		}
		$this->body .= $content;
	}
	
	function addbr($tabs = 0)
	{
		if ($this->show_info) { echo "<p><b>Output:</b> Adding newline to body. (" . ($tabs+1) . " bytes)</p>"; }
		$this->body .= "\n";
		for ($i = 0; $i < $tabs; $i++)
		{
			$this->body .= "\t";
		}
	}
	
	function add($content)
	{
		if ($this->show_info) { echo "<p><b>Output:</b> Adding data to body. (" . strlen($content) . " bytes)</p>"; }
		$this->body .= $content;
	}
	
	function display()
	{
		global $starttime, $db;
		$dbtime = array_sum($db->time);
		$dbqueries = count($db->sql);
		// Echo all the data to the browser.
		$output = '';
		if ($this->use_base_html)
		{
			$output .= $this->base_header();
		}
		if ($this->use_header && $this->header != '')
		{
			$output .= $this->header;
		}
		$output .= $this->body;
		if ($this->use_footer && $this->footer != '')
		{
			$output .= $this->footer;
		}
		if ($this->use_base_html)
		{
			$output .= $this->base_footer();
		}
		if (!$this->show_info)
		{
			echo $output;
		}
		else
		{
			$endtime = microtime();
			$starttime = explode(" ",$starttime);
			$endtime = explode(" ",$endtime);
			$time = round(($endtime[0]+$endtime[1])-($starttime[0]+$starttime[1]),3);
			if ($time <= 0)
			{
				$dbtime = 50;
				$phptime = 50;
			}
			else
			{
				$dbtime = round(($dbtime/$time)*100);
				$phptime = 100-$dbtime;
			}
			if (strlen($output) > 1024)
			{
				echo "<p><b>Done.</b> Output size: " . round((strlen($output)/1024),1) . " kb (used base html: $this->use_base_html - use header: $this->use_header - footer: $this->use_footer). Took $time seconds ($phptime% in PHP, $dbtime% in the database). Queries used: $dbqueries</p></body></html>";
			}
			else
			{
				echo "<p><b>Done.</b> Output size: " . strlen($output) . " bytes (used base html: $this->use_base_html - use header: $this->use_header - footer: $this->use_footer). Took $time seconds ($phptime% in PHP, $dbtime% in the database). Queries used: $dbqueries</p></body></html>";
			}
		}
	}
}

?>