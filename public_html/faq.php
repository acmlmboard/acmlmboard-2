<?php
include('lib/common.php');
include('lib/diff/Diff.php');
include('lib/diff/Diff/Renderer/inline.php');

//Smilies List
$smilieslist = $sql->query("SELECT * FROM `smilies`");
$numsmilies = $sql->resultq("SELECT COUNT(*) FROM `smilies`");
$smiliewidth = ceil(sqrt($numsmilies));
$smilietext = "<table>";

$x = 0;
while ($smily = $sql->fetch($smilieslist)) {
  if ($x == 0) {
    $smilietext .= "<tr>";
  }
  $smilietext .= "<td width='35'>$smily[text]</td><td width='27'><img src='$smily[url]'/></td><td width='5'></td>";
  ++$x;
  $x %= $smiliewidth;
  if ($x == 0) {
    $smilietext .= "</tr>";
  }
}
$smilietext .= "</table>";
pageheader("FAQ");



$ncx = $sql->query("SELECT title, nc0, nc1, nc2 FROM `group` WHERE nc0 != '' ORDER BY sortorder ASC");
$nctable = "";
$sexname = array('male','female','unspec.');

while ($ncr = $sql->fetch($ncx)) {

	$nctable .= "<tr>";

	for ($sex = 0; $sex < 3; $sex++) {
		$nc = $ncr["nc$sex"];
		$nctable .=
		"<td width='200'><b><font color='#$nc'>".$ncr['title'].", ".$sexname[$sex]."</td>";
	}

	$nctable .= "</tr>";

}

/*Highlighting system core
Not ready yet. Do not uncomment until ready.
$hl=$_GET['hl'];
$hls=explode($hl,":");
*/

//Begin written FAQ

$L[FAQTD]="$L[TD1] style='padding:10px!important;'";

print "$L[TBL1]>
".    "  $L[TRh]>
".    "    $L[TDh]>FAQ</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "    <a href=\"#gpg\">General Posting Guidelines</a><br><br>

".    "    <a href=\"#sprite\">What are these little character doodads appearing on the board?</a><br>
".    "    <a href=\"#move\">I just made a thread, where did it go?</a><br>
".    "    <a href=\"#rude\">I feel that a user is being rude to me. What do I do?</a><br>
".    "    <a href=\"#badge\">What are badges?</a><br>
".    "    <a href=\"#kcs\">What is \"KCS\"?</a><br>
".    "    <a href=\"#smile\">Are smilies and BBCode supported?</a><br>
".    "    <a href=\"#tags\">Board Specific tags (non-BBcode [tags] and other substitutions)</a><br>
".    "    <a href=\"#irc\">What's this IRC thing I keep hearing about?</a><br>
".    "    <a href=\"#reg\">Can I register more than one account?</a><br>
".    "    <a href=\"#layout\">How do I get a layout?</a><br>
".    "    <a href=\"#css\">What are we not allowed to do in our custom CSS layouts?</a><br>
".    "    <a href=\"#title\">How do I get a custom title?</a><br>
".    "    <a href=\"#rpg\">RPG Stats</a><br>
".    "    <a href=\"#itemshop\">Items and the Item Shop</a><br>";
if($syndromenable == 1) print "    <a href=\"#syndrome\">Acmlmboard Syndromes</a><br>";
print     "    <a href=\"#amps\">&Tags& (Amp tags)</a><br>
"/*.    "    <a href=\"#dispname\">Display Name System</a><br>
"*/.    "    <a href=\"#avatar\">What are avatars & mood avatars?</a><br>
".    "    <a href=\"#private\">Are private messages supported?</a><br>
".    "    <a href=\"#search\">Search Feature</a><br>
".    "    <a href=\"#calendar\">What is the calendar for?</a><br>
".    "    <a href=\"#usercols\">What do the username colours mean?</a><br>

".    "    </td>
".    "</table><br>";

print "$L[TBL1]>
".    "  $L[TRh]>$L[TDh]><a name='gpg'>General Posting Guidelines:
".    "  $L[TR]>$L[FAQTD]>Posting on a message forum is generally relaxed.  There are, however, a few things to keep in mind when posting.
".    "  <ul style=\"list-style-type: decimal;\">
".    "  <li>One word posts.  These types of posts generally do not add to the conversation topic and should be avoided at all cost.  Come on, at least form a complete sentence!
".    "  <li>Trolling/flaming/drama.  This behavior is totally unacceptable and will be dealt with accordingly, namely with a warning.  Direct (or even indirect) personal attacks on <b><u><i>any</i></u></b> member of this community for any reason whatsoever will result in immediate action.  Do NOT test us on this.
".    "  <li>Reviving, or \"bumping\" old threads.  If the last post in a thread was a month ago or more, we ask that you do not add another post unless you have something very relevant and interesting to add to the topic.
".    "  <li>Spamming.  Spam is a pretty broad and grey area.  Spam can be generalized as multiple posts with no real meaning to the topic or what anyone else is talking about.
".    "  <li>Staff impersonation and 'back seat moderation.' Staff impersonation will <b>not</b> be tolerated. Doing so will may result in an instant ban. While you may feel you are helping by telling a fellow member that they need to stop doing something you know is wrong, you may do more harm than good. If you see an issue please report the issue to the staff immediately.
".    "  <li>Suggestive Material.  Remember that there are others here who enjoy the board experience. Their standards are not necessarily going to be like yours all the time, so please, do not post anything pornographic or otherwise potentially disturbing to other members.
".    "  </ul>
".    "  <br><b><u>Procedural</u></b>:
".    "  <br>Kafuka follows the \"Three Strike Rule\". This means if you have been warned twice by staff for whatever reason, your third notice will be a ban and a reason, coupled with a ban length.  Each time you are given a \"strike\", you will receive a PM from a staff member stating so.  This PM will also include a link to the post in question and a reason for the warning.  Your third strike will come with a ban.   Ban lengths are as follows:
".    "  <br>
".    "  <table cellpadding=0>
".    "  <tr><td>Offence</td><td>Duration</td></tr>
".    "  <tr><td>1st</td><td>1 Week</td></tr>
".    "  <tr><td>2nd</td><td>2 Weeks</td></tr>
".    "  <tr><td>3rd</td><td>1 Month</td></tr>
".    "  <tr><td>4th</td><td>2 Months</td></tr>
".    "  <tr><td>5th</td><td>Indefinite</td></tr>
".    "  </table>
".    "  <br>Please note that these ban lengths are \"soft\" and may be changed and/or deviated from by staff at their discretion. Decisions made regarding length will not be negotiable. If you have been banned but not warned, let a member of staff know.
".    "  <br>
".    "  <br><b><u>Behavioral</u></b>:
".    "  <br>Following one rule doesn't mean your post is automatically acceptable. If it is distasteful, repugnant, or offensive, then don't post it.
".    "  <br>
".    "  <br>If your post is seen by staff to incite drama, put down others, have negative connotations/bad attitude, or otherwise find fault therein, they have absolute right in deciding what to do with it and with you.
".    "  <br>
".    "  <br>IRC is IRC, and the board is the board, and there's a distinct level of separation between the two. However, we acknowledge that they are closely related and will make decisions based on your actions from both mediums of this community. This means if you're prone to being rude on IRC, and then rude on the board, it will most likely be considered when determining disciplinary action.
".    "  <br>
".    "  <br><b><u>Codeside</u></b>:
".    "  <br>The use of CSS usage to change your username colour, impersonate being staff, or similar is forbidden. Any alteration to one's username (font, icon etc) fake custom titles, and other additional text in a non-post field are under discretion of the the staff. Likewise, use of CSS that changes the board layout, others' posts or anything outside of your own post is forbidden. Failure to do so can result in deletion of your post layout, or even disabling the feature on your account altogether.
".    "  <br>
".    "  <br><b><u>Disclaimer</u></b>:
".    "  <br>If you don't like this place, or cannot deal with decisions or conversations had here, you will be offered no compensation and you will not be given any explanations herewith. This is a free service; so you are not entitled to anything contained herein, nor are you entitled to anything from any other party.

".    "  $L[TRh]>$L[TDh]><a name='sprite'>What are these little character doodads appearing on the board?
".    "  $L[TR]>$L[FAQTD]>Those are called Sprites. If you click on them, you will have \"found\" them and will be logged <a href=\"sprites.php\">here</a>. Collect them all!

".    "  $L[TRh]>$L[TDh]><a name='move'>I just made a thread, where did it go?
".    "  $L[TR]>$L[FAQTD]>It was probably moved or deleted by a staff member. If it was deleted, please make sure your thread meets the criteria we have established. If it was moved, look into the other forums and consider why it was moved there. If you have any questions, PM a staff member.

".    "  $L[TRh]>$L[TDh]><a name='rude'>I feel that a user is being rude to me. What do I do?
".    "  $L[TR]>$L[FAQTD]>Stay cool. Don't further disrupt the thread by responding <b>at all</b> to the rudeness. Let a member of staff know with a link to the offending post(s). Please note that responding to the rudeness is promoting flaming, which is a punishable offense.

".    "  $L[TRh]>$L[TDh]><a name='badge'>What are badges?
".    "  $L[TR]>$L[FAQTD]>Badges are special trinkets that a user gathers through special means. You can acquire them from staff, meeting special requirements, or even find them somewhere. For example; sometimes special contests will be held, with a badge given for those who participate and/or win said competition.

".    "  $L[TRh]>$L[TDh]><a name='kcs'>What is \"KCS\"?
".    "  $L[TR]>$L[FAQTD]>KCS stands for Kafuka Champion Series. It is an ongoing posting contest which is found in General Chat. All general posting rules apply.

".    "  $L[TRh]>$L[TDh]><a name='smile'>Are smilies and BBCode supported?
".    "  $L[TR]>$L[FAQTD]>There are some smilies here, a chart is below to show what smilies are supported.
".    "  <br>$smilietext 
".    "  <br>Likewise, a selection of BBCode is supported. See the chart below.
".    "  <table class=table cellspacing=0>
	 <tr><td class='b h'>Tag</td><td class='b h'>Effect
	 <tr><td class='b n1'>[b]<i>text</i>[/b]</td><td class='b n2'><b>Bold Text</b></td>
	 <tr><td class='b n1'>[i]<i>text</i>[/i]</td><td class='b n2'><i>Italic Text</i></td>
	 <tr><td class='b n1'>[u]<i>text</i>[/u]</td><td class='b n2'><u>Underlined Text</u></td>
	 <tr><td class='b n1'>[s]<i>text</i>[/s]</td><td class='b n2'><s>Striked-out Text</s></td>
	 <tr><td class='b n1'>[red]<i>text</i>[/red]</td><td class='b n2'><span style=\"color: #FFC0C0\">Black Text</span></td>
	 <tr><td class='b n1'>[green]<i>text</i>[/green]</td><td class='b n2'><span style=\"color: #C0FFC0\">Green Text</span></td>
	 <tr><td class='b n1'>[blue]<i>text</i>[/blue]</td><td class='b n2'><span style=\"color: #C0C0FF\">Blue Text</span></td>
	 <tr><td class='b n1'>[orange]<i>text</i>[/orange]</td><td class='b n2'><span style=\"color: #FFC080\">Orange Text</span></td>
	 <tr><td class='b n1'>[yellow]<i>text</i>[/yellow]</td><td class='b n2'><span style=\"color: #FFEE20\">Yellow Text</span></td>
	 <tr><td class='b n1'>[pink]<i>text</i>[/pink]</td><td class='b n2'><span style=\"color: #FFC0FF\">Pink Text</span></td>
	 <tr><td class='b n1'>[white]<i>text</i>[/white]</td><td class='b n2'><span style=\"color: #FFFFFF\">White Text</span></td>
	 <tr><td class='b n1'>[black]<i>text</i>[/black]</td><td class='b n2'><span style=\"color: #000000\">Black Text</span></td>
	 <tr><td class='b n1'>[color=<u>hexcolor</u>]<i>text</i>[/color]</td><td class='b n2'><span style=\"color: #BCDE9A\">Custom color Text</span></td>
	 <tr><td class='b n1'>[img]<i>URL of image to display</i>[/img]</td><td class='b n2'>Displays an image.</td>
	 <tr><td class='b n1'>[svg]<i>URL of a SVG image to display</i>[/svg]</td><td class='b n2'>Displays a SVG Image.</td>
	 <tr><td class='b n1'>[spoiler]<i>text</i>[/spoiler]</td><td class='b n2'>Used for hiding spoiler text.</td>
	 <tr><td class='b n1'>[code]<i>code text</i>[/code]</td><td class='b n2'>Displays code in a formatted box.</td>
	 <tr><td class='b n1'>[url]<i>URL of site or page to link to</i>[/url]<br>[url=<i>URL</i>]<i>Link title</i>[/url]</td><td class='b n2'>Creates a link with or without a title.</td>
	 <tr><td class='b n1'>@\"<i>User Name</i>\"<br>[user=<i>id</i>]</td><td class='b n2'>Creates a link to a user's profile complete with name colour.</td>
	 <tr><td class='b n1'>[forum=<i>id</i>]</td><td class='b n2'>Creates a link to a forum by id.</td>
	 <tr><td class='b n1'>[thread=<i>id</i>]</td><td class='b n2'>Creates a link to a thread by id.</td>
	 <tr><td class='b n1'>[youtube]<i>video id</i>[/youtube]</td><td class='b n2'>Creates an embeded YouTube video.</td>
	 </table>

".    "  $L[TRh]>$L[TDh]><a name='irc'>What's this IRC thing I keep hearing about?
".    "  $L[TR]>$L[FAQTD]>If you have an IRC client like mIRC, you can join a chatroom hosted by the Kafuka community. All crazy kinds of things can happen there, but will you take the plunge? Connect to the server irc.nolimitzone.com and join the channel #kafuka. Mibbit is a great client to start with if you don't know what you're doing.

".    "  $L[TRh]>$L[TDh]><a name='reg'>Can I register more than one account?
".    "  $L[TR]>$L[FAQTD]>No, you may not. Most uses for a secondary account tend to be to bypass bans. The the most common non-malicious use is to have a different name, and we have another feature will allow this cleanly.

".    "  $L[TRh]>$L[TDh]><a name='layout'>How do I get a layout?
".    "  $L[TR]>$L[FAQTD]>You must code one yourself. Sometimes there are others who might be willing to help you with your layout. If your layout is bad, you may find it deleted by a staff member. Make sure that when you design your layout, it isn't hard to read and doesn't stretch the tables.

".    "  $L[TRh]>$L[TDh]><a name='css'>What are we not allowed to do in our custom CSS layouts?
".    "  $L[TR]>$L[FAQTD]>While we allow very open and customizable layouts and side bars, we have a few rules that will be strictly enforced. Please read them over and follow them. Loss of post layout privileges will be enacted for those who are repeat offenders. If in doubt ask a member of staff. Staff has discretion in deciding violations. This list is expected to be updated regularly, so please make sure to stay up to date.
".    "  <br>The following are not allowed:
".    "  <ul style=\"list-style-type: decimal;\">
".    "  <li>Modification of anyone else's post layout <b>for any reason</b>.
".    "  <li>Modification of any tables, images, themes, etc outside of your personal layout.
".    "  <li>Adding a custom title to your profile via css. Custom titles are provided using a board system.
".    "  <li>Altering your Nick color in any way. Nick color is an indicator of staff, and it will be considered impersonation of staff.
".    "  <li>Altering the board layout. A good example of this would be CSS that has your post text or any part of that table appearing anywhere in your sidebar. 
".    "  </ul>


".    "  $L[TRh]>$L[TDh]><a name='title'>How do I get a custom title?
".    "  $L[TR]>$L[FAQTD]>Custom titles are titles you can use in addition to, or in place of the ranks provided by the board. There are three ways to get them:
".    "  <ul style=\"list-style-type: decimal;\">
".    "  <li>After 100 posts, or if you have been around 2 months you will need 50.
".    /*"  <li>Receive the *badge name goes here* badge. This can be given by staff, by special event, or by meeting a pre-determined goal.
".    */"  <li>Being a member of staff.
".    "  </ul>
".    "  <br>The custom title is a reward for being an active member of the community. Use of the custom title to impersonate staff, or to flame members/staff may result in the loss of custom title.

".    "  $L[TRh]>$L[TDh]><a name='rpg'>RPG Stats
".    "  $L[TR]>$L[FAQTD]>The RPG stats are based on your post count. Currently they are purely cosmetic. They were a part of a forum battle system developed by Acmlm.

".    "  $L[TRh]>$L[TDh]><a name='itemshop'>Items and the Item Shop
".    "  $L[TR]>$L[FAQTD]>Items are equipment that is actually a part of the RPG stat system. Like in an RPG equipment can boost your stats. An item shop allows you to use the RPG coins you get from posting to buy items and equipment. However, it doesn't currently matter what your stats are since they don't do anything right now, as said above.
";
if($syndromenable == 1){
print "  $L[TRh]>$L[TDh]><a name='syndrome'>Acmlmboard Syndromes
".    "  $L[TR]>$L[FAQTD]>The syndromes are an old Acmlmboard tradition carried over from the first version. Syndrome are triggered when you reach the amount of posts posted per day listed in the table below.
".    "  <br><table class=table cellspacing=0>
	    <tr><td class='b h'>Posts</td><td class='b h'>Syndrome
	    <tr><td class='b n1'> 75</td><td class='b n2'>".syndrome(75)."
	    <tr><td class='b n1'>100</td><td class='b n2'>".syndrome(100)."
	    <tr><td class='b n1'>150</td><td class='b n2'>".syndrome(150)."
	    <tr><td class='b n1'>200</td><td class='b n2'>".syndrome(200)."
	    <tr><td class='b n1'>250</td><td class='b n2'>".syndrome(250)."
	    <tr><td class='b n1'>300</td><td class='b n2'>".syndrome(300)."
	    <tr><td class='b n1'>350</td><td class='b n2'>".syndrome(350)."
	    <tr><td class='b n1'>400</td><td class='b n2'>".syndrome(400)."
	    <tr><td class='b n1'>450</td><td class='b n2'>".syndrome(450)."
	    <tr><td class='b n1'>500</td><td class='b n2'>".syndrome(500)."
	    <tr><td class='b n1'>600</td><td class='b n2'>".syndrome(600)."
	  </table>";
}
print "  $L[TRh]>$L[TDh]><a name='amps'>&Tags& (Amp tags)
".    "  $L[TR]>$L[FAQTD]>amp tags (or &tags&) are tags that allow you to put some of for your profile and RPG stats in a post. They can be incorporated into a layout or used once in a post.
".    "  <br>	<table class=table cellspacing=0>
	  <tr><td class='b h'>Tag</td><td class='b h'>Value
	  <tr><td class='b n1'>&postnum&	</td><td class='b n2'>Current post count
	  <tr><td class='b n1'>&numdays&	</td><td class='b n2'>Number of days since registration
	  <tr><td class='b n1'>&level&	</td><td class='b n2'>Level
	  <tr><td class='b n1'>&exp&		</td><td class='b n2'>EXP
	  <tr><td class='b n1'>&expdone&	</td><td class='b n2'>EXP done in the current level
	  <tr><td class='b n1'>&expnext&	</td><td class='b n2'>Amount of EXP left for next level
	  <tr><td class='b n1'>&exppct&	</td><td class='b n2'>Percentage of EXP done in the level
	  <tr><td class='b n1'>&exppct2&	</td><td class='b n2'>Percentage of EXP left in the level
	  <tr><td class='b n1'>&expgain&	</td><td class='b n2'>EXP gain per post
	  <tr><td class='b n1'>&expgaintime&</td><td class='b n2'>Seconds for 1 EXP when idle
	  <tr><td class='b n1'>&lvlexp&	</td><td class='b n2'>Total EXP amount needed for next level
	  <tr><td class='b n1'>&lvllen&	</td><td class='b n2'>EXP needed to go through the current level
	  <tr><td class='b n1'>&5000&		</td><td class='b n2'>Posts left until you have 5000
	  <tr><td class='b n1'>&20000&	</td><td class='b n2'>Posts left until you have 20000
	  <tr><td class='b n1'>&rank&		</td><td class='b n2'>Current rank, according to your amount of posts
	  <tr><td class='b n1'>&rankname&		</td><td class='b n2'>Text only current rank, according to your amount of posts 
	  <tr><td class='b n1'>&postrank&		</td><td class='b n2'>Shows your rank by number of posts 
	 </table>

"./*    "  $L[TRh]>$L[TDh]><a name='dispname'>Display Name System
".    "  $L[TR]>$L[FAQTD]>The display name system allows you to have your name displayed as something other than your account's name. For example \"Acmlm\" might decided he would like to have his name display as \"Milly\" for a while. With this system he would be allowed to do so without changing his actual login account name. It is forbidden to use this to flame or impersonate other members. Your real login name will be visible on your profile. Misuse of this feature will result in blocking of your ability to use it, and possibly further action if warranted. **Feature not tested. Currently not public**

".*/    "  $L[TRh]>$L[TDh]><a name='avatar'>What are avatars & mood avatars?
".    "  $L[TR]>$L[FAQTD]>Avatars are a form of display picture which appears beside your posts and in your profile. Likewise, a mood avatar allows you to display a different picture as opposed to the one specified in your profile.

".    "  $L[TRh]>$L[TDh]><a name='private'>Are private messages supported?
".    "  $L[TR]>$L[FAQTD]>Yes. Your private message inbox is represented by an envelope icon which is highlighted green when you have unread messages. Likewise, you may send a user a message from here, or alternatively use \"Send Private Message\" from the user's profile.

".    "  $L[TRh]>$L[TDh]><a name='search'>Search Feature
".    "  $L[TR]>$L[FAQTD]>The search feature is used to search the forum posts and threads for whatever you may be looking for. It has the ability to be filtered by forum and user it was posted by.

".    "  $L[TRh]>$L[TDh]><a name='calendar'>What is the calendar for?
".    "  $L[TR]>$L[FAQTD]>The calendar lists user birthdays and special board events.

".    "  $L[TRh]>$L[TDh]><a name='usercols'>What do the username colours mean?</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      They reflect the gender setting and group of the user.<table>
".$nctable."      </table>
".    "</table>";
pagefooter();

?>
