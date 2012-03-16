<?php
  require 'lib/common.php';


  needs_login(1);

  $targetuserid = $loguser['id'];

  if (isset($_GET['id'])) {
    $temp = $_GET['id'];
    if (checknumeric($temp))
      $targetuserid = $temp;
  }

  if (!can_edit_user($targetuserid)) $targetuserid = 0;

  if ($targetuserid == 0) {
     pageheader('No permission');
     no_perm();
  }


        $blockroot = " AND `default` >= 0 ";

      if (has_perm('no-restrictions')) $blockroot = "";

      $allgroups = $sql->query("SELECT * FROM `group` WHERE `primary`=1 $blockroot ORDER BY sortorder ASC");

      $listgroup = array();

      while ($group = $sql->fetch($allgroups)) {
        $listgroup[$group['id']] = $group['title'];
      }



  if($_POST[action]=='Edit profile' && $_POST[pass]!='' && $_POST[pass]==$_POST[pass2]&&$targetuserid==$loguser[id])
    setcookie('pass',packlcookie(md5($_POST[pass].$pwdsalt)),2147483647);

  pageheader('Edit profile');


  global $user, $userrpg;

  $user = $sql->fetchq("SELECT * FROM users WHERE `id` = $targetuserid");
  $userrpg = getstats($userrpgdata = $sql->fetchq('SELECT u.name, u.posts, u.regdate, r.* '
                                                 .'FROM users u '
                                                 .'LEFT JOIN usersrpg r ON u.id=r.id '
                                                 ."WHERE u.id=$user[id]"));


  echo $userrpg['eq1'];
  $act=$_POST[action];
/*  if(!$log){
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
  }else*/
  if($_POST[pass] && $_POST[pass2] && $_POST[pass]!=$_POST[pass2]){
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    The two passwords you entered don't match.<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
  }elseif(!$act){
    

    $listsex=array('Male','Female','N/A');



/*    $listpower=array(-1 => '-1 Banned',0 => ' 0 Normal User',' 1 Local Moderator',' 2 Global Moderator',' 3 Administrator');


    if($loguser['power'] == 4)
      $listpower[4] = " 4 Root";*/



      $alltz = $sql->query("SELECT name FROM `timezones`"); 

      $listtimezones = array();
      while ($tz = $sql->fetch($alltz)) {
        $listtimezones[$tz['name']] = $tz['name'];
      }

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
".           (has_perm("edit-users") ? fieldrow('Username'        ,fieldinput(40,255,'name'     )) : fieldrow('Username'        ,$user[name]                 ))."
".(has_perm("has-displayname") ? fieldrow('Display name',fieldinput(40,255,'displayname')) : "" )."
".           fieldrow('Password'        ,$passinput                     )."
";

if (has_perm("edit-users"))
  print
           catheader('Administrative bells and whistles')."
".           fieldrow('Group'      ,fieldselect('group_id',$user['group_id'],$listgroup))."
";

  print
           catheader('Appearance')."
".           fieldrow('Rankset'   ,fieldselect('rankset', $user['rankset'], ranklist()))."
".           ((checkctitle()) ?fieldrow('Title'           ,fieldinput(40,255,'title'     )):"")."
".           fieldrow('Picture'         ,'<input type=file name=picture size=40> <input type=checkbox name=picturedel value=1 id=picturedel><label for=picturedel>Erase</label><br><font class=sfont>Must be PNG, JPG or GIF, within 60KB, within '.$avatardimx.'x'.$avatardimy.'.</font>')."
".           fieldrow('MINIpic'         ,'<input type=file name=minipic size=40> <input type=checkbox name=minipicdel value=1 id=minipicdel><label for=minipicdel>Erase</label><br><font class=sfont>Must be PNG or GIF, within 10KB, exactly '.$minipicsize.'x'.$minipicsize.'.</font>')."
";

if (has_perm("edit-users"))
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
".           fieldrow('Timezone'      ,fieldselect('timezone',$user['timezone'],$listtimezones))."
".           fieldrow('Posts per page'  ,fieldinput( 3,  3,'ppp'       ))."
".           fieldrow('Threads per page',fieldinput( 3,  3,'tpp'       ))."
".           fieldrow('Long pagelists'  ,fieldoption('longpages',$user[longpages],array('Abbreviate as needed','Always display in entirety')))."
".           fieldrow('Font size'       ,fieldinput( 3,  3,'fontsize'  ))."
".           fieldrow('Date format'     ,fieldinput(15, 15,'dateformat').' or preset: '.fieldselect('presetdate',0,$datelist))."
".           fieldrow('Time format'     ,fieldinput(15, 15,'timeformat').' or preset: '.fieldselect('presettime',0,$timelist))."
".           fieldrow('Post layouts', fieldoption('blocklayouts',$user['blocklayouts'],array('Show everything in general', 'Block everything')))."
".           fieldrow('Sprites', fieldoption('blocksprites',$user['blocksprites'],array('Show them', 'Disable sprite layer')))."
".           fieldrow('Hide from Online Views', fieldoption('hidden',$user['hidden'],array('Show me online', 'Never show me online')))."
".           fieldrow('Redirect Type', fieldoption('redirtype',$user['redirtype'],array('Display redirect page', 'Instant redirect')))."
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

    if (has_perm("edit-users")) {
      
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
      $targetgroup = $_POST['group_id'];
      checknumeric($targetgroup);
      if (!isset($listgroup[$targetgroup])) $targetgroup = 0;

//      $targetpower = min($targetpower, $loguser[power]);
      $targetname = $_POST['name'];

      if ($sql->resultq("SELECT COUNT(`name`) FROM `users` WHERE (`name` = '$targetname' OR `displayname` = '$targetname') AND `id` != $user[id]")) {
        $targetname = $user[name];
        $error.="- Name already in use, will not change<br />";
      }

      $sql->query("UPDATE users SET "
                 . ($targetgroup?"`group_id` = $targetgroup, ":"")
                 . "`name` = '$targetname'"
                 . " WHERE `id`=$user[id]"
                 );

    }

    $sql->query('UPDATE users SET '
               . ($pass?'pass="'.md5($pass.$pwdsalt).'",':'')
               . (has_perm("has-displayname")?(setfield('displayname')   .','):'')
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
               . setfield('blocklayouts')   .','
               . setfield('blocksprites')   .','
               . setfield('hidden') .','
               . setfield('redirtype') .','
               . setfield('timezone') .','
               . "tzoff=$tztotal,"
               . "birth=$birthday,"
               . "usepic=$usepic,"
               . "minipic=$minipic,"
               . "dateformat='$dateformat',"
               . "timeformat='$timeformat' "
               . "WHERE `id`=$user[id]"
               );

/*    $trainingHelmetTokenID = 200; //CHANGEME
    $disableSpritesTokenID = 201; //CHANGEME
    if($_POST['blocklayouts'] == 1)
      $sql->query('INSERT IGNORE INTO usertokens VALUES('.$user[id].', '.$trainingHelmetTokenID.')');
    else
      $sql->query('DELETE FROM usertokens WHERE u='.$user[id].' AND t='.$trainingHelmetTokenID);
    if($_POST['sprites'] == 1)
      $sql->query('INSERT IGNORE INTO usertokens VALUES('.$user[id].', '.$disableSpritesTokenID.')');
    else
      $sql->query('DELETE FROM usertokens WHERE u='.$user[id].' AND t='.$disableSpritesTokenID);*/
  
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    <font color='#FF0000' style='font-weight: bold' />$error</font>
".        "    Profile changes saved!<br>
".        "    ".redirect("profile.php?id=$user[id]",'the updated profile')."
".        "$L[TBLend]
";
  }

  pagefooter();

?>