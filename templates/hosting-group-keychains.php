<?php

global $post;

$control_panel_keychain_id = get_post_meta( $post->ID, '_orbis_hosting_group_control_panel_keychain_id', true );

if ( ! empty( $control_panel_keychain_id ) ) : ?>

	<div class="panel">
		<header>
			<h3><?php esc_html_e( 'Keychains', 'orbis_keychains' ); ?></h3>
		</header>

		<div class="content">
			<dl>
				<dt>
					<?php esc_html_e( 'Control Panel', 'orbis_keychains' ); ?>
				</dt>
				<dd>
					<a href="<?php echo esc_attr( get_permalink( $control_panel_keychain_id ) ); ?>">
						<?php echo get_the_title( $control_panel_keychain_id ); ?>
					</a>
				</dd>
			</dl>
		</div>
	</div>

<?php endif; ?>
