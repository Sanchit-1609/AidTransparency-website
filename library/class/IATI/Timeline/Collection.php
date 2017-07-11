<?php
/**
 * Description of IATI_Timeline_Collection
 *
 * @author csweet
 */
class IATI_Timeline_Collection extends Sweetapple_Posttype_Collection
{

    //put your code here
    public function __construct( $timeline = null )
    {
        if(!$timeline) {
            $timeline = new IATI_Timeline();
        }
        parent::__construct( $timeline );
    }


}
