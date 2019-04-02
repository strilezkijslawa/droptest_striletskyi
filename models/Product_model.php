<?php

class Product_model extends Base_model
{
    public $table = 'products';

    /**
     * Get products from db
     * @param array $where
     * @return mixed|null
     */
    public function getProducts( $where = [] )
    {
        $products = $this->db->get($this->table, $where);
        if ($products) {
            return $products->asObject();
        }

        return null;
    }

    /**
     * Get single product by id
     * @param $id
     * @return mixed|null
     */
    public function getProductById( $id )
    {
        $product = $this->db->get($this->table, ['id'=>$id]);
        if ($product) {
            return $product->rowAsObject();
        }

        return null;
    }

    /**
     * Get products from db by country id
     * @param $country_id
     * @return mixed|null
     */
    public function getProductsByCountryId( $country_id )
    {
        $this->db->setLeftJoin('product_countries', 'products.id = product_countries.product_id');
        $this->db->setWhere(['product_countries.country_id'=>$country_id]);
        $products = $this->db->get($this->table, ['products.active'=>1]);
        if ($products) {
            return $products->asObject();
        }

        return null;
    }
}