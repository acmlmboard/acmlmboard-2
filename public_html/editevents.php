<?php
//Uses editsprites.php as template

require("lib/common.php");

  $r = request_variables(array('id','action','act'));
  $pagebar = array();
  checknumeric($r['id']);

  if(!has_perm('edit-calendar-events')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

  pageheader("Edit Events");
  
  $user = array();
  $quser = $sql->query("SELECT `id`, `name` FROM `users`ORDER BY `id`");
  
  while ($alluserquery= $sql->fetch($quser))
  { 
    $user[$alluserquery['id']]= $alluserquery['name'];
  
  }
  $id = $r['id'];

  if ($r['action'] == "del") {
    unset($r['action']);
    if ($id > 0) {
        $event=$sql->fetchp('SELECT * FROM events WHERE id=?',array($id));
        if (!$event) $pagebar['message'] = "Unable to delete event: invalid event ID.";
     else if ($sql->prepare('DELETE FROM events WHERE id=?',array($id))) {
      $pagebar['message'] = "Event successfully deleted.";
 }
else {
 $pagebar['message'] = "Unable to delete event.";
}
    }
  }

  if(empty($r['action'])) {

$headers = array
(
	"id" => array //Entry key is used in $data to bind fields
	(
		"caption" => "#",
		"width" => "32px",
		"align" => "center",
		"color" => 1
	),
	"event_title" => array("caption"=>"Title", "align"=>"center", "color"=>2),
	"user" => array("caption"=>"User", "align"=>"center", "color"=>1),
	"private" => array("caption"=>"Private", "align"=>"center", "color"=>2),
	"month" => array("caption"=>"Month", "align"=>"center", "color"=>1),
	"day" => array("caption"=>"Day", "align"=>"center", "color"=>2),
	"year" => array("caption"=>"Year", "align"=>"center", "color"=>1),
  "edit" => array("caption"=>"Actions","align"=>"center", "color"=>2),
);

$data = array();
$eventReq = $sql->query("SELECT * FROM events ORDER BY id ASC");
while($event = $sql->fetch($eventReq))
{
$eventuser=$sql->fetchq("SELECT * FROM `users` WHERE `id` = '$event[user]'");
$actions = array(
  array('title' => 'Edit','href' => 
'editevents.php?action=edit&id='.$event['id']),
  array('title' => 'Delete','href' => 
'editevents.php?action=del&id='.$event['id'], 
confirm => true),
);
		
$data[] = array
		(
			"id" => $event['id'],
			"event_title" => $event['event_title'],
			"user" => userlink($eventuser),
			"private" => $event['private'],
			"month" => $event['month'],
			"day" => $event['day'],
			"year" => $event['year'],
      "edit" => RenderActions($actions,1),
		);
}
$pagebar['title'] = 'Edit Events';
$pagebar['actions'] = array(
    array('title' => 'New Event','href' => 'editevents.php?action=new'),
);
RenderPageBar($pagebar);
RenderTable($data, $headers);


}
elseif ($r['action']=="edit" || $r['action']=="new") {
if (!empty($r['act'])) {
      $e =
request_variables(array('month','day','year','user','private','event_title'));
$e['user']=$_POST[user];


if ($r['action']=="edit" && $id > 0) {

if(      $sql->prepare('UPDATE events SET 
month=?,day=?,year=?,user=?,private=?,event_title=? WHERE id=?;', array(
$e['month'],
$e['day'],
$e['year'],
$e['user'],
$e['private'],
$e['event_title'],
$id,
)
)){
      $pagebar['message'] = "Event successfully updated.";

}
else {
 $pagebar['message'] = "Unable to update event.";

}
}

elseif ($r['action']=="new"){
if (      $sql->prepare('INSERT INTO events SET
month=?,day=?,year=?,user=?,private=?,event_title=? ;', array(
$e['month'],
$e['day'],
$e['year'],
$e['user'],
$e['private'],
$e['event_title'],
)
)) {
$id = $sql->insertid();
$r['action'] = "edit";
      $pagebar['message'] = "Event successfully created.";
}
else {
 $pagebar['message'] = "Unable to create event.";
}
}
}
$pagebar['breadcrumb'] = array(
    array('title' => 'Edit Events','href' => 'editevents.php'),
    );


if ($id > 0) {
    $t=$sql->fetchp('SELECT * FROM events WHERE id=?',array($id));
  if (!$t) { noticemsg("Error", "Invalid event ID"); pagefooter(); die();
  } else {
$pagebar['title'] = $t['event_title'];
$pagebar['actions'] = array(
    array('title' => 'Delete Event','href' => 
'editevents.php?action=del&id='.$id, 
'confirm' 
=> 
true),
);
  }

}
else {
$pagebar['title'] = 'New Event';
$t = array(
  'id' => 0,
  'event_title' => '',
  'user' => 1,
  'private' => '0',
  'month' => '',
  'day' => '',
  'year' => '',  
);
}
RenderPageBar($pagebar);
$form = array(
  'action' =>
    urlcreate('editevents.php', array(
      'action' => $r['action'],
      'id' => $t['id'],
    )
    ),
  'method' => 'POST',
  'categories' => array(
    'metadata' => array(
      'title' => 'Event Metadata',
      'fields' => array(
        'event_title' => array(
          'title' => 'Title',
          'type' => 'text',
          'length' => 60,
          'size' => 40,
'value' => $t['event_title'],
        ),
          'user' => array(
          'title' => 'User',
          'type' => 'dropdown',
          'choices' => $user,
'value' => $t['user'],    
	),
        'private' => array(
          'title' => 'Private',
          'type' => 'dropdown',
          'choices' => array(
              '0' => '0',
              '1' => '1',
              ),
'value' => $t['private'],
        ),
        'month' => array(
          'title' => 'Month',
          'type' => 'numeric',
          'length' => 2,
'value' => $t['month'],
        ),
        'day' => array(
          'title' => 'Day',
          'type' => 'numeric',
          'length' => 2,
'value' => $t['day'],
        ),
          'year' => array(
          'title' => 'Year',
          'type' => 'numeric',
          'length' => 4,
'value' => $t['year'],
        ),
      ),
    ),
    'actions' => array(
      'fields' => array(
        'act' => array(
          'title' => ($id>0)?'Update metadata':'Create event',
          'type' => 'submit',
        ),
      ),
    ),
  ),
);

RenderForm($form);

}


pagefooter();

?>