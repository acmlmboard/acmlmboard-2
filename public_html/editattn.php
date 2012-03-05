<?php
  include('lib/common.php');

  if(!has_perm('edit-attentions-box')) {
    pageheader('Nothing here.');
  } else {

    if($_POST[action]=="Submit") {
      $sql->query("UPDATE misc SET txtval='".$_POST[txtval]."' WHERE field='attention'");
    }

    $pageheadtxt = "Edit ".$config[atnname];
    pageheader($pageheadtxt);

    print "<form action=editattn.php method=post>
".        "$L[TBL1]>
".        "  $L[TRh]>
".        "    $L[TDh]>
".        "      Edit $config[atnname]
".        "  $L[TR1]>
".        "    $L[TD]>
".        "      $L[TXTa]='txtval' rows=8 cols=120>".$sql->resultq("SELECT txtval FROM misc WHERE field='attention'")."</textarea>
".        "  $L[TR1]>
".        "    $L[TD1c]>
".        "      $L[INPs]=action value=Submit>
".        "$L[TBLend] </form>";

  }

  pagefooter();

?>
