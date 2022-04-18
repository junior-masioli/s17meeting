<?php

namespace Drupal\meeting\Controller\Survey;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\meeting\Ajax\RemoveTrCommand;
use Drupal\Component\Render\FormattableMarkup;
use \Symfony\Component\HttpFoundation\Response;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Link;
use Drupal\Core\Render\Markup;

class SurveyController extends ControllerBase
{

  public static function get($id, $ajax=false)
  {

    //create table header
    $header_table = array(
      'action'          => array('data' => 'Actions', 'class' => ["col-1 text-center"]),
      'poll_question'   => array('data' => 'Poll question', 'class' => ["col-7 text-center"]),
      'allow'           => array('data' => 'Allow multiple choice', 'class' => ["col-1 text-center"]),
      'poll_control'    => array('data' => 'Poll control', 'class' => ["col-1 text-center"]),
      'results_control' => array('data' => 'Poll control', 'class' => ["col-1 text-center"]),
      'participant'     => array('data' => 'Participant count', 'class' => ["col-1 text-center"]),
    );

    // get data from database
    $query = \Drupal::database()->select('poll', 'p');
    $query->fields('p', ['id','poll_question', 'poll_allow_multiple_choice', 'poll_activate_poll', 'poll_show_results']);
    $query->condition('meeting_id', $id);
    $query->orderBy('id','desc');
    $results = $query->execute()->fetchAll();

    $rows = array();
    foreach ($results as $data) {



      //get data
      $rows[] = array(
        'edit'            => Self::actionEdit($data),
        'poll_question'   => array('data' => $data->poll_question, 'class' => ["text-left"]),
        'allow'           => Self::buttonControl($data, 'meeting.poll_allow', $data->poll_allow_multiple_choice),
        'poll_control'    => Self::buttonControl($data, 'meeting.poll_poll_control', $data->poll_activate_poll),
        'results_control' => Self::buttonControl($data, 'meeting.poll_result_controll', $data->poll_show_results),
        'participant'     => array('data' => '10', 'class' => ["col-1 text-center"]),
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


  public static function actionEdit($data)
  {
    $url_delete = Url::fromRoute('meeting.poll_delete', ['id' => $data->id]);

    $link_url = Url::fromRoute('meeting.poll_answer_modal', ['id' => $data->id]);;
    $link_url->setOptions([
      'attributes' => [
        'class' => ['use-ajax'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode(['width' => '980'])
      ],

    ]);

    return  array(
      'data' => new FormattableMarkup(
        '<div class="action-dropdown">
              <div class="action-link-dropdown">&#8942;</div>
              <div class="action-item-dropdown">
                '.Link::fromTextAndUrl(Markup::create('<i class=feather-file-text></i> @name_answer'), $link_url)->toString().'
                <button class="remove-poll" data-form="poll-form"  data-url=' . $url_delete->toString() . '><i class="feather-trash"></i> @name_delete</button>
              </div>
           </div>
           <button class="btn btn-sm btn--primary edit-poll-ajax" data-poll="@data_edit">@name_edit</button>
           ',
        [
          '@name_edit' => 'Edit', '@data_edit' => json_encode($data),
          '@name_answer' => 'Answers', '@data_answer' => json_encode($data),
          '@name_delete' => 'Delete'
        ]
      )
    );
  }

  public static function buttonControl($data, $url, $field)
  {
    $url_poll_control = Url::fromRoute($url);
    $poll_choice = $field == 1 ? "Yes" : "No";
    $poll_choice_class = $field == 1 ? "btn--success" : "btn--danger";
    return  array(
      'data' => new FormattableMarkup(
        '<button class="btn poll-control-ajax btn-sm ' . $poll_choice_class . '" data-id="@id" data-url="' . $url_poll_control->toString() . '">@name</button>',
        ['@name' => $poll_choice, '@id' => $data->id]
      )
    );
  }


  public function delete($id)
  {

    $query = \Drupal::database();
    $query->delete('poll')
    ->condition('id', $id)
      ->execute();

    $response = new AjaxResponse();

    $response->addCommand(new RemoveTrCommand('.remove-ajax'));

    return $response;
  }

}
