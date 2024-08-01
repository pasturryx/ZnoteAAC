<?php

//easy database access
function query($sql, $connect){
$result = mysqli_query($connect, $sql);
if($result){
    return $result;
}else{
    echo 'ERROR'.mysqli_error($connect);
}
}

function thousand_format($number) {
    $number = (int) preg_replace('/[^0-9]/', '', $number);
    if ($number >= 1000) {
        $rn = round($number);
        $format_number = number_format($rn);
        $ar_nbr = explode(',', $format_number);
        $x_parts = array('K', 'M', 'B', 'T', 'Q');
        $x_count_parts = count($ar_nbr) - 1;
        $dn = $ar_nbr[0] . ((int) $ar_nbr[1][0] !== 0 ? '.' . $ar_nbr[1][0] : '');
        $dn .= $x_parts[$x_count_parts - 1];

        return $dn;
    }
    return $number;
}


?>