<?php

namespace Drupal\meeting\Form\PollAnswer;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\meeting\Ajax\LoadPartialCommand;
use Drupal\Core\Url;

class MeetingPollAnswerForm extends FormBase
{

  /**
   * getFormId
   *
   * @return void
   */
  public function getFormId()
  {
    return 'poll_answer_form';
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
    $poll =  $this->getForeign('poll', 'p', $id);
    $meeting =  $this->getForeign('meeting', 'm', $poll['meeting_id']);

    $form_state->setCached(FALSE);

    $form['#attributes']['novalidate'] = 'novalidate';

    $form['meeting_id'] = [
      '#type' => 'hidden',
      '#default_value' => $meeting['id'],
    ];

    $form['poll_id'] = [
      '#type' => 'hidden',
      '#default_value' => $poll['id'],
    ];

    $form['fields'] = [
      '#type'  => 'container',
      '#open'  => true,
      '#attributes' => ['class' => 'row']
    ];

    $form['fields']['box'] = [
      '#type'  => 'container',
      '#attributes' => ['class' => 'col-xs-12 col-sm-12 col-md-12col-lg-12']
    ];

    $form['fields']['box']['row'] = [
      '#type'  => 'container',
      '#attributes' => ['class' => 'row']
    ];

    $form['fields']['box']['row']['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 result-answer-message"></div>'
    ];

    $form['fields']['box']['row']['id'] = [
      '#type' => 'textfield',
      '#attributes' => ['data-selector-id' => 'poll-answer-id'],
      '#wrapper_attributes' => ['class' => 'hidden']
    ];

    $form['fields']['box']['row']['answer'] = [
      '#type' => 'textfield',
      '#title' => 'Answer',
      '#required' => true,
      '#size' => 60,
      '#default_value' => '',
      '#maxlength' => 128,
      '#wrapper_attributes' => ['class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12'],
      '#attributes' => ['placeholder' => 'Type a answer here', 'class' => ['col-full']],
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
  public function promptCallback(array &$form, FormStateInterface $form_state)
  {
    if ($form_state->hasAnyErrors()) {
      $renderer = \Drupal::service('renderer');
      $status_messages = ['#type' => 'status_messages'];

      $response = new AjaxResponse();
      $response->addCommand(
        new HtmlCommand(
          '.result-answer-message',
          $renderer->renderRoot($status_messages)
        ),
      );
    }else{
      $response = new AjaxResponse();

      \Drupal::messenger()->addMessage($this->t('Successfully saved'), 'status', TRUE);
      $messages = ['#type' => 'status_messages'];

      $response->addCommand(
        new HtmlCommand(
          '.result-answer-message',
          $messages
        ),
      );
      $url = Url::fromRoute('meeting.poll_answer_display_data', ['id' => $form_state->getValue('poll_id'), 'ajax' =>true]);
      $response->addCommand(new LoadPartialCommand('.poll-answer-form', '#display_poll_answer', $url->toString()));
    }

    return $response;
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
      'meeting_id' =>  $form_state->getValue('meeting_id'),
      'poll_id'    =>  $form_state->getValue('poll_id'),
      'answer'     =>  $form_state->getValue('answer')
    );


    if($form_state->getValue('id')){
      $query = \Drupal::database();
      $query->update('poll_answer')
        ->fields($data)
        ->condition('id', $form_state->getValue('id'))
        ->execute();

    }else{
      //create new poll answer
      \Drupal::database()->insert('poll_answer')->fields($data)->execute();
    }


    $form_state->setRebuild();
  }

  /**
   * Undocumented function
   *
   * @param [type] $database
   * @param [type] $alias
   * @param [type] $id
   * @return void
   */
  public function getForeign($database, $alias, $id)
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
