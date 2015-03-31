<?php

  header('Content-type: text/xml');

  require 'lib/common.php';

  if(isssl()) $config[base]=$config[sslbase];

  print "<?xml version=\"1.0\"?>
".      "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">
".      "  <channel>
".      "    <title>$boardtitle</title>
".      "    <copyright>Posts are owned by the poster. Acmlmboard $abversion software Copyright 2005-2015 $boardprog</copyright>
".      "    <generator>Acmlmboard $abversion ($abdate)</generator>
".      "    <ttl>5</ttl>
".      "    <atom:link href=\"$config[base]$url\" rel=\"self\" type=\"application/rss+xml\" />
".      "    <language>en</language>
".      "    <category>forum</category>
";

  $fieldlist='';
  $ufields=array('id','name','sex','group_id');
  foreach($ufields as $field)
    $fieldlist.="u1.$field u1$field, u2.$field u2$field, ";

  $mintime=time()-3*86400;

  if(isset($_GET['thread'])) {
    //This dosen't work as blackhole noted, we need to replace the SQL argument
    $posts=$sql->query("SELECT $fieldlist pt.*, p.*, t.title ttitle, f.id fid, f.title ftitle "
                      ."FROM posts p "
                      ."LEFT JOIN poststext pt ON pt.id=p.id "
                      ."LEFT JOIN poststext pt2 ON pt2.revision=(pt.revision+1) AND pt2.id=pt.id "
                      ."LEFT JOIN threads t ON t.id=$_GET[thread] "
                      ."LEFT JOIN users u1 ON u1.id=t.user "
                      ."LEFT JOIN users u2 ON u2.id=p.user "
                      ."LEFT JOIN forums f ON f.id=t.forum "
                      ."WHERE p.thread=$_GET[thread] "
                      .  "AND f.id IN ".forums_with_view_perm()." "
                      .  "AND ISNULL(pt2.id) "
                      ."ORDER BY p.id DESC "
                      ."LIMIT 20");
    $p=$sql->fetch($posts);
    print "    <link>$config[base]$config[path]</link>
".        "    <description>Latest posts in \"$p[ttitle]\"</description>
".        "    <image>
".        "      <url>$config[base]$config[path]theme/abII.png</url>
".        "      <title>$boardtitle</title>
".        "      <link>$config[base]$config[path]</link>
".        "    </image>
".        "    <lastBuildDate>".date("r",$p[lastdate])."</lastBuildDate>
";
    do {
      $p[text]=preg_replace("'<(.*?)>'si" ,"" ,$p[text]); 
      $p[text]=preg_replace("'\[quote(.*?)\[/quote\]'si","",$p[text]);
      $p[text]=preg_replace("'\[(b|u|i|s)\](.*?)\[/(b|u|i|s)\]'si","\\2",$p[text]);
      $p[text]=str_replace("\n"," ",$p[text]);
      $ptext=substr($p[text],0,48);
      if(strlen($p[text])>48) $ptext.=" (...)";
      //Whitespace feels awkward within <description></description> but i'm not sure what to change it to, feel free to change this blackhole89!
      //And yes, that is how you do HTML in RSS. :)
      print "    <item>
".          "      <title>".date("[$loguser[timeformat]]",$p[date])." by $p[u2name]: $ptext</title>
".          "      <description>Post by &lt;a href=\"$config[base]$config[path]profile.php?id=$p[u2id]\"&gt;$p[u2name]&lt;/a&gt;, ".
                     "thread by &lt;a href=\"$config[base]$config[path]profile.php?id=$p[u1id]\"&gt;$p[u1name]&lt;/a&gt; ".
                     "in &lt;a href=\"$config[base]$config[path]forum.php?id=$p[forum]\"&gt;$p[ftitle]&lt;/a&gt;</description>
".          "      <pubDate>".date("r",$p[date])."</pubDate>
".          "      <category>$p[ftitle]</category>
".          "      <guid>$config[base]$config[path]thread.php?pid=$p[id]#$p[id]</guid>
".          "    </item>
";
    } while($p=$sql->fetch($posts));
  } else if(isset($_GET['forum'])){
    $threads=$sql->query("SELECT $fieldlist t.*, f.id fid, f.title ftitle "
                        ."FROM threads t "
                        ."LEFT JOIN users u1 ON u1.id=t.user "
                        ."LEFT JOIN users u2 ON u2.id=t.lastuser "
                        ."LEFT JOIN forums f ON f.id=t.forum "
                        ."WHERE t.forum=$_GET[forum] "
                        .  "AND f.id IN ".forums_with_view_perm()." "
                        ."ORDER BY t.lastdate DESC "
                        ."LIMIT 20");
    $t=$sql->fetch($threads);
    print "    <link>$config[base]$config[path]</link>
".        "    <description>The latest active threads of $t[ftitle]</description>
".        "    <image>
".        "      <url>$config[base]$config[path]theme/abII.png</url>
".        "      <title>$boardtitle</title>
".        "      <link>$config[base]$config[path]</link>
".        "    </image>
".        "    <lastBuildDate>".date("r",$t[lastdate])."</lastBuildDate>
";
    do {
      //Whitespace feels awkward within <description></description> but i'm not sure what to change it to, feel free to change this blackhole89!
      //And yes, that is how you do HTML in RSS. :)
      print "    <item>
".          "      <title>$t[title] - ".date("[$loguser[timeformat]]",$t[lastdate])." by $t[u2name]</title>
".          "      <description>Last post by &lt;a href=\"$config[base]$config[path]profile.php?id=$t[u2id]\"&gt;$t[u2name]&lt;/a&gt;, ".
                     "thread by &lt;a href=\"$config[base]$config[path]profile.php?id=$t[u1id]\"&gt;$t[u1name]&lt;/a&gt;</description>
".          "      <pubDate>".date("r",$t[lastdate])."</pubDate>
".          "      <category>$t[ftitle]</category>
".          "      <guid>$config[base]$config[path]thread.php?pid=$t[lastid]#$t[lastid]</guid>
".          "    </item>
";
    } while($t=$sql->fetch($threads));
  } else {
    $threads=$sql->query("SELECT $fieldlist t.*, f.id fid, f.title ftitle "
                        ."FROM threads t "
                        ."LEFT JOIN users u1 ON u1.id=t.user "
                        ."LEFT JOIN users u2 ON u2.id=t.lastuser "
                        ."LEFT JOIN forums f ON f.id=t.forum "
                        ."LEFT JOIN categories c ON f.cat=c.id "
                        ."WHERE f.id IN ".forums_with_view_perm()." "
                        .  "AND c.id IN ".cats_with_view_perm()." "
                        .  "AND t.lastdate>$mintime "
                        ."ORDER BY t.lastdate DESC "
                        ."LIMIT 20");
    $t=$sql->fetch($threads);
    print "    <link>$config[base]$config[path]</link>
".        "    <description>The latest active threads of $boardtitle</description>
".        "    <image>
".        "      <url>$config[base]$config[path]theme/abII.png</url>
".        "      <title>$boardtitle</title>
".        "      <link>$config[base]$config[path]</link>
".        "    </image>
".        "    <lastBuildDate>".date("r",$t[lastdate])."</lastBuildDate>
";
    do {
      //Whitespace feels awkward within <description></description> but i'm not sure what to change it to, feel free to change this blackhole89!
      //And yes, that is how you do HTML in RSS. :)
      print "    <item>
".          "      <title>$t[title] - ".date("[$loguser[timeformat]]",$t[lastdate])." by $t[u2name]</title>
".          "      <description>Last post by &lt;a href=\"$config[base]$config[path]profile.php?id=$t[u2id]\"&gt;$t[u2name]&lt;/a&gt;, ".
                     "thread by &lt;a href=\"$config[base]$config[path]profile.php?id=$t[u1id]\"&gt;$t[u1name]&lt;/a&gt; ".
                     "in &lt;a href=\"$config[base]$config[path]forum.php?id=$t[forum]\"&gt;$t[ftitle]&lt;/a&gt;</description>
".          "      <pubDate>".date("r",$t[lastdate])."</pubDate>
".          "      <category>$t[ftitle]</category>
".          "      <guid>$config[base]$config[path]thread.php?pid=$t[lastid]#$t[lastid]</guid>
".          "    </item>
";
    } while($t=$sql->fetch($threads));
 }
  print "  </channel>
".      "</rss>
";
  
?>
