<?php  
  $box_site_footer_cache_id = cache::cache_id('box_category_tree', array('language', 'login', 'region'));
  if (cache::capture($box_site_footer_cache_id, 'file')) {
    
    $box_site_footer = new view();
    
    $box_site_footer->snippets = array(
      'categories' => array(),
      'manufacturers' => array(),
      'pages' => array(),
    );
    
  // Categories
    $categories_query = database::query(
      "select c.id, if(ci.name, ci.name, alt_ci.name) as name
      from ". DB_TABLE_CATEGORIES ." c
      left join ". DB_TABLE_CATEGORIES_INFO ." ci on (ci.category_id = c.id and ci.language_code = '". language::$selected['code'] ."')
      left join ". DB_TABLE_CATEGORIES_INFO ." alt_ci on (alt_ci.category_id = c.id and alt_ci.language_code = 'en')
      where status
      and parent_id = '0'
      order by c.priority asc, ci.name asc;"
    );
    
    $i = 0;
    while ($category = database::fetch($categories_query)) {
      if (++$i < 10) {
        $box_site_footer->snippets['categories'][] = array(
          'id' => $category['id'],
          'name' => $category['name'],
          'href' => document::href_ilink('category', array('category_id' => $category['id'])),
        );
      } else {
        $box_site_footer->snippets['categories'][] = array(
          'id' => 0,
          'name' => language::translate('title_more', 'More'),
          'href' => document::href_ilink('categories'),
        );
        break;
      }
    }
    
  // Manufacturers
    $manufacturers_query = database::query(
      "select m.id, m.name
      from ". DB_TABLE_MANUFACTURERS ." m
      where status
      order by m.name asc
      limit 1;"
    );
    
    $i = 0;
    while ($manufacturer = database::fetch($manufacturers_query)) {
      if (++$i < 10) {
        $box_site_footer->snippets['manufacturers'][] = array(
          'id' => $manufacturer['id'],
          'name' => $manufacturer['name'],
          'href' => document::href_ilink('manufacturer', array('manufacturer_id' => $manufacturer['id'])),
        );
      } else {
        $box_site_footer->snippets['manufacturers'][] = array(
          'id' => 0,
          'name' => language::translate('title_more', 'More'),
          'href' => document::href_ilink('manufacturers'),
        );
        break;
      }
    }
    
    $pages_query = database::query(
      "select p.id, pi.title from ". DB_TABLE_PAGES ." p
      left join ". DB_TABLE_PAGES_INFO ." pi on (p.id = pi.page_id and pi.language_code = '". language::$selected['code'] ."')
      where status
      and find_in_set('information', dock)
      order by p.priority, pi.title;"
    );
    while ($page = database::fetch($pages_query)) {
      $box_site_footer->snippets['pages'][] = array(
        'id' => $page['id'],
        'title' => $page['title'],
        'href' => document::href_ilink('information', array('page_id' => $page['id'])),
      );
    }
    
    echo $box_site_footer->stitch('views/box_site_footer');
    
    cache::end_capture($box_site_footer_cache_id);
  }
?>