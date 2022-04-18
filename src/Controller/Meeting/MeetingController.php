<?php

namespace Drupal\meeting\Controller\Meeting;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;


class MeetingController extends ControllerBase
{
  public function index()
  {
    $header = $this->tableHeader();
    $rows   = $this->tableRows($header);

    $form['form'] = $this->formBuilder()->getForm('Drupal\meeting\Form\Meeting\MeetingfilterForm');
    // render table
    $form['table'] = [
      '#type'   => 'table',
      '#header' => $header,
      '#rows'   => $rows,
      '#empty'  => $this->t('No data found'),
    ];
    return $form;
  }

  public function tableHeader()
  {
    //create table header
    $header = array(
      array(
        'data'  => $this->t('Meeting name'),
        'field' => 'm.meeting_name',
        'class' => ['col-11 text-center'],
      ),
      array(
        'data'  => $this->t('Operations'),
        'class' => ['col-1 text-center'],
      ),
    );

    return $header;
  }

  public function tableRows($header)
  {
    // get data from database
    $query = \Drupal::database()->select('meeting', 'm');
    $query->fields('m');
    $query->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header);

    if (\Drupal::request()->query->get('search')) {
      $orGroup = $query->orConditionGroup()
        ->condition('m.id', trim(\Drupal::request()->query->get('search')))
        ->condition('m.meeting_name', trim(\Drupal::request()->query->get('search')));
      $query->condition($orGroup);
    }
    $results = $query->execute();

    $rows = array();
    foreach ($results as $data) {
      $row = [];
      $row['title']['data'] = [
        '#type' => 'inline_template',
        '#template' => '<div class="block-filter-text-source">{{ label }}</div>',
        '#context' => [
          'label' => $data->meeting_name,
        ],
      ];

      $links['edit'] = [
        'title' => $this->t('Edit'),
        'url' => Url::fromRoute('meeting.edit_form', ['id' => $data->id]),
      ];

      $links['delete'] = [
        'title' => $this->t('Delete'),
        'url' => Url::fromRoute('meeting.delete_form', ['id' => $data->id]),
      ];

      $row[] = [
        'data' => [
          '#prefix' => '<div class="d-flex justify-content-center">',
          '#type' => 'operations',
          '#links' => $links,
          '#suffix' => '</div>',
        ],
      ];

      $rows[] = $row;
    }
    return $rows;
  }
}
