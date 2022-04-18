<?php

namespace Drupal\meeting\Form\Survey;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Url;
use Drupal\meeting\Plugin\Helper\Component;

class MeetingSurveyForm extends FormBase
{

  /**
   * getFormId
   *
   * @return void
   */
  public function getFormId()
  {
    return 'survey_form';
  }
  /**
   * Undocumented function
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return void
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id=NULL)
  {
    $meeting =  $this->getCurrentData('meeting', 'm', 'id', $id);
    $checked  =  $this->isActivated($id);
    $data_id  =  $meeting['id'] . '|survey|3';
    $data_url =  Url::fromRoute('meeting.activate_module')->toString();

    $survey =  $this->getCurrentData('survey', 'q', 'meeting_id', $meeting['id']);

    $form_state->setCached(FALSE);

    $form['#attributes']['novalidate'] = 'novalidate';


    $form['fields'] = [
      '#type'  => 'container',
      '#open'  => true,
      '#attributes' => ['class' => 'row']
    ];

    $form['fields']['box'] = [
      '#type'  => 'container',
      '#attributes' => ['class' => 'col-xs-12 col-sm-12 col-md-12col-lg-12']
    ];


    $form['fields']['box']['ck'] = [
      '#type' => 'markup',
      '#markup' => Component::checkbox('activate_survey_', 'module-activate-ajax', 'btn-activate-module', $checked, $data_id, $data_url)
    ];


    $form['fields']['box']['row'] = [
      '#type'  => 'container',
      '#attributes' => ['class' => 'row']
    ];

    $form['fields']['box']['row']['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 result-survey-message"></div>'
    ];

    $form['fields']['box']['row']['meeting_id'] = [
      '#type' => 'hidden',
      '#default_value' => $meeting['id'],
    ];

    $form['fields']['box']['row']['id'] = [
      '#type' => 'hidden',
      '#default_value' => (isset($survey['id'])) ? $survey['id'] : '',
      '#attributes' => ['data-selector-id' => 'survey-id']
    ];

    $form['fields']['box']['row']['title'] = [
      '#type' => 'textfield',
      '#title' => 'Survey title',
      '#required' => true,
      '#size' => 60,
      '#default_value' => (isset($survey['title'])) ? $survey['title'] : '',
      '#maxlength' => 128,
      '#wrapper_attributes' => ['class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12'],
      '#attributes' => ['placeholder' => 'Survey title', 'class' => ['col-full']],
    ];

    $form['fields']['box']['row']['description'] = [
      '#type' => 'text_format',
      '#format' =>  'meeting_html_editor',
      '#title' => 'Survey description',
      '#default_value' => (isset($survey['description'])) ? $survey['description'] : '',
      '#wrapper_attributes' => ['class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12'],
      '#attributes' => ['placeholder' => 'Survey description', 'class' => ['col-full']]
    ];

    $form['fields']['box']['row']['url'] = [
      '#type' => 'textfield',
      '#title' => 'Survey url',
      '#required' => true,
      '#size' => 60,
      '#default_value' => (isset($survey['url'])) ? $survey['url'] : '',
      '#maxlength' => 128,
      '#wrapper_attributes' => ['class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12'],
      '#attributes' => ['placeholder' => 'Survey url', 'class' => ['col-full']],
    ];


    $form['actions'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#attributes' => ['class' => ['action_ajax']],
      '#ajax' => [
        'callback' => '::promptCallback',
      ],
    ];

    return $form;
  }


  /**
   * Undocumented function
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return void
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    //array data to insert on database
    $data = array(
      'meeting_id'     =>  1,
      'title'          =>  $form_state->getValue('title'),
      'description'    =>  $form_state->getValue('description')['value'],
      'url'            =>  $form_state->getValue('url')
    );
    $query = \Drupal::database();

    $survey = $query->select('survey', 's')->fields(null, ['meeting_id'])
    ->condition('meeting_id', $form_state->getValue('meeting_id'))->countQuery()
    ->execute()->fetchField();

    if($survey){
      $query->update('survey')
        ->fields($data)
        ->condition('meeting_id', $form_state->getValue('meeting_id'))
        ->execute();

    }else{
      //create new meeting and get last id
      $query->insert('survey')->fields($data)->execute();
    }

    $form_state->setRebuild();
  }

  /**
   * Undocumented function
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return void
   */
  public function promptCallback(array &$form, FormStateInterface $form_state)
  {
    if ($form_state->hasAnyErrors()) {
      $renderer = \Drupal::service('renderer');
      $status_messages = ['#type' => 'status_messages'];

      $response = new AjaxResponse();
      $response->addCommand(
        new HtmlCommand(
          '.result-survey-message',
          $renderer->renderRoot($status_messages)
        ),
      );
    }else{
      $response = new AjaxResponse();

      \Drupal::messenger()->addMessage($this->t('Successfully saved'), 'status', TRUE);
      $messages = ['#type' => 'status_messages'];

      $response->addCommand(
        new HtmlCommand(
          '.result-survey-message',
          $messages
        ),
      );
    }

    return $response;
  }

  /**
   * Undocumented function
   *
   * @param [type] $database
   * @param [type] $alias
   * @param [type] $id
   * @return void
   */
  public function getCurrentData($database, $alias, $field, $id)
  {
    $conn = Database::getConnection();
    $data = array();
    if (isset($id)) {
      $query = $conn->select($database, $alias)
        ->condition($field, $id)
        ->fields($alias);
      $data = $query->execute()->fetchAssoc();
    }

    return $data;
  }

  /**
   * Undocumented function
   *
   * @param [type] $id
   * @return boolean
   */
  public function isActivated($id)
  {
    $conn = Database::getConnection();
    $data = array();
    if (isset($id)) {
      $query = $conn->select("meeting_module", "m")
        ->condition('meeting_id', $id)
        ->condition('module', "survey")
        ->fields("m");
      $data = $query->execute()->fetchAssoc();
    }
    if ($data) {
      return "checked";
    }

    return null;
  }
}
