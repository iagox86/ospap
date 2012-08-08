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

	# post_comment.php
	# Post a comment on an image.  
	# 

	header('Pragma: no-cache');

	require('shared.php');

	# Make a connection to the database
	$db_read = get_db_read();
	$db_write = get_db_write();

	if(!$me)
		show_error_redirect_back("Please log in first");

	if(isset($_POST['picture_id']) == false)
		show_error_redirect_back("Couldn't find picture id");
	if(isset($_POST['comment']) == false)
		show_error_redirect_back("Couldn't find comment");
	
	$comment = mysql_escape_string(nl2br(htmlentities(trim($_POST['comment']))));
	$picture_id = $_POST['picture_id'];

	if(validate_comment($comment) == false)
		show_error_redirect_back("Invalid comment.  Comments have to be 0-$max_length_comment characters.");

	if(is_numeric($picture_id) == false)
		show_error_redirect_back("Invalid category.");

	try_mysql_query("INSERT INTO comments (user_id, picture_id, text, date_added) VALUES ('" . $me['user_id'] . "', '$picture_id', '$comment', NOW())", $db_write);

	$user = get_user_from_picture_id($picture_id, $db_read);

	if($user['notify_comments'] == '1')
	{
		smtp_send(array($user['email']), "OSPAP - New Comment", "New Comment Notification", "A new comment has been posted for one of your pictures!  It was posted by " . $me['username'] . " and can be viewed here:\n" . get_full_path_to("show_picture.php?picture_id=$picture_id") . "\n\nNote: this is an automatic email, please don't reply.");
	}
	

	show_message_redirect("Comment added", "show_picture.php?picture_id=$picture_id#comments");

?>
