<?php 
/**
  * Function to define a view increment hook.
  *
  *   @param int $current_count
  *     The number of times the current user has viewd this node during this session.
  *
  *   @param \Drupal\node\NodeInterface $node
  *     The node being viewed.
  */
function hook_view_count($current_count, \Drupal\node\NodeInterface $node) {
  if($current_count == 1) {
    \Drupal::messenger()->addMessage(t('This is the first time you\'r visiting the node'));
  }
}