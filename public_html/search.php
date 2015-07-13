<?php
require("lib/common.php");
require("lib/threadpost.php");
loadsmilies();

pageheader("Search");

$showforum=1;

$HARBL="<table class=harbl";
print "<style>.harbl{width:100%;border-collapse:collapse;padding:0}.lame{border-right:1px solid black;border-top:1px solid black}.superlame{border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black}.bblone{border-bottom:1px solid black}form{margin:0}optgroup{font-style:normal}</style>
<script>
var lit='search';
function field(show) {
	document.getElementById(lit+'btn').className='n2 superlame';
	document.getElementById(lit+'div').style.display='none';
	document.getElementById(show+'btn').className='lame';
	document.getElementById(show+'div').style.display='block';
	lit=show;
}
</script>";

$categs=$sql->query("SELECT * "
                   ."FROM categories "
                   ."WHERE id IN ".cats_with_view_perm()." "
                   ."ORDER BY ord");
while($c=$sql->fetch($categs))
  $categ[$c[id]]=$c;
$forums=$sql->query("SELECT f.* "
                   ."FROM forums f "
                   ."LEFT JOIN categories c ON c.id=f.cat "
                   ."WHERE f.id IN ".forums_with_view_perm()." AND c.id IN ".cats_with_view_perm()." "
                   ."ORDER BY c.ord,ord");

$cat=-1;
$fsel="<select name=f><option value=0>Any</option>";

while($forum=$sql->fetch($forums)){
  if($forum[cat]!=$cat){
    $cat=$forum[cat];
    $fsel.="<optgroup label='".($categ[$cat][title])."'>";
  }
  $sel="";
  if($_GET[f]==$forum[id]) $sel=" selected";
  $fsel.="<option value=$forum[id]$sel>$forum[title]</option>";
}
$fsel.="</select>";

print "<table cellspacing=\"0\" class=\"c1\">
".    "  <tr class=\"h\">
" .   "    <td class=\"b h\">Search</td>
"  .  "  <tr>
"   . "    <td class=\"b n1\" style=padding:10 height=150 valign=top>
"    ."      <form action=search.php method=get>
"   . "      <table cellpadding=0 cellspacing=0><tr><td>
"  .  "      <table cellpadding=0 cellspacing=0 style=cursor:default;width:100%><tr><td width=15 class=bblone>&nbsp;<td width=60 class=lame style='border-left:1px solid black' align=center id=searchbtn onclick=field('search')><b>Search</b><td width=60 class='n2 superlame' align=center id=filterbtn onclick=field('filter')><b>Filters</b><td width=60 class='n2 superlame' align=center id=emptybtn onclick=field('empty')><b>Harbl</b><td class=bblone>&nbsp;</table>
" .   "      <tr><td style='padding:3;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black'>
".    "      <div id=searchdiv>$HARBL>
" .   "      <tr><td>Search for:&nbsp;<td><input type=\"text\" name=q size=40 value='".htmlspecialchars(stripslashes($_GET[q]), ENT_QUOTES)."'><td>&nbsp;<input type=\"submit\" class=\"submit\" name=action value=Search></td>
"  .  "      <tr><td><td>in:&nbsp;<input type=\"radio\" class=\"radio\" name=w value=0 id=threadtitle".(($_GET[w]==0)?" checked":"")."><label for=threadtitle>&nbsp;thread title</label>&nbsp;<input type=\"radio\" class=\"radio\" name=w value=1 id=posttext".(($_GET[w]==1)?" checked":"")."><label for=posttext>&nbsp;post text</label><td>
"   . "      </table></div>
"    ."      <div id=filterdiv style=display:none>$HARBL>
"   . "      <tr><td>Forum:&nbsp;<td>$fsel
"  .  "      <tr><td>Thread creator:&nbsp;<td><input type=\"text\" name=t value='".htmlspecialchars(stripslashes($_GET[t]), ENT_QUOTES)."'>
" .   "      <tr><td>Post creator:&nbsp;<td><input type=\"text\" name=p value='".htmlspecialchars(stripslashes($_GET[p]), ENT_QUOTES)."'>
".    "      <tr><td> <td><font class='sfont'>% acts as a generic wildcard.</font>
" .   "      <tr><td><td><input type=\"submit\" class=\"submit\" name=action value=Search>
"  .  "      </table></div>
"   . "      <div id=emptydiv style=display:none>";



print "      </div></td></table>
"    ."      </form>
"   . "    </td>
"  .  "</table>";

if($_GET[action] == "Search") {
if(strlen($_GET[q]) > 3) {
	print "<br>
".        "<div id=pleasewait>
".        "<table cellspacing=\"0\" class=\"c1\">
".        "  <tr class=\"h\">
".        "    <td class=\"b h\">Results</td>
".        "  <tr>
".        "    <td class=\"b n1\" style=padding:25 align=center>
".        "    Search in progress...
".        "</table>
".        "</div>
".        "<div id=youwaited style=display:none>
".        "<table cellspacing=\"0\" class=\"c1\">
".        "  <tr class=\"h\">
".        "    <td class=\"b h\">Results</td>
".        "</table>";
if($_GET[w] == 1) {

  $searchquery = $_GET[q];
  $searchquery = preg_replace("@[^\" a-zA-Z0-9]@", "", $searchquery);
  preg_match_all("@\"([^\"]+)\"@", $searchquery, $matches);
  foreach($matches[0] as $key => $value) {
    $searchquery = str_replace($value, " !".$key." ", $searchquery);
  }
  $searchquery = str_replace("\"", "", $searchquery);
  while(strpos($searchquery, "  ") !== FALSE) {
    $searchquery = str_replace("  ", " ", $searchquery);
  }
  $wordor = explode(" ", trim($searchquery));
  $dastring = "";
  $lastbool = 0;
  $defbool = "AND";
  $nextbool = "";
  $searchfield = "pt.text";
  $boldify = array();
  foreach($wordor as $numbah => $werdz) {
	if($lastbool == 0) {
		$nextbool = $defbool;
	}
    if((($werdz == "OR") || ($werdz == "AND")) && !empty($dastring)) {
		$nextbool = $werdz;
		$lastbool = 1;
	}
	else {
		if(substr($werdz, 0, 1) == "!") {
			$dastring .= $nextbool." ".$searchfield." LIKE '%".$matches[1][substr($werdz, 1)]."%' ";
			$boldify[$numbah] = "@".$matches[1][substr($werdz, 1)]."@i";
		}
		else {
			$dastring .= $nextbool." ".$searchfield." LIKE '%".$werdz."%' ";
			$boldify[$numbah] = "@".$werdz."@i";
		}
	}
  }
  $dastring = trim(substr($dastring, strlen($defbool)));
//  print $dastring;
  $fieldlist='';
  $ufields=array('id','name','posts','regdate','lastpost','lastview','location','sex','group_id','rankset','title','usepic','head','sign');
  foreach($ufields as $field)
    $fieldlist.="u.$field u$field,";

  if(strlen($_GET[p]))
    $dastring.=" AND u.name LIKE '$_GET[p]' ";

  if($_GET[f])
    $dastring.=" AND f.id='$_GET[f]' ";

  $posts=$sql->query("SELECT $fieldlist p.*,  pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.id tid, t.title ttitle, t.forum tforum "
                    ."FROM posts p "
                    ."LEFT JOIN poststext pt ON p.id=pt.id "
                    ."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) "
                    ."LEFT JOIN users u ON p.user=u.id "
                    ."LEFT JOIN threads t ON p.thread=t.id "
                    ."LEFT JOIN forums f ON f.id=t.forum "
                    ."LEFT JOIN categories c ON c.id=f.cat "
                    ."WHERE $dastring AND ISNULL(pt2.id) "
                   ."AND f.id IN ".forums_with_view_perm()." AND c.id IN ".cats_with_view_perm()." "

                    ."ORDER BY p.id");


  while($post=$sql->fetch($posts)){
    $pthread[id]=$post[tid];
    $pthread[title]=$post[ttitle];
    $post[text]=preg_replace($boldify,"<b>\\0</b>",$post[text]); 
    print "<br>
".         threadpost($post,0,$pthread);
  }
}
else {
  $searchquery = $_GET[q];
  $searchquery = preg_replace("@[^\" a-zA-Z0-9]@", "", $searchquery);
  preg_match_all("@\"([^\"]+)\"@", $searchquery, $matches);
  foreach($matches[0] as $key => $value) {
    $searchquery = str_replace($value, " !".$key." ", $searchquery);
  }
  $searchquery = str_replace("\"", "", $searchquery);
  while(strpos($searchquery, "  ") !== FALSE) {
    $searchquery = str_replace("  ", " ", $searchquery);
  }
  $wordor = explode(" ", trim($searchquery));
  $dastring = "";
  $lastbool = 0;
  $defbool = "AND";
  $nextbool = "";
  $searchfield = "t.title";
  $boldify = array();
  foreach($wordor as $numbah => $werdz) {
	if($lastbool == 0) {
		$nextbool = $defbool;
	}
    if((($werdz == "OR") || ($werdz == "AND")) && !empty($dastring)) {
		$nextbool = $werdz;
		$lastbool = 1;
	}
	else {
		if(substr($werdz, 0, 1) == "!") {
			$dastring .= $nextbool." ".$searchfield." LIKE '%".$matches[1][substr($werdz, 1)]."%' ";
			$boldify[$numbah] = "@".$matches[1][substr($werdz, 1)]."@i";
		}
		else {
			$dastring .= $nextbool." ".$searchfield." LIKE '%".$werdz."%' ";
			$boldify[$numbah] = "@".$werdz."@i";
		}
	}
  }
  $dastring = trim(substr($dastring, strlen($defbool)));
  
  $fieldlist='';
  $ufields=array('id','name','sex','group_id');
  foreach($ufields as $field)
    $fieldlist.="u1.$field u1$field, u2.$field u2$field, ";

  if(strlen($_GET[t]))
    $dastring.=" AND u1.name LIKE '$_GET[t]' ";

  if($_GET[f])
    $dastring.=" AND f.id='$_GET[f]' ";

  if($page<1) $page=1;
  $threads=$sql->query("SELECT $fieldlist t.*, f.id fid, f.title ftitle "
                      ."FROM threads t "
                      ."LEFT JOIN users u1 ON u1.id=t.user "
                      ."LEFT JOIN users u2 ON u2.id=t.lastuser "
                      ."LEFT JOIN forums f ON f.id=t.forum "
                      ."LEFT JOIN categories c ON f.cat=c.id "
                      ."WHERE $dastring "
                   ."AND f.id IN ".forums_with_view_perm()." AND c.id IN ".cats_with_view_perm()." "
                      ."ORDER BY t.sticky DESC, t.lastdate DESC "
                      ."LIMIT ".(($page-1)*$loguser[tpp]).",".$loguser[tpp]);


  $forum[threads]=$sql->resultq("SELECT count(*) "
                               ."FROM threads t "
			       ."LEFT JOIN users u1 ON u1.id=t.user "
                               ."LEFT JOIN forums f ON f.id=t.forum "
                               ."LEFT JOIN categories c ON f.cat=c.id "
                               ."WHERE $dastring "
                   ."AND f.id IN ".forums_with_view_perm()." AND c.id IN ".cats_with_view_perm()." "
);
  print "<br>
".      "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"h\">
".      "    <td class=\"b h\" width=17>&nbsp;</td>
".      "    <td class=\"b h\" width=17>&nbsp;</td>
".($showforum?
        "    <td class=\"b h\">Forum</td>":'')."
".      "    <td class=\"b h\">Title</td>
".      "    <td class=\"b h\" width=130>Started by</td>
".      "    <td class=\"b h\" width=50>Replies</td>
".      "    <td class=\"b h\" width=50>Views</td>
".      "    <td class=\"b h\" width=130>Last post</td>
";

  $lsticky=0;
  for($i=1;$thread=$sql->fetch($threads);$i++){
    $pagelist='';
    if($thread[replies]>=$loguser[ppp]){
      for($p=1;$p<=1+floor($thread[replies]/$loguser[ppp]);$p++)
        $pagelist.=" <a href=thread.php?id=$thread[id]&page=$p>$p</a>";
      $pagelist=" <font class=sfont>(pages: $pagelist)</font>";
    }

    $status='';
    if($thread[closed])                $status.='off';
    if($thread[replies]>=50)           $status.='hot';

    if($log){
      if(!$thread[isread]) $status.='new';
    }else
      if($thread[lastdate]>(ctime()-3600)) $status.='new';

    if($status)
      $status="<img src=img/status/$status.png>";
    else
      $status='&nbsp;';

    if(!$thread[title])
      $thread[title]='�';

    if($thread[icon])
      $icon="<img src=$thread[icon] height=15>";
    else
      $icon='&nbsp;';

    if($thread['sticky'])
      $tr = 'n1';
    else
      $tr = ($i % 2 ? 'n2' :'n3');

    if(!$thread['sticky'] && $lsticky)
      print
          "  <tr class=\"c\">
".        "    <td class=\"b\" colspan=".($showforum?8:7)." style='font-size:1px'>&nbsp;</td>
";
    $lsticky=$thread['sticky'];

    print "<tr class=\$tr\" align=\"center\">
".        "    <td class=\"b n1\">$status</td>
".        "    <td class=\"b\">$icon</td>
".($showforum?
          "    <td class=\"b\"><a href=forum.php?id=$thread[fid]>$thread[ftitle]</a></td>":'')."
".        "    <td class=\"b\" align=\"left\">".($thread[ispoll]?"<img src=img/poll.gif height=10>":"")."<a href=thread.php?id=$thread[id]>".forcewrap(htmlval($thread[title]))."</a>$pagelist</td>
".        "    <td class=\"b\">".userlink($thread,'u1')."</td>
".        "    <td class=\"b\">$thread[replies]</td>
".        "    <td class=\"b\">$thread[views]</td>
".        "    <td class=\"b\"><nobr>".cdate($dateformat,$thread[lastdate])."</nobr><br><font class=sfont>by ".userlink($thread,'u2')."</font></td>
";
  }

  if($forum[threads]<=$loguser[tpp])
    $fpagelist='<br>';
  else{
    $fpagelist='Pages:';
    for($p=1;$p<=1+floor(($forum[threads]-1)/$loguser[tpp]);$p++)
      if($p==$page)
        $fpagelist.=" $p";
      else
        $fpagelist.=" <a href=search.php?q=".urlencode($_GET[q])."&action=Search&w=0&f=0&t=&p=&page=$p>$p</a>";
  }

  print "</table>
".      "$fpagelist
";


}
print "</div>
".    "<script>document.getElementById('pleasewait').style.display='none';document.getElementById('youwaited').style.display='block';</script>";
  }
}
pagefooter();

?>
