<?php
  require 'lib/common.php';
  $action=$_GET[action];
  if ($_POST[action]=="save"&&isadmin()) {
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
      $id = mysql_insert_id();
    }
    header("location: shop.php?action=desc&id=$id");       
  }


  pageheader('Item shop');

  $cat=$_GET[cat];
  checknumeric($cat);
$f=fopen("shop-ref.log","a");
fwrite($f,"[".date("m-d-y H:i:s")."] ".$ref."\n");
fclose($f);

  if(!$log){
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    You must be logged in to access the Item Shop!<br>
".        "    <a href=./>Back to main</a> or <a href=login.php>login</a>
".        "$L[TBLend]
";
  }elseif($loguser[power]==-1){
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Banned users may not use the Item Shop!<br>
".        "    <a href=./>Back to main</a> or <a href=login.php>login</a>
".        "$L[TBLend]
";
  }elseif (($_GET[action]=='edit'||$_GET[action]=='save'||$_GET[action]=='delete')&&!isadmin()) { //Added (Sukasa)
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Your powerlevel is not high enough to manage items<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
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
        checknumeric($id);
        if ($id) { //Can't delete nothing
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
              "  $L[TR]>
".            "    $L[TD2]>
".            "      <a href=shop.php?action=items&cat=$shop[id]#status>$shop[name]</a>
".            "      <br><font class=sfont>$shop[description]</font>
".            "    </td>
".            "    $L[TD1c]><a href=shop.php?action=desc&id=".$eq["eq$shop[id]"].">".$items[$eq["eq$shop[id]"]][name]."</a></td>
";
        print "<img src=gfx/status.php?u=$loguser[id]>
".            "<br>
".            "$L[TBL1]>
".            "  $L[TRh]>
".            "    $L[TDh]>Shop</td>
".            "    $L[TDh]>Item equipped</td>
".            "$shoplist
".            "$L[TBLend]
";
      break;
      case 'edit': //Added (Sukasa)
        checknumeric($_GET[id]);
        $item=$sql->fetchq("SELECT * FROM items WHERE id='$_GET[id]' union select * from items where id='-1'");
        print "<style>
".            "   .disabled {color:#888888}
".            "   .higher   {color:#abaffe}
".            "   .equal    {color:#ffea60}
".            "   .lower    {color:#ca8765}
".            "</style>
";
        $statlist='';
        $catlist=$L[OPT]."='99'>Not Listed</option>";
        $shops=$sql->query ('SELECT * FROM itemcateg ORDER BY corder');
        while($shop=$sql->fetch($shops)) {
          $catlist.=$L[OPT].'="'.$shop[id]."\"".(($shop[id]==$item[cat])||($item[cat]==99&&isset($_GET[cat])&&$shop[id]==$_GET[cat])?
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
".                    "    $L[TD2oc]'>$L[INPt]='$stat[$i]' size='4' value='$st'></td>";
          $stathdr.=  "
".                    "    $L[TD1c] width=6%>$stat[$i]</td>
";
        }
        print "<form action='shop.php?action=save&id=$item[id]' method='post'>$L[TBL1]>
".            "  $L[TD1c]><a href=shop.php>Return to shop list</a>
".            "$L[TBLend] <br>
".            "<img src=gfx/status.php?u=$loguser[id]><br>
".            "<br>
".            "$L[TBL1] style=width:300px>
".            "  $L[TRh] align=left>
".            "    $L[TDh] colspan=9>$L[INPt]='name' size='40' value=\"".str_replace("\"","&quot;",$item[name])."\"> <img src='img/coin.gif'> 
".            "      $L[INPt]='coins' size='7' value=\"".str_replace("\"","&quot;",$item[coins])."\"> <img src='img/coin2.gif'> 
".            "      $L[INPt]='coins2' size='7' value=\"".str_replace("\"","&quot;",$item[coins2])."\">$L[INPc]='hidden' id='hidden' ".($item[hidden]?"checked":"")."><label for='hidden'>Hidden Item</label></td>
".            "  $L[TR]>
".            "    $stathdr
".            "  $L[TR]>
".            "    $statlist
".            "  $L[TR]>
".            "    $L[TD2] colspan=8>$L[INPt]='desc' size='40' value=\"".str_replace("\"","&quot;",$item[desc])."\">  
".            "      $L[INPl]='cat' style='width: 115px'>$catlist</select>
".            "      $L[TD2]>$L[INPs]='Save' value='Save'> 
".            "    </td>
".            "$L[TBLend]</form>
";
      break;
      case 'desc':
        checknumeric($_GET[id]);
        $item=$sql->fetchq("SELECT * FROM items WHERE id='$_GET[id]'");
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
          if (isadmin()) //Added (Sukasa)
            $edit=" [<a href='shop.php?action=edit&id=$item[id]'>Edit</a>] [<a href='shop.php?action=delete&id=$item[id]'>Delete</a>]";
          if(!$color){
                if($itst> 0) $cl='higher';
            elseif($itst==0) $cl='equal';
            elseif($itst< 0) $cl='lower';
          }else
            $cl='';

          $statlist.= "
".                    "    $L[TD2oc] $cl'>$st</td>";
          $stathdr.=  "
".                    "    $L[TD1c] width=6%>$stat[$i]</td>
";
        }
        print "$L[TBL1]>
".            "  $L[TD1c]><a href=shop.php>Return to shop list</a>
".            "$L[TBLend] <br>
".            "<img src=gfx/status.php?u=$loguser[id]><br>
".            "<br>
".            "$L[TBL1] style=width:300px>
".            "  $L[TRh] align=left>
".            "    $L[TDh] colspan=9>$item[name]$edit</td>
".            "  $L[TR]>
".            "    $stathdr
".            "  $L[TR]>
".            "    $statlist
".            "  $L[TR]>
".            "    $L[TD2] colspan=9><font class=sfont>$item[desc]</font></td>
".            "$L[TBLend]
";
      break;
      case 'items':
        
        $eq=$sql->fetchq("SELECT eq$cat AS e FROM usersrpg WHERE id=$loguser[id]");
        $eqitem=$sql->fetchq("SELECT * FROM items WHERE id=$eq[e]");

        $edit="";
        if (isadmin())
          $edit=" | <a href='shop.php?action=edit&id=-1&cat=$cat'>Add new item</a>";

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
".            "$L[TBL1]>
".            "  $L[TD1c]><a href=shop.php>Return to shop list</a> $edit
".            "$L[TBLend]
".            "<br>
".            "$L[TBL] id=status>
".            "  $L[TDn] width=256><img src=gfx/status.php?u=$loguser[id]></td>
".            "  $L[TDnc] width=150>
".            "    <font class=fonts>
".            "      <div id=pr></div>
".            "    </font>
".            "  </td>
".            "  $L[TDn]>
".            "    <img src=img/_.png id=prev>
".            "$L[TBLend]
".            "<br>
";
        $atrlist='';
        for($i=0;$i<9;$i++)
          $atrlist.="    $L[TDh] width=6%>$stat[$i]</td>
";

        $seehidden = 0;
        if (isadmin())
          $seehidden = 1;

        $items=$sql->query('SELECT * FROM items '
                          ."WHERE (cat=$cat OR cat=0) AND `hidden` <= $seehidden "
                          .'ORDER BY type,coins');

        print "$L[TBL1]>
".            "  $L[TRh]>
".            "    $L[TDh] width=100>Commands</td>
".            "    $L[TD2] width=1 rowspan=10000>&nbsp;</td>
".            "    $L[TDh]>Item</td>
".            "$atrlist
".            "    $L[TDh] width=6%><img src=img/coin.gif></td>
".            "    $L[TDh] width=6%><img src=img/coin2.gif></td>
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
".            "    $L[TD2oc] $cl'>$st</td>";
          }

          print
              "  $L[TR]$color>
".            "    $L[TD2c]>$comm</td>
".            "    $L[TD1]><b><a href=shop.php?action=desc&id=$item[id]>$item[name]</a></b></td>
".            "$atrlist
".            "    $L[TD1r]>$item[coins]</td>
".            "    $L[TD1r]>$item[coins2]</td>
";
        }
        print "$L[TBLend]
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

	  sendirc("{irccolor-name}".get_irc_displayname()." {irccolor-base}is now equipped with {irccolor-title}$item[name]{irccolor-base}.");
          print
              "$L[TBL1]>
".            "  $L[TD1c]>
".            "    The $item[name] has been bought and equipped!<br>
".            "    ".redirect('shop.php','the shop')."
".            "$L[TBLend]
";
        }
      break;
      case 'sell':
        $pitem=$sql->fetchq("SELECT name, coins FROM items "
                           ."WHERE id=".$user['eq'.$cat]);
        $sql->query("UPDATE usersrpg "
                   ."SET eq$cat=0, spent=spent-$pitem[coins]*0.6 "
                   ."WHERE id=$loguser[id]");

        print "$L[TBL1]>
".            "  $L[TD1c]>
".            "    The $pitem[name] has been unequipped and sold.<br>
".            "    ".redirect('shop.php','the shop')."
".            "$L[TBLend]
";
      break;
      default:
    }
  }

  pagefooter();
?>
