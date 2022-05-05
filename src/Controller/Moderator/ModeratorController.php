<?php

namespace Drupal\meeting\Controller\Moderator;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\JsonResponse;

class ModeratorController extends ControllerBase
{
    public static function index($uuid)
    {
        // get data from database
        $meeting = self::getCurrentMeeting('meeting', 'm', $uuid);
        $query = \Drupal::database()->select('question_submission', 'q');
        $query->fields('q', [
            'id',
            'meeting_id',
            'question_id',
            'question',
            'is_read',
            'status',
        ]);
        $query->condition('meeting_id', $meeting['id']);
        $query->condition('status', 3, '<');
        $query->orderBy('id', 'desc');
        $results = $query->execute()->fetchAll();

        // render questions
        $questions['container'] = [
            '#theme' => 'question_moderator',
            '#type' => 'markup',
            '#meeting' => $meeting,
            '#questions' => $results,
        ];

        return $questions;
    }

    /**
     * Get questions speaker.
     */
    public static function speaker($uuid)
    {
        // get data from database
        $meeting = self::getCurrentMeeting('meeting', 'm', $uuid);
        $query = \Drupal::database()->select('question_submission', 'q');
        $query->fields('q', [
            'id',
            'meeting_id',
            'question_id',
            'question',
            'is_read',
            'status',
        ]);
        $query->condition('meeting_id', $meeting['id']);
        $query->condition('status', 3, '<');
        $query->orderBy('id', 'desc');
        $results = $query->execute()->fetchAll();

        // render questions
        $questions['container'] = [
            '#theme' => 'question_speaker',
            '#type' => 'markup',
            '#meeting' => $meeting,
            '#questions' => $results,
        ];

        return $questions;
    }

    /*
     * get  meetings by uuid
     */
    public static function get($uuid)
    {
        // get data from database
        $meeting = self::getCurrentMeeting('meeting', 'm', $uuid);
        $query = \Drupal::database()->select('question_submission', 'q');
        $query->fields('q', [
            'id',
            'meeting_id',
            'question_id',
            'question',
            'is_read',
            'status',
        ]);
        $query->condition('meeting_id', $meeting['id']);
        $query->condition('status', 3, '<');
        $query->orderBy('id', 'desc');
        $results = $query->execute()->fetchAll();

        $questions = [];
        foreach ($results as $data) {
            $questions[] = [
                'id' => $data->id,
                'meeting' => $meeting,
                'question_id' => $data->question_id,
                'question' => $data->question,
                'is_read' => $data->is_read,
                'status' => $data->status,
            ];
        }

        return new JsonResponse([
            'data' => $questions,
            'method' => 'GET',
            'status' => 200,
        ]);
    }

    /**
     *update function
     */

    public function update()
    {
        $id = \Drupal::request()->request->get('id');
        $question = \Drupal::request()->request->get('question');
        $status = \Drupal::request()->request->get('status');

        $conn = \Drupal::database();
        $query_question = $conn
            ->select('question_submission', 's')
            ->condition('id', $id)
            ->fields('s');
        $data = $query_question->execute()->fetchAssoc();
        $st = $status ? $status : $data['status'];
        $qn = $question ? $question : $data['question'];

        $fields = [
            'question' => $qn,
            'status' => $st,
        ];

        $conn
            ->update('question_submission')
            ->fields($fields)
            ->condition('id', $id)
            ->execute();

        return new JsonResponse([
            'message' => 'Success',
            'method' => 'GET',
            'status' => 200,
        ]);
    }

    /**
     * getCurrentMeeting function
     *
     * @param [type] $database
     * @param [type] $alias
     * @param [type] $id
     * @return void
     */
    public function getCurrentMeeting($database, $alias, $uuid)
    {
        $conn = Database::getConnection();
        $data = [];
        if (isset($uuid)) {
            $query = $conn
                ->select($database, $alias)
                ->condition('uuid', $uuid)
                ->fields($alias);
            $data = $query->execute()->fetchAssoc();
        }

        return $data;
    }
}
