<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 *
 * @author csweet
 */
abstract class Sweetapple_Posttype{

    protected $_postname = '';

    protected $_metaprefix = '';

    protected $_metaloaded = false;


    /**
     *
     * @param array $values
     */
    public function __construct( Array $values = null )
    {
        if($values){
            $this->setValues($values);
        }
    }



    public function __get($name)
    {
        $this->loadMetaData();
        return ( isset($this->$name) ) ? $this->$name : "N/A";
    }


    /**
     * Set/Get attribute wrapper
     *
     * @param   string $method
     * @param   array $args
     * @return  mixed
     */
    public function __call($method, $args)
    {
        switch (substr($method, 0, 3)) {
            case 'has' :
                $propertyName = strtolower( substr($method,3) );
                //Check if not null and greater than 0 length
                return ( isset($this->{$propertyName}) && strlen($this->{$propertyName}) > 0 );
        }
        throw new BadMethodCallException("Invalid method ".get_class($this)."::".$method."(".print_r($args,1).")");
    }

    /**
     * Load meta data when calling unknown properties
     */
    protected function loadMetaData()
    {
        if(!$this->_metaloaded) {
            $this->setMetaData();
        }
    }


    /**
     * Set properties on object
     * @param array $values
     */
    public function setValues(Array $values)
    {
        if( $values ) {
            foreach ($values as $key => $value) {
                $this->$key = $value;
            }
        }
    }


    /**
     * Grabs all the custom field data for this object and populates it
     */
    public function setMetaData()
    {
        $meta = get_post_custom( $this->ID);

        foreach ($meta as $key => $value) {
            $metaPrefixPosition = strripos($key, $this->_metaprefix);
            if($metaPrefixPosition !== false ){
                $key = substr($key, strlen($this->_metaprefix) , strlen($key));
            }
            $this->$key = $value[0];
        }
        $this->_metaloaded = true;
    }


    public function setMetaValue( $key, $value )
    {
        $metaPrefixPosition = strripos($key, $this->_metaprefix);
        if( $metaPrefixPosition !== false ){
            $key = substr($key, strlen($this->_metaprefix) , strlen($key));
        }
        $this->$key = (string) $value;
    }


    public function getPostname()
    {
        return $this->_postname;
    }


    public function getMetaPrefix()
    {
        return $this->_metaprefix;
    }


    public function setPostType( $posttype )
    {
        $this->_postname = $posttype;
    }


    /**
     *
     * @param string $prefix
     */
    public function setMetaPrefix( $prefix )
    {
        $this->_metaprefix = $prefix;
    }

    /**
     * Loads a parent post type object, should one exist...
     * @param string|Sweetapple_Posttype $posttype
     * @return object|boolean
     */
    public function getParent($post_type)
    {
         $fieldname = "";
        if(is_a($post_type, 'Sweetapple_Posttype')){
            $fieldname = $post_type->getPostname();
        }else{
           $fieldname = $post_type;
        }
        //Check if we already have a parent set...
        $parent = $this->{$fieldname};
        if($parent) return $parent;
        //Otherwise load it...
        if( function_exists('wpcf_pr_post_get_belongs')){
            if(is_a($post_type, 'Sweetapple_Posttype')){
                $parent_id = wpcf_pr_post_get_belongs($this->ID, $post_type->getPostname());
                $post = get_post($parent_id);
                $post_type->setValues( (array)$post );
                $parent = $post_type;
            }else{
                $parent_id = wpcf_pr_post_get_belongs($this->ID, $post_type);
                $parent = get_post($parent_id);

            }

            if($parent){
                $this->{$fieldname} = $parent;
                return $parent;
            }
        }

        return false;
    }


    public function setParent(Sweetapple_Posttype $parent )
    {
        $post_name = $parent->getPostname();
        $this->{$post_name} = $parent;
    }


    /**
     *
     * @param string $post_type
     * @return boolean
     */
    public function hasParent($post_type)
    {
        return ($this->getParent($post_type)) ? true : false;
    }


    /**
     *
     * @param int $timestamp
     * @return \DateTime
     */
    public function timestampToDateTime($timestamp )
    {
        return new DateTime("@$timestamp");
        if(phpversion() >= 5.3){

            $datetime = DateTime::createFromFormat('U', $timeStamp);
        }else{
            $datestring = date('Y/n/d', $timestamp);
            $datetime = new DateTime($datestring);
        }
        return $datetime;
    }


}
