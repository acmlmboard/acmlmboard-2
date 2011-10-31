<?php 

function urlcreate($url,$query) {
  return $url.'?'.http_build_query($query);
}

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
//[KAWA] i LoVe AlL oF yOu MoThErFuCkErS :o)


// insanity ensues

function RenderForm($form) {
  if ($form) {
    $formp = '<form action=%s method=%s>%s</form>';
    $table = '<table cellspacing=0 class=c1>%s</table>';
    $row = '<tr>%s</tr>';
    $rowhead = '<tr class="h">%s</tr>';
    $cell = '<td class="b n2">%s</td>';
    $cellhead = '<td class="b h" colspan="2">%s</td>';
    $celltitle = '<td align="center" class="b n1">%s</td>';
    $cellaction = '<td class="b">%s</td>';
    $input = '<input id=%s name=%s type=%s %s />';
    $formout = '';

    if (isset($form['categories'])) {
      foreach ($form['categories'] as $catid => $cat) {

        $title = (isset($cat['title'])) ? $cat['title'] : '&nbsp;';
        $catout = sprintf($rowhead,sprintf($cellhead,$title));
        foreach ($cat['fields'] as $fieldid => $field) {
          $type = $field['type'];
          if ($type != 'submit') {
            $title = (isset($field['title'])) ? $field['title'].':' : '&nbsp;';
            $fieldout = sprintf($celltitle,$title);
          }
          else {
            $fieldout = sprintf($celltitle,'&nbsp;');
          }
          switch ($type) {
            case 'color':
              $size = 6; $length = 6;
              $valuestring = (isset($field['value'])) ?
                ' value="'.$field['value'].'" ' : '';
              $fieldout .= sprintf($cell,sprintf($input,$fieldid,$fieldid,
                'text',"size=$size maxlength=$length $valuestring"));
              break;
            case 'imgref':
              $size = 40;
              $length = 60;
              $valuestring = (isset($field['value'])) ?
                ' value="'.$field['value'].'" ' : '';
              $fieldout .= sprintf($cell,sprintf($input,$fieldid,$fieldid,
                'text',"size=$size maxlength=$length $valuestring"));
              break;
            case 'numeric':
            case 'text':
              $length = (isset($field['length'])) ? $field['length'] : 60;
              if (!isset($field['size']) && !isset($field['length'])) {
                $size = 40;
              }
              elseif (!isset($field['size'])) {
                $size = $length;
              }
              else {
                $size = $field['size'];
              }
              $valuestring = (isset($field['value'])) ?
                ' value="'.$field['value'].'" ' : '';
              $fieldout .= sprintf($cell,sprintf($input,$fieldid,$fieldid,
                'text',"size=$size maxlength=$length $valuestring"));

              break;
            case 'submit':
              $title = (isset($field['title'])) ? $field['title'] : 'Submit';
              $fieldout .= sprintf($cellaction,sprintf($input,$fieldid,$fieldid,
                'submit','class=submit value="'.$title.'"'));
              break;
            default:
              $fieldout .= sprintf($cell,'&nbsp;');
              break;            
          }
          $catout .= sprintf($row,$fieldout);
        }
        $formout .= $catout;
      }
    }

    $method = (isset($form['method'])) ? $form['method'] : 'POST';
    $action = (isset($form['action'])) ? $form['action'] : '#';
    $out = 
sprintf($formp,'"'.$action.'"','"'.$method.'"',sprintf($table,$formout));
    echo $out;
  }
}



?>
