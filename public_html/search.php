<?php

require "lib/common.php";
require "lib/threadpost.php";

pageheader("Simple Search");

$showtags    = false; 
$likefilter  = true;  // Filter SQL LIKE wildcards (% and _)

// Defaults
$_POST['text']   = isset($_POST['text'])    ? $_POST['text'] : "";
$_POST['user']   = isset($_POST['user'])    ? trim($_POST['user']) : "";
$_POST['ipmask'] = isset($_POST['ipmask'])  ? trim($_POST['ipmask']) : "";
// Search in thread titles
$_POST['mode']  = isset($_POST['mode'])  ? (int) $_POST['mode'] : 0;
// Search in General Forum only
$_POST['forumena'] = isset($_POST['forumena']) ? (int) $_POST['forumena'] : 1;
$_POST['forum']    = isset($_POST['forum'])    ? (int) $_POST['forum'] : 1;
// Search in the last 30 days
$_POST['date']     = isset($_POST['date'])     ? (int) $_POST['date']     : 1;
$_POST['datedays'] = isset($_POST['datedays']) ? (int) $_POST['datedays'] : 30;
// Do not order posts
$_POST['order']  = isset($_POST['order'])  ? (int) $_POST['order']  : 0;
$_POST['filter'] = isset($_POST['filter']) ? (int) $_POST['filter'] : 0;
// Date ranges
$datefrom = fieldstotimestamp('from', '_POST');
$dateto   = fieldstotimestamp('to', '_POST');



if (isset($_POST['search'])) {
		
	switch ($_POST['mode']) {
		case 0: $stable = "t";  $sfield = "title"; break;
		case 1: $stable = "pt"; $sfield = "text"; break;
		default: 
			noticemsg("Error", "Invalid mode selected."); 
			pagefooter();
	}
	
	$message = "";
	$qsearch = array();
	$qsearch[] = parsesearch($_POST['text'], "{$stable}.{$sfield}", $qval, $matches, $likefilter);
	
	if (!$qsearch[0] || strlen(trim(str_replace(array('AND', 'OR', '%', '_', '\\'), '', $_POST['text']))) < 4)
		$message = "You have to search for at least 4 characters.";
	if (count($matches) > 5)
		$message = "Too many AND/OR statements.";
	
	if ($message) {
		noticemsg("Error", "The search could not start for the following reason(s): {$message}");
		$_POST['search'] = NULL; // Do not display "No results found" message
	} else {
		// Display a message immediately while we're fetching posts. This will be hidden after we're done.
		print "
		<br>
		$L[TBL1] id='pleasewait'>
			$L[TRh]>$L[TDh]>Please wait</td></tr>
			$L[TR]>$L[TD1c] style='padding: 25px'>A search is in progress...</td></tr>
		</table>";
		
		// Get the list of forums we're allowed to search in
		$forums = $sql->getresultsbykey("
			SELECT f.id, f.title
			FROM forums f
			LEFT JOIN categories c ON f.cat = c.id 
			WHERE f.id IN ".forums_with_view_perm()." AND c.id IN ".cats_with_view_perm()."
		", 'id', 'title');
		if (!$forums) { // just in case
			noticemsg("Error", "You aren't allowed to search in any forum.");
			pagefooter();
		}
		$allowedforums = "(".implode(',', array_keys($forums)).")";
		//--
		
		// Common shared options
		$qsearch[0] = "({$qsearch[0]})";
		if (has_perm('view-post-ips') && $_POST['ipmask']) {
			$qsearch[] = "u.ip LIKE ?";
			$qval[]    = str_replace('*', '%', $_POST['ipmask']);
		}
		if ($_POST['forumena'] && $_POST['forum']) {
			$qsearch[] = "t.forum = ?";
			$qval[]    = $_POST['forum'];
		}
		if (!$_POST['filter']) {
			$qsearch[] = "t.filter = ".($_POST['lulz'] ? 1 : 0);
		}
		
		
		if ($_POST['mode'] == 0) { 
			// Search options for thread title mode
			$limit   = $loguser['tpp'];
			
			if ($_POST['date'] == 1 && $_POST['datedays'] > 0) {
				$qsearch[] = "t.lastdate > ?";
				$qval[]    = ctime() - $_POST['datedays'] * 86400;
			} else if ($_POST['date'] == 2 && $datefrom && $dateto && $datefrom <= $dateto) {
				$qsearch[] = "t.lastdate > ? AND t.lastdate < ?";
				$qval[]    = $datefrom;
				$qval[]    = $dateto;
			} else {
				$_POST['date'] = 0; // Do not preserve choice on bad $datefrom/$dateto
			}
			if ($_POST['user']) {
				$qsearch[] = "u1.name = ?";
				$qval[]    = $_POST['user'];
			}
			switch ($_POST['order']) {
				case 1: $order = "ORDER BY t.lastdate ASC"; break;
				case 2: $order = "ORDER BY t.lastdate DESC"; break;
				default: $order = "";
			}		
			$qwhere = $qsearch ? implode(' AND ', $qsearch)." AND" : "";
			
			
			$total = $sql->resultp("
				SELECT COUNT(*) 
				FROM threads t 
				LEFT JOIN users u1 ON u1.id  = t.user 
				WHERE {$qwhere} t.forum IN {$allowedforums}
			", $qval);
			$pageselect = pageselect($total, $limit); // Will restrict $_POST['page'] to real values
			
			$results = $sql->prepare("
				SELECT ".userfields('u1','u1').", ".userfields('u2','u2').", t.*, 0 ispoll, 1 isread
				FROM threads t 
				LEFT JOIN users u1 ON u1.id = t.user 
				LEFT JOIN users u2 ON u2.id = t.lastuser
				WHERE {$qwhere} t.forum IN {$allowedforums}
				{$order}
				LIMIT ".($_POST['page'] * $limit).", {$limit}
			", $qval);
			
			if ($showtags) {
				$tags = $sql->getarray("SELECT * FROM tags WHERE fid ".($_POST['forumena'] ? " = {$_POST['forum']}" : " IN {$allowedforums}"));
			}
			
		} else { 
			// Search options for posts text mode
			$limit   = $loguser['ppp'];
			
			if ($_POST['date'] == 1 && $_POST['datedays'] > 0) {
				$qsearch[] = "p.date > ?";
				$qval[]    = ctime() - $_POST['datedays'] * 86400;
			} else if ($_POST['date'] == 2 && $datefrom && $dateto) {
				$qsearch[] = "p.date > ? AND p.date < ?";
				$qval[]    = $datefrom;
				$qval[]    = $dateto;
			} else {
				$_POST['date'] = 0; // Do not preserve choice on bad $datefrom/$dateto
			}
			if ($_POST['user']) {
				$qsearch[] = "u.name = ?";
				$qval[]    = $_POST['user'];
			}
			switch ($_POST['order']) {
				case 1: $order = "ORDER BY p.id ASC"; break;
				case 2: $order = "ORDER BY p.id DESC"; break;
				default: $order = "";
			}			
			$qwhere = $qsearch ? implode(' AND ', $qsearch)." AND" : "";
			
			// TODO: why not just as an extra option in userfields()
			$ufields = array('posts','regdate','lastpost','lastview','location','rankset','title','usepic','head','sign','signsep', 'minipic');
			$fieldlist = "";
			foreach ($ufields as $field)
				$fieldlist .= "u.{$field} u{$field},";
			
			$results = $sql->prepare("
				SELECT ".userfields('u','u').", $fieldlist u.posts uposts, p.*, pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.id tid, t.title ttitle, t.forum tforum 
				FROM posts p 
				LEFT JOIN poststext  pt  ON p.id     = pt.id 
				LEFT JOIN poststext  pt2 ON pt.id    = pt2.id AND pt2.revision = (pt.revision+1) 
				LEFT JOIN users      u   ON p.user   = u.id 
				LEFT JOIN threads    t   ON p.thread = t.id 
				WHERE {$qwhere} ISNULL(pt2.id) ".(true ? "AND p.deleted = 0" : "")."
				AND t.forum IN {$allowedforums}
				{$order}
			", $qval);
			
			$total = $sql->numrows($results);
			$pageselect = pageselect($total, $limit);
		}
		
		
	
	}
}
if (!$datefrom) $datefrom = ctime() - 86400;
if (!$dateto)   $dateto   = ctime();




print "
<form method='POST' action='?'>
$L[TBL1]>
	$L[TRh]>$L[TDhc] colspan=2><b>Search</b></td></tr>
	$L[TR1]>
		$L[TD1c]><b>Search for:</b></td>
		$L[TD2]>$L[INPt]='text' size=40 value=\"".htmlspecialchars($_POST['text'])."\"></td>
	</tr>
	$L[TR1]>
		$L[TD1c]><b>Search in:</b></td>
		$L[TD2]>".fieldoption('mode', $_POST['mode'], array(
			0 => 'Thread title',
			1 => 'Post text'
		))."</td>
	</tr>
	$L[TR1]>
		$L[TD1c]><b>User name:</b></td>
		$L[TD2]>$L[INPt]='user' size=20 value=\"".htmlspecialchars($_POST['user'])."\"></td>
	</tr>
".(has_perm('view-post-ips') ? "
	$L[TR1]>
		$L[TD1c]><b>IP mask:</b></td>
		$L[TD2]>
			$L[INPt]='ipmask' size=16 maxlength=32 value=\"".htmlspecialchars($_POST['ipmask'])."\">
			<small>use * as wildcard</small>
		</td>
	</tr>
" :"")."
	$L[TR1]>
		$L[TD1c]><b>Forum:</b></td>
		$L[TD2]>".fieldoption('forumena', $_POST['forumena'], array(
			0 => 'All forums',
			1 => 'Only in '.forumlist('forum', $_POST['forum'])
		))."</td>
	</tr>
	$L[TR1]>
		$L[TD1c]><b>Date:</b></td>
		$L[TD2]>".fieldoption('date', $_POST['date'], array(
			0 => 'Any date',
			1 => "Last $L[INPt]='datedays' size=4 maxlength=4 value='{$_POST['datedays']}' style='text-align: right'> days",
			2 => "From ".datetofields($datefrom, 'from')." to ".datetofields($dateto, 'to')." (mm/gg/yyyy)",
		))."</td>
	</tr>
	$L[TR1]>
		$L[TD1c]><b>Show NSFW threads:</b></td>
		$L[TD2]>".fieldoption('filter', $_POST['filter'], array(
			0 => 'No',
			1 => "Yes"
		))."
		$L[INPh]='lulz' value=0>
		</td>
	</tr>
	$L[TR1]>
		$L[TD1c]><b>Order:</b></td>
		$L[TD2]>".fieldoption('order', $_POST['order'], array(
			0 => 'Disabled',
			1 => 'Oldest first',
			2 => 'Newest first',
		))."</td>
	</tr>
".(isset($_POST['search']) ? "
	$L[TR1] id='pagetr'>
		$L[TD1c]><b>View page:</b></td>
		$L[TD2]>{$pageselect}</td>
	</tr>
" : "")."
	$L[TR2]>
		$L[TD1c]></td>
		$L[TD2]>$L[INPs]='search' value='Search'></td>
	</tr>
</table>
</form>
";


// Search results
if (isset($_POST['search'])) {
	
	if (!$total) {
		noticemsg("Search", "No results found.");
	} else if (!$_POST['mode']) { // Threads mode
		
		print "
		<br>
		$L[TBL1]>
			$L[TRh]>
				$L[TDh] style='width: 17px'>&nbsp;</td>
				$L[TDh] style='width: 17px'>&nbsp;</td>
				$L[TDh]>Forum</td>
				$L[TDh]>Title</td>
				$L[TDh] style='width: 130px'>Started by</td>
				$L[TDh] style='width: 50px'>Replies</td>
				$L[TDh] style='width: 50px'>Views</td>
				$L[TDh] style='width: 130px'>Last post</td>
			</tr>
";
		for ($i = 0; $thread = $sql->fetch($results); ++$i) {		
			$pagelist = pagelist($thread['replies'] + 1, $loguser['ppp'], 'thread.php?id='.$thread['id'], -1, $loguser['longpages']);
			if ($pagelist) {
				$pagelist = " <span class='sfont'>(".lcfirst($pagelist).")</span>";
			}

			// Thread status
			$status   = '';
			$statalt  = '';
			if ($thread['closed']) { 
				$status  .= 'o'; 
				$statalt  = 'OFF'; 
			}
			if ($thread['replies'] >= 50) { // $config['hotcount'] / $misc['hotcount']
				$status .= '!'; 
				if (!$statalt) $statalt = 'HOT';
			}
			if ($loguser['id']){
				if (!$thread['isread']) { 
					$status .= 'n'; 
					if ($statalt != 'HOT') $statalt  = 'NEW'; 
				}
			} else {
				if ($thread['lastdate'] > (ctime() - 3600)) { 
					$status.='n'; 
					if ($statalt!='HOT') $statalt='NEW';
				}
			}
			$status = $status ? rendernewstatus($status) : "&nbsp;";
			
			// Other data
			if (!$thread['title']) $thread['title'] = '?'; // 'hurr durr I made a blank thread';
			$icon = $thread['icon'] ? "<img src=\"{$thread['icon']}\" style='max-height: 15px'>" : "";
			
			// Handle tags
			$taglist = "";
			if ($showtags) {
				for ($k = 0; $k < sizeof($tags); ++$k) {
					$t = $tags[$k];
					if ($thread['tags'] & (1 << $t['bit'])) {
						if ($config['classictags']) {
							list($r,$g,$b) = sscanf($t['color'],"%02X%02X%02X");
							if ($r < 128 && $g < 128) { 
								$r += 32;
								$g += 32; 
							}
							$t['color2'] = sprintf("%02X%02X%02X",$r,$g,$b);
							$taglist.=" <span style='background-repeat: repeat; background: url('gfx/tpng.php?c={$t['color']}&t=105'); font-size: 7pt; font-family: Small Fonts, sans-serif; padding: 1px 1px'>"
									  ."<span style='background-repeat: repeat; background: url('gfx/tpng.php?c={$t['color']}&t=105'); font-size: 7pt; font-family: Small Fonts, sans-serif; padding: 2px 3px; color: {$t['color2']}' alt=\"{$t['name']}\">{$t['tag']}"
									  ."</span></span>";
						} else {
							$taglist .= " <img src=\"./gfx/tags/tag{$t['fid']}-{$t['bit']}.png\" alt=\"{$t['name']}\" title=\"{$t['name']}\" style='position: relative; top: 3px'/>";
						}
					}
				}
			}

			$tr = ($i % 2 ? 'TR2' : 'TR3').'c';

			print "
			$L[$tr]>
				$L[TD1]>$status</td>
				$L[TD]>$icon</td>
				$L[TD]><a href='forum.php?id={$thread['forum']}'>{$forums[$thread['forum']]}</a></td>
				$L[TDl]><a href='thread.php?id={$thread['id']}'>".forcewrap(htmlval($thread['title']))."</a>$taglist$pagelist</td>
				$L[TD]>".userlink($thread,'u1',$config['startedbyminipic'])."</td>
				$L[TD]>{$thread['replies']}</td>
				$L[TD]>{$thread['views']}</td>
				$L[TD]>
					<nobr>".cdate($dateformat,$thread['lastdate'])."</nobr><br>
					<span class='sfont'>
						by&nbsp;".userlink($thread,'u2',$config['forumminipic'])."
						&nbsp;<a href='thread.php?pid={$thread['lastid']}#{$thread['lastid']}'>&raquo;</a>
					</span>
				</td>
			</tr>";
		}
		print "</table>";
	} else { // Posts mode
		$data = $results->data_seek($_POST['page'] * $limit);
		
		for ($i = 0; ($post = $sql->fetch($results)) && $i < $limit; ++$i){
			// Boldify text
			$post[$sfield] = preg_replace($matches, "<b>$0</b>",$post[$sfield]);
			$post['maxrevision'] = $post['revision']; // not pinned, hence the max. revision equals the revision we selected
			print "<br>" . threadpost($post, 0);
		}
	}
	
	
?>
<script type='text/javascript'>
	document.getElementById('pleasewait').style.display = 'none';
	document.getElementById('pagetr').style.display = 'table-row';
</script>
<?php
}

pagefooter();


function parsesearch($srctext, $srcfield, &$qval, &$boldify, $likefilter = true) {
	// If wildcards have to be filtered, do it now
	if ($likefilter) {
		$srctext = strtr($srctext, array('%' => '\\%', '_' => '\\_'));
	}
	
	// Get an array of non-empty words
	$words = explode(" ", trim($srctext));
	$words = array_filter($words, function($x) { return $x !== ''; });
	if (!$words) 
		return "";

	// Iterate over each word to generate the SQL statement / query values / bold text regex
	$qsearch = "";       // Search query with placeholders
	$qval    = array();  // Query values
	$boldify = array();  // Words to highlight (regex)
	$curword  = "";      // Current processed word
	foreach ($words as $x) {
		if ($curword && ($x == 'AND' || $x == 'OR')) { // AND / OR are separators
			$qsearch  .= "{$srcfield} LIKE ? {$x} ";
			$curword   = substr($curword, 1);
			$boldify[] = "/".preg_quote($curword, '/')."/i";
			$qval[]    = "%{$curword}%";
			$curword   = "";
		} else {
			$curword  .= " {$x}";
		}
	}
	$qsearch  .= "{$srcfield} LIKE ?";
	$curword   = substr($curword, 1);
	$boldify[] = "/".preg_quote($curword, '/')."/i";
	$qval[]    = "%{$curword}%";
	
	return $qsearch;
}