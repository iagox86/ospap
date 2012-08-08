<?php

	# Copyright (c) 2005, Ron Bowes
	# All rights reserved.
	#
	# Redistribution and use in source and binary forms, with or without modification, 
	# are permitted provided that the following conditions are met:
	#
	#	  * Redistributions of source code must retain the above copyright notice, this 
	#	list of conditions and the following disclaimer.
	#	  * Redistributions in binary form must reproduce the above copyright notice, 
	#	this list of conditions and the following disclaimer in the documentation 
	#	and/or other materials provided with the distribution.
	#	  * Neither the name of the organization nor the names of its contributors 
	#	may be used to endorse or promote products derived from this software 
	#	without specific prior written permission.
	#
	# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
	# AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
	# IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
	# ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
	# LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
	# CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
	# SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
	# INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
	# CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
	# ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
	# POSSIBILITY OF SUCH DAMAGE.

	# shared.php
	# This script defines common functions that are used by other scripts

	require('configuration.php');
	require('database.php');

	# We have to start the session!
	session_start();


	if(is_file("skins/$skin/skin.php") == false)
		$skin = "default";
	require("skins/$skin/skin.php");

	# Get the previous page and set the current page
	$back = "index.php";
	if(isset($_SESSION['back']))
		$back = $_SESSION['back'];

	# Delete files from our preview directory that are too old
	$dir = opendir($preview_directory);
	while($f = readdir($dir))
	{
		if(is_dir($f) == false)
		{
			$info = stat("$preview_directory/$f");
			$age_in_seconds = time() - $info['mtime'];
			$age_in_minutes = $age_in_seconds / 60;
			if($age_in_minutes > $preview_timeout)
				unlink("$preview_directory/$f");
		}
	}

	if(is_dir($upload_directory) == false)
		mkdir($upload_directory) or show_error_die("Unable to create upload directory '$upload_directory'");
	if(is_dir($preview_directory) == false)
		mkdir($preview_directory) or show_error_die("Unable to create preview directory '$preview_directory'");

	$me = get_current_user_info(get_db_read());

	# Makes sure the username is made up of letters and numbers, and is between 3 and 14 characters long	
	function validate_username($username)
	{
		if(isset($username) == false)
			return false;

		if(ereg('^([a-zA-Z0-9])*$', $username) == false)
			return false;

		if(strlen($username) < 3 || strlen($username) > 14)
			return false;

		return true;
	}

	# Makes sure the password isn't an unreasonable length
	function validate_password($password)
	{
		if(isset($password) == false)
			return false;

		if(strlen($password) < 6)
			return false;

		return true;
	}

	# Checks if the email address is a reasonable length (1024 bytes) and in the form of 
	# [characters]@[characters].[characters]
	function validate_email($email)
	{
		global $max_length_email;

		if(isset($email) == false)
			return false;
	
		if(strlen($email) > $max_length_email)
			return false;

		if(ereg('^[a-zA-Z0-9_-][\.a-zA-Z0-9_-]*@[\.a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]+$', $email) == false)
			return false;

		return true;
	}

	# Makes sure the category is valid
	function validate_category($category)
	{
		global $max_length_category;

		if(isset($category) == false)
			return false;

		if(strlen($category) < 3 || strlen($category) > $max_length_category)
			return false;

		return true;
	}

	# Makes sure the picture's title is valid
	function validate_title($title)
	{
		global $max_length_title;

		if(isset($title) == false)
			return false;

		if(strlen($title) < 0 || strlen($title) > $max_length_title)
			return false;

		return true;
	}

	# Makes sure a comment is valid
	function validate_comment($comment)
	{
		global $max_length_comment;


		if(isset($comment) == false)
			return false;

		if(strlen($comment) < 0 || strlen($comment) > $max_length_comment)
			return false;

		return true;
	}

	# Generates a random string of hex-digits up to the specified length (Default 16 characters)
	function generate_salt($size = 16)
	{
		$salt = "";
		for ($i = 0; $i < $size; $i++)
			$salt .= substr('0123456789abcdef', rand(0,15), 1);

		return $salt;
	}

	# Hashes the password with the given salt.  I probably complicated it a little by making it the sha1
	# of the md5, but it looks so cool!
	function hash_password($password, $salt)
	{
		return sha1(md5($password . $salt) . md5($salt));
	}

	# Returns the assoc array about the currently logged in user.  If they aren't logged in, it returns
	# null.  
	function get_current_user_info($db)
	{
		global $require_authorization;

		if(isset($_SESSION['username']))
			$username =$_SESSION['username']; 

		if(isset($username))
		{
			# Get their info from the session
			$ret = get_user_info($db, $username);
		}
		else
		{
			# Get their info from the cookie
			if(isset($_COOKIE['username']) == false || isset($_COOKIE['password']) == false)
				return null;
	
			$username = $_COOKIE['username'];
			$password = $_COOKIE['password'];
	
			# Make sure they aren't trying to sneak some garbage into the cookie
			if(validate_username($username) == false)
				return null;
	
			$ret = get_user_info($db, $username, $password);
		}

		if($ret == null)
			return null;

		# Check if they're waiting for authorization
		if($require_authorization && $ret['authorized'] != '1')
		{
			print "Your username is " . $ret['username'] . ", but it requires authorization by an administrator<BR>";
			print "In the meantime, you can log into a different account<BR>";
			print "<HR>";

			return null;
		}

		return $ret;
	}

	# Returns the characters of a string after the final "." and returns them
	function get_extension($filename)
	{
		return substr(strrchr($filename, '.'), 1);
	}

	function get_directory($filename)
	{
		return substr($filename, 0, strlen($filename) - strlen(strrchr($filename, '/')));
	}

	function get_full_path_to($file)
	{
		return("http://" . $_SERVER['HTTP_HOST'] . get_directory($_SERVER['REQUEST_URI'])) . '/' . $file;
	}

	# Resizes (if necessary) and compresses image to the new filename.  
	# The new filename should only be in .jpeg format.  
	function resize_and_compress($max_width, $max_height, $jpeg_quality, $old_filename, $new_filename)
	{
		# Check the image's size		 
		list($width, $height, $type, $attr) = getimagesize($old_filename);

		# Check if resizing is necessary									  
		if($width > $max_width or $height > $max_height)
		{				
			# Get the ratio		 
			$ratio = $width / $height;
									
			# Try using maximum width
			$new_width = $max_width;	 
			$new_height = $new_width / $ratio;

			# If that failed, try maximum height
			if($new_height > $max_height)
			{
				$new_height = $max_height;
				$new_width = $ratio * $new_height;
		
				# If THAT failed (which I don't think it will...), just use max both
				if($new_width > $max_width)
					$new_width = $max_width;
			}
		}
		else
		{
			$new_width = $width;
			$new_height = $height;
		}
	 
		resize_image($new_width, $new_height, $jpeg_quality, $old_filename, $new_filename);
	}

	# Displays an unrecoverable (unredirectable) error
	function show_error_die($message)
	{
		template_error($message, null);
		exit(1);
	}

	# Displays an error and redirects after 5 seconds
	function show_error_redirect($message, $url = null)
	{
		template_error($message, $url);
		exit(1);
	}

	# Displays a message and redirects after 5 seconds
	function show_message_redirect($message, $url = null)
	{
		template_message($message, $url);
	}

	# Displays an error and redirects after 5 seconds
	function show_error_redirect_back($message)
	{
		global $back;
		template_error($message, $back);
		exit(1);
	}

	# Displays a message and redirects back after 5 seconds
	function show_message_redirect_back($message)
	{
		global $back;
		template_message($message, $back);
		exit(1);
	}

	function show_mysql_error($query, $sql_error)
	{
		template_error("Error in SQL: $sql_error<BR>Query: $query<BR>");
		exit(1);
	}

	# Taken from SMF source and modified to suit my needs
	# Sends an email via the SMTP server
	function smtp_send($mail_to_array, $from_name, $subject, $message)
	{
		global $smtp_allow, $smtp_server, $smtp_port, $smtp_email, $smtp_username, $smtp_password;

		# If they didn't want smtp being used, just die
		if($smtp_allow != 1)
			return true;

		# Try connecting to the SMTP server
		if (!($socket = fsockopen($smtp_server, $smtp_port, $errno, $errstr, 5)))
		{
			# Unable to connect!
			print("Unable to connect to the SMTP server to email alerts: $errno : $errstr<BR>");
			return false;
		}

        // Construct the mail headers...
        $headers = 'From: "' . addcslashes($from_name, '<>[]()\'\\"') . '" <' . $smtp_email . ">\r\n";
        $headers .= 'Date: ' . gmdate('D, d M Y H:i:s') . ' +0000' . "\r\n";

		// Wait for a response of 220, without "-" continuer.
		if (!server_parse(null, $socket, '220'))
			return false;

		if ($smtp_username != '' && $smtp_password != '')
		{
			// EHLO could be understood to mean encrypted hello...
			if (!server_parse('EHLO ' . $smtp_server, $socket, '250'))
				return false;
			if (!server_parse('AUTH LOGIN', $socket, '334'))
				return false;
			// Send the username ans password, encoded.
			if (!server_parse(base64_encode($smtp_username), $socket, '334'))
				return false;
			if (!server_parse(base64_encode($smtp_password), $socket, '235'))
				return false;
		}
		else
		{
				// Just say "helo".
			if (!server_parse('HELO ' . $smtp_server, $socket, '250'))
				return false;
		}

		foreach ($mail_to_array as $mail_to)
		{
			// From, to, and then start the data...
			if (!server_parse('MAIL FROM: <' . $smtp_email . '>', $socket, '250'))
				return false;
			if (!server_parse('RCPT TO: <' . $mail_to . '>', $socket, '250'))
				return false;
			if (!server_parse('DATA', $socket, '354'))
				return false;
			fputs($socket, 'Subject: ' . $subject . "\r\n");
			if (strlen($mail_to) > 0)
				fputs($socket, 'To: <' . $mail_to . ">\r\n");
			fputs($socket, $headers . "\r\n\r\n");
			fputs($socket, $message . "\r\n");

			// Send a ., or in other words "end of data".
			if (!server_parse('.', $socket, '250'))
				return false;
			// Reset the connection to send another email.
			if (!server_parse('RSET', $socket, '250'))
				return false;
		}
		fputs($socket, "QUIT\r\n");
		fclose($socket);

		return true;
}

# Parse a message to the SMTP server.
function server_parse($message, $socket, $response)
{
	global $txt;

	if ($message !== null)
		fputs($socket, $message . "\r\n");

	// No response yet.
	$server_response = '';

	while (substr($server_response, 3, 1) != ' ')
	{
		if (!($server_response = fgets($socket, 256)))
		{
			print("Error in SMTP server: invalid response<BR>");
			return false;
		}
	}

	if (substr($server_response, 0, 3) != $response)
	{
		print("SMTP error: unexpected response: $server_response<BR>");
		return false;
	}

	return true;
}

# Get the list of skins
function get_skins()
{
	$files = opendir("skins");

	$skins = array();
	while($file = readdir($files))
	{
		if(is_file("skins/$file/skin.php"))
		{
			array_push($skins, $file);
		}
	}

	return $skins;
}


// NO NEWINE at bottom! It screws up pictures!
?>
