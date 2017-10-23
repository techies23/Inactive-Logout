<?php $result_roles = $this->helper->ina_get_all_roles(); ?>
<div id="message" class="updated notice is-dismissible" style="display: none;"></div>

<form method="post" action="?page=inactive-logout&tab=ina-advanced">
  <?php wp_nonce_field( '_nonce_action_save_timeout_adv_settings', '_save_timeout_adv_settings' ); ?>
  <table class="ina-form-tbl form-table">
    <tbody>
      <tr>
        <th scope="row"><label for="ina_enable_different_role_timeout"><?php _e("Multi-Role Timeout", "inactive-logout"); ?></label></th>
        <td>
          <input name="ina_enable_different_role_timeout" type="checkbox" id="ina_enable_different_role_timeout" <?php echo !empty($ina_multiuser_timeout_enabled) ? "checked" : false; ?> value="1" >
          <p class="description"><?php _e("This will enable multi-user role timeout functionality.", "inactive-logout"); ?></p>
        </td>
      </tr>
      <tr class="ina-multi-role-table">
        <th scope="row"><label for="idle_timeout"><?php _e("Enable Multi-User Feature", "inactive-logout"); ?></label></th>
        <td>
          <select class="ina-hacking-multi-select" id="ina_definetime_specific_userroles" multiple="multiple" name="ina_multiuser_roles[]">
            <?php
            foreach ($result_roles as $k => $role) {
              $selected = $this->helper->ina_check_role_enabledfor_multiuser($k);
              ?>
              <option value="<?php echo $k; ?>" <?php echo !empty($selected) ? 'selected' : FALSE; ?>><?php echo $role; ?></option>
              <?php
            }
            ?>
          </select>
          <p class="description"><i><?php _e("This will allow you to define different timeout constraint according to different selected user roles.", "inactive-logout"); ?></i></p>
        </td>
      </tr>
    </tbody>
  </table>
  <?php if( !empty($ina_multiuser_settings) ) { ?>
  <table class="ina-form-tbl ina-multi-role-table wp-list-table widefat fixed striped pages">
    <thead>
      <th class="manage-column" width="10%"><?php _e("User Role", "inactive-logout"); ?></th>
      <th class="manage-column" width="15%"><?php _e("Timeout (In Minutes)", "inactive-logout"); ?></th>
      <th class="manage-column" width="40%"><?php _e("Redirect Page", "inactive-logout"); ?></th>
      <th class="manage-column" width="10%"><?php _e("Disable", "inactive-logout"); ?></th>
      <th class="manage-column" width="20%"><?php _e("Disable Concurrent Login", "inactive-logout"); ?></th>
    </thead>
    <tbody>
      <?php
      foreach ($ina_multiuser_settings as $k => $ina_multiuser_setting) {
        $role = $ina_multiuser_setting['role'];
        ?>
        <tr>
          <td><?php echo $result_roles[$role]; ?></td>
          <td><input type="number" min="1" value="<?php echo !empty($ina_multiuser_setting['timeout']) ? $ina_multiuser_setting['timeout'] : 15; ?>" name="ina_individual_user_timeout[]"></td>
          <td>
            <select name="ina_redirect_page_individual_user[]" class="regular-text ina-hacking-select">
              <option value="0"><?php _e("Set Global Redirect Page", "inactive-logout"); ?></option>
              <?php
              $posts = Inactive__Logout_functions::ina_get_all_pages_posts();
              if( $posts ) { ?>
              <optgroup label="Posts">
                <?php foreach( $posts as $post ) {
                  if( $post['post_type'] == 'post' ) { ?>
                  <option <?php echo $ina_multiuser_setting['redirect_page'] == $post['ID'] ? 'selected' : NULL; ?> value="<?php echo $post['ID']; ?>"><?php echo $post['title']; ?></option>
                  <?php }
                }
                ?>
              </optgroup>
              <optgroup label="Pages">
                <?php foreach( $posts as $post ) {
                  if( $post['post_type'] == 'page' ) { ?>
                  <option <?php echo $ina_multiuser_setting['redirect_page'] == $post['ID'] ? 'selected' : NULL; ?> value="<?php echo $post['ID']; ?>"><?php echo $post['title']; ?></option>
                  <?php }
                }
                ?>
              </optgroup>
              <?php
            } else {
              ?>
              <option value=""><?php _e("No Posts Found.", "inactive-logout"); ?></option>
              <?php
            }
            ?>
          </select>
        </td>
        <td><input type="checkbox" name="ina_disable_inactive_logout[<?php echo $role; ?>]" <?php echo !empty($ina_multiuser_setting['disabled_feature']) ? 'checked' : false; ?> value="1"></td>
        <td><input type="checkbox" name="ina_disable_inactive_concurrent_login[<?php echo $role; ?>]" <?php echo !empty($ina_multiuser_setting['disabled_concurrent_login']) ? 'checked' : false; ?> value="1"></td>
      </tr>
      <?php }  ?>
    </tbody>
  </table>
  <?php $bold_string = '<span class="ina-highlight"><strong>"' . __("Disable", "inactive-logout") . '"</strong></span>'; ?>
  <p class="hide-description-ina description ina-warn-info" style="float:right;"><?php printf(__("Note: %s is used for disabling inactive logout functionality to that specific user.", "inactive-logout"), $bold_string ); ?></p>
  <?php } ?>
  <p class="ina_adv_submit"><input type="submit" name="adv_submit" id="submit" class="button button-primary" value="<?php _e("Save Changes", "inactive-logout"); ?>"> <a id="ina-reset-adv-data" class="button button-primary button-reset-ina" data-msg="<?php _e('Are you sure you want to erase all advanced settings. This cannot be undone !', 'inactive-logout'); ?>"><?php _e("Reset Advanced Settings !", "inactive-logout"); ?></a></p>
</form>