<?php
  require 'lib/common.php';
  pageheader('Memberlist');

  $sort = $_REQUEST['sort'];
  $sex = $_REQUEST['sex'];
  $pow = $_REQUEST['pow'];
  $ppp = $_REQUEST['ppp'];
  $page = $_REQUEST['page'];
  $mini = $_REQUEST['mini'];
  $orderby = $_REQUEST['orderby'];



  if($ppp<1) $ppp=50;
  if($page<1) $page=1;

  if($orderby=='a') $sortby= " ASC";
  else $sortby= " DESC";

  $order='posts'.$sortby;
  if($sort=='exp' ) $order='exp'.$sortby;
  if($sort=='name') $order='name'.$sortby;
  if($sort=='reg' ) $order='regdate'.$sortby;

  $where='1';
  if($sex=='m') $where='sex=0';
  if($sex=='f') $where='sex=1';
  if($sex=='n') $where='sex=2';

  if($pow!='' && is_numeric($pow)){
    if ($pow=='-1') $where.=" AND `group_id` =  ANY (SELECT `x_id` FROM `x_perm` WHERE `x_id`= ANY (SELECT `id` FROM `group` WHERE `perm_id` = \"show-as-staff\") AND`x_type` =\"group\")";
    else $where.=" AND group_id=$pow";
  }

  $users=$sql->query("SELECT *,".sqlexp()." FROM users "
                    ."WHERE $where "
                    ."ORDER BY $order "
                    ."LIMIT ".($page-1)*$ppp.",$ppp");
  $num=$sql->resultq("SELECT COUNT(*) FROM users "
                    ."WHERE $where");

  if($num<=$ppp)
    $pagelist='';
  else{
    $pagelist='Pages:';
    for($p=1;$p<=1+floor(($num-1)/$ppp);$p++)
      if($p==$page)
        $pagelist.=" $p";
      else
        $pagelist.=' '.mlink($sort,$sex,$pow,$ppp,$p,$mini,$orderby)."$p</a>";
  }

  $activegroups = $sql->query("SELECT * FROM `group` WHERE id IN (SELECT `group_id` FROM users GROUP BY `group_id`) ORDER BY `sortorder` ASC ");

  $groups = array();
  $gc = 0;
  while ($group = $sql->fetch($activegroups)) {
    $groups[$gc++] = mlink($sort,$sex,$group['id'],$ppp,$page,$mini,$orderby).$group['title']."</a>";
  }

  print "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] colspan=2>$num user".($num>1?'s':'')." found.</td>
".      "  $L[TR]>
".      "    $L[TD1] width=60>Sort by:</td>
".      "    $L[TD2c]>
".      "      ".mlink(''    ,$sex,$pow,$ppp,$page,$mini,$orderby)."Posts</a> |
".      "      ".mlink('exp' ,$sex,$pow,$ppp,$page,$mini,$orderby)."EXP</a> |
".      "      ".mlink('name',$sex,$pow,$ppp,$page,$mini,$orderby)."Username</a> |
".      "      ".mlink('reg' ,$sex,$pow,$ppp,$page,$mini,$orderby)."Registration date</a>
".      "  $L[TR]>
".      "    $L[TD1] width=60>Order by:</td>
".      "    $L[TD2c]>
".      "      ".mlink($sort,$sex,$pow,$ppp,$page,$mini,'d')."Descending</a> |
".      "      ".mlink($sort,$sex,$pow,$ppp,$page,$mini,'a')."Ascending</a>
".      "  $L[TR]>
".      "    $L[TD1]>Sex:</td>
".      "    $L[TD2c]>
".      "      ".mlink($sort,'m',$pow,$ppp,$page,$mini,$orderby)."Male</a> |
".      "      ".mlink($sort,'f',$pow,$ppp,$page,$mini,$orderby)."Female</a> |
".      "      ".mlink($sort,'n',$pow,$ppp,$page,$mini,$orderby)."N/A</a> |
".      "      ".mlink($sort,'' ,$pow,$ppp,$page,$mini,$orderby)."All</a>
".      "  $L[TR]>
".      "    $L[TD1]>Image:</td>
".      "    $L[TD2c]>
".      "      ".mlink($sort,$sex,$pow,$ppp,$page,'0',$orderby)."Avatars</a> |
".      "      ".mlink($sort,$sex,$pow,$ppp,$page,'1',$orderby)."Minipics</a>
".      "  $L[TR]>
".      "    $L[TD1]>Group:</td>
".      "    $L[TD2c]>";
$c = 0;
foreach ($groups as $k => $v) {  
  echo $v;
  $c++;
  //if ($c < $gc) 
  echo " | ";
}
echo      "      ".mlink($sort,$sex,  '-1',$ppp,$page,$mini,$orderby)."All Staff</a>
"." |       ".mlink($sort,$sex,  '',$ppp,$page,$mini,$orderby)."All</a>
".      "      
".      "$L[TBLend]
".      "<br>";

//Need to replace a few things if $mini=1 -Emuz
if($mini==1){
  $piccap = "Minipic";
  $picwid = "16px";
}
else {
  $piccap = "Picture";
  $picwid = "64px";
}

//[KAWA] Rebuilt this to use my new renderer. Not sure what to do about the part above though X3
$headers = array
(
	"id" => array("caption"=>"#", "width"=>"32px", "align"=>"center"),
	"pic" => array("caption"=>$piccap, "width"=>$picwid),
	"name" => array("caption"=>"Name"),
	"reg" => array("caption"=>"Registered on", "width"=>"130px"),
	"posts" => array("caption"=>"Posts", "width"=>"50px"),
	"lvl" => array("caption"=>"Level", "width"=>"40px"),
	"exp" => array("caption"=>"EXP", "width"=>"80px"),
);
$data = array();
for($i=($page-1)*$ppp+1; $user=$sql->fetch($users); $i++)
{
    $user[exp]=floor($user[exp]);
    $user[level]=calclvl($user[exp]);
    if($mini==1) $picture=($user[minipic]?"<center><img style='vertical-align:text-bottom' src='".$user[$u.'minipic']."' border=0 ></center> "
                           :'<img src=img/_.png width=16 height=16>');
    else $picture=($user[usepic]?"<img src=gfx/userpic.php?id=$user[id] width=60 height=60>"
                           :'<img src=img/_.png width=60 height=60>');

	$data[] = array
	(
		"id" => $user['id'].'.',
		"pic" => $picture,
		"name" => userlink($user),
		"reg" => cdate($dateformat,$user[regdate]),
		"posts" => $user[posts],
		"lvl" => $user[level],
		"exp" => $user[exp],
	);
}

RenderTable($data, $headers);

  print "<br>
".      "$pagelist
".      "<br>
";
  pagefooter();

  function mlink($sort,$sex,$pow,$ppp,$page=1,$mini,$orderby){
    return '<a href=memberlist.php?'
           .($sort   ?"sort=$sort":'')
           .($sex    ?"&sex=$sex":'')
           .($pow!=''?"&pow=$pow":'')
           .($ppp!=50?"&ppp=$ppp":'')
           .($page!=1?"&page=$page":'')
           .($mini!=0?"&mini=$mini":'')
           .($orderby!=''?"&orderby=$orderby":'')
           .'>';
  }
?>
