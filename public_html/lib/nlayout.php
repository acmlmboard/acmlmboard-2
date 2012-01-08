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

function HTMLAttribEncode($string) {
 $pass1 = htmlentities($string, ENT_QUOTES);
 return "'$pass1'";
}

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
    $select = '<select id=%s name=%s>%s</select>';
    $option = '<option value=%s %s>%s</option>';
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
                ' value='.HTMLAttribEncode($field['value']).' ' : '';
              $fieldout .= sprintf($cell,sprintf($input,$fieldid,$fieldid,
                'text',"size=$size maxlength=$length $valuestring"));
              break;
            case 'imgref':
              $size = 40;
              $length = 60;
              $valuestring = (isset($field['value'])) ?
                ' value='.HTMLAttribEncode($field['value']).' ' : '';
              
$fieldout 
.= sprintf($cell,sprintf($input,$fieldid,$fieldid,
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
                ' value='.HTMLAttribEncode($field['value']).' ' : '';
              
$fieldout 
.= sprintf($cell,sprintf($input,$fieldid,$fieldid,
                'text',"size=$size maxlength=$length $valuestring"));

              break;
            case 'dropdown':
              $optout = '';
              foreach($field['choices'] as $choiceid => $choice) {
                $selected = ($field['value'] == $choiceid) ? ' 
selected="selected" ' : '';
                $optout .= 
sprintf($option,HTMLAttribEncode($choiceid),$selected,$choice);
              }
              $fieldout .= 
sprintf($cell,sprintf($select,$fieldid,$fieldid,$optout));
              
              break;              
            case 'submit':
              $title = (isset($field['title'])) ? $field['title'] : 'Submit';
              $fieldout .= sprintf($cellaction,sprintf($input,$fieldid,$fieldid,
                'submit','class=submit value='.HTMLAttribEncode($title)));
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

function RenderActions($actions,$ret = false) {
  $out = '';
  $i = 0;
  foreach ($actions as $action) {
    if ($action['confirm'] == true) {
      $href = "javascript:if(confirm('Are you sure you want to 
".$action['title']."?')){window.location.href='".$action['href']."';} else 
{void('');};";
    }
    else {
      $href = $action['href'];
    }
    if ($i++) $out.= ' | ';
    $out .= sprintf('<a 
href=%s>%s</a>',HTMLAttribEncode($href),$action['title']);
  }
if ($ret) return $out;
else echo $out;
}

function RenderBreadcrumb($breadcrumb) {
  foreach ($breadcrumb as $action) {
    echo sprintf('<a href=%s>%s</a> - 
',HTMLAttribEncode($action['href']),$action['title']);
  }
}

function RenderPageBar($pagebar) {
  echo "<table cellspacing=0 width=100%>";
  echo "<td class=nb>";
  if (!empty($pagebar['breadcrumb'])) RenderBreadcrumb($pagebar['breadcrumb']);
  echo $pagebar['title'];
  echo "</td><td align=right class=nb>";
  if (!empty($pagebar['actions'])) RenderActions($pagebar['actions']);
  else echo "&nbsp;";
  echo "</td></table><br/>";
  if (!empty($pagebar['message'])) {
    echo "<table cellspacing=0 width=100% class=c1><tr><td class='center'>";
    echo $pagebar['message'];
    echo "</td></tr></table><br/>";
  }
}
  function setfield($field){
    return "$field='$_POST[$field]'";
  }

  function catheader($title){
    global $L;
    return "  $L[TRh]>
".         "    $L[TDh] colspan=2>$title</td>";
  }

  function fieldrow($title,$input){
    global $L;
    return "  $L[TR]>
".         "    $L[TD1c]>$title:</td>
".         "    $L[TD2]>$input</td>";
  }

  function fieldinput($avatarsize,$max,$field){
    global $L,$user;
    return "$L[INPt]=$field size=$avatarsize maxlength=$max value=\"".str_replace("\"", "&quot;", $user[$field])."\">";
//  return "$L[INPt]=$field size=$avatarsize maxlength=$max value=\"".htmlval($loguser[$field])."\">";
  }

  function fieldinputrpg($avatarsize,$max,$field){
    global $L,$userrpg;
    return "$L[INPt]=$field size=$avatarsize maxlength=$max value=\"".str_replace("\"", "&quot;", $userrpg[$field])."\">";
//  return "$L[INPt]=$field size=$avatarsize maxlength=$max value=\"".htmlval($loguser[$field])."\">";
  }

  function fieldtext($rows,$cols,$field){
    global $L,$user;
    return "$L[TXTa]=$field rows=$rows cols=$cols>".htmlval($user[$field]).'</textarea>';
//  return "$L[TXTa]=$field rows=$rows cols=$cols>".htmlval($loguser[$field]).'</textarea>';
  }

  function fieldoption($field,$checked,$choices){
    global $L;
    $text='';
    $sel[$checked]=' checked=1';
    //[KAWA] Added <label> so the text is clickable.
    foreach($choices as $key=>$val)
      $text.="
".           "      <label>$L[INPr]=$field value=$key$sel[$key]>$val &nbsp;</label>";
    return "$text
".         "    ";
  }

// 2/22/2007 xkeeper - takes $choices (array with "value" and "name")
  function fieldselect($field,$checked,$choices){
    global $L;
    $text="
".        "$L[SEL]=$field>";
    $sel[$checked]=' selected';
    foreach($choices as $key=>$val)
      $text.="
".           "      $L[OPT]=\"$key\"$sel[$key]>$val</option>";
    return "$text
".         "    ";
  }

  function itemselect($field,$current,$cat) {
    global $sql, $L;

    $viewhidden = 0;

    if (isadmin())
      $viewhidden = 1;

    $items = $sql->query("SELECT * FROM items WHERE `cat` = 0 UNION SELECT * FROM items WHERE `cat` = $cat AND `hidden` <= $viewhidden");

    $text="
".        "$L[SEL]=$field>";

    while ($item = $sql->fetch($items)) {
      $text.="
".           "      $L[OPT]=\"$item[id]\"";
      if ($current == $item['id'])
        $text.=" selected";

      $text.="> $item[name]</option>";
    }
    return "$text    ";
  }

  function themelist() {
    global $sql, $loguser;

    $t = $sql -> query("SELECT `theme`, COUNT(*) AS 'count' FROM `users` GROUP BY `theme`");
    while ($x = $sql -> fetch($t)) $themeuser[$x['theme']] = intval($x['count']);

    $themes = unserialize(file_get_contents("themes_serial.txt"));
    $themelist = array();
    foreach($themes as $t) {
      $themeusers = isset($themeuser[$t[1]]) ? $themeuser[$t[1]] : 0;
      $themelist[$t[1]] = $t[0] . ($themeusers ? (" [$themeusers user".($themeusers == 1 ? "" : "s")."]") : "");
    }

    return $themelist;
  }

  function ranklist() {
    global $sql, $loguser;
    $r=$sql->query("SELECT * FROM ranksets ORDER BY id ASC");
    while($d=$sql->fetch($r)) $rlist[$d[id]]=$d[name];

    return $rlist;
  }
?>