<?php

namespace Drupal\make_paths_relative\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

class MakePathsRelativeForm extends ConfigFormBase {
  /**
   * Implements FormBuilder::getFormId
   */
  public function getFormId() {
    return 'make_paths_relative_admin_settings';
  }

  /**
   * Implements ConfigFormBase::getEditableConfigNames
   */
  protected function getEditableConfigNames() {
    return ['make_paths_relative.settings'];
  }

  /**
   * Implements FormBuilder::buildForm
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $make_paths_relative_settings = $this->config('make_paths_relative.settings')->get('make_paths_relative');

    $page_scripts = 0;
    $page_styles = 0;
    $page_body = 0;
    if ($make_paths_relative_settings != '') {
      $make_paths_relative_settings = unserialize($make_paths_relative_settings);

      if (isset($make_paths_relative_settings['scripts']) && $make_paths_relative_settings['scripts'] == 1) {
        $page_scripts = 1;
      }

      if (isset($make_paths_relative_settings['styles']) && $make_paths_relative_settings['styles'] == 1) {
        $page_styles = 1;
      }

      if (isset($make_paths_relative_settings['body']) && $make_paths_relative_settings['body'] == 1) {
        $page_body = 1;
      }
    }

    $form['scripts'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Scripts (JS Files)'),
      '#default_value' => $page_scripts,
    );

    $form['styles'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Styles (CSS Files)'),
      '#default_value' => $page_styles,
    );

    $form['body'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Body (href, image src etc.)'),
      '#default_value' => $page_body,
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * Implements FormBuilder::submitForm().
   *
   * Serialize the user's settings and save it to the Drupal's config Table.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $make_relatives = array();
    
    $make_relatives['scripts'] = $values['scripts'];
    $make_relatives['styles'] = $values['styles'];
    $make_relatives['body'] = $values['body'];

    $make_relatives = serialize($make_relatives);
    
    $this->config('make_paths_relative.settings')
      ->set('make_paths_relative', $make_relatives)
      ->save();

    drupal_set_message($this->t('Your Settings have been saved.'), 'status');
  }
}
