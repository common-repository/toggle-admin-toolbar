<?php
  /**
   * Admin Settings Page for Toggle Admin Toolbar Plugin
   * Value set is bpm_tat_options a serialized array
   */

  /**
   * Do option call for bpm_tat_options
   *
   * @param string $option a string associated with option value
   *
   * @return mixed
   * @since 1.0.0
   *
   */
  function bpm_tat_option_settings(string $option)
  {
    $toggleable = get_option('bpm_tat_options');
    if (isset($toggleable[$option])):
      return $toggleable[$option];
    endif;
    return '#000';
  }

  /**
   * Add a menu for our option page
   *
   * @since 1.0.0
   */
  function bpm_tat_add_page()
  {
    add_options_page(
      __('Toggle Admin Toolbar', 'toggle-admin-toolbar'),
      __('Toggle Toolbar', 'toggle-admin-toolbar'),
      'manage_options',
      'bpm_tat',
      'bpm_tat_option_page'
    );
  }

  add_action('admin_menu', 'bpm_tat_add_page');

  /**
   * Draw the option page
   *
   * @since 1.0.0
   */
  function bpm_tat_option_page()
  {
    ?>
    <div class="wrap">
      <h2><?php _e('Toggle Admin Toolbar', 'toggle-admin-toolbar'); ?></h2>
      <form action="options.php" method="post">
        <?php
          settings_fields('bpm_tat_settings');
          do_settings_sections('bpm_tat');
          submit_button();
        ?>
      </form>
    </div>
    <?php
  }

  /**
   * Validate user input
   *
   * @since 1.0.0
   *
   * @param $input array $_POST from settings form submission
   *
   * @return array of setting values
   */
  function bpm_tat_validate_options(array $input): array
  {
    $output = [];

    foreach ($input as $key => $val):

      switch ($key) {

        case 'toggleable':
          $output[$key] = ($val ? 1 : 0);
          break;

        case 'color':

          if (null === sanitize_hex_color($val)):

            $output[$key] = sanitize_hex_color('#000000');

            add_settings_error(
              'color',
              esc_attr('bpm_tat_options'),
              __('The color hex code that you entered was not valid. Codes must begin with a #, and contain 3 or 6 characters.', 'toggle-admin-toolbar')
            );

          else:

            $output[$key] = sanitize_hex_color($val);

          endif;

          break;

      }
    endforeach;

    return $output;
  }

  /**
   * Register and define the settings
   *
   * @since 1.0.0
   */
  function bpm_tat_admin_init()
  {
    register_setting(
      'bpm_tat_settings',
      'bpm_tat_options',
      'bpm_tat_validate_options'
    );
    add_settings_section(
      'bpm_tat_main',
      __('', 'toggle-admin-toolbar'),
      'bpm_tat_section_text',
      'bpm_tat'
    );
    add_settings_field(
      'toggleable',
      __('Keep toggle button visible?', 'toggle-admin-toolbar'),
      'bpm_tat_keep_toggle_button_visible',
      'bpm_tat',
      'bpm_tat_main'
    );
    add_settings_field(
      'color',
      __('Toggle Admin icon color.', 'toggle-admin-toolbar'),
      'bpm_tat_toggle_button_color',
      'bpm_tat',
      'bpm_tat_main'
    );
  }

  add_action('admin_init', 'bpm_tat_admin_init');

  /**
   * Draw the section header
   *
   * @since 1.0.0
   */
  function bpm_tat_section_text()
  {
    echo '<p>';
    __('Configure the function and style of the admin toggle buttons to suit your preferences.', 'toggle-admin-toolbar');
    echo '</p>';
  }

  /**
   * Display and fill the form field
   *
   * @since 1.0.0
   */
  function bpm_tat_keep_toggle_button_visible()
  {
    $toggleable = bpm_tat_option_settings('toggleable');
    ob_start();
    ?>
    <fieldset>
      <p>
        <label for="bpm_tat_toggleable_true">
          <input type="radio" id="bpm_tat_toggleable_true" name="bpm_tat_options[toggleable]" value="1" <?php checked(1, esc_attr($toggleable)); ?>/>
          <?php _e('Yes', 'toggle-admin-toolbar'); ?>
        </label>
        <label for="bpm_tat_toggleable_false">
          <input type="radio" id="bpm_tat_toggleable_false" name="bpm_tat_options[toggleable]" value="0" <?php checked(0, esc_attr($toggleable)); ?>/>
          <?php _e('No', 'toggle-admin-toolbar'); ?>
        </label>
      </p>
    </fieldset>
    <?php
    echo ob_get_clean();
  }

  /**
   * Display and fill the form field
   *
   * @since 1.0.0
   */
  function bpm_tat_toggle_button_color()
  {
    $color = bpm_tat_option_settings('color');
    ob_start();
    ?>
    <fieldset>
      <p>
        <input type="text" id="bpm_tat_toggleable_color" name="bpm_tat_options[color]" value="<?php echo esc_attr($color); ?>"/>
        <label for="bpm_tat_toggleable_color"><?php _e( 'Set a HEX code for the sandwich icon that reopens the admin bar.', 'toggle-admin-toolbar'); ?></label>
      </p>
    </fieldset>
    <?php
    echo ob_get_clean();
  }