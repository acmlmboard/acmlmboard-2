<?php
include('lib/common.php');

pageheader('Graphs');

  print "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"h\">
".      "    <td class=\"b h\">
".      "      Posts per day (strong: 8-day average)
".      "    <td class=\"b h\">
".      "      Distribution of last 24 hours' new posts
".      "  <tr class=\"n1\">
".      "    <td class=\"b\" align=\"center\" width=960>
".      "      <img src=gfx/statsgraph.php>
".      "    <td class=\"b\" style=text-align:right>
".      "      <img src=gfx/forumsgraph.php>
".      "</table>";

pagefooter();

?>
