<?php
include("config.php");
include("engine/database/connect.php");
include("engine/function/general.php");
include("engine/function/users.php");
include("cesar525/functions.php");
include("cesar525/sceditor/cesar525/engine/functions.php");

//error_reporting(E_ALL ^ E_NOTICE);

if($_SERVER['REQUEST_METHOD'] == "POST"){

//forum variables
	if(isset($_POST['forum'])){
			$forum_id_for_return = "forum=".$_POST['forum'];
			}else{
				$forum_id_for_return = "";
			}
if(isset($_POST['cat_id'])){
	$cat_id_for_return = "&cat=".$_POST['cat_id'];
	}else{
		$cat_id_for_return = "";
	}
	if(isset($_POST['thread_id'])){
		$thread_id_for_return = "&thread=".$_POST['thread_id'];
		}else{
			$thread_id_for_return = "";
		}
	
	
	$link_back = "/forum.php?". $forum_id_for_return. $cat_id_for_return. $thread_id_for_return;
//ADMIN - DELETING AND EDITING BOARDS

if (!empty($_POST)) {
	$admin_post_id = getValue($_POST['admin_post_id']);
	$admin_post_delete = getValue($_POST['admin_post_delete']);

	$admin_category_delete = getValue($_POST['admin_category_delete']);
	$admin_category_edit = getValue($_POST['admin_category_edit']);
	$admin_category_id = getValue($_POST['admin_category_id']);

	$admin_update_category = getValue($_POST['admin_update_category']);
	$admin_category_name = getValue($_POST['admin_category_name']);

	$admin_category_access = getValue($_POST['admin_category_access']);
	$admin_category_closed = getValue($_POST['admin_category_closed']);
	$admin_category_hidden = getValue($_POST['admin_category_hidden']);
	$admin_category_guild_id = getValue($_POST['admin_category_guild_id']);
	$admin_category_comment = getValue($_POST['admin_category_comment']);

	if ($admin_category_access === false) $admin_category_access = 0;
	if ($admin_category_closed === false) $admin_category_closed = 0;
	if ($admin_category_hidden === false) $admin_category_hidden = 0;
	if ($admin_category_guild_id === false) $admin_category_guild_id = 0;

	//////////////////
	// update category
	if ($admin_update_category !== false) {
		$admin_category_id = (int)$admin_category_id;

		// Update the category
		mysql_update("UPDATE `znote_forum` SET
			`name`='$admin_category_name',
			`access`='$admin_category_access',
			`closed`='$admin_category_closed',
			`hidden`='$admin_category_hidden',
			`guild_id`='$admin_category_guild_id',
			`category_comment`='$admin_category_comment'
			WHERE `id`='$admin_category_id' LIMIT 1;");
		header("Location: forum.php?boardupdate=boardupdated");
	}
?>

	<?php

	// delete category
	if ($admin_category_delete !== false) {
		$admin_category_id = (int)$admin_category_id;

		// find all threads in category
		$threads = mysql_select_multi("SELECT `id` FROM `znote_forum_threads` WHERE `forum_id`='$admin_category_id';");

		// Then loop through all threads, and delete all associated posts:
		foreach($threads as $thread) {
			mysql_delete("DELETE FROM `znote_forum_posts` WHERE `thread_id`='". $thread['id'] ."';");
		}
		// Then delete all threads
		mysql_delete("DELETE FROM `znote_forum_threads` WHERE `forum_id`='$admin_category_id';");
		// Then delete the category
		mysql_delete("DELETE FROM `znote_forum` WHERE `id`='$admin_category_id' LIMIT 1;");
		header("Location: forum.php?boarddelete=boarddeleted");
	}

	// delete post
	if ($admin_post_delete !== false) {
		$admin_post_id = (int)$admin_post_id;

		// Delete the post
		mysql_delete("DELETE FROM `znote_forum_posts` WHERE `id`='$admin_post_id' LIMIT 1;");
		header("Location:" . $link_back. '&feedbacks=deletedpost' );
	}
}
//ADMIN - DELETING AND EDITING BOARD END



############################################################################################################


//CREATING CATGORY
//Creating Category section transforming POST into variables
$admin_board_create_name = getValue($_POST['admin_board_create_name']);
$admin_board_create_access = getValue($_POST['admin_board_create_access']);
$admin_board_create_closed = getValue($_POST['admin_board_create_closed']);
$admin_board_create_hidden = getValue($_POST['admin_board_create_hidden']);
$admin_board_create_guild_id = getValue($_POST['admin_board_create_guild_id']);
$admin_board_create_comment = getValue($_POST['admin_board_create_comment']);


if ($admin_board_create_access === false) $admin_board_create_access = 0;
if ($admin_board_create_closed === false) $admin_board_create_closed = 0;
if ($admin_board_create_hidden === false) $admin_board_create_hidden = 0;
if ($admin_board_create_guild_id === false) $admin_board_create_guild_id = 0;

// Create board
if ($admin_board_create_name !== false) {

    // Insert data
    mysql_insert("INSERT INTO `znote_forum` (`name`, `access`, `closed`, `hidden`, `guild_id`, `category_comment`)
        VALUES ('$admin_board_create_name',
            '$admin_board_create_access',
            '$admin_board_create_closed',
            '$admin_board_create_hidden',
            '$admin_board_create_guild_id',
            '$admin_board_create_comment');");
    header("Location: forum.php?creatingboard=boardadded");
}
//END OF CREATING CATEGORY




// POSTING
if(isset($_POST['reply_thread_post'])){
	
	if($_POST['reply_thread_post'] == "start_posting"){
		
	/////////////////////
	// When you are POSTING in an existing thread
	//variables needed
$reply_thread = $_POST['reply_thread']; // good
$reply_cid = $_POST['reply_cid']; // good 
$reply_text = insertTextSQL($_POST['reply_text']);// good
$cat_id = $_POST['cat_id'];


//getting chartacter name
$result_getName = query("SELECT `id`, `name`, `level` FROM players WHERE id='$reply_cid'", $connect);
if($result_getName){
	$row_char = mysqli_fetch_assoc($result_getName);
	$player_name = $row_char['name'];
}
	if ($reply_thread !== false && $reply_text !== false && $reply_cid !== false) {
		$reply_cid = (int)$reply_cid;
	
	
		echo '<br> cat id='.$cat_id; // adding cat id to post to be able to count them
		if (true) {
			
			user_update_znote_account(array('cooldown'=>(time() + $config['forum']['cooldownPost'])));

			$thread = mysql_select_single("SELECT `closed` FROM `znote_forum_threads` WHERE `id`='$reply_thread' LIMIT 1;");

			if ($thread['closed'] == 1 && $admin === false) $access = false;
			else $access = true;
			
			if ($access) {
				$inserting_post = query("INSERT INTO `znote_forum_posts` (`thread_id`, `player_id`, `player_name`, `text`, `created`, `updated`, `forum_id`) VALUES ('$reply_thread', '$reply_cid', '$player_name', '$reply_text', '". time() ."', '". time() ."', '$cat_id');", $connect);
			$post_id ="#post".mysqli_insert_id($connect); 
				header("Location:". $link_back.'&feedbacks=postedpost'. $post_id);
				if ($config['forum']['newPostsBumpThreads']) mysql_update("UPDATE `znote_forum_threads` SET `updated`='". time() ."' WHERE `id`='$reply_thread';");
			} else echo '<p><b><font color="red">You don\'t have permission to post on this thread. [Thread: Closed]</font></b></p>';
		} else {
			?>
    <font class="forumCooldown" color="red">Antispam: You need to wait
        <?php echo ($user_znote_data['cooldown'] - time()); ?> seconds before you can create or post.</font>
    <?php
		}
	}
}
}



}else{
    echo'Ups, Error!';
}


?>