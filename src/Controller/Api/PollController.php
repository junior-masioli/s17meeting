<?php

namespace Drupal\meeting\Controller\Api;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Request;

class PollController extends ControllerBase
{
    public function get($uuid)
    {
        // get data from database
        $query = \Drupal::database()->select('poll', 'p');
        $query->fields('p', [
            'id',
            'poll_question',
            'poll_allow_multiple_choice',
            'poll_activate_poll',
            'poll_show_results',
        ]);
        $query->condition(
            'meeting_id',
            self::getCurrentMeeting('meeting', 'm', $uuid)
        );
        $query->condition('poll_activate_poll', 1);
        $query->orderBy('id', 'desc');
        $results = $query->execute()->fetchAll();

        $polls = [];
        foreach ($results as $data) {
            $polls[] = [
                'poll_id' => $data->id,
                'poll_question' => $data->poll_question,
                'poll_allow_multiple_choice' =>
                    $data->poll_allow_multiple_choice,
                'poll_answer' => self::getPollAnswers($data->id),
                'poll_answer_count' => self::verifyPollVote(
                    self::getCurrentMeeting('meeting', 'm', $uuid),
                    $data->id
                ),
            ];
        }

        return new JsonResponse([
            'data' => $polls,
            'method' => 'GET',
            'status' => 200,
        ]);
    }

    /**
     * postPollVote function
     * @param $request
     * @return mixed
     */
    public function postPollVote(Request $request)
    {
        $conn = Database::getConnection();
        $json_string = \Drupal::request()->getContent();
        $decoded = \Drupal\Component\Serialization\Json::decode($json_string);
        $current_user = \Drupal::currentUser();
        $user_id = $current_user->id();
        $user_mail = $current_user->getEmail();
        $user_display_name = $current_user->getDisplayName();

        if ($decoded['poll_allow_multiple_choice'] > 0) {
            $votes = explode(',', $decoded['poll_opt']);
            foreach ($votes as $vote) {
                $data = [
                    'meeting_id' => self::getCurrentMeeting(
                        'meeting',
                        'm',
                        $decoded['meeting_uuid']
                    ),
                    'poll_id' => $decoded['poll_id'],
                    'poll_answer_id' => $vote,
                    'user_id' => $user_id,
                    'user_name' => $user_display_name,
                ];

                $conn
                    ->insert('poll_answer_user')
                    ->fields($data)
                    ->execute();
            }
        } else {
            $data = [
                'meeting_id' => self::getCurrentMeeting(
                    'meeting',
                    'm',
                    $decoded['meeting_uuid']
                ),
                'poll_id' => $decoded['poll_id'],
                'poll_answer_id' => $decoded['poll_opt'],
                'user_id' => $user_id,
                'user_name' => $user_display_name,
            ];

            $conn
                ->insert('poll_answer_user')
                ->fields($data)
                ->execute();
        }

        return new JsonResponse([
            'message' => 'Success',
            'method' => 'POST',
            'status' => 200,
        ]);
    }

    public function verifyPollVote($meeting_id, $poll_id)
    {
        $current_user = \Drupal::currentUser();
        $user_id = $current_user->id();

        $conn = Database::getConnection();
        $query = $conn
            ->select('poll_answer_user', 'pa')
            ->condition('meeting_id', $meeting_id)
            ->condition('poll_id', $poll_id)
            ->condition('user_id', $user_id)
            ->fields('pa', ['id']);
        $data = $query->execute()->fetchAssoc();
        if ($data) {
            return 1;
        }
        return 0;
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getPollAnswers($poll_id)
    {
        $query = \Drupal::database()->select('poll_answer', 'a');
        $query->fields('a', ['id', 'answer', 'vote']);
        $query->condition('poll_id', $poll_id);
        $response = $query->execute()->fetchAll();

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

        return $data['id'];
    }
}
