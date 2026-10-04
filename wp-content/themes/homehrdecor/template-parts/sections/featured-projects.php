<?php
/**
 * Featured projects — an asymmetric editorial grid.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_featured = hhd_projects( array( 'khane-aram', 'villa-aftab', 'apartment-12', 'cafe-art' ) );
?>
<section class="section section--surface" id="featured-projects">
	<div class="container container--wide">
		<div class="featured-projects">
			<div class="featured-projects__head">
				<?php
				echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'eyebrow' => 'پروژه‌ها',
						'title'   => 'پروژه‌های منتخب',
						'text'    => 'نگاهی به برخی از فضاهایی که با دقت، خلاقیت و توجه به جزئیات طراحی شده‌اند.',
					)
				);
				?>
				<div class="reveal">
					<?php
					echo hhd_link( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label' => 'همه پروژه‌ها',
							'url'   => hhd_page_url( 'projects' ),
						)
					);
					?>
				</div>
			</div>

			<?php
			$hhd_layouts = array( 'landscape', 'portrait', 'portrait', 'landscape' );
			foreach ( $hhd_featured as $hhd_index => $hhd_project ) :
				hhd_project_card(
					$hhd_project,
					array(
						'layout' => isset( $hhd_layouts[ $hhd_index ] ) ? $hhd_layouts[ $hhd_index ] : 'portrait',
						'class'  => 'reveal',
						'sizes'  => '(max-width: 720px) 100vw, (max-width: 1080px) 50vw, 40vw',
						'index'  => $hhd_index,
					)
				);
			endforeach;
			?>
		</div>
	</div>
</section>
