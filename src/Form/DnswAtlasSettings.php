<?php

namespace Drupal\dnsw_atlas\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DnswAtlasSettings.
 */
class DnswAtlasSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dnsw_atlas_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return 'dnsw_atlas.settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['atlas_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Atlass Api Key'),
      '#description' => $this->t('Atlas key provided'),
      '#maxlength' => 64,
      '#size' => 64,
      '#required' => TRUE,
      '#default_value' => \Drupal::configFactory()
        ->getEditable('dnsw_atlas.settings')
        ->get('atlas_api_key'),
    ];

    $form['atlas_results_per_page'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Atlass Results Per Page'),
      '#description' => $this->t('How many results to return'),
      '#maxlength' => 64,
      '#size' => 64,
      '#required' => TRUE,
      '#default_value' => \Drupal::configFactory()
        ->getEditable('dnsw_atlas.settings')
        ->get('atlas_results_per_page'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save config result.
    $values = $form_state->getValues();
    \Drupal::configFactory()->getEditable('dnsw_atlas.settings')
      ->set('atlas_api_key', $values['atlas_api_key'])
      ->set('atlas_results_per_page', $values['atlas_results_per_page'])
      ->save();
  }

}
