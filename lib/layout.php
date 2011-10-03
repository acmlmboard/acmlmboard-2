<?php
  $L[TR ] ='<tr class=""';
  $L[TRh] ='<tr class=h';
  $L[TRg] ='<tr class=c';
  $L[TR1] ='<tr class=n1';
  $L[TR2] ='<tr class=n2';
  $L[TR3] ='<tr class=n3';
  $L[TD ] ='<td class=b';
  $L[TDh] ='<td class="b h"';
  $L[TDg] ='<td class="b c"';
  $L[TD1] ='<td class="b n1"';
  $L[TD2] ='<td class="b n2"';
  $L[TD3] ='<td class="b n3"';
  $L[TDn] ='<td class=nb';

  foreach($L as $key=>$val){
    $L[$key.'o']=str_replace('"','',$L[$key]);
    $L[$key.'o']=str_replace('=',"='",$L[$key.'o']);
    $L[$key.'s']=$L[$key.'o']." sfont'";
  }

  foreach($L as $key=>$val){
    $L[$key.'l']=str_replace(' cl',' align=left cl',$L[$key]);
    $L[$key.'r']=str_replace(' cl',' align=right cl' ,$L[$key]);
    $L[$key.'c']=str_replace(' cl',' align=center cl',$L[$key]);
  }

  $L[TR] ='<tr';

  $L[TBL] ='<table cellspacing=0';
  $L[TBL1]='<table cellspacing=0 class=c1';
  $L[TBL2]='<table cellspacing=0 class=c2';
  $L[TBLend]='</table>';

  $L[INPt]='<input type=text name';
  $L[INPp]='<input type=password name';
  $L[INPh]='<input type=hidden name';
  $L[INPs]='<input type=submit class=submit name';
  $L[BTTn]='<button type=button class=submit id'; // 
  $L[INPr]='<input type=radio class=radio name';
  $L[INPc]='<input type=checkbox name';
  $L[INPl]='<select name'; // 2009-07 Sukasa: I needed it for the mood selector
  $L[TXTa]='<textarea wrap=virtual name';

  $L[SEL] ='<select name';
  $L[OPT] ='<option value';

  $signsep[0]='<br><br>--------------------<br>';
  $signsep[1]='<br><br>____________________<br>';
  $signsep[0]='<hr>';
?>
