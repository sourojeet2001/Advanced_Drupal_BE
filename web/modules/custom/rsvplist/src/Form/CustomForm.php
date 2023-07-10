<?php

/**
 * @file
 * A form to collect RSVP data from users.
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\text\Plugin\migrate\field\d6\TextField;

/**
 * This is a config form used to take user input from admin only and stores the 
 * values on a CustomForm.settings.yml file.
 */
class CustomForm extends ConfigFormBase{

  /**
   * Setting Variables
   */
  Const CONFIGNAME = 'CustomForm.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
      return [static::CONFIGNAME];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::CONFIGNAME);
    $form['fullname'] = [
      '#type' => 'textfield', 
      '#title' => 'FullName',
      '#default_value' => $config->get('fullname'),
      '#description' => $this->t('The fullname of the user.'),
    ];
    $form['fullname_error'] = [
      '#type' => 'markup',
      '#markup' => "<div class='danger fullname_err'></div>",
    ];
    $form['phone'] = [
      '#type' => 'tel', 
      '#title' => 'Phone Number',
      '#default_value' => $config->get('phone'),
      '#description' => $this->t('The phone of the user.'),
    ];
    $form['phone_error'] = [
      '#type' => 'markup',
      '#markup' => "<div class='danger phone_err'></div>",
    ];
    $form['email'] = [
      '#type' => 'email', 
      '#title' => 'Email Address',
      '#default_value' => $config->get('email'),
      '#description' => $this->t('The email of the user.'),
    ];
    $form['email_error'] = [
      '#type' => 'markup',
      '#markup' => "<div class='danger email_err'></div>",
    ];
    $form['gender'] = [
      '#type' => 'radios',
      '#title' => $this->t('Gender'),
      '#options' => [
        'male' => $this->t('Male'),
        'female' => $this->t('Female'),
        'other' => $this->t('Other'),
      ]
    ];
    $form['actions'] = [
      '#type' => 'button',
      '#value' => $this->t('Save'),
      '#ajax' => [
        'callback' => '::validateForm'
      ],
    ];
    return $form;
  }

    /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    $email = $form_state->getValue('email');
    $mob = $form_state->getValue('phone');
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@(yahoo|gmail|outlook)\.com$/', $email)) {
      $ajax_response->addCommand(new HtmlCommand('.email_err', t('Invalid email address or unsupported domain.')));
    }
    // Check if the phone number starts with '+91' and has exactly 10 digits.
    if(!preg_match('/^\+91\d{10}$/', $mob)) {
      $ajax_response->addCommand(new HtmlCommand('.phone_err', t('The phone number is too short. Please enter a full phone number with country code.')));
    }
    return $ajax_response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config(static::CONFIGNAME);
    $config->delete();
    $config->set('fullname', $form_state->getValue('fullname'));
    $config->set('phone', $form_state->getValue('phone'));
    $config->set('email', $form_state->getValue('email'));
    $config->save();
  }
}