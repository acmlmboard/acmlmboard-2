<?php
//[KAWA] Blocklayouts
function LoadBlocklayouts()
{
	global $blocklayouts, $loguser, $log, $sql;
	if(isset($blocklayouts) || !$log)
		return;
	$rBlocks = $sql->query("select * from blockedlayouts where blockee = ".$loguser['id']);
	while($block = $sql->fetch($rBlocks))
		$blocklayouts[$block['user']] = 1;

}

  function threadpost($post,$type,$pthread=''){
    global $L,$dateformat,$loguser,$sql,$blocklayouts;
    $exp=calcexp($post[uposts],(ctime()-$post[uregdate])/86400);

    $post[head]=str_replace("<!--", "&lt;!--", $post[head]);
    $post[uhead]=str_replace("<!--", "&lt;!--", $post[uhead]);

    $post[text]=$post[head].$post[text].$signsep[$loguser[signsep]].$post[sign];

    $syn="";

    $post[utitle]= getrank($post[urankset],$post[uposts])
                  .$syn
                  .(($post[urankset]&&strlen($post[utitle]))?"<br>":"")
                  .$post[utitle];

	//[KAWA] TODO: replace with token effect, or preferably just a profile switch
	/*
    //opaque goggles
    if ($x_hacks['opaques']) {
      $post['usign'] = $post['uhead'] = "";
    }
	*/
    //if($post[nolayout]) {
    //[KAWA] Blocklayouts. Supports user/user ($blocklayouts), per-post ($post[nolayout]) and user/world (token).
	LoadBlockLayouts(); //load the blocklayout data - this is just once per page.
	$isBlocked = $blocklayouts[$post['uid']] || $post['nolayout'] || $loguser['blocklayouts'];
    if($isBlocked)
      $post['usign'] = $post['uhead'] = "";
    //}

    //post has been deleted, display placeholder
    if($post[deleted]) {
      $postlinks="";
      if(can_edit_forum_posts(getforumbythread($post[thread]))) {
        $postlinks.="<a href=thread.php?pid=$post[id]&pin=$post[id]&rev=$post[revision]#$post[id]>Peek</a> | ";
        $postlinks.="<a href=editpost.php?pid=".urlencode(packsafenumeric($post[id]))."&act=undelete>Undelete</a>";
      }

      if($post[id])
        $postlinks.=($postlinks?' | ':'')."ID: $post[id]";

      $text="$L[TBL1]>
".          "  $L[TR]>
".          "    $L[TD1] style=border-bottom:0;border-right:0;width:180px height=17>
".          "      ".userlink($post,'u')."</td>
".          "    $L[TD1] style=border-left:0>
".          "      $L[TBL] width=100%>
".          "        $L[TDns]>(post deleted)</td>
".          "        $L[TDnsr]>$postlinks</td>
".          "      $L[TBLend]
".          "$L[TBLend]";
      return $text;
    }

    switch($type){
     case 0:
     case 1:
      $postheaderrow = "";
      if($pthread)
        $threadlink=", in <a href=thread.php?id=$pthread[id]>$pthread[title]</a>";

      if($post[id])
        $postlinks="<a href=thread.php?pid=$post[id]#$post[id]>Link</a>";		// headlinks for posts

      //2007-03-08 blackhole89
      if($post[revision]>=2)
        $revisionstr=" (rev. $post[revision] of ".cdate($dateformat,$post[ptdate]).")";

      // I have no way to tell if it's closed (or otherwise impostable (hah)) so I can't hide it in those circumstances...
      if($post[isannounce]) {
          $postheaderrow =
            "$L[TRh]>
               $L[TD] colspan=2>".$post['ttitle']."</td>
             </tr>
            ";
      } 
      else if($post[thread] && $loguser['id']!=0) {
          $postlinks.=($postlinks?' | ':'')."<a href=newreply.php?id=$post[thread]&pid=$post[id]>Reply</a>";
      }

      // "Edit" link for admins or post owners, but not banned users
/*      if($post[thread] && ((has_perm('update-own-post') && $post[user] == $loguser[id]) || ismod(getforumbythread($post[thread]))))*/
if (can_edit_post($post[id]) && $post[id])
        $postlinks.=($postlinks?' | ':'')."<a href=editpost.php?pid=$post[id]>Edit</a>";

      if($post[id] && can_delete_forum_posts(getforumbythread($post[thread])))
        $postlinks.=($postlinks?' | ':'')."<a href=editpost.php?pid=".urlencode(packsafenumeric($post[id]))."&act=delete>Delete</a>";

      if($post[id])
        $postlinks.=" | ID: $post[id]";

      if(has_perm('view-post-ips'))
        $postlinks.=($postlinks?' | ':'')."IP: $post[ip]";

      if(can_view_forum_post_history(getforumbythread($post[thread]))
         && $post[maxrevision]>1) {
        $revisionstr.=" | Go to revision: ";
        for($i=1;$i<=$post[maxrevision];++$i)
          $revisionstr.="<a href=thread.php?pid=$post[id]&pin=$post[id]&rev=$i#$post[id]>$i</a> ";
      }

      // if quote enabled then if $postlink2 then postlink2 .= | [quote]

      // 2/22/2007 xkeeper - guess which moron forgot to close the </a>
      //[KAWA] Fun fact: <a name> is deprecated in favor of using IDs.
      //       That's right, you can use <anything id="foo"> in place of <a name="foo">!
$tbar1=($type==0) ? "topbar".$post['uid']."_1" : "";
$tbar2=($type==0) ? "topbar".$post['uid']."_2" : "";
$sbar=($type==0) ? "sidebar".$post['uid'] : "";
$mbar=($type==0) ? "mainbar".$post['uid'] : "";
      $text="$L[TBL1] id=".$post['id'].">
".        "  $postheaderrow 
".        "  $L[TR]>
".        "    <td class=\"b n1 $tbar1\" style=\"border-bottom:0; border-right:0; min-width: 180px;\" height=17>
".        "      ".userlink($post,'u').


/*" ".gettokenstring($post[uid])."</td> //[KAWA] Removed in favor of profile field
".*/        "    </td>
".        "    <td class=\"b n1 $tbar2\" style=\"border-left:0\" width=100%>
".        "      $L[TBL] width=100%>
".        "        $L[TDns]>Posted on ".cdate($dateformat,$post[date])."$threadlink$revisionstr</td>
".        "        $L[TDnsr]>$postlinks</td>
".        "      $L[TBLend]
".        "  $L[TR] valign=top>
".        "    <td class='b n1 sfont $sbar' style=\"border-top:0;\">
";
      if($type==0){
        $location=($post[ulocation]?'<br>From: '.postfilter2($post[ulocation]):'');
        $lastpost=($post[ulastpost]?timeunits(ctime()-$post[ulastpost]):'none');

        $picture=($post[uusepic]?"<img src=gfx/userpic.php?id=$post[uid]>":'');

        if($post[mood] > 0) { // 2009-07 Sukasa: This entire if block.  Assumes $post[uid] and $post[mood] were checked before the function call
          $mood = $sql->fetchq("select `url`, `local`, 1 `existing` from `mood` where `user`=$post[uid] and `id`=$post[mood] union select '' `url`, 0 `local`, 0 `existing`");
          if ($mood[existing]) {
            $picture = (!$mood[local] ? "<img src=\"".$mood[url]."\">" : "<img src=gfx/userpic.php?id=".$post[uid]."_".$post[mood].">" );
          }
        }

		if ($post[usign]) {
			if($post[usignsep])
				$post[usign] = "<br><br><small>". $post['usign'] ."</small>";
			else
				$post[usign] = "<br><br><small>____________________<br>". $post['usign'] ."</small>";
		}

        //2/26/2007 xkeeper - making "posts: [[xxx/]]yyy" conditional instead of constant
        $text.=
		 grouplink($post[uid])."
".        "      <br>".postfilter2($post[utitle])."
".        "      <br>Level: ".calclvl($exp)."
".        "      <br>$picture
".        "      <br>Posts: ".($post[num]?"$post[num]/":'')."$post[uposts]
".        "      <br>EXP: $exp
".        "      <br>Next: ".calcexpleft($exp)."
".        "      <br>
".        "      <br>Since: ".cdate('m-d-y',$post[uregdate])."
".        "      $location
".        "      <br>
".        "      <br>Last post: $lastpost
".        "      <br>Last view: ".timeunits(ctime()-$post[ulastview])."
";
      }else{
   $text.="
".        "      Posts: $post[num]/$post[uposts]
";
}
      $text.=
          "    </td>
".        "    <td class=\"b n2 $mbar\">".postfilter(amptags($post,$post['uhead']). $post[text] .amptags($post,$post['usign']))."</td>
".        "$L[TBLend]
";
    }
    return $text;
  }
?>