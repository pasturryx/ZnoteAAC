<style>
.cesar-backtoforum{
font-family: anton;
color: #faeeee;
}

.cesar-backtoforum:hover{
font-family: anton;
color: #d2d800;
}


</style>

<?php
function request_feedback($request){
$style = 'style="background-color: black;padding: 11px;border: solid 1px #4e4e4e;border-radius: 21px;margin-bottom: 7px;"';
$back_to_board_style = 'style="color:yellow;"';

switch($request){
case "deletedpost": echo '<center '.$style.'><font color="green" style="font-family:lato;">Post has been successfully <font color="red">DELETED.</font></font></center>';
break;
case "postedpost" : echo '<center '.$style.'><font color="green" style="font-family:lato;">Post has been successfully POSTED.</font></center>';
break;
case "boarddeleted" :  echo '<center '.$style.'><font color="green" style="font-family:lato;">Board has been successfully Deleted.</font>&nbsp;<a href="forum.php" class="cesar-backtoforum"><font>Back to Board</font></a></center>';
break;
case "boardupdated" :  echo '<center '.$style.'><font color="green" style="font-family:lato;">Board has been successfully updated.</font>&nbsp;<a href="forum.php" class="cesar-backtoforum"><font>Back to Board</font></a></center>';
break;
case "boardadded" :  echo '<center '.$style.'><font color="green" style="font-family:lato;">Board has been successfully added.</font>&nbsp;<a href="forum.php" class="cesar-backtoforum"><font>Back to Board</font></a></center>';
break;
}



}




?>