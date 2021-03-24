<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>
<?php
if(isset($title)){
    echo $title;
} else {
    echo "RT framework";
}
?>
</title>
</head>
<body>
<?php 

$tplfile = $tpl.".php" ;
if( !file_exists($tplfile)) {
    echo "<h3>view $tpl not exists</h3>";
} else {
    include $tplfile;
}
?>
</body>
</html>