<?php

/**
 * @file
 * A form to collect RSVP data from users.
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * This is multistep form consisting of 3 pages, taking user input of firstname,
 * lastname and email in consecutive pages.
 */
class MultiForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_multistep_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    if($form_state->has('cpage') && $form_state->get('cpage') == 2) {
      return $this->secondForm($form, $form_state);
    }

    if($form_state->has('cpage') && $form_state->get('cpage') == 3) {
      return $this->thirdForm($form, $form_state);
    }

    $form_state->set('cpage', 1);
    $form['firstname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
    ];

    $form['firstnext'] = [
      '#type' => 'submit',
      '#value' => 'Next',
      '#submit' => ['::firstNext'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  
  public function firstNext(array &$form, FormStateInterface $form_state) {
    $form_state->set('cpage', 2);
    $form_state->set("data", [
      'firstname' => $form_state->get('firstname'),
    ]);
    $form_state->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function secondForm(array &$form, FormStateInterface $form_state) {
    $form['lastname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
    ];

    $form['secondback'] = [
      '#type' => 'submit',
      '#value' => 'Back',
      '#submit' => ['::secondBack'],
    ];

    $form['secondnext'] = [
      '#type' => 'submit',
      '#value' => 'Next',
      '#submit' => ['::secondNext'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function secondBack(array &$form, FormStateInterface $form_state) {
    $form_state->setValues($form_state->get('data'));
    $form_state->set('cpage', 1);
    $form_state->setRebuild(TRUE);
  }

    /**
   * {@inheritdoc}
   */
  public function secondNext(array &$form, FormStateInterface $form_state) {
    $values = $form_state->get('data');
    $form_state->set('cpage', 3);
    $form_state->set("data", [
      'firstname' => $values['firstname'],
      'lastname' => $form_state->get('lastname'),
    ]);
    $form_state->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function thirdForm(array &$form, FormStateInterface $form_state) {
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email Name'),
      '#description' => $this->t('Email Name'),
    ];

    $form['thirdback'] = [
      '#type' => 'submit',
      '#value' => 'Back',
      '#submit' => ['::thirdBack'],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
    ];

    return $form;
  }

    /**
   * {@inheritdoc}
   */
  public function thirdBack(array &$form, FormStateInterface $form_state) {
    $form_state->setValues($form_state->get('data'));
    $form_state->set('cpage', 2);
    $form_state->setRebuild(TRUE);
  }

    /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $mail = $form_state->getValue('email');
    $values = $form_state->get('data');
    $firstname = $values['firstname'];
    $lastname = $values['lastname'];
    $this->messenger()->addMessage(t("The form submitted successfully with Name: @firstname @lastname, Email: @email", [
      '@firstname' => $firstname, '@lastname' => $lastname, '@email' => $mail]));
  }
}