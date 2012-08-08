<?php
	# Copyright (c) 2005, Ron Bowes
	# All rights reserved.
	#
	# Redistribution and use in source and binary forms, with or without modification, 
	# are permitted provided that the following conditions are met:
	#
	#	* Redistributions of source code must retain the above copyright notice, this 
	#	  list of conditions and the following disclaimer.
	#	* Redistributions in binary form must reproduce the above copyright notice, 
	#	  this list of conditions and the following disclaimer in the documentation 
	#	  and/or other materials provided with the distribution.
	#	* Neither the name of the organization nor the names of its contributors 
	#	  may be used to endorse or promote products derived from this software 
	#	  without specific prior written permission.
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
 	#

	# login.php
	# This is the file used for logging in.  There are two parameters, the username
	# and the password.  These are sanitized, and it checks if the account is in the
	# database
	# 

	header('Pragma: no-cache');

	require('shared.php');

	if(isset($_GET['action']))
		$action = $_GET['action'];

	if(isset($action) && $action == "logout")
	{
		logout();
		show_message_redirect_back("Logged out");
	}
	else
	{
		$username = mysql_escape_string(htmlentities(trim($_POST['username'])));
		$password = $_POST['password'];
		$info = check_login($username, $password);
	
		if(!$info)
		{
			show_error_redirect_back("Login failed");
		}
		else
		{
			show_message_redirect_back("Logged in");
		}
	}


	# Functions

	# NOTE: USERNAME HAS TO BE SANITIZED BEFORE ENTERING!
	function check_login($username, $password, $remember = true)
	{
		$db = get_db_read();

		# Get the salt and check if the user exists at the same time
		$result = try_mysql_query("SELECT salt FROM users WHERE username = '$username'", $db);
		if(mysql_num_rows($result) != 1)
			return null;

		$row = mysql_fetch_assoc($result);
		$salt = $row['salt'];
		mysql_free_result($result);

		$hashed_password = hash_password($password, $salt);

		$ret = get_user_info($db, $username, $hashed_password);
		if($ret == null)
			return null;

		if($remember == true)
		{
			setcookie("username", $username, time()+60*60*24*3000);
			setcookie("password", $hashed_password, time()+60*60*24*3000);
		}

		$_SESSION["username"] = $username;

		return $ret;
	}

	function logout()
	{
		$_SESSION["username"] = null;
		$_SESSION["password"] = null;

		setcookie("username", null, time());
		setcookie("password", null, time());
	}
	
?>
