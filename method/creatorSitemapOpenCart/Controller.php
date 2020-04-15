<?php

namespace method\creatorSitemapOpenCart;

use system\core\Logger;

/**
 * Class Controller
 *
 * @package method\test
 */
class Controller extends \system\core\abstracts\mvc\MVCController
{
    public function getRun():void
    {
	$categories = $this->model->getCategory();
	$products = $this->model->getProduct();

	$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
	$xml .= '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

	$xml .= '	<url>'."\n";
	$xml .= "            <loc>".$_ENV['OPVoipTechUrl']."</loc>"."\n";
	$xml .= '	</url>'."\n";

	foreach($categories as $category) {
	    $xml .= '	<url>'."\n";
	    $xml .= "            <loc>".$category['url']."</loc>"."\n";
	    $xml .= "            <lastmod>".date('Y-m-d',strtotime($category['date_modified']))."</lastmod>"."\n";
	    $xml .= '	</url>'."\n";
	}

	foreach($products as $product) {
	    $xml .= '	<url>'."\n";
	    $xml .= "            <loc>".$product['url']."</loc>"."\n";
	    $xml .= "            <lastmod>".date('Y-m-d',strtotime($product['date_modified']))."</lastmod>"."\n";
	    $xml .= '	</url>'."\n";
	}

        $xml .= '</urlset>'."\n";

        $this->view->setResult($xml);
    }
}