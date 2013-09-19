<?php	if (($this->_options['wpns_preview_pages'] == 1) and ($this->_options['wpns_display_if_one_page'] !='1')) : ?>

	<i><?php _e('Nothing to display as there is only one page','page-navi-slider');?><br>
	<?php _e('You can change the &ldquo;Display even if there is only one page&rdquo; option in general settings','page-navi-slider');?></i>
	
<?php	else: ?>
	<div style="border : 1px dotted #C4C4C4; background-color : <?php echo $this->_options['wpns_preview_background_color']; ?>"><i> <?php _e('Your page','page-navi-slider'); ?></i>
		<?php
			for ($i = 0; $i < $this->_options['wpns_preview_pages']; $i++){
				$pagination[$i]='<a class="page-numbers" href="#">'.($i+1).'</a>';
			}
			wpns_frontend($this->_options['wpns_preview_current'],$this->_options['wpns_preview_pages'],$pagination,$this->_options,true);
		?>
		<i><?php _e('rest of your page...','page-navi-slider'); ?></i>
	</div>

<?php endif; ?>

