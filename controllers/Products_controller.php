<?php

class Products_controller extends Base_controller
{
    /**
     * Products_controller constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadModel('Country_model');
        $this->loadModel('Product_model');
    }

    /**
     * Get all countries or country by iso2
     * @param bool $iso
     * @return mixed|null
     */
    public function countries( $iso = false )
    {
        $where = [];
        $oCountry = new Country_model();
        if ($iso) {
            $where = ['iso2'=>$iso];
        }

        return $oCountry->getCountries($where);
    }

    /**
     * Get all products or products by country
     * @param bool $country_id
     * @return mixed|null
     */
    public function products( $country_id = false )
    {
        $oProduct = new Product_model();
        if ($country_id) {
            return $oProduct->getProductsByCountryId($country_id);
        }

        return $oProduct->getProducts();
    }

    /**
     * Get single product data by his id
     * @param $product_id
     * @return mixed|null
     */
    public function product( $product_id )
    {
        $oProduct = new Product_model();
        $oCountry = new Country_model();
        $oProduct = $oProduct->getProductById($product_id);
        $oProduct->countries = $oCountry->getCountriesByProductId($product_id);

        return $oProduct;
    }
}