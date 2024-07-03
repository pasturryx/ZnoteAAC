<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>


  <div class="TableContainer">
            <table class="Table4" cellpadding="0" cellspacing="0">
                <div class="CaptionContainer">
                    <div class="CaptionInnerContainer">
                        <span class="CaptionEdgeLeftTop" style="background-image:url(images/buttons/content/box-frame-edge.gif)"/></span>
                        <span class="CaptionEdgeRightTop" style="background-image:url(images/buttons/content/box-frame-edge.gif)"/></span>
                        <span class="CaptionBorderTop" style="background-image:url(images/buttons/content/table-headline-border.gif)"></span>
                        <span class="CaptionVerticalLeft" style="background-image:url(images/buttons/content/box-frame-vertical.gif)"/></span>
                        <div class="Text">Download Client</div>
                        <span class="CaptionVerticalRight" style="background-image:url(images/buttons/content/box-frame-vertical.gif)"/></span>
                        <span class="CaptionBorderBottom" style="background-image:url(images/buttons/content/table-headline-border.gif)"></span>
                        <span class="CaptionEdgeLeftBottom" style="background-image:url(images/buttons/content/box-frame-edge.gif)"/></span>
                        <span class="CaptionEdgeRightBottom" style="background-image:url(images/buttons/content/box-frame-edge.gif)"/></span>
                    </div>
                </div>
                <tr>
                    <td>
                        <div class="InnerTableContainer">
                            <table style="width:100%">
                                <tr>
                                    <td>
                                        <table width="100%" cellpadding=0 cellspacing=0>
                                            <tr>
                                                <td style="vertical-align:top">
                                                    <div class="TableShadowContainerRightTop">
                                                        <div class="TableShadowRightTop" style="background-image:url(images/buttons/content/table-shadow-rt.gif)"></div>
                                                    </div>
                                                    <div class="TableContentAndRightShadow" style="background-image:url(images/buttons/content/table-shadow-rm.gif)">
                                                        <div class="TableContentContainer">
                                                            <table class="TableContent" width="100%">
                                                                <tr>
                                                                    <td>
                                                                        <table style="width:100%;text-align:center">
                                                                            <tr>
                                                                                <td><a type="application/octet-stream" target="_top"><img style="width:140px;height:140px;border:0px" src="images/account/download_windows.png"/></a></td>
                                                                            </tr>
                                                                            <tr>
                                                                            <td valign="top">
                                                                                <?php
                                                                                    $download_links = array(
                                                                                       "updater" => "Forgotten-Win.rar",
                                                                                       // "netcore2" => "Forgotten-Win-updater.rar",
                                                                                        //"netcore" => "ForgottenNOT-BOT.rar",
                                                                                        "netcore1" => "ForgottenNot.apk",
                                                                                    );

                                                                                    $download_url = false;
                                                                                    if (isset($_GET['client']) && isset($download_links[$_GET['client']])) {
                                                                                      $download_url = $download_links[$_GET['client']];
                                                                                      header("Location: {$download_url}");
                                                                                      die();
                                                                                    }
                                                                                    ?>

                                                                                    <form action="" method="GET" target="_blank">
                                                                                      <label for="client"></label>
                                                                                      <select id="client" name="client">
                                                                                        <optgroup label="Select Download:">
                                                                                        <option value="updater">Forgotten Client</option>
                                                                                         <!-- <option value="netcore2">Forgotten Updater-Win</option> -->
                                                                                        <!-- <option value="netcore">Forgotten BOT Client</option>  -->
                                                                                        <option value="netcore1">Forgotten Android</option> 
                                                                                        <!-- <option value="netcore">.NETCORE</option> -->
                                                                                      </select>
                                                                                      <br><br>
                                                                                      <input type="submit" value="Submit" div class="BigButton btn" style="background: url(layout/tibia_img/sbutton.gif); width:135px;height:25px;border: 0 none;" border="0">
                                                                                      <!-- <input type="submit" value="Submit"> -->
                                                                                    </form>
                                                                                <br/>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="TableShadowContainer">
                                                        <div class="TableBottomShadow" style="background-image:url(images/buttons/content/table-shadow-bm.gif)">
                                                            <div class="TableBottomLeftShadow" style="background-image:url(images/buttons/content/table-shadow-bl.gif)"></div>
                                                            <div class="TableBottomRightShadow" style="background-image:url(images/buttons/content/table-shadow-br.gif)"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="vertical-align:top"><div class="TableShadowContainerRightTop">
                                                        <div class="TableShadowRightTop" style="background-image:url(images/buttons/content/table-shadow-rt.gif)"></div>
                                                    </div>
                                                    <div class="TableContentAndRightShadow" style="background-image:url(images/buttons/content/table-shadow-rm.gif)">
                                                        <div class="TableContentContainer">
                                                            <table class="TableContent" width="100%">
                                                                <tr>
                                                                    <td style="text-align:center"><img style="width:214px;height:188px;margin:17px" src="images/account/successful_download.jpg"/></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="TableShadowContainer">
                                                        <div class="TableBottomShadow" style="background-image:url(images/buttons/content/table-shadow-bm.gif)">
                                                            <div class="TableBottomLeftShadow" style="background-image:url(images/buttons/content/table-shadow-bl.gif)"></div>
                                                            <div class="TableBottomRightShadow" style="background-image:url(images/buttons/content/table-shadow-br.gif)"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    <tr>
                                    <td>
                                        <div class="TableShadowContainerRightTop">
                                            <div class="TableShadowRightTop" style="background-image:url(images/buttons/content/table-shadow-rt.gif)"></div>
                                        </div>
                                        <div class="TableContentAndRightShadow" style="background-image:url(images/buttons/content/table-shadow-rm.gif)">
                                            <div class="TableContentContainer">
                                                <table class="TableContent" width="100%">
                                                    <tr>


                                                        <td class="LabelV">Disclaimer</td>


                                                    <!-- </tr> -->
                                       
                                                        <head>
    <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap" rel="stylesheet">
</head>

<body>
    <table>
        <tr>
            <td style="font-weight: bold; color: red; font-size: 18px; font-family: 'MedievalSharp', cursive;">
                You need to download <span style="color: blue;">vc_redist86x</span> in order to use our client in windows. If you <span style="color: blue;">DON'T HAVE IT INSTALLED YET</span>, PLEASE DO IT.
            </td>
            <td>
                <center>
                    <input type="submit" onclick="location.href='vc_redist.x86.exe';" value="vc_redist.x86.exe" class="BigButton btn" style="margin: 0 5px;display: inline-block;background-image:url(layout/tibia_img/sbutton.gif);">
                </center>
            </td>
        </tr>
    </table>
</body>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <p></p>
                                        <div class="TableShadowContainer">
                                            <div class="TableBottomShadow" style="background-image:url(images/buttons/content/table-shadow-bm.gif)">
                                                <div class="TableBottomLeftShadow" style="background-image:url(images/buttons/content/table-shadow-bl.gif)"></div>
                                                <div class="TableBottomRightShadow" style="background-image:url(images/buttons/content/table-shadow-br.gif)"></div>
                                            </div>
                                           <iframe src="https://discord.com/widget?id=1068695350719807568&theme=dark" width="350" height="500" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe>
                                        </div>
                                        <p></p>
                                        <div class="TableContentAndRightShadow" style="background-image:url(images/buttons/content/table-shadow-rm.gif)">
                                            <div class="TableContentContainer">
                                                <table class="TableContent" width="100%">
                                                    <tr>
                                                        <td class="LabelV">Support</td>
                                                    
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </table>
                </div>
            </td>
        </tr>
      
<?php
include 'layout/overall/footer.php'; ?>
