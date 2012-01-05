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







$L[FAQTD]="$L[TD1] style='padding:10px!important;'";

print "$L[TBL1]>
".    "  $L[TRh]>
".    "    $L[TDh]>FAQ</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      <a href=#disclaimer>General Disclaimer</a><br>
".    "      <br>
".    "      <a href=#whyreg>Why should I register an account on the board?<br>
".    "      <a href=#kafukaguideline>What are some basic guidelines for members? <br>
".    "      <a href=#acceptable>What is the standard of acceptable behaviour on the board?<br>
".    "      <a href=#spam>What is considered \"spam\" at the board?<br>
".    "      <a href=#newtoboard>I'm new, where should I start?<br>
".    "      <a href=#strike>What is the strike system? <br>
".    "      <a href=#escalation>What if you see something was handled improperly? <br>
".    "      <a href=#closedth>What can I do about my thread being closed/trashed/deleted? <br>
".    "      <a href=#banned>Help! I've been banned! What do I do now?<br>
".    "      <a href=#cookies>Are cookies used for this board?<br>
".    "      <a href=#html>Can HTML be used?<br>
".    "      <a href=#abcode>Is there some sort of replacement code for HTML?<br>
".    "      <a href=#layouts>Are there any rules for post layouts?<br>
".    "      <a href=#exp>What is Level and EXP, and how do I get more EXP?<br>
".    "      <a href=#stats>How do I add my stats to my posts?<br>
".    "      <a href=#syndromes>What are the \"syndromes\" and how do I get them?<br>
".    "      <a href=#kcs>What is the KCS?<br>
".    "      <a href=#sprites>What are sprites?<br>
".    "      <a href=#badge>What are badges?<br>
".    "      <a href=#ranks>What are the user ranks?<br>
".    "      <a href=#customr>How can I get a custom rank?<br>
".    "      <a href=#bestaff>How can I become a moderator or administrator?<br>
".    "      <a href=#annc>What are announcements and the Points of Required Attention?<br>
".    "      <a href=#album>Can you add me to the Photo Album?<br>
".    "      <a href=#nickchange>How can I change my username?<br>
".    "      <a href=#smilies>What smilies does the board have?<br>
".    "      <a href=#colours>What do the username colours mean?<br>
".    "    </td>
".    "</table><br>";

print "$L[TBL1]>
".    "  $L[TRh]>
".    "    $L[TDh]><a name=disclaimer></a>General Disclaimer</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      The site does not own, does not accept responsibility for and cannot be held responsible for statements made by members on the forum. This site is offered as-is to the user. Any statements made on the board may be altered or removed at the discretion of the staff. Furthermore, all users are expected to have read, understood, and agreed to this FAQ before posting.<br>
".    "      We do not sell, distribute or otherwise disclose member information like IP addresses to any third party. If you have questions about any information contained in this FAQ, please send a private message with your question to a moderator or administrator before posting.<br>
".    "      By viewing or making others aware of this site the user agrees to all terms of this document and to the final say of the administrative staff.</td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name=whyreg>Why should I register an account on the board?</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      By registering a user account, you will be able to post on the board as well as use several features only accessible through registering, such as the ability to mark forums as read and private messaging. Unregistered users have guest access to the board, meaning they can view threads but not reply to them.</td>
"."  $L[TRh]>
".    "    $L[TDh]><a name=kafukaguideline>What are some basic guidelines for members?</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      The major rules here are very simple. 
<ul><li>Respect your fellow posters. The board is the members who make up the community, we can only grow if we respect one another</li>
<li>Respect that others may not share your opinions, as we all have them. We will not agree on everything. Healthy debate makes life more interesting. Slamming people just causes bad feelings and more issues than an election</li>
<li>No personal attacks or insults. This is one of those zero tolerance ones. If you do it expect to get slapped upside the head.</li>
<li>No Trolling. Yup. It’s up to the staff to decide what is and isn’t. We’ll warn ya.. than we’ll hit you with a frying pan.</li> 
<li>Respect the Staff. The Staff are merely the caretakers of the community. They make sure everything runs.</li>  
<li>The Staff can and will act without prior warning if needed. Not we’ll do it that often, but we can and will if the need arises.</li>
<li>Report any staff issues you find. If you wish you can report them directly to Emuz or any of the system admins or Root Admins. Staff are not immune either as they are part of the community just as much as the regular members!</li></ul></td>

".    "  $L[TRh]>
".    "    $L[TDh]><a name=acceptable>What is the standard of acceptable behaviour on the board?</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      The following is the original rules of Acmlm's Board. Our rules are based of these rules and as such we've provided them for reference.<br />First off, do not assume that our rules are the same as those of other bulletin boards; just because spammy posts might be acceptable on another forum, they are not necessarily so here.<br><br>However, most of the rules are not that hard to follow, and as long as you keep them in mind, you should not run into trouble.<br><br><b>DO:</b>
".    "  <ul><li>Follow the rules the local moderators proclaim in their forums.
".    "  <li>LISTEN to the staff when they speak to you.  We don't do it for our own health.
".    "  <li>Use proper grammar and avoid '1337speak' and AOL speak (u/ur/y/ in place of words such as you/you're/why). It is a good way to gain and keep respect.
".    "  <li>Exercise common sense when posting.
".    "  <li>Exercise caution when posting threads about controversial topics such as religion to avoid unnecessarily insulting other board members</ul>
".    "  <b>DON'T:</b>
".    "  <ul><li>Spam.<li>Be a bigot. Hateful, discriminating or homophobic remarks are not welcome on this board. ('<i>DUDE THATS GAY STFU UR GAY</i>')
".    "  <li>Bump (post in) old or outdated threads, unless you have something meaningful to contribute; mere questions do not count as such outside of question threads
".    "  <li>Create multiple accounts unless explicitely authorized to do so by an administrator. Creation of secondary accounts to evade bans will result in a permanent banishment from the board.
".    "  <li>Ask any questions that could be answered using this FAQ or easily and commonly available sources.
".    "  <li>Stick around if you hate this board. Nobody is forcing you to.
".    "  <li>Act like the board is a chat room (post messages like '<i>HEY ACMLM</i>', '<i>I AGREE</i>', '<i>What's up?</i>', or any messages directed to individuals). This is what the private message function is for.
".    "  <li>FLAME, or purposefully try to be an ass to people.
".    "  <li>Ask to be a moderator, suck up or act like a moderator. It's one of the best ways NOT to get what you want.
".    "  <li>Backseat moderate.  We have staff for that.  At most, direct users to this FAQ.
".    "  <li>Threaten to hack the board. Chances are you can't. Besides, we have a few real hackers on staff who test the board constantly for any security problems.
".    "  <li>Post blatantly pornographic material in inappropriate situations where it might be frowned upon. In case of doubt, usage of NSFW tags in the thread title or on links is recommended.
".    "  <li>Post ROM/warez links on the board. You are free to exchange links with others through private messages, but do not post them out in the open.
".    "  <li>Join the board with the sole intent of advertising your site/service/etc.
".    "  <li>Force your opinion on others.  This is a discussion board, members are entitled to their own opinions.
".    "  <li>Respond to a spammer attack/obvious flame. If it's obvious that the post/thread in question will be deleted, don't post and make fun of the person or ask for the post/thread to be taken care of.</ul>
".    "  Naturally, exceptions to most of those rules could arise out of social context or similar; however, unless you have gained a deeper understanding of the forum community, you are on the safe side assuming that to not be the case. In case of any remaining doubts, common sense prevails.
".    "  $L[TRh]>
".    "    $L[TDh]><a name=spam>What is considered \"spam\" at the board?</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "    <b>Spam is making:</b>

<ul><li>A post that is off-topic, meaning that it has nothing to do with the original thread. </li>
<li>A post that is only a few words long and doesn't contribute anything meaningful to the discussion. </li>
<li>A thread that doesn't have any real meaning. </li>
<li>A thread with a poll that is pointless. </li>
<li>A thread that is an exact duplicate of a pre-existing, recent thread.</li></ul> 

If you feel that a thread is spammy, then PM or IM an admin and ask them to take a look at it. Don't ask for someone to take a look at it in the thread itself or lecture the poster on the dangers of spamming. </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name=newtoboard>I'm new, where should I start? </td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "   The best place for newcomers to start is the <a href=forum?id=1>General</a> Forum where you can introduce yourself to the board. If you have any questions not addressed in this FAQ, feel free to ask them in the <a href=forum?id=1>Help / Suggestions</a> forum.</td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='strike'>What is the strike system?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      In most cases, the staff at the board follows a three-strike system when it comes to punishments. For most minor offenses, an offending user will be warned about his/her conduct through a PM. If they continue to break the rules, they will be banned for 24 hours (possibly more if it was a serious offense, up to 72 hours). If the user continues to cause trouble, they will receive a second warning, possibly followed by another ban which could last anywhere from 72 hours to a week. Finally, if the user is still causing trouble after all these warnings, they will be permanently banned from the board.<br /><br />Harsher bans may be issued if a user racks up multiple warnings in a short amount of time or does something idiotic such as flooding the board or posting questionable material.<br /><br />Note that if you register another account to post with during a ban, you will be permanently banned on the spot. As well, if you think it's fun to constantly harass us on the board, keep in mind that we can and will contact your ISP and lodge a formal complaint. Many ISP's take harassment very seriously and may deactivate your account. </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='escalation'>What if you see something was handled improperly? </td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      If you experince a staff or poster issue and something needs to be examined by someone of higher status, we have setup rules for escalation. This will help everyone to know who they need to contact faster. This set of rules applies for most decisions when they have been questioned.
<ol><li>The local moderator has say on the areas they have their name attached to. They are free to moderate as they see fit as long as it follows the general staff guidelines of the board. </li>
<li>If an issue arises with a moderation action of a LM, it should be brought to the staff forum for short debate. Lacking that if two or more GMs, agree or an Admin, they can overturn the action. They should state the reason why it was overturned. (offending thread or in the staff forum)</li>
<li>If an issue arises with a moderation action of a GM, it should be brought to the staff forum for short debate. Lacking that if two Admins or one SysAdmin agrees they can overturn the action. They should state the reason why it was overturned. (offending thread or in the staff forum) </li>
<li>If an issues arises with something an Admin has performed it should be brought to the staff forum for short debate. Lacking that if three or more Admins (any), Two Sys Admins or a Root Admin agree, They should state the reason why it was overturned. (offending thread or in the staff forum) </li>
<li>If an issues arises with something an Admin has performed it should be brought to the staff forum for short debate. Lacking that if four or more Admins (any), three Sys Admins or a Root Admin agree, They should state the reason why it was overturned. (offending thread or in the staff forum) </li>
<li>The Buck stops at Emuz. He’ll have the final say if one is needed.</li></ol> </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='closedth'>What can I do about my thread being closed/trashed/deleted? </td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      First off, don't complain on the board about a thread being closed or deleted, and don't single out a certain mod or admin as being responsible. This will result in a ban. Learn from your mistakes and move on.<br/ >If you feel you need clarification on why the thread was closed or if you feel a mistake was made, contact a local mod in charge of the forum or an admin and politely explain the problem. </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='banned'>Help! I've been banned! What do I do now?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      First off, DO NOT create a new account to complain about your ban, as that will just result in a permanent ban from the board. If you feel that the ban was unfair or if you wish to know exactly why you were banned, PM an admin and calmly ask about it. Aside from that, the only thing you can do is wait for the ban to expire and make sure you know why it happened.</td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='cookies'>Are cookies used for this board?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      Yes, it uses cookies to store your login information. You can still use this board with cookies disabled, but you'll have to enter your username and password every time it's asked, and some features may not be available.</td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='html'>Can HTML be used?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      Yes, it can be used in posts, private messages, nearly everywhere except in things such as thread titles, usernames, etc.</td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='abcode'>Is there some sort of replacement code for HTML?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      Yes, but it's a bit limited. Here's what can be used: (most are case sensitive)<br>
	 [b]<b>Bold text</b>[/b]<br>
	 [i]<i>Italic text</i>[/i]<br>
	 [u]<u>Underlined text</u>[/u]<br>
	 [s]<s>Stroke text</s>[/s]<br>
	 [img]URL of image to display[/img]<br>
	 [svg]URL of a SVG image to display[/svg]<br>
	 [spoiler]Tag used to hide spoilers[/spoiler]<br>
	 [code]Shows code in a formatted block[/code]<br>
	 [url]URL of site or page to link to[/url] </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='layouts'>Are there any rules for post layouts?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "         There are a few simple rules which all users should follow in their layouts:<br />

<ul><li>No flashy backgrounds which make text difficult to read. This doesn't mean you can't have a nice background, but at least make sure that the area where the text is doesn't make a post hard to read. </li>
<li>No huge pictures or excessive amounts of GIFs. Huge pictures just make threads longer to load and having too many GIFs can slow down some computers. </li>
<li>No broken tables. If you're working with tables and feel that you might be messing up, check your post in your user profile. If the profile seems to be messed up, then the layout is as well. Broken tables can wreck havoc with threads. </li></ul>

If you have any questions about layouts in general or need help, visit the <a href=index.php?id=1>Modern Art forum.</a>  </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='exp'>What is Level and EXP, and how do I get more EXP?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      EXP is calculated from your amount of posts and how long it's been since you registered. Level is calculated from EXP. You gain increasing amounts of EXP by posting, and by being registered longer. </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='stats'>How do I add my stats to my posts?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      In a way similar to HTML and the markup replacements (described above), just put those where you want the numbers to be:<br>
	<center><table><td><table class=table cellspacing=0>
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
	 </table></table></center></td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='syndromes'>What are the \"syndromes\" and how do I get them? </td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      The syndromes are based on how many posts you have made in the last 24 hours. The amount of posts required for each syndrome is as follows:<br>
	  <center><table><td><table class=table cellspacing=0>
	    <tr><td class='b h'>Posts</td><td class='b h'>Syndrome
	    <tr><td class='b n1'> 75</td><td class='b n2'><x<br><i><font color=83F3A3>Affected by 'Reinfors Syndrome'</font></i>
	    <tr><td class='b n1'>100</td><td class='b n2'><x<br><i><font color=FFE323>Affected by 'Reinfors Syndrome' +</font></i>
	    <tr><td class='b n1'>150</td><td class='b n2'><x<br><i><font color=FF5353>Affected by 'Reinfors Syndrome' ++</font></i>
	    <tr><td class='b n1'>200</td><td class='b n2'><x<br><i><font color=CE53CE>Affected by 'Reinfors Syndrome' +++</font></i>
	    <tr><td class='b n1'>250</td><td class='b n2'><x<br><i><font color=8E83EE>Affected by 'Reinfors Syndrome' ++++</font></i>
	    <tr><td class='b n1'>300</td><td class='b n2'><x<br><i><font color=BBAAFF>Affected by 'Wooster Syndrome'!!</font></i>
	    <tr><td class='b n1'>350</td><td class='b n2'><x<br><i><font color=FFB0FF>Affected by 'Wooster Syndrome' +!!</font></i>
	    <tr><td class='b n1'>400</td><td class='b n2'><x<br><i><font color=FFB070>Affected by 'Wooster Syndrome' ++!!</font></i>
	    <tr><td class='b n1'>450</td><td class='b n2'><x<br><i><font color=C8C0B8>Affected by 'Wooster Syndrome' +++!!</font></i>
	    <tr><td class='b n1'>500</td><td class='b n2'><x<br><i><font color=A0A0A0>Affected by 'Wooster Syndrome' ++++!!</font></i>
	    <tr><td class='b n1'>600</td><td class='b n2'><x<br><i><font color=C762F2>Affected by 'Anya Syndrome'!!!</font></i>
	  </table></table></center>
	  Any other \"syndromes\" you may see such as \"Cute Kitten Syndrome ++\" are not syndromes; they are simply a custom title that someone else has decided to take.<br> Don't forget that spamming in an attempt to gain these syndromes will result in a warning or a ban. The only right way to gain a syndrome is by making clear, non-spammy posts.</td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='kcs'>What is the KCS?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      The KCS stands for Kafuka Championship Series (Like the ACS from Acmlm's Board), and is a posting \"competition\". Each day the top ten posters (and ties) on the board are given points. The top poster each day receives 10 points and the 10th placed poster receives 1. At around midnight Eastern time each day, the rankings are compiled and posted in the Craziness Domain. \"Awards\" are given out at the end of each month. Note that the KCS is just for fun and people really shouldn't be posting just to rank in it. As well, the points mean absolutely nothing in the grand scheme of things. You can't exchange them for anything. That being said, the KCS has been a success on the board and thankfully we haven't had to ban too many people for spamming to get a top spot. Let's hope it stays that way.<br /><i>Note: KCS may not start right away. Check <a href=forum.php?id=1>General Chat</a> for details</i></td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='sprites'>What are sprites?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      Sprite system shows random sprites from many classic games, and media. When you see one and click on it with your mouse you collect it. The sprites are recorded in your record. Some sprites appear more than others like boss creatures. Try to <s>catch</s> collect them all! </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='badge'>What are badges?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "    Most badges are given out by the for reaching various milestones on the board. Some can be bought using coins, and others can only give directly by a staff member. They are displayed in your profile for everyone to see.<br /><i>Note: Badges are currently not implemented but are coming soon</i>   </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='ranks'>What are the user ranks?</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      Depending on the amount of posts you have made, you will be grouped into a certain rank within your set, with the ranks intended to convey a certain sense of progression. You can choose from one of several available ranksets or disable them altogether through editing your profile. </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='customr'>How can I get a custom rank?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      You can get one under one of those conditions:<ul>
<li>Be a moderator or administrator</li>
<li>Have at least 1200 posts</li>
<li>Have at least 800 posts, and have been registered for at least 200 days</li></ul>
There may be a few rare exceptions, but asking for a custom title before having the requirements for it won't get you one.</td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='bestaff'>How can I become a moderator or administrator?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      If us administrators trust you enough for this. Don't ask us, we may ask you if we ever feel you worthy of being promoted. Being a good and regular member helps, while asking for this doesn't. It also depends whether we feel a need to promote more people, which isn't so often the case.  </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='annc'>What are announcements and the Points of Required Attention?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      Announcements, and the PorA are general messages posted by administrators only. Everybody can view them, but not reply to them.<br/><i>Note: Annoucments are currently not implemented but are coming soon</i>  </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='album'>Can you add me to the Photo Album?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      <s>Yes, just send or post a picture of yourself and I may add it. Only actual photographs are accepted.</s><br /><i>Note: The Photo Album are currently not implemented but is coming soon</i> </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='nickchange'>How can I change my username?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      You can't change it yourself, only administrators can, but you can ask one of them for a name change. </td>
".    "  $L[TRh]>
".    "    $L[TDh]><a name='smilies'>What smilies does the board have?</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      The board will automatically swap in the following $numsmilies smilies:<br />
".    "      $smilietext
".    "  $L[TRh]>
".    "    $L[TDh]><a name='colours'>What do the username colours mean?</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      They reflect the gender setting and group of the user.<table>
".$nctable."      </table>
".    "  $L[TRh]>
".    "    $L[TDh]>Release notes</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      Acmlmboard II is currently under active development. There may be new undocumented features, and various bugs. While this board is running a 'release' version of the code, it is still very much beta software. ABII is developed on a test server, than it is tested, and when it reaches a mature point it is moved to the live server (which is 'Kafuka'). Still we are only human of if you note any issues with the board software please report it on Kafuka's Bug report forum.<br /><br />FAQ Last Updated: <i>2011-12-28</i>.
".    "</table>";
pagefooter();

?>
