<?php

/*

Plugin Name:  A custom post type.
Description:  Gestion d'un custom post type avec écran de réglages.
Version:      1.0

*/

if( !class_exists( 'Custom_Post_Type_With_Settings' ) ) {

	/**
	 * Class Custom_Post_Type_With_Settings
	 * 
	 * @author Benoit MERCUSOT <benoit@mbcreation.net>
	 * 
	 * @version 1.0
	 * @since 1.0
	 * 
	 */

	class Custom_Post_Type_With_Settings {

			protected $_post_type = 'portfolio';

			protected $_post_type_name = 'Réalisation';
			
			protected $_post_type_archive_slug = 'portfolio';

			protected $_post_type_icon = 'dashicons-lightbulb';

			protected $_post_type_menu_position = 18;

			protected $_post_type_supports = array('title','editor','thumbnail','excerpt','custom-fields');

			protected $options;

			protected $_setting_page_name = 'Réglages';	

			protected $_setting_page_slug;

			protected $options_name;
	
			protected $options_group;

			protected $default_options  = array(

				'archive-title' => '',
				'archive-description' => '',
				'archive-thumbnail' => '',
				'order_by' => 'title',
				'order' => 'ASC',
				'posts_per_page' => 4
	
			);

			protected $_plugin_path;

			protected $_posts_per_page;

			protected $_orderby;

			protected $_order;

			public function __construct(){

				$this->_plugin_path = dirname(__FILE__);

				$this->_setting_page_slug = 'ctp-settings-'.$this->_post_type;
				
				$this->options_name = 'ctp-'.$this->_post_type.'-options';

				$this->options_group = 'ctp-'.$this->_post_type.'-group';

				register_activation_hook( __FILE__ , array( &$this, 'install' ) );

				add_action('plugins_loaded', array(&$this, 'hooks' ) );

			}


			public function install(){

				$this->register_post_type();
				flush_rewrite_rules(false);

				update_option( $this->options_name , $this->default_options );

			}


			public function hooks(){

			
				$this->set_options();	

		
				add_action( 'init', array(&$this, 'register_post_type' )  );
	
				add_action( 'pre_get_posts' , array(&$this, 'pre_get_posts') );


				if( is_admin() && current_user_can('manage_options' ) ) :
					
					add_action( 'admin_init', array( $this, 'options_init') );
					add_action( 'load-'.$this->_post_type.'_page_'.$this->_setting_page_slug, array( $this, 'plugin_admin_boostrap' ) );
					add_action( 'admin_menu', array( $this, 'add_plugin_menu' ) );
				
				endif;


				// Template action

				add_action( $this->_post_type.'_archive_title',function() {

					echo $this->options['archive-title'];

				});

				add_action( $this->_post_type.'_archive_description',function(){

					echo $this->options['archive-description'];

				});

				add_action( $this->_post_type.'_archive_thumbnail',function(){

					echo '<img src="'.esc_url($this->options['archive-thumbnail']).'" alt="'.esc_attr($this->options['archive-title']).'" />';

				});


			

			}
			
			/**
			 *
			 * @hooked init
			 *
			 * @access public
			 * @version 1.0
			 * @since 1.0
			 * 
			 * @return void
			 */
			public function register_post_type(){

				register_post_type( $this->_post_type , 
					
					array( 'labels' => array(

						'name' => $this->_post_type_name, /* This is the Title of the Group */
						'singular_name' => $this->_post_type_name, /* This is the individual type */
						'all_items' => $this->_post_type_name, /* the all items menu item */
						'add_new' => __( 'Ajouter' ), /* The add new menu item */
						'add_new_item' => __( 'Ajouter' ), /* Add New Display Title */
						'edit' => __( 'Modifier' ), /* Edit Dialog */
						'edit_item' => __( 'Modifier' ), /* Edit Display Title */
						'new_item' => __( 'Nouveau' ), /* New Display Title */
						'view_item' => __( 'Voir' ), /* View Display Title */
						'search_items' => __( 'Rechercher' ), /* Search Custom Type Title */
						'not_found' =>  __( 'Aucun élement trouvé' ), /* This displays if there are no entries yet */
						'not_found_in_trash' => __( 'Aucun élement trouvé dans la corbeille' ), /* This displays if there is nothing in the trash */
						'parent_item_colon' => ''
						), 
						'public' => true,
						'publicly_queryable' => true,
						'exclude_from_search' => false,
						'show_ui' => true,
						'query_var' => true,
						'menu_position' => $this->_post_type_menu_position,
						'menu_icon' => $this->_post_type_icon, 
						'rewrite'	=> array( 'slug' => $this->_post_type_archive_slug , 'with_front' => false ), /* you can specify its url slug */
						'has_archive' => $this->_post_type_archive_slug , /* you can rename the slug here */
						'capability_type' => 'post',
						'hierarchical' => false,
						'supports' => $this->_post_type_supports
					) 
				); 

				
			}

		
			 public function pre_get_posts($query){

				
				if ( is_admin() || ! $this->is_cpt() || ! $query->is_main_query() )
				return;
			
				$query->set( 'orderby', $this->options['order_by'] );
				$query->set( 'order', $this->options['order'] );
				$query->set( 'posts_per_archive_page', $this->options['posts_per_page'] );


			 }


			public function is_cpt() {

				         return ( 

				         	is_single() && get_post_type() == $this->_post_type 
				         	|| is_post_type_archive( $this->_post_type )

				         ) ? true : false;
				     				
			}



			public function get_options()
			
			{

				return wp_parse_args( get_option($this->options_name), $this->default_options );

			}

			public function set_options()
			{

				$this->options = wp_parse_args( get_option($this->options_name), $this->default_options );

			}

			public function add_plugin_menu()

			{
				
				add_submenu_page( 'edit.php?post_type='.$this->_post_type, $this->_setting_page_name, $this->_setting_page_name , 'manage_options', $this->_setting_page_slug, array($this,'options_panel')  );
			
			} 

			public function options_init()
			{

				register_setting( $this->options_group, $this->options_name , array( $this, 'options_validate' ) );

			} 

			public function plugin_admin_boostrap()
			{	

			

			} 


			/**
			 * HTML Page admin
			 * 
			 * @since 1.0
	         * @version 1.0
			 * @access public
			 * 
			 * @return void
			 */
			public function options_panel()
			{ 
				if ( ! current_user_can( 'manage_options' ) )
				wp_die( __( 'You do not have sufficient permissions to manage options for this site.','kopines-selection' ) );

			?>
				
				<div class="wrap">
				

					<h2>Réglages de l'archive <?php echo $this->_post_type_name;?></h2>
					
					<form method="post" action="options.php">

					<?php settings_fields( $this->options_group ); ?>
							
					<table class="form-table">
					<tbody>
						<tr>
						<th scope="row"><label for="archive-title">Titre de rubrique</label></th>
						<td><input name="<?php echo $this->options_name;?>[archive-title]" type="text" id="archive-title" value="<?php echo $this->options['archive-title'];?>" class="regular-text"></td>
						</tr>
						<tr>
						<th scope="row"><label for="archive-thumbnail">Illustration de rubrique</label></th>
						<td><input name="<?php echo $this->options_name;?>[archive-thumbnail]" type="text" id="archive-thumbnail" value="<?php echo $this->options['archive-thumbnail'];?>" class="regular-text"></td>
						</tr>
						<tr>
						<th scope="row">Texte d'introduction</th>
						<td><textarea name="<?php echo $this->options_name;?>[archive-description]" rows="10" cols="50" class="large-text code"><?php echo $this->options['archive-description'];?></textarea></td>
						</tr>
						<tr>
						<th scope="row">Paramètres d'affichage</th>
						<td>
						<label for="<?php echo $this->options_name;?>[posts_per_page]">Nombre de <?php echo $this->_post_type_name;?> par page :</label>
						<input name="<?php echo $this->options_name;?>[posts_per_page]" type="number" step="1" min="-1" id="<?php echo $this->options_name;?>[posts_per_page]" value="<?php echo $this->options['posts_per_page'];?>" class="small-text">
						<label>Ordonné par :</label>
							<select name="<?php echo $this->options_name;?>[order_by]">
								<option value="ID" <?php selected( 'ID', $this->options['order_by'] );?>>ID</option>
								<option value="author" <?php selected( 'author', $this->options['order_by'] );?>>Auteur</option>
								<option value="title" <?php selected( 'title', $this->options['order_by'] );?>>Titre</option>
								<option value="date" <?php selected( 'date', $this->options['order_by'] );?>>Date</option>
								<option value="modified" <?php selected( 'modified', $this->options['order_by'] );?>>Date de modification</option>
								<option value="rand" <?php selected( 'rand', $this->options['order_by'] );?>>Aléatoire</option>
							</select>
							<select name="<?php echo $this->options_name;?>[order]">
								<option value="ASC" <?php selected( 'ASC', $this->options['order'] );?>>Croissant</option>
								<option value="DESC" <?php selected( 'DESC', $this->options['order'] );?>>Décroissant</option>
							</select>
						</td>
						</tr>
					</tbody>
					</table>
						
					<?php submit_button(); ?>
					</form>
				</div>




			<?php 

			}

			public function options_validate($options){

				flush_rewrite_rules(false);
				return $options;
			
			} 


		
	}

	$my_post_type = new Custom_Post_Type_With_Settings();
}

