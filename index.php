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

	# index.php
	# This is the main index file.  
	# * If there are no users, ask the viewer to create an administrator account.
	# * It will have a list of all users (who have albums/categories).  When 
	#   clicked on, it will bring the viewer to the user's page.  
	# * It will also have a "view all categories" option.
	# * If the viewer isn't logged in, it will ask them to log in/register.
	# * If the viewer is logged in, it will list their information (categories, pictures).
	# * If the viewer is an admin, display a list of users requiring authorization.
	# * Display other stats (pictures, categories, etc).
	# 

	header('Pragma: no-cache');

	require('shared.php');

	# Make a connection to the database
	$db = get_db_read();
	$_SESSION['back'] = $_SERVER['REQUEST_URI'];

	if($me)
	{
		$me['category_count'] = count(get_categories_by_user_id($me['user_id'], true, $db));
		$me['picture_count'] = count(get_pictures_by_user_id($me['user_id'], true, $db));
		$me['url'] = "show_user.php?user_id=" . $me['user_id'];
	}

	$users = get_all_users($me != null, $db);

	# Set up the list of users (add appropriate information to the array)
	$full_users = array();
	$total_users = array();
	$total_users['category_count'] = 0;
	$total_users['picture_count'] = 0;
	$total_users['url'] = "show_user.php";
	while($user_information = array_shift($users))
	{
		$user_information['category_count'] = count(get_categories_by_user_id($user_information['user_id'], $me != null, $db));
		$user_information['picture_count'] = count(get_pictures_by_user_id($user_information['user_id'], $me != null, $db));
		$user_information['url'] = "show_user.php?user_id=" . $user_information['user_id'];

		array_push($full_users, $user_information);
		$total_users['category_count'] += $user_information['category_count'];
		$total_users['picture_count'] += $user_information['picture_count'];

		if(!$me)
			$user_information['last_updated'] = $user_information['last_updated_public'];
	}

	$unauthorized = get_unauthorized_users($db);
	$new_unauthorized = array();

	# Fix up the unauthorized list
	foreach($unauthorized as $user)
	{
		$user['url'] = "admin.php?action=authorize&user_id=" . $user['user_id'];
		array_push($new_unauthorized, $user);
	}

	template_display_user_list($me, $full_users, $total_users, $new_unauthorized);

?>
	
