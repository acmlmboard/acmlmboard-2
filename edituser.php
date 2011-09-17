<?php
  require 'lib/common.php';
  pageheader('Edit user');

  $act=$_POST[action];
  if(!$log){
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    You must be logged in to do this!<br>
".        "    <a href=./>Back to main</a> or <a href=login.php>login</a>
".        "$L[TBLend]
";
  }elseif($loguser[power]<3){
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    This function is restricted to administrators.<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
  }elseif(!$act){
    $user=$sql->fetchq("SELECT * FROM users WHERE id='".intval($_GET['id'])."'");

	if($user['power'] == 4 && $user['id'] != $loguser['id'])
	{
	    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Root users cannot be edited.<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
		pagefooter();
		die();
	}

    $listsex=array('Male','Female','N/A');
    $listpower=array(-1 => '-1 Banned',0 => ' 0 Normal User',' 1 Local Moderator',' 2 Global Moderator',' 3 Administrator');
    if($user['power'] == 4)
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

    $passinput="$L[INPp]=pass size=13 maxlength=32>";
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
".        " <form action=edituser.php?id=$_GET[id] method=post enctype=multipart/form-data>
".        " <input type=hidden name=logmd5 value='$loguser[pass]'>
".
           catheader('Login information')."
".           fieldrow('Username'        ,fieldinput(40, 60,'name'      ))."
".           fieldrow('Password'        ,$passinput                     )."
".
           catheader('Appearance')."
".           fieldrow('Title'           ,fieldinput(40,255,'title'     ))."
".           fieldrow('Picture'         ,'<input type=file name=picture size=40>')."
".
           catheader('Administrative bells and whistles')."
".           fieldrow('Powerlevel'      ,fieldselect('power',$user[power],$listpower))."
".           fieldrow('PM sending'      ,fieldselect('pmblocked',$user[pmblocked],$listpm))."
".           fieldrow('Thread Retitling',fieldselect('renamethread',$user[renamethread],$listrename))."
".
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
".           fieldrow('Long pagelists'  ,fieldoption('longpages',$loguser[longpages],array('Abbreviate as needed','Always display in entirety')))."
".           fieldrow('Font size'       ,fieldinput( 3,  3,'fontsize'  ))."
".           fieldrow('Date format'     ,fieldinput(15, 15,'dateformat').' or preset: '.fieldselect('presetdate',0,$datelist))."
".           fieldrow('Time format'     ,fieldinput(15, 15,'timeformat').' or preset: '.fieldselect('presettime',0,$timelist))."
".
           catheader('&nbsp;')."
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>$L[INPs]=action value='Edit profile'></td>
".        " </form>
".        "$L[TBLend]
";
  }elseif($act=='Edit profile'){
    //counter-XSRF validation
    if($_POST['logmd5']!=$loguser[pass] || !$loguser[pass] || $loguser[pass]=="")
    {
      print "$L[TBL1]>
".          "  $L[TD1c]>
".          "    Stored hash mismatch. For security reasons, you shall not pass.<br>
".          "    <a href=./>Back to main</a>
".          "$L[TBLend]
";
      pagefooter();
      die();
    }
    
    //[KAWA] Copying the whole rootcheck thing here because this page has bad logic.
    //Gotta check it at this point anyway because the target may have become root while you were still filling out fields.
	$user=$sql->fetchq("SELECT * FROM users WHERE id='".intval($_GET['id'])."'");
	if($user['power'] == 4)
	{
		if($user['id'] != $loguser['id'])
		{
			print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Root users cannot be edited.<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
			pagefooter();
			die();
		}
		if($_POST['power'] < 4)
		{
			$_POST['power'] = 4; //[KAWA] Prevent taking away root. Hackish, I know. ABXD disables the select.
		}
	}


    $usepic='usepic';
    if($fname=$_FILES[picture][name]){
      $fext=strtolower(substr($fname,-4));
      $error='';

      $exts=array('.png','.jpg','.gif');
      $dimx=100;
      $dimy=100;
      $dimxs=60;
      $dimys=60;
      $size=30720;

      $validext=false;
      $extlist='';
      foreach($exts as $ext){
        if($fext==$ext)
          $validext=true;
        $extlist.=($extlist?', ':'').$ext;
      }
      if(!$validext)
        $error.="<br>- Invalid file type, must be either: $extlist";

      if(($fsize=$_FILES[picture][size])>$size)
        $error.="<br>- File size is too high, limit is $size bytes";

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
          $img2=imagecreatetruecolor($dimxs,$dimys);
          imagecolorallocate($img2,0,0,0);

          if($r>1)
            imagecopyresampled($img2,$img1,0,round($dimys*(1-1/$r)/2),0,0,$dimxs,$dimys/$r,imagesx($img1),imagesy($img1));
          else
            imagecopyresampled($img2,$img1,round($dimxs*(1-$r)/2),0,0,0,$dimxs*$r,$dimys,imagesx($img1),imagesy($img1));
          imagepng($img2,$file2);
        }

        if($width<=$dimx && $height<=$dimy && $type<=3)
          copy($tmpfile,"userpic/$user[id]");
        elseif($type<=3){
          if($r>1){
            $img2=imagecreatetruecolor($dimx,$dimy/$r);
            imagecopyresampled($img2,$img1,0,0,0,0,$dimx,$dimy/$r,imagesx($img1),imagesy($img1));
          }else{
            $img2=imagecreatetruecolor($dimx*$r,$dimy);
            imagecopyresampled($img2,$img1,0,0,0,0,$dimx*$r,$dimy,imagesx($img1),imagesy($img1));
          }
          imagepng($img2,$file);
        }else{
          $error.="<br>- Bad image format";
        }

        $usepic=1;
      }else
        print $error;
    }

    if($_POST[fontsize]<30)
      $_POST[fontsize]=30;
    if($_POST[fontsize]>999)
      $_POST[fontsize]=999;

    $pass=$_POST[pass];
    $tztotal=$_POST[tzoffH]*3600+$_POST[tzoffM]*60*($_POST[tzoffH]<0?-1:1);
    if(!$birthM && !$birthD && !$birthY)
      $birthday=-1;
    else
      $birthday=mktime(0,0,0,$birthM,$birthD,$birthY);

    $dateformat=($_POST[presetdate]?$_POST[presetdate]:$_POST[dateformat]);
    $timeformat=($_POST[presettime]?$_POST[presettime]:$_POST[timeformat]);

    $_POST[name]=trim($_POST[name]);	// deleteme?
    
    //[KAWA] Prevent editing the <select> to have a "Root" entry from working.
    if($user['power'] < 4 && $_POST['power'] >= 4)
    	$_POST['power'] = $user['power'];

    $sql->query('UPDATE users SET '
               . ($pass?'pass="'.md5($pass).'",':'')
               . setfield('name')     .','
	       . setfield('sex')      .','
               . setfield('ppp')      .','
               . setfield('tpp')      .','
	       . setfield('longpages').','
               . setfield('title')    .','
               . setfield('realname') .','
               . setfield('location') .','
               . setfield('email')    .','
               . setfield('homeurl')  .','
               . setfield('homename') .','
               . setfield('head')     .','
               . setfield('sign')     .','
               . setfield('bio')      .','
               . setfield('fontsize') .','
	       . setfield('renamethread').','
               . setfield('theme')    .','
	       . setfield('power')    .','
	       . setfield('pmblocked').','
               . "tzoff=$tztotal,"
               . "birth=$birthday,"
               . "usepic=$usepic,"
               . "dateformat='$dateformat',"
               . "timeformat='$timeformat' "
               ."WHERE id=".intval($_GET[id])
               );

    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Profile changes saved!<br>
".        "    ".redirect("profile.php?id=$_GET[id]",'the user\'s profile')."
".        "$L[TBLend]
";
  }

  pagefooter();

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

  function fieldinput($size,$max,$field){
    global $L,$user;
    return "$L[INPt]=$field size=$size maxlength=$max value=\"".htmlval($user[$field])."\">";
  }

  function fieldtext($rows,$cols,$field){
    global $L,$user;
    return "$L[TXTa]=$field rows=$rows cols=$cols>".htmlval($user[$field]).'</textarea>';
  }

  function fieldoption($field,$checked,$choices){
    global $L;
    $text='';
    $sel[$checked]=' checked=1';
    foreach($choices as $key=>$val)
      $text.="
".           "      $L[INPr]=$field value=$key$sel[$key]>$val &nbsp;";
    return "$text
".         "    ";
  }

  function fieldcheck($field,$checked,$val){
    global $L;
    return "$L[INPc]=$field checked=$checked>$val";
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

  function setfield($field){
    return "$field='$_POST[$field]'";
  }

  function themelist() {
		global $sql, $user;
		$s	= $sql -> query("SELECT `id`, `name` FROM `themes` WHERE `ord` >= '0' OR `id` = '$user[theme]' ORDER BY `ord`");		// it'd be grand if I knew how to get the usercounts easily too .'D

		$t	= $sql -> query("SELECT `theme`, COUNT(*) AS 'count' FROM `users` GROUP BY `theme`");
		while ($x = $sql -> fetch($t)) $themeuser[$x['theme']] = intval($x['count']);

//		print_r($themeuser);

		while ($x = $sql -> fetch($s)) $themelist[$x['id']] = $x['name'] ." (". $themeuser[$x['id']] .")";
		return $themelist;
  }
?>
