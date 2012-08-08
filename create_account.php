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

	# create_account.php
	# This script creates a new account for a user, and inserts it into the database
	# 

	header('Pragma: no-cache');

	require_once('shared.php');

	# Make a connection to the database
	$db_read = get_db_read();
	$db_write = get_db_write();

	$username = mysql_escape_string(htmlentities(trim($_POST['username'])));
	$password = $_POST['password'];
	$email = mysql_escape_string(htmlentities(trim($_POST['email'])));
	$notify_comments = isset($_POST['notify_comments'])   ? '1' : '0';
	$notify_pictures = isset($_POST['notify_pictures']) ? '1' : '0';

	if($password != $_POST['password2'])
		show_error_redirect_back("Passwords don't match!");

	if(count(get_all_users(true, $db_read)) == 0)
		$admin = '1';
	else
		$admin = '0';

	# Validate the variables
	if(validate_username($username) == false)
		show_error_redirect_back("Please enter a username made up of 3 - 14 alpha-numeric characters");
	if(validate_password($password) == false)
		show_error_redirect_back("Please enter a password that is at least 6 characters (it's for your own protection!)");
	if(validate_email($email) == false)
		show_error_redirect_back("Please enter a valid email address");

	# Check if the username is being used
	$result = try_mysql_query("SELECT * FROM users WHERE username='" . $username . "'", $db_read);
	if(mysql_num_rows($result) > 0)
		show_error_redirect_back("Sorry, that username is already in use.");
	mysql_free_result($result);

	# Check if the email address is already used
	$result = try_mysql_query("SELECT * FROM users WHERE email='" . $email . "'", $db_read);
	if(mysql_num_rows($result) > 0)
		show_error_redirect_back("Sorry, that email address is already in use.");
	mysql_free_result($result);


	# Generate the salt and hash the password
	$salt = generate_salt();
	$hashed_password = hash_password($password, $salt);

	try_mysql_query("INSERT INTO users (username, password, salt, email, date_registered, authorized, admin, last_updated, last_updated_public, notify_comments, notify_pictures) VALUES ('$username', '$hashed_password', '$salt', '$email', NOW(), '$require_authorization', '$admin', '0', '0', '$notify_comments', '$notify_pictures')", $db_write);

	show_message_redirect_back("Account created! Please log in.");	

?>
	
	
	

