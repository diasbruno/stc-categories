<?php

use STC\Config;

class CategoryComponent
{
  public function build($files)
  {
    $categories = [];
    $files = $files->get_all();
    $cat = '';
    foreach($files as $file) {
      if ($file['type'] != 'post') continue;

      if (is_array($file['categories'])) {
        foreach($file['categories'] as $cat) {
          if (!array_key_exists($cat, $categories)) {
            $categories[$cat] = [];
          }
          $categories[$cat][] = $file;
        }
      } else {
        $cat = $file['categories'];
        if (!array_key_exists($cat, $categories)) {
          $categories[$cat] = [];
        }
        $categories[$cat][] = $file;
      }
    }

    Config::db()->store('categories_list', $categories);
  }
}
