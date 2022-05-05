<?php

namespace Drupal\meeting\Form\Question;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Url;
use Drupal\meeting\Plugin\Helper\Component;

class MeetingQuestionForm extends FormBase
{
    /**
     * getFormId
     *
     * @return void
     */
    public function getFormId()
    {
        return 'question_form';
    }
    /**
     * Undocumented function
     *
     * @param array $form
     * @param FormStateInterface $form_state
     * @return void
     */
    public function buildForm(
        array $form,
        FormStateInterface $form_state,
        $id = null
    ) {
        $meeting = $this->getCurrentData('meeting', 'm', 'id', $id);
        $checked = $this->isActivated($id);
        $data_id = $meeting['id'] . '|question|2';
        $data_url = Url::fromRoute('meeting.activate_module')->toString();

        $question = $this->getCurrentData(
            'question',
            'q',
            'meeting_id',
            $meeting['id']
        );

        $form_state->setCached(false);

        $form['#attributes']['novalidate'] = 'novalidate';

        $form['fields'] = [
            '#type' => 'container',
            '#open' => true,
            '#attributes' => ['class' => 'row'],
        ];

        $form['fields']['box'] = [
            '#type' => 'container',
            '#attributes' => [
                'class' => 'col-xs-12 col-sm-12 col-md-12col-lg-12',
            ],
        ];

        $form['fields']['box']['ck'] = [
            '#type' => 'markup',
            '#markup' => Component::checkbox(
                'activate_question_',
                'module-activate-ajax',
                'btn-activate-module',
                $checked,
                $data_id,
                $meeting['uuid'],
                $data_url
            ),
        ];

        $form['fields']['box']['row'] = [
            '#type' => 'container',
            '#attributes' => ['class' => 'row'],
        ];

        $form['fields']['box']['row']['message'] = [
            '#type' => 'markup',
            '#markup' =>
                '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 result-question-message"></div>',
        ];

        $form['fields']['box']['row']['meeting_id'] = [
            '#type' => 'hidden',
            '#default_value' => $meeting['id'],
        ];

        $form['fields']['box']['row']['id'] = [
            '#type' => 'hidden',
            '#default_value' => isset($question['id']) ? $question['id'] : '',
            '#attributes' => ['data-selector-id' => 'question-id'],
        ];

        $form['fields']['box']['row']['title'] = [
            '#type' => 'textfield',
            '#title' => 'Question title',
            '#required' => true,
            '#size' => 60,
            '#default_value' => isset($question['title'])
                ? $question['title']
                : '',
            '#maxlength' => 128,
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
            ],
            '#attributes' => [
                'placeholder' => 'Question title',
                'class' => ['col-full'],
            ],
        ];

        $form['fields']['box']['row']['description'] = [
            '#type' => 'text_format',
            '#format' => 'meeting_html_editor',
            '#title' => 'Question description',
            '#default_value' => isset($question['description'])
                ? $question['description']
                : '',
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
            ],
            '#attributes' => [
                'placeholder' => 'Question description',
                'class' => ['col-full'],
            ],
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
        $data = [
            'meeting_id' => $form_state->getValue('meeting_id'),
            'title' => $form_state->getValue('title'),
            'description' => $form_state->getValue('description')['value'],
        ];
        $query = \Drupal::database();

        $question = $query
            ->select('question', 'q')
            ->fields(null, ['meeting_id'])
            ->condition('meeting_id', $form_state->getValue('meeting_id'))
            ->countQuery()
            ->execute()
            ->fetchField();

        if ($question) {
            $query
                ->update('question')
                ->fields($data)
                ->condition('meeting_id', $form_state->getValue('meeting_id'))
                ->execute();
        } else {
            //create new meeting and get last id
            $query
                ->insert('question')
                ->fields($data)
                ->execute();
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
                    '.result-question-message',
                    $renderer->renderRoot($status_messages)
                )
            );
        } else {
            $response = new AjaxResponse();

            \Drupal::messenger()->addMessage(
                $this->t('Successfully saved'),
                'status',
                true
            );
            $messages = ['#type' => 'status_messages'];

            $response->addCommand(
                new HtmlCommand('.result-question-message', $messages)
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
        $data = [];
        if (isset($id)) {
            $query = $conn
                ->select($database, $alias)
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
        $data = [];
        if (isset($id)) {
            $query = $conn
                ->select('meeting_module', 'm')
                ->condition('meeting_id', $id)
                ->condition('module', 'question')
                ->fields('m');
            $data = $query->execute()->fetchAssoc();
        }
        if ($data) {
            return 'checked';
        }

        return null;
    }
}
