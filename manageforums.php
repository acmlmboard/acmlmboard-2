<?php
  
  require 'lib/common.php';

  if (isset($_GET[a]) && $_GET[a] == 's' && isadmin()) {

    $id = $_POST[id];
    if (!checknumeric($id))
      die();

    //save
    switch ($_POST[t]) {
      case 'f': //Saving forum
        if (isset($_POST['save'])) {
          $min=$_POST[minpower];
          if ($min < 0) $min = 0;
          if ($id == -2) //If it's a new forum, get the next available fid
            $id = $sql->resultq("select (id+1) nid from forums where id <> 99 order by nid desc limit 1");
          $sql->query("insert into `forums` (id, title, descr, minpower, cat, ord, minpowerthread, minpowerreply, lastid, posts, lastdate) values ($id, '".$_POST[title]."', '$_POST[descr]
            ', $_POST[minpower], $_POST[cat], $_POST[ord], $_POST[minpowerthread], $_POST[minpowerreply], 0, 0, 0) on duplicate key update
             title='$_POST[title]', descr='".$_POST[descr]."', minpower=$_POST[minpower], minpowerreply=$_POST[minpowerreply]
            , minpowerthread=$_POST[minpowerthread], cat=$_POST[cat], ord=$_POST[ord]");

            $lmods = explode(",", $_POST['localmods']); //All $_POST values are filtered as part of the board framework, so no need to re-filter here.
            $sql->query("DELETE FROM `forummods` WHERE `fid`=$id");
            foreach ($lmods as $modid)
              if (strlen($modid)) {
                $sql->query("INSERT INTO `forummods` (`uid`, `fid`) VALUES ($modid, $id)");
              }

        } elseif (isset($_POST['delete'])) {
          if ($id==99) { //Don't delete the 'lost threads' forum, since it's a special-case
            break;
          }
          $sql->query("delete from `forums` where `id`=$id");
          $sql->query("update `threads` set `forum`=99 where `forum`=$id"); //And then if you're deleting the forum, move the orphaned threads to Lost Threads
        }
        break;
      case 'c': //Save Category
        if (isset($_POST[save])) {
          if ($id==-2)
            $id = $sql->resultq("select (id+1) nid from categories order by nid desc limit 1");
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

  pageheader("Forums Administration");

  if (!isadmin()) {
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

  if (isset($_GET[t]) && $_GET[t]=='f') { //Edit Forum
    print "<script type=\"text/javascript\" src=\"manageforum.js\"></script>";
    $data = $sql->fetchq("select id,title,descr,ord,cat,minpower,minpowerthread,minpowerreply from forums where id=".addslashes($_GET[i])." union select -2 id, '' title, '' descr, 0 ord, 1 cat, 0 minpower, 0 minpowerthread, 0 minpowerreply");
    $data[title] = str_replace("\"","'",$data[title]);
    $data[descr] = str_replace("\"","'",$data[descr]);

    print "<form method=\"post\" enctype=\"multipart/form-data\" action=\"manageforums.php?a=s&t=f\">".$L['TBL1']." width=\"100%\">    
".        "  $L[TRh]>
".        "    $L[TDhc] colspan=4> ".($_GET[i]>=0 ? "Edit" : "Create")." Forum
".        "    </td>
".        "  </tr>
".           $L['TR1'].">
".           $L['TD1'].">
".        "      Title
".        "    </td>
".            $L['TD1'].">
".        "      $L[INPt]=\"title\" value=\"$data[title]\" style=\"width: 220px\">
".        "    </td>
".            $L['TD1'].">
".        "      Description
".        "    </td>
".            $L['TD1'].">
".        "      $L[INPt]=\"descr\" value=\"$data[descr]\" style=\"width: 520px\">
".        "    </td>
".            $L['TR2'].">
".            $L['TD2'].">
".        "      Category:
".        "    </td>
".        "  ".$L['TD2'].">
".        "      ".categorylist($data[cat])."
".        "    </td>
".            $L['TD2'].">
".        "      Order:
".        "    </td>
".        "  ".$L['TD2'].">
".        "      $L[INPt]=\"ord\" value=\"$data[ord]\" style=\"width: 40px\">
".        "    </td>
".        "  </tr>
".            $L['TR1'].">
".            $L['TD1'].">
".        "      Minimum powerlevel to view:
".        "    </td>
".        "  ".$L['TD1'].">
".        "      $L[INPt]=\"minpower\" value=\"$data[minpower]\" style=\"width: 40px\">
".        "    </td>
".            $L['TD1'].">
".        "      Minimum powerlevel to post:
".        "    </td>
".        "  ".$L['TD1'].">
".        "      $L[INPt]=\"minpowerreply\" value=\"$data[minpowerreply]\" style=\"width: 40px\">
".        "    </td>
".        "  </tr>
".            $L['TR2'].">
".            $L['TD2'].">
".        "      Minimum powerlevel to post new threads:
".        "    </td>
".        "  ".$L['TD2']." colspan='3'>
".        "      $L[INPt]=\"minpowerthread\" value=\"$data[minpowerthread]\" style=\"width: 40px\">
".        "    </td>
".        "  </tr>
".        "</table> <br />
".         $L['TBL1'].">
".            $L[TRh].">
".        "    $L[TDhc] colspan=4> Select Moderators
".        "    </td>
".        "  </tr>
".            $L['TR1'].">
".            $L['TD1'].">
".        "      Search string: $L[INPt]=\"srchstr\" id=\"srchnm\" value=\"\" style=\"width: 140px\">
".        "    </td>
".            $L['TD1'].">
".        "      Matches:<select name=\"slctnm\" id=\"slctnm\" style=\"min-width: 240px\"></select>
".        "    $L[BTTn]=\"btnadd\">&gt;&gt;</button>
".        "    </td>
".            $L['TD1']." COLSPAN=\"2\">
".        "      Local Moderators: 
".        "      ".moderatorlist($data[id])."
".        "    $L[BTTn]=\"btnrmv\">X</button>
".        "    </td>
".        "  </tr>
".        "</table> <br />
".         $L['TBL1'].">
".            $L[TRh].">
".        "    $L[TDhc] colspan=4> Manage Tags
".        "    </td>
".        "  </tr>
".            $L['TR2'].">
".            $L['TD2'].">
".               taglist($data[id])."
".        "    </td>
".            $L['TD2'].">
".        "    $L[BTTn]=\"addtg\">Add New</button> <br />
".        "    $L[BTTn]=\"modtg\">Edit</button> <br />
".        "    $L[BTTn]=\"deltg\">Delete</button>
".        "    </td>
".            $L['TD2']." colspan=\"2\">
".        "      Short Form: $L[INPt]=\"tgshrt\" size=5 maxlength=5 value=\"\"> <br />
".        "      &nbsp;Long Form: $L[INPt]=\"tglong\" value=\"\" style=\"width: 200px\"> <br />
".        "      $L[BTTn]=\"savtg\">Save</button> $L[BTTn]=\"clrtg\">Clear</button>
".        "      $L[INPh]=\"tagops\" id=\"tagops\" value=\"\" />
".        "    </td>
".        "  </tr>
".        "</table> <br />
".         $L['TBL1'].">
".            $L[TRh].">
".        "    $L[TDhc] colspan=4> Actions
".        "    </td>
".        "  </tr>
".            $L['TR1'].">
".             $L['TD1c']." colspan=4>
".        "      $L[INPs]=save value=\"Save Forum\">";

  if ($data[id] > 0) //If not creating a new forum, give the option to delete it
    print " $L[INPs]=delete value=\"Delete Forum\" onclick=\"return confirm('Are you sure you wish to delete ".str_replace("'","\\'", $data[title])."?  This action cannot be undone!')\">";

  print   "  </tr>
".           $L['TR2'].">
".             $L['TD2c']." colspan=4> <br />
".        "      <a href=manageforums.php>Back</a>
".        "$L[TBLend]
".        "$L[INPh]=\"id\" value=\"$data[id]\">$L[INPh]=\"t\" value=\"$_GET[t]\"></form><br>";

  } elseif (isset($_GET[t])&&$_GET[t]=='c') {
    $data = $sql->fetchq("select id,title,minpower,ord from categories where id=".addslashes($_GET[i])." union select -2 id, '' title, 0 minpower, 0 ord");
    $data[title] = str_replace("\"","'",$data[title]);

        print "<form method=\"post\" enctype=\"multipart/form-data\" action=\"manageforums.php?a=s&t=c\">".$L['TBL1']." width=\"100%\">    
".        "  $L[TRh]>
".        "    $L[TDhc] colspan=2> ".($_GET[i]>=0?"Edit":"Create")." Category
".        "    </td>
".        "  </tr>
".           $L['TR1'].">
".           $L['TD1'].">
".        "      Title
".        "    </td>
".            $L['TD1'].">
".        "      $L[INPt]=\"title\" value=\"$data[title]\" style=\"width: 220px\">
".        "    </td>
".            $L['TR2'].">
".            $L['TD2'].">
".        "      Order:
".        "    </td>
".        "  ".$L['TD2'].">
".        "      $L[INPt]=\"ord\" value=\"$data[ord]\" style=\"width: 40px\">
".        "    </td>
".        "  </tr>
".            $L['TR1'].">
".            $L['TD1'].">
".        "      Minimum powerlevel to view:
".        "    </td>
".        "  ".$L['TD1'].">
".        "      $L[INPt]=\"minpower\" value=\"$data[minpower]\" style=\"width: 40px\">
".        "    </td>
".        "  </tr>
".            $L[TRh].">
".        "    $L[TDhc] colspan=4> Actions
".        "    </td>
".            $L['TR1'].">
".             $L['TD1c']." colspan=4>
".        "      $L[INPs]=save value=\"Save Forum\">";

  if ($data[id] > 0)
    print " $L[INPs]=delete value=\"Delete Category\" onclick=\"return confirm('Are you sure you wish to delete $data[title]?  This action cannot be undone!')\">";

  print   $L['TR2'].">
".             $L['TD2c']." colspan=4>
".        "      <a href=manageforums.php>Back</a>
".        "$L[TBLend]
".        "$L[INPh]=\"id\" value=\"$data[id]\">$L[INPh]=\"t\" value=\"$_GET[t]\"></form><br>";
  } else {

    $forums = $sql->query("select f.id, f.title, f.cat, f.ord, f.descr from forums f  left join categories c on c.id = f.cat order by c.ord, f.cat, f.ord");
    $cats = $sql->query("select id, title from categories order by ord");
    print "$L[TBL] style=\"border:0px; font-size: 0.9em; width: 100%\">
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
    global $sql,$L;
    $cats = $sql->query("select title, id from categories");
    $catst = $L[INPl]."=\"cat\">";
    while ($cat=$sql->fetch($cats))
      $catst.="<option value=\"$cat[id]\"".($id==$cat[id]?"selected=\"selected\"":"").">$cat[title]</option>";
    return $catst."</select>";
  }

  function moderatorlist($fid=-1) {
    global $sql,$L;
    $mods = $sql->query("SELECT `f`.`uid`, `u`.`name` FROM `forummods` `f` JOIN `users` `u` ON `u`.`id` = `f`.`uid` WHERE `f`.`fid`=$fid");
    $st = $L[INPl]."\"mods\" id=\"lmods\" style=\"min-width: 240px;\">";
    while ($mod=$sql->fetch($mods)) {
      $st.="<option value=\"$mod[uid]\">$mod[name]</option>";
      $hst.="$mod[uid],";
    }
    return $st."</select>$L[INPh]=\"localmods\" id=\"localmods\" value=\"$hst\" />";
  }

  function taglist($fid=-1) {
    global $sql,$L;
    $tags = $sql->query("SELECT `t`.`bit`, `t`.`fid`,  `t`.`name`, `t`.`tag` FROM `tags` `t` WHERE `t`.`fid`=$fid");
    $st = $L[INPl]."\"tglst\" id=\"tglst\" size=\"5\" style=\"min-width: 280px;\">";
    while ($tag=$sql->fetch($tags)) {
      $st.="<option value=\"[$tag[bit]:$tag[name]:$tag[tag]]\">$tag[name] ($tag[tag])</option>";
    }
    return $st."</select>";
  }
?>
