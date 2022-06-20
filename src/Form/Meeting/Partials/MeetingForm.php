<?php

namespace Drupal\meeting\Form\Meeting\Partials;

use Drupal\Core\Url;

class MeetingForm
{
    /**
     * {@inheritdoc}
     */

    public function getFormId()
    {
        return 'meeting_form';
    }

    public static function partialForm($form, $form_state, $data)
    {
        $form['id'] = [
            '#type' => 'hidden',
            '#default_value' => isset($data['id']) ? $data['id'] : '',
        ];

        $form['uuid'] = [
            '#type' => 'hidden',
            '#default_value' => isset($data['uuid']) ? $data['uuid'] : '',
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

        $form['fields']['box']['row'] = [
            '#type' => 'container',
            '#attributes' => ['class' => 'row'],
        ];

        $form['fields']['box']['row']['meeting_name'] = [
            '#type' => 'textfield',
            '#title' => 'Meeting name',
            '#required' => true,
            '#size' => 60,
            '#default_value' => isset($data['meeting_name'])
                ? $data['meeting_name']
                : '',
            '#maxlength' => 128,
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
            ],
            '#attributes' => [
                'placeholder' => 'Meeting Name',
                'class' => ['col-full'],
            ],
        ];

        $form['fields']['box']['row']['meeting_url_video'] = [
            '#type' => 'textfield',
            '#title' => 'URL Video',
            '#default_value' => isset($data['meeting_url_video'])
                ? $data['meeting_url_video']
                : '',
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
            ],
            '#attributes' => [
                'placeholder' => 'URL Video',
                'class' => ['col-full'],
            ],
        ];

        $form['fields']['box']['row']['meeting_description'] = [
            '#type' => 'text_format',
            '#format' => 'meeting_html_editor',
            '#title' => 'Meeting description',
            '#default_value' => isset($data['meeting_description'])
                ? $data['meeting_description']
                : '',
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
            ],
            '#attributes' => [
                'placeholder' => 'Meeting description',
                'class' => ['col-full'],
            ],
        ];

        $form['fields']['box']['row']['meeting_background_colour'] = [
            '#type' => 'inputcolour',
            '#title' => t('Background colour'),
            '#default_value' => isset($data['meeting_background_colour'])
                ? $data['meeting_background_colour']
                : '#ffffff',
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-6 col-md-6 col-lg-6',
            ],
        ];

        $form['fields']['box']['row']['meeting_text_colour'] = [
            '#type' => 'inputcolour',
            '#title' => t('Text colour'),
            '#default_value' => isset($data['meeting_text_colour'])
                ? $data['meeting_text_colour']
                : '#000000',
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-6 col-md-6 col-lg-6',
            ],
        ];

        $form['fields']['box']['row']['meeting_button_background_colour'] = [
            '#type' => 'inputcolour',
            '#title' => t('Button colour'),
            '#default_value' => isset($data['meeting_button_background_colour'])
                ? $data['meeting_button_background_colour']
                : '#000000',
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-6 col-md-6 col-lg-6',
            ],
        ];

        $form['fields']['box']['row']['meeting_button_text_colour'] = [
            '#type' => 'inputcolour',
            '#title' => 'Button text colour',
            '#default_value' => isset($data['meeting_button_text_colour'])
                ? $data['meeting_button_text_colour']
                : '#ffffff',
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-6 col-md-6 col-lg-6',
            ],
        ];

        $form['fields']['box']['row']['meeting_results_shadow_colour'] = [
            '#type' => 'inputcolour',
            '#title' => 'Results shadow colour',
            '#default_value' => isset($data['meeting_results_shadow_colour'])
                ? $data['meeting_results_shadow_colour']
                : '#cccccc',
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-6 col-md-6 col-lg-6',
            ],
        ];

        $form['fields']['box']['row']['meeting_results_bar_colour'] = [
            '#type' => 'inputcolour',
            '#title' => 'Results bar colour',
            '#default_value' => isset($data['meeting_results_bar_colour'])
                ? $data['meeting_results_bar_colour']
                : '#000000',
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-6 col-md-6 col-lg-6',
            ],
        ];

        $form['fields']['box']['box-thumb'] = [
            '#type' => 'container',
            '#attributes' => ['class' => 'row box-thumb'],
            '#open' => true,
        ];

        $form['fields']['box']['box-thumb']['meeting_results_background'] = [
            '#title' => 'Results background image',
            '#description' => 'Choose Image gif png jpg jpeg',
            '#type' => 'managed_image',
            '#default_value' => isset($data['fid']) ? [$data['fid']] : [],
            '#upload_location' => 'public://images/',
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
            ],
            '#attributes' => ['class' => ['col-full']],
            '#image_style' => 'wide',
            '#upload_validators' => [
                'file_validate_extensions' => ['gif png jpg jpeg'],
            ],
        ];

        $form['fields']['box']['box-thumb-banner'] = [
            '#type' => 'container',
            '#attributes' => ['class' => 'row box-thumb-banner'],
            '#open' => true,
        ];

        $form['fields']['box']['box-thumb-banner']['mobile_banner_image'] = [
            '#title' => 'Mobile banner image',
            '#description' => 'Choose Image gif png jpg jpeg',
            '#type' => 'managed_image',
            '#default_value' => isset($data['banner_fid'])
                ? [$data['banner_fid']]
                : [],
            '#upload_location' => 'public://images/',
            '#wrapper_attributes' => [
                'class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
            ],
            '#attributes' => ['class' => ['col-full']],
            '#image_style' => 'wide',
            '#upload_validators' => [
                'file_validate_extensions' => ['gif png jpg jpeg'],
            ],
        ];

        // $form['embed_link'] = [
        //     '#title' => 'Embed link',
        //     '#type' => 'link',
        //     '#url' => Url::fromRoute('meeting.display_data'),
        //     '#attributes' => [
        //         'class' => ['embed-link margin-b-10'],
        //     ],
        // ];
        if ($data['uuid']) {
            $form['moderator_view_link'] = [
                '#title' => 'Moderator View Link',
                '#type' => 'link',
                '#url' => Url::fromRoute('meeting.question_moderator', [
                    'uuid' => $data['uuid'],
                ]),
                '#attributes' => [
                    'class' => ['embed-link margin-b-10'],
                    'target' => '_blank',
                ],
            ];

            $form['speaker_view_link'] = [
                '#title' => 'Speaker View Link',
                '#type' => 'link',
                '#url' => Url::fromRoute('meeting.question_speaker', [
                    'uuid' => $data['uuid'],
                ]),
                '#attributes' => [
                    'class' => ['embed-link margin-b-15'],
                    'target' => '_blank',
                ],
            ];

            $form['mobile_view_link'] = [
                '#title' => 'Mobile standalone link',
                '#type' => 'link',
                '#url' => Url::fromRoute('meeting.question_speaker', [
                    'uuid' => $data['uuid'],
                ]),
                '#attributes' => [
                    'class' => ['embed-link margin-b-15'],
                    'target' => '_blank',
                ],
            ];
        }

        $form['meeting_status'] = [
            '#type' => 'checkbox',
            '#title' => 'Published',
            '#default_value' => isset($data['meeting_status'])
                ? $data['meeting_status']
                : '',
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => 'Save',
            '#attributes' => ['class' => ['button', 'button--primary']],
        ];

        $form['link'] = [
            '#title' => 'Cancel',
            '#type' => 'link',
            '#url' => Url::fromRoute('meeting.display_data'),
            '#attributes' => [
                'class' => [
                    'button',
                    'js-form-submit',
                    'form-submit',
                    'btn-border-red',
                ],
            ],
        ];

        return $form;
    }
}
