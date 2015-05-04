<?php

/* 
 * Edited by Will Rosenberg / Response Mktg
 * @TODO: Add option to output either the timezone name 'America/New_York' or the hours offset from GMT.
 */

class acf_field_timezone_picker extends acf_field {

    // vars
    var $settings, // will hold info such as dir / path
        $defaults; // will hold default field options


    /*
    *  __construct
    *
    *  Set name / label needed for actions / filters
    *
    *  @since	3.6
    *  @date	23/01/13
    */

    function __construct()
    {
        // vars
        $this->name = 'timezone_picker';
        $this->label = __('Timezone');
        $this->category = __("Choice",'acf'); // Basic, Content, Choice, etc
        $this->defaults = array(
            // add default here to merge into your field.
            // This makes life easy when creating the field options as you don't need to use any if( isset('') ) logic. eg:
            //'preview_size' => 'thumbnail'
            'default_timezone' => 'America/New_York',
        );

        // do not delete!
        parent::__construct();

        // settings
        $this->settings = array(
            'path' => apply_filters('acf/helpers/get_path', __FILE__),
            'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
            'version' => '1.0.1'
        );
    }

    
    function create_options( $field )
    {
        // defaults?
        $field = array_merge($this->defaults, $field);
        
        // key is needed in the field names to correctly save the data
        $key = $field['name'];
        $utc = new DateTimeZone('UTC');
        $dt = new DateTime('now', $utc);
        
        
        // Create Field Options HTML
        ?>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e("Default Timezone",'acf'); ?></label>
                <p class="description"><?php _e("Select the Default Timezone to be used for new posts.",'acf'); ?></p>
            </td>
            <td>
                <?php
                $timezones = self::get_timezone_options(); // get array of timezones for the select field.
                
                do_action('acf/create_field', array(
                    'type'      =>  'select',
                    'name'      =>  'fields['.$key.'][default_timezone]',
                    'value'     =>  $field['default_timezone'],
                    'layout'    =>  'horizontal',
                    'choices'   =>  $timezones
                ));
                
                ?>
            </td>
        </tr>
        <?php
        
    }


    /*
    *  create_field()
    *
    *  Create the HTML interface for your field
    *
    *  @param	$field - an array holding all the field's data
    *
    *  @type	action
    *  @since	3.6
    *  @date	23/01/13
    */

    function create_field( $field )
    {
        /*
        *  Create a select dropdown with all available timezones
        */
        $utc = new DateTimeZone('UTC');
        $dt = new DateTime('now', $utc);
        ?>
        <select name="<?php echo esc_attr($field['name']) ?>">
            <?php
            $timezones = self::get_timezone_options('options',$field);
            echo implode("\n            ", $timezones);
            ?>
        </select>
    <?php
    }

    // This function gets the list of timezones from PHP to be used in our plugin.
    // When $output = options --> output array of html <option>s to be used in a <select> element.
    // When $output != options --> output array of [tz_identifier] -> [tz_label].
    private function get_timezone_options($output = null, $field = null) {
        $output_options = false;
        if ($output == "options") {
            $output_options = true;
        }

        // dim vars
        $utc = new DateTimeZone('UTC');
        $dt = new DateTime('now', $utc);
        $tz_output = array();

        // loop
        foreach (\DateTimeZone::listIdentifiers() as $tz) {
            $current_tz = new \DateTimeZone($tz);
            $transition = $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
            $abbr = $transition[0]['abbr'];
            $trimtz = trim($tz);
            if ($output_options) {
                if (isset($field['value'])) {
                    $tz_output[] = '<option value="'. $trimtz .'" '.($trimtz == trim($field["value"]) ? 'selected="selected"' : '') . '>' . $tz . ' (' . $abbr . ')' . '</option>';
                } else {
                    $tz_output[] = '<option value="'. $trimtz .'">' . $tz . ' (' . $abbr . ')' . '</option>';
                }
            } else {
                $tz_output[] = $tz . ' (' . $abbr . ')';
            }
        }
        return $tz_output;
    }
}


// create field
new acf_field_timezone_picker();

?>
