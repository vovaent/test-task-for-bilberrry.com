<?php
/**
 * Create a class and register a custom type field TTB GF Test Product.
 *
 * @package Ttb_GF_Test_Product_Field
 */

if ( ! class_exists( 'GF_Field' ) ) {
	return;
}

/**
 * TTB GF Test Product Field
 */
class TTB_GF_Field_Test_Product extends GF_Field {
	/**
	 * Field type.
	 *
	 * @var string
	 */
	public $type = 'ttb_gf_test_product';

	/**
	 * Initializing Hooks
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'gform_field_standard_settings', array( $this, 'form_field_standard_settings' ), 10, 2 );
		add_action( 'gform_editor_js', array( $this, 'editor_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_wp_media_files' ) );
		add_filter( 'gform_field_content', array( $this, 'form_field_content' ), 10, 2 );
	}

	/**
	 * Returns the field title.
	 *
	 * @return string
	 */
	public function get_form_editor_field_title(): string {
		return esc_attr__( 'Test Product' );
	}

	/**
	 * Returns the class names of the settings which should be available on the field in the form editor.
	 *
	 * @return array
	 */
	public function get_form_editor_field_settings(): array {
		return array(
			'ttb_gf_image_setting',
			'label_setting',
			'description_setting',
		);
	}

	/**
	 * Generates HTML attribute Image for field settings.
	 *
	 * @param int $position Attribute position.
	 * @param int $form_id Form ID.
	 *
	 * @return void
	 */
	public function form_field_standard_settings( int $position, int $form_id ): void {
		$image_id = '';
		$form     = GFAPI::get_form( $form_id );

		foreach ( $form['fields'] as $field ) {
			if ( $this->type === $field->type ) {
				$image_html = $this->ttb_gf_get_image_html( $field );

				if ( property_exists( $field, 'ttb_gf_field_image_id' ) ) {
					$image_id = $field->ttb_gf_field_image_id;
				}

				break;
			}
		}

		$remove_button_display = $image_id ? 'inline-block' : 'none';
		$add_button_text       = $image_id ? __( 'Replace image' ) : __( 'Add image' );

		if ( 0 === $position ) {
			?>
			<li class="ttb_gf_image_setting field_setting">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $image_html;
				?>
				<input type="hidden" id="ttb_gf_field_image_id" value="<?php echo esc_attr( $image_id ); ?>" />
				<input type='button' class="button-secondary" value="<?php echo esc_attr( $add_button_text ); ?>"
						id="ttb_gf_add_image_button" />
				<input type='button' class="button-secondary" value="<?php esc_attr_e( 'Remove image' ); ?>"
						id="ttb_gf_remove_image_button" style="<?php echo esc_attr( $remove_button_display ); ?>" />
			</li>
			<?php
		}
	}

	/**
	 * Editor script.
	 *
	 * @return void
	 */
	public function editor_script(): void {
		?>
		<script type='text/javascript'>
			let imageFieldId;
			let mediaFrame;

			const jqDocument = jQuery(document);
			const addImageButton = jQuery("#ttb_gf_add_image_button");
			const removeImageButton = jQuery("#ttb_gf_remove_image_button");
			const imageIdInputField = jQuery("#ttb_gf_field_image_id");
			const settingsImagePreviewContainer = jQuery("#ttb_gf_settings_image_preview_container");
			const settingsImagePreview = settingsImagePreviewContainer.find("#ttb_gf_image_preview");
			const formImagePreviewContainer = jQuery("#ttb_gf_form_image_preview_container");
			const formImagePreview = formImagePreviewContainer.find("#ttb_gf_image_preview");

			jqDocument.on("gform_load_field_settings", ttbGfLoadFieldSettingsHandler);
			jqDocument.on("gform_field_deleted", ttbGfLoadFieldDeletedHandler);

			function ttbGfLoadFieldSettingsHandler(event, field) {
				imageFieldId = field.id;

				addImageButton.click(function(e) {
					e.preventDefault();

					if (typeof mediaFrame !== "undefined") {
						mediaFrame.open();
						return;
					}

					mediaFrame = wp.media({
						title: "Select or Upload Media Of Your Chosen Persuasion",
						multiple: false,
						library: {
							type: "image"
						}
					});

					mediaFrame.on("select", function() {
						const attachment = mediaFrame.state().get("selection").first().toJSON();

						imageIdInputField.val(attachment.id);
						ttbGfRefreshImage(attachment.id);
						SetFieldProperty("ttb_gf_field_image_id", attachment.id);
						removeImageButton.show();
					});

					mediaFrame.on("open", function() {
						const selection = mediaFrame.state().get("selection");
						const attachment = wp.media.attachment(imageIdInputField.val());

						attachment.fetch();
						selection.add(attachment ? [attachment] : []);
					});

					mediaFrame.open();
				});

				removeImageButton.click(function(e) {
					e.preventDefault();

					removeImageButton.hide();
					settingsImagePreviewContainer.hide();
					formImagePreviewContainer.hide();
					imageIdInputField.val("");
					addImageButton.val("<?php esc_attr_e( 'Add image' ); ?>");
					SetFieldProperty("ttb_gf_field_image_id", "");
				});
			}

			function ttbGfLoadFieldDeletedHandler(event, form, fieldId) {
				if (parseInt(fieldId) === imageFieldId) {
					settingsImagePreviewContainer.hide();
					imageIdInputField.val("");
					addImageButton.val("<?php esc_attr_e( 'Add image' ); ?>");
				}
			}

			function ttbGfRefreshImage(the_id) {
				wp.media.attachment(the_id).fetch().then(function(attachment) {
					if (typeof attachment.sizes.thumbnail.url !== "undefined") {
						settingsImagePreview.attr("src", attachment.sizes.thumbnail.url);
						settingsImagePreviewContainer.show();
						formImagePreview.attr("src", attachment.sizes.medium.url);
						formImagePreviewContainer.show();
					}
				});

				addImageButton.val("<?php esc_attr_e( 'Replace image' ); ?>");
			}
		</script>
		<?php
	}

	/**
	 * Queues media library scripts.
	 *
	 * @param string $page Admin settings page.
	 *
	 * @return void
	 */
	public function load_wp_media_files( string $page ): void {
		if ( 'toplevel_page_gf_edit_forms' === $page ) {
			wp_enqueue_media();
		}
	}

	/**
	 * Creates image markup to display in the form preview.
	 *
	 * @param Object $field Field object data.
	 * @param string $location Image location.
	 *
	 * @return string
	 */
	public function ttb_gf_get_image_html( object $field, $location = 'settings' ): string {
		$image_container_hidden_style = 'display: none;';

		if ( 'settings' === $location ) {
			$image_container_shown_style = '';
			$image_size                  = 'thumbnail';
		} else {
			$image_container_shown_style = 'display: flex; align-items: center; justify-content: center; margin-bottom: 20px; padding: 50px 0; background-color: #f1efef;';
			$image_size                  = 'medium';
		}

		$image_container_style = $image_container_hidden_style;
		$image_src             = '';

		$image_html  = '<div id="ttb_gf_%s_image_preview_container" style="%s">';
		$image_html .= '<img src="%s" id="ttb_gf_image_preview" alt="ttb_gf_image_preview">';
		$image_html .= '</div>';

		$image_src_data = array();

		if ( property_exists( $field, 'ttb_gf_field_image_id' ) ) {
			$image_src_data = wp_get_attachment_image_src( $field->ttb_gf_field_image_id, $image_size );
		}

		if ( $image_src_data ) {
			$image_src             = $image_src_data[0];
			$image_container_style = $image_container_shown_style;
		}

		return sprintf( $image_html, esc_attr( $location ), esc_attr( $image_container_style ), esc_attr( $image_src ) );
	}

	/**
	 * Adds image markup to display in the form preview.
	 *
	 * @param array|string $field_content Field content.
	 * @param Object       $field Field object data.
	 *
	 * @return array|string
	 */
	public function form_field_content( array|string $field_content, object $field ): array|string {
		if ( $this->type === $field->type ) {
			$image_html = $this->ttb_gf_get_image_html( $field, 'form' );

			return str_replace( '<label', $image_html . '<label', $field_content );
		}

		return $field_content;
	}
}

$ttb_gf_field_test_product = new TTB_GF_Field_Test_Product();

try {
	GF_Fields::register( $ttb_gf_field_test_product );
} catch ( Exception $e ) {
	wp_die( esc_html( $e->getMessage() ) );
}

$ttb_gf_field_test_product->init();
