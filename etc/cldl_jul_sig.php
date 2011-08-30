<?php
  /* This file is not a standard part of the board */
  /* If it not being used, it can be removed along with its font (yi_font2.png) */
  /* WARNING: may contain brain rotting ugly code */
  
  require '../lib/function.php';
  //Header('Content-type:text/plain');
  //error_reporting(E_ALL);
                     /* 0 1 2 3 4 5 6 7 8 9 a b c d e f       */
  $glyphwidths = array (8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,        /* 0 */
                        8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,        /* 1 */
                        5,8,8,8,8,8,8,6,6,6,7,7,8,8,8,6,        /* 2 */
                        8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,        /* 3 */
                        8,8,8,8,8,7,7,8,8,5,8,8,7,8,8,8,        /* 4 */
                        8,8,8,8,8,8,8,8,8,8,8,5,6,5,6,7,        /* 5 */
                        8,8,8,8,8,7,7,8,8,4,7,8,4,8,8,8,        /* 6 */
                        8,8,7,7,7,8,8,8,8,8,8,8,8,8,8,8,        /* 7 */
                        8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,        /* 8 extra */
                        15,7,15,7,15,7,15,7,15,7,15,7,15,7,15,7,/* 9 status */
                        8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,        /* a extra */
                        8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,        /* b extra */
                        8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,        /* c extra */
                        8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,        /* d extra */
                        8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,        /* e extra */
                        8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8);       /* f extra */
                        
                                      /* M */                /* F */                /* U */
  $ncolors = array( -1 => array(array(0x88,0x88,0x88), array(0x88,0x88,0x88), array(0x88,0x88,0x88)),
                          array(array(0x97,0xAC,0xEF), array(0xF1,0x85,0xC9), array(0x7C,0x60,0xB0)),
                          array(array(0xD8,0xE8,0xFE), array(0xFF,0xB3,0xF3), array(0xEE,0xB9,0xBA)),
                          array(array(0xAF,0xFA,0xBE), array(0xC7,0x62,0xF2), array(0x47,0xB5,0x3C)),
                          array(array(0xFF,0xEA,0x95), array(0xC5,0x3A,0x9E), array(0xF0,0xC4,0x13)));
  $ncfontcache = array();
  
  $colgap = 4;
  
  if (isset($_REQUEST['maxthreads']) && is_numeric($_REQUEST['maxthreads'])) {
    $maxthreads = $_REQUEST['maxthreads'];
  } else {
    $maxthreads = 16;
  }
  
  cookielogon();
  
  $fieldlist='';
  $ufields=array('name','sex','power');
  foreach($ufields as $field)
    $fieldlist.="u1.$field u1$field, u2.$field u2$field, ";
  
  $threads = resultall( "SELECT $fieldlist t.*, f.id fid, f.title ftitle".($log?', NOT ISNULL(r.time) isread':'').' '
                        ."FROM threads t "
                        ."LEFT JOIN users u1 ON u1.id=t.user "
                        ."LEFT JOIN users u2 ON u2.id=t.lastuser "
                        ."LEFT JOIN forums f ON f.id=t.forum "
                        ."LEFT JOIN categories c ON f.cat=c.id "
                  .($log?"LEFT JOIN threadsread r ON (r.tid=t.id AND r.uid=$loguser[id]) ":'')
                        ."WHERE f.minpower<=$loguser[power] "
                        .  "AND c.minpower<=$loguser[power] "
                        ."ORDER BY t.lastdate DESC "
                        ."LIMIT $maxthreads");

  
  
  foreach ($threads as $k => $v) { /* shorten some forum names */
    switch ($v['fid']) {
    case 33: $threads[$k]['ftitle'] = 'Hack releases'; break;
    case 6:  $threads[$k]['ftitle'] = 'Help/Sug'; break;
    }
  }
  
  $dispcols = array(array('dbcol'=>'status'         , 'dispcol'=>''),
                    array('dbcol'=>'ftitle'         , 'dispcol'=>'Forum'),
                    array('dbcol'=>'title'          , 'dispcol'=>'Title'),
                    array('dbcol'=>'u1name'         , 'dispcol'=>'Started by'),
                    array('dbcol'=>'replies'        , 'dispcol'=>'Replies'),
                    array('dbcol'=>'lastusernameago', 'dispcol'=>'Last post'),
                    /*array('dbcol'=>'isread'         , 'dispcol'=>'IR')*/);
  
  foreach ($dispcols as $k => $v) {
    if ($v['dispcol']) {
      $dispcols[$k]['width'] = twidth($v['dispcol']) + 16;
    } else {
      $dispcols[$k]['width'] = 0;
    }
  }
  
  $nr_threads = 0;
  foreach ($threads as $tk => $tv) {
    $nr_threads++;
    /* derive cell value */
    $threads[$tk]['lastpostago'] = timeunits_short(time() - $tv['lastdate']);
    $threads[$tk]['status'] = chr((($threads[$tk]['isread']?0:4)|($threads[$tk]['closed']?8:0)|(($threads[$tk]['replies']>50)?2:0))+0x90)/*.$threads[$tk]['ftitle']*/;
    
    foreach ($dispcols as $ck => $cv) {
      /* find cell width */
      if ($cv['dbcol'] == 'lastusernameago') {
        $tmpw = twidth($threads[$tk]['u2name']) + twidth($threads[$tk]['lastpostago']) + 2;
      } else {
        $tmpw = twidth($threads[$tk][$cv['dbcol']]);
      }
      /* update col width record */
      if ($tmpw > $cv['width']) {
        $dispcols[$ck]['width'] = $tmpw;
      }
    }
  }
  
  $pmtexts = array();
  if ($log) {
    $unreadpms = $sql->resultq("SELECT COUNT(*) FROM pmsgs WHERE userto=$loguser[id] AND unread=1");
    if ($unreadpms > 0) {
      $pmsgs = $sql->fetchq("SELECT p.id id, p.date date, u.id uid, u.name uname, u.sex usex, u.power upower "
                           ."FROM pmsgs p "
                           ."LEFT JOIN users u ON u.id=p.userfrom "
                           ."WHERE p.userto=$loguser[id] "
                           ."ORDER BY date DESC LIMIT 1");
      $totalpms = $sql->resultq("SELECT COUNT(*) FROM pmsgs WHERE userto=$loguser[id]");
      $pmtexts = array(array(array('font'=>'white', 'text' => chr(0x94)."You have $totalpms private messages ($unreadpms new)")/*)*/,
                       /*array(*/array('font'=>'white', 'text' => "  Last from: "),
                             array('font'=>'name', 'power'=>$pmsgs['upower'], 'sex'=>$pmsgs['usex'], 'text'=>$pmsgs['uname']),
                             array('font'=>'white', 'text' => ' '.timeunits(time() - $pmsgs['date']).' ago')));
                             
      //print "<pre>";
      //print_r($pmtexts);
      //print "</pre>";
    }
  }
  
  
  /* calculate image size */
  //$total_width = 16 - $colgap;
  $total_col_width = 0;
  foreach ($dispcols as $k => $v) {
    $total_col_width += $v['width'];
    //$total_width += $v['width'] + $colgap;
  }
  $total_width = $total_col_width + 16 + $colgap * (count($dispcols)-1);
  $total_height = 36 + $nr_threads*12;
  
  if ($pmtexts) {
    $pmsize = format_lines_size($pmtexts);
    $pmsize['width'] += 16;
    $pmsize['height'] += 16;
    if ($pmsize['width'] > $total_width) {
      $ratio = ($pmsize['width'] - 16 + $colgap * (count($dispcols)-1)) / $total_width;
      $total_width = $pmsize['width'];
      foreach ($dispcols as $k => $v) {
        $dispcols[$k]['width'] = ceil($dispcols[$k]['width']*$ratio);
      }
    }
    $total_height += $pmsize['height'] +4;
  } else {
    $pmsize['width'] = 0;
    $pmsize['height'] = 0;
  }    
  
  
  
  /* create image */
  $img = ImageCreate($total_width, $total_height);
  $color_index['bg']     =ImageColorAllocate($img, 40, 40, 90);
  $color_index['bxb0']   =ImageColorAllocate($img,  0,  0,  0);
  $color_index['bxb1']   =ImageColorAllocate($img,200,170,140);
  $color_index['bxb2']   =ImageColorAllocate($img,155,130,105);
  $color_index['bxb3']   =ImageColorAllocate($img,110, 90, 70);
  for($i=0;$i<100;$i++)
    $color_index[$i]   =ImageColorAllocate($img, 10, 16, 60+$i/2);
  ImageColorTransparent($img,0);
  
  $whitefont = fontc(255,255,255, 210,210,210,  0, 0, 0);
  
  $xorg = 0;
  $yorg = 0;
  
  if ($pmtexts) {
    box($img, $color_index, $xorg, $yorg, $total_width, $pmsize['height']);
    format_lines_write($pmtexts, $xorg+8, $yorg+8);
    $yorg += $pmsize['height']+4;
  }
  
  /* bounding box */
  box($img, $color_index, $xorg, $yorg + 12, $total_width, 24 + $nr_threads*12);
  
  $x = $xorg + 8;
  foreach ($dispcols as $k => $v) {
    /* draw col header */
    if ($v['dispcol']) {
      box($img, $color_index, $x, $yorg, $v['width'], 28);
      twrite_center($img, $whitefont, $x+8, $yorg + 8, $v['width']-16, $v['dispcol']);
    }
    /* draw col values */
    $y = $yorg + 28;
    foreach ($threads as $tk => $tv) {
      switch ($v['dbcol']) {
      case 'ftitle':
      case 'title':
      default:
        twrite_left($img, $whitefont, $x, $y, $v['width'], $tv[$v['dbcol']]);
        break;
      case 'u1name':
        twrite_center($img, ncfont($tv['u1power'], $tv['u1sex']), $x, $y, $v['width'], $tv[$v['dbcol']]);
        break;
      case 'replies':
        twrite_center($img, $whitefont, $x, $y, $v['width'], $tv[$v['dbcol']]);
        break;
      case 'lastusernameago':
        twrite_left($img, ncfont($tv['u2power'], $tv['u2sex']), $x, $y, $v['width'], $tv['u2name']);
        twrite_right($img, $whitefont, $x, $y, $v['width'], $tv['lastpostago']);
        break;
      }
      $y+=12;
    }
    $x += $v['width']+$colgap;
  }
  
  Header('Content-type:image/png');
  ImagePNG($img);
  ImageDestroy($img);
  
  //print_r($threads);
  //print_r($dispcols);
  
  
  function format_lines_write($a, $xorg, $yorg)
  {
    global $img, $whitefont;
    $y = $yorg;
    foreach ($a as $l) {
      $x = $xorg;
      foreach ($l as $sp) {
        switch ($sp['font']) {
        case 'white':
        default:
          twrite($img, $whitefont, $x, $y, 0, $sp['text']);
          break;
        case 'name':
          twrite($img, ncfont($sp['power'],$sp['sex']), $x, $y, 0, $sp['text']);
          break;
        }
        $x += twidth($sp['text']);
      }
      $y += 12;
    }
  }
  
  function format_lines_size($a)
  {
    $r = array('width'=>0, 'height'=>0);
    foreach ($a as $v) {
      $w = format_line_width($v);
      if ($w > $r['width']) {
        $r['width'] = $w;
      }
      $r['height'] += 12;
    }
    return $r;
  }
  
  function format_line_width($a)
  {
    $r = 0;
    
    foreach ($a as $v) {
      $r += twidth($v['text']);
      //print_r($v);
      //print $r."<br>";
    }
    return $r;
  }
  
  function twidth($s)
  {
    global $glyphwidths;
    $r = 0;
    for ($i = 0; $i < strlen($s); $i++) {
      $r += $glyphwidths[ord($s[$i])];
    }
    //return intval(($r + 7)/8);
    return $r;
  }
  
  function timeunits_short($sec){
    if($sec<    60) return $sec.'s';
    if($sec<  3600) return floor($sec/60).'m';
    if($sec< 86400) return floor($sec/3600).'h';
    return floor($sec/86400).'d';
  }  
  
  function cookielogon()
  {
    global $log,$loguser,$sql;
    /* pasted from common.php */
    /* don't need all the other queries that common.php runs */
    $log=false;
    if($_COOKIE['user']>0){
      $_COOKIE[pass]=decryptpwd($_COOKIE[pass]);
//      echo ":$_COOKIE[pass]:";
//      echo $_COOKIE[user];
      if($id=checkuid($_COOKIE['user'],$_COOKIE['pass'])){
        $log=true;
        $loguser=$sql->fetchq("SELECT * FROM users WHERE id=$id");
      }
    }
    if(!$log){
    $loguser		= array();
      $loguser['power']=0;
      //$loguser['timeformat']='h:i A';
      //$loguser['signsep']=0;
    }
  }
  
  function ncfont($power, $sex)
  {
    global $ncfontcache, $ncolors;
    
    $power = clamp($power, -1, 3);
    $sex = ($sex >=0 && $sex <= 2)?$sex:2;
    
    if (isset($ncfontcache[$power][$sex])) {
      return $ncfontcache[$power][$sex];
    }
    $c = $ncolors[$power][$sex];
    //print_r($ncolors);
    return ($ncfontcache[$power][$sex] = fontc(($c[0]+255)/2, ($c[1]+255)/2, ($c[2]+255)/2, $c[0], $c[1], $c[2], 0, 0, 0));
  }
  
  function clamp($x, $min, $max)
  {
    if ($x > $max) {
      return $max;
    } else if ($x < $min) {
      return $min;
    } else {
      return $x;
    }
  }
  
  function resultall($qs)
  {
    global $sql;
    $res = $sql->query($qs);
    $r = array();
    while ($arr = $sql->fetch($res)) {
      $t = array();
      foreach ($arr as $k => $v) {
        /* throw away all numeric keys, we don't want them */
        if (!is_numeric($k)) {
          $t[$k] = $v;
        }
      }
      $r[] = $t;
    }
    return $r;
  }
  
  function twrite_left($img, $font,$x,$y,$w,$text)
  {
    twrite($img, $font, $x, $y, 0, $text);
  }
  
  function twrite_center($img, $font,$x,$y,$w,$text)
  {
    $sw = twidth($text);
    twrite($img, $font, $x + ($w - $sw)/2, $y, 0, $text);
  }
  
  function twrite_right($img, $font,$x,$y,$w,$text)
  {
    $sw = twidth($text);
    twrite($img, $font, $x + $w - $sw, $y, 0, $text);
  }
  
  function twrite($img, $font,$x,$y,$l,$text){
    global $glyphwidths;
    $text.='';
    if(strlen($text)<$l) $x+=($l-strlen($text))*8;
    for ($i=0; $i<strlen($text); $i++) {
      //ImageCopy($img,$font,$i*8+$x,$y,(ord($text[$i]) & 0x0f)<<8, (ord($text[$i])&0xf0)>>1,8,8);
      ImageCopy($img,$font,$x,$y,(ord($text[$i])%16)*8,floor(ord($text[$i])/16)*12,$glyphwidths[ord($text[$i])],12);
      //print "($img,$font,$i*8+$x,$y,(ord($text[$i]) & 0x0f)<<8, (ord($text[$i])&0xf0)>>1,8,8)<br>";
      $x += $glyphwidths[ord($text[$i])];
    }
  }
  
  function fontc($r1,$g1,$b1, $r2,$g2,$b2, $r3,$g3,$b3){
    //print ("fontc called\n");
    $font=ImageCreateFromPNG('yi_font2.png');
    ImageColorTransparent($font,1);
    //ImageColorSet($font,6,$r1,$g1,$b1);
    //ImageColorSet($font,5,($r1*2+$r2)/3,($g1*2+$g2)/3,($b1*2+$b2)/3);
    //ImageColorSet($font,4,($r1+$r2*2)/3,($g1+$g2*2)/3,($b1+$b2*2)/3);
    ImageColorSet($font,3,$r2,$g2,$b2);
    //ImageColorSet($font,0,$r3,$g3,$b3);
    //print $font;
    return $font;
  }
  
  function box($img,$color_index,$x,$y,$w,$h){
    //$x*=8;
    //$y*=8;
    //$w*=8;
    //$h*=8;
    ImageRectangle($img,$x+0,$y+0,$x+$w-1,$y+$h-1,$color_index['bxb0']);
    ImageRectangle($img,$x+1,$y+1,$x+$w-2,$y+$h-2,$color_index['bxb3']);
    ImageRectangle($img,$x+2,$y+2,$x+$w-3,$y+$h-3,$color_index['bxb1']);
    ImageRectangle($img,$x+3,$y+3,$x+$w-4,$y+$h-4,$color_index['bxb2']);
    ImageRectangle($img,$x+4,$y+4,$x+$w-5,$y+$h-5,$color_index['bxb0']);
    for($i=5;$i<$h-5;$i++){
      $n=(1-$i/$h)*100;
      ImageLine($img,$x+5,$y+$i,$x+$w-6,$y+$i,$color_index[$n]);
    }
  }

?>
