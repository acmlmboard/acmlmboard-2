<?php

function generate_sprite_hash($userid,$spriteid) {
  global $spritesalt;
  return md5($spritesalt.$userid.$spriteid.$spritesalt);
}

?>