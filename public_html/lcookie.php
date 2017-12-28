<?php
  
  include('lib/common.php');

  if(!$log){
    needs_login(1);
  }

  if($_POST[action]=="update") {
    $err="";
    if(!preg_match("/^([0-9|.|,|\*]*)$/",$_POST[ranges]))
      $err="      Range string contains illegal characters.
".         "      <a href=''>Go back</a> or <a href='index.php'>give up</a>.";

    if($err){
      print "<a href=./>Main</a> - Error";
      noticemsg("Error", $err);
      pagefooter();
      die();
    }else{
      $_COOKIE[pass]=packlcookie(unpacklcookie($_COOKIE[pass]),$_POST[ranges]);
      setcookie('pass',$_COOKIE[pass],2147483647);
    }
  }

  pageheader('Advanced login cookie setup');

  $d=explode(",",decryptpwd($_COOKIE[pass]));

  $data.="$L[TBL1] style='width:200px!important'>
".       "  $L[TRh]>
".       "    $L[TDh] colspan=2>Current data
".       "  $L[TRh]>
".       "    $L[TDh]>Field
".       "    $L[TDh]>Value
".       "  $L[TR1]>
".       "    $L[TD1c]>generating IP
".       "    $L[TD2c]>$d[0]
".       "  $L[TR1]>
".       "    $L[TD1c]>password hash
".       "    $L[TD2c]><i>*snip*</i>";
  for($i=2;strlen($d[$i]);++$i) {
    $data.="  $L[TR1]>
".         "    $L[TD1c]>allowed range
".         "    $L[TD2c]>".$d[$i];
  }
  $data.="$L[TBLend]<br>";

  print "$data
".      "<form action='lcookie.php' method='post'>$L[INPh]='action' value='update'>
".      "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh]>Modify allowed ranges
".      "  $L[TR1]>
".      "    $L[TD2]>$L[INPt]='ranges' value='".implode(",",array_slice($d,2))."' style='width:80%'>$L[INPs] value='Update'>
".      "            <br><font class='sfont'>Data must be provided as comma-separated IPs without spaces,
".      "            each potentially ending in a single * wildcard. (e.g. <font color='#C0C020'>127.*,10.0.*,1.2.3.4</font>)
".      "            Faulty data might result in instant self-destruction of your login cookie.</font>
".      "$L[TBLend]</form>";

  pagefooter();

?>
