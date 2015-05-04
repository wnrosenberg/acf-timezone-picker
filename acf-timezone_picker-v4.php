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

    function __construct() {
        // vars
        $this->name = 'timezone_picker';
        $this->label = __('Timezone');
        $this->category = __("Choice",'acf'); // Basic, Content, Choice, etc
        $this->defaults = array(
            // set defaults
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

    /*
    *  create_options()
    *
    *  Create extra options for your field. This is rendered when editing a field.
    *  The value of $field['name'] can be used (like below) to save extra data to the $field
    *
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $field  - an array holding all the field's data
    */
    function create_options($field) {
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
                // get array of timezones for the select field.
                $timezones = self::get_timezone_options(); 
                
                // output the field.
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
    *  Create a select dropdown with all available timezones
    *
    *  @param	$field - an array holding all the field's data
    *
    *  @type	action
    *  @since	3.6
    *  @date	23/01/13
    */
    function create_field($field) {
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


    /*
    *  get_timezone_options()
    *
    *  This function gets the list of timezones from PHP to be used in our plugin.
    *  If output == "options", outputs an array of <option>s, otherwise an array of zones/labels.
    *
    *  @type    internal
    *  @since   3.6
    *  @date    May 4, 2015
    *
    *  @param   $output - a string, "options" or something else
    *  @param   $field  - an array holding all the field's data
    */
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
