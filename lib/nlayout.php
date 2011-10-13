<?php 

/** Our first step to sanity, brought to us by Kawa **
 *
 * function RenderTable(data, headers) 
 *
 * Renders (outputs) a table in HTML using `headers` for column definition
 * and `data` to fill cells with data.
 *
 * Return value: none
 *
 * Parameters:
 * `headers`
 * An associative array of column definitions:
 *    key                -> column key
 *    value['caption']   -> display text for the column header
 *    value['width']     -> (optional) specify a fixed width size (CSS width:)
 *    value['color']     -> (optional) color for the column data cells 
 *                          which corresponds to CSS '.n' classes)
 *    value['align']     -> (optional) CSS text-align: for the data cells
 *    value['hidden']    -> (optional) 
 *
 * `data`
 * An associative array of cell data values:
 *    key                -> column key (must match the header column key)
 *    value              -> cell value
 *
 */
function RenderTable($data, $headers)
{
  $zebra = 0;
  $cols = count($header);

  print "<table cellspacing=\"0\" class=\"c1\">\n";
  print "\t<tr class=\"h\">\n";
  foreach($headers as $headerID => $headerCell)
  {
    if($headerCell['hidden'])
      continue;

    if(isset($headerCell['width']))
      $width = " style=\"width: ".$headerCell['width']."\"";
    else
      $width = "";

    print "\t\t<td class=\"b h\"".$width.">".$headerCell['caption']."</td>\n";
  }
  print "\t</tr>\n";
  foreach($data as $dataCell)
  {
    print "\t<tr>\n";
    foreach($dataCell as $id => $value)
    {
      if($headers[$id]['hidden'])
        continue;

      $color = $zebra + 1;
      $align = "";
      if(isset($headers[$id]['color']))
        $color = $headers[$id]['color'];
      if(isset($headers[$id]['align']))
        $align = " style=\"text-align: ".$headers[$id]['align']."\"";
      print "\t\t<td class=\"b n".$color."\"".$align.">".$value."</td>\n";
    }
    print "\t</tr>\n";
    $zebra = ($zebra + 1) % 2;
  }
  print "</table>\n";
}

?>
