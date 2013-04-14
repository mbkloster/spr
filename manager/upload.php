<?php

$starttime = microtime();

require("../include/include_manager.php");

$inserts_maxsize = 204800;
$inserts_maxx = 800;
$inserts_maxy = 800;
$inserts_formats = array('jpg','jpeg','gif','png','bmp');

$files_maxsize = 2621440;
$files_formats = array('zip','txt','doc','pdf','log','hlp','avi','wmv','ogm','mp3','wav','ogg','swf');

$junk_maxsize = 7864320;
$junk_maxx = 1600;
$junk_maxy = 1200;
$junk_formats = array_merge($files_formats,$inserts_formats);

$thumb_max = 200;
$thumb_ext = '-th';

// Duration that the upload limit applies for (in hours)
$limit_duration = 12;
// Number of megs users are limited to uploading
$limit_size = 8;

function generate_thumbnail($fileupload,$imgsize,$uploaddir)
{
	// generates a thumbnail from a standard image
	global $thumb_max,$thumb_ext,$spruser;
	if ($imgsize[0] > $thumb_max || $imgsize[1] > $thumb_max)
	{
		if ($imgsize[0] >= $imgsize[1])
		{
			$thumbx = $thumb_max;
			$thumby = round(($imgsize[1]/$imgsize[0])*$thumb_max);
		}
		else
		{
			$thumby = $thumb_max;
			$thumbx = round(($imgsize[0]/$imgsize[1])*$thumb_max);
		}
		$thumb = imagecreatetruecolor($thumbx, $thumby);
		if ($imgsize[2] == 1)
		{
			$srcimg = imagecreatefromgif($fileupload['tmp_name']);
		}
		elseif ($imgsize[2] == 2)
		{
			$srcimg = imagecreatefromjpeg($fileupload['tmp_name']);
		}
		elseif ($imgsize[2] == 3)
		{
			$srcimg = imagecreatefrompng($fileupload['tmp_name']);
		}
		elseif ($imgsize[2] == 6)
		{
			$srcimg = imagecreatefromwbmp($fileupload['tmp_name']);
		}
		if ($srcimg) // source image opening works
		{
			if ($imgsize[2] == 2)
			{
				imagecopyresampled($thumb, $srcimg, 0, 0, 0, 0, $thumbx, $thumby, $imgsize[0], $imgsize[1]);
				
			}
			else
			{
				imagecopyresized($thumb, $srcimg, 0, 0, 0, 0, $thumbx, $thumby, $imgsize[0], $imgsize[1]);
			}
			// Now, work on the filename
			$thumbfilename = explode('.',$fileupload['name']);
			$thumbfilename[count($thumbfilename)-2] .= $thumb_ext;
			$thumbfilename = implode('.',$thumbfilename);
			$finalthumbfilename = "$uploaddir/$thumbfilename";
			if ($imgsize[2] == 1)
			{
				$finalthumb = imagegif($thumb,$finalthumbfilename);
			}
			elseif ($imgsize[2] == 2)
			{
				$finalthumb = imagejpeg($thumb,$finalthumbfilename);
			}
			elseif ($imgsize[2] == 3)
			{
				$finalthumb = imagepng($thumb,$finalthumbfilename);
			}
			elseif ($imgsize[2] == 6)
			{
				$finalthumb = imagewbmp($thumb,$finalthumbfilename);
			}
			if ($finalthumb)
			{
				return array($finalthumb,$thumbfilename,$thumbfinalfilename,$thumbx,$thumby);
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	else
	{
		return 0;
	}
	
}

function file_within_limits($filesize)
{
	global $limit_duration, $limit_size, $db, $output, $spruser;
	$limitbytes = 1024*1024*$limit_size;
	$limitsecs = 3600*$limit_duration;
	$uploaded = $db->query_first("SELECT sum(filesize) AS filesize FROM uploadlog WHERE userid = '" . $spruser['userid'] . "' AND date > " . (gmdate("U")-$limitsecs));
	if ($filesize+$uploaded['sum(filesize)'] > $limitbytes)
	{
		$output->addl("<p>Uploading this file would exceed the space storage limit for the current duration. The limit is $limit_size MB in a $limit_duration-hour duration.</p>",1);
		$output->display();
		exit;
	}
	return ($filesize+$uploaded['filesize']);
}

function add_file($filetype)
{
	global $_FILES, $_SERVER, $spruser, $db;
	$db->query("INSERT INTO uploadlog (date, ipaddress, userid, username, uploadname, filename, filetype, filesize)"
	.        "\nVALUES ('" . gmdate("U") . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $spruser['userid'] . "', '" . addslashes($spruser['username']) . "', '" . addslashes($spruser['uploadname']) . "', '" . addslashes($_FILES['fileupload']['name']) . "', '$filetype', '" . $_FILES['fileupload']['size'] . "')");
}

function space_used_line()
{
	global $output, $spaceused, $limit_size, $limit_duration;
	$output->addl("<p>You are using <b>" . round($spaceused/(1024*1024),1) . " MB</b> of your total <b>$limit_size MB</b> for this $limit_duration-hour duration.</p>",1);
}

function thumbnail_lines($thumb, $whereat)
{
	global $output, $spruser, $_FILES;
	$output->addl("<p><b>Thumbnailed image code:</b><br />&lt;a href=\"http://senselesspoliticalramblings.com$whereat/" . rawurlencode($_FILES['fileupload']['name']) . "\"&gt;&lt;img src=\"http://senselesspoliticalramblings.com$whereat/" . rawurlencode($thumb[1]) . "\" alt=\"" . htmlspecialchars($_FILES['fileupload']['name']) . "\" width=\"$thumb[3]\" height=\"$thumb[4]\" /&gt;&lt;/a&gt;</p>",1);
	$output->addl("<p><b>Thumbnailed SPR-specific image code:</b><br />&lt;a href=\"$whereat/" . rawurlencode($_FILES['fileupload']['name']) . "\"&gt;&lt;img src=\"$whereat/" . rawurlencode($thumb[1]) . "\" alt=\"" . htmlspecialchars($_FILES['fileupload']['name']) . "\" width=\"$thumb[3]\" height=\"$thumb[4]\" /&gt;&lt;/a&gt;</p>",1);
	$output->addl("<p><b>Thumbnailed VB code:</b><br />[url=http://senselesspoliticalramblings.com$whereat/" . rawurlencode($_FILES['fileupload']['name']) . "][img]http://senselesspoliticalramblings.com$whereat/" . rawurlencode($thumb[1]) . "[/img][/url]</p>",1);
}

if (trim($action) == '')
{
	$action = "upload";
}

if ($action == 'upload')
{
	$output->subtitle = 'Upload File';
	$output->addl("<form action=\"./upload.php\" method=\"post\" enctype=\"multipart/form-data\">",1);
	$output->addl("<input type=\"hidden\" name=\"action\" value=\"upload2\" />",1);
	$output->addl("<input type=\"hidden\" name=\"uploadtype\" value=\"0\" />",1);
	$output->addl("<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$inserts_maxsize\" />",1);
	$output->addl("<h2>Upload Image Insert</h2>",1);
	$output->addl("<div>Please, only use image inserts for images <i>in SPR entries</i>. For general image hosting, use a junk upload.</div>",1);
	$output->addl("<input type=\"file\" name=\"fileupload\" size=\"50\" /><br />",1);
	$output->addl("<input type=\"checkbox\" name=\"makethumbnail\" value=\"1\" /> Make thumbnail?<br /><input type=\"submit\" class=\"button\" value=\"Upload File\"> <span class=\"small\"><b>Max Size:</b> " . ($inserts_maxsize/1024) . " kb. <b>Max Dimensions:</b> $inserts_maxx"."x"."$inserts_maxy. <b>Valid Formats:</b> " . implode(" ",$inserts_formats) . "</span>",1);
	$output->addl("</form>",1);
	$output->addl("<form action=\"./upload.php\" method=\"post\" enctype=\"multipart/form-data\">",1);
	$output->addl("<input type=\"hidden\" name=\"action\" value=\"upload2\" />",1);
	$output->addl("<input type=\"hidden\" name=\"uploadtype\" value=\"1\" />",1);
	$output->addl("<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$files_maxsize\" />",1);
	$output->addl("<h2>Upload File</h2>",1);
	$output->addl("<div>Please, only use this for files <i>used in SPR entries</i>. For general file hosting, use a junk upload.</div>",1);
	$output->addl("<input type=\"file\" name=\"fileupload\" size=\"50\" /><br />",1);
	$output->addl("<input type=\"submit\" class=\"button\" value=\"Upload File\"> <span class=\"small\"><b>Max Size:</b> " . ($files_maxsize/1024) . " kb. <b>Valid Formats:</b> " . implode(" ",$files_formats) . "</span>",1);
	$output->addl("</form>",1);
	$output->addl("<form action=\"./upload.php\" method=\"post\" enctype=\"multipart/form-data\">",1);
	$output->addl("<input type=\"hidden\" name=\"action\" value=\"upload2\" />",1);
	$output->addl("<input type=\"hidden\" name=\"uploadtype\" value=\"2\" />",1);
	$output->addl("<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$junk_maxsize\" />",1);
	$output->addl("<h2>Upload Junk File/Image</h2>",1);
	$output->addl("<div>All misc files that <i>do not belong in any SPR entry</i> should go in junk. Use this for files you want to link to remotely from other forums/sites.</div>",1);
	$output->addl("<input type=\"file\" name=\"fileupload\" size=\"50\" /><br />",1);
	if ($spruser['isadmin'])
	{
		$output->addl("<input type=\"checkbox\" name=\"makethumbnail\" value=\"1\" /> Make thumbnail? (if image) <input type=\"checkbox\" name=\"omituserdir\" value=\"1\" checked=\"checked\" /> Omit user directory name?<br />",1);
	}
	else
	{
		$output->addl("<input type=\"checkbox\" name=\"makethumbnail\" value=\"1\" /> Make thumbnail? (if image)<br />",1);
	}
	$output->addl("<input type=\"submit\" class=\"button\" value=\"Upload File\"> <span class=\"small\"><b>Max Size:</b> " . ($junk_maxsize/1024) . " kb. <b>Max Dimensions:</b> $junk_maxx"."x"."$junk_maxy. <b>Valid Formats:</b> " . implode(" ",$junk_formats) . "</span>",1);
	$output->addl("</form>",1);
	$output->display();
}

if ($action == 'upload2')
{
	if (is_uploaded_file($_FILES['fileupload']['tmp_name']))
	{
		$filename = explode(".",strtolower($_FILES['fileupload']['name']));
		if ($_POST['uploadtype'] == 0)
		{
			// image insert
			if (array_search($filename[count($filename)-1],$inserts_formats) !== false)
			{
				$imgsize = getimagesize($_FILES['fileupload']['tmp_name']);
				if ($imgsize != false)
				{
					$ext = $filename[count($filename)-1];
					if (($ext == 'gif' && $imgsize[2] == 1) || ($ext == 'jpg' && $imgsize[2] == 2) || ($ext == 'jpeg' && $imgsize[2] == 1) || ($ext == 'png' && $imgsize[2] == 3) || ($ext == 'bmp' && $imgsize[2] == 6))
					{
						if ($imgsize[0] <= $inserts_maxx && $imgsize[1] <= $inserts_maxy)
						{
							$spaceused = file_within_limits($_FILES['fileupload']['size']);
							if ($_POST['makethumbnail'])
							{
								$thumb = generate_thumbnail($_FILES['fileupload'],$imgsize,"$images_dir/inserts/" . $spruser['uploadname']);
							}
							if (move_uploaded_file($_FILES['fileupload']['tmp_name'],"$images_dir/inserts/" . $spruser['uploadname'] . "/" . $_FILES['fileupload']['name']))
							{
								add_file(1);
								$output->subtitle = 'Uploaded Image Insert';
								$output->addl("<h2>Image Insert uploaded successfully!</h2>",1);
								$output->addl("<p><b>Image URL:</b><br /><a href=\"$images_url/inserts/" . rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "\">http://senselesspoliticalramblings.com/images/inserts/" . rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "</a></p>");
								$output->addl("<p><b>Image HTML:</b><br />&lt;img src=\"http://senselesspoliticalramblings.com$images_url/inserts/" . rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "\" alt=\"" . htmlspecialchars($_FILES['fileupload']['name']) . "\" title=\"\" ".$imgsize[3]." /&gt;</p>");
								$output->addl("<p><b>SPR Image HTML:</b><br />&lt;img src=\"$images_url/inserts/" . rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "\" alt=\"" . htmlspecialchars($_FILES['fileupload']['name']) . "\" title=\"\" ".$imgsize[3]." /&gt;</p>");
								if ($_FILES['fileupload']['size'] >= 1048576)
								{
									$output->addl("<p><b>File Size:</b> " . round($_FILES['fileupload']['size']/1048576,1) . " mb. <b>Dimensions:</b> ".$imgsize[0]."x".$imgsize[1]."</p>",1);
								}
								elseif ($_FILES['fileupload']['size'] >= 1024)
								{
									$output->addl("<p><b>File Size:</b> " . round($_FILES['fileupload']['size']/1024,1) . " kb. <b>Dimensions:</b> ".$imgsize[0]."x".$imgsize[1]."</p>",1);
								}
								else
								{
									$output->addl("<p><b>File Size:</b> " . $_FILES['fileupload']['size'] . " bytes. <b>Dimensions:</b> ".$imgsize[0]."x".$imgsize[1]."</p>",1);
								}
								if ($_POST['makethumbnail'] && is_array($thumb))
								{
									thumbnail_lines($thumb,"/images/inserts/" . rawurlencode($spruser['uploadname']));
								}
								elseif ($_POST['makethumbnail'])
								{
									if ($imgsize[0] <= $thumb_max && $imgsize[1] <= $thumb_max)
									{
										$output->addl("<p>In addition, your image was not thumbnailed because it is too small. (image must be wider or higher than $thumb_max"."px)</p>",1);
									}
									else
									{
										$output->addl("<p><b><i>Your image has not been thumbnailed due to an unknown error! Please try again.</i></b></p>",1);
									}
								}
								space_used_line();
								$output->addl("<p><a href=\"upload.php\">Once more!</a></p>",1);
								$output->display();
							}
							else
							{
								$output->addl("<p>The uploading of your file failed. The cause for this is unknown; There may be an error in the filesystem. Try again momentarily.</p>",1);
								$output->display();
							}
						}
						else
						{
							$output->addl("<p>The image is too big. It must be at most $inserts_maxx"."x"."$inserts_maxy pixels.</p>",1);
							$output->display();
						}
					}
					else
					{
						$output->addl("<p>The file extension and the file's format do not match. Correct this and try again.</p>",1);
						$output->display();
					}
				}
				else
				{
					$output->addl("<p>The image you are submitting does not appear to be a valid image. Make sure that that it's valid and try again.</p>",1);
					$output->display();
				}
			}
			else
			{
				$output->addl("<p>Your image insert is not in a proper image format!<br />Accepted formats are: " .implode(" ",$inserts_formats) . "</p>",1);
				$output->display();
			}
		}
		elseif ($_POST['uploadtype'] == 1)
		{
			// file upload
			if (array_search($filename[count($filename)-1],$files_formats) !== false)
			{
				$spaceused = file_within_limits($_FILES['fileupload']['size']);
				if (move_uploaded_file($_FILES['fileupload']['tmp_name'],"$files_dir/uploads/" . $spruser['uploadname'] . "/" . $_FILES['fileupload']['name']))
				{
					add_file(2);
					$output->subtitle = 'Uploaded File';
					$output->addl("<h2>File uploaded successfully!</h2>",1);
					$output->addl("<p><b>File URL:</b><br /><a href=\"$files_url/uploads/" . rawurlencode($spruser['uploadname']) . "/" .rawurlencode($_FILES['fileupload']['name']) . "\">http://senselesspoliticalramblings.com$files_url/uploads/" . rawurlencode($spruser['uploadname']) . "/" .rawurlencode($_FILES['fileupload']['name']) . "</a></p>",1);
					$output->addl("<p><b>File link:</b><br />&lt;a href=\"http://senselesspoliticalramblings.com$files_url/uploads/" . rawurlencode($spruser['uploadname']) . "/" .rawurlencode($_FILES['fileupload']['name']) . "\"&gt;" . rawurlencode($_FILES['fileupload']['name']) . "&lt;/a&gt;</p>",1);
					$output->addl("<p><b>File SPR link:</b><br />&lt;a href=\"$files_url/uploads/" . rawurlencode($spruser['uploadname']) . "/" .rawurlencode($_FILES['fileupload']['name']) . "\"&gt;" . rawurlencode($_FILES['fileupload']['name']) . "&lt;/a&gt;</p>",1);
					if ($_FILES['fileupload']['size'] >= 1048576)
					{
						$output->addl("<p><b>File Size:</b> " . round($_FILES['fileupload']['size']/1048576,1) . " mb</p>",1);
					}
					elseif ($_FILES['fileupload']['size'] >= 1024)
					{
						$output->addl("<p><b>File Size:</b> " . round($_FILES['fileupload']['size']/1024,1) . " kb</p>",1);
					}
					else
					{
						$output->addl("<p><b>File Size:</b> " . $_FILES['fileupload']['size'] . " bytes</p>",1);
					}
					space_used_line();
					$output->addl("<p><a href=\"upload.php\">Let's not stop here.</a></p>",1);
					$output->display();
				}
			}
			else
			{
				$output->addl("<p>Your upload is not in a proper file format!<br />Accepted formats are: " .implode(" ",$files_formats) . "</p>",1);
				$output->display();
			}
		}
		else
		{
			// junk/misc upload
			if (array_search($filename[count($filename)-1],$junk_formats) !== false)
			{
				$ext = $filename[count($filename)-1];
				$imgsize = getimagesize($_FILES['fileupload']['tmp_name']);
				if ($imgsize == false)
				{
					// is not an image; proceed as planned
					if ($ext != 'gif' && $ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'bmp')
					{
						$spaceused = file_within_limits($_FILES['fileupload']['size']);
						if ($_POST['omituserdir'] && $spruser['isadmin'])
						{
							$m = move_uploaded_file($_FILES['fileupload']['tmp_name'],"$junk_dir/" . $_FILES['fileupload']['name']);
						}
						else
						{
							$m = move_uploaded_file($_FILES['fileupload']['tmp_name'],"$junk_dir/" . $spruser['uploadname'] . "/" . $_FILES['fileupload']['name']);
						}
						if ($m)
						{
							$output->subtitle = 'Junk Upload';
							$output->addl("<h2>Junk File uploaded successfully!</h2>",1);
							if ($_POST['omituserdir'] && $spruser['isadmin'])
							{
								add_file(4);
								$output->addl("<p><b>File URL:</b><br /><a href=\"$junk_url/" . rawurlencode($_FILES['fileupload']['name']) . "\">http://senselesspoliticalramblings.com$junk_url/" . rawurlencode($_FILES['fileupload']['name']) . "</a></p>",1);
								$output->addl("<p><b>File VB code:</b><br />[url]http://senselesspoliticalramblings.com$junk_url/" . rawurlencode($_FILES['fileupload']['name']) . "[/url]</p>",1);
								$output->addl("<p><b>File HTML code:</b><br />&lt;a href=\"http://senselesspoliticalramblings.com$junk_url/" . rawurlencode($_FILES['fileupload']['name']) . "\"&gt;" . htmlspecialchars($_FILES['fileupload']['name']) . "&lt;/a&gt;</p>",1);
								$output->addl("<p><b>File SPR-specific HTML code:</b><br />&lt;a href=\"$junk_url/" . rawurlencode($_FILES['fileupload']['name']) . "\"&gt;" . htmlspecialchars($_FILES['fileupload']['name']) . "&lt;/a&gt;</p>",1);
							}
							else
							{
								add_file(3);
								$output->addl("<p><b>File URL:</b><br /><a href=\"$junk_url/".rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "\">http://senselesspoliticalramblings.com$junk_url/".rawurlencode($spruser['uploadname']) . "/".rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "</a></p>",1);
								$output->addl("<p><b>File VB code:</b><br />[url]http://senselesspoliticalramblings.com$junk_url/".rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "[/url]</p>",1);
								$output->addl("<p><b>File HTML code:</b><br />&lt;a href=\"http://senselesspoliticalramblings.com$junk_url/".rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "\"&gt;" . htmlspecialchars($_FILES['fileupload']['name']) . "&lt;/a&gt;</p>",1);
								$output->addl("<p><b>File SPR-specific HTML code:</b><br />&lt;a href=\"$junk_url/".rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "\"&gt;" . htmlspecialchars($_FILES['fileupload']['name']) . "&lt;/a&gt;</p>",1);
							}
							space_used_line();
							$output->addl("<p><a href=\"upload.php\">Done? Fuck that shit. I'm not done. Not by a long sight.</a></p>",1);
							$output->display();
						}
						else
						{
							$output->addl("<p>Your junk file could not be uploaded due to an unknown error. The filesystem may have a problem. Please try again later.</p>",1);
							$output->display();
						}
						
					}
					else
					{
						$output->addl("<p>Your file does not appear to be a valid image, despite using an image extension. Either rename or fix this file and try again.</p>",1);
						$output->display();
					}
				}
				else
				{
					// is an image, check it
					if (($imgsize[2] == 1 && $ext == 'gif') || ($imgsize[2] == 2 && $ext == 'jpg') || ($imgsize[2] == 2 && $ext == 'jepg') || ($imgsize[2] == 3 && $ext == 'png') || ($imgsize[2] == 6 && $ext == 'bmp'))
					{
						if ($imgsize[0] <= $junk_maxx && $imgsize[1] <= $junk_maxy)
						{
							$spaceused = file_within_limits($_FILES['fileupload']['size']);
							if ($_POST['makethumbnail'])
							{
								if ($spruser['isadmin'] && $_POST['omituserdir'])
								{
									$thumb = generate_thumbnail($_FILES['fileupload'],$imgsize,$junk_dir);
								}
								else
								{
									$thumb = generate_thumbnail($_FILES['fileupload'],$imgsize,"$junk_dir/" . $spruser['uploadname']);
								}
							}
							if ($_POST['omituserdir'] && $spruser['isadmin'])
							{
								$m = move_uploaded_file($_FILES['fileupload']['tmp_name'],"$junk_dir/" . $_FILES['fileupload']['name']);
							}
							else
							{
								$m = move_uploaded_file($_FILES['fileupload']['tmp_name'],"$junk_dir/" . $spruser['uploadname'] . "/" . $_FILES['fileupload']['name']);
							}
							if ($m)
							{
								$output->subtitle = 'Junk Upload';
								$output->addl("<h2>Junk Image uploaded successfully!</h2>",1);
								if ($_POST['omituserdir'] && $spruser['isadmin'])
								{
									add_file(4);
									$output->addl("<p><b>Image URL:</b><br /><a href=\"$junk_url/" . rawurlencode($_FILES['fileupload']['name']) . "\">http://senselesspoliticalramblings.com$junk_url/" . rawurlencode($_FILES['fileupload']['name']) . "</a></p>",1);
									$output->addl("<p><b>Image VB code:</b><br />[img]http://senselesspoliticalramblings.com$junk_url/" . rawurlencode($_FILES['fileupload']['name']) . "[/img]</p>",1);
									$output->addl("<p><b>Image HTML code:</b><br />&lt;img src=\"http://senselesspoliticalramblings.com$junk_url/" . rawurlencode($_FILES['fileupload']['name']) . "\" alt=\"" . htmlspecialchars($_FILES['fileupload']['name']) . "\" title=\"\" " . $imgsize[3] . " /&gt;</p>",1);
									$output->addl("<p><b>Image SPR-specific HTML code:</b><br />&lt;img src=\"$junk_url/" . rawurlencode($_FILES['fileupload']['name']) . "\" alt=\"" . htmlspecialchars($_FILES['fileupload']['name']) . "\" title=\"\" " . $imgsize[3] . " /&gt;</p>",1);
									if ($_POST['makethumbnail'] && is_array($thumb))
									{
										thumbnail_lines($thumb,"/junk");
									}
								}
								else
								{
									add_file(3);
									$output->addl("<p><b>Image URL:</b><br /><a href=\"$junk_url/" . rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "\">http://senselesspoliticalramblings.com$junk_url/" . rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "</a></p>",1);
									$output->addl("<p><b>Image VB code:</b><br />[img]http://senselesspoliticalramblings.com$junk_url/" . rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "[/img]</p>",1);
									$output->addl("<p><b>Image HTML code:</b><br />&lt;img src=\"http://senselesspoliticalramblings.com$junk_url/" . rawurlencode($spruser['uploadname']) . "/" . htmlspecialchars($_FILES['fileupload']['name']) . "\" alt=\"" . htmlspecialchars($_FILES['fileupload']['name']) . "\" title=\"\" " . $imgsize[3] . " /&gt;</p>",1);
									$output->addl("<p><b>Image SPR-specific HTML code:</b><br />&lt;img src=\"$junk_url/" . rawurlencode($spruser['uploadname']) . "/" . rawurlencode($_FILES['fileupload']['name']) . "\" alt=\"" . rawurlencode($_FILES['fileupload']['name']) . "\" title=\"\" " . $imgsize[3] . " /&gt;</p>",1);
									if ($_POST['makethumbnail'] && is_array($thumb))
									{
										thumbnail_lines($thumb,"/junk/" . rawurlencode($spruser['uploadname']));
									}
								}
								space_used_line();
								$output->addl("<p><a href=\"upload.php\">Don't let it end like this.</a></p>",1);
								$output->display();
							}
							else
							{
								$output->addl("<p>Your junk file could not be uploaded due to an unknown error. The filesystem may have a problem. Please try again later.</p>",1);
								$output->display();
							}
						}
						else
						{
							$output->addl("<p>Your image is too goddamn big! Go and size it down to $junk_maxx"."x"."$junk_maxy and try again.</p>",1);
							$output->display();
						}
					}
					else
					{
						$output->addl("<p>The extension that this image has does not match the file format it is in. Please correct this and try again.</p>",1);
						$output->display();
					}
				}
			}
			else
			{
				$output->addl("<p>Your junk upload is not in a proper file format!<br />Accepted formats are: " .implode(" ",$junk_formats) . "</p>",1);
				$output->display();
			}
		}
	}
	else
	{
		$output->addl("<p>Your file could not be uploaded. Perhaps it was too big, or it may be potentially malicious.</p>",1);
		$output->display();
	}
}

?>