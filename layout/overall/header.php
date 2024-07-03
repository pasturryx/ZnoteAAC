<!DOCTYPE html>
	<head>
		<meta charset="UTF-8">
		<title><?php echo $config['site_title']; ?></title>
		<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Amarante|Mirza" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="layout/style.css">
		<link rel="stylesheet" type="text/css" href="layout/tibia.css">
		<script src="../engine/js/jquery-1.10.2.min.js"></script>
		<script src="layout/Cufon-yui.js"></script>
		<script src="layout/jquery.slides.min.js"></script>
		<script src="layout/Trajan_Pro_400.font.js"></script>
		<script type="text/javascript">
		Cufon.replace('.cufon', {
				color: '-linear-gradient(#ffa800, #6a3c00)',
				textShadow: '#14110c 1px 1px, #14110c -1px 1px'
			});
		</script>
		<style>
.display-none {
	display: none !important;
}

.display-inline {
	display: inline !important;
}
		</style>
		<script>
			jQuery(function(){
				jQuery('.changelog_trigger').click(function(e){
					e.preventDefault();
				jQuery('.minus'+$(this).attr('targetid')).toggle();
				jQuery('.plus'+$(this).attr('targetid')).toggle();	 
				
						jQuery('.changelog_big'+$(this).attr('targetid')).toggleClass("display-inline");
					  
					  jQuery('.changelog_small'+$(this).attr('targetid')).toggleClass("display-none");
					
				});
			});
		</script>
		<script>
		   $(function() {
		   $('#slides').slidesjs({
			width: 207,
			height: 100,
			navigation: true,
			play: {
			active: false,
			auto: true,
			interval: 3000,
			swap: true,
			pauseOnHover: false,
			restartDelay: 2500
			  }
		   });
		   });
	  </script>
	</head>
	<body>
	<?php
		function user_count_characters() {
			$result = mysql_select_single("SELECT COUNT(`id`) AS `id` from `players`;");
			return ($result !== false) ? $result['id'] : 0;
		}
	?>
<div class="video-background">
    <!-- <iframe id="bgvid" src="https://www.youtube.com/embed/-jK5CSXETb8?autoplay=1&mute=1&loop=1&playlist=-jK5CSXETb8&enablejsapi=1" frameborder="0" allowfullscreen></iframe> -->
</div>


<style>
    body {
        margin: 0;
        padding: 0;
        font-size: 13px;
        font-family: 'Open Sans', sans-serif;
        background: #0d0f12;
    }


    .video-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        overflow: hidden;
    }


    #bgvid {
        width: 100vw;
        height: 100vh;
    }
</style>>
<script src="https://www.youtube.com/iframe_api"></script>
<script>
    var player;
    function onYouTubeIframeAPIReady() {
        player = new YT.Player('bgvid', {
            events: {
                'onReady': onPlayerReady
            }
        });
    }

    function onPlayerReady(event) {
        event.target.playVideo();
        var playerElement = document.getElementById('bgvid');
        playerElement.style.pointerEvents = 'none';
        playerElement.style.display = 'block';
    }
</script>
	<!-- agregado -->
	<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_EN/sdk.js#xfbml=1&version=v2.8";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<!-- agregado -->
	<div class="top-bar">
		<a href="register.php">
		
							<?php
							$date = 'Dec 23 2022 16:00:00 CET';
							$exp_date = strtotime($date);
							$now = time();

							if ($now < $exp_date) {
							?>
							<script>
							// Count down milliseconds = server_end - server_now = client_end - client_now
							var server_end = <?php echo $exp_date; ?> * 1000;
							var server_now = <?php echo time(); ?> * 1000;
							var client_now = new Date().getTime();
							var end = server_end - server_now + client_now; // this is the real end time

							var _second = 1000;
							var _minute = _second * 60;
							var _hour = _minute * 60;
							var _day = _hour *24
							var timer;

							function showRemaining()
							{
								var now = new Date();
								var distance = end - now;
								if (distance < 0 ) {
								   clearInterval( timer );
								   document.getElementById('countdown').innerHTML = 'EXPIRED!';

								   return;
								}
								var days = Math.floor(distance / _day);
								var hours = Math.floor( (distance % _day ) / _hour );
								var minutes = Math.floor( (distance % _hour) / _minute );
								var seconds = Math.floor( (distance % _minute) / _second );

								var countdown = document.getElementById('countdown');
								countdown.innerHTML = '';
								if (days) {
									countdown.innerHTML += ' <span style="color:white;">' + days + '</span> DAYS ';
								}
								countdown.innerHTML += ' <span style="color:white;">' + hours+ '</span> HOURS';
								countdown.innerHTML += ' <span style="color:white;">' + minutes+ '</span> MINUTES';
								countdown.innerHTML += ' <span style="color:white;">' + seconds+ '</span> SECONDS';
							}

							timer = setInterval(showRemaining, 1000);
							</script>
							Forgotten Server 8.6 Old Mechanics WAR SEASON Will Start In: <span style="color: yellow;" id="countdown">loading...</span>
							<?php
							} else {
								echo 'Forgotten Server 8.6 Old Mechanics WAR SEASON Will Start In: <span style="color: yellow;">SERVER STARTED!</span>';
							}
							?>

		</a>
	</div>
		<div class="container_main">

			<div class="container_left">
				<!-- whatsapp -->
				
    <div class="widget-container">
    	<!-- <a href="https://chat.whatsapp.com/IwjzOHzBmCB8r2UIg4MgTl" target="_blank" style="background: none;"><img style="height: 70px; width: 70px;" align ="center" src="layout/img/whatsapp.png"></a> -->
  
        </a>
    </div>
</body>

<!-- whatsapp -->

		<!-- <div class="container_main"> -->

			<!-- <div class="container_left"> -->
				<!-- <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script> -->
<!-- <div class="elfsight-app-dab78a65-bbe8-4a94-83bb-54cdc3885788"></div> -->
				<!-- <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script> -->
<!-- <div class="elfsight-app-6792601c-fb11-44fe-82cd-d62e8308d890"></div> -->
			<!-- .aqui esta dentro de un box agreado -->

<div class="right_box">
    <div class="corner_lt"></div>
    <div class="corner_rt"></div>
    <div class="corner_lb"></div>
    <div class="corner_rb"></div>
    <div class="title">
        <a href="https://chat.whatsapp.com/IwjzOHzBmCB8r2UIg4MgTl" target="_blank" style="background: none;">
            <img style="height: 50px; width: 50px;" src="layout/img/whatsapp.png" alt="WhatsApp">
        </a>
        <span style="background-image: url(layout/widget_texts/social.png);"></span>
    </div>
    <div class="content">
        <div class="rise-up-content" style="display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 10px;">
            <iframe src="https://discord.com/widget?id=1068695350719807568&theme=dark" width="100%" height="300" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts" style="max-width: 280px;"></iframe>
            <form action="https://www.paypal.com/donate" method="post" target="_top" style="margin-top: 1px;">
                <input type="hidden" name="hosted_button_id" value="N9XCTMEVVETXU" />
                <input type="image" src="layout/img/paypal3.png" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" width="185" height="80" />
                <img alt="" border="0" src="https://www.paypal.com/en_CL/i/scr/pixel.gif" width="1" height="1" />
            </form>
        </div>
    </div>
    <div class="border_bottom"></div>
</div>
<!-- <iframe src="https://discord.com/widget?id=1068695350719807568&theme=dark" width="250" height="250" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe> -->
		<!-- aqui esta dentro de un box -->
				<div class="left_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/account.gif"><span style="background-image: url(layout/widget_texts/account.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
							<ul>

								<li><a href="register.php"><b><font color="orange">Create Account</font></b></a></li>
								<li><a href="downloads.php">Download Client</a></li>
								<li><a href="recovery.php">Lost Account Interface</a></li>
								<li><a href="forum.php"><b><font color="green">Forum</font></b></a></li>
							</ul>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
                <div class="left_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/shop.gif"><span style="background-image: url(layout/widget_texts/shop.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
							<ul>
								<li><a href="buypoints.php"><b><font color="darkalmond">Buy Points</font></b></a></li>
								<li><a href="shop.php"><b><font color="mocha">Shop Offers</font></b></a></li>
							</ul>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
				<div class="left_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/newsicon_0.gif"><span style="background-image: url(layout/widget_texts/community.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
							<ul>
								<li><a href="sub.php?page=charactersearch"><b><font color="raspberry">Search Character</font></b></a></li>
								<!-- <li><a href="sub.php?page=highscores">Highscores</a></li> -->
								<li><a href="monsters.php">Monsters Loot</a></li>
								<!-- <li><a href="downloads.php">Downloads</a></li> -->
								<li><a href="highscores.php">Highscores</a></li>
								<li><a href="onlinelist.php">Online List</a></li>
								<li><a href="market.php">Item Market</a></li>
								<li><a href="gallery.php">Gallery</a></li>
								
								<li><a href="helpdesk.php">Helpdesk</a></li>
								<li><a href="houses.php">Houses</a></li>
								<li><a href="deaths.php"><b><font color="red">Deaths</font></b></a></li>
								<li><a href="killers.php"><b><font color="red">Killers</font></b></a></li>
									<?php //if ($config['guildwar_enabled'] === true) { ?>
											<li><a href="guilds.php">Guild List</a></li>
											<li><a href="guildwar.php">Guild Wars</a></li>
									<?php //} ?>
                        	</ul>
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
				<div class="left_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
					<div class="title"><img src="layout/img/library2.gif"><span style="background-image: url(layout/widget_texts/library.png);"></span></div>
					<div class="content">
						<div class="rise-up-content">
							<ul>
								<li><a href="serverinfo.php">Server Information</a></li>
								<li><a href="spells.php">Spells</a></li>
								<li><a href="support.php">Support</a></li>	
								<li><a href="changelog.php">Changelog</a></li>	
						</div>
					</div>
					<div class="border_bottom"></div>
				</div>
			</div>
			<div class="container_mid">
			<!-- FACEBOOK -->
				<div class="center_box">
					<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div> 
				 <div class="title"><span style="background-image: url(layout/widget_texts/facebook.png);"></span></div> 
				 <div class="content_bg">
						<div class="content">
							 <div class="rise-up-content" style="min-height: 150px;"> 
							 <div class="fb-page" style="padding: 10px 47px;" data-href="https://www.facebook.com/forgottennot.online" data-width="500" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false"><blockquote cite="https://www.facebook.com/forgottennot.online" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/forgottennot.online">ForgottenNot</a></blockquote></div> 
						 </div> 
					 </div> 
				 </div>
				 <div class="border_bottom"></div>
			 </div>
			 <!-- agregado -->
				<!-- CHANGELOG SYSTEM -->
				<?php
					if ($config['UseChangelogTicker'] && basename($_SERVER["SCRIPT_FILENAME"], '.php') == 'index') {
						?>
						<div class="center_box">
							<div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
							<div class="title"><span style="background-image: url(layout/widget_texts/changelog.png);"></span></div>
							<div class="content_bg">
								<div class="content">
									<div class="rise-up-content">
						<?php
						//////////////////////
						// Changelog ticker //
						// Load from cache
						//$changelogCache = new Cache('engine/cache/changelognews'); old
						$changelogCache = new Cache('engine/cache/changelog'); //edited new
						$changelogs = $changelogCache->load();

						if (isset($changelogs) && !empty($changelogs) && $changelogs !== false) {
							?>
							<table class="stripped" cellpadding="2" style="margin: 5px 0;">
								<?php
								for ($i = 0; $i < count($changelogs) && $i < 5; $i++) {
									?>
									<tr>
										<td><small><?php echo getClock($changelogs[$i]['time'], true, true); ?></small> - 
											<div class="changelog_small<?php echo $i; ?>"  style="display: inline-block;"><?php if(strlen($changelogs[$i]['text']) > 57) {echo substr($changelogs[$i]['text'], 0, 60) . '...';}else { echo $changelogs[$i]['text'];} ?></div>
											<div class="changelog_big<?php echo $i; ?>"  style="display: none;"><?php echo $changelogs[$i]['text']; ?></div>
										</td>
										<td width="5%" valign="top"><center><a href="#" targetid="<?php echo $i; ?>" class="changelog_trigger"><img class="plus<?php echo $i; ?>" src="layout/tibia_img/plus.gif"><img class="minus<?php echo $i; ?>" style="display: none;" src="layout/tibia_img/minus.gif"></a></center></td>
									</tr>
									<?php
								}
								?>
							</table>
							<?php
						} else echo "<center>No changelogs submitted.</center>";
						
						?>
									</div>
								</div>
							</div>
							<div class="border_bottom"></div>
						</div>
						<?php
					}
				?>
				<!-- MAIN CONTENT -->
				<!-- <div class="center_box"> -->
					<!-- <div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div> -->
					<!-- <div class="title"><span class="cufon" style="text-transform: uppercase;text-align: center;line-height: 43px;font-size: 16px;"><php echo basename($_SERVER["SCRIPT_FILENAME"], '.php'); ?></span></div> -->
					<!-- <div class="content_bg"> -->
						<!-- <div class="content"> -->
							<!-- <div class="rise-up-content" style="min-height: 565px;"> -->
								            <!-- MAIN CONTENT -->
<!-- MAIN CONTENT -->
<?php
// Define custom titles for specific pages
$page_titles = array(
    'charactersearch.php' => 'layout/widget_texts/charactersearch.png',
    'characterprofile.php' => 'layout/widget_texts/characterprofile.png',
    'protected.php' => 'layout/widget_texts/protected.png',
    'myaccount.php' => 'layout/widget_texts/myaccount.png',
    'buypoints.php' => 'layout/widget_texts/buypoints.png',
    'changelog.php' => 'layout/widget_texts/changelog.png',
    'support.php' => 'layout/widget_texts/support.png',
    'spells.php' => 'layout/widget_texts/spells.png',
    'serverinfo.php' => 'layout/widget_texts/serverinfo.png',
    'register.php' => 'layout/widget_texts/register.png',
    'monsters.php' => 'layout/widget_texts/monsters.png',
    'onlinelist.php' => 'layout/widget_texts/online.png',
    'market.php' => 'layout/widget_texts/market.png',
    'gallery.php' => 'layout/widget_texts/gallery.png',
    'helpdesk.php' => 'layout/widget_texts/helpdesk.png',
    'houses.php' => 'layout/widget_texts/houses.png',
    'downloads.php' => 'layout/widget_texts/downloads.png',
    'index.php' => 'layout/widget_texts/index.png',
    'shop.php' => 'layout/widget_texts/shopoffers.png'
);

// Get the requested subpage from the URL parameter
$subpage = isset($_GET['page']) ? $_GET['page'] : '';

// Append the .php extension if not already present
if (!empty($subpage) && !strpos($subpage, '.php')) {
    $subpage .= '.php';
}

// Check if a custom title is defined for the requested subpage
if (isset($page_titles[$subpage])) {
    define('CUSTOM_TITLE', $page_titles[$subpage]);
}

// Get the current script filename
$current_script = basename($_SERVER["SCRIPT_FILENAME"]);

// Check if a custom title is defined for the current script filename
if (isset($page_titles[$current_script])) {
    define('CUSTOM_TITLE', $page_titles[$current_script]);
}
?>
<!-- MAIN CONTENT -->
<div class="center_box">
    <div class="corner_lt"></div><div class="corner_rt"></div><div class="corner_lb"></div><div class="corner_rb"></div>
    <div class="title">
        <?php if (defined('CUSTOM_TITLE')): ?>
            <span style="background-image: url(<?php echo CUSTOM_TITLE; ?>);"></span>
        <?php else: ?>
            <span class="cufon" style="text-transform: uppercase;text-align: center;line-height: 43px;font-size: 16px;"><?php echo basename($_SERVER["SCRIPT_FILENAME"], '.php'); ?></span>
        <?php endif; ?>
    </div>
    <div class="content_bg">
        <div class="content">
            <div class="rise-up-content" style="min-height: 55px;">