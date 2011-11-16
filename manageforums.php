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
          if ($id == -2) //If it's a new forum, get the next available fid (-2 is a flag value)
            $id = $sql->resultq("select (id+1) nid from forums order by nid desc limit 1");
			$sql->query("insert into `forums` (id, title, descr, minpower, cat, ord, minpowerthread, minpowerreply, lastid, posts, lastdate) values ($id, '".$_POST[title]."', '$_POST[descr]
						', $_POST[minpower], $_POST[cat], $_POST[ord], $_POST[minpowerthread], $_POST[minpowerreply], 0, 0, 0) on duplicate key update
						title='$_POST[title]', descr='".$_POST[descr]."', minpower=$_POST[minpower], minpowerreply=$_POST[minpowerreply]
						, minpowerthread=$_POST[minpowerthread], cat=$_POST[cat], ord=$_POST[ord]");
			$lmods = explode(",", $_POST['localmods']); //All $_POST values are filtered as part of the board framework, so no need to re-filter here.
			$sql->query("DELETE FROM `forummods` WHERE `fid`=$id");
			foreach ($lmods as $modid)
				if (strlen($modid))
					$sql->query("INSERT INTO `forummods` (`uid`, `fid`) VALUES ($modid, $id)");
			
			// Read $_POST[tagops] and sort into delete and add/update queues.  Run through all the deletions, THEN process add/removes.


			/*
				Particularly observant coders, upon seeing manageforum.js, might notice that normally all the deletion entries will come first from the client-side code
				and that sorting the entries so deletions can be handled first isn't necessary. I've chosen NOT to take that optimization in order to handle the potential
				case of someone deliberately trying to mess with the server by sending malformed (or badly-ordered) tag change entries.  Of course, it's also possible that
				the order will change due to future revisions so I might as well cover that case too.
			*/
			
			$deletions = array();
			$modifications = array();
			$additions = array();
			$operations = explode(";", $_POST[tagops]);

			for ($x = 0; $x < count($operations) - 2; $x++) {
				switch ($operations[$x]) {
					case "a":
						array_push($additions, array($operations[$x + 1], $operations[$x + 2], $operations[$x + 3]));
						$x += 3;
						break;
					case "d":
						array_push($deletions, array($operations[$x + 1]));
						$x++;
						break;
					case "u":
						array_push($modifications, array($operations[$x + 1], $operations[$x + 2], $operations[$x + 3], $operations[$x + 4]));
						$x += 4;
						break;
					default:
						//If an entry is malformed, immediately stop sorting/processing tag changes.  Since this is the last step of saving a forum, simply stop and redirect the user.
						//I thought of using a goto down to the normal redirect part to avoid code duplication, but gotos can present maintenance headaches...
						header("location: manageforums.php");
						die();
				}
			}
			
			$tagcount = $sql->resultq("select count(`bit`) from `tags` where `fid`=$id");
			$tagcount += count($additions) - count($deletions);
			if ($tagcount <= 32) {
				foreach ($deletions as $deletion) {
					$sql->query("update `threads` set `tags` = `tags` & ~".(1 << $deletion[0])." WHERE `forum`=$id");
					$sql->query("delete from `tags` where `fid`=$id and `bit` = $deletion[0]");
				}
				foreach ($modifications as $modification) {
					$sql->query("update `tags` set `tag`='$modification[1]', `name`='$modification[2]', `color`='$modification[3]' where `fid`=$id and `bit`=$modification[0]");
					renderTag($modification[1], $id, $modification[0], $modification[3]);
				}
				
				//I wish I could have come up with a cleaner solution...
				$sql->query("CREATE TEMPORARY TABLE TempTable(FreeId INT)");
				$sql->query("INSERT INTO `TempTable` VALUES(0),(1),(2),(3),(4),(5),(6),(7),(8),(9),(10),(11),(12),(13),(14),(15),(16),(17),(18),(19),(20),(21),(22),(23),(24),(25),(26),(27),(28),(29),(30),(31)");
				$FreeIds = $sql->query("SELECT `T`.`FreeId` FROM `TempTable` `T` LEFT JOIN `tags` ON `tags`.`bit` = `T`.`FreeId` and `tags`.`fid`=$id WHERE `tags`.`bit` IS NULL");
				$sql->query("DROP TEMPORARY TABLE `TempTable`");
				foreach ($additions as $addition) {
					$FreeId = $sql->fetch($FreeIds);
					$FreeId = $FreeId['FreeId'];
					$sql->query("insert into `tags` (`fid`, `bit`, `tag`, `name`, `color`) values ($id, $FreeId, '$addition[0]', '$addition[1]', '$addition[2]')");
					renderTag($addition[0], $id, $FreeId, $addition[2]);
				}
			}

        } elseif (isset($_POST['delete'])) {
          if ($id <= 0) //Refuse to delete "utility" forums
            break;
          $sql->query("delete from `forums` where `id`=$id");
		  $sql->query("delete from `tags` where `fid`=$id");
		  $sql->query("delete from `localmods` where `fid`=$id");
          $sql->query("update `threads` set `forum`=-3, tags=0 where `forum`=$id"); //And then if you're deleting the forum, move the orphaned threads to the correct forum, and strip all tags.
		  //NOTE: the "correct forum" is forum -3, the "orphanage" utility forum.  It's a magic number.
        }
        break;
      case 'c': //Save Category
        if (isset($_POST[save])) {
          if ($id==-2)
            $id = $sql->resultq("select (id+1) nid from categories order by nid desc limit 1");
          $sql->query("replace into `categories` (id, ord, title, minpower) values ($id, ".addslashes($_POST[ord]).", '".$_POST[title]."', ".$_POST[minpower].")");
        } elseif (isset($_POST['delete'])) {
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
	//Unplanned "feature": Insert a row for forum id -2 into the database, and it'll be treated as a "template" forum.
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
".        "    $L[BTTn]=\"modtg\">Edit</button> <br />
".        "    $L[BTTn]=\"deltg\">Delete</button>
".        "    </td>
".            $L['TD2']." colspan=\"2\"><script type=\"text/javascript\" src=\"jscolor/jscolor.js\"></script>
".        "      Colour: <input class=\"color {pickerFaceColor:'transparent',pickerBorder:0,pickerInsetColor:'black'}\" value=\"808080\" name=\"color\" id=\"tgcol\"> <br />
".        "      Inline Form: $L[INPt]=\"tgshrt\" id=\"tgshrt\" size=5 maxlength=20 value=\"\"> <br />
".        "      &nbsp;Descriptive Form: $L[INPt]=\"tglong\" id=\"tglong\" value=\"\" style=\"width: 200px\"> <br />
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
".        "      $L[INPs]=save id=\"svefm\" value=\"Save Forum\">";

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
".        "      $L[INPs]=save value=\"Save Category\">";

  if ($data[id] > 0)
    print " $L[INPs]=delete value=\"Delete Category\" onclick=\"return confirm('Are you sure you wish to delete $data[title]?  This action cannot be undone!')\">";

  print   $L['TR2'].">
".             $L['TD2c']." colspan=4>
".        "      <a href=manageforums.php>Back</a>
".        "$L[TBLend]
".        "$L[INPh]=\"id\" value=\"$data[id]\">$L[INPh]=\"t\" value=\"$_GET[t]\"></form><br>";
  } else {

    $forums = $sql->query("select f.id, f.title, f.cat, f.ord, f.descr from forums f left join categories c on c.id = f.cat where f.id > 0 order by c.ord, f.cat, f.ord");
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
    $tags = $sql->query("SELECT `t`.`bit`, `t`.`fid`,  `t`.`name`, `t`.`tag`, `t`.`color` FROM `tags` `t` WHERE `t`.`fid`=$fid");
    $st = $L[INPl]."\"tglst\" id=\"tglst\" size=\"5\" style=\"min-width: 280px;\">";
	$count = 0;
    while ($tag=$sql->fetch($tags)) {
		$st.="<option value=\"[&quot;$tag[name]&quot;, &quot;$tag[tag]&quot;, true, ".$count++.", true, 0, $tag[bit], &quot;$tag[color]&quot;]\">$tag[name] ($tag[tag])</option>";
    }
    return $st."</select>";
  }

  function renderTag($TagText, $ForumID, $TagBit, $TintColour) {
		
		$TagTextImage = RenderText($TagText);
		$Tag = Image::Create($TagTextImage->Size[0] + 11, 16);

		$LeftImage = Image::LoadPNG("./gfx/tagleft.png");
		$RightImage = Image::LoadPNG("./gfx/tagright.png");
		$Tag->DrawImageDirect($LeftImage, 0, 0);
		
		for ($X = 7; $X < $Tag->Size[0] - 7; $X += 4)
			$Tag->DrawImageDirect($RightImage, $X, 0);

		$Tag->DrawImageDirect($RightImage, $Tag->Size[0] - 8, 0);
		$Tag->Colourize(hexdec(substr($TintColour, 0, 2)), hexdec(substr($TintColour, 2, 2)), hexdec(substr($TintColour, 4, 2)), 0xFF);

		$Tag->DrawImageDirect($TagTextImage, 8, 2);
		$Tag->SavePNG("./gfx/tags/tag$ForumID-$TagBit.png");

		$LeftImage->Dispose();
		$RightImage->Dispose();
  }
?>
