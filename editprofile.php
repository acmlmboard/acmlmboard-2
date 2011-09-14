<?php
  require 'lib/common.php';

  $rs=13; //move to config?

  if($_POST[action]=='Edit profile' && $_POST[pass]!='' && $_POST[pass]==$_POST[pass2])
    setcookie('pass',packlcookie(md5($_POST[pass])),2147483647);

  pageheader('Edit profile');

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
  }elseif($_POST[pass] && $_POST[pass2] && $_POST[pass]!=$_POST[pass2]){
    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    The two passwords you entered don't match.<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
  }elseif(!$act){
    $listsex=array('Male','Female','N/A');

    if($loguser[birth]!=-1){
      $birthday=getdate($loguser[birth]);
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
".        "      $L[INPt]=tzoffH size=3 maxlength=3 value=".(int)($loguser[tzoff]/3600)."> :
".        "      $L[INPt]=tzoffM size=2 maxlength=2 value=".floor(abs($loguser[tzoff]/60)%60).">
".        "    ";

    print "$L[TBL1]>
".        " <form action=editprofile.php method=post enctype=multipart/form-data>
".
           catheader('Login information')."
".           fieldrow('Username'        ,$loguser[name]                 )."
".           fieldrow('Password'        ,$passinput                     )."
".
           catheader('Appearance')."
".           fieldrow('Rankset'		,fieldselect('rankset', $loguser['rankset'], ranklist()))."
".           (checkctitle()?fieldrow('Title'           ,fieldinput(40,255,'title'     )):"")."
".           fieldrow('Picture'         ,'<input type=file name=picture size=40> <input type=checkbox name=picturedel value=1 id=picturedel><label for=picturedel>Erase</label><br><font class=sfont>Must be PNG, JPG or GIF, within 60KB, within 100x100.</font>')."
".           fieldrow('MAXIpic'         ,'<input type=file name=minipic size=40> <input type=checkbox name=minipicdel value=1 id=minipicdel><label for=minipicdel>Erase</label><br><font class=sfont>Must be PNG or GIF, within 10KB, exactly '.$rs.'x'.$rs.'.</font>')."
".
           catheader('Personal information')."
".           fieldrow('Sex'             ,fieldoption('sex',$loguser[sex],$listsex))."
".           fieldrow('Real name'       ,fieldinput(40, 60,'realname'  ))."
".           fieldrow('Location'        ,fieldinput(40, 60,'location'  ))."
".           fieldrow('Birthday'        ,$birthinput                    )."
".           fieldrow('Bio'             ,fieldtext ( 5, 80,'bio'       ))."
".
           catheader('Post layout')."
".           fieldrow('Header'          ,fieldtext ( 5, 80,'head'      ))."
".           fieldrow('Signature'       ,fieldtext ( 5, 80,'sign'      ))."
".           fieldrow('Signature line'  ,fieldoption('signsep',$loguser[signsep],array('Display','Hide')))."
".
           catheader('Contact information')."
".           fieldrow('Email address'   ,fieldinput(40, 60,'email'     ))."
".           fieldrow('Homepage URL'    ,fieldinput(40,200,'homeurl'   ))."
".           fieldrow('Homepage name'   ,fieldinput(40, 60,'homename'  ))."
".
           catheader('Options')."
".           fieldrow('Theme'           ,fieldselect('theme', $loguser['theme'], themelist()))."
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
        if($width!=$rs || $height!=$rs) {
          $error.="<br>- Minipic size must be {$rs}x$rs.";
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
      $dimx=100;
      $dimy=100;
      $dimxs=60;
      $dimys=60;
      $size=2*30720;

      if($x_hacks["180px"]) {
        $dimx=180;
	$dimy=180;
	$size=2*61440;
      }

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
        $file="userpic/$loguser[id]";
        $file2="userpic/s$loguser[id]";

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
          copy($tmpfile,"userpic/$loguser[id]");
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
    if($_POST['picturedel']) $usepic=0;

    //check for table breach
    if(tvalidate($_POST['head'].$_POST['sign'])!=0)
    {
      $error.="<br>- Table tag count mismatch in post layout; wiped.";
      print $error;
      $_POST['head']=$_POST['sign']="";
    }
    if(tvalidate($_POST['title'])!=0)
    {
      $error.="<br>- Table tag count mismatch in custom title.";
      print $error;
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
               ."WHERE id=$loguser[id]"
               );

    print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Profile changes saved!<br>
".        "    ".redirect("profile.php?id=$loguser[id]",'your profile')."
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
    global $L,$loguser;
    return "$L[INPt]=$field size=$size maxlength=$max value=\"".str_replace("\"", "&quot;", $loguser[$field])."\">";
//  return "$L[INPt]=$field size=$size maxlength=$max value=\"".htmlval($loguser[$field])."\">";
  }

  function fieldtext($rows,$cols,$field){
    global $L,$loguser;
    return "$L[TXTa]=$field rows=$rows cols=$cols>".htmlval($loguser[$field]).'</textarea>';
//  return "$L[TXTa]=$field rows=$rows cols=$cols>".htmlval($loguser[$field]).'</textarea>';
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
		global $sql, $loguser;
		$s	= $sql -> query("SELECT `id`, `name` FROM `themes` WHERE `ord` >= '0' OR `id` = '$loguser[theme]' ORDER BY `ord`");		// it'd be grand if I knew how to get the usercounts easily too .'D

		$t	= $sql -> query("SELECT `theme`, COUNT(*) AS 'count' FROM `users` GROUP BY `theme`");
		while ($x = $sql -> fetch($t)) $themeuser[$x['theme']] = intval($x['count']);

//		print_r($themeuser);

		while ($x = $sql -> fetch($s)) $themelist[$x['id']] = $x['name'] ." (". $themeuser[$x['id']] .")";
		return $themelist;
  }

  function ranklist() {
    global $sql, $loguser;
    $r=$sql->query("SELECT * FROM ranksets ORDER BY id ASC");
    while($d=$sql->fetch($r)) $rlist[$d[id]]=$d[name];

    return $rlist;
  }
?>
