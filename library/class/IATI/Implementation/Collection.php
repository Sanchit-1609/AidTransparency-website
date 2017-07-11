<?php
/**
 * Description of IATI_Implementation_Collection
 *
 * @author csweet
 */
class IATI_Implementation_Collection extends Sweetapple_Posttype_Collection
{

    //put your code here
    public function __construct( $implementation = null )
    {
        if(!$implementation) {
            $implementation = new IATI_Implementation();
        }
        parent::__construct( $implementation );
    }


}
