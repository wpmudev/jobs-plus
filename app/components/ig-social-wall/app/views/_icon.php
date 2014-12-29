<?php
$url = '#';
if ($data['type'] == 'url') {
    $url = $data['value'];
} elseif ($data['type'] == 'email') {
    $url = 'mailto:' . $data['value'];
} else {
    $url = '#' . $data['value'];
}

?>
<a target="_blank" href="<?php echo $url ?>" data-id="<?php echo $data['name'] ?>"
   data-value="<?php echo esc_attr($data['value']) ?>"
   data-type="<?php echo $social['type'] ?>" class="jbp-social je-tooltip"
   title="<?php echo $social['name'] . ' | ' . $data['value'] ?>">
    <img src="<?php echo $social['url'] ?>">
</a>