<?php
require_once 'engine/init.php';
define('CUSTOM_TITLE', 'layout/widget_texts/deaths.png');
include 'layout/overall/header.php';

$cache = new Cache('engine/cache/deaths');
if ($cache->hasExpired()) {
    if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10') {
        $deaths = fetchLatestDeaths();
    } else if ($config['ServerEngine'] == 'TFS_03' || $config['ServerEngine'] == 'OTHIRE') {
        $deaths = fetchLatestDeaths_03(30);
    }
    $cache->setContent($deaths);
    $cache->save();
} else {
    $deaths = $cache->load();
}

if ($deaths) {
?>

<div class="TableContainer">
    <div class="TableContainer" style="margin-top: 1cm;margin-bottom: 1cm; ">
    <div class="CaptionContainer">
        <div class="CaptionInnerContainer">
            <span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
            <span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
            <span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
            <span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                <div class="Text">Latest Deaths</div>
            <span class="CaptionVerticalRight" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
            <span class="CaptionBorderBottom" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
            <span class="CaptionEdgeLeftBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
            <span class="CaptionEdgeRightBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
        </div>
    </div>
</div>

<table id="deathsTable" class="table table-striped">
    <?php foreach ($deaths as $death) { ?>
        <tr>
            <td colspan="3">
                <div class="TableContainer">
                    <div class="TableContainer" style="margin-top: 1cm;margin-bottom: 1cm; ">
                    <div class="CaptionContainer">
                        <div class="CaptionInnerContainer">
                            <span class="CaptionEdgeLeftTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                            <span class="CaptionEdgeRightTop" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                            <span class="CaptionBorderTop" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                            <span class="CaptionVerticalLeft" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                                <div class="Text">Victim: <a href='characterprofile.php?name=<?php echo $death['victim']; ?>'><?php echo $death['victim']; ?></a></div>
                            <span class="CaptionVerticalRight" style="background-image:url(layout/tibia_img/box-frame-vertical.gif);"></span>
                            <span class="CaptionBorderBottom" style="background-image:url(layout/tibia_img/table-headline-border.gif);"></span>
                            <span class="CaptionEdgeLeftBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                            <span class="CaptionEdgeRightBottom" style="background-image:url(layout/tibia_img/box-frame-edge.gif);"></span>
                        </div>
                    </div>
                    <table class="Table3" cellspacing="1">
                        <tr>
                            <td><strong>Level:</strong></td>
                            <td><?php echo $death['level']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Time:</strong></td>
                            <td><?php echo getClock($death['time'], true); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Killer:</strong></td>
                            <td>
                                <?php if ($death['is_player'] == 1) { ?>
                                    Player: <a href='characterprofile.php?name=<?php echo $death['killed_by']; ?>'><?php echo $death['killed_by']; ?></a>
                                <?php } else if ($death['is_player'] == 0) { ?>
                                    Monster: <?php echo ucfirst($death['killed_by']); ?>
                                <?php } else { ?>
                                    <?php echo $death['killed_by']; ?>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    <?php } ?>
</table>

<?php
} else {
    echo 'No deaths exist.';
}

include 'layout/overall/footer.php';
?>
