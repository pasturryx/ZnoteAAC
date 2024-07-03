

<?php

//Enabling forum expand when mouse on top
$forum_config_cesar['forum_expand_hover'] = true;

$forum_config_cesar['forum'] = array(
'hidden' => '<font color="orange">[Hidden]</font>',
'closed' => '<font color="red">[Closed]</font>',
'sticky' => '<font color="green">[Pinned]</font>',
);

///AVATAR SETTINGS if Enabling is False it will turn into Outfit outfit Avatar
$forum_config_cesar['set_avatar_for_all'] = array(
"enabling" => false, // enabling photo every where
"image_for_all_path" => "cesar525/img/cesartag.jpg", // this is the image for everyone (example: you can set up the server logo)
"enabling_show_on_last_post_category" => true, // as it says
"enabling_show_on_last_thread_category" => true, // as it says
"enabling_show_on_thread_posted_by" => true // as it says
);

//FORUM CATEGORIES DISPLAY SETTINGS
$forum_config_cesar['category_display'] = array(
"counting_post_for_categories" => true, // show counting post
"counting_threads_for_categories" => true, // show counting threads
"adding_last_post_view" => true, //show last post
"adding_last_thread_view" => true // show last thread
);

// FORUM THREADS DISPLAY SETTINGS
$forum_config_cesar['thread_display'] = array(
"display_counting_post" => true, // show count post
"display_last_post_by" => true // show last post
);

//POST TEXT BOX FONT SIZE
$forum_config_cesar['font_size_default'] = "21px";









// to find the panel width search for :
// <!-- MAIN FEED -->
				//<div class="pull-left leftPane" style="width: 1201px;">


?>