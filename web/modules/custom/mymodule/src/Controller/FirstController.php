<?php

/**
 * @file
 * Generates markup  to be displayed. Functionality in this controller is wired
 * to Drupal in mymodule.routing.yml.
 */

namespace Drupal\mymodule\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserInterface;

class FirstController extends ControllerBase {
  public function simpleContent() {
    return [
      '#type' => 'markup',
      '#markup' => t(string: 'Hello Sourojeet'),
    ];
  }

  public function variableContent($name1, $name2) {
    return [
      '#type' => 'markup',
      '#markup' => t(string: 'Hello @name1 and @name2',
        args: ['@name1' => $name1, '@name2' => $name2]),
    ];
  }

  
  // public $user = \Drupal::currentUser();
  public function hello() {
    $user = \Drupal::currentUser()->id();
    if($user) {
      $cacheTags = ['user:' . \Drupal::currentUser()->id()];
      return [
        '#type' =>'markup',
        '#cache' => [
          'tags' => $cacheTags,
        ],
        '#markup' => t(string: 'Hello ' . \Drupal::currentUser()->getAccountName()),
      ];
    }
    else {
      return [
        '#type' =>'markup',
        '#markup' => t(string: 'Hello User'),
      ];
    }
  }

  public function test() {
    \Drupal::messenger()->addMessage(t("Hello Sourojeet Test Successful"));
  }
}