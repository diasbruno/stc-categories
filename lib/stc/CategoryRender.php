<?php

use STC\Config;
use STC\DataWriter;
use Cocur\Slugify\Slugify;

class CategoryRender
{
  const TYPE = 'post';

  private $slugify;

  public function __construct()
  {
    $this->slugify = new Slugify();
  }

  public function filter_by_type($file)
  {
    return $file['type'] == CategoryRender::TYPE;
  }

  public function make_item_slug($str)
  {	
    return $this->slugify->slugify($str);
  }

  public function make_category_slug($cat)
  {
    return '/category/' . $this->slugify->slugify($cat);
  }

  private function make_data($cat, $posts)
  {
    $tmpl = $posts;

    $t = Config::templates()->templates_path() . '/';

    $tmpl['slug'] = $this->make_category_slug($cat);
    printLn('===> Category link: ' . $tmpl['slug']);

    $fixed_posts = [];
    foreach($posts as $post) {
      $post['slug'] = $this->make_item_slug($post['title']);
      $fixed_posts[] = $post;
    }

    $tmpl['html'] = view($t . 'categories.phtml', [
      'name' => $cat,
      'posts' => $fixed_posts,
    ]);

    return $tmpl;
  }
  
  private function make_categories_directory()
  {
    printLn('==> Make categories directory.');
    @mkdir(Config::site()->public_folder() . '/category', 0755, true);
  }

  public function render($files)
  {
    printLn('=> CategoryRender.');
    $this->make_categories_directory();

    $categories = Config::db()->retrieve('categories_list');

    $writer = new DataWriter();

    foreach($categories as $cat => $posts) {
      $tmpl = $this->make_data($cat, $posts);
      $writer->write($tmpl['slug'], 'index.html', $tmpl['html']);
    }
  }
}