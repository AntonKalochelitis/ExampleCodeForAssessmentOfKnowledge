<?php

namespace method\creatorSitemapOpenCart;

use system\core\db\DatabaseMysqli;

/**
 *
 *
 * Class Model
 *
 * @package method\test
 */
class Model extends \system\core\abstracts\mvc\MVCModel
{
    /**
    *
    */
    public function dbConnect()
    {
        return new DatabaseMysqli();
    }

    /**
    *
    */
    public function getCategory():array
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "SELECT"
	    ." oc.date_modified,"
	    ." CONCAT('".$_ENV['OPVoipTechUrl']."', ocsu.keyword) AS url"
	    ." FROM `oc_category` oc"
	    ." LEFT JOIN `oc_seo_url` ocsu ON ocsu.`query`=CONCAT('category_id=', oc.`category_id`)"
	    ." WHERE"
	    ." oc.status = 1";
        $this->db->query($query);

        return $this->db->row();

    }

    /**
    *
    */
    public function getProduct():array
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "SELECT"
	    ." op.date_modified,"
	    ." CONCAT('".$_ENV['OPVoipTechUrl']."', ocsu.keyword) AS url"
	    ." FROM `oc_product` op"
	    ." LEFT JOIN `oc_seo_url` ocsu ON ocsu.`query`=CONCAT('product_id=', op.`product_id`)"
	    ." WHERE"
	    ." op.status = 1"
	    ." AND"
	    ." op.stock_status_id=7";
        $this->db->query($query);

        return $this->db->row();
    }
}