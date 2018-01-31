<?php

namespace Drupal\dnsw_atlas\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dnsw_atlas\DnswAtlasApi;

/**
 * Class DnswAtlasForm.
 */
class DnswAtlasForm extends FormBase {

  /**
   * Drupal\dnsw_atlas\DnswAtlasApi definition.
   *
   * @var \Drupal\dnsw_atlas\DnswAtlasApi
   */
  protected $dnswAtlasApi;

  /**
   * Constructs a new DnswAtlasForm object.
   */
  public function __construct(DnswAtlasApi $dnsw_atlas_api) {
    $this->dnswAtlasApi = $dnsw_atlas_api;
    $key = \Drupal::config('dnsw_atlas.settings')->get('atlas_api_key');
    $this->dnswAtlasApi->setApiKey($key);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dnsw_atlas.api')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dnsw_atlas_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // @todo: Get options using Regions listing from API using Api Service.
    $form['region'] = [
      '#type' => 'select',
      '#title' => $this->t('NSW Regions'),
      '#description' => $this->t('Please select a region'),
      '#options' => [
        '' => 'Show All',
        'Blue Mountains' => 'Blue Mountains',
        'Central Coast' => 'Central Coast',
        'Country NSW' => 'Country NSW',
        'Hunter' => 'Hunter',
        'Lord Howe Island' => 'Lord Howe Island',
        'North Coast' => 'North Coast',
        'Outback NSW' => 'Outback NSW',
        'Snowy Mountains' => 'Snowy Mountains',
        'South Coast' => 'South Coast',
      ],
      '#default_value' => \Drupal::request()->query->get('region'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Redirect to controller with GET parameters.
    $url = Url::fromRoute('dnsw_atlas.dnsw_atlas_controller',
      ['region' => $form_state->getValue('region')]
    );
    return $form_state->setRedirectUrl($url);

  }

}
