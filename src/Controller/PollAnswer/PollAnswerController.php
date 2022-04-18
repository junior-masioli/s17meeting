<?php

namespace Drupal\meeting\Controller\PollAnswer;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\meeting\Ajax\RemoveTrCommand;
use Drupal\Component\Render\FormattableMarkup;
use \Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Render\Markup;


class PollAnswerController extends ControllerBase
{

  public static function get($id, $ajax=false)
  {

    //create table header
    $header_table = array(
      'answer'   => array('data' => 'Poll answer', 'class' => ["col-9 text-center"]),
      'vote' => array('data' => 'Votes', 'class' => ["col-1 text-center"]),
      'edit' => array('data' => 'Edit', 'class' => ["col-1 text-center"]),
      'delte'     => array('data' => 'Delete', 'class' => ["col-1 text-center"]),
    );

    // get data from database
    $query = \Drupal::database()->select('poll_answer', 'a');
    $query->fields('a', ['id', 'answer', 'vote']);
    $query->condition('poll_id', $id);
    $results = $query->execute()->fetchAll();

    $rows = array();
    foreach ($results as $data) {

      $vote = $data->vote > 0 ? $data->vote : 0;

      //get data
      $rows[] = array(
        'answer'     => array('data' => $data->answer, 'class' => ["text-left"]),
        'vote'       => array('data' => $vote, 'class' => ["text-center"]),
        'edit'       => Self::buttonControl($data, null, '<i class=feather-edit></i>',  'btn--success answer-edit-ajax'),
        'delte'      => Self::buttonControl($data, 'meeting.poll_answer_delete', '<i class=feather-trash></i>',  'btn--danger remove-answer-ajax')
      );
    }

    // render table
    $form['table'] = [
      '#type'   => 'table',
      '#header' => $header_table,
      '#rows'   => $rows,
      '#empty'  => 'No data found'
    ];

    if($ajax){
      return new Response(render($form));
    }

    return $form;
  }

  public static function buttonControl($data, $url,  $label,  $color)
  {
    $url = $url ? Url::fromRoute($url)->toString() : null;

    $poll_choice_class = $color;
    return  array(
      'data' => new FormattableMarkup(
        '<button class="btn  btn-sm btn-sm d-flex m-auto '.$poll_choice_class.'" data-answer="@data" data-answer-url="' . $url . '">@name</button>',
        ['@name' => Markup::create($label), '@data' => json_encode($data)]
      )
    );
  }


  public function delete()
  {

    $id = \Drupal::request()->request->get('id');

    $query = \Drupal::database();
    $query->delete('poll_answer')
    ->condition('id', $id)
      ->execute();

    $response = new AjaxResponse();

    $response->addCommand(new RemoveTrCommand('.remove-answer-ajax'));

    return $response;
  }


}
