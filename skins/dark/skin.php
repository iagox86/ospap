<?php

# The structure of the data for users:
#   $user['user_id'] = the id of the user in the database
#   $user['username'] = the displayed username of the user
#   $user['email'] = the user's email address
#   $user['notify_comments'] = 1 if the user wants to be notified when somebody comments on their picture
#   $user['notify_pictures'] = 1 if the user wants to be notified for any picture
#   $user['date_registered'] = the date that the user registered on the forum
#   $user['authorized'] = 1 if the user is authorized
#   $user['admin'] = 1 if the user is an administrator
#   $user['last_category'] = the last category that the user viewed
#   $user['last_updated'] = the last update to this user's categories
#   $user['category_count'] = the number of categories belonging to this user
#   $user['picture_count'] = the number of pictures belonging to this user
#   $user['url'] = the URL to view the list of categories for the user


# This function isn't "exported", so to speak.  It's just used to make life easier.  
function display_header($redirect = null, $redirect_delay = 0)
{
	print("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n");
	print("\n");
	print("<!--\n");
	print(" Copyright (c) 2005, Ron Bowes\n");
	print(" All rights reserved.\n");
	print("\n");
	print(" Redistribution and use in source and binary forms, with or without modification, \n");
	print(" are permitted provided that the following conditions are met:\n");
	print("\n");
	print("   * Redistributions of source code must retain the above copyright notice, this \n");
	print(" list of conditions and the following disclaimer.\n");
	print("   * Redistributions in binary form must reproduce the above copyright notice, \n");
	print(" this list of conditions and the following disclaimer in the documentation \n");
	print(" and/or other materials provided with the distribution.\n");
	print("   * Neither the name of the organization nor the names of its contributors \n");
	print(" may be used to endorse or promote products derived from this software \n");
	print(" without specific prior written permission.\n");
	print("\n");
	print(" THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS \"AS IS\" \n");
	print(" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE \n");
	print(" IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE \n");
	print(" ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE \n");
	print(" LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR \n");
	print(" CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF \n");
	print(" SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS \n");
	print(" INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN \n");
	print(" CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) \n");
	print(" ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE \n");
	print(" POSSIBILITY OF SUCH DAMAGE.\n");
	print(" -->\n\n");

	print("<HTML>\n");
	print("	<HEAD>\n");
	print("		<TITLE>OSPAP</TITLE>\n");
	print("		<LINK REL=\"stylesheet\" TYPE=\"text/css\" HREF=\"skins/dark/style.css\">");
	if($redirect != null)
		print("		<META HTTP-EQUIV=\"refresh\" CONTENT=\"$redirect_delay;URL='$redirect'\">");
	print("	</HEAD>\n");
	print("	<BODY>\n");
}

# This function isn't "exported", so to speak.  It's just used to make life easier.  
function display_footer()
{
	print("<HR>\n");
	print("Written by <A HREF=\"mailto:iago@valhallalegends.com\">Ron Bowes</A>, 2005.  Comments welcome!\n");
	print("</BODY>\n");
	print("</HTML>\n");
}

# This function isn't required, but it's recommended.  The user panel is the display
# at the top of every page thd either says "please log in" or "welcome back".  
# $current_user - will be set to the user's data, or null if they aren't logged in.
function display_user_panel($current_user)
{
	if($current_user == null)
	{
		print("Log in:<BR>\n");
		print("	<FORM ACTION=\"login.php\" METHOD=\"POST\">\n");
		print("		Username: <INPUT TYPE=\"text\" NAME=\"username\"><BR>\n");
		print("		Password: <INPUT TYPE=\"password\" NAME=\"password\"><BR>\n");
		print("		<INPUT TYPE=\"submit\" VALUE=\"Log in\"><BR>\n");
		print("	</FORM>\n");

		print("<HR>\n");

		print("If you haven't registered already, register an account:<BR>\n");
		print("	<FORM ACTION=\"create_account.php\" METHOD=\"post\">\n");
		print("		Username: <INPUT TYPE=\"text\" NAME=\"username\" VALUE=\"\"><BR>\n");
		print("		Password: <INPUT TYPE=\"password\" NAME=\"password\" VALUE=\"\"><BR>\n");
		print("		(Again): <INPUT TYPE=\"password\" NAME=\"password2\" VALUE=\"\"><BR>\n");
		print("		Email: <INPUT TYPE=\"text\" NAME=\"email\" value=\"\"><BR>\n");
		print("		Email you when somebody posts a comment to your picture? <INPUT TYPE=\"checkbox\" NAME=\"notify_comments\" CHECKED=\"checked\"><BR>\n");
		print("		Email you when somebody posts a new picture? (Note: this will probably get annoying) <INPUT TYPE=\"checkbox\" NAME=\"notify_pictures\" VALUE=\"0\"><BR>\n");
		print("		<INPUT TYPE=\"submit\" VALUE=\"Create Account\"><BR>\n");
		print("	</FORM>\n");

		print("<HR>");
	}
	else
	{
		print("<TABLE>\n");
		print("	<TR>\n");

		# Welcome them back
		print("		<TD>Welcome back, " . $current_user['username'] . " [<A HREF=\"login.php?action=logout\">Log out</A>]</TD>\n");

		# Let them create a category
		print("<FORM ACTION=\"create_category.php\" METHOD=\"POST\">\n");
		print("		<TD>New category: <INPUT TYPE=\"text\" NAME=\"category\" VALUE=\"\">\n");
		print("			<INPUT TYPE=\"checkbox\" NAME=\"private\" TITLE=\"Private; only registered users will be able to view this category\">\n");
		print("			<INPUT TYPE=\"submit\" VALUE=\"Create\">\n");
		print("		</TD>\n");
		print("</FORM>\n");

		# Let them upload a picture
		# TODO: Make sure the user actually has at least one category
		print("<FORM ACTION=\"preview.php\" METHOD=\"POST\" ENCTYPE=\"multipart/form-data\">\n");
		print("		<TD>\n");
		print("			Upload image: <INPUT TYPE=\"file\" NAME=\"file\">\n");
		print("			<INPUT TYPE=\"submit\" VALUE=\"Preview\">\n");
		print("		</TD>\n");
		print("</FORM>\n");

		print("	</TR>\n");
		print("</TABLE>\n");
	}
}

# Displays the main list of users, found on the main page.  
#
# $current_user - The currently logged in user
# $all_users - An array of user structures for all users
# $total_users - An array with 3 elements:
#   $total_users['url'] - The URL to view all users
#   $total_users['category_count'] - The number of categories the user has
#   $total_users['picture_count'] - The number of pictures the user has
# $unauthorized_users - The list of users that require authorization by
#   an administrator.  This should only be displayed to administrators, but
#   it doesn't really hurt to display them to everybody.  
function template_display_user_list($current_user, $all_users, $total_users, $unauthorized_users)
{
	display_header();
	display_user_panel($current_user);

	print("<H2>Users</H2>\n");

	print("<TABLE>\n");
	print("	<TR>\n");
	print("		<TD>Username</TD>\n");
	print("		<TD>Categories</TD>\n");
	print("		<TD>Pictures</TD>\n");
	print("		<TD>Last Update</TD>\n");
	print("	</TR>\n");

	foreach($all_users as $user)
	{
		print("<TR>\n");
		print("	<TD ALIGN=\"left\"><A HREF=\"" . $user['url'] . "\">" . $user['username'] . "</A></TD>\n");
		print("	<TD ALIGN=\"center\">" . $user['category_count'] . "</TD>\n");
		print("	<TD ALIGN=\"center\">" . $user['picture_count'] . "</TD>\n");
		print("	<TD ALIGN=\"center\">" . ($user['last_updated'] == 0 ? "<I>Never</I>" : $user['last_updated']) . "</TD>\n");
		print("</TR>\n");
	}

	print("<TR>\n");
	print("	<TD COLSPAN=\"3\"></TD>\n");
	print("</TR>\n");

	print("<TR>\n");
	print("	<TD ALIGN=\"left\"><A HREF=\"" . $total_users['url'] . "\">Total</A></TD>\n");
	print("	<TD ALIGN=\"center\">" . $total_users['category_count'] . "</TD>\n");
	print("	<TD ALIGN=\"center\">" . $total_users['picture_count'] . "</TD>\n");
	print("</TR>\n");

	print("</TABLE>\n");

	if($current_user['admin'] == 1 && count($unauthorized_users) > 0)
	{
		print("<H3>Users requiring authentication</H3>");

		print("<TABLE>\n");
		print("	<TR>\n");
		print("		<TD>Authorize</TD>\n");
		print("		<TD>Id</TD>\n");
		print("		<TD>Username</TD>\n");
		print("		<TD>Email</TD>\n");
		print("	</TR>\n");

		foreach($unauthorized_userss as $unauthorized_users)
		{
			print("	<TR>\n");
			print("		<TD><A HREF=\"" . $unauthorized_users['url'] . "\">Authorize</A>\n");
			print("		<TD>" . $unauthorized_users['user_id'] . "</TD>\n");
			print("		<TD>" . $unauthorized_users['username'] . "</TD>\n");
			print("		<TD>" . $unauthorized_users['email'] . "</TD>\n");
			print("	</TR>\n");
		}

		print("</TABLE>\n");
	}
	display_footer();
}

function template_display_category_list($current_user, $owner, $categories)
{
	display_header();
	display_user_panel($current_user);

	if($owner != null)
		print("<H2>" . $owner['username'] . "'s Categories</H2>\n");
	else
		print("<H2>All Categories</H2>\n");

	print("<A HREF=\"index.php\">Back to user list</A><BR>\n");
	print("<HR>\n");

	if($current_user['admin'] == '1')
	{
		if($owner['admin'] == '1')
			print("<A HREF=\"admin.php?action=demote&user_id=" . $owner['user_id'] . "\">Demote " . $owner['username'] . "</A><BR>\n");
		else
			print("<A HREF=\"admin.php?action=promote&user_id=" . $owner['user_id'] . "\">Promote " . $owner['username'] . "</A><BR>\n");
	}

	if(count($categories) == 0)
	{
		print("No categories found for " . $owner['username'] . "<BR>\n");
	}
	else
	{
		print("<HR>\n");
		
		print("<TABLE>\n");
		print("	<TR>\n");
		print("		<TD>Date Created</TD>\n");
		print("		<TD>Category</TD>\n");
		print("		<TD>Pictures</TD>\n");
		print("		<TD>Last Updated</TD>\n");
		print("	</TR>\n");
		
			# Display all the categories
		foreach($categories as $category)
		{  
			print("	<TR>\n");
			print("		<TD>" . $category['date_created'] . "</TD>\n");
			print("		<TD><A HREF=\"" . $category['url'] . "\">" . $category['name'] . "</A>\n");
			print("		<TD ALIGN=\"center\">" . $category['num_pictures'] . "</TD>\n");
		
			if($category['last_updated'] == 0)
				print("		<TD><I>Never</I></TD>\n");
			else
				print("		<TD>" . $category['last_updated'] . "</TD>\n");
			print("	</TR>\n");
		}

		print("</TABLE>\n");
	}
	display_footer();
}

function template_display_picture_list($current_user, $owner, $category,  $pictures, $thumbnail_height, $thumbnail_width)
{
	display_user_panel($current_user);
	display_header();

	$thumbnails_per_row = 6;

	print("<H2>Displaying Category <I>" . $category['name'] . "</I> By <I>" . $owner['username'] . "</I> [" . count($pictures) . "]</H2>\n");

	print("<A HREF=\"index.php\">Back to user list</A><BR>\n");
	print("<A HREF=\"show_user.php?user_id=" . $owner['user_id'] . "\">Back to category list</A><BR>\n");
	print("<HR>\n");

	if($current_user['admin'] || $category['user_id'] == $current_user['user_id'])
		print("[<A HREF=\"delete.php?category_id=" . $category['category_id'] . "\">Delete this category</A>]<BR>\n");

	$i = 0;
	$table_height = $thumbnail_height + 50;
	$table_width = $thumbnail_width + 50;

	print("<TABLE BORDER=\"1\">\n");
	print("	<TR>\n");

	foreach($pictures as $picture)
	{
		print("		<TD ALIGN=\"center\" HEIGHT=\"$table_height\" WIDTH=\"$table_width\">\n");
		print("			<A HREF=\"" . $picture['url'] . "\"><IMG SRC=\"" . $picture['tn_url'] . "\"></A><BR>\n");
		print("			<A HREF=\"" . $picture['url'] . "\">" . $picture['title'] . "</A><BR>\n");
		print("			<FONT SIZE=\"-2\">[" . $picture['num_comments'] . " comments]\n");
		print("		</TD>");

		$i++;
		if($i == $thumbnails_per_row)
		{
			print("	</TR>\n");
			print("	<TR>\n");
			$i = 0;
		}
	}

	print("	</TR>\n");
	print("</TABLE>\n");
	display_footer();
}

function template_display_picture($current_user, $owner, $category, $picture, $next_picture, $prev_picture, $comments, $max_height, $max_width)
{
	display_header();
	display_user_panel($current_user);

	print("<H2>Displaying Picture: " . $picture['title'] . "; Posted By: " . $owner['username'] . "</H2>\n");
	print("<A HREF=\"index.php\">Back to user list</A><BR>\n");
	print("<A HREF=\"show_user.php?user_id=" . $owner['user_id'] . "\">Back to category list</A><BR>\n");
	print("<A HREF=\"show_category.php?category_id=" . $category['category_id'] . "\">Back to picture list</A><BR>\n");
	print("<HR>\n");
	print("<TABLE BORDER=\"1\" WIDTH=\"$max_width\">\n");
	print("	<TR>\n");
	print("		<TD ALIGN=\"left\" WIDTH=\"40%\"><A HREF=\"" . $prev_picture['url'] . "\">&lt;-- " . $prev_picture['title']  . "</A></TD>\n");
	print("		<TD ALIGN=\"center\" WIDTH=\"20%\">" . $picture['date_added'] . "</TD>\n");
	print("		<TD ALIGN=\"right\" WIDTH=\"40%\"><A HREF=\"" . $next_picture['url'] . "\">" . $next_picture['title']  . " --&gt;</A></TD>\n");
	print("	</TR>\n");
	print("	<TR>\n");						
	print("		<TD COLSPAN=\"3\">" . $picture['caption'] . "</TD>\n");
	print("	</TR>\n");
	print("	<TR>\n");						
	print("		<TD COLSPAN=\"3\" ALIGN=\"center\" HEIGHT=\"$max_height\">\n");
	print("			<IMG SRC=\"picture.php?picture_id=" . $picture['picture_id'] . "\">\n");
											  
	# If this is an admin, or the picture is owned by this user, give the option to delete
	if($current_user['admin'] == '1' || $current_user['user_id'] == $owner['user_id'])
		print("			<BR>[<A HREF=\"delete.php?picture_id=" . $picture['picture_id'] . "\">Delete</A>]\n");
   
	print("		</TD>\n");
	print("	</TR>\n");

	while($current_result = array_shift($comments))
	{   
		print("	<TR>");
		print("		<TD COLSPAN=\"3\">");
		print("			<B>Comment by</B> " . $current_result['comment_poster']['username'] . " <B>on</B> " . $current_result['date_added'] . "<BR>");
		print("			" . $current_result['text']);
		print("		</TD>");
		print("	</TR>");
	}
	
	if($current_user)
	{   
		# Allow the user to post their own comment
		print("<FORM ACTION=\"post_comment.php\" METHOD=\"POST\">");
		print("<INPUT TYPE=\"hidden\" NAME=\"picture_id\" VALUE=\"" . $picture['picture_id'] . "\">");
		print("	<TR>");
		print("		<TD COLSPAN=\"3\">");
		print("			Post comment:<BR>");
		print("			<TEXTAREA ROWS=\"4\" COLS=\"80\" NAME=\"comment\"></TEXTAREA><BR>");
		print("			<INPUT TYPE=\"submit\" VALUE=\"Post comment\">");
		print("		</TD>");
		print("	</TR>");
		print("</FORM>");
	}

	print("</TABLE>\n");
	display_footer();
}

function template_display_preview($preview_path, $old_filename, $categories, $last_category_id)
{
	display_header();

	print("Preview:<BR>\n");
	print("<HR>\n");
	print("$old_filename:<BR>\n");
	print("<IMG SRC=\"$preview_path\"><BR>\n");
	print("<HR>\n");
	print("<FORM ACTION=\"confirm_upload.php\" METHOD=\"POST\" ENCTYPE=\"multipart/form-data\">\n");

	print("	Category <SELECT NAME=\"category_id\">\n");
	foreach($categories as $category)
	{
		if($last_category_id == $category['category_id'])
			print("		<OPTION VALUE=\"" . $category['category_id'] . "\" SELECTED>" . $category['name'] . "</OPTION>\n");
		else
			print("		<OPTION VALUE=\"" . $category['category_id'] . "\">" . $category['name'] . "</OPTION>\n");
	}
	print("	</SELECT><BR>\n");

    print("	Title <INPUT TYPE=\"text\" NAME=\"title\"><BR>\n");
    print("	Caption<br><TEXTAREA NAME=\"caption\" ROWS=\"5\" COLS=\"50\"></TEXTAREA><BR>\n");
    print("	<INPUT TYPE=\"submit\" VALUE=\"Confirm\"><BR>\n");
    print("</FORM>\n");

	display_footer();
}

# This is the page displayed when an error occurs.  
# $message - the message to display
# $url - the url that is recommended for the next page.  It's up to the template
#  whether to redirect them there after a delay, or to just link them.  If $url is 
#  null, then just display the error.  
function template_error($message, $url)
{
	display_header($url, 3);
	print("<SPAN CLASS=\"error\">Error!</SPAN><BR>");
	print("<SPAN CLASS=\"error\">$message</SPAN><BR>");

	if($url != null)
		print("<A HREF=\"$url\">You will be redirected in 3 seconds...</A><BR>");
	display_footer();
}

# This is the page displayed when a message is shown, usually on a successful change.  
# $message - the message to display
# $url - the url that is recommended for the next page.  It's up to the template
#  whether to redirect them there after a delay, or to just link them.  If $url is 
#  null, then just display the message
function template_message($message, $url)
{
	display_header($url, 3);
	print("$message<BR>");

	if($url != null)
		print("<A HREF=\"$url\">You will be redirected in 3 seconds...</A><BR>");

	display_footer();
}


?>
