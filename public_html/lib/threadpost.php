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
	$isBlocked = $blocklayouts[$post['uid']] || $post['nolayout'] || acl("block-layouts");
    if($isBlocked)
      $post['usign'] = $post['uhead'] = "";
    //}

    //post has been deleted, display placeholder
    if($post[deleted]) {
      $postlinks="";
      if(isadmin() || ismod(getforumbythread($post[thread]))) {
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
      if($pthread)
        $threadlink=", in <a href=thread.php?id=$pthread[id]>$pthread[title]</a>";

      if($post[id])
        $postlinks="<a href=thread.php?pid=$post[id]#$post[id]>Link</a>";		// headlinks for posts

      //2007-03-08 blackhole89
      if($post[revision]>=2)
        $revisionstr=" (rev. $post[revision] of ".cdate($dateformat,$post[ptdate]).")";

      // I have no way to tell if it's closed (or otherwise impostable (hah)) so I can't hide it in those circumstances...
      if($post[thread])
        $postlinks.=($postlinks?' | ':'')."<a href=newreply.php?id=$post[thread]&pid=$post[id]>Quote</a>";

      // "Edit" link for admins or post owners, but not banned users
      if($post[thread] && ((!isbanned() && $post[user] == $loguser[id]) || ismod(getforumbythread($post[thread]))))
        $postlinks.=($postlinks?' | ':'')."<a href=editpost.php?pid=$post[id]>Edit</a>";

      if($post[id] && isadmin() || ismod(getforumbythread($post[thread])))
        $postlinks.=($postlinks?' | ':'')."<a href=editpost.php?pid=".urlencode(packsafenumeric($post[id]))."&act=delete>Delete</a>";

      if($post[id])
        $postlinks.=" | ID: $post[id]";

      if(acl_for_user($post[uid],"show-ips"))
        $postlinks.=($postlinks?' | ':'')."IP: $post[ip]";

      if(   acl_for_thread($post[thread],"see-history")
         && $post[maxrevision]>1) {
        $revisionstr.=" | Go to revision: ";
        for($i=1;$i<=$post[maxrevision];++$i)
          $revisionstr.="<a href=thread.php?pid=$post[id]&pin=$post[id]&rev=$i#$post[id]>$i</a> ";
      }

      // if quote enabled then if $postlink2 then postlink2 .= | [quote]

      // 2/22/2007 xkeeper - guess which moron forgot to close the </a>
      //[KAWA] Fun fact: <a name> is deprecated in favor of using IDs.
      //       That's right, you can use <anything id="foo"> in place of <a name="foo">!
      $text="$L[TBL1] id=".$post['id'].">
".        "  $L[TR]>
".        "    $L[TD1] style=border-bottom:0;border-right:0 height=17>
".        "      ".userlink($post,'u')./*" ".gettokenstring($post[uid])."</td> //[KAWA] Removed in favor of profile field
".*/        "    </td>
".        "    $L[TD1] style=border-left:0 width=100%>
".        "      $L[TBL] width=100%>
".        "        $L[TDns]>Posted on ".cdate($dateformat,$post[date])."$threadlink$revisionstr</td>
".        "        $L[TDnsr]>$postlinks</td>
".        "      $L[TBLend]
".        "  $L[TR] valign=top>
".        "    $L[TD1s] style=border-top:0>
".        "      <img src=img/_.png width=180 height=1>
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
          "      <br>".postfilter2($post[utitle])."
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
      }else $text.="
".        "      Posts: $post[num]/$post[uposts]
";

      $text.=
          "    </td>
".        "    <td class=\"b n2 mainbar".$post['uid']."\">".postfilter(amptags($post,$post['uhead']). $post[text] .amptags($post,$post['usign']))."</td>
".        "$L[TBLend]
";
    }
    return $text;
  }
?>
