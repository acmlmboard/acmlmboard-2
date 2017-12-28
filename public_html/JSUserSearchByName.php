<?PHP
  require 'lib/common.php';  
  if(isset($_GET['a'])) {
    $name=addslashes($_GET['a']); 
    $results=$sql->query("SELECT `name`, `id` FROM `users` WHERE `name` LIKE '".$name."%' ORDER BY `name`");
    while($line=$sql->fetch($results)) {
      echo $line['name']."¬".$line['id']."\n";
    }
  }
?>