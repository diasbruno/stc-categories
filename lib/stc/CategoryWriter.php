<?php

namespace STC;

use Cocur\Slugify\Slugify;

class CategoryWriter
{
  const TYPE = 'post';

  protected $slugify;

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

    $t = Application::templates()->templates_path() . '/';

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
    @mkdir(Application::site()->public_folder() . '/category', 0755, true);
  }

  public function execute($files)
  {
    printLn('=> CategoryWriter.');
    $this->make_categories_directory();

    $categories = Application::db()->retrieve('categories_list');

    $writer = new DataWriter();

    foreach($categories as $cat => $posts) {
      $tmpl = $this->make_data($cat, $posts);
      $writer->write($tmpl['slug'], 'index.html', $tmpl['html']);
    }
  }
}
