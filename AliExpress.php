<?php

namespace mtrofimenko\yii2aliexpress;

use yii\base\Component;
use mtrofimenko\yii2aliexpress\request\ListProductRequest;
use mtrofimenko\yii2aliexpress\request\ProductRequest;
use mtrofimenko\yii2aliexpress\request\PromotionLinksRequest;
use mtrofimenko\yii2aliexpress\inventory\AliClient;

class AliExpress extends Component {

    private $_client = false;

    /**
     *
     * @var string Alexpress api key
     */
    public $key;

    /**
     *
     * @var string Aliexpress API secret
     * please obtain it here http://portals.aliexpress.com/adcenter/api_setting.htm
     */
    public $signature;

    /**
     * Get original Aliexpress client
     * @return Aliexpress 
     * 
     */
    public function getClient() {
        if ($this->_client === false) {
            $this->_client = new AliClient($this->key, $this->signature);
        }

        return $this->_client;
    }

    /**
     * Get list Product
     * @param string $searchParam CategoryID or Keywords<br/>
     * (The category ID for the products. Add the level-1 category description
     * below for your reference.<br/>Product title must contain a relevant keyword.
     * Note: You should select at least one of the parameters, keywords or
     * category ID, when you run a query.) 
     * @param array $params <br/>
     * <ul>
     * <li><i>string</i> <b>fields</b> - List of fields needed to return.
     *      Please separate each other with an English comma “,” if you want
     *      to use more than one field.
     *      <ol>
     *          <li><i>totalResults</i> - The number of the total data returned.</li>
     *          <li><i>productId</i> - The product ID.</li>
     *          <li><i>productTitle</i> - The Product Title.</li>
     *          <li><i>productUrl</i>  - The URL of the Detail Page of the product.</li>
     *          <li><i>imageUrl</i> - The URL of the main picture of the product.</li>
     *          <li><i>originalPrice</i> - The original price of the product, with the 
     *              dollar sign, including cents, E.g. $9.95</li>
     *          <li><i>salePrice</i> - The current price of the product, with the 
     *              dollar sign, including cents, E.g. $6.95.</li>
     *          <li><i>discount</i>  - The discount of the product, E.g. 10.00%.</li>
     *          <li><i>evaluateScore</i> - The average evaluated score of the product.</li>
     *          <li><i>commission</i> - The commission amount, with the dollar sign, including cents, E.g. $3.95.</li>
     *          <li><i>commissionRate</i> - The commission rate, E.g. 12.34%.</li>
     *          <li><i>30daysCommission</i>  - The total commission amount within 
     *              the most recent 30 days, with the dollar sign, including cents, 
     *              E.g. $33.95.</li>
     *          <li><i>volume</i>  - The lower limit of the total volume by affiliates 
     *              within the most recent 30days.</li>
     *          <li><i>packageType</i> - The packaging type of the products: piece or lot.</li>
     *          <li><i>lotNum</i> - The number of products in the lot.</li>
     *          <li><i>validTime</i> - The valid time of the product (yyyy-MM-dd).</li>
     *      </ol>
     * </li>
     * <li><i>double</i> <b>commissionRateFrom</b> - The minimum commission rate,
     *      must be stated in percentage, E.g. 0.03.
     * </li>
     * <li><i>double</i> <b>commissionRateTo</b> - The maximum commission rate,
     *     must be stated in percentage E.g. 0.05.
     * </li>
     * <li><i>double</i> <b>originalPriceFrom</b> - The lowest product price,must
     *     be stated in USD, E.g. 12.34.
     * </li>
     * <li><i>double</i> <b>originalPriceTo</b> - The maximum product price,must
     *     be stated in USD E.g. US 56.78.
     * </li>
     * <li><i>integer</i> <b>volumeFrom</b> - The lower limit of the total volume
     *     by affiliates within the most recent 30 days, E.g. 123.
     * </li>
     * <li><i>integer</i> <b>volumeTo</b> - The upper limit of the total volume by
     *     affiliates within the most recent 30days, for example, 456.
     * </li>
     * <li><i>pageNo</i> <b>pageNo</b> - Page number. The incoming value
     *     of 1 represents the first page; The incoming value of 2 represents the
     *     second page, and so on. The returned data from the first page is set to default.
     * </li>
     * <li><i>pageSize</i> <b>pageSize</b> - The maximum number of data that can
     *     be set on each page is 40. The default value is 20.
     * </li>
     * <li><i>string</i> <b>sort</b> - You can set sort by price from high to low
     *     and from low to high, credit score from high to low and from low to high,
     *     the commission rate from high to low and from low to high, volume from
     *     high to low, the valid time from high to low and from low to high.
     *     Values(orignalPriceUp, orignalPriceDown, sellerRateDown, commissionRateUp,
     *     commissionRateDown, volumeDown, validTimeUp, validTimeDown) 
     * </li>
     * <li><i>integer</i> <b>startCreditScore</b> - The lower limit of the credit
     *     score of the seller, for example, 12.
     * </li>
     * <li><i>integer</i> <b>endCreditScore</b> - The upper limit of the credit
     *     score of the seller, for example, 123.
     * </li>
     * </ul>
     * @return mixed
     */
    public function getListProduct($searchParam, $params = array()) {

        $request = new ListProductRequest();
        if (is_numeric($searchParam)) {
            $request->setCategoryId($searchParam);
        } else {
            $request->setKeywords($searchParam);
        }
        foreach ($params as $key => $val) {
            $set = 'set' . ucfirst($key);
            $request->$set($val);
        }

        $responce = $this->getClient()->getData($request);

        return $responce;
    }

    /**
     * Get Product
     * @param string $productId The product ID
     * @param string $fields List of fields needed to return. Please separate 
     * each other with an English comma “,” if you want to use more than one field.
     * <ol>
     *  <li><i>productId</i> - The product ID.</li>
     *  <li><i>productTitle</i> - The Product Title.</li>
     *  <li><i>productUrl</i>  - The URL of the Detail Page of the product.</li>
     *  <li><i>imageUrl</i> - The URL of the main picture of the product.</li>
     *  <li><i>originalPrice</i> - The original price of the product, with the 
     *       dollar sign, including cents, E.g. $9.95</li>
     *  <li><i>salePrice</i> - The current price of the product, with the 
     *       dollar sign, including cents, E.g. $6.95.</li>
     *  <li><i>discount</i>  - The discount of the product, E.g. 10.00%.</li>
     *  <li><i>evaluateScore</i> - The average evaluated score of the product.</li>
     *  <li><i>commission</i> - The commission amount, with the dollar sign, including cents, E.g. $3.95.</li>
     *  <li><i>commissionRate</i> - The commission rate, E.g. 12.34%.</li>
     *  <li><i>30daysCommission</i>  - The total commission amount within 
     *       the most recent 30 days, with the dollar sign, including cents, 
     *       E.g. $33.95.</li>
     *  <li><i>volume</i>  - The lower limit of the total volume by affiliates 
     *       within the most recent 30days.</li>
     *  <li><i>packageType</i> - The packaging type of the products: piece or lot.</li>
     *  <li><i>lotNum</i> - The number of products in the lot.</li>
     *  <li><i>validTime</i> - The valid time of the product (yyyy-MM-dd).</li>
     *  <li><i>storeName</i> - The name of the seller’s store.</li>
     *  <li><i>storeUrl</i> - The Url of the seller’s store.</li>
     * </ol>
     * @return mixed
     */
    public function getProduct($productId, $fields = null) {

        $request = new ProductRequest();
        $request->setProductId($productId);
        if ($fields !== null) {
            $request->setFields($fields);
        }

        $responce = $this->getClient()->getData($request);

        return $responce;
    }

    /**
     * Get Promotion Links
     * @param string $trackingId The tracking ID of your account in the Portals platform.
     * @param string $urls The list of URLs need to be converted to promotion URLs. 
     * Please separate each URL with an English “,” if you want to use more than 
     * one field. The limit of URL’s that can be used is 50.
     * @param string $fields List of fields needed to return, options including 
     * “tracking ID” , ”publisher ID” , ”promotion URL”. Please separate each 
     * other with “,” if you want to use more than one field.
     * <ol>
     *  <li><i>totalResults</i> - The number of the total data returned.</li>
     *  <li><i>trackingId</i> - The tracking ID of your account in the Portals platform.</li>
     *  <li><i>publisherId</i> - The publisher ID of your account in the Portals platform.</li>
     *  <li><i>url</i>  - The list of URLs need to be converted to promotion URLs.</li>
     *  <li><i>promotionUrl</i>  - The Promotion URL encoding some important parameters 
     *      can be billing.Wecan not return the promotion urls of the goods what 
     *      are not promoted by the Portals Platform.</li>
     * </ol>
     * @return type
     */
    public function getPromotionLinks($trackingId, $urls, $fields = null) {

        $request = new PromotionLinksRequest();
        $request->setTrackingId($trackingId);
        $request->setUrls($urls);
        if ($fields !== null) {
            $request->setFields($fields);
        }

        $responce = $this->getClient()->getData($request);

        return $responce;
    }

    /**
     * Get Category
     * @return array
     */
    public function getListCategory() {
        return array(
            array(
                'id' => 3,
                'name' => 'Apparel & Accessories'
            ),
            array(
                'id' => 34,
                'name' => 'Automobiles & Motorcycles'
            ),
            array(
                'id' => 1501,
                'name' => 'Baby Products'
            ),
            array(
                'id' => 66,
                'name' => 'Beauty & Health'
            ),
            array(
                'id' => 7,
                'name' => 'Computer & Networking'
            ),
            array(
                'id' => 13,
                'name' => 'Construction & Real Estate'
            ),
            array(
                'id' => 44,
                'name' => 'Consumer Electronics'
            ),
            array(
                'id' => 100008578,
                'name' => 'Customized Products'
            ),
            array(
                'id' => 5,
                'name' => 'Electrical Equipment & Supplies'
            ),
            array(
                'id' => 502,
                'name' => 'Electronic Components & Supplies'
            ),
            array(
                'id' => 2,
                'name' => 'Food'
            ),
            array(
                'id' => 1503,
                'name' => 'Furniture'
            ),
            array(
                'id' => 200003655,
                'name' => 'Hair & Accessories'
            ),
            array(
                'id' => 42,
                'name' => 'Hardware'
            ),
            array(
                'id' => 15,
                'name' => 'Home & Garden'
            ),
            array(
                'id' => 6,
                'name' => 'Home Appliances'
            ),
            array(
                'id' => 200003590,
                'name' => 'Industry & Business'
            ),
            array(
                'id' => 36,
                'name' => 'Jewelry & Watch'
            ),
            array(
                'id' => 39,
                'name' => 'Lights & Lighting'
            ),
            array(
                'id' => 1524,
                'name' => 'Luggage & Bags'
            ),
            array(
                'id' => 21,
                'name' => 'Office & School Supplies'
            ),
            array(
                'id' => 509,
                'name' => 'Phones & Telecommunications'
            ),
            array(
                'id' => 30,
                'name' => 'Security & Protection'
            ),
            array(
                'id' => 322,
                'name' => 'Shoes'
            ),
            array(
                'id' => 200001075,
                'name' => 'Special Category'
            ),
            array(
                'id' => 18,
                'name' => 'Sports & Entertainment'
            ),
            array(
                'id' => 1420,
                'name' => 'Tools'
            ),
            array(
                'id' => 26,
                'name' => 'Toys & Hobbies'
            ),
            array(
                'id' => 1511,
                'name' => 'Watches'
            ),
        );
    }

}
