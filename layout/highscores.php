<?php require_once 'engine/init.php'; include 'layout/overall/header.php';

if ($config['log_ip']) {
    znote_visitor_insert_detailed_data(3);
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

if ($vocGroups) 
    $vocGroup = (is_array($vocGroups[$vocation])) ? $vocGroups[$vocation] : $vocGroups[$vocGroups[$vocation]];
    ?>

    <div class="TableContainer highlight" style="margin-top: 1cm;margin-bottom: 1cm; ">
    <table class="hoverTable">
    <div class="CaptionContainer">
            <div class="CaptionInnerContainer">
                <span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                <span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                    <div class="Text">Ranking for <?php echo skillName($type) .", ". (($vocation === 'all') ? 'any vocation' : vocation_id_to_name($vocation)) ?>.</div>
                <span class="CaptionVerticalRight" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                <span class="CaptionBorderBottom" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                <span class="CaptionEdgeLeftBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionEdgeRightBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
            </div>
        </div>
        <form action="" method="GET">
            <table class="Table3" cellspacing="1">
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
                                                        <td>
                                                            <select name="type">
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
                                                        </td>
                                                        <td>
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
                                                        </td>
                                                        <td>
                                                            <select name="page">
                                                                <?php
                                                                $pages = ($vocGroup[$type] !== false) ? ceil(min(($highscore['rows'] / $highscore['rowsPerPage']), (count($vocGroup[$type]) / $highscore['rowsPerPage']))) : 1;
                                                                for ($i = 0; $i < $pages; $i++) {
                                                                    $x = $i + 1;
                                                                    if ($x == $page) echo "<option value='".$x."' selected>Page: ".$x."</option>";
                                                                    else echo "<option value='".$x."'>Page: ".$x."</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <!-- <input type="submit" value=" View " class="btn btn-info"> -->
                                                            <input type="Submit" value="View" class="BigButton btn" style="background: url(layout/tibia_img/sbutton.gif); width:135px;height:25px;border: 0 none;" border="0">
                                                        </td>
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
</form>


<div class="TableContainer highlight" style="margin-top: 1cm;margin-bottom: 1cm; ">
        <div class="CaptionContainer">
            <div class="CaptionInnerContainer">
                <span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                <span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                <span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                <div class="Text">Highscores</div>
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
    <table id="highscoresTable" class="table table-striped table-hover">

        <tr class="yellow">

              <?php if ($loadOutfits) echo '<td style="background-color: #4f4f4f;">Outfit</td>'; ?>
                                                        <td style="background-color: #4f4f4f;"><strong>Rank</strong></td>
                                                        <td style="background-color: #4f4f4f;"><strong>Name</strong></td>
                                                        <td style="background-color: #4f4f4f;"><strong>Vocation</strong></td>
                                                        <td style="background-color: #4f4f4f;"><strong>Level</strong></td>
                                                        <?php if ($type === 7) echo '<td style="background-color: #4f4f4f;">Points</td>'; ?>
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
                    <tr class="highlight">
                    <?php if ($loadOutfits): ?>
                            <td class="outfitColumn"><img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo $vocGroup[$type][$i]['type']; ?>&addons=<?php echo $vocGroup[$type][$i]['addons']; ?>&head=<?php echo $vocGroup[$type][$i]['head']; ?>&body=<?php echo $vocGroup[$type][$i]['body']; ?>&legs=<?php echo $vocGroup[$type][$i]['legs']; ?>&feet=<?php echo $vocGroup[$type][$i]['feet']; ?>" alt="img"></strong></td>
                        <?php endif; ?>
                        <td><strong><?php echo $i+1; ?></strong></td>
                        <td><strong><?php echo $flag; ?><strong><a href="characterprofile.php?name=<?php echo $vocGroup[$type][$i]['name']; ?>"><?php echo $vocGroup[$type][$i]['name']; ?></a></strong></td>
                        <td><strong><?php echo vocation_id_to_name($vocGroup[$type][$i]['vocation']); ?></strong></td>
                        <td><strong><?php echo $vocGroup[$type][$i]['value']; ?></strong></td>
                        <?php if ($type === 7) echo "<td><strong>". $vocGroup[$type][$i]['experience'] ."</strong></td>"; ?>
                    </tr>
                    <tr class="highlight">
                                                                    <?php if ($loadOutfits): ?>
                            <td class="outfitColumn"><img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo $vocGroup[$type][$i]['type']; ?>&addons=<?php echo $vocGroup[$type][$i]['addons']; ?>&head=<?php echo $vocGroup[$type][$i]['head']; ?>&body=<?php echo $vocGroup[$type][$i]['body']; ?>&legs=<?php echo $vocGroup[$type][$i]['legs']; ?>&feet=<?php echo $vocGroup[$type][$i]['feet']; ?>" alt="img"></td>
                        <?php endif; ?>
                        <td><strong><?php echo $i+1; ?></strong></td>
                        <td><strong><?php echo $flag; ?><strong><a href="characterprofile.php?name=<?php echo $vocGroup[$type][$i]['name']; ?>"><?php echo $vocGroup[$type][$i]['name']; ?></a></strong></td>
                        <td><strong><?php echo vocation_id_to_name($vocGroup[$type][$i]['vocation']); ?></strong></td>
                        <td><strong><?php echo $vocGroup[$type][$i]['value']; ?></strong></td>
                       <?php if ($type === 7) echo "<td><strong>". $vocGroup[$type][$i]['experience'] ."</strong></td>"; ?>
                    </tr>
                    <?php
                }
            }
        }
        ?>
    </table>
    </table>
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

?>
<style>
    tr.highlight:hover td {
        background-color: orange;
    }
</style>
<?php
include 'layout/overall/footer.php';
?>