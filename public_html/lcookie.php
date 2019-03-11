<?php
  
  include('lib/common.php');

  if(!$log){
    needs_login(1);
  }

  if(checkvar('_POST','action')=="update") {
    $err="";
    if(!preg_match("/^([0-9|.|,|\*]*)$/",$_POST['ranges']))
      $err="      Range string contains illegal characters.
".         "      <a href=''>Go back</a> or <a href='index.php'>give up</a>.";

    if($err){
      print "<a href=./>Main</a> - Error";
      noticemsg("Error", $err);
      pagefooter();
      die();
    }else{
      switch($_POST['duration']){
        case 1: $dstr=strtotime('+1 Week'); break;
        case 2: $dstr=strtotime('+1 Month'); break;
        case 3: $dstr=strtotime('+1 Year'); break;
        case 4: $dstr=2147483647; break; //Legacy value
        default: $dstr=0;
      }
      $_COOKIE['pass']=packlcookie(unpacklcookie($_COOKIE['pass']),$_POST['ranges']);
      setcookie('user',$_COOKIE['user'],$dstr);
      setcookie('pass',$_COOKIE['pass'],$dstr);
    }
  }

  pageheader('Advanced login cookie setup');

  $d=explode(",",decryptpwd($_COOKIE['pass']));

  $data="$L[TBL1] style='width:200px!important'>
".       "  $L[TRh]>
".       "    $L[TDh] colspan=2>Current data
".       "  $L[TRh]>
".       "    $L[TDh]>Field
".       "    $L[TDh]>Value
".       "  $L[TR1]>
".       "    $L[TD1c]>Current IP
".       "    $L[TD2c]>$d[0]";
  for($i=2;strlen(checkvar('d',$i));++$i) {
    $data.="  $L[TR1]>
".         "    $L[TD1c]>Current range
".         "    $L[TD2c]>".checkvar('d',$i);
  }
  $data.="$L[TBLend]<br>";

  print "$data
".      "<form action='lcookie.php' method='post'>$L[INPh]='action' value='update'>
".      "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] colspan=2>Modify allowed ranges
".      "  $L[TR1]>
".      "    $L[TD1]>Range
".      "    $L[TD2]>$L[INPt]='ranges' value='".implode(",",array_slice($d,2))."' style='width:120px'>
".      "  $L[TR1]>
".           fieldrow('Duration', fieldoption('duration',0,array(0=> 'Session', '1 Week', '1 Month', '1 Year')))."
".      "  $L[TR1]>
".      "    $L[TD1]>
".      "    $L[TD2]>
".      "            <span class='sfont'>Data must be provided as comma-separated IPs without spaces,
".      "            each potentially ending in a single * wildcard. (e.g. <span style='color:#C0C020;'>127.*,10.0.*,1.2.3.4</span>)<br>
".      "            Incorrect data will instantly log you out.</span><br>
".      "  $L[INPs] value='Update'>
".      "$L[TBLend]</form>";

  pagefooter();

?>
