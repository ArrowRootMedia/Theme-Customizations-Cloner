<?php
class Theme_Customizations_Cloner_Admin {

	var $export_page_id;
	var $options_group = 'theme-customizations-cloner';
        var $error="";
	/**
	 * Constructor
	 */
	function Theme_Customizations_Cloner_Admin() {

		global $wp_version;

		add_action('plugins_loaded',array(__CLASS__,'plugins_loaded'));
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        }

	/**
	 * Admin Menu
	 */
	function admin_menu() {

		$this->settings_page_id = add_utility_page( 'Theme Customizations Cloner', 'Theme Customizations Cloner', 'manage_options', 'theme-cust-cloner', array( $this, 'utilities_page' ), '' );
                        
	}
	
	/*
	   Trigger download
	*/
   static function plugins_loaded() 
   {
        if(wp_verify_nonce( $_REQUEST['_wpnonce'], 'export'))
        {
            if(current_user_can( 'manage_options' ))
            {
               if(isset($_POST['export_theme']))
               {
                  $export_theme=esc_attr($_POST['export_theme']);
                  $mods=get_option('theme_mods_'.$export_theme);
                  if(!is_array($mods))
                  {
                      echo "Theme modifications are not valid";
                      return false;
                  }

                  header( 'Content-Description: File Transfer' );
                  header('Content-Type: text/plain');
                  header('Content-Disposition: attachment; filename="'.$export_theme.'.json"');
                  header("Pragma: no-cache");
                  header("Expires: 0");
                  echo json_encode( $mods);
                  exit();
               }        

            }
            else
                echo "Possible resubmit or action not authorized.";
        }
        
    
        
      
    }
	/**
	 * Utilities Page
	 */
	function utilities_page() {
           
            $themes=wp_get_themes();
           ?>
            <div class="wrap">
                <div id="icon-options-general" class="icon32"><br /></div>
                <h2>Make Exporter for Theme Foundry</h2>
                <?php echo $this->error."<br>";?>
		<form action="" method="post" enctype="multipart/form-data">
	<?php
            
	    if(current_user_can( 'manage_options' ))
            {
                wp_nonce_field( 'export' );
                if(isset($_POST['import_theme']))
                {
                    if(wp_verify_nonce( $_REQUEST['_wpnonce'], 'import'))
                    {
                        $import_theme=esc_attr($_POST['import_theme']);
                        if(strlen($_FILES['themeMods']['tmp_name'])>0)
                        {
                            $mods=file_get_contents($_FILES['themeMods']['tmp_name']);
                            $modArr=json_decode(sanitize_text_field($mods), true);
                            if(is_array($modArr))
                                update_option('theme_mods_'.$import_theme,$modArr);
                            else
                                echo "Theme modifications in uploaded file are not valid";
                        }
                    }
                    else
                        echo "Possible resubmit or action not authorized.";
                  
                } 
				   echo "<p>Choose theme to export modifications:</p>";
				   echo "<p><select id='export_theme' name='export_theme'>";
				   foreach($themes as $theme_slug=>$theme)
               {
                  echo "<option value='".$theme_slug."'>".$theme->Name."</option>";
               }
               echo "</select></p>";
               echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Export modifications"></p>';
               echo '</form><form method="post" enctype="multipart/form-data">';
               wp_nonce_field( 'import' );
				   echo "<p>Warning! Importing theme modifications will overwrite current theme customizations. Please export if you want to save them.<br>Please select theme to import modifications:</p>";
				   echo "<p><select id='import_theme' name='import_theme'>";
				   foreach($themes as $theme_slug=>$theme)
               {
                  echo "<option value='".$theme_slug."'>".$theme->Name."</option>";
               }
               echo "</select></p>";
				   echo "<p>Choose modification file:</p>";
				   echo '<input type="file" name="themeMods">';
				   echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Import modifications"></p>';
				}
				else
				   echo "Not enough permissions. Please contact administrator to allow to manage options";
				?>
				
			</form>
		</div>

		<?php
	}

	

	

	
}

