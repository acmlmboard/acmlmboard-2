<?php
  include('lib/common.php');

  if(!has_perm('edit-attentions-box')) {
    pageheader('Nothing here.');
  } else {

    if($_POST[action]=="Submit") {
      $sql->query("UPDATE misc SET txtval='".$_POST[txtval]."' WHERE field='attention'");
    }

    if($_POST[action]=="Preview") {
      $attndata = stripslashes($_POST[txtval]);

      $previewattn = "
                 $L[TBL1] width=\"100%\" align=\"center\">
                   $L[TRh]>
                      $L[TDh]><font color='red'><i>Preview </i></font>$config[atnname] $ae</td>
                    $L[TR2] align=\"center\">
                      $L[TDs]>".$attndata."
                      </td>
                 $L[TBLend]";
     $mockboardlogo = "
       $L[TBL] width=100%>
         $L[TRc]>
           $L[TD] style=\"border:none!important\" valign=\"center\"></td>
           $L[TD] style=\"border:none!important\" valign=\"center\" width=\"300\">
             $previewattn
           </td>
       $L[TBLend]<br/>";
    }
    else $attndata = $sql->resultq("SELECT txtval FROM misc WHERE field='attention'");

    $pageheadtxt = "Edit ".$config[atnname];
    pageheader($pageheadtxt);
    //print $previewattn."<br />";
    print $mockboardlogo;

    print "<form action=editattn.php method=post>
".        "$L[TBL1]>
".        "  $L[TRh]>
".        "    $L[TDh]>
".        "      Edit $config[atnname]
".        "  $L[TR1]>
".        "    $L[TD]>
".        "      $L[TXTa]='txtval' rows=8 cols=120>".$attndata."</textarea>
".        "  $L[TR1]>
".        "    $L[TD1c]>
".        "      $L[INPs]=action value=Preview>
".        "      $L[INPs]=action value=Submit>
".        "$L[TBLend] </form>";

  }

  pagefooter();

?>
