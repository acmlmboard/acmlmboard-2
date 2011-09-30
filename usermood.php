<?php
  // 2009-07 Sukasa: The entire file.  I tried to conform to what I remembered of the standards.
  // 2011-09-09 Emuz: Small update to allow for <s>32</s> 64 avatars
  // 2011-09-29 Sukasa: Fine, I'll timestamp it that way then.  Updated to allow administrators to modify anyone's mood avatars
  require 'lib/common.php';


  $targetuserid = $loguser[id];
  $target = false;
  $targetget = "";
  $targetgeta = "";

  if (CanAlterAll() && isset($_GET[uid]) && $_GET[uid] != $loguser[id]) {
    $targetuserid = addslashes($_GET[uid]);
    $target = true;
    $targetget = "&uid=".$targetuserid;
    $targetgeta = "?uid=".$targetuserid;
  }

  //Select existing avatar or new one
  $id = (isset($_GET[i]) ? $_GET[i] : (isset($_POST[aid]) ? $_POST[aid] : -1 ));

  $activeavatar = $sql->fetchq("select `id`,`label`,`url`,`local`,1 `existing` from `mood` where `user` = $targetuserid and `id`=".addslashes($id)." union select 0 `id`, '(Label)' `label`, '' `url`, 1 `local`, 0 `existing`");
  $avatars = $sql->query("select * from `mood` where `user`= ".$targetuserid." ");
  $numavatars = $sql->resultq("select count(*) from `mood` where `user` = ".$targetuserid);

  if (isset($_POST[a]) && $_POST[a][0]=='D' && $activeavatar[existing]) {
    $sql->query("delete from mood where id= ".addslashes($id)." and user = ".$targetuserid);
    $avatars = $sql->query("select * from `mood` where `user` = ".$targetuserid);
  }

  if (isset($_POST[a]) && $_POST[a][0]=='S' && ($numavatars < 64 || $activeavatar[existing])) {
    //vet the image
    $islocal=($_POST[islocal]!='on'?1:0);
    $avatarid = ($activeavatar[existing] == 1 ? addslashes($id) : $sql->resultq("select (id + 1) nid from `mood` where user = ".$targetuserid." union select 1 nid order by nid  desc"));
    if($islocal&&$fname=$_FILES[picture][name]){
      $fext=strtolower(substr($fname,-4));
      $error='';
      $exts=array('.png','.jpg','.gif');
      $dimx=100; //My test used much larger sizes.  IIRC, these were the defaults on board2
      $dimy=100;
      $dimxs=60;
      $dimys=60;
      $size=30720;

	//[KAWA] TODO: replace with token effect
	/*
      if($x_hacks["180px"]) {
        $dimx=180;
	$dimy=180;
	$size=61440;
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

      if(($fsize=$_FILES[picture][size])>$size)
        $error.="<br>- File size is too high, limit is $size bytes";

      if(!$error){
        $tmpfile=$_FILES[picture][tmp_name];
        $file="userpic/".$targetuserid."_$avatarid";
        $file2="userpic/s".$targetuserid."_$avatarid";

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
          copy($tmpfile,"userpic/".$targetuserid."_$avatarid");
        elseif($type<=3){
          if($r>1){
            $img2=imagecreatetruecolor($dimx,$dimy/$r);
            imagecopyresampled($img2,$img1,0,0,0,0,$dimx,$dimy/$r,imagesx($img1),imagesy($img1));
          }else{
            $img2=imagecreatetruecolor($dimx*$r,$dimy);
            imagecopyresampled($img2,$img1,0,0,0,0,$dimx*$r,$dimy,imagesx($img1),imagesy($img1));
          }
          imagepng($img2,$file);
          $usepic=1;
        }else{
          print "<br>- Bad image format";
        }
        //Save the mood avatar
        $sql->query("INSERT INTO mood (id,user,url,local,label) VALUES ($avatarid, ".$targetuserid." ,'$_POST[url]', $islocal,'$_POST[label]') ON DUPLICATE KEY UPDATE url='$_POST[url]', local=$islocal, label='$_POST[label]'");
        //and reload it
        $id=-1;
        
      }else
        print $error;
    }
 }  

  $activeavatar = $sql->fetchq("select `id`,`label`,`url`,`local`, 1 `existing` from `mood` where `user`= ".$targetuserid." and `id`=".addslashes($id)." union select 0 `id`, '(Label)' `label`, '' `url`, 1 `local`, 0 `existing`");
  $numavatars = $sql->resultq("select count(*) from `mood` where `user`= ".$targetuserid." ");
  $avatars = $sql->query("select * from `mood` where `user`=".$targetuserid);
  if ($target) {
    $targetname = $sql->resultq("select `name` from `users` where `id`='$targetuserid'");
  }

  // Moved pageheader here so that I can do header()s without fucking everything up again
  pageheader();


  print "<form id=\"f\" action=\"usermood.php$targetgeta\" enctype=\"multipart/form-data\" method=\"post\">
".      "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] width=250>
".      "      ". ($target ? $targetname."'s" : "Your") ." current mood avatars ($numavatars)
".      "    </td>
".      "  $L[TDhc] colspan='2'><nobr>
".      "    Add/change a mood avatar
".      "  </td>
".      "  $L[TR]>
".      "    $L[TD1] style=\"vertical-align: top\" rowspan='3'>";

  while ($row=$sql->fetch($avatars))
    print "<a href=\"?a=e&i=$row[id]$targetget\">$row[label]</a><br>";
  
  if ($numavatars < 64)
    print "          <a href=\"usermood.php$targetgeta\">(Add New)</a>";

  print "        </td>
".      "        $L[TD2]><nobr>
".      "          <input type=\"text\" style=\"width: 100%\" name=\"label\" value=\"$activeavatar[label]\">
".      "        $L[TD2]>
".      "          <input type=\"submit\" name='a' value=\"Save\">
".($id>0?"           
":"").  "     $L[TR]>
".      "       $L[TD3]>
".      "          <input type=\"text\" style=\"width: 100%\" name=\"url\" value=\"$activeavatar[url]\">
".      "       $L[TD3] width='1'><nobr><input id=\"islocal\" name=\"islocal\" type=\"checkbox\"".(!$activeavatar[local]?'checked="checked"':'')."
".      "          <label for=\"islocal\">Use URL instead of uploaded file</label>
".      "     $L[TR]>
".      "        $L[TD2]>
".      "          <input type=\"file\" name=\"picture\"><input type=\"hidden\" name=\"aid\" id=\"aid\" value=\"$activeavatar[id]\">
".($id>0?"           <input type=\"submit\" name='a' value=\"Delete\">
":"").  "<input type=\"hidden\" name=\"aid\" id=\"aid\" value=\"$activeavatar[id]\"></td>
".      "        $L[TD2]><small>Limits: 100x100px, 30KB</small></td>
".      "$L[TBLend]</form>
".      "<br>";

  pagefooter();

  
  function CanAlterAll() {
    global $loguser;
    return ($loguser[power] >= 3);
  }

  ?>
