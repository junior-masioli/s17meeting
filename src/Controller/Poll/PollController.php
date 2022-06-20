<?php

namespace Drupal\meeting\Controller\Poll;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\meeting\Ajax\RemoveTrCommand;
use Drupal\Component\Render\FormattableMarkup;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Link;
use Drupal\Core\Render\Markup;
use Drupal\meeting\Plugin\Helper\Component;
use Drupal\Core\Database\Database;

class PollController extends ControllerBase
{
    public static function get($id, $ajax = false)
    {
        //create table header
        $header_table = [
            'action' => ['data' => 'Actions', 'class' => ['col-1 text-center']],
            'poll_question' => [
                'data' => 'Poll question',
                'class' => ['col-7 text-center'],
            ],
            'allow' => [
                'data' => 'Allow multiple choice',
                'class' => ['col-1 text-center'],
            ],
            'poll_control' => [
                'data' => 'Poll control',
                'class' => ['col-1 text-center'],
            ],
            'results_control' => [
                'data' => 'Results control',
                'class' => ['col-1 text-center'],
            ],
            'results' => [
                'data' => '',
                'class' => ['col-1 text-center'],
            ],
            'participant' => [
                'data' => 'Participant',
                'class' => ['col-1 text-center'],
            ],
        ];

        // get data from database
        $query = \Drupal::database()->select('poll', 'p');
        $query->fields('p', [
            'id',
            'meeting_id',
            'poll_question',
            'poll_allow_multiple_choice',
            'poll_activate_poll',
            'poll_show_results',
            'poll_show_results',
        ]);
        $query->condition('meeting_id', $id);
        $query->orderBy('id', 'desc');
        $results = $query->execute()->fetchAll();

        $rows = [];
        foreach ($results as $data) {
            //get data
            $rows[] = [
                'edit' => self::actionEdit($data),
                'poll_question' => [
                    'data' => $data->poll_question,
                    'class' => ['text-left'],
                ],
                'allow' => self::buttonControl(
                    $data,
                    self::getCurrentMeeting('meeting', 'm', $id),
                    'ck1',
                    'meeting.poll_allow',
                    $data->poll_allow_multiple_choice
                ),
                'poll_control' => self::buttonControl(
                    $data,
                    self::getCurrentMeeting('meeting', 'm', $id),
                    'ck2',
                    'meeting.poll_poll_control',
                    $data->poll_activate_poll
                ),
                'results_control' => self::buttonControl(
                    $data,
                    self::getCurrentMeeting('meeting', 'm', $id),
                    'ck3',
                    'meeting.poll_result_controll',
                    $data->poll_show_results
                ),
                'results' => self::actionResults($data),
                'participant' => [
                    'data' => self::pollParticipant(
                        $data->meeting_id,
                        $data->id
                    ),
                    'class' => ['col-1 text-center'],
                ],
            ];
        }

        // render table
        $form['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => 'No data found',
        ];

        if ($ajax) {
            return new Response(render($form));
        }

        return $form;
    }

    public static function actionEdit($data)
    {
        $url_delete = Url::fromRoute('meeting.poll_delete', [
            'id' => $data->id,
        ]);

        $link_url = Url::fromRoute('meeting.poll_answer_modal', [
            'id' => $data->id,
        ]);
        $link_url->setOptions([
            'attributes' => [
                'class' => ['use-ajax'],
                'data-dialog-type' => 'modal',
                'data-dialog-options' => Json::encode(['width' => '980']),
            ],
        ]);

        return [
            'data' => new FormattableMarkup(
                '<div class="action-dropdown-wrapper"> <div class="action-dropdown">
              <div class="action-link-dropdown">&#8942;</div>
              <div class="action-item-dropdown">
                ' .
                    Link::fromTextAndUrl(
                        Markup::create(
                            '<i class=feather-file-text></i> @name_answer'
                        ),
                        $link_url
                    )->toString() .
                    '
                <button class="remove-poll" data-form="poll-form"  data-url=' .
                    $url_delete->toString() .
                    '><i class="feather-trash"></i> @name_delete</button>
              </div>
           </div>
           <button class="btn btn-sm btn--primary edit-poll-ajax" data-poll="@data_edit">@name_edit</button>
           </div>',
                [
                    '@name_edit' => 'Edit',
                    '@data_edit' => json_encode($data),
                    '@name_answer' => 'Answers',
                    '@data_answer' => json_encode($data),
                    '@name_delete' => 'Delete',
                ]
            ),
        ];
    }

    public static function actionResults($data)
    {
        $url = Url::fromRoute('meeting.poll_display_results', [
            'id' => $data->id,
        ]);

        $url->setOptions([
            'attributes' => [
                'class' => ['btn btn-xs btn--primary'],
                'target' => '_blank',
            ],
        ]);

        return [
            'data' => new FormattableMarkup(
                Link::fromTextAndUrl(
                    Markup::create('<i class="feather-bar-chart"></i>'),
                    $url
                )->toString(),
                [
                    '@name' => '',
                ]
            ),
        ];
    }

    public static function buttonControl($data, $uuid, $ck, $url, $field)
    {
        $url_poll_control = Url::fromRoute($url);
        $poll_choice = $field == 1 ? 'checked' : '';

        return Component::checkbox(
            "switch$data->id-$ck",
            'poll-control-ajax',
            null,
            $poll_choice,
            $data->id,
            $uuid,
            $url_poll_control->toString()
        );
    }

    public function delete($id)
    {
        $query = \Drupal::database();
        $query
            ->delete('poll')
            ->condition('id', $id)
            ->execute();

        $response = new AjaxResponse();

        $response->addCommand(new RemoveTrCommand('.remove-ajax'));

        return $response;
    }

    public static function getCurrentMeeting($database, $alias, $id)
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

        return $data['uuid'];
    }

    public static function pollParticipant($meeting_id, $poll_id)
    {
        $conn = Database::getConnection();
        $query = $conn
            ->select('poll_answer_user', 'pa')
            ->condition('meeting_id', $meeting_id)
            ->condition('poll_id', $poll_id)
            ->fields('pa', ['id'])
            ->countQuery()
            ->execute()
            ->fetchField();
        return $query;
    }
}
