<?
# Easy Hosting Control Panel (ehcp)

require("classapp.php");

$app = new Application();
$app->connectTodb(); # fill config.php with db user/pass for things to work..


$ret=$app->fixMailConfiguration();

if($ret){
    echo "Success";
} else {
    echo $app->output;
} 

echo "($ret)";

?>
