<?php

/**
 * @file
 * A form to collect RSVP data from users.
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * This form is used to take userid as input and generate a one time login link 
 * for the users who are registered onto the site and not blocked. Its also
 * validating the user input using Ajax forms.
 */
class UserLoginForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId(){
    return 'rsvplist_email_form';
  }

  /**
   * {@inheritdoc}
   */ 
  public function buildForm(array $form, FormStateInterface $form_state) {
    //Attempt to get the fully loaded node object 
    $node = \Drupal::routeMatch()->getParameter('node');
    // Some pages may not be nodes, so $node will be null in that case.
    // If node is loaded then get the id.
    if( !is_null($node)) {
      $nid = $node->id();
    }
    else {
      // If node could not be found, default to 0.
      $nid = 0;
    }
    //Establish the form render array. It has an Email text field with a
    //submit button, and a hidden field with the node id.
    $form['uid'] = [
      '#type' => 'number',
      '#title' => t('User Id'),
      '#size' => 4,
      '#desc' => t('Enter the userid to get the login link'),
      '#required' => TRUE,
      '#suffix' => "<div class='danger err'></div>"
    ];
    $form['actions'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => '::validateAjax',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Generating OTTL...'),
        ], 
      ],
      '#suffix' => "<div class='link'></div>"
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateAjax(array &$form, FormStateInterface $form_state) {
    $ajax_res = new AjaxResponse();
    $ajax_res->addCommand(new CssCommand('.danger', ['color' => 'red']));
    $ajax_res->addCommand(new HtmlCommand('.err', t('')));
    $uid = $form_state->getValue('uid');
    if (!$uid) {
      $ajax_res->addCommand(new HtmlCommand('.err', t('Enter a valid User Id')));
    }
    else {
      $user = User::load($uid);
      if (!$user) {
        $ajax_res->addCommand(new HtmlCommand('.err', 'Invalid User ID.'));
      }
      elseif (user_is_blocked($user->getAccountName())) {
        $ajax_res->addCommand(new HtmlCommand('.err', 'User is blocked.'));
      }
      else {
        $link = user_pass_reset_url($user) . '/login';
        $ajax_res->addCommand(new HtmlCommand('.link', $link));
      }
    }
    return $ajax_res;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}