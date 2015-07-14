<?php
  require 'lib/common.php';

  $rdmsg="";
  if($_COOKIE['pstbon']){
	header("Set-Cookie: pstbon=".$_COOKIE['pstbon']."; Max-Age=1; Version=1");
 $rdmsg="<script language=\"javascript\">
	function dismiss()
	{
		document.getElementById(\"postmes\").style['display'] = \"none\";
	}
</script>
	<div id=\"postmes\" onclick=\"dismiss()\" title=\"Click to dismiss.\"><br>
".      "<table cellspacing=\"0\" class=\"c1\" width=\"100%\" id=\"edit\"><tr class=\"h\"><td class=\"b h\">";
if($_COOKIE['pstbon']==-1){
	$rdmsg.="Item Sold<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
".	"<tr><td class=\"b n1\" align=\"left\">The $pitem[name] has been unequipped and sold.</td></tr></table></div>";
} elseif($_COOKIE['pstbon']==-2){
	$rdmsg.="Item Bought<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
".	"<tr><td class=\"b n1\" align=\"left\">The $item[name] has been bought and equipped!</td></tr></table></div>"; }
}

  $action=$_GET[action];
  if ($_POST[action]=="save"&&has_perm('manage-shop-items')) {
    checknumeric($_GET[id]);
    $set="";
    $id = $_GET[id];
    $stype="";
    if ($_GET[id]!=-1) {
      for($i=0;$i<9;$i++) {
        $stype.=(preg_match('/^x/', $_POST[$stat[$i]])?'m':'a');
        $set.="`s".$stat[$i]."`=".preg_replace('/[+x\.]/','',(strlen($_POST[$stat[$i]])?$_POST[$stat[$i]]:'0')).", ";
      }
      $set.="`name`='$_POST[name]', `desc`='$_POST[desc]', `stype`='$stype', `coins`='$_POST[coins]', `coins2`='$_POST[coins2]', `cat`='$_POST[cat]', `hidden`=".($_POST['hidden'] ? "1" : "0");
      $sql->query("UPDATE items SET $set WHERE id='$_GET[id]'");

    } else {
      for($i=0;$i<9;$i++) {
        $stype.=(preg_match('/^x/', $_POST[$stat[$i]])?'m':'a');
        $set.="`s$stat[$i]`=".preg_replace('/[x+-\.]/','',$_POST[$stat[$i]]).", ";
        $names.="`s$stat[$i]`, ";
        $vals.="'".preg_replace('/[x+-\.]/','',$_POST[$stat[$i]])."', ";
      }
      $names.="`name`, `desc`, `stype`, `coins`, `coins2`, `cat`, `hidden`";
      $vals.="'$_POST[name]', '$_POST[desc]', '$stype', '$_POST[coins]', '$_POST[coins2]', '$_POST[cat]', ".($_POST['hidden'] ? "1" : "0")."";
      $sql->query("INSERT INTO items ($names) VALUES ($vals)");
      $id = $sql->insertid();
    }
    header("location: shop.php?action=desc&id=$id");       
  }


  needs_login(1);

  $cat=$_GET[cat];
  checknumeric($cat);
$f=fopen("shop-ref.log","a");
fwrite($f,"[".date("m-d-y H:i:s")."] ".$ref."\n");
fclose($f);

  if(!has_perm('use-item-shop')){
     error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
  }elseif (($_GET[action]=='edit'||$_GET[action]=='save'||$_GET[action]=='delete')&&!has_perm('manage-shop-items')) { //Added (Sukasa)
     error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
  }else {
    $user=$sql->fetchq('SELECT u.name, u.posts, u.regdate, r.* '
                      .'FROM users u '
                      .'LEFT JOIN usersrpg r ON u.id=r.id '
                      ."WHERE u.id=$loguser[id]");
    $p=$user[posts];
    $d=(ctime()-$user[regdate])/86400;
    $st=getstats($user);
    $GP=$st[GP];

    switch($action){
      case 'delete': //Added (Sukasa)
        checknumeric($_GET[id]);
        if ($_GET[id]) { //Can't delete nothing
          $sql->query("DELETE FROM items WHERE id='$_GET[id]'");
          for ($i=1;$i<7;$i++)
            $sql->query("UPDATE usersrpg SET `eq$i` = 0 WHERE `eq$i`='$_GET[id]'");
        }
      case '':
        $shops  =$sql->query ('SELECT * FROM itemcateg ORDER BY corder');
        $eq     =$sql->fetchq("SELECT * FROM usersrpg WHERE id=$loguser[id]");
        $eqitems=$sql->query ('SELECT * FROM items');

        while($item=$sql->fetch($eqitems))
          $items[$item[id]]=$item;

        while($shop=$sql->fetch($shops))
          $shoplist.=
              "  <tr>
".            "    <td class=\"b n2\">
".            "      <a href=shop.php?action=items&cat=$shop[id]#status>$shop[name]</a>
".            "      <br><font class=sfont>$shop[description]</font>
".            "    </td>
".            "    <td class=\"b n1\" align=\"center\"><a href=shop.php?action=desc&id=".$eq["eq$shop[id]"].">".$items[$eq["eq$shop[id]"]][name]."</a></td>
";
  pageheader('Item shop');
        print "<img src=gfx/status.php?u=$loguser[id]>
";
    if($_COOKIE['pstbon']){ print $rdmsg;}
print       "<br>
".            "<table cellspacing=\"0\" class=\"c1\">
".            "  <tr class=\"h\">
".            "    <td class=\"b h\">Shop</td>
".            "    <td class=\"b h\">Item equipped</td>
".            "$shoplist
".            "</table>
";
      break;
      case 'edit': //Added (Sukasa)
        checknumeric($_GET[id]);
        $item=$sql->fetchq("SELECT * FROM items WHERE id='$_GET[id]' union select * from items where id='-1'");
  pageheader('Item shop');
        print "<style>
".            "   .disabled {color:#888888}
".            "   .higher   {color:#abaffe}
".            "   .equal    {color:#ffea60}
".            "   .lower    {color:#ca8765}
".            "</style>
";
        $statlist='';
        $catlist="<option value='99'>Not Listed</option>";
        $shops=$sql->query ('SELECT * FROM itemcateg ORDER BY corder');
        while($shop=$sql->fetch($shops)) {
          $catlist.='<option value="'.$shop[id]."\"".(($shop[id]==$item[cat])||($item[cat]==99&&isset($_GET[cat])&&$shop[id]==$_GET[cat])?
            "selected='selected'":"").">".$shop[name]."</option>";
        }
        for($i=0;$i<9;$i++){
          $st=$item["s$stat[$i]"];
          if(substr($item[stype],$i,1)=='m'){
            $st=vsprintf('x%1.2f',$st/100);
          }else{
            if($st>0) $st="+$st";
          }
          $itst=$item["s$stat[$i]"];
          $eqst=$eqitem["s$stat[$i]"];
          if(!$color){
                if($itst> 0) $cl='higher';
            elseif($itst==0) $cl='equal';
            elseif($itst< 0) $cl='lower';
          }else
            $cl='';

          $statlist.= "
".                    "    <td class=\"b n2 align=\"center\"'><input type=\"text\" name='$stat[$i]' size='4' value='$st'></td>";
          $stathdr.=  "
".                    "    <td class=\"b n1\" align=\"center\" width=6%>$stat[$i]</td>
";
        }
        print "<form action='shop.php?action=save&id=$item[id]' method='post'><table cellspacing=\"0\" class=\"c1\">
".            "  <td class=\"b n1\" align=\"center\"><a href=shop.php>Return to shop list</a>
".            "</table> <br>
".            "<img src=gfx/status.php?u=$loguser[id]><br>
".            "<br>
".            "<table cellspacing=\"0\" class=\"c1\" style=width:300px>
".            "  <tr class=\"h\" align=left>
".            "    <td class=\"b h\" colspan=9><input type=\"text\" name='name' size='40' value=\"".str_replace("\"","&quot;",$item[name])."\"> <img src='img/coin.gif'> 
".            "      <input type=\"text\" name='coins' size='7' value=\"".str_replace("\"","&quot;",$item[coins])."\"> <img src='img/coin2.gif'> 
".            "      <input type=\"text\" name='coins2' size='7' value=\"".str_replace("\"","&quot;",$item[coins2])."\"><input type=\"checkbox\" name='hidden' id='hidden' ".($item[hidden]?"checked":"")."><label for='hidden'>Hidden Item</label></td>
".            "  <tr>
".            "    $stathdr
".            "  <tr>
".            "    $statlist
".            "  <tr>
".            "    <td class=\"b n2\" colspan=8><input type=\"text\" name='desc' size='40' value=\"".str_replace("\"","&quot;",$item[desc])."\">  
".            "      <select name='cat' style='width: 115px'>$catlist</select>
".            "      <td class=\"b n2\"><input type=\"submit\" class=\"submit\" name='Save' value='Save'> 
".            "    </td>
".            "</table></form>
";
      break;
      case 'desc':
        checknumeric($_GET[id]);
        $item=$sql->fetchq("SELECT * FROM items WHERE id='$_GET[id]'");
  pageheader('Item shop');
        print "<style>
".            "   .disabled {color:#888888}
".            "   .higher   {color:#abaffe}
".            "   .equal    {color:#ffea60}
".            "   .lower    {color:#ca8765}
".            "</style>
";
        $statlist='';
        for($i=0;$i<9;$i++){
          $st=$item["s$stat[$i]"];
          if(substr($item[stype],$i,1)=='m'){
            $st=vsprintf('x%1.2f',$st/100);
            if($st==100) $st='&nbsp;';
          }else{
            if($st>0) $st="+$st";
            if(!$st) $st='&nbsp;';
          }
          $itst=$item["s$stat[$i]"];
          $eqst=$eqitem["s$stat[$i]"];
          $edit="";
          if (has_perm('manage-shop-items')) //Added (Sukasa)
            $edit=" [<a href='shop.php?action=edit&id=$item[id]'>Edit</a>] [<a href='shop.php?action=delete&id=$item[id]'>Delete</a>]";
          if(!$color){
                if($itst> 0) $cl='higher';
            elseif($itst==0) $cl='equal';
            elseif($itst< 0) $cl='lower';
          }else
            $cl='';

          $statlist.= "
".                    "    <td class=\"b n2 align=\"center\" $cl'>$st</td>";
          $stathdr.=  "
".                    "    <td class=\"b n1\" align=\"center\" width=6%>$stat[$i]</td>
";
        }
        print "<table cellspacing=\"0\" class=\"c1\">
".            "  <td class=\"b n1\" align=\"center\"><a href=shop.php>Return to shop list</a>
".            "</table> <br>
".            "<img src=gfx/status.php?u=$loguser[id]><br>
".            "<br>
".            "<table cellspacing=\"0\" class=\"c1\" style=width:300px>
".            "  <tr class=\"h\" align=left>
".            "    <td class=\"b h\" colspan=9>$item[name]$edit</td>
".            "  <tr>
".            "    $stathdr
".            "  <tr>
".            "    $statlist
".            "  <tr>
".            "    <td class=\"b n2\" colspan=9><font class=sfont>$item[desc]</font></td>
".            "</table>
";
      break;
      case 'items':
        
        $eq=$sql->fetchq("SELECT eq$cat AS e FROM usersrpg WHERE id=$loguser[id]");
        $eqitem=$sql->fetchq("SELECT * FROM items WHERE id=$eq[e]");

        $edit="";
        if (has_perm('manage-shop-items'))
          $edit=" | <a href='shop.php?action=edit&id=-1&cat=$cat'>Add new item</a>";

  pageheader('Item shop');
        print "<script>
".            "  function preview(user,item,cat,name){
".            "    document.getElementById('prev').src='gfx/status.php?u='+user+'&it='+item+'&ct='+cat+'&'+Math.random();
".            "    document.getElementById('pr').innerHTML='Equipped with<br>'+name+'<br>---------->';
".            "  }
".            "</script>
".            "<style>
".            "   .disabled {color:#888888}
".            "   .higher   {color:#abaffe}
".            "   .equal    {color:#ffea60}
".            "   .lower    {color:#ca8765}
".            "</style>
".            "
".            "<table cellspacing=\"0\" class=\"c1\">
".            "  <td class=\"b n1\" align=\"center\"><a href=shop.php>Return to shop list</a> $edit
".            "</table>
".            "<br>
".            "<table cellspacing=\"0\" id=status>
".            "  <td class=\"nb\" width=256><img src=gfx/status.php?u=$loguser[id]></td>
".            "  <td class=\"nb\" align=\"center\" width=150>
".            "    <font class=fonts>
".            "      <div id=pr></div>
".            "    </font>
".            "  </td>
".            "  <td class=\"nb\">
".            "    <img src=img/_.png id=prev>
".            "</table>
".            "<br>
";
        $atrlist='';
        for($i=0;$i<9;$i++)
          $atrlist.="    <td class=\"b h\" width=6%>$stat[$i]</td>
";

        $seehidden = 0;
        if (has_perm('manage-shop-items'))
          $seehidden = 1;

        $items=$sql->query('SELECT * FROM items '
                          ."WHERE (cat=$cat OR cat=0) AND `hidden` <= $seehidden "
                          .'ORDER BY type,coins');

        print "<table cellspacing=\"0\" class=\"c1\">
".            "  <tr class=\"h\">
".            "    <td class=\"b h\" width=100>Commands</td>
".            "    <td class=\"b n2\" width=1 rowspan=10000>&nbsp;</td>
".            "    <td class=\"b h\">Item</td>
".            "$atrlist
".            "    <td class=\"b h\" width=6%><img src=img/coin.gif></td>
".            "    <td class=\"b h\" width=6%><img src=img/coin2.gif></td>
";

        while($item=$sql->fetch($items)){
          $buy=    "<a href=shop.php?action=buy&id=$item[id]>Buy</a>";
          $sell=   "<a href=shop.php?action=sell&cat=$cat>Sell</a>";
          $preview="<a href=#status onclick=\"preview($loguser[id],$item[id],$cat,'".addslashes($item[name])."')\">Preview</a>";

              if($item[id] && $item[id]==$eq[e]) $comm=$sell;
          elseif($item[id] && $item[coins]<=$GP && $item[coins2]<=0) $comm="$buy | $preview";
          elseif(!$item[id] && !$eq[e])          $comm='-';
          else                                   $comm=$preview;

          if($item[id]==$eqitem[id]) $color=' class=equal';
          elseif($item[coins]>$GP)   $color=' class=disabled';
          else                       $color='';
          $atrlist='';
          for($i=0;$i<9;$i++){
            $st=$item["s$stat[$i]"];
            if(substr($item[stype],$i,1)=='m'){
              $st=vsprintf('x%1.2f',$st/100);
              if($st==100) $st='&nbsp;';
            }else{
              if($st>0) $st="+$st";
              if(!$st) $st='&nbsp;';
            }
            $itst=$item["s$stat[$i]"];
            $eqst=$eqitem["s$stat[$i]"];

            if(!$color && substr($item[stype],$i,1)==substr($eqitem[stype],$i,1)){
                  if($itst> $eqst) $cl='higher';
              elseif($itst==$eqst) $cl='equal';
              elseif($itst< $eqst) $cl='lower';
            }else
              $cl='';

            $atrlist.= "
".            "    <td class=\"b n2 align=\"center\" $cl'>$st</td>";
          }

          print
              "  <tr$color>
".            "    <td class=\"b n2\" align=\"center\">$comm</td>
".            "    <td class=\"b n1\"><b><a href=shop.php?action=desc&id=$item[id]>$item[name]</a></b></td>
".            "$atrlist
".            "    <td class=\"b n1\" align=\"right\">$item[coins]</td>
".            "    <td class=\"b n1\" align=\"right\">$item[coins2]</td>
";
        }
        print "</table>
";
      break;
      case 'buy':
        if(!strstr($ref,"shop.php?action=items&cat=") || ctime()-$loguser[lastview]<1) die();

        $id=$_GET[id];
        checknumeric($id);
        $item=$sql->fetchq("SELECT * FROM items WHERE id=$id");

        if($item[coins]<=$GP && $item[coins2]<=0 && $item[cat]) { //FIXME
          $pitem=$sql->fetchq("SELECT coins FROM items WHERE id=".$user['eq'.$item[cat]]);
          $pitem[coins]=intval($pitem[coins]); //fixes the problem if no prior item had been equipped/$pitem[coins] is empty for whatever reason [blackhole89]
          $sql->query("UPDATE usersrpg "
                     ."SET eq$item[cat]=$id, spent=spent-$pitem[coins]*0.6+$item[coins] "
                     ."WHERE id=$loguser[id]");

	  if($config['ircshopnotice']) sendirc("{irccolor-name}".get_irc_displayname()." {irccolor-base}is now equipped with {irccolor-title}$item[name]{irccolor-base}.");
              /*if($loguser[redirtype]==0){ //Classical Redirect
  $loguser['blocksprites']=1;
  pageheader('Item shop');
          print
              "<table cellspacing=\"0\" class=\"c1\">
".            "  <td class=\"b n1\" align=\"center\">
".            "    The $item[name] has been bought and equipped!<br>
".            "    ".redirect('shop.php','the shop')."
".            "</table>
";
             } else { //Modern redirect*/
                  redirect("shop.php",-2);
             //}
        }
      break;
      case 'sell':
        $pitem=$sql->fetchq("SELECT name, coins FROM items "
                           ."WHERE id=".$user['eq'.$cat]);
        $sql->query("UPDATE usersrpg "
                   ."SET eq$cat=0, spent=spent-$pitem[coins]*0.6 "
                   ."WHERE id=$loguser[id]");

              /*if($loguser[redirtype]==0){ //Classical Redirect
  $loguser['blocksprites']=1;
  pageheader('Item shop');
        print "<table cellspacing=\"0\" class=\"c1\">
".            "  <td class=\"b n1\" align=\"center\">
".            "    The $pitem[name] has been unequipped and sold.<br>
".            "    ".redirect('shop.php','the shop')."
".            "</table>
";
             } else { //Modern redirect*/
                  redirect("shop.php",-1);
             //}
      break;
      default:
    }
  }

  pagefooter();
?>