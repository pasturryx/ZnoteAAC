<?php require_once 'engine/init.php'; define('CUSTOM_TITLE', 'layout/widget_texts/killers.png'); include 'layout/overall/header.php';
if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10' || $config['ServerEngine'] == 'OTHIRE') {
$cache = new Cache('engine/cache/killers');
if ($cache->hasExpired()) {
	$killers = fetchMurders();

	$cache->setContent($killers);
	$cache->save();
} else {
	$killers = $cache->load();
}
$cache = new Cache('engine/cache/victims');
if ($cache->hasExpired()) {
	$victims = fetchLoosers();

	$cache->setContent($victims);
	$cache->save();
} else {
	$victims = $cache->load();
}
$cache = new Cache('engine/cache/lastkillers');
if ($cache->hasExpired()) {
	$latests = mysql_select_multi("SELECT `p`.`name` AS `victim`, `d`.`killed_by` as `killed_by`, `d`.`time` as `time` FROM `player_deaths` as `d` INNER JOIN `players` as `p` ON d.player_id = p.id WHERE d.`is_player`='1' ORDER BY `time` DESC LIMIT 20;");
	if ($latests !== false) {
		$cache->setContent($latests);
		$cache->save();
	}
} else {
	$latests = $cache->load();
}
if ($killers) {
?>
 <div class="TableContainer" style="margin-top: 1cm; ">
        <div class="CaptionContainer">
            <div class="CaptionInnerContainer">
                <span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                <span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                <div class="Text">Biggest Murders</div>
                <span class="CaptionVerticalRight" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                <span class="CaptionBorderBottom" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                <span class="CaptionEdgeLeftBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionEdgeRightBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
            </div>
        <!-- </div> -->
<!-- <h1>Biggest Murders</h1> -->
  <table class="Table3" cellspacing="2">
            <tr>
                <td>
                    <div class="InnerTableContainer">
                        <table style="width:100%;">
                            <tr>
                                <td>
                                    <div class="TableShadowContainerRightTop">
                                        <div class="TableShadowRightTop" style="background-image:url(layout/tibia_img/table-shadow-rt.gif);"></div>
                                    </div>
                                    <div class="TableContentAndRightShadow" style="background-image:url(layout/tibia_img/table-shadow-rm.gif);">
                                        <div class="TableContentContainer">
                                            <table class="TableContent" width="100%" style="border:1px solid #faf0d7;">
                                                <tr>
<table id="killersTable" class="table table-striped">
	<tr class="yellow">
		<th>Name</th>
		<th>Kills</th>
	</tr>
	<?php foreach ($killers as $killer) {
		echo '<tr>';
		echo "<td width='70%'><a href='characterprofile.php?name=". $killer['killed_by'] ."'>". $killer['killed_by'] ."</a></td>";
		echo "<td width='30%'>". $killer['kills'] ."</td>";
		echo '</tr>';
	} ?>
</table>
</table>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="TableShadowContainer">
                                        <div class="TableBottomShadow" style="background-image:url(layout/tibia_img/table-shadow-bm.gif);">
                                            <div class="TableBottomLeftShadow" style="background-image:url(layout/tibia_img/table-shadow-bl.gif);"></div>
                                            <div class="TableBottomRightShadow" style="background-image:url(layout/tibia_img/table-shadow-br.gif);"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
      <!-- TERMINO -->
<?php 
} else 
    echo 'No player kills exist.';

if ($victims) {
?>
 <div class="TableContainer" style="margin-top: 1cm; ">
        <div class="CaptionContainer">
            <div class="CaptionInnerContainer">
                <span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                <span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                <div class="Text">Biggest Victims</div>
                <span class="CaptionVerticalRight" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                <span class="CaptionBorderBottom" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                <span class="CaptionEdgeLeftBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionEdgeRightBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
            </div>
        <!-- </div> -->
<!-- <h1>Biggest Victims</h1> -->
<table class="Table3" cellspacing="2">
            <tr>
                <td>
                    <div class="InnerTableContainer">
                        <table style="width:100%;">
                            <tr>
                                <td>
                                    <div class="TableShadowContainerRightTop">
                                        <div class="TableShadowRightTop" style="background-image:url(layout/tibia_img/table-shadow-rt.gif);"></div>
                                    </div>
                                    <div class="TableContentAndRightShadow" style="background-image:url(layout/tibia_img/table-shadow-rm.gif);">
                                        <div class="TableContentContainer">
                                            <table class="TableContent" width="100%" style="border:1px solid #faf0d7;">
                                                <tr>
<table id="victimsTable" class="table table-striped">
	<tr class="yellow">
		<th>Name</th>
		<th>Deaths</th>
	</tr>
	<?php foreach ($victims as $victim) {
		echo '<tr>';
		echo "<td width='70%'><a href='characterprofile.php?name=". $victim['name'] ."'>". $victim['name'] ."</a></td>";
		echo "<td width='30%'>". $victim['Deaths'] ."</td>";
		echo '</tr>';
	} ?>
</table>
</table>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="TableShadowContainer">
                                        <div class="TableBottomShadow" style="background-image:url(layout/tibia_img/table-shadow-bm.gif);">
                                            <div class="TableBottomLeftShadow" style="background-image:url(layout/tibia_img/table-shadow-bl.gif);"></div>
                                            <div class="TableBottomRightShadow" style="background-image:url(layout/tibia_img/table-shadow-br.gif);"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
      <!-- TERMINO -->
<?php 
} else {
    echo 'No player kills exist.';
}

if ($latests) {
?>
    <div class="TableContainer" style="margin-top: 1cm; margin-bottom: 1cm;">
        <div class="CaptionContainer">
            <div class="CaptionInnerContainer">
                <span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                <span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                <div class="Text">Latest kills</div>
                <span class="CaptionVerticalRight" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                <span class="CaptionBorderBottom" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                <span class="CaptionEdgeLeftBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionEdgeRightBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
            </div>
        </div>
        <table class="Table3" cellspacing="2">
            <tr>
                <td>
                    <div class="InnerTableContainer">
                        <table style="width:100%;">
                            <tr>
                                <td>
                                    <div class="TableShadowContainerRightTop">
                                        <div class="TableShadowRightTop" style="background-image:url(layout/tibia_img/table-shadow-rt.gif);"></div>
                                    </div>
                                    <div class="TableContentAndRightShadow" style="background-image:url(layout/tibia_img/table-shadow-rm.gif);">
                                        <div class="TableContentContainer">
                                            <table class="TableContent" width="100%" style="border:1px solid #faf0d7;">
                                                <tr>
                                                    <table id="killersTable" class="table table-striped">
                                                        <tr class="yellow">
                                                            <th>Killer</th>
                                                            <th>Time</th>
                                                            <th>Victim</th>
                                                        </tr>
                                                        <?php foreach ($latests as $last) { ?>
                                                            <tr>
                                                                <td width='35%'><a href='characterprofile.php?name=<?= $last['killed_by'] ?>'><?= $last['killed_by'] ?></a></td>
                                                                <td width='30%'><?= getClock($last['time'], true) ?></td>
                                                                <td width='35%'><a href='characterprofile.php?name=<?= $last['victim'] ?>'><?= $last['victim'] ?></a></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </table>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="TableShadowContainer">
                                        <div class="TableBottomShadow" style="background-image:url(layout/tibia_img/table-shadow-bm.gif);">
                                            <div class="TableBottomLeftShadow" style="background-image:url(layout/tibia_img/table-shadow-bl.gif);"></div>
                                            <div class="TableBottomRightShadow" style="background-image:url(layout/tibia_img/table-shadow-br.gif);"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
<?php
} else {
    echo 'No player kills exist.';
}

} else if ($config['ServerEngine'] == 'TFS_03') {
    $cache = new Cache('engine/cache/killers');
    if ($cache->hasExpired()) {
        $deaths = fetchLatestDeaths_03(30, true);
        $cache->setContent($deaths);
        $cache->save();
    } else {
        $deaths = $cache->load();
    }

    if ($deaths && !empty($deaths)) {
?>
        <h1>Latest Killers</h1>
        <table id="deathsTable" class="table table-striped">
            <tr class="yellow">
                <th>Killer</th>
                <th>Time</th>
                <th>Victim</th>
            </tr>
            <?php foreach ($deaths as $death) { ?>
                <tr>
                    <td><a href='characterprofile.php?name=<?= $death['killed_by'] ?>'><?= $death['killed_by'] ?></a></td>
                    <td><?= getClock($death['time'], true) ?></td>
                    <td>At level <?= $death['level'] ?>: <a href='characterprofile.php?name=<?= $death['victim'] ?>'><?= $death['victim'] ?></a></td>
                </tr>
            <?php } ?>
        </table>
<?php
    } else {
        echo 'No player deaths exist.';
    }
}
include 'layout/overall/footer.php'; 
?>