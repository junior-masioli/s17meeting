<?php

namespace Drupal\meeting\Form\Poll;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\file\Entity\File;
use Drupal\meeting\Ajax\LoadPartialCommand;
use Drupal\Core\Url;
use Drupal\meeting\Plugin\Helper\Component;

class MeetingPollForm extends FormBase
{
    /**
     * getFormId
     *
     * @return void
     */
    public function getFormId()
    {
        return 'poll_form';
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
        $data = $this->getCurrentMeeting('meeting', 'm', $id);
        $checked = $this->isActivated($id);
        $data_id = $data['id'] . '|poll|1';
        $data_url = Url::fromRoute('meeting.activate_module')->toString();

        $form_state->setCached(false);

        $form['#attributes']['novalidate'] = 'novalidate';

        $form['meeting_id'] = [
            '#type' => 'hidden',
            '#default_value' => $data['id'],
        ];

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
                'activate_poll_',
                'module-activate-ajax',
                'btn-activate-module',
                $checked,
                $data_id,
                $data['uuid'],
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
                '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 result-poll-message"></div>',
        ];

        $form['fields']['box']['row']['id'] = [
            '#type' => 'textfield',
            '#attributes' => ['data-selector-id' => 'poll-id'],
            '#wrapper_attributes' => ['class' => 'hidden'],
        ];

        $form['fields']['box']['row']['poll_question'] = [
            '#type' => 'textfield',
            '#title' => 'Poll question',
            '#required' => true,
            '#size' => 60,
            '#default_value' => '',
            '#maxlength' => 128,
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
            ],
            '#attributes' => [
                'placeholder' => 'Type your question here',
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
    public function promptCallback(array &$form, FormStateInterface $form_state)
    {
        if ($form_state->hasAnyErrors()) {
            $renderer = \Drupal::service('renderer');
            $status_messages = ['#type' => 'status_messages'];

            $response = new AjaxResponse();
            $response->addCommand(
                new HtmlCommand(
                    '.result-poll-message',
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
                new HtmlCommand('.result-poll-message', $messages)
            );
            $url = Url::fromRoute('meeting.poll_display_data', [
                'id' => $form_state->getValue('meeting_id'),
                'ajax' => true,
            ]);
            $response->addCommand(
                new LoadPartialCommand(
                    '.poll-form',
                    '#display_poll',
                    $url->toString()
                )
            );
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
        //get picture id
        $picture = $form_state->getValue('picture');

        //array data to insert on database
        $data = [
            'meeting_id' => $form_state->getValue('meeting_id'),
            'poll_question' => $form_state->getValue('poll_question'),
            'poll_allow_multiple_choice' => 0,
        ];

        // save file as Permanent
        if (isset($picture[0])) {
            $file = File::load($picture[0]);
            $file->setPermanent();
            $file->save();
        }

        if ($form_state->getValue('id')) {
            $query = \Drupal::database();
            $query
                ->update('poll')
                ->fields($data)
                ->condition('id', $form_state->getValue('id'))
                ->execute();
        } else {
            //create new meeting and get last id
            \Drupal::database()
                ->insert('poll')
                ->fields($data)
                ->execute();
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
    public function getCurrentMeeting($database, $alias, $id)
    {
        $conn = Database::getConnection();
        $data = [];
        if (isset($id)) {
            $query = $conn
                ->select($database, $alias)
                ->condition('id', $id)
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
                ->condition('module', 'poll')
                ->fields('m');
            $data = $query->execute()->fetchAssoc();
        }
        if ($data) {
            return 'checked';
        }

        return null;
    }
}
