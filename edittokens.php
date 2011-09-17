<?php
  require 'lib/common.php';

  if($id=$_GET[id])
    checknumeric($id);
  else $id=0;

  if(!acl("edit-tokens")) {
     print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    You do not have the permissions to do this.<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
     die();  
  }

  if($action="") {
    $r=$sql->fetchq("SELECT * FROM tokens");
      
    $print="<a href=./>Main</a> - Edit Tokens<br><br>
".         "$L[TBL1]>
".         "  $L[TRh]>
".         "    $L[TDh]>ID
".         "    $L[TDh]>Icon
".         "    $L[TDh]>Name
".         "    $L[TDh]>Colours
".         "    $L[TDh]>Colour precedence
".         "    $L[TDh]>&nbsp;
".         "  </tr>";

    while($t=$sql->fetch($r)) {
      $print.=" ".(($i=!$i)?$L[TR3]:$L[TR2]).">
".              "  $L[TDc]>$t[id]
".              "  $L[TDc]>".$t[img]==""?"":"<img src='$t[img]'>"."
".              "  $L[TDc]>$t[name]
".              "  $L[TDc]><a href=#><font color='#$t[nc0]'>Masculine</font></a> <a href=#>font color='#$t[nc1]'>Feminine</font></a> <a href=#><font color='#$t[nc2]'>Neuter</font></a>
".              "  $L[TDc]>$t[nc_prio]
".              "  $L[TDc]><a href='edittokens.php?action=edit&id=$t[id]'>Edit</a>
".              "</tr>";
    }
    
    $print.="$L[TBLend]";
    
    echo $print;
  }
  
  pagefooter();
?>
