<?php
  require 'lib/common.php';

  if($id=$_GET[id])
    checknumeric($id);
  else $id=0;

  pageheader("Edit Tokens");

  if(!acl("edit-tokens")) {
     print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    You do not have the permissions to do this.<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
     die();  
  }

  $rights=array();
  $q=$sql->query("SELECT * FROM rights");
  while($d=$sql->fetch($q)) $rights[$d[r]]=$d;

  function get_right_info($r) {
    global $rights;
    $re=explode(' ',$r);
    while($re[0]=="not"||$re[0][0]=='+') $re=array_slice($re,1);
    $ret=$rights[$re[0]];
    if($re[1]) {
      $desc=array( "u" => "User ", "t" => "Thread ", "f" => "Forum ", "c" => "Category" );
      $ret[title].=" for ";
      $ret[title].=$desc[$re[1][0]].substr($re[1],1);
    }
    return $ret;
  }

  if($action=="") {
    $r=$sql->query("SELECT * FROM tokens");
      
    $print="<a href=./>Main</a> - Edit Tokens<br><br>
".         "$L[TBL1]>
".         "  $L[TRh]>
".         "    $L[TDh]>ID
".         "    $L[TDh]>Icon
".         "    $L[TDh]>Name
".         "    $L[TDh]>Colors
".         "    $L[TDh]>Color precedence
".         "    $L[TDh]>&nbsp;
".         "  </tr>";

    $i=0;
    while($t=$sql->fetch($r)) {
      $print.=" ".(($i=!$i)?$L[TR3]:$L[TR2]).">
".              "  $L[TDc]>$t[id]
".              "  $L[TDc]>".($t[img]==""?"":"<img src='$t[img]'>")."
".              "  $L[TDc]>$t[name]
".              "  $L[TDc]><a href=#><font color='#$t[nc0]'>Male</font></a> <a href=#><font color='#$t[nc1]'>Female</font></a> <a href=#><font color='#$t[nc2]'>N/A</font></a>
".              "  $L[TDc]>$t[nc_prio]
".              "  $L[TDc]><a href='edittokens.php?action=edit&id=$t[id]'>Edit</a>
".              "</tr>";
    }
    
    $print.="$L[TBLend]";
    
    echo $print;
  } else if($action=="edit") {
    if($_POST[act]=="Update metadata") {
      $sql->query("UPDATE tokens SET id='$_POST[id]', name='".addslashes($name)."', img='".addslashes($img)."', nc0='$nc0', nc1='$nc1', nc2='$nc2', nc_prio='$nc_prio' WHERE id=$oldid");
      if($_POST[id]!=$oldid) {
        $sql->query("UPDATE tokenrights SET t='$_POST[id]' WHERE t='$oldid'");
        $sql->query("UPDATE usertokens SET t='$_POST[id]' WHERE t='$oldid'");
        $id=$_POST[id];
      }
    } else if($_POST[act]=="Add right") {
      $sql->query("INSERT INTO tokenrights VALUES('$id','".addslashes($_POST[right])."')");
    } else if($_GET[act]=="del") {
      $sql->query("DELETE FROM tokenrights WHERE t='$id' AND r='$right'");
    }
    
    $t=$sql->fetchq("SELECT * FROM tokens WHERE id=$id");

    print "<a href=./>Main</a> - <a href='edittokens.php'>Edit Tokens</a> - $t[name]<br><br>
".        "<form action='edittokens.php?action=edit&id=$id' method=post><input type=hidden name=oldid value=$t[id]> $L[TBL1]>
".        catheader("Token metadata")."
".        fieldrow("ID",fieldinput(4,4,"id",$t))."
".        fieldrow("Name",fieldinput(40,60,"name",$t))."
".        fieldrow("Image",fieldinput(40,60,"img",$t))."
".        fieldrow("Male(0) namecolour",fieldinput(6,6,"nc0",$t))."
".        fieldrow("Female(1) namecolour",fieldinput(6,6,"nc1",$t))."
".        fieldrow("N/A(2) namecolour",fieldinput(6,6,"nc2",$t))."
".        fieldrow("Namecolor precedence",fieldinput(6,6,"nc_prio",$t))."
".        catheader("&nbsp;")."
".        "$L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>$L[INPs]=act value='Update metadata'></td>
".        "$L[TBLend]</form>
";       

    $r=$sql->query("SELECT r FROM tokenrights tr WHERE tr.t='$id'");

    print "<form action='edittokens.php?action=edit&id=$id' method=post> $L[TBL1]>
".        "  $L[TRh]>
".        "    $L[TDh]>Right
".        "    $L[TDh]>Description
".        "    $L[TDh]>&nbsp;
"; 
    $i=0;
    while($d=$sql->fetch($r)) {
      $info=get_right_info($d[r]);
      print " ".(($i=!$i)?$L[TR3]:$L[TR2]).">
".          "  $L[TDc]>$info[title] ($d[r])
".          "  $L[TDc]>{$info[description]}
".          "  $L[TDc]><a href='edittokens.php?action=edit&id=$id&act=del&right=".urlencode($d[r])."'>revoke</a>
";
    }
    print "  $L[TR1]>
".        "    $L[TD1]>$L[INPt]=right style='width:100%' maxlength=60>
".        "    $L[TD1] colspan=2>$L[INPs]=act value='Add right'>
".        "$L[TBLend]</form>"; 
  }
  
  pagefooter();

  function catheader($title){
    global $L;
    return "  $L[TRh]>
".         "    $L[TDh] colspan=2>$title</td>";
  }

  function fieldrow($title,$input){
    global $L;
    return "  $L[TR]>
".         "    $L[TD1c]>$title:</td>
".         "    $L[TD2]>$input</td>";
  }

  function fieldinput($size,$max,$field,$t){
    global $L;
    return "$L[INPt]=$field size=$size maxlength=$max value=\"".str_replace("\"", "&quot;", $t[$field])."\">";
  }

?>
