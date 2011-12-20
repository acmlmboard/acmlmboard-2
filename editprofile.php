<?php
  require 'lib/common.php';


  $targetuserid = $loguser['id'];

  if (isset($_GET['id']) && acl_for_user($_GET[id],"edit-user")) {
    $temp = $_GET['id'];
    if (checknumeric($temp))
      $targetuserid = $temp;
  }

  if($_POST[action]=='Edit profile' && $_POST[pass]!='' && $_POST[pass]==$_POST[pass2]&&$targetuserid==$loguser[id])
    setcookie('pass',packlcookie(md5($_POST[pass])),2147483647);

  

  pageheader('Edit profile');


  global $user, $userrpg;

  $user = $sql->fetchq("SELECT * FROM users WHERE `id` = $targetuserid");
  $userrpg = getstats($userrpgdata = $sql->fetchq('SELECT u.name, u.posts, u.regdate, r.* '
                                                 .'FROM users u '
                                                 .'LEFT JOIN usersrpg r ON u.id=r.id '
                                                 ."WHERE u.id=$user[id]"));


  echo $userrpg['eq1'];
  $act=$_POST[action];
  if(!$log){
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    You must be logged in to edit your profile!<br>
".        "    <a href=./>Back to main</a> or <a href=login.php>login</a>
".        "$L[TBLend]
";
  }elseif($loguser[power]==-1){
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Banned users may not edit their profile.<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
  }elseif($loguser[power] < 4 && $user[power] == 4){
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Root user profiles cannot be edited by non-root users.<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
  }elseif($_POST[pass] && $_POST[pass2] && $_POST[pass]!=$_POST[pass2]){
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    The two passwords you entered don't match.<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
  }elseif(!$act){
    

    $listsex=array('Male','Female','N/A');
    $listpower=array(-1 => '-1 Banned',0 => ' 0 Normal User',' 1 Local Moderator',' 2 Global Moderator',' 3 Administrator');
    if($loguser['power'] == 4)
    	$listpower[4] = " 4 Root";
    $listpm=array('allow','disallow');
    $listrename=array('disallow','allow');

    if($user[birth]!=-1){
      $birthday=getdate($user[birth]);
      $birthM=$birthday[mon];
      $birthD=$birthday[mday];
      $birthY=$birthday[year];
    }

    $dateformats=array('','m-d-y','d-m-y','y-m-d','Y-m-d','m/d/Y','d.m.y','M j Y','D jS M Y');
    $timeformats=array('','h:i A','h:i:s A','H:i','H:i:s');

    foreach($dateformats as $format)
      $datelist[$format]=($format?$format.' ('.cdate($format,ctime()).')':'');
    foreach($timeformats as $format)
      $timelist[$format]=($format?$format.' ('.cdate($format,ctime()).')':'');

    $passinput="$L[INPp]=pass size=13 maxlength=32> / Retype: $L[INPp]=pass2 size=13 maxlength=32>";
    $birthinput="
".        "      Month: $L[INPt]=birthM size=2 maxlength=2 value=$birthM>
".        "      Day:   $L[INPt]=birthD size=2 maxlength=2 value=$birthD>
".        "      Year:  $L[INPt]=birthY size=4 maxlength=4 value=$birthY>
".        "    ";
    $tzoffinput="
".        "      $L[INPt]=tzoffH size=3 maxlength=3 value=".(int)($user[tzoff]/3600)."> :
".        "      $L[INPt]=tzoffM size=2 maxlength=2 value=".floor(abs($user[tzoff]/60)%60).">
".        "    ";

    print "$L[TBL1]>
".        " <form action='editprofile.php?id=$targetuserid' method='post' enctype='multipart/form-data'>
".
           catheader('Login information')."
".           (acl_for_user($targetuserid,"edit-user") ? fieldrow('Username'        ,fieldinput(40,255,'name'     )) : fieldrow('Username'        ,$user[name]                 ))."
".           fieldrow('Password'        ,$passinput                     )."
";

if (acl_for_user($_GET[id],"edit-user"))
  print
           catheader('Administrative bells and whistles')."
".           fieldrow('Powerlevel'      ,fieldselect('power',$user[power],$listpower))."
".           fieldrow('PM sending'      ,fieldselect('pmblocked',$user[pmblocked],$listpm))."
".           fieldrow('Thread Retitling',fieldselect('renamethread',$user[renamethread],$listrename))."
";

  print
           catheader('Appearance')."
".           fieldrow('Rankset'		,fieldselect('rankset', $user['rankset'], ranklist()))."
".           (checkctitle()?fieldrow('Title'           ,fieldinput(40,255,'title'     )):"")."
".           fieldrow('Picture'         ,'<input type=file name=picture size=40> <input type=checkbox name=picturedel value=1 id=picturedel><label for=picturedel>Erase</label><br><font class=sfont>Must be PNG, JPG or GIF, within 60KB, within '.$avatardimx.'x'.$avatardimy.'.</font>')."
".           fieldrow('MINIpic'         ,'<input type=file name=minipic size=40> <input type=checkbox name=minipicdel value=1 id=minipicdel><label for=minipicdel>Erase</label><br><font class=sfont>Must be PNG or GIF, within 10KB, exactly '.$minipicsize.'x'.$minipicsize.'.</font>')."
";

if (acl_for_user($_GET[id],"edit-user"))
  print    catheader('RPG Stats')."
  ".           fieldrow('Coins'       ,fieldinputrpg(9, 7, 'GP'))."
  ".           fieldrow('Frog Coins'  ,fieldinputrpg(9, 7, 'gcoins' ))."
  ".           fieldrow('Weapon'      ,itemselect('eq1', $userrpgdata['eq1'], 1))."
  ".           fieldrow('Armour'      ,itemselect('eq2', $userrpgdata['eq2'], 2))."
  ".           fieldrow('Shield'      ,itemselect('eq3', $userrpgdata['eq3'], 3))."
  ".           fieldrow('Helmet'      ,itemselect('eq4', $userrpgdata['eq4'], 4))."
  ".           fieldrow('Boots'      ,itemselect('eq5', $userrpgdata['eq5'], 5))."
  ".           fieldrow('Accessory'      ,itemselect('eq6', $userrpgdata['eq6'], 6))."
  ";

  print
           catheader('Personal information')."
".           fieldrow('Sex'             ,fieldoption('sex',$user[sex],$listsex))."
".           fieldrow('Real name'       ,fieldinput(40, 60,'realname'  ))."
".           fieldrow('Location'        ,fieldinput(40, 60,'location'  ))."
".           fieldrow('Birthday'        ,$birthinput                    )."
".           fieldrow('Bio'             ,fieldtext ( 5, 80,'bio'       ))."
".
           catheader('Post layout')."
".           fieldrow('Header'          ,fieldtext ( 5, 80,'head'      ))."
".           fieldrow('Signature'       ,fieldtext ( 5, 80,'sign'      ))."
".           fieldrow('Signature line'  ,fieldoption('signsep',$user[signsep],array('Display','Hide')))."
".
           catheader('Contact information')."
".           fieldrow('Email address'   ,fieldinput(40, 60,'email'     ))."
".           fieldrow('Homepage URL'    ,fieldinput(40,200,'homeurl'   ))."
".           fieldrow('Homepage name'   ,fieldinput(40, 60,'homename'  ))."
".
           catheader('Options')."
".           fieldrow('Theme'           ,fieldselect('theme', $user['theme'], themelist()))."
".           fieldrow('Timezone offset' ,$tzoffinput                    )."
".           fieldrow('Posts per page'  ,fieldinput( 3,  3,'ppp'       ))."
".           fieldrow('Threads per page',fieldinput( 3,  3,'tpp'       ))."
".           fieldrow('Long pagelists'  ,fieldoption('longpages',$user[longpages],array('Abbreviate as needed','Always display in entirety')))."
".           fieldrow('Font size'       ,fieldinput( 3,  3,'fontsize'  ))."
".           fieldrow('Date format'     ,fieldinput(15, 15,'dateformat').' or preset: '.fieldselect('presetdate',0,$datelist))."
".           fieldrow('Time format'     ,fieldinput(15, 15,'timeformat').' or preset: '.fieldselect('presettime',0,$timelist))."
".           fieldrow('Post layouts', fieldoption('blocklayouts',acl('block-layouts'),array('Show everything in general', 'Block everything')))."
".           fieldrow('Sprites', fieldoption('sprites',acl('disable-sprites'),array('Show them', 'Disable sprite layer')))."
".
           catheader('&nbsp;')."
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>$L[INPs]=action value='Edit profile'></td>
".        " </form>
".        "$L[TBLend]
";
  }elseif($act=='Edit profile'){

    $minipic='minipic';
    if($fname=$_FILES[minipic][name]){
      $error="";
      $fext=strtolower(substr($fname,-4));
      if($fext!=".png" && $fext!=".gif") {
        $error.="<br>- Invalid minipic file type; must be PNG or GIF.";
      }
      if($_FILES[minipic][size]>10240) {
        $error.="<br>- Minipic file size is too high; must be 10KB or less.";
      }
      if(!$error){
        $tmpfile=$_FILES[minipic][tmp_name];
        list($width,$height,$type)=getimagesize($tmpfile);
        if($width!=$minipicsize || $height!=$minipicsize) {
          $error.="<br>- Minipic size must be {$minipicsize}x$minipicsize.";
        } else if($type!=3 && $type!=1) {
          $error.="<br>- Minipic file broken or not a valid PNG or GIF image!";
        } else {
	  if($type==1) $type="gif";
	  else $type="png";
          $minipic="\"data:image/$type;base64,".base64_encode(file_get_contents($tmpfile))."\"";
        }
      }
      if($error) print $error;
    }
    if($_POST['minipicdel']) $minipic="\"\"";
    $usepic='usepic';
    if($fname=$_FILES[picture][name]){
      $fext=strtolower(substr($fname,-4));
      $error='';

      $exts=array('.png','.jpg','.gif');

	//[KAWA] TODO: replace with token effect
	/*
      if($x_hacks["180px"]) {
        $dimx=180;
	$dimy=180;
	$size=2*61440;
      }
	*/

      $validext=false;
      $extlist='';
      foreach($exts as $ext){
        if($fext==$ext)
          $validext=true;
        $extlist.=($extlist?', ':'').$ext;
      }
      if(!$validext)
        $error.="<br>- Invalid file type, must be either: $extlist";

      if(($fsize=$_FILES[picture][size])>$avatarsize)
        $error.="<br>- File size is too high, limit is $avatarsize bytes";

      if(!$error){
        $tmpfile=$_FILES[picture][tmp_name];
        $file="userpic/$user[id]";
        $file2="userpic/s$user[id]";

        list($width,$height,$type)=getimagesize($tmpfile);

        if($type==1) $img1=imagecreatefromgif ($tmpfile);
        if($type==2) $img1=imagecreatefromjpeg($tmpfile);
        if($type==3) $img1=imagecreatefrompng ($tmpfile);

        if($type<=3){
          $r=imagesx($img1)/imagesy($img1);
          $img2=imagecreatetruecolor($avatardimxs,$avatardimys);
          imagecolorallocate($img2,0,0,0);

          if($r>1)
            imagecopyresampled($img2,$img1,0,round($avatardimys*(1-1/$r)/2),0,0,$avatardimxs,$avatardimys/$r,imagesx($img1),imagesy($img1));
          else
            imagecopyresampled($img2,$img1,round($avatardimxs*(1-$r)/2),0,0,0,$avatardimxs*$r,$avatardimys,imagesx($img1),imagesy($img1));
          imagepng($img2,$file2);
        }

        if($width<=$avatardimx && $height<=$avatardimy && $type<=3)
          copy($tmpfile,"userpic/$user[id]");
        elseif($type<=3){
          if($r>1){
            $img2=imagecreatetruecolor($avatardimx,$avatardimy/$r);
            imagecopyresampled($img2,$img1,0,0,0,0,$avatardimx,$avatardimy/$r,imagesx($img1),imagesy($img1));
          }else{
            $img2=imagecreatetruecolor($avatardimx*$r,$avatardimy);
            imagecopyresampled($img2,$img1,0,0,0,0,$avatardimx*$r,$avatardimy,imagesx($img1),imagesy($img1));
          }
          imagepng($img2,$file);
        }else{
          $error.="<br>- Bad image format";
        }

        $usepic=1;
      }else
        print $error;
    }
    if($_POST['picturedel']) $usepic=0;

    //check for table breach
    if(tvalidate($_POST['head'].$_POST['sign'])!=0)
    {
      $error.="- Table tag count mismatch in post layout; layout wiped.<br />";
      $_POST['head']=$_POST['sign']="";
    }
    if(tvalidate($_POST['title'])!=0)
    {
      $error.="- Table tag count mismatch in custom title; title erased<br />";
      $_POST['title']="";
    }

    if($_POST[fontsize]<30)
      $_POST[fontsize]=30;
    if($_POST[fontsize]>999)
      $_POST[fontsize]=999;
    if($_POST[sex]<0)
      $_POST[sex]=0;
    if($_POST[sex]>2)
      $_POST[sex]=2;

    $pass=$_POST[pass];
    if(!strlen($_POST[pass2])) $pass="";
    $tztotal=$_POST[tzoffH]*3600+$_POST[tzoffM]*60*($_POST[tzoffH]<0?-1:1);
    if(!$birthM && !$birthD && !$birthY)
      $birthday=-1;
    else
      $birthday=mktime(0,0,0,$birthM,$birthD,$birthY);

    $dateformat=($_POST[presetdate]?$_POST[presetdate]:$_POST[dateformat]);
    $timeformat=($_POST[presettime]?$_POST[presettime]:$_POST[timeformat]);

    if (acl_for_user($_GET[id],"edit-user")) {
      
      $spent = ($userrpg['GP'] + $userrpgdata['spent']) - $_POST['GP'];
      $sql->query("UPDATE usersrpg SET "
	             . setfield('eq1').","
	             . setfield('eq2').","
	             . setfield('eq3').","
	             . setfield('eq4').","
	             . setfield('eq5').","
	             . setfield('eq6').","
               . "`spent` = $spent,"
               . setfield('gcoins')
               . " WHERE `id` = $user[id]"
               );

      //Update admin bells and whistles
      $targetpower = $_POST['power'];
      $targetpower = min($targetpower, $loguser[power]);
      $targetname = $_POST['name'];

      if ($sql->resultq("SELECT COUNT(`name`) FROM `users` WHERE `name` = '$targetname' AND `id` != $user[id]")) {
        $targetname = $user[name];
        $error.="- Name already in use, will not change<br />";
      }

      $sql->query("UPDATE users SET "
	               . setfield('renamethread').","
	               . setfield('pmblocked').","
                 . "`power` = $targetpower, "
                 . "`name` = '$targetname'"
                 . " WHERE `id`=$user[id]"
                 );

    }

    $sql->query('UPDATE users SET '
               . ($pass?'pass="'.md5($pass).'",':'')
               . setfield('sex')     .','
               . setfield('ppp')     .','
               . setfield('tpp')     .','
               . setfield('signsep').','
               . setfield('longpages').','
               . setfield('rankset') .','
               . (checkctitle()?(setfield('title')   .','):'')
               . setfield('realname').','
               . setfield('location').','
               . setfield('email')   .','
               . setfield('homeurl') .','
               . setfield('homename').','
               . setfield('head')    .','
               . setfield('sign')    .','
               . setfield('bio')     .','
               . setfield('fontsize').','
               . setfield('theme')   .','
               . "tzoff=$tztotal,"
               . "birth=$birthday,"
               . "usepic=$usepic,"
               . "minipic=$minipic,"
               . "dateformat='$dateformat',"
               . "timeformat='$timeformat' "
               . "WHERE `id`=$user[id]"
               );

    $trainingHelmetTokenID = 200; //CHANGEME
    $disableSpritesTokenID = 201; //CHANGEME
    if($_POST['blocklayouts'] == 1)
      $sql->query('INSERT IGNORE INTO usertokens VALUES('.$user[id].', '.$trainingHelmetTokenID.')');
    else
      $sql->query('DELETE FROM usertokens WHERE u='.$user[id].' AND t='.$trainingHelmetTokenID);
    if($_POST['sprites'] == 1)
      $sql->query('INSERT IGNORE INTO usertokens VALUES('.$user[id].', '.$disableSpritesTokenID.')');
    else
      $sql->query('DELETE FROM usertokens WHERE u='.$user[id].' AND t='.$disableSpritesTokenID);
	
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    <font color='#FF0000' style='font-weight: bold' />$error</font>
".        "    Profile changes saved!<br>
".        "    ".redirect("profile.php?id=$user[id]",'the updated profile')."
".        "$L[TBLend]
";
  }

  pagefooter();

  function setfield($field){
    return "$field='$_POST[$field]'";
  }

  function catheader($title){
    global $L;
    return "  $L[TRh]>
".         "    $L[TDh] colspan=2>$title</td>";
  }

  function fieldrow($title,$input){
    global $L;
    return "  $L[TR]>
".         "    $L[TD1c]>$title:</td>
".         "    $L[TD2]>$input</td>";
  }

  function fieldinput($avatarsize,$max,$field){
    global $L,$user;
    return "$L[INPt]=$field size=$avatarsize maxlength=$max value=\"".str_replace("\"", "&quot;", $user[$field])."\">";
//  return "$L[INPt]=$field size=$avatarsize maxlength=$max value=\"".htmlval($loguser[$field])."\">";
  }

  function fieldinputrpg($avatarsize,$max,$field){
    global $L,$userrpg;
    return "$L[INPt]=$field size=$avatarsize maxlength=$max value=\"".str_replace("\"", "&quot;", $userrpg[$field])."\">";
//  return "$L[INPt]=$field size=$avatarsize maxlength=$max value=\"".htmlval($loguser[$field])."\">";
  }

  function fieldtext($rows,$cols,$field){
    global $L,$user;
    return "$L[TXTa]=$field rows=$rows cols=$cols>".htmlval($user[$field]).'</textarea>';
//  return "$L[TXTa]=$field rows=$rows cols=$cols>".htmlval($loguser[$field]).'</textarea>';
  }

  function fieldoption($field,$checked,$choices){
    global $L;
    $text='';
    $sel[$checked]=' checked=1';
    //[KAWA] Added <label> so the text is clickable.
    foreach($choices as $key=>$val)
      $text.="
".           "      <label>$L[INPr]=$field value=$key$sel[$key]>$val &nbsp;</label>";
    return "$text
".         "    ";
  }

// 2/22/2007 xkeeper - takes $choices (array with "value" and "name")
  function fieldselect($field,$checked,$choices){
    global $L;
    $text="
".        "$L[SEL]=$field>";
    $sel[$checked]=' selected';
    foreach($choices as $key=>$val)
      $text.="
".           "      $L[OPT]=\"$key\"$sel[$key]>$val</option>";
    return "$text
".         "    ";
  }

  function itemselect($field,$current,$cat) {
    global $sql, $L;

    $viewhidden = 0;

    if (isadmin())
      $viewhidden = 1;

    $items = $sql->query("SELECT * FROM items WHERE `cat` = 0 UNION SELECT * FROM items WHERE `cat` = $cat AND `hidden` <= $viewhidden");

    $text="
".        "$L[SEL]=$field>";

    while ($item = $sql->fetch($items)) {
      $text.="
".           "      $L[OPT]=\"$item[id]\"";
      if ($current == $item['id'])
        $text.=" selected";

      $text.="> $item[name]</option>";
    }
    return "$text    ";
  }

  function themelist() {
		global $sql, $loguser;

		$t = $sql -> query("SELECT `theme`, COUNT(*) AS 'count' FROM `users` GROUP BY `theme`");
		while ($x = $sql -> fetch($t)) $themeuser[$x['theme']] = intval($x['count']);

		$themes = unserialize(file_get_contents("themes_serial.txt"));
		$themelist = array();
		foreach($themes as $t)
			$themelist[$t[1]] = $t[0] . " (".$themeuser[$t[1]].")";

		return $themelist;
  }

  function ranklist() {
    global $sql, $loguser;
    $r=$sql->query("SELECT * FROM ranksets ORDER BY id ASC");
    while($d=$sql->fetch($r)) $rlist[$d[id]]=$d[name];

    return $rlist;
  }
?>
