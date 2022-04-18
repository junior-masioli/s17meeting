<?php

namespace Drupal\meeting\Form\Meeting;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\meeting\Form\Meeting\Partials\MeetingForm;

class MeetingEditForm extends FormBase
{

  /**
   * {@inheritdoc}
   */

  public function getFormId()
  {
    return 'meeting_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL)
  {
    $data =  $this->getCurrentRegister('meeting', 'm', $id);
    $form = MeetingForm::partialForm($form, $form_state, $data);

    return $form;
  }


  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    if (is_numeric($form_state->getValue('meeting_name'))) {
      $form_state->setErrorByName('meeting_name', $this->t('Error, The Meeting name Must Be A String'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    //get picture id
    $picture = $form_state->getValue('meeting_results_background');

    $data = array(
      'meeting_name'                      =>  $form_state->getValue('meeting_name'),
      'meeting_description'               =>  $form_state->getValue('meeting_description')['value'],
      'meeting_url_video'                 =>  $form_state->getValue('meeting_url_video'),
      'fid'                               =>  isset($picture[0]) ? $picture[0] : NULL,
      'meeting_background_colour'         =>  $form_state->getValue('meeting_background_colour'),
      'meeting_text_colour'               =>  $form_state->getValue('meeting_text_colour'),
      'meeting_button_background_colour'  =>  $form_state->getValue('meeting_button_background_colour'),
      'meeting_button_text_colour'        =>  $form_state->getValue('meeting_button_text_colour'),
      'meeting_results_shadow_colour'     =>  $form_state->getValue('meeting_results_shadow_colour'),
      'meeting_results_bar_colour'        =>  $form_state->getValue('meeting_results_bar_colour'),
      'meeting_status'                    =>  $form_state->getValue('meeting_status'),
    );

    // save file as Permanent
    if (isset($picture[0])) {
      $file = File::load($picture[0]);
      $file->setPermanent();
      $file->save();
    }

    \Drupal::database()->update('meeting')->fields($data)->condition('id', $form_state->getValue('id'))->execute();

    // show message and redirect to list page
    \Drupal::messenger()->addStatus('Succesfully saved');

    $url = Url::fromRoute('meeting.edit_form', ['id' => $form_state->getValue('id')]);
    $form_state->setRedirectUrl($url);

    return;
  }


  /**
   * Undocumented function
   *
   * @param [type] $database
   * @param [type] $alias
   * @param [type] $id
   * @return void
   */
  public function getCurrentRegister($database, $alias, $id)
  {
    $conn = Database::getConnection();
    $data = array();
    if (isset($id)) {
      $query = $conn->select($database, $alias)
        ->condition('id', $id)
        ->fields($alias);
      $data = $query->execute()->fetchAssoc();
    }

    return $data;
  }

}
