<?php
  require 'lib/common.php';
  needs_login(1);
  
//Permissions
  if(isset($_GET['user'])){
    $edid=$_GET['user'];
    $lnkex="?user=$edid";
  } else {
    $edid=$loguser['id'];
  }
  $edid = (int)$edid;
  if(!can_edit_user_moods($edid)){
    error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
  }
  
//Editing functionality
  if(isset($_POST['id']) && $_POST['a']=="Save"){
    if(is_numeric($_POST['id'])){
      $fname=$_FILES['picture'];
      if($fname['size']>0){
        if($_POST['id']!=-1){
          $ava_out=img_upload($fname,"userpic/".$edid."_".$_POST['id'],$avatardimx,$avatardimy,$avatarsize);
        } else {//Default Avatar
          $sql->query("UPDATE `users` SET `usepic`=`usepic`+1 WHERE `id`=".$edid);
          $ava_out=img_upload($fname,"userpic/".$edid,$avatardimx,$avatardimy,$avatarsize);
        }
        if($ava_out=="OK!"){
          if($_POST['id']!=-1){ $sql->query("REPLACE INTO `mood` VALUES (".$_POST['id'].",".$edid.",'".addslashes($_POST['label'])."',1,'')"); }
        } else { $err.=$ava_out; }
      } else { //No file uploaded
        if(strlen($_POST['url'])>0) {
          $img_data=getimagesize($_POST['url']); print_r($img_data);
          $ftypes=array("png","jpeg","jpg","gif");
          if($img_data[0]>$avatardimx){ $err="Image linked is too wide.<br>"; }
          if($img_data[1]>$avatardimy){ $err.="Image linked is too tall.<br>"; }
          if(!in_array(str_replace("image/","",$img_data['mime']),$ftypes)){ $err.="Image linked is not a gif, jpg or png file.<br>";}
          if(!$err){ $sql->query("REPLACE INTO `mood` VALUES (".$_POST['id'].",".$edid.",'".addslashes($_POST['label'])."',0,'".addslashes($_POST['url'])."')"); }
        } else {//No url specified.
          $sql->query("UPDATE `mood` SET `label`='".addslashes($_POST['label'])."' WHERE `id`=".$_POST['id']." AND `user`=".$edid);
        }
      }
    } else { $err="Bad id"; }
  if(!$err){ $err="Mood avatar updated."; }
  }
  if(isset($_POST['id']) && $_POST['a']=="Delete"){
    if(is_numeric($_POST['id'])){
      if($_POST['id']==-1){
        $sql->query("UPDATE `users` SET `usepic`=0 WHERE `id`=".$edid);
        echo "Default avatar set to blank.";
      } else {
        //Delete mood avatar
        $sql->query("DELETE FROM `mood` WHERE `id`=".$_POST['id']." AND `user`=".$edid);
        echo "Deleted.";
      }
    } else {
      echo "Bad id.";
    }
    die(); //Don't render page.
  }
  pageheader('Mood Avatar Editor 0.a');
//Various magic
  if(isset($err)){
    noticemsg("Notice", $err);
  }
  print "<script language=\"javascript\">
	function edit(av_id, av_lab, av_url)
	{
		document.getElementById(\"editpane\").style['display'] = \"inline\";
		document.getElementById(\"id\").value = av_id;
		document.getElementById(\"label\").value = av_lab;
		document.getElementById(\"url\").value = av_url;
		if(av_id==-1){
			document.getElementById(\"em\").style['display'] = \"none\";
			document.getElementById(\"em2\").style['display'] = \"none\";
		} else {
			document.getElementById(\"em\").style['display'] = \"\";
			document.getElementById(\"em2\").style['display'] = \"\";
		} 
	}
	function del(av_id, av_lab)
	{
		if(confirm(\"Are you sure you wish to delete \"+av_lab+\"?\")){
		y = new XMLHttpRequest();
		y.onreadystatechange = function()
		{
			if(y.readyState == 4)
			{
				if(y.responseText != \"OK\")
					alert(y.responseText);
				if(y.responseText == \"Deleted.\")
					document.getElementById(\"mood\"+av_id).style['display'] = \"none\";
				if(y.responseText == \"Default avatar set to blank.\")
					document.getElementById(\"defava\").style['background'] = \"none\";
			}
		};
		y.open('POST','mood.php$lnkex',true);
		y.setRequestHeader('Content-type','application/x-www-form-urlencoded');
		y.send('a=Delete&id='+av_id);
		}
	}
</script>";
//Default Avatar.
  $u=$sql->fetch($sql->query("SELECT `usepic` FROM `users` WHERE `id`=".$edid));
  if($u['usepic']>=1){ $aurl="gfx/userpic.php?id=".$edid."&r=".$u['usepic']; }
  print "<div style=\"margin: 4px; float: left; display:inline-block;\"><table cellspacing=\"0\" class=\"c1\">
  <tr class=\"h\">
    <td class=\"b h\">Default</td>
  </tr>
  <tr>
    <td class=\"b n2\"><div style=\"padding: 0px; margin: 0px; width: 180px; height: 180px; background: url($aurl) no-repeat center;\" id=\"defava\"></div>
  </tr><tr>
    <td class=\"b n1\"><a href=\"#\" onclick=\"edit(-1,'','')\">Edit</a> | <a href=\"#\" onclick=\"del(-1,'Default')\">Delete</a></td>
  </tr>
</table></div>";

//Mood Avatars
  $fid=0;
  $lid=0;
  $avas = $sql->query("SELECT * FROM `mood` WHERE `user`=".$edid);
  for($i=1;$mav=$sql->fetch($avas);$i++){
  if($lid!=($mav['id']-1) && $fid==0){ $fid=($mav['id']-1); } //Find a "free" ID.
  $lid=$mav['id'];
  if($mav['local']==1){
    $aurl="gfx/userpic.php?id=".$edid."_".$mav['id'];
  } else {
    $aurl=stripslashes($mav['url']);
  }
  print "<div style=\"margin: 4px; float: left; display:inline-block;\" id=\"mood".$mav['id']."\"><table cellspacing=\"0\" class=\"c1\">
  <tr class=\"h\">
    <td class=\"b h\">".stripslashes($mav['label'])."</td>
  </tr>
  <tr>
    <td class=\"b n2\"><div style=\"padding: 0px; margin: 0px; width: 180px; height: 180px; background: url(".$aurl.") no-repeat center;\"></div>
  </tr><tr>
    <td class=\"b n1\"><a href=\"#\" onclick=\"edit(".$mav['id'].",'".htmlspecialchars($mav['label'])."', '".$mav['url']."')\">Edit</a> | <a href=\"#\" onclick=\"del(".$mav['id'].",'".htmlspecialchars($mav['label'])."')\">Delete</a></td>
  </tr>
</table></div>";
}
if($fid==0){ $fid=$lid+1; } //If no free ID.
if($fid<=64){
  print "<div style=\"margin: 4px; float: left; display:inline-block;\" id=\"mood64\"><table cellspacing=\"0\" class=\"c1\">
  <tr class=\"h\">
    <td class=\"b h\" style=\"width:180px;\">&nbsp</td>
  </tr>
  </tr><tr>
    <td class=\"b n1\"><a href=\"#\" onclick=\"edit(".$fid.",'(Label)', '')\">Add New</a></td>
  </tr>
</table></div>";
}
  print "<br clear=\"all\"><div id=\"editpane\" style=\"display:none;\"><form id=\"f\" action=\"mood.php$lnkex\" enctype=\"multipart/form-data\" method=\"post\"><table cellspacing=\"0\" class=\"c1\">
  <tr class=\"h\">
    <td class=\"b h\" colspan=2>Editing mood avatar</td>
  </tr>
  <tr id=\"em\">
    <td class=\"b n1\">Label</td>
    <td class=\"b n2\"><input type=\"text\" name=\"label\" id=\"label\" size=50 maxlength=100></td>
  </tr><tr>
    <td class=\"b n1\">Upload File</td>
    <td class=\"b n2\"><input type=\"file\" name=\"picture\" size=50></td>
  </tr><tr id=\"em2\">
    <td class=\"b n1\">Web link</td>
    <td class=\"b n2\"><input type=\"text\" name=\"url\" id=\"url\" size=50 maxlength=250></td>
  </tr><tr>
    <td class=\"b n1\"><input type=\"hidden\" name=\"id\" id=\"id\"></td>
    <td class=\"b n2\"><input type=\"submit\" name='a' value=\"Save\"></td>
  </tr></table></form></div>";
  pagefooter();
?>