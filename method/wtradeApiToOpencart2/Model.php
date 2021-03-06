<?php

namespace method\wtradeApiToOpencart2;

use Medoo\Medoo;
use system\core\db\DatabaseMysqli;
use DevelopingW\WTrade\Classes\ApiCommand;

include_once(DEFAULT_DIR.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'configuration.php');

/**
 *
 *
 * Class Model
 *
 * @property DatabaseMysqli db
 *
 * @package method\wtradeApiToOpencart2
 */
class Model extends \system\core\abstracts\mvc\MVCModel
{
    /**
     * @return null|DatabaseMysqli
     */
    public function dbConnect()
    {
        return new DatabaseMysqli();
//        return new Medoo([
//            'database_type' => 'mysql',
//            'database_name' => (new \JConfig)->db,
//            'server' => 'localhost',
//            'username' => (new \JConfig)->user,
//            'password' => (new \JConfig)->password,
//        ]);
    }

    /**
     * @return array
     */
    public function getCurrencyExchangeFromAPI():array
    {
        $apiCurrencyExchangeJson = ApiCommand::getApiCurrencyExchange(
            ['USD', 'EUR', 'RUB'],
            $_ENV['apiWTradeUrl'],
            $_ENV['apiWTradeName'],
            $_ENV['apiWTradeKey']
        );

        return (array)json_decode($apiCurrencyExchangeJson, 1);
    }

    /**
     *
     */
    public function createTableIfNotExists()
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "CREATE TABLE IF NOT EXISTS `WTrade_offer_link_product` ("
            ."`docObjId` int(11) NOT NULL,"
            ."`offerId` int(11) NOT NULL,"
            ."`productId` int(11) NOT NULL,"
            ."`create_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',"
            ."`update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
            .") ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        $this->db->query($query);

        $query = "ALTER TABLE `WTrade_offer_link_product` ADD UNIQUE KEY `docObjId_offerId` (`docObjId`, `offerId`);";
        $this->db->query($query);
    }

    /**
     * @param int $docObjId
     * @param int $offerId
     * @param int $productId
     */
    public function insertOfferLinkProduct(int $docObjId, int $offerId, int $productId)
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "INSERT INTO `ocvoiptech`.`WTrade_offer_link_product`"
            ." (`docObjId`, `offerId`, `productId`, `create_date`, `update_date`)"
            ." VALUES "
            ."('".$docObjId."', '".$offerId."', '".$productId."', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);";

        $this->db->query($query);
    }

    /**
     * @return array
     */
    public function getOfferFromAPI():array
    {
        $resultJson = ApiCommand::getApiDocumentProducts(
            106,
            $_ENV['apiWTradeUrl'],
            $_ENV['apiWTradeName'],
            $_ENV['apiWTradeKey']
        );

        if (!empty($resultJson)) {
            return json_decode($resultJson, 1);
        }

        return [];
    }

    /**
     * @return array
     */
    public function getVirtuemartProduct():array
    {
        $this->db->connect(
            (new \JConfig)->host,
            (new \JConfig)->user,
            (new \JConfig)->password,
            (new \JConfig)->db
        );

        $query = "SELECT
vvp.`virtuemart_product_id` as item_id,
vvpc.`virtuemart_category_id` as categories_id,
vvmrr.`mf_name` as brand_name,
vvprr.`product_name` as name,
vvp.`product_sku` as model,
vvprr.`product_s_desc` as description,
CONCAT('http://voiptech.com.ua/', vvprr.`slug`,'.html') as url,
ROUND(vvpp.`product_price` / vvc.`currency_exchange_rate`,2) as price_ua,
CONCAT('http://voiptech.com.ua/', (
        SELECT
                vvm.`file_url`
        FROM `vp_virtuemart_product_medias` vvpme
        JOIN `vp_virtuemart_medias` vvm ON vvpme.`virtuemart_media_id`=vvm.`virtuemart_media_id`
        WHERE vvpme.`virtuemart_product_id`=vvp.`virtuemart_product_id`
        ORDER BY vvpme.`virtuemart_media_id` DESC
        LIMIT 1
)) as img
FROM `vp_virtuemart_products` vvp
JOIN `vp_virtuemart_products_ru_ru` vvprr ON vvp.`virtuemart_product_id` = vvprr.`virtuemart_product_id`
JOIN `vp_virtuemart_product_categories` vvpc ON vvp.`virtuemart_product_id` = vvpc.`virtuemart_product_id`
JOIN `vp_virtuemart_category_categories` vvcc ON vvpc.`virtuemart_category_id` = vvcc.`id`
JOIN `vp_virtuemart_product_manufacturers` vvpm ON vvp.`virtuemart_product_id` = vvpm.`virtuemart_product_id`
JOIN `vp_virtuemart_manufacturers_ru_ru` vvmrr ON vvpm.virtuemart_manufacturer_id = vvmrr.`virtuemart_manufacturer_id`
JOIN `vp_virtuemart_product_prices` vvpp ON vvp.`virtuemart_product_id` = vvpp.`virtuemart_product_id`
JOIN `vp_virtuemart_currencies` vvc ON vvpp.`product_currency` = vvc.`virtuemart_currency_id`
WHERE
vvp.`published` = 1
AND
vvcc.`category_parent_id` = 0";

        $this->db->query($query);

        return $this->db->row();
    }

    /**
     * @param $id
     *
     * @return array|null
     */
    public function getProductDescriptionById($id):?array
    {
        $this->db->connect(
            (new \JConfig)->host,
            (new \JConfig)->user,
            (new \JConfig)->password,
            (new \JConfig)->db
        );

        $query = "SELECT
vvprr.product_name,
vvprr.customtitle as meta_title,
vvprr.metadesc as meta_description,
vvprr.metakey as meta_key,
vvprr.product_s_desc,
vvprr.product_desc,
vvprr.slug
FROM `vp_virtuemart_products_ru_ru` vvprr
WHERE
vvprr.virtuemart_product_id = '".$id."'";
        $this->db->query($query);

        return $this->db->row();
    }

    /**
     * @param $id
     *
     * @return array|null
     */
    public function getProductImagesById($id):?array
    {
        $this->db->connect(
            (new \JConfig)->host,
            (new \JConfig)->user,
            (new \JConfig)->password,
            (new \JConfig)->db
        );

        $query = "SELECT
CONCAT('http://voiptech.com.ua/', vvm.`file_url`) as image
FROM `vp_virtuemart_product_medias` vvpme
JOIN `vp_virtuemart_medias` vvm ON vvpme.`virtuemart_media_id`=vvm.`virtuemart_media_id`
WHERE vvpme.`virtuemart_product_id`='".$id."'
ORDER BY vvpme.`virtuemart_media_id` DESC";
        $this->db->query($query);

        return $this->db->row();
    }

    /**
     * @param $manufacturer
     * @param $sku
     *
     * @return int
     */
    public function getCheckProductOpencart($manufacturer, $sku)
    {
        $result = $this->getProductOpencart($manufacturer, $sku);

        return (!empty($result))?1:0;
    }

    /**
     * @param $brand_name
     *
     * @return array|null
     */
    public function getManufacturerProductOpencart($brand_name):?array
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "SELECT * FROM `oc_manufacturer` WHERE `name`='".$brand_name."'";
        $this->db->query($query);

        return $this->db->row();
    }

    /**
     * @param $manufacturerId
     * @param $model
     * @param $sku
     * @param $price
     *
     * @return int|null
     */
    public function setInsertProductOpencart($manufacturerId, $model, $sku, $price):?int
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "INSERT INTO
`ocvoiptech`.`oc_product`
(
`model`,
`sku`,
`upc`,
`ean`,
`jan`,
`isbn`,
`mpn`,
`location`,
`quantity`,
`stock_status_id`,
`image`,
`manufacturer_id`,
`shipping`,
`price`,
`points`,
`tax_class_id`,
`date_available`,
`weight`,
`weight_class_id`,
`length`,
`width`,
`height`,
`length_class_id`,
`subtract`,
`minimum`,
`sort_order`,
`status`,
`viewed`,
`date_added`,
`date_modified`,
`noindex`
) VALUES (
'".$model."',
'".$sku."',
'',
'',
'',
'',
'',
'',
'1',
'7',
NULL,
'".((!empty($manufacturerId))?$manufacturerId:0)."',
'0',
'".$price."',
'0',
'10',
'".date('Y-m-d')."',
'0.00000000',
'1',
'0.00000000',
'0.00000000',
'0.00000000',
'1',
'0',
'1',
'1',
'1',
'0',
'".date('Y-m-d H:i:s')."',
'".date('Y-m-d H:i:s')."',
'1'
)";
        $this->db->query($query);

        return $this->db->getID();
    }

    /**
     * @param $id
     */
    public function setUpdateProductModelOpencart($id)
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $count = mb_strlen($id);
        $idName = str_repeat('0', 5 - $count);

        $query = "UPDATE `ocvoiptech`.`oc_product` SET `model` = '".$idName.$id."'WHERE `oc_product`.`product_id` = '".$id."';";
        $this->db->query($query);
    }

    /**
     *
     **/
    public function setUpdateProductOpencart($id, $pathImageFile)
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "UPDATE `ocvoiptech`.`oc_product` SET `image` = '".$pathImageFile."' WHERE `oc_product`.`product_id` = '".$id."';";

        $this->db->query($query);
    }

    /**
     * @param $productId
     * @param $pathImageFile
     * @param $i
     *
     * @return int|null
     */
    public function setInsertProductImageOpencart($productId, $pathImageFile, $i):?int
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "INSERT INTO `ocvoiptech`.`oc_product_image` (`product_id`, `image`, `sort_order`) VALUES ('".$productId."', '".$pathImageFile."', '".$i."')";
        $this->db->query($query);

        return $this->db->getID();
    }

    /**
     * @param $productId
     * @param $product_name
     * @param $product_s_desc
     * @param $product_desc
     * @param $meta_title
     * @param $meta_description
     * @param $meta_key
     * @param $slug
     *
     * @return int|null
     */
    public function setInsertProductDescriptionOpencart($productId, $product_name, $product_s_desc, $product_desc, $meta_title, $meta_description, $meta_key, $slug):?int
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "INSERT INTO `ocvoiptech`.`oc_product_description`
(
`product_id`,
`language_id`,
`name`,
`description_preview`,
`description`,
`tag`,
`meta_title`,
`meta_description`,
`meta_keyword`,
`meta_h1`
) VALUES (
'".$productId."',
'1',
'".$product_name."',
'".$product_s_desc."',
'".$product_desc."',
'',
'".$meta_title."',
'".$meta_description."',
'".$meta_key."',
'".$product_name."'
)";
        $this->db->query($query);

        return $this->db->getID();
    }

    /**
     * @param $product_id
     *
     * @return int|null
     */
    public function setInsertProductToLayoutOpencart($product_id):?int
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "INSERT INTO `ocvoiptech`.`oc_product_to_layout` (`product_id`, `store_id`, `layout_id`) VALUES ('".$product_id."', '0', '0')";
        $this->db->query($query);

        return $this->db->getID();
    }

    /**
     * @param $product_id
     *
     * @return int|null
     */
    public function setInsertProductToStoreOpencart($product_id):?int
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "INSERT INTO `ocvoiptech`.`oc_product_to_store` (`product_id`, `store_id`) VALUES ('".$product_id."', '0')";
        $this->db->query($query);

        return $this->db->getID();
    }

    /**
     * @param $productId
     * @param $optionId
     *
     * @return int|null
     */
    public function setProductOption($productId, $optionId):?int
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $productOptionId = $this->getIdForTableByPrimaryKey('oc_product_option', 'product_option_id');

        $query = "INSERT INTO `ocvoiptech`.`oc_product_option` (`product_option_id`, `product_id`, `option_id`, `value`, `required`"
            .") VALUES ("
            ."'".$productOptionId."', '".$productId."', '".$optionId."', '', '1');";
        $this->db->query($query);

        return $this->db->getID();
    }

    /**
     * @param $productOptionId
     * @param $productId
     *
     * @return array|null
     */
    public function getProductOptionValue($productOptionId, $productId):?array
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "SELECT * FROM `oc_product_option_value` WHERE `product_option_id`='".$productOptionId."' AND `product_id`='".$productId."'";
        $this->db->query($query);

        $rows = $this->db->row();

        return (!empty($rows))?$rows:null;
    }

    /**
     * @param $table
     * @param $primaryKey
     */
    public function getIdForTableByPrimaryKey($table, $primaryKey)
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $rPrimaryKey = [];

        $query = "SELECT max(`". $primaryKey."`) as primaryKey FROM `".$table."`";
        $this->db->query($query);
        $rows = $this->db->row();

        $maxPrimaryKey = $rows[0]['primaryKey']+1;
        for($i=1;$i<=$maxPrimaryKey;$i++) {
            $rPrimaryKey[$i] = $i;
        }

        $query = "SELECT `". $primaryKey."` as primaryKeyList FROM `".$table."`";
        $this->db->query($query);
        $rows = $this->db->row();

        foreach($rows as $row) {
            unset($rPrimaryKey[$row['primaryKeyList']]);
        }

        return array_shift($rPrimaryKey);
    }

    /**
     * @param $productOptionId
     * @param $productId
     * @param $optionId
     * @param $optionValueId
     * @param $price
     *
     * @return int|null
     */
    public function setProductOptionValue($productOptionId, $productId, $optionId, $optionValueId, $price):?int
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $productOptionValueId = $this->getIdForTableByPrimaryKey('oc_product_option_value', 'product_option_value_id');

        $query = "INSERT INTO
`ocvoiptech`.`oc_product_option_value`
(
`product_option_value_id`,
`product_option_id`,
`product_id`,
`option_id`,
`option_value_id`,
`quantity`,
`subtract`,
`price`,
`price_prefix`,
`points`,
`points_prefix`,
`weight`,
`weight_prefix`
) VALUES (
".$productOptionValueId.",
'".$productOptionId."',
'".$productId."',
'".$optionId."',
'".$optionValueId."',
'1',
'0',
'".$price."',
'+',
'0',
'-',
'0.00000000',
'-'
);";
        $this->db->query($query);

        return $this->db->getID();
    }

    /**
     * @param $productId
     *
     * @return array|null
     */
    public function getProductOption($productId):?array
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "SELECT * FROM `oc_product_option` WHERE `product_id`='".$productId."' AND `option_id` IN (1,2)";
        $this->db->query($query);

        $rows = $this->db->row();

        return (!empty($rows))?$rows:null;
    }

    /**
     * @param int $productOptionId
     */
    public function setDeleteProductOption(int $productOptionId)
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "DELETE FROM `oc_product_option` WHERE `product_option_id` = '".$productOptionId."'";

        $this->db->query($query);
    }

    /**
     * @param $manufacturer
     * @param $sku
     *
     * @return array|null
     */
    public function getProductOpencart($manufacturer, $sku):?array
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "SELECT `manufacturer_id` FROM `oc_manufacturer` WHERE `name` = '".$manufacturer."' LIMIT 1";
        $this->db->query($query);
        $rows = $this->db->row();
        $manufacturerId = ((!empty($rows[0]['manufacturer_id']))?$rows[0]['manufacturer_id']:'20');

        $query = "SELECT * FROM `oc_product` op WHERE `manufacturer_id`='".$manufacturerId."' AND op.sku = '".$sku."' LIMIT 1";
        $this->db->query($query);

        $rows = $this->db->row();

        return (!empty($rows[0])?$rows[0]:null);
    }

    /**
     * @param int $docObjId
     * @param int $offerId
     *
     * @return array|null
     */
    public function getProductOpencartByDocObjIdAndOfferId(int $docObjId, int $offerId):?array
    {
        $productId = null;

        $query = "SELECT `productId` FROM `WTrade_offer_link_product` WHERE `docObjId`='".$docObjId."' AND `offerId`='".$offerId."' LIMIT 1";
        $this->db->query($query);
        $rows = $this->db->row();

        if (!empty($rows[0]['productId'])) {
            $productId = $rows[0]['productId'];

            $query = "SELECT * FROM `oc_product` WHERE `product_id`='".$productId."' LIMIT 1";
            $this->db->query($query);
            $rows = $this->db->row();
        }

        if (!empty($rows[0])) {
            return $rows[0];
        }

        // Удаляем связь, если продукт уже удален
        $query = "DELETE FROM `WTrade_offer_link_product` WHERE `docObjId`='".$docObjId."' AND `offerId`='".$offerId."'";
        $this->db->query($query);

        return null;
    }

    /**
     * @param int $productOptionValueId
     */
    public function setDeleteProductOptionValue(int $productOptionValueId)
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "DELETE FROM `oc_product_option_value` WHERE `product_option_value_id` = '".$productOptionValueId."'";

        $this->db->query($query);
    }

    /**
     * @param int $productId
     * @param $price
     */
    public function setProductPrice(int $productId, $price)
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        $query = "UPDATE `ocvoiptech`.`oc_product` SET `price` = '".$price."' WHERE `oc_product`.`product_id`='".$productId."';";

        $this->db->query($query);
    }

    /**
     * @param int $docObjId
     * @param array $productIdList
     */
    public function setProductStatus(int $docObjId, array $productIdList = [])
    {
        $this->db->connect(
            $_ENV['OPVoipTechIp'],
            $_ENV['OPVoipTechLogin'],
            $_ENV['OPVoipTechPass'],
            $_ENV['OPVoipTechMysqlBase'],
            $_ENV['OPVoipTechPort']
        );

        if (!empty($productIdList)) {
            $productIdListInActive = [];

            $query = "SELECT `productId` FROM `WTrade_offer_link_product` WHERE `docObjId`='".$docObjId."'";
            $this->db->query($query);
            $rows = $this->db->row();
            foreach ($rows as $row) {
                $productIdListInActive[] = $row['productId'];
            }

            $query = "UPDATE `ocvoiptech`.`oc_product` SET `quantity`=0, `stock_status_id`=5 "
                ."WHERE `product_id` IN (".implode(",", $productIdListInActive).");";
            $this->db->query($query);

            $query = "UPDATE `ocvoiptech`.`oc_product` SET `quantity`=1, `stock_status_id`=7 "
                ."WHERE `product_id` IN (".implode(",", $productIdList).");";
            $this->db->query($query);
        }

    }
}