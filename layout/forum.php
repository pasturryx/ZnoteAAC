<!-- //Cesar includes -->

<?php
include("cesar525/cesar525_forum_config.php");
include("cesar525/request_feedback.php");
include("cesar525/functions.php");
include("cesar525/sceditor/cesar525/engine/functions.php");
$link = $_SERVER['REQUEST_URI'];
?>

<!-- cesar includes ends -->

<?php require_once 'engine/init.php'; include 'cesar525/cesar_header.php';
?>
<div>

    <?php
protect_page();
error_reporting(E_ALL ^ E_NOTICE);
$feedback = true;
$post_updated = false;
$stick_thread = false;
$updating_thread = false;
$close_thread = false;
$unstick_thread = false;
$open_thread = false;
$create_board_show = true;
// if(!empty($_GET['feedbacks'])){
// 	request_feedback($_GET['feedbacks']);
// }


if (!$config['forum']['enabled']) admin_only($user_data);

/*  -------------------------------
	---		Znote AAC forum 	---
	-------------------------------
	Created by Znote.
	Version 1.4.

	Changelog (1.0 --> 1.2):
	- Updated to the new date/clock time system
	- Bootstrap design support.

	Changelog (1.2 --> 1.3):
	- Show character outfit as avatar
	- Show in-game position

	Changelog (1.3 -> 1.4):
	- Fix SQL query error when editing Board name.
*/
// BBCODE support:

function TransformToBBCode($string) {
	$tags = array(
		'[center]{$1}[/center]' => '<center>$1</center>',
		'[b]{$1}[/b]' => '<b>$1</b>',
		'[img]{$1}[/img]'    => '<a href="$1" target="_BLANK"><img src="$1" alt="image" style="width: 100%"></a>',
		'[link]{$1}[/link]'    => '<a href="$1">$1</a>',
		'[link={$1}]{$2}[/link]'   => '<a href="$1" target="_BLANK">$2</a>',
		'[url={$1}]{$2}[/url]'   => '<a href="$1" target="_BLANK">$2</a>',
		'[color={$1}]{$2}[/color]' => '<font color="$1">$2</font>',
		'[*]{$1}[/*]' => '<li>$1</li>',
		'[youtube]{$1}[/youtube]' => '<div class="youtube"><div class="aspectratio"><iframe src="//www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe></div></div>',
	);

	foreach ($tags as $tag => $value) {
		$code = preg_replace('/placeholder([0-9]+)/', '(.*?)', preg_quote(preg_replace('/\{\$([0-9]+)\}/', 'placeholder$1', $tag), '/'));
		$string = preg_replace('/'.$code.'/i', $value, $string);
		if (strpos($string, "<a href=") !== false) {
			if (strpos($string, "http") === false) {
				$string = substr_replace($string, "//", 9, 0);
			}
		}
	}

	return $string;
}
Function PlayerHaveAccess($yourChars, $playerName){
	$access = false;
	foreach($yourChars as $char) {
		if ($char['name'] == $playerName) $access = true;
	}
	return $access;
}

// Start page init
$admin = is_admin($user_data);
if ($admin) $yourChars = mysql_select_multi("SELECT `id`, `name`, `group_id` FROM `players` WHERE `level`>='1' AND `account_id`='". $user_data['id'] ."';");
else $yourChars = mysql_select_multi("SELECT `id`, `name`, `group_id` FROM `players` WHERE `level`>='". $config['forum']['level'] ."' AND `account_id`='". $user_data['id'] ."';");
if (!$yourChars) $yourChars = array();
$charCount = count($yourChars);
$yourAccess = accountAccess($user_data['id'], $config['ServerEngine']);
if ($admin) {
	if (!empty($_POST)) {
		$guilds = mysql_select_multi("SELECT `id`, `name` FROM `guilds` ORDER BY `name`;");
		$guilds[] = array('id' => '0', 'name' => 'No guild');
	}
	$yourAccess = 100;
}

// Your characters, indexed by char_id
$charData = array();
foreach ($yourChars as $char) {
	$charData[$char['id']] = $char;
	if (get_character_guild_rank($char['id']) > 0) {
		$guild = get_player_guild_data($char['id']);
		$charData[$char['id']]['guild'] = $guild['guild_id'];
		$charData[$char['id']]['guild_rank'] = $guild['rank_level'];
	} else $charData[$char['id']]['guild'] = '0';
}
$cooldownw = array(
	$user_znote_data['cooldown'],
	time(),
	$user_znote_data['cooldown'] - time()
	);

/////////////////
// Guild Leader & admin
$leader = false;
foreach($charData as $char) {
	if ($char['guild'] > 0 && $char['guild_rank'] == 3) $leader = true;
}
if ($admin && !empty($_POST) || $leader && !empty($_POST)) {
	$admin_thread_delete = getValue($_POST['admin_thread_delete']);
	$admin_thread_close = getValue($_POST['admin_thread_close']);
	$admin_thread_open = getValue($_POST['admin_thread_open']);
	$admin_thread_sticky = getValue($_POST['admin_thread_sticky']);
	$admin_thread_unstick = getValue($_POST['admin_thread_unstick']);
	$admin_thread_id = getValue($_POST['admin_thread_id']);

	// delete thread
	if ($admin_thread_delete !== false) {
		$admin_thread_id = (int)$admin_thread_id;
		$access = false;
		if (!$admin) {
			$thread = mysql_select_single("SELECT `forum_id` FROM `znote_forum_threads` WHERE `id`='$admin_thread_id';");
			$forum = mysql_select_single("SELECT `guild_id` FROM `znote_forum` WHERE `id`='". $thread['forum_id'] ."';");
			foreach($charData as $char) if ($char['guild'] == $forum['guild_id'] && $char['guild_rank'] == 3) $access = true;
		} else $access = true;

		if ($access) {
			// Delete all associated posts
			mysql_delete("DELETE FROM `znote_forum_posts` WHERE `thread_id`='$admin_thread_id';");
			// Delete thread itself
			mysql_delete("DELETE FROM `znote_forum_threads` WHERE `id`='$admin_thread_id' LIMIT 1;");
			echo '<div style="background-color: #0c0c0c;border-radius: 11px;border: solid 1px #353535;padding: 7px;color: red;font-size: 20px;font-family:lato;"><center><font>Thread and all associated posts deleted.</font></center></div>';

		} else echo '<p><b><font color="red">Permission denied.</font></b></p>';
	}

	// Close thread
	
	if ($admin_thread_close !== false) {	
		$admin_thread_id = (int)$admin_thread_id;
		$access = false;
		if (!$admin) {
			$thread = mysql_select_single("SELECT `forum_id` FROM `znote_forum_threads` WHERE `id`='$admin_thread_id';");
			$forum = mysql_select_single("SELECT `guild_id` FROM `znote_forum` WHERE `id`='". $thread['forum_id'] ."';");
			foreach($charData as $char) if ($char['guild'] == $forum['guild_id'] && $char['guild_rank'] == 3) $access = true;
		} else $access = true;
		if ($access) {
			mysql_update("UPDATE `znote_forum_threads` SET `closed`='1' WHERE `id`='$admin_thread_id' LIMIT 1;");
			//die("UPDATE `znote_forum_threads` SET `closed`='1' WHERE `id`='$admin_thread_id' LIMIT 1;");
			//echo '<h1>Thread has been closed.</h1>';
			$close_thread = true;
			
		} else echo '<p><b><font color="red">Permission denied.</font></b></p>';
	}

	// open thread
	if ($admin_thread_open !== false) {
		$admin_thread_id = (int)$admin_thread_id;
		$access = false;
		if (!$admin) {
			$thread = mysql_select_single("SELECT `forum_id` FROM `znote_forum_threads` WHERE `id`='$admin_thread_id';");
			$forum = mysql_select_single("SELECT `guild_id` FROM `znote_forum` WHERE `id`='". $thread['forum_id'] ."';");
			foreach($charData as $char) if ($char['guild'] == $forum['guild_id'] && $char['guild_rank'] == 3) $access = true;
		} else $access = true;
		if ($access) {
			mysql_update("UPDATE `znote_forum_threads` SET `closed`='0' WHERE `id`='$admin_thread_id' LIMIT 1;");
			//echo '<h1>Thread has been opened.</h1>';
			$open_thread = true;
		} else echo '<p><b><font color="red">Permission denied.</font></b></p>';
	}

	// stick thread
	if ($admin_thread_sticky !== false) {
		$admin_thread_id = (int)$admin_thread_id;
		$access = false;
		if (!$admin) {
			$thread = mysql_select_single("SELECT `forum_id` FROM `znote_forum_threads` WHERE `id`='$admin_thread_id';");
			$forum = mysql_select_single("SELECT `guild_id` FROM `znote_forum` WHERE `id`='". $thread['forum_id'] ."';");
			foreach($charData as $char) if ($char['guild'] == $forum['guild_id'] && $char['guild_rank'] == 3) $access = true;
		} else $access = true;
		if ($access) {
			mysql_update("UPDATE `znote_forum_threads` SET `sticky`='1' WHERE `id`='$admin_thread_id' LIMIT 1;");
			//echo '<h1>Thread has been sticked.</h1>';
			$stick_thread = true;
		} else echo '<p><b><font color="red">Permission denied.</font></b></p>';
	}

	// unstick thread
	if ($admin_thread_unstick !== false) {
		$admin_thread_id = (int)$admin_thread_id;
		$access = false;
		if (!$admin) {
			$thread = mysql_select_single("SELECT `forum_id` FROM `znote_forum_threads` WHERE `id`='$admin_thread_id';");
			$forum = mysql_select_single("SELECT `guild_id` FROM `znote_forum` WHERE `id`='". $thread['forum_id'] ."';");
			foreach($charData as $char) if ($char['guild'] == $forum['guild_id'] && $char['guild_rank'] == 3) $access = true;
		} else $access = true;
		if ($access) {
			mysql_update("UPDATE `znote_forum_threads` SET `sticky`='0' WHERE `id`='$admin_thread_id' LIMIT 1;");
			$unstick_thread = true;
			// echo '<h1>Thread has been unsticked.</h1>';
		} else echo '<p><b><font color="red">Permission denied.</font></b></p>';
	}
}

/////////////////
// ADMIN FUNCT

// End admin function

// Fetching get values
if (!empty($_GET)) {
	
	$getCat = getValue($_GET['cat']);
	$getForum = getValue($_GET['forum']);
	$getThread = getValue($_GET['thread']);

	$new_thread_category = getValue($_POST['new_thread_category']);
	$new_thread_cid = getValue($_POST['new_thread_cid']);

	$create_thread_cid = getValue($_POST['create_thread_cid']);
	$create_thread_title = getValue($_POST['create_thread_title']);
	$create_thread_text = insertTextSQL($_POST['create_thread_text']);//getValue($_POST['create_thread_text']);
	$create_thread_category = getValue($_POST['create_thread_category']);

	$update_thread_id = getValue($_POST['update_thread_id']);
	$update_thread_title = getValue($_POST['update_thread_title']);
	$update_thread_text = insertTextSQL($_POST['update_thread_text']); //getValue($_POST['update_thread_text']);

	$edit_thread = getValue($_POST['edit_thread']);
	$edit_thread_id = getValue($_POST['edit_thread_id']);

	$reply_thread = getValue($_POST['reply_thread']);
	$reply_text = insertTextSQL($_POST['reply_text']);//getValue($_POST['reply_text']);
	$reply_cid = getValue($_POST['reply_cid']);

	$edit_post = getValue($_POST['edit_post']);
	$edit_post_id = getValue($_POST['edit_post_id']);

	$update_post_id = getValue($_POST['update_post_id']);
	$update_post_text =  insertTextSQL($_POST['update_post_text']); //getValue($_POST['update_post_text']);

	/////////////////////


	/////////////////////
	// When you ARE creating new thread
	if ($create_thread_cid !== false && $create_thread_title !== false && $create_thread_text !== false && $create_thread_category !== false) {
		if ($user_znote_data['cooldown'] < time()) {
			user_update_znote_account(array('cooldown'=>(time() + $config['forum']['cooldownCreate'])));

			$category = mysql_select_single("SELECT `access`, `closed`, `guild_id` FROM `znote_forum` WHERE `id`='$create_thread_category' LIMIT 1;");
			if ($category !== false) {
				$access = true;
				if (!$admin) {
					if ($category['access'] > $yourAccess) $access = false;
					if ($category['guild_id'] > 0) {
						$status = false;
						foreach($charData as $char) {
							if ($char['guild'] == $category['guild_id']) $status = true;
						}
						if (!$status) $access = false;
					}
					if ($category['closed'] > 0) $access = false;
				}

				if ($access) {
					mysql_insert("INSERT INTO `znote_forum_threads`
						(`forum_id`, `player_id`, `player_name`, `title`, `text`, `created`, `updated`, `sticky`, `hidden`, `closed`)
						VALUES (
							'$create_thread_category',
							'$create_thread_cid',
							'". $charData[$create_thread_cid]['name'] ."',
							'$create_thread_title',
							'$create_thread_text',
							'". time() ."',
							'". time() ."',
							'0', '0', '0');");
					SendGet(array('cat'=>$create_thread_category), 'forum.php');
				} else echo '<p><b><font color="red">Permission to create thread denied.</font></b></p>';
			} else echo 'Category does not exist.';
		} else {
			?>
    <font class="forumCooldown" color="red">Antispam: You need to wait
        <?php echo ($user_znote_data['cooldown'] - time()); ?> seconds before you can create or post.</font>
    <?php
		}
	}

	/////////////////////
	// When you ARE updating post
	if ($update_post_id !== false && $update_post_text !== false) {
		// Fetch the post data
		$post = mysql_select_single("SELECT `id`, `player_name`, `text`, `thread_id` FROM `znote_forum_posts` WHERE `id`='$update_post_id' LIMIT 1;");
		$thread = mysql_select_single("SELECT `closed` FROM `znote_forum_threads` WHERE `id`='". $post['thread_id'] ."' LIMIT 1;");

		// Verify access
		$access = PlayerHaveAccess($yourChars, $post['player_name']);
		if ($thread !== false && $thread['closed'] == 1 && $admin === false) $access = false;
		if ($admin) $access = true;
		//if ($thread === false) $access = false;

		if ($access) {
			mysql_update("UPDATE `znote_forum_posts` SET `text`='$update_post_text', `updated`='". time() ."' WHERE `id`='$update_post_id';");
			//echo '<div style="background-color: #0c0c0c;border-radius: 11px;border: solid 1px #353535;padding: 7px;color: green;font-size: 20px;"><center><font>Post has been updated.</font></center></div>';
$feedback = false;
$post_updated = true;
		} else echo "<p><font color='red'>Your permission to edit this post has been denied.</font></p>";
	}

	/////////////////////
	// When you ARE updating thread
	if ($update_thread_id !== false && $update_thread_title !== false && $update_thread_text !== false) {
		// Fetch the thread data
		$thread = mysql_select_single("SELECT `id`, `player_name`, `title`, `text`, `closed` FROM `znote_forum_threads` WHERE `id`='$update_thread_id' LIMIT 1;");

		// Verify access
		$access = PlayerHaveAccess($yourChars, $thread['player_name']);
		if ($thread['closed'] == 1 && $admin === false) $access = false;
		if ($admin) $access = true;

		if ($access) {
			mysql_update("UPDATE `znote_forum_threads` SET `title`='$update_thread_title', `text`='$update_thread_text' WHERE `id`='$update_thread_id';");
		$feedback = false;
		$updating_thread = true;
		} else echo "<p><font color='red'>Your permission to edit this thread has been denied.</font></p>";
	}
 
	/////////////////////
	// When you want to edit a post
	if ($edit_post_id !== false && $edit_post !== false) {
		// Fetch the post data
		$post = mysql_select_single("SELECT `id`, `thread_id`, `text`, `player_name` FROM `znote_forum_posts` WHERE `id`='$edit_post_id' LIMIT 1;");
		$thread = mysql_select_single("SELECT `closed` FROM `znote_forum_threads` WHERE `id`='". $post['thread_id'] ."' LIMIT 1;");
		// Verify access
		$access = PlayerHaveAccess($yourChars, $post['player_name']);
		if ($thread['closed'] == 1 && $admin === false) $access = false;
		if ($admin) $access = true;

		if ($access) {
			?>
			  
				
	<div style="background-color:black;border-radius: 31px;border: solid 1px #464646;">
    <div style="padding: 11px;">
            <font style="font-size: 31px;font-family: gideon;padding-left: 12px;color: #d8d8d8;">Edit Post</font>
        </div>
    <form type="" method="post">
        <input name="update_post_id" type="hidden" value="<?php echo $post['id']; ?>">
		
		<div style="vertical-align:top;width:95%; height:308px;background-color:black;">
		<textarea id="texteditor_edit_post" name="update_post_text"
            style="width: 100%; height: 308px"><?php echo $post['text']; ?></textarea>
		</div>
		
			<center><input class="cesar-submit-button" type="submit" value="Update Post" ></center>
    </form>
		</div>
	<script>
        var textarea = document.getElementById("texteditor_edit_post");

        sceditor.create(textarea, {
            format: "xhtml",
            icons: "monocons",
            style: "cesar525/sceditor/minified/themes/content/default.min.css"
        });
        var themeInput = document.getElementById("theme");
        themeInput.onchange = function() {
            var theme = "cesar525/sceditor/minified/themes/" + themeInput.value + ".min.css";
        };
        </script>

    <?php
		} else echo '<p><b><font color="red">You don\'t have permission to edit this post.</font></b></p>';
	} else

	/////////////////////
	// When you want to edit a thread
	if ($edit_thread_id !== false && $edit_thread !== false) {
		// Fetch the thread data
		$thread = mysql_select_single("SELECT `id`, `title`, `text`, `player_name`, `closed` FROM `znote_forum_threads` WHERE `id`='$edit_thread_id' LIMIT 1;");

		$access = PlayerHaveAccess($yourChars, $thread['player_name']);
		if ($thread['closed'] == 1) $access = false;
		if ($admin) $access = true;

		if ($access) {
			?>
    <div style="background-color:black;border:solid 1px gray;border-radius:20px;">
        <div style="padding: 11px;">
            <font style="font-size: 31px;font-family: gideon;color: #d8d8d8;">Edit Thread</font>
        </div>
        <form type="" method="post">
            <input name="update_thread_id" type="hidden" value="<?php echo $thread['id']; ?>">

            <font style="font-size: 21px;padding-left: 11px;color: #d8d8d8; ">Subject:</font><input
                style="margin-left: 11px;padding:5px;width: 500px;font-size: 18px;background-color: #0d0d0d;border: solid 1px gray;border-radius: 8px;color: white;"
                name="update_thread_title" type="text" value="<?php echo $thread['title']; ?>" style="width: 500px;" required>

            <br><br>
            <div style="border-radius: 11px;vertical-align:top;width:96%; height:308px;background-color:black;">
                <textarea id="texteditor_edit_post" name="update_thread_text"
                    style="width: 100%;height:308px;"><?php echo $thread['text']; ?></textarea>
            </div>
            <br>
            <center>
                <input class="cesar-submit-button" type="submit" value="Update Thread" class="btn btn-success">
            </center>
        </form>
    </div>
    <script>
    var textarea = document.getElementById("texteditor_edit_post");

    sceditor.create(textarea, {
        format: "xhtml",
        icons: "monocons",
        style: "cesar525/sceditor/minified/themes/content/default.min.css"
    });
    var themeInput = document.getElementById("theme");
    themeInput.onchange = function() {
        var theme = "cesar525/sceditor/minified/themes/" + themeInput.value + ".min.css";
    };
    </script>

    <?php
		} else echo '<p><b><font color="red">Edit access denied.</font></b></p>';
	} else

	/////////////////////
	// When you want to view a thread
	if ($getThread !== false) {
		$getThread = (int)$getThread;
		$threadData = mysql_select_single("SELECT `id`, `forum_id`, `player_id`, `player_name`, `title`, `text`, `created`, `updated`, `sticky`, `hidden`, `closed` FROM `znote_forum_threads` WHERE `id`='$getThread' LIMIT 1;");

		if ($threadData !== false) {

			$category = mysql_select_single("SELECT `hidden`, `access`, `guild_id` FROM `znote_forum` WHERE `id`='". $threadData['forum_id'] ."' LIMIT 1;");
			if ($category === false) die("Thread category does not exist.");

			$access = true;
			$leader = false;
			if ($category['hidden'] == 1 || $category['access'] > 1 || $category['guild_id'] > 0) {
				$access = false;
				if ($category['hidden'] == 1) $access = PlayerHaveAccess($yourChars, $threadData['player_name']);
				if ($category['access'] > 1 && $yourAccess >= $category['access']) $access = true;
				foreach($charData as $char) {
					if ($category['guild_id'] == $char['guild']) $access = true;
					if ($char['guild_rank'] == 3) $leader = true;
				}
				if ($admin) $access = true;
			}


			if ($access) {
				$threadPlayer = ($config['forum']['outfit_avatars'] || $config['forum']['player_position']) ? mysql_select_single("SELECT `id`, `group_id`, `sex`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `lookaddons` FROM `players` WHERE `id`='".$threadData['player_id']."';") : false;
				?>
    <div
        style="font-family: 'Martel', serif;background-color: #121111;padding: 4px;padding-left: 9px;border: solid 1px #4a4949;border-radius: 9px;margin-bottom: 6px;">
        <font style="color:orange;">LinkMap:</font>
        <font><a class="link-map" href="forum.php">Forum</a> <font color="white">-</font> <a class="link-map" href="?cat=<?php echo $getCat; ?>"><?php echo $getForum; ?></a></font>
        <br>
        <div><center>
            <font size="5" id="ThreadTitle">
                <font style="color:orange;">Viewing thread:</font>
                <?php echo "<a class='link-map' href='?forum=". $getForum ."&cat=". $getCat ."&thread=". $threadData['id'] ."'>". $threadData['title'] ."</a>"; ?>
            </font></center>
        </div>
    </div>
    <table class="cesar-forum-background-table" style="margin-bottom:11px;">
        <tr class="yellow" style="background-color: #ff000000;">
            <th style="color:orange;" <?php if ($threadPlayer !== false) echo ' colspan="2"'; ?>>
                <?php
							echo getClock($threadData['created'], true);
							if ($threadPlayer === false): ?>
                - Created by:
                <?php
						 		echo "<a href='characterprofile.php?name=". $threadData['player_name'] ."'>". $threadData['player_name'] ."</a>";
					 		endif;
					 		?>
            </th>
        </tr>
        <tr>
            <?php if ($threadPlayer !== false): ?>
            <td class="cesar-avatar cesar-data-forum-table" style="vertical-align:top;padding: 11px;">
                <!-- topping -->
                <div style="word-break: break-all;">
                    <a class="post-player-name"
                        href='characterprofile.php?name=<?php echo $threadData['player_name']; ?>'><?php echo $threadData['player_name']; ?></a>
                    <?php if ($config['forum']['outfit_avatars']): ?>
                    <br>
                    <?php if($forum_config_cesar["set_avatar_for_all"]["enabling"] == true){
				echo'<img style="margin-top: 6px;border: solid 1px #37363659;width: 118px;height: 118px;"  src="'.$forum_config_cesar["set_avatar_for_all"]["image_for_all_path"].'" alt="no image">';
			}else{
				?>

                    <img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo $threadPlayer['looktype']; ?>&addons=<?php echo $threadPlayer['lookaddons']; ?>&head=<?php echo $threadPlayer['lookhead']; ?>&body=<?php echo $threadPlayer['lookbody']; ?>&legs=<?php echo $threadPlayer['looklegs']; ?>&feet=<?php echo $threadPlayer['lookfeet']; ?>"
                        alt="img">
                    <?php  }?>

                    <?php endif; ?>
                    <?php if ($config['forum']['player_position']): ?>
                    <br>
                    <center style="margin-top: 21px;margin-bottom: 11px;"><span
                            style="background-color: #1a1919;padding: 6px;border-radius: 20px;font-size: 15px;"><?php echo group_id_to_name($threadPlayer['group_id']); ?>
                    </center></span>
                    <?php endif; ?>
            </td>
</div>
<?php endif; ?>
<td class="cesar-data-forum-table" style="vertical-align:top;" style="v">
    <div
        style="word-break: break-all;font-size: <?php echo $forum_config_cesar['font_size_default'];?>;vertical-align: top;padding:9px;">
        <font><?php echo nl2br(TransformToBBCode($threadData['text'])); ?></font>
    </div>
</td>
</tr>
</table>
<!-- <hr class="bighr"> -->
<?php
				if ($admin || $leader) {
					// PlayerHaveAccess($yourChars, $thread['player_name']) ||
					// $yourChars
					?>
<div class="cesar-forum-background-table"
    style="border: 1px solid #900;border-radius: 0px;padding-top: 9px;padding-bottom: 0px;margin-top:-11px;width:98%;">

    <center style="margin-bottom: -6px;">
        <form action="" method="post" style="display: inline-block;">
            <input type="hidden" name="admin_thread_id" value="<?php echo $threadData['id']; ?>">
            <input class="cesar-button-delete" style="width: 101px;" type="submit" name="admin_thread_delete"
                value="Delete Thread" class="btn btn-danger">
        </form>

        <?php if ($threadData['closed'] == 0) { ?>
        <form action="" method="post" style="display: inline-block;">
            <input type="hidden" name="admin_thread_id" value="<?php echo $threadData['id']; ?>">
            <input class="cesar-button-delete" style="width: 101px;" type="submit" name="admin_thread_close"
                value="Close Thread" class="btn btn-warning">
        </form>
        <?php } else { ?>
        <form action="" method="post" style="display: inline-block;">
            <input type="hidden" name="admin_thread_id" value="<?php echo $threadData['id']; ?>">
            <input class="cesar-button" type="submit" style="width: 101px;" name="admin_thread_open" value="Open Thread"
                class="btn btn-success">
        </form>
        <?php } ?>

        <?php if ($threadData['sticky'] == 0) { ?>
        <form action="" method="post" style="display: inline-block;">
            <input type="hidden" name="admin_thread_id" value="<?php echo $threadData['id']; ?>">
            <input class="cesar-button" type="submit" style="width: 101px;" name="admin_thread_sticky"
                value="Stick thread" class="btn btn-info">
        </form>
        <?php } else { ?>
        <form action="" method="post" style="display: inline-block;">
            <input type="hidden" name="admin_thread_id" value="<?php echo $threadData['id']; ?>">
            <input class="cesar-button" type="submit" style="width: 101px;" name="admin_thread_unstick"
                value="Unstick thread" class="btn btn-primary">
        </form>
        <?php } ?>

        <form action="" method="post" style="display: inline-block;">
            <input type="hidden" name="edit_thread_id" value="<?php echo $threadData['id']; ?>">
            <input class="cesar-button" type="submit" name="edit_thread" style="width: 101px;" value="Edit Thread"
                class="btn btn-warning">
        </form>
    </center>
</div><br>
<?php
				} else {
					if ($threadData['closed'] == 0 && PlayerHaveAccess($yourChars, $threadData['player_name'])) {
						?>
<!-- editing thread form -->
<div class="cesar-forum-background-table"
style="border: 1px solid #900;border-radius: 0px;padding-top: 9px;padding-bottom: 0px;width: 98%;margin-top:-11px;">
    <center style="margin-bottom: -6px;">

        <form action="" method="post">
            <input type="hidden" name="edit_thread_id" value="<?php echo $threadData['id']; ?>">
            <input class="cesar-button" style="width: 101px;" type="submit" name="edit_thread" value="Edit Thread">
        </form>

    </center>
</div>
<br>
<?php
					}
				}
				?>
<?php
				// Display replies... (copy table above and edit each post)
				$posts = mysql_select_multi("SELECT `id`, `player_id`, `player_name`, `text`, `created`, `updated` FROM `znote_forum_posts` WHERE `thread_id`='". $threadData['id'] ."' ORDER BY `created`;");
				if ($posts !== false) {
					// Load extra data (like outfit avatars?)
					$players = array();
					$extra = false;
					if ($config['forum']['outfit_avatars'] || $config['forum']['player_position']) {
						$extra = true;

						foreach($posts as $post)
							if (!isset($players[$post['player_id']]))
								$players[$post['player_id']] = array();

						$sql_players = mysql_select_multi("SELECT `id`, `group_id`, `sex`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `lookaddons` FROM `players` WHERE `id` IN (".implode(',', array_keys($players)).");");

						foreach ($sql_players as $player)
							$players[$player['id']] = $player;

					}

					foreach($posts as $post) {
						?>
<table id="post<?php echo $post['id'];?>" class="cesar-forum-background-table" style="margin-bottom: 11px;">
    <tr class="yellow" style="background-color: #ff000000;">
        <th style="color:orange;" <?php if ($extra) echo ' colspan="2"'; ?>>
            <?php echo getClock($post['created'], true);
									if (!$extra): ?>
            - Posted by:
            <?php echo "<a href='characterprofile.php?name=". $post['player_name'] ."'>". $post['player_name'] ."</a>";
									 endif; ?>
        </th>
    </tr>
    <tr>
        <?php if ($extra): ?>
        <td class="cesar-avatar cesar-data-forum-table" style="vertical-align:top;padding: 11px;">
            <!-- bottomm -->
            <div style="word-break: break-all;">
                <a class="post-player-name"
                    href='characterprofile.php?name=<?php echo $post['player_name']; ?>'><?php echo $post['player_name']; ?></a>
                <?php if ($config['forum']['outfit_avatars']): ?>
                <br>



                <?php if($forum_config_cesar["set_avatar_for_all"]["enabling"] == true){
				echo'<center><img style="margin-top: 6px;border: solid 1px #37363659;width: 118px;height: 118px;"  src="'.$forum_config_cesar["set_avatar_for_all"]["image_for_all_path"].'" alt="no image"></center>';
			}else{
				?>
                <img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo $players[$post['player_id']]['looktype']; ?>&addons=<?php echo $players[$post['player_id']]['lookaddons']; ?>&head=<?php echo $players[$post['player_id']]['lookhead']; ?>&body=<?php echo $players[$post['player_id']]['lookbody']; ?>&legs=<?php echo $players[$post['player_id']]['looklegs']; ?>&feet=<?php echo $players[$post['player_id']]['lookfeet']; ?>"
                    alt="img">
                <?php  }?>








                <?php endif; ?>
                <?php if ($config['forum']['player_position']): ?>
                <center style="margin-top: 21px;margin-bottom: 11px;"><span
                        style="background-color: #1a1919;padding: 6px;border-radius: 20px;font-size: 15px;"><?php echo group_id_to_name($players[$post['player_id']]['group_id']); ?>
					</center></span>
                <?php endif; ?>
            </div>
        </td>
        <?php endif; ?>
        <td class="cesar-data-forum-table" style="font-size: 14px;vertical-align: top;">
            <div style=" word-break: break-word;padding: 9px;font-size:<?php echo $forum_config_cesar['font_size_default'];?>"><?php echo $post['text']; ?></div>
        </td>
    </tr>
</table>

<?php
						if (PlayerHaveAccess($yourChars, $post['player_name']) || $admin) {
							echo'<div style="background-color: black;padding: 11px;padding-bottom: 1px;border-radius: 0px;border: solid 1px #6a4600;margin-bottom: 11px;margin-top:-11px;">
<center style="margin-bottom: -6px;">';
						if ($admin) {
								?>
<form action="forum_cesar_api.php" method="post" style="display: inline-block;">
    <input type="hidden" name="admin_post_id" value="<?php echo $post['id']; ?>">
    <input class="cesar-button-delete" style="width: 101px;" type="submit" name="admin_post_delete" value="Delete Post">
	<input type="hidden" name="forum" value="<?php echo $_GET['forum']; ?>">
	<input type="hidden" name="thread_id" value="<?php echo $_GET['thread']; ?>">
	<input type="hidden" name="cat_id" value="<?php echo $_GET['cat']; ?>">
	

	
	<input type="hidden" name="admin_post_id" value="<?php echo $post['id']; ?>">

</form>

<?php
							}
							if ($threadData['closed'] == 0 || $admin) {
									?>
<form action="" method="post" style="display: inline-block;">
    <input type="hidden" name="edit_post_id" value="<?php echo $post['id']; ?>">
    <input class="cesar-button" style="width: 101px;" type="submit" name="edit_post" value="Edit Post">
</form>

<?php
										echo'</center>
</div>';}}
		
		
					}
				}
				if($open_thread || $post_updated || $stick_thread || $updating_thread || $unstick_thread || $close_thread){}else{
if(!empty($_GET['feedbacks'])){

	if($feedback){
		request_feedback($_GET['feedbacks']);
	}
	}}
if($post_updated){
	echo '<div style="background-color: #0c0c0c;border-radius: 11px;border: solid 1px #353535;padding: 7px;color: green;font-size: 20px;font-family:lato;"><center><font>Post has been updated.</font></center></div>';
}
if($stick_thread){
	echo '<div style="background-color: #0c0c0c;border-radius: 11px;border: solid 1px #353535;padding: 7px;color: green;font-size: 20px;font-family:lato;"><center><font>Thread has been sticked.</font></center></div>';
}
if($updating_thread){
	echo '<div style="background-color: #0c0c0c;border-radius: 11px;border: solid 1px #353535;padding: 7px;color: green;font-size: 20px;font-family:lato;"><center><font>Thread has been updated.</font></center></div>';
}
if($unstick_thread){
	echo '<div style="background-color: #0c0c0c;border-radius: 11px;border: solid 1px #353535;padding: 7px;color: green;font-size: 20px;font-family:lato;"><center><font>Thread has been unsticked.</font></center></div>';
}
if($close_thread){
	echo '<div style="background-color: #0c0c0c;border-radius: 11px;border: solid 1px #353535;padding: 7px;color: red;font-size: 20px;font-family:lato;"><center><font>Thread has been closed.</font></center></div>';
}
if($open_thread){
	echo '<div style="background-color: #0c0c0c;border-radius: 11px;border: solid 1px #353535;padding: 7px;color: green;font-size: 20px;font-family:lato;"><center><font>Thread has been opened.</font></center></div>';
}

				// Quick Reply
				if ($charCount > 0) {
					if ($threadData['closed'] == 0 || $yourAccess > 3) {
						?>

						<?php
$cat_id = $_GET['cat'];

						?>
<form action="forum_cesar_api.php" method="post">

    <input name="reply_thread" type="hidden" value="<?php echo $threadData['id']; ?>"><br>
    <input name="cat_id" type="hidden" value="<?php echo $cat_id; ?>"><br>
    <input name="reply_thread_post" type="hidden" value="start_posting"><br>
	<input type="hidden" name="forum" value="<?php echo $_GET['forum']; ?>">
	<input type="hidden" name="cat_id" value="<?php echo $_GET['cat']; ?>">
	<input type="hidden" name="thread_id" value="<?php echo $_GET['thread']; ?>">





    <div style="background-color:black;border-radius: 31px;border: solid 1px #464646;margin-top: -55px;">

        <div style="width:30%;">
            <font style="font-size:19px;color: #d8d8d8;padding: 11px;">Select Player:</font><br><br>
            <select style="margin-left: 11px;padding:5px;width: 500px;font-size: 18px;background-color: #0d0d0d;border: solid 1px gray;border-radius: 8px;color: white;"
 name="reply_cid">
                <?php
								foreach($yourChars as $char) {
									echo "<option value='". $char['id'] ."'>". $char['name'] ."</option>";
								}
								?>
            </select>
        </div><br>

        <div style="vertical-align:top;width:95%; height:308px;background-color:black;">
            <textarea id="texteditor" style="width:100%; height:100%;" name="reply_text">
	</textarea>
        </div><br>

        <script>
        var textarea = document.getElementById("texteditor");

        sceditor.create(textarea, {
            format: "xhtml",
            icons: "monocons",
            style: "cesar525/sceditor/minified/themes/content/default.min.css"
        });
        var themeInput = document.getElementById("theme");
        themeInput.onchange = function() {
            var theme = "cesar525/sceditor/minified/themes/" + themeInput.value + ".min.css";
        };
        </script>

        <center> <input class="cesar-submit-button" name="" type="submit" value="Post Reply" class="btn btn-primary">
        </center>
    </div>
</form>
<?php
					} else echo '<div style="background-color: black;padding: 1px;border-radius: 18px;border: solid 1px #5d5c5c;"><center style="color:red;"><p><b>You don\'t have permission to post on this thread. [Thread: Closed]</b></p></center></div>';
				} else {
					?>
					<div style="background-color: #0c0c0c;border-radius: 11px;border: solid 1px #353535;padding: 7px;color: #ed3b3bf7;font-size: 20px;"><center>You must have a character on your account that is level&nbsp;<?php echo $config['forum']['level']; ?>+ to reply to
    this thread.</center></div>
	
	<?php
				}
			} else echo "<p><font color='red'>Your permission to access this thread has been denied.</font></p>";
		} else {
			?>
			
			<center><div style="background-color: #0c0c0c;border-radius: 11px;border: solid 1px #353535;padding: 7px;color: orange;font-size: 20px;">
<font>-Thread unavailable-</font><br>
<font>Thread is unavailable for you, or do not exist any more.
    <?php
				if ($_GET['cat'] > 0 && !empty($_GET['forum'])) {
					$tmpCat = getValue($_GET['cat']);
					$tmpCatName = getValue($_GET['forum']);
					?>
		<br><a class="slide-click" style="text-decoration:none;" href="forum.php?forum=<?php echo $tmpCatName; ?>&cat=<?php echo $tmpCat; ?>">Go back to:
        <?php echo $tmpCatName; ?></a>
	</font></div></center>
<?php
				} else {
					?>
<br><a href="forum.php">Go back to Forum</a></p>
<?php
				}
				?>

<?php
		}

	} else

	/////////////////////
	// When you want to create a new thread
	if ($new_thread_category !== false && $new_thread_cid !== false) {
		// Verify we got access to this category
		$category = mysql_select_single("SELECT `access`, `closed`, `guild_id` FROM `znote_forum` WHERE `id`='$new_thread_category' LIMIT 1;");
		if ($category !== false) {
			$access = true;
			if (!$admin) {
				if ($category['access'] > $yourAccess) $access = false;
				if ($category['guild_id'] > 0) {
					$status = false;
					foreach($charData as $char) {
						if ($char['guild'] == $category['guild_id']) $status = true;
					}
					if (!$status) $access = false;
				}
				if ($category['closed'] > 0) $access = false;
			}

			if ($access) {
				?>
				    <div style="background-color:black;border:solid 1px gray;border-radius:20px;">
					<div style="padding: 11px;">
            <font style="font-size: 31px;font-family: gideon;color:#d8d8d8;">Create new thread</font>
        </div>

<form type="" method="post">
   <div> 
	<font style="font-size: 21px;color:#d8d8d8;"> Character Selected:&nbsp;</font><input style="width: 100px;background-color: black;padding: 5px;border: solid 1px gray;border-radius: 8px;color: gray;" type="text" disabled value="<?php echo $charData[$new_thread_cid]['name']; ?>" style="width: 100px;">
     </div>
	<input name="create_thread_cid" type="hidden" value="<?php echo $new_thread_cid; ?>">
    
	 <input  name="create_thread_category" type="hidden" value="<?php echo $new_thread_category; ?>">
	 <div>
	<font style="font-size: 21px;color:#d8d8d8;">Subject:&nbsp;</font><input style="margin-left: 11px;padding:5px;width: 500px;font-size: 18px;background-color: #0d0d0d;border: solid 1px gray;border-radius: 8px;color: white;" name="create_thread_title" type="text" placeholder="Thread title" style="width: 500px;" required><br>
			</div>
	<div style="border-radius: 11px;vertical-align:top;width:96%; height:308px;background-color:black;">
	<textarea id="texteditor_create_thread" name="create_thread_text" style="width: 100%; height: 308px" placeholder="Thread text"></textarea><br>
    </div>
	
	<center><input class="cesar-submit-button" type="submit" value="Create Thread" ></center>
</form>
</div>
<script>
        var textarea = document.getElementById("texteditor_create_thread");

        sceditor.create(textarea, {
            format: "xhtml",
            icons: "monocons",
            style: "cesar525/sceditor/minified/themes/content/default.min.css"
        });
        var themeInput = document.getElementById("theme");
        themeInput.onchange = function() {
            var theme = "cesar525/sceditor/minified/themes/" + themeInput.value + ".min.css";
        };
        </script>



<?php
			} else echo '<p><b><font color="red">Permission to create thread denied.</font></b></p>';
		}
	} else

	/////////////////////
	// When category is specified// which means here are all the threads of the specify cat
	if ($getCat !== false) {
		$getCat = (int)$getCat;

		// Fetch category rules
		$category = mysql_select_single("SELECT `name`, `access`, `closed`, `hidden`, `guild_id`, `category_comment` FROM `znote_forum` WHERE `id`='$getCat' AND `access`<='$yourAccess' LIMIT 1;");

		if ($category !== false && $category['guild_id'] > 0 && !$admin) {
			$access = false;
			foreach($charData as $char) if ($category['guild_id'] == $char['guild']) $access = true;
			if ($access !== true) $category = false;
		}

		if ($category !== false) {
			// TODO : Verify guild access
			//foreach($charData)
//STARTTT


			echo "<div style='background-color: #131313;padding: 4px;padding-left: 9px;border: solid 1px #4a4949;border-radius: 9px;'><font color='white'>Link Map: &nbsp;</font><font><a class='link-map' href='forum.php'>Forum</a> <font color='orange'>Board:</font> <font color='yellow'>". $category['name'] ."</font><</font></div>";
?>





<?php
			// Threads
			//  - id - forum_id - player_id - player_name - title - text - created - updated - sticky - hidden - closed
			$threads = mysql_select_multi("SELECT `id`, `player_name`, `title`, `sticky`, `closed`, `created` FROM `znote_forum_threads` WHERE `forum_id`='$getCat' ORDER BY `sticky` DESC, `updated` DESC;");

			///// HTML \\\\\
			if ($threads !== false) {
				//threads table
				?>

<table class="cesar-forum-background-table" style="margin-top: 11px;" id="forumThreadTable">
    <tr class="yellow" style="background-color: #ff000000;">
        <th class="cesar-table-headers" width="6%">img</th>
        <th class="cesar-table-headers" width="70%" colspan="2">Topic</th>
        <th class="cesar-table-headers" width="20%">Posted by</th>
    </tr>
    <?php
					foreach($threads as $thread) {
						$access = true;
						if ($category['hidden'] == 1) {
							if (!$admin) $access = false;
							$access = PlayerHaveAccess($yourChars, $thread['player_name']);
							if ($yourAccess > 3) $access = true;
						}

						if ($access) {
							?>
    <tr class="special">

        <td class="cesar-data-forum-table">
            <center><img style="width: 37px;height: 41px;margin-bottom: -14px;margin-left: -12px;"
                    src="cesar525/img/cloud.jpg" alt=""></center>
        </td>
        <?php
								$url = url("forum.php?forum=". $category['name'] ."&cat=". $getCat ."&thread=". $thread['id']);
								echo '<td class="cesar-data-forum-table-select" style="width: 60%;" onclick="javascript:window.location.href=\'' . $url . '\'">';
								?>
        <!--<td class="cesar-data-forum-table">-->
        <?php
		//counting total post for threads
		$thread_id = $thread['id'];
		
$count_post_result_threads = query("SELECT COUNT(forum_id) FROM znote_forum_posts WHERE thread_id='$thread_id'", $connect);
$count_arrays = mysqli_fetch_array($count_post_result_threads);
if($count_arrays[0] == 0){
	$counting_post_for_threads = 0;
}else{
	$counting_post_for_thrads = $count_arrays[0];
}

?>
        <?php
		$thread_created_guild = $thread['created'];
		echo'<div style="margin-bottom: -22px;white-space: nowrap; overflow: hidden;text-overflow: ellipsis;width: 87%;font-size: 23px;">';
									if ($thread['sticky'] == 1) echo $forum_config_cesar['forum']['sticky'],' ';
									if ($thread['closed'] == 1) echo $forum_config_cesar['forum']['closed'],' ';
									echo $thread['title'].'</div>';
echo '<br><font style="font-size: 12px;color: #a09d9d;position: relative;top: 8px;">posted:&nbsp;<font style="color: #cc6223;">'.getClock($thread_created_guild, true).'</font></font>';
									//post count for threads
									if($forum_config_cesar['thread_display']['display_counting_post'] == true){
										echo '<td style="width: 13%;font-size: 17px;padding-right: 10px;" class="cesar-data-forum-table" ><center><div >Posts<br> '.thousand_format($counting_post_for_thrads).'</div></center></td>';
									}
									?>
        </td>
        <?php
								$url = url("characterprofile.php?name=". $thread['player_name']);
								// echo '<td class="cesar-data-forum-table-select" onclick="javascript:window.location.href=\'' . $url . '\'">';
								?>
        <!--<td class="cesar-data-forum-table">-->
        <?php
		///POSTED BY
		if(false){
			echo'<td  class="cesar-data-forum-table" >';
			echo'<center><font  style="font-size: 13px;">-No posts at this moment-</font></center>';
			echo'</td>';
			}else{
				
				$player_name_guild = $thread['player_name'];
				//getting player level
				$checking_for_player_level = query("SELECT `level` FROM players WHERE `name`='$player_name_guild'", $connect);
if($checking_for_player_level){
	
$row_getting_level = mysqli_fetch_assoc($checking_for_player_level);
$player_level_guild = $row_getting_level['level'];
}else{
	
}

///new
echo'<td style="width: 17%;font-size: 17px;padding-bottom: 3px;" class="cesar-data-forum-table" ><div style="height: 54px;display:inline-block " >';
	//name
echo'<div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width: 101px;height: 24px;margin-bottom: -19px;color:orange;""><font style="font-family: Martel, serif;color:orange;">'.$thread['player_name'].'</font></div><br>';
//level
echo'<div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width: 101px;width: 101px;height: 21px;margin-bottom: -19px;"><font> lvl: '.$player_level_guild.'</font></div><br>';
//date posted
echo'<font style="margin-top:10px;font-size: 10px;"> '.getClock($thread['created'], true).'</font></div>&nbsp;&nbsp;&nbsp;';
//image showing
if($forum_config_cesar['set_avatar_for_all']['enabling'] && $forum_config_cesar['set_avatar_for_all']['enabling_show_on_thread_posted_by'] == true){
	echo'<div style="display:inline-block;"><img style="width: 44px;border: solid 1px #37363659;height: 44px;" src="'.$forum_config_cesar['set_avatar_for_all']['image_for_all_path'].'"></img>
</div>';}
echo'</td>';				
//new
			}
			
									?>


    </tr>
    <?php
						}
					}
					?>
</table>


<?php
			} else echo '<center><div style="background-color: black;padding: 4px;padding-left: 9px;border: solid 1px #4a4949;border-radius: 9px;"><font color="red" style="font-family:lato;">  Board is empty, no threads exist yet.  </font></div></center>';

			///////////
			// Create thread button
			if ($charCount > 0) {
				if ($category['closed'] == 0  || $admin) {
					?>

<div class="cesar-create-board">
    <center><form action="" method="post">
        <table style="background-color:black;">
            <center style="margin-top: 11px;font-size: 21px;color: #d8d8d8;">Create Thread</center>
            <input type="hidden" value="<?php echo $getCat; ?>" name="new_thread_category">
            <tr>
                <td class="cesar-data-forum-table" style="width: 50%;">
                    <center style="margin-top: 11px;">
                        <font style="font-size: 16px;">Select character name:</font>

                    </center>
                </td>
                <td class="cesar-data-forum-table" style="width: 50%;">
                    <center><select class="cesar-selecting"
                            style="font-size: 15px;margin-top: 11px;width: 281px;height: 45px;" name="new_thread_cid">
                            <?php
							foreach($yourChars as $char) {
								echo "<option value='". $char['id'] ."'>". $char['name'] ."</option>";
							}
							?>
                        </select></center>
                </td>
            </tr>
            <tr>
                <td colspan="2"><br>
                    <center><input type="submit" value="Create new thread" class="cesar-submit-button"></center>
                </td>
            </tr>
    </form></center>
    </table>
</div>

<?php
				} else echo '<p>This board is closed.</p>';
			} else echo '<div style="background-color: #0c0c0c;border-radius: 11px;border: solid 1px #353535;padding: 7px;color: #ed3b3bf7;font-size: 20px;"><center>You must have a character on your account that is level &nbsp;'.$config["forum"]["level"].'+ to create new threads.</center></div>'; ?>
		
	<?php
	} else 
	echo '<div style="background-color: #0c0c0c;border-radius: 11px;border: solid 1px #353535;padding: 7px;color:  #ff2a2a;;font-size: 20px;"><center><font>Your permission to access this board has been denied.<br>If you are trying to access a Guild Board, you need level: '. $config['forum']['level'] .'+</font></center></div>';

	}
} else {

	//////////////////////
	// No category specified, show list of available categories
	if (!$admin) $categories = mysql_select_multi(
		"SELECT `id`, `name`, `access`, `closed`, `hidden`, `guild_id`, `category_comment` FROM `znote_forum` WHERE `access`<='$yourAccess' ORDER BY `name`;");
		else $categories = mysql_select_multi("SELECT `id`, `name`, `access`, `closed`, `hidden`, `guild_id`, `category_comment` FROM `znote_forum` ORDER BY `name`;");

	$guildboard = false;
	if ($forum_config_cesar['category_display']['counting_post_for_categories'] && $forum_config_cesar['category_display']['counting_threads_for_categories'] === true ){
	$change_colspan = 'colspan="3"';
}elseif($forum_config_cesar['category_display']['counting_post_for_categories'] == true){
	$change_colspan = 'colspan="2"';
}elseif($forum_config_cesar['category_display']['counting_threads_for_categories'] == true ){
	$change_colspan = 'colspan="2"';
}

	?>

<table class="cesar-forum-background-table" id="forumCategoryTable">
    <tr class="yellow" style="background-color: #ff000000;">
        <th class="cesar-table-headers">Img</th>
        <th class="cesar-table-headers" <?php echo $change_colspan;?>>Forum Boards</th>
        <?php 
		if($forum_config_cesar['category_display']['adding_last_thread_view'] == true){
			echo '<th class="cesar-table-headers">Last Thread by</th>';
		}
		
		if($forum_config_cesar['category_display']['adding_last_post_view'] == true){
		echo '<th class="cesar-table-headers">Last Post by</th>';
	}
        
			$guild = false;
			foreach($charData as $char) {
				if ($char['guild'] > 0) $guild = true;
			}

			if ($admin || $guild) {
				if (!isset($guilds))  {
					$guilds = mysql_select_multi("SELECT `id`, `name` FROM `guilds` ORDER BY `name`;");
					$guilds[] = array('id' => '0', 'name' => 'No guild');
				}
				$guildName = array();
				foreach($guilds as $guild) {
					$guildName[$guild['id']] = $guild['name'];
				}
				if ($admin) {
					?>
        <th class="cesar-table-headers">Edit</th>
        <th class="cesar-table-headers">Delete</th>

        <?php
				}
			}
			?>
    </tr>
    <?php
		if ($categories !== false) {

			foreach ($categories as $category) {
				$access = true;
				
				if ($category['guild_id'] > 0) {
					$guildboard[] = $category;
					$access = false;
					
				}

				/*
				if ($guild) {
					foreach($charData as $char) {
						if ($category['guild_id'] == $char['guild']) $access = true;
					}
				}
				*/
				if ($access) {
					$url = url("forum.php?cat=". $category['id']);
					
					echo '<tr class="special">';
					echo'<td class="cesar-data-forum-table imgs" ><img style="width: 31px;height: 31px;" src="cesar525/img/folders.png"></img></td>';
					echo '<td class="cesar-data-forum-table-select" onclick="javascript:window.location.href=\'' . $url . '\'"><font>';
					if ($category['closed'] == 1) echo $forum_config_cesar['forum']['closed'],' ';
					if ($category['hidden'] == 1) echo $forum_config_cesar['forum']['hidden'],' ';
					if ($category['guild_id'] > 0) {
						echo "[". $guildName[$category['guild_id']] ."] ";
					}
					echo $category['name'] ."</font><div class='cesar-category-hint'>".$category['category_comment']."</div></td>";


//counting thread in category
					$category_id = $category['id'];
					$count_result = query("SELECT COUNT(forum_id) FROM `znote_forum_threads` WHERE forum_id='$category_id'",$connect);
					$count = mysqli_fetch_array($count_result);
					if( $count[0] == 0){ 
						// echo'zero';
					$count_threads = 0;}else{ 
						// echo 'we have some';
						$count_threads = $count[0];
					}
					if($forum_config_cesar['category_display']['counting_threads_for_categories'] == true){
echo'<td style="width: 13%;font-size: 17px;padding-right: 10px;" class="cesar-data-forum-table" ><center><div >Threads<br> '.thousand_format($count_threads).'</div></center></td>';
					}
//couting total threads end


//counting total post for category
$count_post_result = query("SELECT COUNT(forum_id) FROM znote_forum_posts WHERE forum_id='$category_id'", $connect);
$count_array = mysqli_fetch_array($count_post_result);
if($count_array[0] == 0){
	$counting_post_for_cat = 0;
}else{
	$counting_post_for_cat = $count_array[0];
}
if($forum_config_cesar['category_display']['counting_post_for_categories'] == true){
echo'<td style="width: 13%;font-size: 17px;padding-right: 10px;" class="cesar-data-forum-table" ><center><div >Posts<br> '.thousand_format($counting_post_for_cat).'</div></center></td>';
}
//countin gpost end


// checking for last threads
if($forum_config_cesar['category_display']['adding_last_thread_view'] == true){
$checking_for_last_threads = query("SELECT forum_id, player_id, player_name, created FROM znote_forum_threads WHERE forum_id='$category_id' ORDER BY created DESC LIMIT 1", $connect);
if($checking_for_last_threads){
	// echo'working';
	$row_threads = mysqli_fetch_assoc($checking_for_last_threads);
$player_id_threads = $row_threads['player_id'];
$thread_created = getClock($row_threads['created'], true);
//getting player level and name
$checking_on_player = query("SELECT `name`, `level` FROM players WHERE id='$player_id_threads'", $connect);
if($checking_on_player){
	// echo'player_working';
$row_players_for_threads = mysqli_fetch_assoc($checking_on_player);
$player_name_for_threads = $row_players_for_threads['name'];
$player_level_for_threads = $row_players_for_threads['level'];

if($player_name_for_threads == ''){
	echo'<td  class="cesar-data-forum-table" >';
	echo'<center><font  style="font-size: 13px;">-No threads at this moment-</font></center>';
	echo'</td>';

}else{
	echo'<td style="width: 17%;font-size: 17px;padding-bottom: 3px;" class="cesar-data-forum-table" ><div style="height: 54px;display:inline-block " >';
	//name
echo'<div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width: 101px;height: 24px;margin-bottom: -19px;color:orange;""><font style="font-family: Martel, serif;color:orange;">'.$player_name_for_threads.'</font></div><br>';
//level
echo'<div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width: 101px;width: 101px;height: 21px;margin-bottom: -19px;"><font> lvl: '.$player_level_for_threads.'</font></div><br>';
//date posted
echo'<font style="margin-top:10px;font-size: 10px;"> '.$thread_created.'</font></div>&nbsp;&nbsp;&nbsp;';
//image showing
if($forum_config_cesar['set_avatar_for_all']['enabling'] && $forum_config_cesar['set_avatar_for_all']['enabling_show_on_last_thread_category'] == true ){
echo'<div style="display:inline-block;"><img style="width: 44px;border: solid 1px #37363659;height: 44px;" src="'.$forum_config_cesar['set_avatar_for_all']['image_for_all_path'].'"></img>
</div>';}
echo'</td>';					
// echo'working';
}
}else{
	// echo'Player not working';
}
}else{
	// echo'not working';
}
}
//checking for last thread end

// setting up last post
if($forum_config_cesar['category_display']['adding_last_post_view'] == true){

$result_checking_last_post = query("SELECT thread_id, player_id, created FROM znote_forum_posts WHERE forum_id='$category_id' ORDER BY created DESC LIMIT 1", $connect);
if($result_checking_last_post){
$row_post = mysqli_fetch_assoc($result_checking_last_post);
$player_id = $row_post['player_id'];
$post_posted = $row_post['created'];
$checking_for_playuer_credentials = query("SELECT `name`, `level` FROM players WHERE id='$player_id'", $connect);
if($checking_for_playuer_credentials){
	// echo'working';
	$row_player_credentials = mysqli_fetch_assoc($checking_for_playuer_credentials);
$player_name = $row_player_credentials['name'];
$player_level = $row_player_credentials['level'];
}else{
	// echo'not working';
}

if($player_name == ''){
echo'<td  class="cesar-data-forum-table" >';
echo'<center><font  style="font-size: 13px;">-No posts at this moment-</font></center>';
echo'</td>';
}else{
echo'<td style="width: 17%;font-size: 17px;padding-bottom: 3px;" class="cesar-data-forum-table" ><div style="height: 54px;display:inline-block " >';
	//name
echo'<div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width: 101px;height: 24px;margin-bottom: -19px;color:orange;""><font style="font-family: Martel, serif;color:orange;">'.$player_name.'</font></div><br>';
//level
echo'<div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width: 101px;width: 101px;height: 21px;margin-bottom: -19px;"><font> lvl: '.$player_level.'</font></div><br>';
//date posted
echo'<font style="margin-top:10px;font-size: 10px;"> '.getClock($post_posted, true).'</font></div>&nbsp;&nbsp;&nbsp;';
//image showing
if($forum_config_cesar['set_avatar_for_all']['enabling'] && $forum_config_cesar['set_avatar_for_all']['enabling_show_on_last_post_category'] == true ){
echo'<div style="display:inline-block;"><img style="width: 44px;border: solid 1px #37363659;height: 44px;" src="'.$forum_config_cesar['set_avatar_for_all']['image_for_all_path'].'"></img>
</div>';}
echo'</td>';					
// echo'working';
}

}else{
// echo'not';
}
}
//setting up last post end --
// Admin columns
					if ($admin) {
						?>
    <td class="cesar-data-forum-table" style="margin: 0px; padding: 0px; width: 100px;">
        <form action="" method="post">
            <input type="hidden" name="admin_category_id" value="<?php echo $category['id']; ?>">
            <center><input class="cesar-button" type="submit" name="admin_category_edit" value="Edit"></center>
        </form>
    </td>
	<!-- DELETE FORUM BOARD -->
    <td class="cesar-data-forum-table" style="margin: 0px; padding: 0px; width: 100px;">
        <form action="forum_cesar_api.php" method="post">
            <input type="hidden" name="admin_category_id" value="<?php echo $category['id']; ?>">
            <center><input class="cesar-button-delete" type="submit" name="admin_category_delete" value="Delete">
            </center>
        </form>
    </td>
    <?php
					}
					echo '</tr>';
				}
			}
		}
		?>
</table>







<!-- <hr class="bighr"> -->
<?php
	if ($guildboard !== false && $guild || $guildboard !== false && $admin) {
		//
		?>



<table class="cesar-forum-background-table" style="margin-top: 11px;" id="forumCategoryTable">
    <tr class="yellow" style="background-color: #ff000000;">
        <th class="cesar-table-headers">Img</th>
        <th class="cesar-table-headers" <?php echo $change_colspan;  ?>>Guild Boards</th>

        <?php  if($forum_config_cesar['category_display']['adding_last_thread_view'] == true){ ?>
        <th class="cesar-table-headers">Last Thread by</th>
        <?php } ?>
        <?php if($forum_config_cesar['category_display']['adding_last_post_view'] == true){ ?>
        <th class="cesar-table-headers">Last Post by</th>
        <?php } ?>
        <?php
				foreach($charData as $char) {
					if ($char['guild'] > 0) $guild = true;
				}

				if ($admin || $guild) {
					if (!isset($guilds))  {
						$guilds = mysql_select_multi("SELECT `id`, `name` FROM `guilds` ORDER BY `name`;");
						$guilds[] = array('id' => '0', 'name' => 'No guild');
					}
					$guildName = array();
					foreach($guilds as $guild) {
						$guildName[$guild['id']] = $guild['name'];
					}
					if ($admin) {
						?>
        <th class="cesar-table-headers">Edit</th>
        <th class="cesar-table-headers">Delete</th>
        <?php
					}
				}
				?>
    </tr>
    <?php




			$count = 0;
			foreach ($guildboard as $board) {
				$access = false;
				foreach($charData as $char) {
					if ($board['guild_id'] == $char['guild']) {
						$access = true;
						$count++;
					}
				}
				if ($access || $admin) {
					$url = url("forum.php?cat=". $board['id']);
					echo '<tr class="special">';
					echo'<td class="cesar-data-forum-table imgs" ><img style="width: 31px;height: 31px;" src="cesar525/img/folders.png"></img></td>';
					echo '<td class="cesar-data-forum-table-select" class="cesar-data-forum-table" onclick="javascript:window.location.href=\'' . $url . '\'">';
					if ($board['closed'] == 1) echo $forum_config_cesar['forum']['closed'],' ';
					if ($board['hidden'] == 1) echo $forum_config_cesar['forum']['hidden'],' ';
					if ($board['guild_id'] > 0) {
						echo "[". $guildName[$board['guild_id']] ."] ";
					}
					echo $board['name'] ."</font><div class='cesar-category-hint'>".$board['category_comment']."</div>";
					

//count guild threads
					$category_id_guils_count_threads = $board['id'];
					$count_result = query("SELECT COUNT(forum_id) FROM `znote_forum_threads` WHERE forum_id='$category_id_guils_count_threads'",$connect);
					$count = mysqli_fetch_array($count_result);
					if( $count[0] == 0){ 
						// echo'zero';
					$count_threads = 0;}else{ 
						// echo 'we have some';
						$count_threads = $count[0];
					}
					if($forum_config_cesar['category_display']['counting_threads_for_categories'] == true){
echo'<td style="width: 13%;font-size: 17px;padding-right: 10px;" class="cesar-data-forum-table" ><center><div >Threads<br> '.thousand_format($count_threads).'</div></center></td>';
					}
//count guild ghtreads end.

//counting total post for category
$count_post_result = query("SELECT COUNT(forum_id) FROM znote_forum_posts WHERE forum_id='$category_id_guils_count_threads'", $connect);
$count_array = mysqli_fetch_array($count_post_result);
if($count_array[0] == 0){
	$counting_post_for_cat = 0;
}else{
	$counting_post_for_cat = $count_array[0];
}
if($forum_config_cesar['category_display']['counting_post_for_categories'] == true){
echo'<td style="width: 13%;font-size: 17px;padding-right: 10px;" class="cesar-data-forum-table" ><center><div >Posts<br> '.thousand_format($counting_post_for_cat).'</div></center></td>';
}
//counting post end



					// checking for last threads by
if($forum_config_cesar['category_display']['adding_last_thread_view'] == true){
	$guild_id = $board['guild_id'];
	$category_id_guild = $board['id'];	
	$checking_for_last_threadss = query("SELECT forum_id, player_id, player_name, created FROM znote_forum_threads WHERE forum_id='$category_id_guild' ORDER BY created DESC LIMIT 1", $connect);
	if($checking_for_last_threadss){
		// echo'working';
		$row_threads = mysqli_fetch_assoc($checking_for_last_threadss);
	$player_id_threads = $row_threads['player_id'];
	$thread_created = getClock($row_threads['created'], true);
	//getting player level and name
	$checking_on_players = query("SELECT `name`, `level` FROM players WHERE id='$player_id_threads'", $connect);
	if($checking_on_players){
		// echo'player_working';
	$row_players_for_threads = mysqli_fetch_assoc($checking_on_players);
	$player_name_for_threads = $row_players_for_threads['name'];
	$player_level_for_threads = $row_players_for_threads['level'];
	
	if($player_name_for_threads == ''){
		echo'<td  class="cesar-data-forum-table" >';
		echo'<center><font  style="font-size: 13px;">-No threads at this moment-</font></center>';
		echo'</td>';
	
	}else{
		echo'<td style="width: 17%;font-size: 17px;padding-bottom: 3px;" class="cesar-data-forum-table" ><div style="height: 54px;display:inline-block " >';
		//name
	echo'<div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width: 101px;height: 24px;margin-bottom: -19px;color:orange;""><font style="font-family: Martel, serif;color:orange;">'.$player_name_for_threads.'</font></div><br>';
	//level
	echo'<div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width: 101px;width: 101px;height: 21px;margin-bottom: -19px;"><font> lvl: '.$player_level_for_threads.'</font></div><br>';
	//date posted
	echo'<font style="margin-top:10px;font-size: 10px;"> '.$thread_created.'</font></div>&nbsp;&nbsp;&nbsp;';
	//image showing
	echo'<div style="display:inline-block;"><img style="width: 44px;border: solid 1px #37363659;height: 44px;" src="'.$forum_config_cesar['set_avatar_for_all']['image_for_all_path'].'"></img>
	</div>';
	echo'</td>';					
	// echo'working';
	}
	}else{
		// echo'Player not working';
	}
	}else{
		// echo'not working';
	}
	}
	//checking for last thread end


// setting up last post by
if($forum_config_cesar['category_display']['adding_last_post_view'] == true){
	$guild_id = $board['guild_id'];
	$category_id_guilds = $board['id'];

$result_checking_last_post = query("SELECT thread_id, player_id, created FROM znote_forum_posts WHERE forum_id='$category_id_guilds' ORDER BY created DESC LIMIT 1", $connect);
if($result_checking_last_post){
$row_post = mysqli_fetch_assoc($result_checking_last_post);
$player_id = $row_post['player_id'];
$post_posted = $row_post['created'];
$checking_for_playuer_credentials = query("SELECT `name`, `level` FROM players WHERE id='$player_id'", $connect);
if($checking_for_playuer_credentials){
	// echo'working';
	$row_player_credentials = mysqli_fetch_assoc($checking_for_playuer_credentials);
$player_name = $row_player_credentials['name'];
$player_level = $row_player_credentials['level'];
}else{
	// echo'not working';
}

if($player_name == ''){
echo'<td  class="cesar-data-forum-table" >';
echo'<center><font  style="font-size: 13px;">-No posts at this moment-</font></center>';
echo'</td>';
}else{
echo'<td style="width: 17%;font-size: 17px;padding-bottom: 3px;" class="cesar-data-forum-table" ><div style="height: 54px;display:inline-block " >';
	//name
echo'<div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width: 101px;height: 24px;margin-bottom: -19px;color:orange;""><font style="font-family: Martel, serif;color:orange;">'.$player_name.'</font></div><br>';
//level
echo'<div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width: 101px;width: 101px;height: 21px;margin-bottom: -19px;"><font> lvl: '.$player_level.'</font></div><br>';
//date posted
echo'<font style="margin-top:10px;font-size: 10px;"> '.getClock($post_posted, true).'</font></div>&nbsp;&nbsp;&nbsp;';
//image showing
echo'<div style="display:inline-block;"><img style="width: 44px;border: solid 1px #37363659;height: 44px;" src="'.$forum_config_cesar['set_avatar_for_all']['image_for_all_path'].'"></img>
</div>';
echo'</td>';					
// echo'working';
}

}else{
// echo'not';
}
}
//setting up last post end --





					// Admin columns
					if ($admin) {
						?>
    <td class="cesar-data-forum-table" style="margin: 0px; padding: 0px; width: 100px;">
        <form action="" method="post">
            <input type="hidden" name="admin_category_id" value="<?php echo $board['id']; ?>">
            <center> <input type="submit" class="cesar-button" name="admin_category_edit" value="Edit"></center>
        </form>
    </td>

	<!-- DELETE GUILD FORUM BOARD -->
    <td class="cesar-data-forum-table" style="margin: 0px; padding: 0px; width: 100px;">
        <form action="forum_cesar_api.php" method="post">
            <input type="hidden" name="admin_category_id" value="<?php echo $board['id']; ?>">
            <center><input class="cesar-button-delete" type="submit" name="admin_category_delete" value="Delete">
            </center>
        </form>
    </td>
    <?php
					}
					echo '</tr>';
				}
			}
			if ($count == 0 && !$admin) echo '<tr><td class="cesar-data-forum-table">You don\'t have access to any guildboards.</td></tr>';
			?>
</table>
<?php
	}
?>



<?php
}
?>
<!-- WORKING SECTION -->




<!-- FORUM BOARD -->





<!-- FORUM BOARD END -->

<?php
if(!empty($_GET)){
request_feedback($_GET['boardupdate']);
request_feedback($_GET['boarddelete']);
request_feedback($_GET['creatingboard']);
}
?>

<!-- ADMIN - EDITING FORUM -->
<?php
//////////////////
	// edit category

	$admin_category_delete = getValue($_POST['admin_category_delete']);
	$admin_category_edit = getValue($_POST['admin_category_edit']);
	$admin_category_id = getValue($_POST['admin_category_id']);


	if ($admin_category_edit !== false) {
		$admin_category_id = (int)$admin_category_id;
		$category = mysql_select_single("SELECT `id`, `name`, `access`, `closed`, `hidden`, `guild_id`, `category_comment` 
			FROM `znote_forum` WHERE `id`='$admin_category_id' LIMIT 1;");
		if ($category !== false) {
			?>
<div class="cesar-create-board">
    <center style="font-size: 31px;color: #a8a6a6;padding-top: 21px;">
        <font style="color:red;font-family:anton;">ADMIN</font>
        <font> - EDIT BOARD</font>
    </center>

    <form action="forum_cesar_api.php" method="post">
        <input type="hidden" name="admin_category_id" value="<?php echo $category['id']; ?>">

        <center><table style="background-color:black;">
            <tr>
                <td style="background-color: #000;color: white;border: solid 1px black;font-size: 21px;">
                    <center><label for="admin_category_name" style="color: #a8a6a6;">Board name:</label></center>
                </td>
                <td style="background-color:black;border: solid 1px #black;">
                    <center><input name="admin_category_name" value="<?php echo $category['name']; ?>"
                            class="span12 cesar-text">
                        <center>
                </td>

            </tr>
            <tr>
                <td style="background-color: #000;color: white;border: solid 1px #black;font-size: 21px;">
                    <center><label for="admin_category_access" style="color: #a8a6a6;">Required Access:</label></center>
                </td>
                <td style="background-color:black;border: solid 1px #black;">
                    <center><select name="admin_category_access" class="cesar-selecting"
                            style="width: 217px;height: 41px;font-size: 17px;padding: 2px 4px 3px 22px;">
                            <?php
								foreach($config['ingame_positions'] as $access => $name) {
									if ($access == $category['access']) echo "<option value='$access' selected>$name</option>";
									else echo "<option value='$access'>$name</option>";
								}
								?>
                        </select></center>
                </td>
            </tr>
            <tr>
                <td style="background-color: #000;color: white;border: solid 1px #black;font-size: 21px;">
                    <center><label for="admin_category_closed" style="color: #a8a6a6;">Closed:</label></center>
                </td>
                <td style="background-color:black;border: solid 1px #black;">
                    <center><select name="admin_category_closed" class="cesar-selecting"
                            style="width: 217px;height: 41px;font-size: 17px;padding: 2px 4px 3px 22px;">
                            <?php
								if ($category['closed'] == 1) echo '<option value="1" selected>Yes</option>';
								else echo '<option value="1">Yes</option>';
								if ($category['closed'] == 0) echo '<option value="0" selected>No</option>';
								else echo '<option value="0">No</option>';
								?>
                        </select></center>
                </td>
            </tr>
            <tr>
                <td style="background-color: #000;color: white;border: solid 1px #black;font-size: 21px;">
                    <center><label for="admin_category_hidden" style="color: #a8a6a6;">Hidden:</label></center>
                </td>
                <td style="background-color:black;border: solid 1px #black;">
                    <center><select name="admin_category_hidden" class="cesar-selecting"
                            style="width: 217px;height: 41px;font-size: 17px;padding: 2px 4px 3px 22px;">
                            <?php
								if ($category['hidden'] == 1) echo '<option value="1" selected>Yes</option>';
								else echo '<option value="1">Yes</option>';
								if ($category['hidden'] == 0) echo '<option value="0" selected>No</option>';
								else echo '<option value="0">No</option>';
								?>
                        </select></center>
                </td>
            </tr>
            <tr>
                <td style="background-color: #000;color: white;border: solid 1px #black;font-size: 21px;">
                    <center><label for="admin_category_guild_id" style="color: #a8a6a6;">Select Guild:</label></center>
                </td>
                <td style="background-color:black;border: solid 1px #black;">
                    <center> <select name="admin_category_guild_id" class="cesar-selecting"
                            style="width: 217px;height: 41px;font-size: 17px;padding: 2px 4px 3px 22px;">
                            <?php foreach($guilds as $guild) {
									if ($category['guild_id'] == $guild['id']) echo "<option value='". $guild['id'] ."' selected>". $guild['name'] ."</option>";
									else echo "<option value='". $guild['id'] ."'>". $guild['name'] ."</option>";
								} ?>
                        </select></center>
                </td>
            </tr>

            <tr>
                <td style="background-color: #000;color: white;border: solid 1px #black;font-size: 21px;">
                    <center><label for="admin_category_name"style="color: #a8a6a6;">Comment:</label></center>
                </td>
                <td style="background-color:black;border: solid 1px #black;">
                    <center><textarea name="admin_category_comment" class="span12 cesar-textarea"
                            maxlength="100"><?php echo $category['category_comment']; ?></textarea>
                        <center>
                </td>

            </tr>


            <tr>
                <td colspan="2" style="background-color: #000;">
                    <center><input type="submit" name="admin_update_category" value="Update Board"
                            class="cesar-submit-button"></center>
                </td>
            </tr>
        </table></center>
    </form>
</div>
<?php
		} else echo '<h2>Category not found.</h2>';

	}?>

<!-- ADMIN - EDITING FORUM ENDS -->











<!-- ADMIN - CREATING BOARD  -->
<?php if(isset($_GET['creatingboard']) || isset($_GET['boarddelete']) || isset($_GET['boardupdate'])){
	$create_board_show = false;
} ?>

<?php
if ($admin && !$getCat && $create_board_show) {
	?>

<div class="cesar-create-board">
    <center><br>
        <font style="font-size: 31px;color: #b5afaf;">
            <font style="color:red;font-family:anton;">ADMIN</font> - Create Board
        </font>
    </center>
   <center><form class="cesar-forms" action="forum_cesar_api.php" method="post">
		<div style="padding-left:30px;">
    <font style="color: #a8a6a6;font-size:21px;">Board Name:</font><br> <input class="cesar-text" type="text" name="admin_board_create_name" maxlength="25"
            style="width:221px;font-size: 14px;" placeholder="No more than 25 letters allowed" required></div> 
        <div style="width:371px;">
            <center>
                <font style="color: #a8a6a6;font-size:21px;">Board bottom message:</font><br> <textarea class="cesar-textarea"
                    name="admin_board_create_comment" maxlength="100" id="" cols="30" rows="10"
                    placeholder="No more than 100 letters allowed"></textarea><br><br>
            </center>
        </div>
       <font style="color: #a8a6a6;font-size:21px;" > Required access: </font><select class="cesar-selecting" name="admin_board_create_access">
            <?php
			foreach($config['ingame_positions'] as $access => $name) {
				echo "<option value='$access'>$name</option>";
			}
			?>
        </select ><br><br>

        <font style="color: #a8a6a6;font-size:21px;"> Board closed:</font> <select class="cesar-selecting" name="admin_board_create_closed">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select><br><br>

        <font style="color: #a8a6a6;font-size:21px;"> Board hidden:</font> <select class="cesar-selecting" name="admin_board_create_hidden">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select><br><br>

        <font style="color: #a8a6a6;font-size:21px;"> Guild board: </font><select class="cesar-selecting" name="admin_board_create_guild_id">
            <?php
			foreach($guilds as $guild) {
				if ($guild['id'] == 0) echo "<option value='". $guild['id'] ."' selected>". $guild['name'] ."</option>";
				else echo "<option value='". $guild['id'] ."'>". $guild['name'] ."</option>";
			}
			?>
        </select><br><br>

        <center> <input class="cesar-submit-button" type="submit" value="Create Board" class="btn btn-primary"></center>
    </form></center> 
</div>
<?php
}
?>
<!-- ADMIN - CREATING BOARD END -->

</div>

<?php include 'cesar525/cesar_footer.php'; ?>