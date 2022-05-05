<?php

namespace Drupal\meeting\Controller\Poll;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\file\Entity\File;

class PollResultController extends ControllerBase
{
    public static function index($id)
    {
        // get data from database
        $poll = self::getCurrentPoll('poll', 'p', $id);
        $meeting = self::getCurrentMeeting('meeting', 'm', $poll['meeting_id']);

        // render questions
        $questions['container'] = [
            '#theme' => 'poll_result_questions',
            '#type' => 'markup',
            '#meeting' => $meeting,
            '#poll' => $poll,
            '#questions' => $poll,
        ];

        return $questions;
    }

    /*
     * get  meetings by uuid
     */
    public static function get($id)
    {
        // get data from database
        $poll = self::getCurrentPoll('poll', 'p', $id);
        $query = \Drupal::database()->select('poll_answer', 'pa');
        $query->fields('pa', ['id', 'poll_id', 'answer']);
        $query->condition('poll_id', $poll['id']);
        $query->orderBy('id', 'desc');
        $results = $query->execute()->fetchAll();

        $poll_questions = [];
        foreach ($results as $data) {
            $poll_questions[] = [
                'id' => $data->id,
                'poll' => $poll,
                'answer' => $data->answer,
                'total_votes' => self::pollTotalVote($data->poll_id),
                'votes' => self::pollVote($data->id),
            ];
        }

        return new JsonResponse([
            'data' => $poll_questions,
            'method' => 'GET',
            'status' => 200,
        ]);
    }

    public function pollTotalVote($poll_id)
    {
        $conn = Database::getConnection();
        $query = $conn
            ->select('poll_answer_user', 'pa')
            ->condition('poll_id', $poll_id)
            ->fields('pa', ['id'])
            ->countQuery()
            ->execute()
            ->fetchField();
        return $query;
    }
    public function pollVote($poll_answer_id)
    {
        $conn = Database::getConnection();
        $query = $conn
            ->select('poll_answer_user', 'pa')
            ->condition('poll_answer_id', $poll_answer_id)
            ->fields('pa', ['id'])
            ->countQuery()
            ->execute()
            ->fetchField();
        return $query;
    }

    /**
     * getCurrentPoll function
     *
     * @param [type] $database
     * @param [type] $alias
     * @param [type] $id
     * @return void
     */
    public function getCurrentPoll($database, $alias, $id)
    {
        $conn = Database::getConnection();
        if (isset($id)) {
            $query = $conn
                ->select($database, $alias)
                ->condition('id', $id)
                ->fields($alias);
            $data = $query->execute()->fetchAssoc();
        }

        return $data;
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
            $result = $query->execute()->fetchAssoc();

            $fid = $result['fid'];
            $file = File::load($fid);
            $path = $file->getFileUri();
            $url = $file->createFileUrl();

            $data = [
                'id' => $result['id'],
                'meeting_results_shadow_colour' =>
                    $result['meeting_results_shadow_colour'],
                'meeting_results_bar_colour' =>
                    $result['meeting_results_bar_colour'],
                'background' => $url,
            ];
        }

        return $data;
    }
}
