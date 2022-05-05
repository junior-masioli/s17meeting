<?php

namespace Drupal\meeting\Controller\Api;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Request;

class MeetingController extends ControllerBase
{
    public function get($uuid)
    {
        // get data from database
        $conn = Database::getConnection();

        $question = self::getQuestion($conn, $uuid);

        $query_s = $conn
            ->select('survey', 's')
            ->condition(
                'meeting_id',
                self::getCurrentMeeting('meeting', 'm', $uuid)['id']
            )
            ->fields('s');
        $survey = $query_s->execute()->fetchAssoc();

        $query_i = $conn
            ->select('info', 'i')
            ->condition(
                'meeting_id',
                self::getCurrentMeeting('meeting', 'm', $uuid)['id']
            )
            ->fields('i');
        $info = $query_i->execute()->fetchAssoc();

        return new JsonResponse([
            'video_url' => self::getCurrentMeeting('meeting', 'm', $uuid)[
                'meeting_url_video'
            ],
            'question' => $question,
            'survey' => $survey,
            'info' => $info,
            'module_poll' => self::getModule($conn, $uuid, 'poll'),
            'module_question' => self::getModule($conn, $uuid, 'question'),
            'module_survey' => self::getModule($conn, $uuid, 'survey'),
            'module_info' => self::getModule($conn, $uuid, 'info'),
            'method' => 'GET',
            'status' => 200,
        ]);
    }
    /**
     * postQuestion function
     * @param $request
     * @return mixed
     */
    public function postQuestion(Request $request)
    {
        $conn = Database::getConnection();
        $json_string = \Drupal::request()->getContent();
        $decoded = \Drupal\Component\Serialization\Json::decode($json_string);
        $question = self::getQuestion($conn, $decoded['meeting_uuid']);

        $data = [
            'question_id' => $question['id'],
            'meeting_id' => $question['meeting_id'],
            'question' => $decoded['question'],
            'is_read' => 0,
            'status' => 0,
        ];

        $conn
            ->insert('question_submission')
            ->fields($data)
            ->execute();

        return new JsonResponse([
            'id' => $data,
            'method' => 'POST',
            'status' => 200,
        ]);
    }
    /**
     * getQuestion function
     * @param $conn
     * @param $uuid
     * @return mixed
     */
    public function getQuestion($conn, $uuid)
    {
        $query_q = $conn
            ->select('question', 'q')
            ->condition(
                'meeting_id',
                self::getCurrentMeeting('meeting', 'm', $uuid)['id']
            )
            ->fields('q');
        $question = $query_q->execute()->fetchAssoc();

        return $question;
    }

    /**
     * getModules  function
     * @param $conn
     * @param $uuid
     * @return mixed
     */
    public function getModule($conn, $uuid, $module)
    {
        $query = $conn
            ->select('meeting_module', 'mo')
            ->orderBy('order', 'asc')
            ->condition(
                'meeting_id',
                self::getCurrentMeeting('meeting', 'm', $uuid)['id']
            )
            ->condition('module', $module)
            ->fields('mo');
        $result = $query->execute()->fetchAssoc();

        return $result['id'] ? true : false;
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
