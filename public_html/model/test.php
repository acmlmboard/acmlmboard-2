<?php
	// In case you were expecting me to make meaningful comments in this file, here's a comment for you: FUCK YOU!
	// 		With unyielding sincerity, Shroomy
	
	require 'api.php';
	
	$post = new Post( 'test post!!!' );
	
	echo $post;
	
	echo '<br><br>';
	
	
	$post->post_body = 'changed the post body!';
	
	echo $post;
	
	echo '<br><br>';
?>