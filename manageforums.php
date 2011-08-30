<?php
  
  require 'lib/common.php';

  if (isset($_GET[a])&&$_GET[a]=='s'&&(isadmin()||$loguser[id]==4)) {

    $id = $_POST[id];
    checknumeric($id);

    //save
    switch ($_POST[t]) {
      case 'f':
        if (isset($_POST[save])) {
          $min=$_POST[minpower];
          if ($min < 0) $min = 0;
          if ($id==-2)
            $id = $sql->resultq("select (id+1) nid from forums where id <> 99 order by nid desc");
          $sql->query("insert into `forums` (id, title, descr, minpower, cat, ord, minpowerthread, minpowerreply) values ($id, '".$_POST[title]."', '$_POST[descr]
            ', $_POST[minpower], $_POST[cat], $_POST[ord], $_POST[minpowerthread], $_POST[minpowerreply]) on duplicate key update
             title='$_POST[title]', descr='".$_POST[descr]."', minpower=$_POST[minpower], minpowerreply=$_POST[minpowerreply]
            , minpowerthread=$_POST[minpowerthread], cat=$_POST[cat], ord=$_POST[ord]");
        } else {
          if ($id==99) { //Don't delete the 'lost threads' forum!
            break;
          }
          $sql->query("delete from `forums` where `id`=$id");
          $sql->query("update `threads` set `forum`=99 where `forum`=$id"); //And then if you're deleting the forum, move the threads to a valid one
        }
        break;
      case 'c':
        if (isset($_POST[save])) {
          if ($id==-2)
            $id = $sql->resultq("select (id+1) nid from categories order by nid desc");
          $sql->query("replace into `categories` (id, ord, title, minpower) values ($id, ".addslashes($_POST[ord]).", '".$_POST[title]."', ".$_POST[minpower].")");
        } else {
          $sql->query("delete from `categories` where `id`=$id");
          $sql->query("update `forums` set `cat`=0 where `cat`=$id");
        }
        break;
    }
    header("location: manageforums.php");
    die();
  }




  pageheader("Forum Admin");

  if (!isadmin() && $loguser[id]!=4) {
    print "$L[TBL1]>
".        "  $L[TR]>
".        "    $L[TDhc]>
".        "      Access Denied
".        "    </td>
".        "  </tr>
".        "$L[TBLend]
".        "<br>";
    pagefooter();
    die();
  }
  if (isset($_GET[t])&&($_GET[t]=='f'||$_GET[t]=='c')) {
    if ($_GET[t]=='f') {
      $data = $sql->fetchq("select id,title,descr,ord,cat,minpower,minpowerthread,minpowerreply from forums where id=".addslashes($_GET[i])." union select -2 id, '' title, '' descr, 0 ord, 1 cat, 0 minpower, 0 minpowerthread, 0 minpowerreply");
    } else {
      $data = $sql->fetchq("select id,title,minpower,ord from categories where id=".addslashes($_GET[i])." union select -2 id, '' title, 0 minpower, 0 ord");
    }
    $data[title] = str_replace("\"","'",$data[title]);
    $data[descr] = str_replace("\"","'",$data[descr]);
    $i = 1;
    print "<form method=\"post\" enctype=\"multipart/form-data\" action=\"manageforums.php?a=s\">".$L['TBL'.$i]." width=\"100%\">    
".        "  $L[TRh]>
".        "    $L[TDhc] colspan=2> ".($_GET[i]>=0?"Edit":"Create")." ".($_GET[t]=='f'?"Forum":"Category")."
".        "    </td>
".        "  </tr>
".           $L['TR'.$i].">
".           $L['TD'.$i].">
".        "      Title
".        "    </td>
".            $L['TD'.$i].">
".        "      $L[INPt]=\"title\" value=\"$data[title]\" style=\"width: 220px\">
".        "    </td>";
  $i = 3 - $i;


  if ($_GET[t]=='f') {


    print    $L['TR'.$i].">
".            $L['TD'.$i].">
".        "      Description
".        "    </td>
".        "  ".$L['TD'.$i].">
".        "      $L[INPt]=\"descr\" value=\"$data[descr]\" style=\"width: 700px\">
".        "    </td>
".        "  </tr>";
    $i = 3 - $i;

    print    $L['TR'.$i].">
".            $L['TD'.$i].">
".        "      Category:
".        "    </td>
".        "  ".$L['TD'.$i].">
".        "      ".categorylist($data[cat])."
".        "    </td>
".        "  </tr>";
    $i = 3 - $i;

  }

  print    $L['TR'.$i].">
".            $L['TD'.$i].">
".        "      Order:
".        "    </td>
".        "  ".$L['TD'.$i].">
".        "      $L[INPt]=\"ord\" value=\"$data[ord]\" style=\"width: 40px\">
".        "    </td>
".        "  </tr>";
  $i = 3 - $i;

  print    $L['TR'.$i].">
".            $L['TD'.$i].">
".        "      Minimum powerlevel to view:
".        "    </td>
".        "  ".$L['TD'.$i].">
".        "      $L[INPt]=\"minpower\" value=\"$data[minpower]\" style=\"width: 40px\">
".        "    </td>
".        "  </tr>";
  $i = 3 - $i;

  if($_GET[t]=='f') {
    print   $L['TR'.$i].">
".            $L['TD'.$i].">
".        "      Minimum powerlevel to reply to threads:
".        "    </td>
".        "  ".$L['TD'.$i].">
".        "      $L[INPt]=\"minpowerreply\" value=\"$data[minpowerreply]\" style=\"width: 40px\">
".        "    </td>
".        "  </tr>";
    $i = 3 - $i;

    print   $L['TR'.$i].">
".            $L['TD'.$i].">
".        "      Minimum powerlevel to post new threads:
".        "    </td>
".        "  ".$L['TD'.$i].">
".        "      $L[INPt]=\"minpowerthread\" value=\"$data[minpowerthread]\" style=\"width: 40px\">
".        "    </td>
".        "  </tr>";
    $i = 3 - $i;

  }

  print      $L['TR'.$i].">
".             $L['TD'.$i.'c']." colspan=2>
".        "      $L[INPs]=save value=\"Save ".($_GET[t]=='f'?"forum":"category")."\">";

  if ($data[id] > 0)
    print " $L[INPs]=delete value=\"Delete ".($_GET[t]=='f'?"forum":"category")."\">";



  $i = 3 - $i;

  print   $L['TR'.$i].">
".             $L['TD'.$i.'c']." colspan=2>
".        "      <a href=manageforums.php>Back</a>
".        "$L[TBLend]
".        "$L[INPh]=\"id\" value=\"$data[id]\">$L[INPh]=\"t\" value=\"$_GET[t]\"></form><br>";


  } else {





    $forums = $sql->query("select f.id, f.title, f.cat, f.ord, f.descr from forums f  left join categories c on c.id = f.cat order by c.ord, f.cat, f.ord");
    $cats = $sql->query("select id, title from categories order by ord");
    print "$L[TBL2] style=\"border:0px\">
".        "  $L[TR] style=\"border:0px\">
".        "    $L[TDc] style=\"border:0px; vertical-align: top\" width=\"50%\">
".        "      $L[TBL1] style=\"width: 95%\">
".        "  $L[TRh]>
".        "    $L[TDhc]>
".        "      Edit Forums
".        "    </td>";
    $cid = -1;
    while ($forum = $sql->fetch($forums)){
      if ($cid != $forum[cat] && $forum[cat] > 0)
        print "  $L[TR]>
".      "    $L[TD2c]>
".      "      ".$sql->resultq("select title from categories where id=$forum[cat]")."
".      "    </td>";
      $cid = $forum[cat];
      print "  $L[TR]>
".          "    $L[TD3c]>
".          "      <a href=\"?a=e&t=f&i=$forum[id]\">$forum[title]</a>
".          "    </td></tr>";
    }

    print "  </tr>
".        "$L[TR]>
".        "    $L[TD3c]>
".        "      <a href=\"?a=e&t=f&i=-2\">(New)</a>
".        "    </td></tr>
".        "$L[TBLend]
".        "    </td>
".        "    $L[TDc] style=\"border:0px; vertical-align: top\" width=\"50%\">
".        "      $L[TBL1] style=\"width: 95%\">
".        "  $L[TRh]>
".        "    $L[TDhc]>
".        "      Edit Categories
".        "    </td>";
    while ($cat = $sql->fetch($cats)){
       print "  $L[TR1]>
".           "    $L[TD3c]>
".           "      <a href=\"?a=e&t=c&i=$cat[id]\">$cat[title]</a>
".           "    </td></tr>";
    }

    print   "  </tr>
".        "$L[TR]>
".        "    $L[TD3c]>
".        "      <a href=\"?a=e&t=c&i=-2\">(New)</a>
".        "    </td></tr>
".        "$L[TBLend]
".        "    </td>
".        "  </tr>
".        "$L[TBLend]
".        "<br>";
  }


  pagefooter();

  function categorylist($id=-1) {
    global $sql;
    $cats = $sql->query("select title, id from categories");
    $catst="<select name=\"cat\">";
    while ($cat=$sql->fetch($cats))
      $catst.="<option value=\"$cat[id]\"".($id==$cat[id]?"selected=\"selected\"":"").">$cat[title]</option>";
    return $catst."</select>";
  }
?>
