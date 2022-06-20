<?php

namespace Drupal\meeting\Form\Meeting;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\meeting\Form\Meeting\Partials\MeetingForm;

class MeetingCreateForm extends FormBase
{
    /**
     * getFormId
     *
     * @return void
     */
    public function getFormId()
    {
        return 'meeting_form';
    }
    /**
     * Undocumented function
     *
     * @param array $form
     * @param FormStateInterface $form_state
     * @return void
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form = MeetingForm::partialForm($form, $form_state, null);

        return $form;
    }

    /**
     * Undocumented function
     *
     * @param array $form
     * @param FormStateInterface $form_state
     * @return void
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        if (is_numeric($form_state->getValue('meeting_name'))) {
            $form_state->setErrorByName(
                'meeting_name',
                $this->t('Error, The Meeting name Must Be A String')
            );
        }
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
        $picture = $form_state->getValue('meeting_results_background');
        $picture_banner = $form_state->getValue('mobile_banner_image');

        //array data to insert on database
        $data = [
            'uuid' => \Drupal::service('uuid')->generate(),
            'meeting_name' => $form_state->getValue('meeting_name'),
            'meeting_description' => $form_state->getValue(
                'meeting_description'
            )['value'],
            'meeting_url_video' => $form_state->getValue('meeting_url_video'),
            'fid' => isset($picture[0]) ? $picture[0] : null,
            'banner_fid' => isset($picture_banner[0])
                ? $picture_banner[0]
                : null,
            'meeting_background_colour' => $form_state->getValue(
                'meeting_background_colour'
            ),
            'meeting_text_colour' => $form_state->getValue(
                'meeting_text_colour'
            ),
            'meeting_button_background_colour' => $form_state->getValue(
                'meeting_button_background_colour'
            ),
            'meeting_button_text_colour' => $form_state->getValue(
                'meeting_button_text_colour'
            ),
            'meeting_results_shadow_colour' => $form_state->getValue(
                'meeting_results_shadow_colour'
            ),
            'meeting_results_bar_colour' => $form_state->getValue(
                'meeting_results_bar_colour'
            ),
            'meeting_status' => $form_state->getValue('meeting_status'),
        ];

        // save file as Permanent
        if (isset($picture[0])) {
            $file = File::load($picture[0]);
            $file->setPermanent();
            $file->save();
        }
        if (isset($picture_banner[0])) {
            $file = File::load($picture_banner[0]);
            $file->setPermanent();
            $file->save();
        }

        //create new meeting and get last id
        $meeting = \Drupal::database()
            ->insert('meeting')
            ->fields($data)
            ->execute();

        // show message and redirect to list page
        \Drupal::messenger()->addStatus('Succesfully saved');
        $url = Url::fromRoute('meeting.edit_form', ['id' => $meeting]);
        $form_state->setRedirectUrl($url);

        return;
    }
}
