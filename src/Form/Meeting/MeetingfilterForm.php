<?php
namespace Drupal\meeting\Form\Meeting;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides the form for filter Students.
 */
class MeetingfilterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'meeting_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $filed_search = trim(\Drupal::request()->query->get('search'));
    $form['link']= [
      '#title' => $this->t('Add new Meeting'),
      '#type' => 'link',
      '#url' => Url::fromRoute('meeting.add_form'),
      '#attributes' => ['class' => 'button button--primary button--action']
    ];

    $form['filters'] = [
      '#type'  => 'container',
      '#open'  => true,
      '#attributes' => ['class' => 'flex-form']
    ];

    $form['filters']['search'] = [
        '#title'         => 'Search here',
        '#type'          => 'search',
        '#default_value' => $filed_search ? $filed_search : '',
        '#attributes' => ['placeholder' => 'Search here']
    ];
    $form['filters']['actions'] = [
        '#type'       => 'actions'
    ];

    $form['filters']['actions']['submit'] = [
        '#type'  => 'submit',
        '#value' => $this->t('Filter')
    ];

    $form['filters']['actions']['link']= [
      '#title' => $this->t('Reset'),
      '#type' => 'link',
      '#url' => Url::fromRoute('meeting.display_data'),
      '#attributes' => ['class' => 'button button--primary']
    ];


    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

	  if ( $form_state->getValue('search') == "") {
		    $form_state->setErrorByName('from', $this->t('You must enter a valid search'));
     }

  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {
	  $field = $form_state->getValues();
    $search = $field["search"];
    $url = \Drupal\Core\Url::fromRoute('meeting.display_data')
          ->setRouteParameters(array('search'=> $search));
    $form_state->setRedirectUrl($url);
  }

}
