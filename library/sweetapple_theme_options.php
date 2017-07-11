<?php
/**
 * SweetApple Generic theme options
 * @author Clive Sweeting, sweet-apple.co.uk <info@sweet-apple.co.uk>
 */


function sweetapple_admin_enqueue_scripts($hook_suffix)
{
    if ($hook_suffix !== 'appearance_page_custom_theme_options')
        return;
    wp_enqueue_style('sweetapple-theme-options', THEME_STYLES . '/custom-theme-options.css');
    wp_enqueue_script('sweetapple-theme-options', THEME_SCRIPTS . '/custom-theme-options.js', array("jquery-ui-tabs") );
}

add_action('admin_enqueue_scripts', 'sweetapple_admin_enqueue_scripts');


function sweetapple_theme_options_init()
{

    if (false === sweetapple_get_theme_options()) {
        add_option('sweetapple_theme', sweetapple_get_default_theme_options());
    }
    register_setting(
        'sweetapple_options',
        'sweetapple_theme_options',
        'sweetapple_theme_options_validate'
    );
}

add_action('admin_init', 'sweetapple_theme_options_init');


/**
 * Set sensible default options...
 * @param string $default_framework
 * @return array
 */
function sweetapple_get_default_theme_options($default_framework = '')
{
    $default_theme_options = array(
        'google_analytics_id' => '',
        'facebook_url_social' => '',
        'twitter_url_social' => '',
        'linkedin_url_social' => '',
        'youtube_url_social' => '',
        'vimeo_url_social' => '',
        'pinterest_url_social' => '',
        'tripadvisor_url_social' => '',
        'contact_phone' => '',
        'contact_email' => '',
        'contact_address' => '',
        'custom_css' => ''
    );
    return $default_theme_options;
}

/**
 * Merge default theme options with user settings
 * @return array
 */
function sweetapple_get_theme_options()
{
    $options = get_option('sweetapple_theme_options');
    if (!is_array($options)) {
        $options = array();
    }
    return array_merge(sweetapple_get_default_theme_options(), $options);
}


/**
 * Add our theme options page to the admin menu, including some help documentation.
 *
 * This function is attached to the admin_menu action hook.
 *
 * @since Twenty Eleven 1.0
 */
function sweetapple_theme_options_add_page()
{
    $theme_page = add_theme_page(
        __('Custom Theme Options', 'sweetapple'), // Name of page
        __('Custom Theme Options', 'sweetapple'), // Label in menu
        'edit_theme_options', // Capability required
        'custom_theme_options', // Menu slug, used to uniquely identify the page
        'sweetapple_theme_options_render_page' // Function that renders the options page
    );

    if (!$theme_page)
        return;

    add_action("load-$theme_page", 'sweetapple_theme_options_help');


}

add_action('admin_menu', 'sweetapple_theme_options_add_page');


function sweetapple_theme_options_help()
{
    $help = '<p>' . __('Some themes provide customization options that are grouped together on a Theme Options screen. If you change themes, options may change or disappear, as they are theme-specific. Your current theme, ' . wp_get_theme() . ' provides the following Theme Options:', 'sweetapple') . '</p>' .
        '<ol>' .
        '<li>' . __('<strong>Google Analytics ID</strong>: You can enter the unique Google Analytics Profile ID for this site.', 'sweetapple') . '</li>' .
        '</ol>' .
        '<p>' . __('Remember to click "Save Changes" to save any changes you have made to the theme options.', 'sweetapple') . '</p>' .
        '<p><strong>' . __('For more information:', 'sweetapple') . '</strong></p>' .
        '<p>' . __('<a href="http://codex.wordpress.org/Appearance_Theme_Options_Screen" target="_blank">Documentation on Theme Options</a>', 'sweetapple') . '</p>' .
        '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>', 'sweetapple') . '</p>';

    $screen = get_current_screen();

    if (method_exists($screen, 'add_help_tab')) {
        // WordPress 3.3
        $screen->add_help_tab(array(
                'title' => __('Overview', 'sweetapple'),
                'id' => 'theme-options-help',
                'content' => $help,
            )
        );
    } else {
        // WordPress 3.2
        add_contextual_help($screen, $help);
    }
}


/*
 * Returns the options array for Sweetapple theme.
 *
 * @since Twenty Eleven 1.2
 */
function sweetapple_theme_options_render_page()
{
    ?>
<div class="wrap">
    <?php screen_icon(); ?>
<h2><?php printf(__('%s Theme Options', 'sweetapple'), wp_get_theme()); ?></h2>
    <?php settings_errors(); ?>

<form method="post" action="options.php">
    <?php
    settings_fields('sweetapple_options');
    $options = sweetapple_get_theme_options();
    ?>

    <div id="custom-theme-options-tabs">

        <ul>
            <li><a href="#custom-theme-options-tabs-tracking">Analytics &amp; Tracking Code</a></li>
            <li><a href="#custom-theme-options-tabs-links">Social Media &amp; Links</a></li>
            <li><a href="#custom-theme-options-tabs-contact">Contact Information</a></li>
            <li><a href="#custom-theme-options-tabs-html">Custom CSS, HTML and JavaScript</a></li>
            <li><a href="#custom-theme-options-tabs-warnings">Warnings, Debugging and Notifications</a></li>
        </ul>

        <div id="custom-theme-options-tabs-tracking">
            <h3><?php _e('Analytics & Tracking Code', 'sweetapple'); ?></h3>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Google Analytics ID', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e('Google Analytics ID', 'sweetapple'); ?></span></legend>
                            <input type="text" name="sweetapple_theme_options[google_analytics_id]"
                                   id="google_analytics_id"
                                   value="<?php echo esc_attr($options['google_analytics_id']); ?>"/>
                            <br/>
                            <small class="description"><?php printf(__('Enter your Google Analytics tracking ID. This will be in the form UA-XXXXX-X', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </div>

        <div id="custom-theme-options-tabs-links">
            <h3><?php _e('Social Media & Links', 'sweetapple'); ?></h3>
            <p><em><?php _e('Your theme may use some of the information you enter as links to social media websites.', 'sweetapple'); ?></em></p>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Facebook Page', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Facebook Page', 'sweetapple'); ?></span>
                            </legend>
                            <input type="text" name="sweetapple_theme_options[facebook_url_social]" id="facebook_url"
                                   value="<?php echo esc_attr($options['facebook_url_social']); ?>"/>
                            <br/>
                            <small class="description"><?php printf(__('Enter Facebook URL', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Twitter Page', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Twitter Url', 'sweetapple'); ?></span>
                            </legend>
                            <input type="text" name="sweetapple_theme_options[twitter_url_social]" id="twitter_url"
                                   value="<?php echo esc_attr($options['twitter_url_social']); ?>"/>
                            <br/>
                            <small class="description"><?php printf(__('Enter Twitter URL', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('LinkedIn Page', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('LinkedIn Url', 'sweetapple'); ?></span>
                            </legend>
                            <input type="text" name="sweetapple_theme_options[linkedin_url_social]" id="linkedin_url"
                                   value="<?php echo esc_attr($options['linkedin_url_social']); ?>"/>
                            <br/>
                            <small class="description"><?php printf(__('Enter LinkedIn URL', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Youtube Page', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Youtube Url', 'sweetapple'); ?></span>
                            </legend>
                            <input type="text" name="sweetapple_theme_options[youtube_url_social]" id="linkedin_url"
                                   value="<?php echo esc_attr($options['youtube_url_social']); ?>"/>
                            <br/>
                            <small class="description"><?php printf(__('Enter Youtube URL', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Vimeo Page', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Vimeo Url', 'sweetapple'); ?></span>
                            </legend>
                            <input type="text" name="sweetapple_theme_options[vimeo_url_social]" id="linkedin_url"
                                   value="<?php echo esc_attr($options['vimeo_url_social']); ?>"/>
                            <br/>
                            <small class="description"><?php printf(__('Enter Youtube URL', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Pinterest Page', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Pinterest Url', 'sweetapple'); ?></span>
                            </legend>
                            <input type="text" name="sweetapple_theme_options[pinterest_url_social]" id="linkedin_url"
                                   value="<?php echo esc_attr($options['pinterest_url_social']); ?>"/>
                            <br/>
                            <small class="description"><?php printf(__('Enter Pinterest URL', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Tripadvisor Page', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e('Tripadvisor Url', 'sweetapple'); ?></span></legend>
                            <input type="text" name="sweetapple_theme_options[tripadvisor_url_social]" id="linkedin_url"
                                   value="<?php echo esc_attr($options['tripadvisor_url_social']); ?>"/>
                            <br/>
                            <small class="description"><?php printf(__('Enter Tripadvisor URL', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <!--                <tr valign="top">-->
                <!--                    <th scope="row">Upload or Select Logo</th>-->
                <!--                    <td><label for="upload_image">-->
                <!--                    <input id="sweetapple_theme_upload_image" type="text" size="36" name="sweetapple_theme_options['logo_url']" value="" />-->
                <!--                    <input id="sweetapple_theme_upload_image_button" type="button" value="Upload Image" />-->
                <!--                    <br />Enter an URL or upload an image for the banner.-->
                <!--                    </label></td>-->
                <!--                </tr>-->
            </table>
        </div>

        <div id="custom-theme-options-tabs-contact">

            <h3><?php _e('Contact Information', 'sweetapple'); ?></h3>
            <p><em><?php _e('Your theme may use some of the information you enter to control what is displayed to site visitors.', 'sweetapple'); ?></em></p>
            <table class="form-table">

                <tr valign="top">
                    <th scope="row"><?php _e('Contact Email Address', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e('Contact Email Address', 'sweetapple'); ?></span></legend>
                            <input type="text" name="sweetapple_theme_options[contact_email]" id="contact_email"
                                   value="<?php echo esc_attr($options['contact_email']); ?>"/>
                            <br/>
                            <small class="description"><?php printf(__('Enter Contact Email Address', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Contact Phone Number', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e('Contact Phone Number', 'sweetapple'); ?></span></legend>
                            <input type="text" name="sweetapple_theme_options[contact_phone]" id="contact_phone"
                                   value="<?php echo esc_attr($options['contact_phone']); ?>"/>
                            <br/>
                            <small class="description"><?php printf(__('Enter Contact Phone Number', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Contact Address', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e('Contact Address', 'sweetapple'); ?></span></legend>
                            <textarea name="sweetapple_theme_options[contact_address]"
                                      id="contact_address"><?php echo esc_textarea($options['contact_address']); ?></textarea>
                            <br/>
                            <small class="description"><?php printf(__('Enter Contact Address', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </div>

        <div id="custom-theme-options-tabs-html">

            <h3><?php _e('Custom CSS, Header and Footer HTML', 'sweetapple'); ?></h3>
            <p><em><?php _e('You can add your own CSS to override the theme look and feel, or add HTML code to the header and footer. Please be aware that malformed HTML may break the layout of the site, so please do so with care.', 'sweetapple'); ?></em></p>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Custom CSS Styles', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e('Custom CSS Styles', 'sweetapple'); ?></span></legend>
                            <textarea name="sweetapple_theme_options[custom_css]"
                                      id="custom_css"><?php echo esc_textarea($options['custom_css']); ?></textarea>
                            <br/>
                            <small class="description"><?php printf(__('Enter Custom CSS Styles', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Custom Head Content', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e('Custom Head Content', 'sweetapple'); ?></span></legend>
                            <textarea name="sweetapple_theme_options[custom_head]"
                                      id="custom_head"><?php echo esc_textarea($options['custom_head']); ?></textarea>
                            <br/>
                            <small class="description"><?php printf(__('Enter Custom &lt;head&gt; Content', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Custom Footer Content', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e('Custom Footer Content', 'sweetapple'); ?></span></legend>
                            <textarea name="sweetapple_theme_options[custom_footer]"
                                      id="custom_footer"><?php echo esc_textarea($options['custom_footer']); ?></textarea>
                            <br/>
                            <small class="description"><?php printf(__('Enter Custom Footer Content', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </div>

        <div id="custom-theme-options-tabs-warnings">

            <h3><?php _e('Development and Update Warnings', 'sweetapple'); ?></h3>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Debug Messages', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e('Debugging', 'sweetapple'); ?></span></legend>
                            <input type="checkbox" name="sweetapple_theme_options[development_debugging]"
                                   id="development_debugging" value="1" <?php if ($options['development_debugging'] == 1) {
                                print "checked='checked'";
                            } ?> />
                            <br/>
                            <small class="description"><?php printf(__('Enabling this may activate some visible debugging information', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Development warning', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e('Show Development warning', 'sweetapple'); ?></span></legend>
                            <input type="checkbox" name="sweetapple_theme_options[development_warning]"
                                   id="development_warning" value="1" <?php if ($options['development_warning'] == 1) {
                                print "checked='checked'";
                            } ?> />
                            <br/>
                            <small class="description"><?php printf(__('Tick this box is you want to show a development warning on the site.', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Development Warning Text', 'sweetapple'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e('Development Warning Text', 'sweetapple'); ?></span></legend>
                            <textarea name="sweetapple_theme_options[development_warning_text]"
                                      id="custom_footer"><?php echo esc_textarea($options['development_warning_text']); ?></textarea>
                            <br/>
                            <small class="description"><?php printf(__('Enter The text you want to show at the top of the page as a development warning', 'sweetapple')); ?></small>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <?php submit_button(); ?>
</form>
</div>
<?php
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 * @see sweetapple_theme_options_init()
 * @todo set up Reset Options action
 */
function sweetapple_theme_options_validate($input)
{
    $output = $defaults = sweetapple_get_default_theme_options();

    $input = array_map('trim', $input);

    //Some basic filtering
    foreach ($input as $key => $value) {
        if (strstr($key, "_url") !== null) {
            if (isset($input[$key])) {
                if (filter_var($input[$key], FILTER_VALIDATE_URL)) {
                    $output[$key] = $input[$key];
                }
            }
        }

        if (strstr($key, "_email")) {
            if (isset($input[$key])) {
                if (filter_var($input[$key], FILTER_VALIDATE_EMAIL)) {
                    $output[$key] = $input[$key];
                }
            }
        }

    }

    if (isset($input['vimeo_url_social'])) {
        if (strpos($input['vimeo_url_social'], 'http://vimeo.com/user') !== false) {
            $output['vimeo_url_social'] = $input['vimeo_url_social'];
        }
    }

    if (isset($input['google_analytics_id'])) {
        if (preg_match('/^ua-\d{4,9}-\d{1,4}$/i', $input['google_analytics_id'])) {
            $output['google_analytics_id'] = $input['google_analytics_id'];
        }
    }

    if (isset($input['contact_phone'])) {
        $output['contact_phone'] = $input['contact_phone'];
    }

    if (isset($input['contact_address'])) {
        $output['contact_address'] = $input['contact_address'];
    }

    if (isset($input['custom_css'])) {
        $custom_css = $input['custom_css'];
        if (strlen($custom_css) > 0) {
            $output['custom_css'] = $input['custom_css'];
        }
    }

    if (isset($input['custom_head'])) {
        $custom_head = $input['custom_head'];
        if (strlen($custom_head) > 0) {
            $output['custom_head'] = $input['custom_head'];
        }
    }

    if (isset($input['custom_footer'])) {
        $custom_footer = $input['custom_footer'];
        if (strlen($custom_footer) > 0) {
            $output['custom_footer'] = $input['custom_footer'];
        }
    }

    if (isset($input['development_debugging'])) {
        $dev_debugging = $input['development_debugging'];
        if ($dev_debugging == 1) {
            $output['development_debugging'] = $input['development_debugging'];
        }
    }

    if (isset($input['development_warning'])) {
        $dev_warning = $input['development_warning'];
        if ($dev_warning == 1) {
            $output['development_warning'] = $input['development_warning'];
        }
    }

    if (isset($input['development_warning_text'])) {
        $development_warning_text = $input['development_warning_text'];
        if (strlen($development_warning_text) > 0) {
            $output['development_warning_text'] = $input['development_warning_text'];
        }
    }

    return apply_filters('sweetapple_theme_options_validate', $output, $input, $defaults);
}
