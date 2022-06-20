<?php

namespace Drupal\meeting\Controller\Meeting;

use Drupal\Core\Controller\ControllerBase;
use Drupal\meeting\Controller\Poll\PollController;
use Drupal\meeting\Controller\Question\QuestionController;

/**
 * Undocumented class
 */
class MeetingEditController extends ControllerBase
{
    /**
     * @return array
     */
    public function index($id)
    {
        // render forms
        $form['container'] = [
            '#theme' => 'meeting_edit_template',
            '#type' => 'markup',
            '#meeting' => $this->formBuilder()->getForm(
                'Drupal\meeting\Form\Meeting\MeetingEditForm',
                $id
            ),
            '#poll' => $this->formBuilder()->getForm(
                'Drupal\meeting\Form\Poll\MeetingPollForm',
                $id
            ),
            '#polls' => PollController::get($id),
            '#question' => $this->formBuilder()->getForm(
                'Drupal\meeting\Form\Question\MeetingQuestionForm',
                $id
            ),
            '#questions' => QuestionController::get($id),
            '#survey' => $this->formBuilder()->getForm(
                'Drupal\meeting\Form\Survey\MeetingSurveyForm',
                $id
            ),
            '#info' => $this->formBuilder()->getForm(
                'Drupal\meeting\Form\Info\MeetingInfoForm',
                $id
            ),
            '#mobile' => $this->formBuilder()->getForm(
                'Drupal\meeting\Form\Mobile\MeetingMobileForm',
                $id
            ),
        ];

        return $form;
    }
}
