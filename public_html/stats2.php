<?php
include('lib/common.php');

pageheader('Graphs');

  print "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh]>
".      "      Posts per day (strong: 8-day average)
".      "    $L[TDh]>
".      "      Distribution of last 24 hours' new posts
".      "  $L[TR1]>
".      "    $L[TDc] width=960>
".      "      <img src=gfx/statsgraph.php>
".      "    $L[TD] style=text-align:right>
".      "      <img src=gfx/forumsgraph.php>
".      "$L[TBLend]";

pagefooter();

?>
