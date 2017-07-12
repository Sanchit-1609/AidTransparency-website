<?php

/**
 * @category    Sweetapple
 * @package     Sweetapple_<xxxxx>
 * @license     http://sweet-apple.co.uk
 * @author      Clive Sweeting <info@sweet-apple.co.uk>
 * @company     Sweet-Apple (http://www.sweet-apple.co.uk)
 * @date:       05/12/2012
 */
 ?>
<div id="home-icon-panels">
    <div class="icon transparency">
        <?php echo types_render_field("home_page_aidtransparency", array( 'raw' => false ) );?>
    </div>
    <div class="icon standard">
        <?php echo types_render_field("home_page_iatistandard", array( 'raw' => false ) );?>
    </div>
    <div class="icon registry">
        <?php echo types_render_field("home_page_iatiregistry", array( 'raw' => false ) );?>
    </div>
    <div class="icon community">
        <?php echo types_render_field("home_page_iaticommunity", array( 'raw' => false ) );?>
    </div>
</div><!--#home-icon-panels -->
