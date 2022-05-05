<?php

namespace Drupal\meeting\Form\Meeting;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;


/**
 * Class DeleteForm
 * @package Drupal\meeting\Form
 */
class DeleteForm extends ConfirmFormBase
{

  public $id;
  public $meeting_name;

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'delete_form';
  }

  public function getQuestion()
  {
    return $this->t('Delete Meeting');
  }

  public function getCancelUrl()
  {
    return new Url('meeting.display_data');
  }

  public function getDescription()
  {
    return $this->t('Do you want to delete the Meeting:  %id ?', array('%id' => $this->meeting_name));
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText()
  {
    return $this->t('Delete it!');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelText()
  {
    return $this->t('Cancel');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL)
  {

    $this->id = $id;
    $conn = Database::getConnection();
    $query = $conn->select('meeting', 'm')
    ->condition('id', $id)
    ->fields('m');
    $data = $query->execute()->fetchAssoc();
    $this->meeting_name = $data['meeting_name'];;

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $query = \Drupal::database();
    $query->delete('meeting')
      ->condition('id', $this->id)
      ->execute();
    \Drupal::messenger()->addStatus('Succesfully deleted.');
    $form_state->setRedirect('meeting.display_data');
  }
}
