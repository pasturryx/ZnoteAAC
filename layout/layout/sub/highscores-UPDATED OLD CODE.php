<?php require_once 'engine/init.php';

if ($config['log_ip']) {
	znote_visitor_insert_detailed_data(3);
}

function fetchAllScoresh($rows, $tfs, $g, $vlist, $v = -1, $flags = false, $outfits = false) {
	if (config('ServerEngine') !== 'OTHIRE') {
		if (config('client') < 780) {
			$outfits = ($outfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`" : "";
		} else {
			$outfits = ($outfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`, `p`.`lookaddons` AS `addons`" : "";
		}
	} else {
		$outfits = ($outfits) ? ", `p`.`lookbody` AS `body`, `p`.`lookfeet` AS `feet`, `p`.`lookhead` AS `head`, `p`.`looklegs` AS `legs`, `p`.`looktype` AS `type`" : "";
	}
	// Return scores ordered by type and vocation (if set)

$data = array();

	// Add "all" as a simulated vocation in vocation_list to represent all vocations and loop through them.
	$vocGroups = array(
		'all' => array()
	);
	foreach ($vlist AS $vid => $vdata) {
		// If this vocation does not have a fromVoc attribute
		if ($vdata['fromVoc'] === false) {
			// Add it as a group
			$vocGroups[(string)$vid] = array();
		} else {
			// Add an extended group for both vocations
			$sharedGroup = (string)$vdata['fromVoc'] . ", " . (string)$vid;
			$vocGroups[$sharedGroup] = array();

			// Make the fromVoc group a reference to the extended group for both vocations
			$vocGroups[(string)$vdata['fromVoc']] = $sharedGroup;
			$vocGroups[(string)$vid] = $sharedGroup;
		}
	}

	foreach ($vocGroups as $voc_id => $key_or_arr) {

		$vGrp = $voc_id;
		// Change to correct vocation group if this vocation id reference a shared vocation group
		if (!is_array($key_or_arr)) $vGrp = $key_or_arr;

		// If this vocation group is empty (then we need to fill it with highscore SQL Data)
		if (empty($vocGroups[$vGrp])) {

			// Generate SQL WHERE-clause for vocation if $v is set
			$v = '';
			if ($vGrp !== 'all')
				$v = (strpos($vGrp, ',') !== false) ? 'AND `p`.`vocation` IN ('. $vGrp . ')' : 'AND `p`.`vocation` = \''.intval($vGrp).'\'';

			elseif ($tfs == 'TFS_10') {
if ($flags === false) { // In this case we only need to query players table
					$v = str_replace('`p`.', '', $v);
					$outfits = str_replace('`p`.', '', $outfits);

					$vocGroups[$vGrp][1] = mysql_select_multi("SELECT `name`, `vocation`, `skill_club` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_club` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][2] = mysql_select_multi("SELECT `name`, `vocation`, `skill_sword` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_sword` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][3] = mysql_select_multi("SELECT `name`, `vocation`, `skill_axe` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_axe` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][4] = mysql_select_multi("SELECT `name`, `vocation`, `skill_dist` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_dist` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][5] = mysql_select_multi("SELECT `name`, `vocation`, `skill_shielding` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_shielding` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][6] = mysql_select_multi("SELECT `name`, `vocation`, `skill_fishing` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_fishing` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][7] = mysql_select_multi("SELECT `name`, `vocation`, `experience`, `level` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `experience` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][8] = mysql_select_multi("SELECT `name`, `vocation`, `maglevel` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `maglevel` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][9] = mysql_select_multi("SELECT `name`, `vocation`, `skill_fist` AS `value` $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `skill_fist` DESC LIMIT 0, $rows;");

				} else { // Inner join znote_accounts table to retrieve the flag
					$vocGroups[$vGrp][1] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_club` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_club` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][2] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_sword` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_sword` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][3] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_axe` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_axe` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][4] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_dist` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_dist` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][5] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_shielding` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_shielding` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][6] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_fishing` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_fishing` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][7] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`experience`, `level` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`experience` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][8] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`maglevel` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`maglevel` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][9] = mysql_select_multi("SELECT `p`.`name`, `p`.`vocation`, `p`.`skill_fist` AS `value`, `za`.`flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`skill_fist` DESC LIMIT 0, $rows;");
}
			} else { // TFS 0.2, 0.3, 0.4

				if ($flags === false) {
					$vocGroups[$vGrp][9] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 0 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][1] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 1 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][2] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 2 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][3] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 3 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][4] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 4 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][5] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 5 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][6] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation` $outfits FROM `player_skills` AS `s` LEFT JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` WHERE `s`.`skillid` = 6 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$v = str_replace('`p`.', '', $v);
					$outfits = str_replace('`p`.', '', $outfits);
					$vocGroups[$vGrp][7] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `experience`, `level` AS `value` $outfits $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `experience` DESC limit 0, $rows;");
					$vocGroups[$vGrp][8] = mysql_select_multi("SELECT `id`, `name`, `vocation`, `maglevel` AS `value` $outfits $outfits FROM `players` WHERE `group_id` < $g $v ORDER BY `maglevel` DESC limit 0, $rows;");

				} else { // Inner join znote_accounts table to retrieve the flag
					$vocGroups[$vGrp][9] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 0 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][1] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 1 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][2] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 2 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][3] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 3 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][4] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 4 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][5] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 5 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][6] = mysql_select_multi("SELECT `s`.`player_id` AS `id`, `s`.`value` AS `value`, `p`.`name` AS `name`, `p`.`vocation` AS `vocation`, `za`.`flag` AS `flag` $outfits FROM `player_skills` AS `s` INNER JOIN `players` AS `p` ON `s`.`player_id`=`p`.`id` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`  WHERE `s`.`skillid` = 6 AND `p`.`group_id` < $g $v ORDER BY `s`.`value` DESC LIMIT 0, $rows;");
					$vocGroups[$vGrp][7] = mysql_select_multi("SELECT `p`.`id`, `p`.`name`, `p`.`vocation`, `p`.`experience`, `p`.`level` AS `value`, `za`.`flag` AS `flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`experience` DESC limit 0, $rows;");
					$vocGroups[$vGrp][8] = mysql_select_multi("SELECT `p`.`id`, `p`.`name`, `p`.`vocation`, `p`.`maglevel` AS `value`, `za`.`flag` AS `flag` $outfits FROM `players` AS `p` INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id` WHERE `p`.`group_id` < $g $v ORDER BY `p`.`maglevel` DESC limit 0, $rows;");
				}
			}
		}
	}
	return $vocGroups;
}
// Fetch highscore type
$type = (isset($_GET['type'])) ? (int)getValue($_GET['type']) : 7;
if ($type > 9) $type = 7;

// Fetch highscore vocation
$configVocations = $config['vocations'];
//$debug['configVocations'] = $configVocations;

$vocationIds = array_keys($configVocations);

$vocation = 'all';
if (isset($_GET['vocation']) && is_numeric($_GET['vocation'])) {
	$vocation = (int)$_GET['vocation'];
	if (!in_array($vocation, $vocationIds)) {
		$vocation = "all";
	}
}

// Fetch highscore page
$page = getValue(@$_GET['page']);
if (!$page || $page == 0) $page = 1;
else $page = (int)$page;

$highscore = $config['highscore'];
$loadFlags = ($config['country_flags']['enabled'] && $config['country_flags']['highscores']) ? true : false;
$loadOutfits = ($config['show_outfits']['highscores']) ? true : false;

$rows = $highscore['rows'];
$rowsPerPage = $highscore['rowsPerPage'];
// Fetch highscore type
// $type = (isset($_GET['type'])) ? (int)getValue($_GET['type']) : 7;
// if ($type > 9) $type = 7;

// Fetch highscore vocation
// $vocation = (isset($_GET['vocation'])) ? (int)getValue($_GET['vocation']) : -1;
// if ($vocation > 8) $vocation = -1;

// Fetch highscore page
// $page = getValue(@$_GET['pag']);
// if (!$page || $page == 0) $page = 1;
// else $page = (int)$page;

// $highscore = $config['highscore'];

// $rows = $highscore['rows'];
// $rowsPerPage = $highscore['rowsPerPage'];


function skillName($type) {
	$types = array(
		1 => "Club",
		2 => "Sword",
		3 => "Axe",
		4 => "Distance",
		5 => "Shield",
		6 => "Fish",
		7 => "Experience", // Hardcoded
		8 => "Magic Level", // Hardcoded
		9 => "Fist", // Since 0 returns false I will make 9 = 0. :)
	);
	return $types[(int)$type];
}

function pageCheck($index, $page, $rowPerPage) {
	return ($index < ($page * $rowPerPage) && $index >= ($page * $rowPerPage) - $rowPerPage) ? true : false;
}

$cache = new Cache('engine/cache/highscores');
if ($cache->hasExpired()) {
	$vocGroups = fetchAllScores($rows, $config['ServerEngine'], $highscore['ignoreGroupId'], $configVocations, $vocation, $loadFlags, $loadOutfits);
	$cache->setContent($vocGroups);
	$cache->save();
} else {
	$vocGroups = $cache->load();
}

if ($vocGroups) {
	$vocGroup = (is_array($vocGroups[$vocation])) ? $vocGroups[$vocation] : $vocGroups[$vocGroups[$vocation]];
	?>
		<style>
			.newstext img
			{
				max-width: 562px;
				height: auto;
				margin: 0 5px;
			}
			.pagination
			{
				text-align: center;
				margin: 10px 0;
			}
			.pagination a
			{
				display: inline-block;
				color: #52412f;
				font-size: 14px;
				line-height: 24px;
				height: 24px;
				width: 24px;
				font-weight: bold;
				text-align: center;
				border: 1px solid #886a4d;
				margin: 0 5px 0 0;
				border-radius: 24px;
				box-shadow: 0 2px 3px rgb(255, 214, 175) inset;
				background: #dcaf83;
				transition: opacity 0.1s linear;
			}
			.pagination a:hover
			{
				opacity: 0.7;
			}
			.pagination a.current
			{
				background: #ad1a1a;
				color: #fff;
				border: 1px solid #000000;
				box-shadow: 0 2px 3px rgb(255, 62, 62) inset;
			}
		</style>
		
	<form type="submit" action="" method="GET">
	<br>
	<center>Skill: <input type="hidden" name="page" value="highscores"/>
		<select style="vertical-align:middle;" name="type">
			<option value="7" <?php if ($type == 7) echo "selected"; ?>>Experience</option>
			<option value="8" <?php if ($type == 8) echo "selected"; ?>>Magic</option>
			<option value="5" <?php if ($type == 5) echo "selected"; ?>>Shield</option>
			<option value="2" <?php if ($type == 2) echo "selected"; ?>>Sword</option>
			<option value="1" <?php if ($type == 1) echo "selected"; ?>>Club</option>
			<option value="3" <?php if ($type == 3) echo "selected"; ?>>Axe</option>
			<option value="4" <?php if ($type == 4) echo "selected"; ?>>Distance</option>
			<option value="6" <?php if ($type == 6) echo "selected"; ?>>Fish</option>
			<option value="9" <?php if ($type == 9) echo "selected"; ?>>Fist</option>
		</select>

		<select name="vocation">
			<option value="all" <?php if (!is_int($vocation)) echo "selected"; ?>>Any vocation</option>
			<?php
			foreach ($configVocations as $v_id => $v_data) {
				if ($v_data['fromVoc'] === false) {
					$selected = (is_int($vocation) && $vocation == $v_id) ? " selected $vocation = $v_id" : "";
					echo '<option value="'. $v_id .'"'. $selected .'>'. $v_data['name'] .'</option>';
				}
			}
			?>
		</select>
		
		 <input type="submit" class="hover" value=""
    style="background-image: url(layout/tibia_img/sbutton_submit.gif); vertical-align:middle; border: solid 0px #000000; width: 120px; height: 18px;" />
		</center>
	</form>
	
	
		<br><center><span class="pagination">
			<?php
			$pages = ($vocGroup[$type] !== false) ? ceil(min(($highscore['rows'] / $highscore['rowsPerPage']), (count($vocGroup[$type]) / $highscore['rowsPerPage']))) : 1;
			for ($i = 0; $i < $pages; $i++) {
				$x = $i + 1;
				if ($x == $page) echo "<option value='".$x."' selected>Page: ".$x."</option>";
				else echo "<option value='".$x."'>Page: ".$x."</option>";
			}
			?>
		</span></center>
		
		
	<table id="highscoresTable" cellpadding="4" class="stripped">
		<tr>	
			<th colspan="7">Ranking for <?php echo skillName($type) .", ". (($vocation < 0) ? 'any vocation' : vocation_id_to_name($vocation)) ?></th>
		</tr>
		<tr>
			<td width="8%"><center>#</center></td>
			<td width="8%"><center><strong>outfit</strong></center></td>
			<?php if ($config['country_flags'] === true) echo'<td width="5%"><center><strong>flag</strong></center></td>'; ?>
			<td><strong>Name</strong></td>
			<td><center><strong>Vocation</strong></center></td>
			<td width="15%"><center><strong>Level</strong></center></td>
			<?php if ($type === 7) echo "<td width=\"15%\"><center><strong>Points</strong></center></td>"; ?>
		</tr>
		<?php
		if ($vocGroup[$type] === false) {
			?>
			<tr>
				<td colspan="5">Nothing to show here yet.</td>
			</tr>
			<?php
		} else {
			for ($i = 0; $i < count($vocGroup[$type]); $i++) {
				if (pageCheck($i, $page, $rowsPerPage)) {
					$flag = ($loadFlags === true && strlen($vocGroup[$type][$i]['flag']) > 1) ? '<img src="' . $config['country_flags']['server'] . '/' . $vocGroup[$type][$i]['flag'] . '.png">  ' : '';
					?>
					<tr>
						<td><center><?php echo $i+1; ?>.</center></td>
						<td>
						<!-- >?php -->
							<!-- echo '<div style="position:relative; left:-32px; top:-32px;width: 32px;height:32px;"> -->
							<!-- <div style="background-image: url(layout/outfitter/outfit.php?id='.$scores[$type][$i]['looktype'].'&head='.$scores[$type][$i]['lookhead'].'&body='.$scores[$type][$i]['lookbody'].'&legs='.$scores[$type][$i]['looklegs'].'&feet='.$scores[$type][$i]['lookfeet'].'); -->
								<!-- width:64px;height:64px;position:absolute;background-repeat:no-repeat;background-position:right bottom;"> -->
							<!-- </div>'; -->
						<!-- ?> -->
					
						</td>
						<?php echo $flag; ?>
						<td><strong><a href="characterprofile.php?name=<?php echo $scores[$type][$i]['name']; ?>"><?php echo $scores[$type][$i]['name']; ?></a></strong></td>
						<td><center><?php echo vocation_id_to_name($vocGroup[$type][$i]['vocation']); ?></center></td>
						<td><center><?php echo $scores[$type][$i]['value']; ?></center></td>
						<?php if ($type === 7) echo "<td><center>". $vocGroup[$type][$i]['experience'] ."</center></td>"; ?>
					</tr>
					<?php
				}
			}
		}
		?>
	</table>
		<center><span class="pagination">
			<?php
			$pages = ceil(min(($highscore['rows'] / $highscore['rowsPerPage']), (count($vocGroup[$type]) / $highscore['rowsPerPage'])));
			for ($i = 0; $i < $pages; $i++) {
				$x = $i + 1;
				if ($x == $page) echo "<a href=\"?".$_SERVER['QUERY_STRING']."&pag=".$x."\" class=\"current\">".$x."</a>";
				else echo "<a  href=\"?".$_SERVER['QUERY_STRING']."&pag=".$x."\">".$x."</a>";
			}
			?>
		</span></center>
	<?php
}
?>
