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

	# confirm_upload.php
	# * Verify that the category is owned by the user
	# * Image is inserted into the database under the proper category
	# 

	header('Pragma: no-cache');

	require('shared.php');

	# Make a connection to the database
	$db_read = get_db_read();
	$db_write = get_db_write();

	if(!$me)
		show_error_redirect_back("Please log in before uploading an image");

	if(isset($_SESSION['image_filename']) == false)
		show_error_redirect_back("Error uploading image!  A session variable is missing set, so either there was a session timeout or you tried to reload the page.  Please try again.");

	$image_filename = $_SESSION['image_filename'];
	$_SESSION['image_filename'] = null;

	if(isset($_POST['category_id']) == false || is_numeric($_POST['category_id']) == false)
		show_error_redirect_back("Error -- category wasn't found");

	$title = mysql_escape_string(htmlentities(trim($_POST['title'])));
	$caption = mysql_escape_string(nl2br(htmlentities(trim($_POST['caption']))));
	$category = get_category_by_category_id($_POST['category_id'], $db_read);

	if(validate_title($title) == false)
		show_error_redirect_back("Invalid title.  Titles have to be 0-$max_length_title characters.");

	if(validate_comment($caption) == false)
		show_error_redirect_back("Invalid caption.  Captions have to be 0-$max_length_comment characters.");


	# Make sure he's uploading to his own category
	$result = try_mysql_query("SELECT * FROM categories WHERE user_id='" . $me['user_id'] . "' AND category_id='" . $category['category_id'] . "'", $db_read);
	if(mysql_num_rows($result) == 0)
		show_error_redirect_back("Invalid category.");
	mysql_free_result($result);

	# Insert the new picture
	try_mysql_query("INSERT INTO pictures (category_id, title, filename, caption, date_added) VALUES ('" . $category['category_id'] . "', '$title', '$image_filename', '$caption', NOW())", $db_write);
	$picture_id = mysql_insert_id($db_write);
	# Update the las modified category (used for the default selection in the category combo)
	try_mysql_query("UPDATE users SET last_category='" . $category['category_id'] . "' WHERE user_id='" . $me['user_id'] . "'", $db_write);

	# Update the last modified time for the private user/category
	try_mysql_query("UPDATE users SET last_updated=NOW() WHERE user_id='" . $me['user_id'] . "'", $db_write);
	try_mysql_query("UPDATE categories SET last_updated=NOW() WHERE category_id='" . $category['category_id'] . "'", $db_write);
	# Set the last modified time for the public user/category
	if($category['private'] != '1')
	{
		try_mysql_query("UPDATE users SET last_updated_public=NOW() WHERE user_id='" . $me['user_id'] . "'", $db_write);
		try_mysql_query("UPDATE categories SET last_updated_public=NOW() WHERE category_id='" . $category['category_id'] . "'", $db_write);
	}

	$user_ids = get_emails_notify_pictures($db_read);
	smtp_send($user_ids, "OSPAP - New Picture", "New picture notification", "A new picture has been posted in " . $me['username'] . "'s category, " . $category['name'] . "!  Here is a link to it:\n\n" . get_full_path_to("show_picture.php?picture_id=" . $picture_id) . "\n\nTitle: $title\n\nCaption:\n$caption\n\nNote: this is an automatic email, please don't reply.");

	show_message_redirect("Picture successfully uploaded", "show_category.php?category_id=" . $category['category_id']);


?>
