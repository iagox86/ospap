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

	# show_user.php
	# This shows a list of the categories owned by the specified user.  If no 
	# user is specified, it shows everything.  
	# 

	header('Pragma: no-cache');

	require('shared.php');

	# Make a connection to the database
	$db = get_db_read();
	$_SESSION['back'] = $_SERVER['REQUEST_URI'];

	# Read the user_id parameter
	$user_information = null;
	if(isset($_GET['user_id']) && is_numeric($_GET['user_id']))
		$user_information = get_user_by_user_id($_GET['user_id'], $db);

	# Get the userinformation, get the category list and display the header
	if($user_information != null)
		$categories = get_categories_by_user_id($user_information['user_id'], $me, $db);
	else
		$categories = get_all_categories($me != null, $db);

	# Display all the categories
	$new_categories = array();
	foreach($categories as $category)
	{
		$category['num_pictures'] = count(get_pictures_by_category_id($category['category_id'], $db));
		$category['last_updated'] = ($me ? $category['last_updated'] : $category['last_updated_public']);
		$category['url'] = "show_category.php?category_id=" . $category['category_id'];
		array_push($new_categories, $category);
	}

	template_display_category_list($me, $user_information, $new_categories);

?>
	
	
	

