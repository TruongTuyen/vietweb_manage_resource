<?php
class Vietveb_ACF_field_resource_theme extends acf_field
{

    // vars
    public $settings; // will hold info such as dir / path
    public $defaults; // will hold default field options

    /*
    *  __construct
    *
    *  Set name / label needed for actions / filters
    *
    *  @since   3.6
    *  @date    23/01/13
    */
    public function __construct()
    {
        // vars
        $this->name = 'resource_theme';
        $this->label = __('Resource Themes');
        $this->category = __('Basic', 'acf'); // Basic, Content, Choice, etc
        $this->defaults = array(
            'multiple'      =>  1,
            'allow_null'    =>  0,
            'choices'       =>  array(),
            'default_value' =>  ''
        );

        // do not delete!
        parent::__construct();

        // settings
        $this->settings = array(
            'path' => apply_filters('acf/helpers/get_path', __FILE__),
            'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
            'version' => '1.0.0'
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
    public function create_options($field)
    {
        $key = $field['name'];

        ?>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e("Default Value", 'acf'); ?></label>
                <p class="description"><?php _e("Enter each default value on a new line", 'acf'); ?></p>
            </td>
            <td>
                <?php
        
                do_action('acf/create_field', array(
                    'type'  =>  'textarea',
                    'name'  =>  'fields['.$key.'][default_value]',
                    'value' =>  $field['default_value'],
                ));
        
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e("Allow Null?",'acf'); ?></label>
            </td>
            <td>
                <?php
                do_action('acf/create_field', array(
                    'type'  =>  'radio',
                    'name'  =>  'fields['.$key.'][allow_null]',
                    'value' =>  $field['allow_null'],
                    'choices'   =>  array(
                        1   =>  __("Yes",'acf'),
                        0   =>  __("No",'acf'),
                    ),
                    'layout'    =>  'horizontal',
                ));
                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e("Select multiple values?",'acf'); ?></label>
            </td>
            <td>
                <?php
                do_action('acf/create_field', array(
                    'type'  =>  'radio',
                    'name'  =>  'fields['.$key.'][multiple]',
                    'value' =>  $field['multiple'],
                    'choices'   =>  array(
                        1   =>  __("Yes",'acf'),
                        0   =>  __("No",'acf'),
                    ),
                    'layout'    =>  'horizontal',
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
    *  @param   $field - an array holding all the field's data
    *
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    */
    public function create_field($field)
    {
        $field['choices'] = $this->value_options();

        // value must be array
        if (!is_array($field['value'])) {
            // perhaps this is a default value with new lines in it?
            if (strpos($field['value'], "\n") !== false) {
                // found multiple lines, explode it
                $field['value'] = explode("\n", $field['value']);
            } else {
                $field['value'] = array( $field['value'] );
            }
        }

        // trim value
        $field['value'] = array_map('trim', $field['value']);

        // multiple select
        $multiple = '';
        if ($field['multiple']) {
            // create a hidden field to allow for no selections
            echo '<input type="hidden" name="' . $field['name'] . '" />';

            $multiple = ' multiple="multiple" size="5" ';
            $field['name'] .= '[]';
        }

        // html
        echo '<select id="' . $field['id'] . '" class="' . $field['class'] .
            '" name="' . $field['name'] . '" ' . $multiple . ' >';
        
        // null
        if ($field['allow_null']) {
            echo '<option value="null">- ' . __("Select", 'acf') . ' -</option>';
        }

        // loop through values and add them as options
        foreach ($field['choices'] as $choice) {
            $selected = in_array($choice['value'], $field['value']) ? 'selected="selected"' : '';
            echo '<option value="'.$choice['value'].'" '.$selected.'>'.$choice['label'].'</option>';
        }

        echo '</select>';
        echo "<script type='text/javascript'>
                $(document).ready(function() {
                    if( jQuery.fn.select2 ){
                        $('#{$field['id']}').select2();
                    }
                });
            </script>";
    }

    /**
     * Outputs Post Types in HTML option tags
     *
     * @return array Array of post type names and labels
     */
    private function value_options()
    {
        $args = apply_filters('vietveb_acf_field_resource_theme/get_post_types_args', array( 
            'post_type' => 'vietweb_resource',
            'meta_query' => array(
        		array(
        			'key'     => '_vietveb_type_of_resouce',
        			'value'   => 'theme',
        			'compare' => '=',
        		),
        	),
        ));
        
        $resource = new WP_Query( $args );
        
        $output = array();
        if( $resource->have_posts() ){
            while( $resource->have_posts() ){
                $resource->the_post();
                $output[] = array(
                    'value' => get_the_ID(),
                    'label' => get_the_title()
                );
            }
        }
        
        return $output;
    }
}

// create field
new Vietveb_ACF_field_resource_theme();
