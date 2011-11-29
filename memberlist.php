<?php
  require 'lib/common.php';
  pageheader('Memberlist');

  if($ppp<1) $ppp=50;
  if($page<1) $page=1;

  $order='posts DESC';
  if($sort=='exp' ) $order='exp DESC';
  if($sort=='name') $order='name';
  if($sort=='reg' ) $order='regdate DESC';

  $where='1';
  if($sex=='m') $where='sex=0';
  if($sex=='f') $where='sex=1';
  if($sex=='n') $where='sex=2';

  if($pow!='' && is_numeric($pow))
    if($pow<5) $where.=" AND power=$pow";
    else       $where.=" AND power>0";

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
        $pagelist.=' '.mlink($sort,$sex,$pow,$ppp,$p)."$p</a>";
  }

  print "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] colspan=2>$num user".($num>1?'s':'')." found.</td>
".      "  $L[TR]>
".      "    $L[TD1] width=60>Sort by:</td>
".      "    $L[TD2c]>
".      "      ".mlink(''    ,$sex,$pow,$ppp)."Posts</a> |
".      "      ".mlink('exp' ,$sex,$pow,$ppp)."EXP</a> |
".      "      ".mlink('name',$sex,$pow,$ppp)."Username</a> |
".      "      ".mlink('reg' ,$sex,$pow,$ppp)."Registration date</a>
".      "  $L[TR]>
".      "    $L[TD1]>Sex:</td>
".      "    $L[TD2c]>
".      "      ".mlink($sort,'m',$pow,$ppp)."Male</a> |
".      "      ".mlink($sort,'f',$pow,$ppp)."Female</a> |
".      "      ".mlink($sort,'n',$pow,$ppp)."N/A</a> |
".      "      ".mlink($sort,'' ,$pow,$ppp)."All</a>
".      "  $L[TR]>
".      "    $L[TD1]>Power:</td>
".      "    $L[TD2c]>
".      "      ".mlink($sort,$sex,'-1',$ppp)."Banned</a> |
".      "      ".mlink($sort,$sex, '0',$ppp)."Normal</a> |
".      "      ".mlink($sort,$sex, '1',$ppp)."Local moderator</a> |
".      "      ".mlink($sort,$sex, '2',$ppp)."Full moderator</a> |
".      "      ".mlink($sort,$sex, '3',$ppp)."Administrator</a> |
".      "      ".mlink($sort,$sex, '4',$ppp)."Root</a> |
".      "      ".mlink($sort,$sex, '9',$ppp)."All staff</a> |
".      "      ".mlink($sort,$sex,  '',$ppp)."All</a>
".      "      
".      "$L[TBLend]
".      "<br>";

//[KAWA] Rebuilt this to use my new renderer. Not sure what to do about the part above though X3
$headers = array
(
	"id" => array("caption"=>"#", "width"=>"32px", "align"=>"center"),
	"pic" => array("caption"=>"Picture", "width"=>"64px"),
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
    $picture=($user[usepic]?"<img src=gfx/userpic.php?id=$user[id]&s=1 width=60 height=60>"
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

  function mlink($sort,$sex,$pow,$ppp,$page=1){
    return '<a href=memberlist.php?'
           .($sort   ?"sort=$sort":'')
           .($sex    ?"&sex=$sex":'')
           .($pow!=''?"&pow=$pow":'')
           .($ppp!=50?"&ppp=$ppp":'')
           .($page!=1?"&page=$page":'')
           .'>';
  }
?>
