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

	# admin.php
	# This script performs several administrative tasks
	# 

	header('Pragma: no-cache');

	require('shared.php');

	# Make a connection to the database
	$db_read = get_db_read();
	$db_write = get_db_write();

	if($me == null || $me['admin'] != '1')
		show_error_redirect_back("Error");

	if(isset($_GET['action']) == false)
		show_error_redirect_back('No action specified');
	$action = $_GET['action'];

	if(isset($_GET['user_id']) && is_numeric($_GET['user_id']))
		$user_id = $_GET['user_id'];

	if($action == 'authorize')
	{
		if(isset($user_id) == false)
			show_error_redirect_back('No user_id specified');

		try_mysql_query("UPDATE users SET authorized='1' WHERE user_id='$user_id'", $db_write);
		show_message_redirect_back("User successfully authorized.");
	}
	else if($action == 'promote')
	{
		if(isset($user_id) == false)
			show_error_redirect_back('No user_id specified');

		try_mysql_query("UPDATE users SET admin='1' WHERE user_id='$user_id'", $db_write);
		show_message_redirect_back("User successfully granted admin privilidges");
	}
	else if($action == 'demote')
	{
		if(isset($user_id) == false)
			show_error_redirect_back('No user_id specified');

		try_mysql_query("UPDATE users SET admin='0' WHERE user_id='$user_id'", $db_write);
		show_message_redirect_back("User successfully revoked admin privilidges");
	}
	else
	{
		show_error_redirect_back("Unknown action");
	}

?>
	
	
	

