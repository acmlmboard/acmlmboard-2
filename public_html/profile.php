<?php
    require "lib/common.php";
    require "lib/threadpost.php";

    $uid = $_GET['id'];
    if(checknumeric($_GET['id']))
    {
      $user = $sql->fetchq("SELECT * FROM `users` WHERE `id`='$uid'");
    }

   if(!$user['id'])
    {
     //use error($message, 0) function later!
     pageheader("Profile");
     print "<a href=\"./\">Main</a> - Profile<br><br>
            $L[TBL1]>
              $L[TD1c]>
            This user does not exist!
            $L[TBLend]";
     pagefooter();
     die();
    }

    $group = $sql->fetchp("SELECT * FROM `group` WHERE id=?", array($user['group_id']));

    pageheader("Profile for ".($user['displayname'] ? $user['displayname'] : $user['name']));

    $days   = (ctime() - $user['regdate']) / 86400;
    $pfound = $sql->resultq("SELECT count(*) FROM `posts` WHERE `user`='$uid'");
    $pavg   = sprintf("%1.02f", $user['posts'] / $days);
    $tfound = $sql->resultq("SELECT count(*) FROM `threads` WHERE `user`='$uid'");
    $tavg   = sprintf('%1.02f',$user['threads'] / $days);
  
    $thread = $sql->fetchq("SELECT `p`.`id`, `t`.`title` `ttitle`, `f`.`title` `ftitle`, `t`.`forum`, `f`.`minpower`
                            FROM `forums` `f`
                            LEFT JOIN `threads` `t` ON `t`.`forum`=`f`.`id`
                            LEFT JOIN `posts` `p` ON `p`.`thread`=`t`.`id`
                            WHERE `p`.`date`='$user[lastpost]' AND p.user='$uid' AND `f`.`id` IN ".forums_with_view_perm());

   if($pfound && $thread)
    {
     $lastpostlink = "<br>in <a href=\"thread.php?pid=$thread[id]#$thread[id]\">".forcewrap(htmlval($thread['ttitle']))."</a> 
                     (<a href=\"forum.php?id=$thread[forum]\">".htmlval($thread['ftitle'])."</a>)";
    }
   else
    {
     $lastpostlink = "<br>in <i>(restricted forum)</i>";
    }

    //[KAWA] Adapting to new theme system...
    $themes = unserialize(file_get_contents("themes_serial.txt"));
    $themename = $themes[0][0];
   foreach($themes as $theme)
    {
      if($theme[1] == $user['theme'])
      {
        $themename = $theme[0];
        break;
      }
    }

   if($user['birth'] != -1)
    {
     //Crudely done code.
     $bdec     = explode("-", $user['birth']);
     $bstr     = $bdec[2]."-".$bdec[0]."-".$bdec[1];
     $bd1      = new DateTime($bstr);
     $bd2      = new DateTime(date("Y-m-d"));
     $bd3      = $bd1->diff($bd2);
     $birthday = date("l, F j, Y", strtotime($bstr));
     $age      = "(".$bd3->format("%Y")." years old)";
    }
   else
    {
     $birthday = "";
     $age      = "";
    }

   if($user['email'])
    {
     $email = str_replace("@", "<b>&#64;</b>", $user['email']);
     $email = str_replace(".", "<b>&#46;</b>", $email);
     $email = htmlval($email);
    }
   else
     $email = "";

   if($user['homeurl'] && $user['homename'])
     $homepage = "<a href=\"".htmlval($user['homeurl'])."\">".htmlval($user['homename'])."</a> - ".htmlval($user['homeurl']);
   elseif($user['homeurl'] && !$user['homename'])
     $homepage = "<a href=\"".htmlval($user['homeurl'])."\">".htmlval($user['homeurl'])."</a>";
   elseif(!$user['homeurl'] && $user['homename'])
     $homepage = $user['homeurl'];
   else
     $homepage = "";

   if($user['url'][0] == "!")
    {
     $user['url'] = substr($user['url'], 1);
     $user['ssl'] = 1;
    }

    $post['date'] = ctime();
    $post['ip']   = $user['ip'];
    $post['num']  = 0; //$user[posts];  #2/26/2007 xkeeper - threadpost can hide "1/" now
  
    $post['text'] = "[quote=\"Emuz\" id=\"2\"]<a href=\"/#fakelink\">[b]The[/b] sample link.</a> [quote=\"Acmlm\" id=\"1\"][quote=\"Shroomy\"]Sample nested quote.[/quote][spoiler]Sample spoiler, but I guess I ruined that for you hehe[/spoiler][/quote]^Sample [u]sarcasm[/u][/quote]
How about some code: 
[code]<?php
if (\$whateverthislongstupidvariable == \$anotherstupidlylongnamedvariable) //Scrydan was here.... 
{
print \"Sample code.\"; #oops you just missed him!
}
?>[/code]
[i]Sample[/i] message.";
  
              /*"[quote=nobody in particular]".
                "[quote=somebody else](<a href=http://en.wikipedia.org/wiki/Example>sample</a> quote)".
                "[quote=William Shakespeare]".
                "<a href=http://en.wikipedia.org/wiki/Bracket>(</a>sample quote<a href=http://en.wikipedia.org/wiki/Bracket>)</a>[/quote][/quote]".
                "(sample <a href=http://en.wikipedia.org/wiki/Quote>quote</a>)[/quote](sample <a href=http://en.wikipedia.org/wiki/Text>text</a>)";*/
                
   foreach($user as $field => $val)
    {
     $post['u'.$field] = $val;
    }

    $shoplist = "
         $L[TBL1] width=\"100%\">
           $L[TRh]>
             $L[TDh] colspan=\"2\">Equipped Items</td></tr>";

    $shops   = $sql -> query('SELECT * FROM itemcateg ORDER BY corder');
    $eq      = $sql -> fetchq("SELECT * FROM usersrpg WHERE id='$uid'");
    $eqitems = $sql -> query("SELECT * FROM items WHERE `id`='$eq[eq1]' OR `id`='$eq[eq2]' OR `id`='$eq[eq3]' OR `id`='$eq[eq4]' OR `id`='$eq[eq5]' OR `id`='$eq[eq6]'");
    
   while($item = $sql -> fetch($eqitems))
    {
     $items[$item['id']]=$item;
    }
     
   while($shop = $sql -> fetch($shops))
    {
     $shoplist.="
         $L[TR] class=\"sfont\">
           $L[TD1] width=\"70\">$shop[name]</td>
           $L[TD2]><a href=\"shop.php?action=desc&id=".$eq['eq'.$shop['id']]."\">".$items[$eq['eq'.$shop['id']]]['name']."</a></td>
         </tr>";
    }
    $shoplist.="
         </table>";

    //New Badge List
    $badgelist ="";
   if ($config['badgesystem'])
    {
     $q = $sql->query("SELECT * FROM `badges`
                       RIGHT JOIN `user_badges` ON `badges`.`id` = `user_badges`.`badge_id`
                       WHERE `user_badges`.`user_id`='$uid' AND `badges`.`image`!= '' ORDER BY `priority` DESC LIMIT 9");
    
    if(!$sql->numrows($q) == 0)
     {
      $badgelist = "
           $L[TBL1] width=\"100%\">
             $L[TRh]>
               $L[TDh] colspan=\"3\">Badges</td></tr>";
      $numbadges = 0;
      $badgelist.="$L[TR]>";
     while($badge = $sql -> fetch($q))
      {
       $badgelist.= "$L[TD2c]><img src=\"".htmlval($badge['image'])."\" alt=\"\" title=\"".htmlval($badge['name'])."\" /></td>";
       $numbadges++;
       if ($numbadges % 3 == 0)
         $badgelist .= "</tr>$L[TR]>";
      }
     while($numbadges < 9)
      {
       $badgelist.= "$L[TD1c]><img src=\"img/_.png\" width=\"25\" height=\"25\" /></td>";
       $numbadges++;
      if ($numbadges % 3 == 0)
        $badgelist .= "</tr>$L[TR]>";
      }
      $badgelist .= "</table>    <br>";
     }
    }
    //END badge list

    //[KAWA] Blocklayout ported from ABXD
    $qblock    = "SELECT * FROM `blockedlayouts` WHERE `user`='$uid' AND `blockee`='$loguser[id]'";
    $rblock    = $sql->query($qblock);
    $isblocked = $sql->numrows($rblock);
   if (has_perm("block-layout"))
    {
    if($isblocked)
      $blocklayoutlink = "| <a href=\"profile.php?id=$uid&amp;block=0\">Unblock layout</a>";
    else
      $blocklayoutlink = "| <a href=\"profile.php?id=$uid&amp;block=1\">Block layout</a>";
    if(isset($_GET['block']) && $log)
     {
      $block = (int)$_GET['block'];

     if($block && !$isblocked)
      {
       $qblock       = "INSERT INTO `blockedlayouts` (`user`, `blockee`) values ('$uid', '$loguser[id]')";
       $rblock       = $sql->query($qblock);
       $blockmessage = "Layout blocked.";
      }
     elseif(!$block && $isblocked)
      {
       $qblock       = "DELETE FROM `blockedlayouts` WHERE `user`='$uid' AND `blockee`='$loguser[id]' LIMIT 1";
       $rblock       = $sql->query($qblock);
       $blockMessage = "Layout unblocked.";
      }
      
     if($blockmessage)
      {
       print "
       $L[TBL1]>
         $L[TD1c]>
           $blockmessage
       $L[TBLend]";
      }
     }
    }

    //timezone calculations
    $now       = new DateTime("now");
    $usertz    = new DateTimeZone($user['timezone']); 
    $userdate  = new DateTime("now", $usertz);
    $userct    = date_format($userdate, $dateformat);
    $logtz     = new DateTimeZone($loguser['timezone']);
    $usertzoff = $usertz->getOffset($now);
    $logtzoff  = $logtz->getOffset($now);

    $user['showminipic'] = 1;
    
    print "<a href=\"./\">Main</a> - Profile for ".userdisp($user)."
           <br><br>
           $L[TBL] width=\"100%\">
             $L[TDn] valign=\"top\">
               $L[TBL1]>
                 $L[TRh]>
                   $L[TDh] colspan=\"2\">General information</td>
                   ".($user['displayname'] ? "$L[TR]>$L[TD1] width=\"110\"><b>Real handle</b></td>$L[TD2]>".htmlval($user['name']) : "")."
                 $L[TR]>
                   $L[TD1] width=\"110\"><b>Group</b></td>
                   $L[TD2]>$group[title]
                 $L[TR]>
                   $L[TD1] width=\"110\"><b>Total posts</b></td>
                   $L[TD2]>$user[posts] ($pfound found, $pavg per day)
                 $L[TR]>
                   $L[TD1]><b>Total threads</b></td>
                   $L[TD2]>$user[threads] ($tfound found, $tavg per day)
                 $L[TR]>
                   $L[TD1]><b>Registered on</b></td>
                   $L[TD2]>".cdate($dateformat, $user['regdate'])." (".timeunits($days * 86400)." ago)
                 $L[TR]>
                   $L[TD1]><b>Last post</b></td>
                   $L[TD2]>
                     ".($user['lastpost']? cdate($dateformat, $user['lastpost'])." (".timeunits(ctime() - $user['lastpost'])." ago)" : "None")."
                     $lastpostlink
                 $L[TR]>
                   $L[TD1]><b>Last view</b></td>
                   $L[TD2]>
                     ".cdate($dateformat, $user['lastview'])." (".timeunits(ctime()-$user['lastview'])." ago)
                     ".($user['url'] ? "<br>at <a href=\"".htmlval($user['url'])."\">".htmlval($user['url'])."</a>":'')."
                     ".($user['ip'] && has_perm("view-post-ips") ? "<br>from IP: $user[ip]":'')."
               $L[TBLend]
               <br>
               $L[TBL1]>
                 $L[TRh]>
                   $L[TDh] colspan=\"2\">Contact information</td>
                 $L[TR]>
                   $L[TD1] width=\"110\"><b>Email address</b></td>
                   $L[TD2]>$email
                 $L[TR]>
                   $L[TD1]><b>Homepage</b></td>
                   $L[TD2]>$homepage
               $L[TBLend]
               <br>
               $L[TBL1]>
                 $L[TRh]>
                   $L[TDh] colspan=\"2\">User settings</td>
                 $L[TR]>
                   $L[TD1] width=\"110\"><b>Theme</b></td>
                   $L[TD2]>
                     ".htmlval($themename)."
                 $L[TR]>
                   $L[TD1] width=\"110\"><b>Time offset</b></td>
                   $L[TD2]>
                     ".sprintf("%d:%02d", ($usertzoff - $logtzoff) / 3600, abs(($usertzoff - $logtzoff) / 60) % 60)." from you
                     <br>(current time: ".$userct.")
                 $L[TR]>
                   $L[TD1]><b>Items per page</b></td>
                   $L[TD2]>$user[ppp] posts, $user[tpp] threads
               $L[TBLend]
               <br>
               $L[TBL1]>
                 $L[TRh]>
                   $L[TDh] colspan=\"2\">Personal information</td>
                 $L[TR]>
                   $L[TD1] width=\"110\"><b>Real name</b></td>
                   $L[TD2]>".($user['realname'] ? htmlval($user['realname']) : "")."
                 $L[TR]>
                   $L[TD1]><b>Location</b></td>
                   $L[TD2]>".($user['location'] ? htmlval($user['location']) : "")."
                 $L[TR]>
                   $L[TD1]><b>Birthday</b></td>
                   $L[TD2]>$birthday $age
                 $L[TR]>
                   $L[TD1]><b>Bio</b></td>
                   $L[TD2]>".($user['bio'] ? postfilter($user['bio']) : "")."
               $L[TBLend]
             </td>
             $L[TDn] width=\"15\"></td>
             $L[TDn] width=\"256\" valign=\"top\">
               $L[TBL1]>
                 $L[TRh]>
                   $L[TDh] colspan=\"2\">RPG status</td>
                 $L[TR]>
                   $L[TD1]><img src=\"gfx/status.php?u=$uid\">
               $L[TBLend]
               <br>
           $badgelist
           $shoplist
             </td>
           $L[TBLend]
           <br>
           $L[TBL1]>
             $L[TRh]>
               $L[TDh]>Sample post</td>
             $L[TR]>
           $L[TBLend]
           ".threadpost($post, 0)."
           <br>
           $L[TBL1]>
             $L[TRh]>
               $L[TD2]><a href=\"forum.php?user=$user[id]\">View threads</a>
                       | <a href=\"thread.php?user=$user[id]\">Show posts</a>
                       | <a href=\"postsbyuser.php?id=$user[id]\">List posts</a>
                       $blockLayoutLink
                       ". (has_perm('create-pms') ? "| <a href=\"sendprivate.php?uid=".$user['id']."\">Send Private Message</a>":"") ."
                       ". (has_perm('view-user-pms') ? "| <a href=\"private.php?id=".$user['id']."\">View Private Messages</a>":"") ."
                       ". (has_perm('edit-moods') ? "| <a href=\"mood.php?user=".$user['id']."\">Edit Mood Avatars</a>":"") ."
                       ". (has_perm('edit-users') ? "| <a href=\"editprofile.php?id=".$user['id']."\">Edit User</a>":"") ."
                       ". (has_perm('edit-permissions') ? "| <a href=\"editperms.php?uid=".$user['id']."\">Edit User Permissions</a>":"") ."
           $L[TBLend]";
           pagefooter();
           
?>