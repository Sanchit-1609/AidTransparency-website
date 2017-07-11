<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RentalCollection
 *
 * @author csweet
 */
abstract class Sweetapple_Posttype_Collection implements IteratorAggregate, Countable
{

    // Member variables
    //<editor-fold>

    private $_position = 0;

    /**
     * Name of
     * @var string
     */
    private $_type;

    private $_postname;

    private $_metaprefix = null;

    private $_posts = null;

    private $_hierarchy = null;


    //</editor-fold>
    // Getters and Setters

    public function getPostname()
    {
        return $this->_postname;
    }


    public function getMetaprefix()
    {
        return $this->_metaprefix;
    }


    //<editor-fold>

    /**
     *
     * @param int $id
     * @return Sweetapple_Posttype
     */
    public function getPost($id)
    {
        return (array_key_exists($id, $this->_posts)) ? $this->_posts[$id] : null;
    }


    public function setPost($object)
    {
        $this->_posts[$object->ID] = $object;
    }


    //</editor-fold>

    /**
     *
     * @param object $posttype
     */
    public function __construct($posttype)
    {
        if ($posttype === null) {
            throw new Exception('Missing $posttype arguement in ' . __FUNCTION__);
        }
        $this->_postname = $posttype->getPostname();
        $this->_metaprefix = $posttype->getMetaPrefix();
        $this->_type = get_class($posttype);
    }


    /**
     *
     * @return array
     */
    public function getPostIds()
    {
        $ids = array ();
        foreach ($this->_posts as $post) {
            $ids[] = $post->ID;
        }
        return $ids;
    }


    /**
     * Returns an array of all objects of this type
     * @return array
     */
    public function getPosts()
    {
        if ($this->_posts === null) {
            $this->findPosts();
        }
        return $this->_posts;
    }



    public function clearPosts()
    {
        $this->_posts = array();
    }


    /**
     *
     * @param type $options
     * @return int|null
     */
    public function findPosts($options = null)
    {
        $args = array (
            'order'     =>  'ASC',
            'post_type' =>  $this->_postname
        );
        if (isset($options)) {
            $args = array_merge($args, $options);
        }
        $objects = get_posts($args);

        if (count($objects) > 0) {
            foreach ($objects as $object) {
                $post = new $this->_type((array) $object);
                $this->setPost($post);
            }
            $this->_findPostsMeta();
            return count($this->_posts);
        } else {
            return null;
        }
    }

    /**
     *
     * @global type $wpdb
     * @param Array $ids
     */
    public function findPostsInIDOrder( $idArray )
    {
        global $wpdb;

        $ids = implode(',', $idArray);

        $query = "SELECT posts.*
                    FROM {$wpdb->posts} AS posts
                   WHERE posts.`post_type` = '{$this->getPostname()}'
                     AND posts.ID IN ({$ids})
                     ORDER BY FIELD(posts.ID,{$ids})";

        $objects = $wpdb->get_results($query, OBJECT);

        if (count($objects) > 0) {
            foreach ($objects as $object) {
                $post = new $this->_type((array) $object);
                $this->setPost($post);
            }
            $this->_findPostsMeta();
            return count($this->_posts);
        } else {
            return null;
        }

    }


    /**
     * Populate the Posts Collection with meta data for each post
     * @global type $wpdb
     */
    private function _findPostsMeta()
    {
        global $wpdb;
        $ids = implode(',', $this->getPostIds());
        $query = "SELECT * FROM $wpdb->postmeta WHERE post_id IN($ids) AND INSTR(meta_key, '_') > 1";
        $metas = $wpdb->get_results($query, OBJECT_K);
        foreach ($metas as $meta) {
            $post = $this->getPost($meta->post_id);
            $post->setMetaValue($meta->meta_key, $meta->meta_value);
        }
    }


    /**
     * Finds all parent objects and links them
     * @param string|Sweetapple_Posttype $post_type
     * @return null
     */
    public function getParents( $post_type )
    {
        //Default meta_key
        $meta_key = '';
        $post_name = '';
        $is_sweetapple_posttype = false;

        if( function_exists('wpcf_pr_post_get_belongs')){

            //Get the correct post type to look for...
            if(is_a($post_type, 'Sweetapple_Posttype')){
                $meta_key = '_wpcf_belongs_' . $post_type->getPostname() . '_id';
                $post_name = $post_type->getPostname();
                $is_sweetapple_posttype = true;
            }else{
                $meta_key = '_wpcf_belongs_' . $post_type . '_id';
                $post_name = $post_type;
            }

            global $wpdb;
            $ids = implode(',', $this->getPostIds());
            $query = "SELECT * FROM $wpdb->postmeta WHERE post_id IN($ids) AND meta_key = %s";
            $preparedQuery = $wpdb->prepare($query, $meta_key);
            $metas = $wpdb->get_results($preparedQuery, OBJECT_K);

            $parentIds = array();
            foreach ($metas as $meta) {
                $parentIds[] = $meta->meta_value;
            }

            $parentQuery = new WP_Query( array( 'post_type' => $post_name , 'post__in' => $parentIds ) );
            $parents = $parentQuery->get_posts();

            //Assign the parents to the children
            foreach ($metas as $meta) {
                $post = $this->getPost($meta->post_id);

                foreach ($parents as $parent) {
                    if($parent->ID == $meta->post_id){
                        if($is_sweetapple_posttype){
                            $post_type->setValues((array)$parent);
                            $post->setParent($post_type);
                        }else{
                            $post->{$post_name} = $parent;
                        }
                        break;
                    }
                }
            }

        }



    }



    /**
     * Function to satisfy the IteratorAggregate interface.  Sets an
     * ArrayIterator instance for the server list to allow this class to be
     * iterable like an array.
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_posts);
    }


    public function count()
    {
        return $this->length();
    }


    public function length()
    {
        return count($this->_posts);
    }


    /**
     * Sort by a particular value...
     * @param string $key
     * @param array $values
     * @return null
     */
    public function setCustomOrder( $key, $values)
    {
        return null;
    }


}
