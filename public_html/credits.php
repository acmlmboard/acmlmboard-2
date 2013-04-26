<?php
    include("lib/common.php");
    pageheader("Acmlmboard Credits");
  
    //[Scrydan] Basic credits page. The footer was getting too long, haha.
    //There might be some work done on this later but you get the idea. A few people weren't on the list and I added them.
    //Feel free to make this more up to date.
    
    $programmers = array
     (
      "<span style=\"color: #EE4444; font-weight:bold;\">Acmlm</span>"       => "The man the board is named after. He built the core of the board (and much more that would take a long time to detail.)",
      "<span style=\"color: #EE4444; font-weight:bold;\">Emuz</span>"        => "He was there from day one with Acmlm. Working on misc things.<br><br>
                        It was on the beginning of the year 2012 when he restarted interest in developement of AB 2.X as 2.5.1.<br>
                        Without him and Bouche, Acmlmboard 2.X may still be dormant.",
      "<span style=\"color: #FFEA95; font-weight:bold;\">blackhole89</span>" => "Worked along with Xkeeper to bring the board from an alpha to production. Created many of the core systems missing from the original provided by Acmlm.",
      "<span style=\"color: #F0C413; font-weight:bold;\">Xkeeper</span>"     => "Xkeeper was the person who convinced Acmlm to work on AB2 after it laid dormant for years. Along with blackhole89 they contributed a majority of the code from 2.0 to 2.1.",
      "<span style=\"color: #FFEA95; font-weight:bold;\">Sukasa</span>"      => "Implemented mood avatars, forums and items editing, and a Text to IMG rendering engine.",
      "<span style=\"color: #AFFABE; font-weight:bold;\">Kawa</span>"        => "Implemented Display Name, the inital root power level, and the current theme management system",
      "<span style=\"color: #FFEA95; font-weight:bold;\">DJ Bouche</span>"   => "Created the permissions and group system, warp whistle reply, and many other things",
      "<span style=\"color: #C762F2; font-weight:bold;\">knuck</span>"       => "Implemented implemented post radar, many small fixes",
      "<span style=\"color: #FFEA95; font-weight:bold;\">Gywall</span>"      => "Revised mood avatar system, sprites random method, many many fixes",
      "<span style=\"color: #33EDCB; font-weight:bold;\">Mega-Mario</span>"  => "Securty officer, permissons editors, globals fixing, many misc issues",
      "<span style=\"color: #33EDCB; font-weight:bold;\">Scrydan</span>"     => "Implemented ranks.php, core cleanup, many other small things (like this page here).<br /><i>Most of his work on the 2.6 branch. He implmented many major changes to it.</i> ",
     );


    $contributors = array
     (
      "<span style=\"color: #F0C413; font-weight:bold;\">KP9000</span>"       => "Provided many themes for AB2 over the last few years... (TBC)",
      "<span style=\"color: #C53A9E; font-weight:bold;\">Ailure</span>"        => "",
      "<span style=\"color: #F185C9; font-weight:bold;\">Nina</span>"        => "Converted all the AB1 themes to AB2.",
      "<span style=\"color: #33EDCB; font-weight:bold;\">Shroomy</span>"     => "Drafted an OOP DB class for AB2. This class not in use.",
     );

    $gitcontributors = array
     (
      "Acmlm"       => "Insert something here.",
      "Emuz"        => "Here as well!",
      "Excalibur"     => "My legend starts in the 12 century..",
     );
     
   foreach ($programmers as $programmer => $contribution)
    {
     $programmerlist .= "
           $L[TR]>
              $L[TD1]>$programmer</td>$L[TD1]>$contribution</td>
           </tr>";
    }
   foreach ($gitcontributors as $gitcontributor => $gitcontribution)
    {
     $gitcontributorlist .= "
           $L[TR]>
              $L[TD1]>$gitcontributor</td>$L[TD1]>$gitcontribution</td>
           </tr>";
    }
   foreach ($contributors as $contributor => $contribution)
    {
     $contributorlist .= "
           $L[TR]>
              $L[TD1]>$contributor</td>$L[TD1]>$contribution</td>
           </tr>";
    }
    unset($programmer, $gitcontributor, $contributor, $contribution);
    
    print "
           $L[TBL1]>
           $L[TRh]>$L[TDh]>Board Credits</td></tr>
           $L[TR]>$L[TD1]>&nbsp;Acmlmboard 2 started back in 2005 by Acmlm as a cleaner rewrite for the original AB1.
           The major new feature he wished to implement is a fully functional battle RPG based on the system be built for the first board.
           However shortly after starting he put AB2 aside, instead some of the cosmetic features eventually ended up in AB1, but the rest of the code would stay dormant until 2007.
           In 2007 AB2 launched with AB 2.0... <h1>This is a WIP.</h1></td></tr>
           </table> <!-- End the table here so you don't have to deal with colspan. -->
           $L[TBL1]>
           $L[TRg]> <!-- TRg is a secondary heading. -->
           $L[TD]>Programmer</td>$L[TD]>Core Contributors</td>
           </tr>
           $programmerlist
           $L[TRg]> <!-- This space is for those who contributed in other ways. -->
           $L[TD]>Name</td>$L[TD]>Contributions</td>
           </tr>
           $contributorlist
           $L[TRg]> <!-- This space is for those who contributed in other ways. -->
           <!-- $L[TD]>Name</td>$L[TD]>Contributions from Git</td>
           </tr>
           $gitcontributorlist -->
           </table><br>
           $L[TBL1]>
           $L[TRh]>$L[TDh]>Additional Thanks</td></tr>
           $L[TR]>$L[TD1]>Thanks to everyone who's supported us during our revival of AB2. <br>
           Thank you to everyone who helped with the start of Kafuka, without your support we wouldn't have got this far.<br>
           Finally, thanks goes to Acmlm for allowing us to continue working on AB2, as well as Scrydan and DJ Bouche who spent
           more single focus time than anyone else for 2.5, and 2.6.
           <hr>3rd Party tools: <a href=\"http://code.google.com/p/google-code-prettify/\">Prettify</a></td></tr>
           </table>";
  
    pagefooter();
 ?>