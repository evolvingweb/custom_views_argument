<?php

namespace Drupal\custom_views_argument\Plugin\views\argument;

use Drupal\taxonomy\Plugin\views\argument\Taxonomy as ArgumentBase;

/**
 * Defines a filter for Taxonomy Term Slugs.
 *
 * @ingroup views_argument_handlers
 *
 * @ViewsArgument("custom_taxonomy_slug")
 */
class CustomTaxonomySlug extends ArgumentBase {

  /**
   * {@inheritdoc}
   */
  public function setArgument($arg) {
    // If we are not dealing with the exception argument, example "all".
    if ($this->isException($arg)) {
      return parent::setArgument($arg);
    }
    // Convert slug to taxonomy term ID.
    //
    // Modify this if you do not want to allow integer IDs in the URL and
    // force only URL slugs in the URL.
    $tid = is_numeric($arg)
      ? $arg : $this->convertSlugToTid($arg);
    $this->argument = (int) $tid;
    return $this->validateArgument($tid);
  }

  /**
   * Get taxonomy term ID from a slug.
   *
   * You might have to implement some constraints or modules to ensure that
   * the taxonomy term slugs are globally unique.
   *
   * @return int
   *   Taxonomy term ID.
   */
  protected function convertSlugToTid($slug) {
    // Build query to get taxonomy term.
    $query = $this->termStorage
      ->getQuery()
      ->condition('field_slug', $slug);
    // Use only allowed vocabularies.
    if (isset($this->options['specify_validation'])
      && isset($this->options['validate_options']['bundles'])) {
      $query->condition('vid', $this->options['validate_options']['bundles']);
    }
    // Fetch term IDs and return them.
    $tids = $query->execute();
    return $tids ? reset($tids) : FALSE;
  }

}
