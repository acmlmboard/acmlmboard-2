<?php
include('lib/common.php');
include('lib/diff/Diff.php');
include('lib/diff/Diff/Renderer/inline.php');

//Smilies List
$smilieslist = $sql->query("SELECT * FROM `acmlmboard`.`smilies`");
$numsmilies = $sql->resultq("SELECT COUNT(*) FROM `acmlmboard`.`smilies`");
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

$L[FAQTD]="$L[TD1] style='padding:10px!important;'";

print "$L[TBL1]>
".    "  $L[TRh]>
".    "    $L[TDh]>FAQ</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      <a href=#disclaimer>General Disclaimer</a><br>
".    "      <br>
".    "      <a href=#whyreg>Why should I register an account on the board?<br>
".    "      <a href=#acceptable>What is the standard of acceptable behaviour on the board?<br>
".    "      <a href=#ranks>What are the user ranks?<br>
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
".    "  $L[TRh]>
".    "    $L[TDh]><a name=acceptable>What is the standard of acceptable behaviour on the board?</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      First off, do not assume that our rules are the same as those of other bulletin boards; just because spammy posts might be acceptable on another forum, they are not necessarily so here.<br><br>However, most of the rules are not that hard to follow, and as long as you keep them in mind, you should not run into trouble.<br><br><b>DO:</b>
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
".    "    $L[TDh]><a name='ranks'>What are the user ranks?</td>

".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      Depending on the amount of posts you have made, you will be grouped into a certain rank within your set, with the ranks intended to convey a certain sense of progression. You can choose from one of several available ranksets or disable them altogether through editing your profile.
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
".    "      They reflect the gender setting and powerlevel of the user.<table>
".    "      <tr><td width='200' class='nc0x'><b>Banned<td width='200' class='nc1x'><b>Banned<td width='200' class='nc2x'><b>Banned
".    "      <tr><td width='200' class='nc00'><b>Normal, male<td width='200' class='nc10'><b>Normal, female<td width='200' class='nc20'><b>Normal, unspec.
".    "      <tr><td width='200' class='nc01'><b>Local moderator, male<td width='200' class='nc11'><b>Local moderator, female<td width='200' class='nc21'><b>Local moderator, unspec.
".    "      <tr><td width='200' class='nc02'><b>Full moderator, male<td width='200' class='nc12'><b>Full moderator, female<td width='200' class='nc22'><b>Full moderator, unspec.
".    "      <tr><td width='200' class='nc03'><b>Administrator, male<td width='200' class='nc13'><b>Administrator, female<td width='200' class='nc23'><b>Administrator, unspec.
".    "      </table>
".    "  $L[TRh]>
".    "    $L[TDh]>Release notes</td>
".    "  $L[TR]>
".    "    $L[FAQTD]>
".    "      The FAQ for board2 is, as of yet, incomplete. For a technically outdated, but more complete version, refer to the <a href=http://acmlm.kafuka.org/archive3/faq.php>Third Incarnation Archive FAQ</a>.<br>Last update as of <i>2008-09-18</i>.
".    "</table>";
pagefooter();

?>
