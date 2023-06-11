<?php

namespace method\wtradeApiToOpencart2;

use system\core\Logger;

/**
 * Class Controller
 *
 * @property \method\wtradeApiToOpencart2\Model model
 * @property \method\wtradeApiToOpencart2\View view
 *
 * @package method\wtradeApiToOpencart2
 */
class Controller extends \system\core\abstracts\mvc\WebController
{
    /**

     */
    public function getRun():void
    {
        $countShow = 0;
        $productIdList = [];

        // Создаем Таблицу связи между предложением и товаром в системе
        $this->model->createTableIfNotExists();

        $currencyExchangeFromAPI = $this->model->getCurrencyExchangeFromAPI();

        $offerList = $this->model->getOfferFromAPI();
//	print_r($offerList);
//	echo "\n";

        foreach($offerList as $oKey => $offer) {
            $item_id = 0;

            $offerId = $offer['currentOffer']['offerId'];
            $docObjId = $offer['currentOffer']['docObjId'];
            $manufacturer = ((!empty($offer['currentOffer']['manufacturer']))?$offer['currentOffer']['manufacturer']:'');
            $typeName = ((!empty($offer['currentOffer']['typeName']))?$offer['currentOffer']['typeName']:'');
            $model = ((!empty($offer['currentOffer']['model']))?$offer['currentOffer']['model']:'');
            $images = ((!empty($offer['currentOffer']['image']))?explode("|", $offer['currentOffer']['image']):[]);
            $description = ((!empty($offer['currentOffer']['description']))?$offer['currentOffer']['description']:'');

            $rrpPrice = 0;
            if (!empty($offer['currentOffer']['rrpPrice'])) {
                if ('UAH' == mb_strtoupper($offer['currentOffer']['rrpCurrency'])) {
                    $rrpPrice = $offer['currentOffer']['rrpPrice'];
                } else {
                    $rrpPrice = (empty($offer['currentOffer']['rrpPrice'])?0:$offer['currentOffer']['rrpPrice'])*$currencyExchangeFromAPI[mb_strtoupper($offer['currentOffer']['rrpCurrency'])];
                }
            }

            $dealerPrice = 0;
            if (!empty($offer['currentOffer']['dealerPrice'])) {
                $dealerPrice = $offer['currentOffer']['dealerPrice'];
                $currency = ((!empty($offer['currentOffer']['currency']))?$offer['currentOffer']['currency']:$offer['currentOffer']['rrpCurrency']);
                if ('UAH' == mb_strtoupper($currency)) {
                    $dealerPrice = $offer['currentOffer']['dealerPrice'];
                } else {
                    $dealerPrice = (empty($offer['currentOffer']['dealerPrice'])?0:$offer['currentOffer']['dealerPrice'])*$currencyExchangeFromAPI[mb_strtoupper($currency)];
                }
            }

            $retailPrice = 0;
            if (!empty($offer['currentOffer']['retailPrice'])) {
                $retailPrice = $offer['currentOffer']['retailPrice'];
                $currency = ((!empty($offer['currentOffer']['currency']))?$offer['currentOffer']['currency']:$offer['currentOffer']['rrpCurrency']);
                if ('UAH' == mb_strtoupper($currency)) {
                    $retailPrice = $offer['currentOffer']['retailPrice'];
                } else {
                    $retailPrice = (empty($offer['currentOffer']['retailPrice'])?0:$offer['currentOffer']['retailPrice'])*$currencyExchangeFromAPI[mb_strtoupper($currency)];
                }
            }

            $price = $rrpPrice;
            if (empty($price)) {
                $price = (!empty($dealerPrice))?$dealerPrice:$retailPrice;
            }
            $price = round($price, 2);

            $currency = (!empty($offer['currentOffer']['rrpCurrency']))?$offer['currentOffer']['rrpCurrency']:'';
            if (empty($currency)) {
                $currency = (!empty($offer['currentOffer']['currency']))?$offer['currentOffer']['currency']:'';
            }
            $countShow++;

            $product = $this->model->getProductOpencartByDocObjIdAndOfferId($docObjId, $offerId);

            // Отображаем все не перенесенные товары
            if (empty($product)) {
                echo '- '.$manufacturer.' '.$model;
                echo "\n";

                $manufacturerOpencart = $this->model->getManufacturerProductOpencart($manufacturer);
                if (empty($manufacturerOpencart)) {
                    $manufacturerOpencartId = 20;
                } else {
                    $manufacturerOpencartId = $manufacturerOpencart[0]['manufacturer_id'];
                }
                $productId = $this->model->setInsertProductOpencart($manufacturerOpencartId, $model, $model, $price);
                $productIdList[] = $productId;

                // Создаем связь между предложением и продуктом в системе
                $this->model->insertOfferLinkProduct($docObjId, $offerId, $productId);

                // Меняем код товара на цифровой
                $this->model->setUpdateProductModelOpencart($productId);

                $this->model->setInsertProductDescriptionOpencart(
                    $productId,
                    $typeName.' '.$manufacturer.' '.$model,
                    $description,
                    $description,
                    $typeName.' '.$manufacturer.' '.$model,
                    '',
                    '',
                    $manufacturer.' '.$model
                );

                $this->model->setInsertProductToLayoutOpencart($productId);
                $this->model->setInsertProductToStoreOpencart($productId);

                $i = 0;
                $count = mb_strlen($productId);
                $idName = str_repeat('0', 5 - $count);
                foreach($images as $image) {
                    $explode = explode('/', $image);
                    $nameFile = end($explode);
                    $pathImageFile = 'catalog/products/'.$idName.$productId.'/'.$nameFile;

                    if (!is_dir('/var/www/voiptech/data/www/opencart.voiptech.com.ua/upload/image/catalog/products/'.$idName.$productId)) {
                        mkdir('/var/www/voiptech/data/www/opencart.voiptech.com.ua/upload/image/catalog/products/'.$idName.$productId);
                        file_put_contents('/var/www/voiptech/data/www/opencart.voiptech.com.ua/upload/image/catalog/products/'.$idName.$productId.'/index.html', '');
                    }

                    exec('wget -c -P /var/www/voiptech/data/www/opencart.voiptech.com.ua/upload/image/catalog/products/'.$idName.$productId.'/ '.$image);

                    if (0 == $i) {
                        $this->model->setUpdateProductOpencart($productId, $pathImageFile);
                    } else {
                        $this->model->setInsertProductImageOpencart($productId, $pathImageFile, $i);
                        $imagesTo[] = $pathImageFile;
                    }

                    $i++;
                }

                if (isset($offer['currentOffer']['vatIsNotIncluded'])) {
                    $optionId = 1;
                    $productOptionId = $this->model->setProductOption($productId, $optionId);

                    $this->model->setProductOptionValue($productOptionId, $productId, $optionId, 32, 0);
                    $this->model->setProductOptionValue($productOptionId, $productId, $optionId, 31, ($price/100*1));
                    $this->model->setProductOptionValue($productOptionId, $productId, $optionId, 49, ($price/100*$offer['currentOffer']['vatIsNotIncluded']));
                }

                if (isset($offer['currentOffer']['vatIsIncluded'])) {
                    $optionId = 2;
                    $productOptionId = $this->model->setProductOption($productId, $optionId);

                    $this->model->setProductOptionValue($productOptionId, $productId, $optionId, 23, 0);
                    $this->model->setProductOptionValue($productOptionId, $productId, $optionId, 50, ($price/100*1));
                }

//		print_r($offer);
//		print_r('price:'.$price);
//		echo "\n";
//		print_r("\n".$productId."\n");
            } else {
                $productId = $product['product_id'];
                $productIdList[] = $productId;
//		print_r("\n".$productId."\n");
//		echo "\n";

//		print_r($offer);
//		echo "\n";
//		print_r('price:'.$price);
//		echo "\n";

                $this->model->setProductPrice($productId, $price);

                $productOptionValueIdList = [];
                $productOptionList = $this->model->getProductOption($productId);
                if (!empty($productOptionList[0]['product_option_id'])) {
                    $productOptionValueList = $this->model->getProductOptionValue($productOptionList[0]['product_option_id'], $productId);

                    if (!empty($productOptionList)) {
                        foreach($productOptionList as $productOption) {
                            $this->model->setDeleteProductOption($productOption['product_option_id']);
                        }
                    }

                    if (!empty($productOptionValueList)) {
                        foreach($productOptionValueList as $productOptionValue) {
                            $productOptionValueId = $productOptionValue['product_option_value_id'];
                            $this->model->setDeleteProductOptionValue($productOptionValueId);
                            $productOptionValueIdList[] = $productOptionValueId;
                        }
                    }

                }

                if (isset($offer['currentOffer']['vatIsNotIncluded'])) {
                    $optionId = 1;
                    $productOptionId = $this->model->setProductOption($productId, $optionId);

                    $this->model->setProductOptionValue($productOptionId, $productId, $optionId, 32, 0);
                    $this->model->setProductOptionValue($productOptionId, $productId, $optionId, 31, ($price/100*1));
                    $this->model->setProductOptionValue($productOptionId, $productId, $optionId, 49, ($price/100*$offer['currentOffer']['vatIsNotIncluded']));
                }

                if (isset($offer['currentOffer']['vatIsIncluded'])) {
                    $optionId = 2;
                    $productOptionId = $this->model->setProductOption($productId, $optionId);

                    $this->model->setProductOptionValue($productOptionId, $productId, $optionId, 23, 0);
                    $this->model->setProductOptionValue($productOptionId, $productId, $optionId, 50, ($price/100*1));
                }

//		echo '+ '.$manufacturer.' '.$model;
//		echo "\n";
            }
        }

        $this->model->setProductStatus($docObjId, $productIdList);
//	print_r('COUNT: '.count($offerList));
//	print_r('COUNT SHOW:'.$countShow);
    }
}