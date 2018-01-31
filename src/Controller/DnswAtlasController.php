<?php

namespace Drupal\dnsw_atlas\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dnsw_atlas\DnswAtlasApi;

/**
 * Class DnswAtlasController.
 */
class DnswAtlasController extends ControllerBase {

  private $region;

  private $pager;

  private $resultsPerPage;

  private $apiKey;

  /**
   * Drupal\dnsw_atlas\DnswAtlasApi definition.
   *
   * @var \Drupal\dnsw_atlas\DnswAtlasApi
   */
  protected $dnswAtlasApi;

  /**
   * Constructs a new DnswAtlasController object.
   */
  public function __construct(DnswAtlasApi $dnsw_atlas_api) {
    $this->dnswAtlasApi = $dnsw_atlas_api;
    $this->region = \Drupal::request()->query->get('region');

    // Set up pager.
    $this->resultsPerPage = \Drupal::config('dnsw_atlas.settings')->get('atlas_results_per_page');
    $this->pager = \Drupal::request()->query->get('page');
    if (empty($this->pager) || $this->pager == 0) {
      $this->pager = 1;
    }
    else {
      $this->pager++;
    }

    $this->apiKey = \Drupal::config('dnsw_atlas.settings')->get('atlas_api_key');
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
   * Display Results.
   *
   * Make the request to the api and display the results.
   *
   * @return array
   *   Return drupal render array.
   */
  public function displayResults() {
    $this->dnswAtlasApi->setApiKey($this->apiKey);
    $query = [];
    if (!empty($this->region)) {
      $query['rg'] = $this->region;
    }
    $query['pge'] = $this->pager;
    $query['size'] = $this->resultsPerPage ? $this->resultsPerPage : 10;
    $results = $this->dnswAtlasApi->request('get', $query);

    // Initialise pager.
    pager_default_initialize($results['body']['numberOfResults'], $query['size']);

    $build = [];
    $build[]['form'] = $form = \Drupal::formBuilder()->getForm('Drupal\dnsw_atlas\Form\DnswAtlasForm');
    $build[] = [
      '#theme'      => 'dnsw_atlas',
      '#results' => $results['body'],
    ];
    $build[] = ['#type' => 'pager'];
    return $build;
  }

}
