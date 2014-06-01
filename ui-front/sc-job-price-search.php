<?php
/**
* @package Jobs Board
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

$the_max = $this->get_max_budget();
$job_min_price = intval(empty($_GET['job_min_price']) ? 0 : $_GET['job_min_price'] );
$job_max_price = intval( empty($_GET['job_max_price']) ? $the_max : $_GET['job_max_price'] );

wp_enqueue_style('jobs-plus-custom');
wp_enqueue_script('jquery-ui-slider');
wp_enqueue_script('jquery-format-currency-i18n');

?>

<section class="job-price-search group">
	<form class="search-form" id="price-search" method="GET" action="<?php echo get_post_type_archive_link('jbp_job'); ?>" >
		<div class="job-inside">
			<div class="sliders">
				<div class="job-range"><span id="job-min"></span> - <span id="job-max"></span></div>
				
				<div class="price-range slider"></div>
			</div>
			<input type="hidden" class="job_min_price" name="job_min_price" value="<?php echo $job_min_price; ?>" />
			<input type="hidden" class="job_max_price" name="job_max_price" value="<?php echo $job_max_price; ?>" />
			<input type="hidden" name="prj-sort" value="<?php echo (isset($_GET['prj-sort']) ? $_GET['prj-sort'] : 'latest'); ?>" />
			<input type="submit" name="filter" id="filter-by-price" value="Filter" />
			<br />
		</div>
	</form>
</section>

<script type="text/javascript">
	jQuery(document).ready( function($){

		var minPrice = .001; //Avoid zeros
		var maxPrice = <?php echo intval($the_max); ?>; //Avoid zeros
		var minval = <?php echo ($job_min_price + 0.001); ?>; //
		var maxval = <?php echo $job_max_price; ?>;

		$('.price-range').slider({
			range: true,
			min: minPrice,
			max: maxPrice,
			// Exponential scaling
			//values: [Number(logp(minval, minPrice, maxPrice)).toFixed(0), Number(logp(maxval, minPrice, maxPrice)).toFixed(0)],

			// Linear scaling
			values: [Number(minval).toFixed(0), Number(maxval).toFixed(0)],
			
			slide: display_range,
			change: display_range
		});

		display_range();
		$('.search-update').hide();

		function display_range(){
			$('.search-update').show();
			// Exponential scaling
			//minp = Number(expon( $('.price-range').slider('values', 0), minPrice, maxPrice)).toFixed(0);
			//maxp = Number(expon( $('.price-range').slider('values', 1), minPrice, maxPrice)).toFixed(0);

			// Linear scaling
			minp = Number( $('.price-range').slider('values', 0) ).toFixed(0);
			maxp = Number( $('.price-range').slider('values', 1) ).toFixed(0);

			$('#job-min').text(minp).formatCurrency({region: "<?php echo $this->js_locale; ?>"});
			$('#job-max').text(maxp).formatCurrency({region: "<?php echo $this->js_locale; ?>"});

			$('.job_min_price').val(minp);
			$('.job_max_price').val(maxp);

			return [minp, maxp];
		}

		function expon(val, min,max) {
			var minv = Math.log(min);
			var maxv = Math.log(max);
			// calculate adjustment factor
			var scale = (maxv-minv) / (max-min);
			return Math.exp(minv + scale*(val-min));
		}

		function logp(val, min,max) {
			var minv = Math.log(min);
			var maxv = Math.log(max);
			// calculate adjustment factor
			var scale = (maxv-minv) / (max-min);
			return (Math.log(val)-minv) / scale + min;
		}

	});

</script>
