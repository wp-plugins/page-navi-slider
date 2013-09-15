<?php
function wpns_frontend($current,$nbpages,$page_links,$settings,$preview){
	if ((( $nbpages ==1 ) and ($settings['wpns_display_if_one_page']=='1'))||( $nbpages > 1)){	
	
		$output = "<div class='wpns_wrapper' style='".($preview ? "border : 1px dotted #C4C4C4;" : "")."'>";
		$output .= "<div class='wpns_container'>";
		
		$title=str_replace('%%NUMBER%%',$current,__($settings['wpns_caption_text'],'page-navi-slider'));
		$title=str_replace('%%TOTAL%%',$nbpages, $title);
		
		if (($settings['wpns_caption_position'] == 'top') and ($title != '')){
			$output .="<div class='wpns_title' style='text-align:".$settings['wpns_caption_alignment']."; font : ".$settings['wpns_caption_font'].";'>".$title."</div>";
			$output .= selector($current,$nbpages,$page_links,$settings);
		}
		
		if (($settings['wpns_caption_position'] == 'bottom') and ($title != '')){
			$output .= selector($current,$nbpages,$page_links,$settings);
			$output .="<div class='wpns_title' style='text-align:".$settings['wpns_caption_alignment']."; font : ".$settings['wpns_caption_font'].";'>".$title."</div>";
		}
		
		if (($settings['wpns_caption_position'] == 'hidden') || ($title == '')){
			$output .= selector($current,$nbpages,$page_links,$settings);
		}
				
		$output .="</div>";
		$output .="</div>";
		$output .= "<script type='text/javascript' >page_navi_slider($current,".json_encode($settings).");</script>";
		echo $output;
	}
}

function selector($current,$nbpages,$page_links,$settings){
	$output = "<div class='wpns_selector'>";	
		$output .= "<div class='wpns_window'>";
			$output .= "<ul class='wpns_sliding_list'>";
				for ($i = 0; $i < $nbpages; $i++) {
					if ($i == $current-1){
						$output .= "<li class='wpns_element wpns_active ";
						$output .=($i == 0 ? 'wpns_first' : '').($i == ($nbpages-1) ? 'wpns_last' : '')."'";
						$output .= "style ='font :".$settings['wpns_current_font']." ;'>";
						$output .="<span class='page-numbers'>".($i+1);
						if ($settings['wpns_show_total'] == '1'){$output .="<small>/".$nbpages."</small>";}
						$output .= "</span></li>";
					}else{
						$output .= "<li class='wpns_element wpns_inactive ";
						$output .=($i == 0 ? 'wpns_first' : '').($i == ($nbpages-1) ? 'wpns_last' : '')."' ";
						$output .= "style ='font :".$settings['wpns_page_font'].";'>";
						$output .= $page_links[$i]."</li>";
					}
				}
			$output .= "</ul>";
		$output .= "</div>";
		$output .= "<div class='wpns_slider'></div>";
	$output .= "</div>";
	return $output;
}
?>
