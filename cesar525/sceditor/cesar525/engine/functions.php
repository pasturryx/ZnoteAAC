<style>
.error{
    color: red;
    font-size: 31px;
}
 
</style>
<?php
function FROM_EDITOR_TO_SQL($content){
$value_content = str_replace("'","\'", $content);
return $value_content;
}


function FROM_SQL_TO_EDITOR($content){
$content_to_editor = str_replace("&", '&amp;', $content);
return $content_to_editor;
}
function clear(){
header("localhost");
}

function PROCESSING_POST($result){
    if($result == "empty"){
      echo'  <center><font class="error">Text are is empty.</font></center>';
    }
}

function insertTextSQL($text){
  $content_semicolon = str_replace("'","\'", $text);
  $content_comas =  str_replace(",","&#44;", $content_semicolon);
$content =  str_replace("$","&#36;", $content_comas);
return $content;
}
//POSTING CODE on DIV..
function code_Sql_to_editor($content){
$content_updated_one = str_replace('&', '&amp;', $content);
return $content_updated_one;
}
?>