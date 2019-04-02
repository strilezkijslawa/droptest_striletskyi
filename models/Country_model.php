<?php

class Country_model extends Base_model
{
    public $table = 'countries';

    /**
     * Get countries from db
     * @param array $where
     * @return mixed|null
     */
    public function getCountries( $where = [] )
    {
        $countries = $this->db->get($this->table, $where);
        if ($countries) {
            return $countries->asObject();
        }

        return null;
    }

    /**
     * Get country from db by id
     * @param $id
     * @return mixed|null
     */
    public function getCountryById( $id )
    {
        $country = $this->db->get($this->table, ['id'=>$id]);
        if ($country) {
            return $country->rowAsObject();
        }

        return null;
    }

    /**
     * Get product countries
     * @param $product_id
     * @return mixed|null
     */
    public function getCountriesByProductId( $product_id )
    {
        $this->db->setLeftJoin('product_countries', 'countries.id = product_countries.country_id');
        $this->db->setWhere(['product_countries.product_id'=>$product_id]);
        $countries = $this->db->get($this->table);
        if ($countries) {
            return $countries->asObject();
        }

        return null;
    }
}