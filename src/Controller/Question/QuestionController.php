<?php

namespace Drupal\meeting\Controller\Question;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Ajax\AjaxResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Render\Markup;
use Drupal\meeting\Plugin\Helper\Status;

class QuestionController extends ControllerBase
{
    public static function get($id, $ajax = false)
    {
        //create table header
        $header_table = [
            'question' => [
                'data' => t('Questions'),
                'class' => ['col-6 text-center'],
            ],
            'is_read' => [
                'data' => t('Has it been read?'),
                'class' => ['col-2 text-center'],
            ],
            'status' => [
                'data' => t('Status'),
                'class' => ['col-1 text-center'],
            ],
            'approve' => [
                'data' => t('Approve'),
                'class' => ['col-1 text-center'],
            ],
            'archive' => [
                'data' => t('Archive'),
                'class' => ['col-1 text-center'],
            ],
            'delete' => [
                'data' => t('Delete'),
                'class' => ['col-1 text-center'],
            ],
        ];

        // get data from database
        $query = \Drupal::database()->select('question_submission', 'q');
        $query->fields('q', [
            'id',
            'meeting_id',
            'question_id',
            'question',
            'is_read',
            'status',
        ]);
        $query->condition('meeting_id', $id);
        $query->condition('status', 3, '<');
        $query->orderBy('id', 'desc');
        $results = $query->execute()->fetchAll();

        $rows = [];
        foreach ($results as $data) {
            $class =
                $data->status == 0
                    ? 'btn--primary update-question-ajax'
                    : 'btn--default';
            //get data
            $rows[] = [
                'question' => [
                    'data' => $data->question,
                    'class' => ['text-left'],
                ],
                'is_read' => [
                    'data' => $data->is_read == 0 ? 'No' : 'Yes',
                    'class' => ['text-center'],
                ],
                'status' => [
                    'data' => Status::question_status($data->status),
                    'class' => ['text-center'],
                ],
                'approve' => self::buttonControl(
                    $data,
                    'meeting.question_update',
                    '<i class=feather-check></i>',
                    1,
                    $class
                ),
                'archive' => self::buttonControl(
                    $data,
                    'meeting.question_update',
                    '<i class=feather-archive></i>',
                    2,
                    $class
                ),
                'delete' => self::buttonControl(
                    $data,
                    'meeting.question_update',
                    '<i class=feather-trash></i>',
                    3,
                    'btn--primary delete-question-ajax'
                ),
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

    public static function buttonControl($data, $url, $label, $status, $class)
    {
        $url = $data->status != 3 ? Url::fromRoute($url)->toString() : null;
        return [
            'data' => new FormattableMarkup(
                '<button class="btn  btn-sm d-flex m-auto ' .
                    $class .
                    '" data-id="@id" data-status="' .
                    $status .
                    '" data-url="' .
                    $url .
                    '">@name</button>',
                ['@name' => Markup::create($label), '@id' => $data->id]
            ),
        ];
    }

    public function update()
    {
        $id = \Drupal::request()->request->get('id');
        $status = \Drupal::request()->request->get('status');

        $query = \Drupal::database();
        $query
            ->update('question_submission')
            ->fields(['status' => $status])
            ->condition('id', $id)
            ->execute();
        $response = new AjaxResponse();
        return $response;
    }
}
