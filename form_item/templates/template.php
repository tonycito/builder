<?php

    $id = uniqid($props['input_type'].'-');
    $required = ($props['input_required'] == true ? 'required' : '');
	$required_text = ($props['input_required'] == true ? '*' : '');
    $multiple = ($props['input_multiple'] == true ? 'multiple' : '');
    $newline = ($props['input_newline'] == true ? 'required' : '');

    if($props['input_text_type'] == "file") {
        $accept = array("application/msword", "application/vnd.ms-excel", "application/vnd.ms-powerpoint", "text/plain", "application/pdf", "image/*", "audio/*", "video/*");
        $accept = "accept=\"" . implode(",", $accept) . "\"";
    }

	switch ($props['input_type']) {

		case 'input':
                if($props['input_text_type'] == "file" AND $props['input_multiple'] == true) {
                    $props["title"] = $props["title"]."[]";
                }
                $el = $this->el($props['input_type'], [
                    'class' => [
                        'uk-' . $props['input_type'],
                        'uk-form-width-' . $props['input_width']
                    ],
                    'id' => $id,
                    'type' => $props['input_text_type'],
                    'name' => $props["title"],
                    'placeholder' => $props['input_placeholder'],
//                    'value' => $_GET[$props["title"]],
                    $accept,
                    $required,
                    $multiple
                ]);

                if($props['input_text_type'] == "email") {
                    $sender = $this->el($props['input_type'], [
                        'type' => 'hidden',
                        'name' => 'sender-'.$id.$props["title"],
                        'value' => 'sender-'.$id
                    ]);
                }
			break;
		
		case 'select':

			$el = $this->el($props['input_type'], [
			    'class' => [
			        'uk-' . $props['input_type'],
			        'uk-form-width-' . $props['input_width']
			    ],
			    'id' => $id,
//                'value' => $_GET[$props["title"]],
			    'name' => $props["title"],
                $required
			]);
			break;

		case 'textarea':

			$el = $this->el($props['input_type'], [
			    'class' => [
			        'uk-' . $props['input_type'],
			        'uk-form-width-' . $props['input_width']
			    ],
			    'id' => $id,
			    'rows' => $props['input_rows'],
			    'placeholder' => $props['input_placeholder'],
//                'value' => $_GET[$props["title"]],
			    'name' => $props["title"],
                $required
			]);
			break;

        case 'radio':
        case 'checkbox':
        case 'section':
        case 'GDPR':
            break;

		default:
			$id = uniqid($props['input_type'] . '-');
			$el = $this->el($props['input_type'], [
			    'class' => [
			        'uk-' . $props['input_type'],
			        'uk-form-width-' . $props['input_width']
			    ],
			    'id' => $id,
			    'type' => 'text',
//                'value' => $_GET[$props["title"]],
			    'name' => $props["title"]
			]);
			break;
	}

?>

<?php if ($props['input_type'] !== "section") { ?>
<div class="uk-margin">
    <label class="uk-form-label" for="<?= $id ?>"><?= $props['input_label'].$required_text ?></label>
    <div class="uk-form-controls">
<?php } ?>

        <?php

            switch ($props['input_type']) {

                    case 'input':
                        if($props['input_icon']){
                            echo '<div class="uk-position-relative">';
                            echo '<span class="uk-form-icon" uk-icon="icon:' . $props['input_icon'] . '"></span>';
                            echo $el($props, $attrs);
                            echo '</div>';
                        }else{
                            echo $el($props, $attrs);
                        }
                        break;

                    case 'select':
                        echo $el($props, $attrs);
                        $values = explode(';', $props['input_values']);
                        if ($props['input_optgroup']) {
                            echo '<option selected hidden disabled>'.$props['input_optgroup'].'</option>';
                        }
                        for ($i=0; $i < sizeof($values); $i++) {
                            echo '<option>' . $values[$i] . '</option>';
                        }
                        echo '</select>';
                        break;

                    case 'textarea':
                        echo $el($props, $attrs) . '</textarea>';
                        break;

                    case 'radio':
                        echo '<div class="uk-grid-small uk-child-width-auto uk-grid">';
                        $values = explode(';', $props['input_values']);
                        for ($i=0; $i < sizeof($values); $i++) {
                            $el = $this->el('input', [
                                'class' => [
                                    'uk-' . $props['input_type']
                                ],
                                'id' => $id,
                                'type' => 'radio',
                                'name' => $props["title"],
                                'value' => $values[$i],
                                $required
                            ]);
                            if($newline) { echo '<div class="uk-width-1-1">'; }
                            echo '<label class="uk-form-label" style="width: auto">' . $el($props, $attrs) . ' ' . $values[$i] . '</label>';
                            if($newline) { echo '</div>'; }
                        }
                        echo '</div>';
                        break;

                    case 'checkbox':
                    case 'GDPR':
                        echo '<div class="uk-grid-small uk-child-width-auto uk-grid">';
                        if($props['input_type'] == "GDPR") {
                            $values = array($props['input_gdpr']);
                            $props["title"] = "GDPR";
                        } else {
                            $values = explode(';', $props['input_values']);
                        }
                        for ($i=0; $i < sizeof($values); $i++) {
                            $el = $this->el('input', [
                                'class' => [
                                    'uk-checkbox'
                                ],
                                'id' => $id,
                                'type' => 'checkbox',
                                'name' => $props["title"] . '[]',
                                'value' => $values[$i],
                                $required
                            ]);
                            if($newline) { echo '<div class="uk-width-1-1">'; }
                            echo '<label class="uk-form-label" style="width: auto">' . $el($props, $attrs) . ' ' . $values[$i] . '</label>';
                            if($newline) { echo '</div>'; }
                        }
                        echo '</div>';
                        break;

                    case 'section':
                        break;

                    default:
                        echo $el($props, $attrs);
                        break;
                }

                if($sender) { echo $sender($props, $attrs); }
        ?>
<?php if ($props['input_type'] !== "section") { ?>
        <?php echo ($props['input_description']) ? '<small>'.$props['input_description'].'</small>' : ''; ?>
    </div>
</div>
<?php } else {

    echo $props['input_section'];
}
?>


<script>
    <?php
        foreach($_GET as $key=>$val) {
            if(stristr($val, "@") === false) {
                echo "selectElement('" . $key . "', '" . $val . "'); ";
            }
        } ?>

    function selectElement(key, value) {
        var key1 = document.getElementsByName(key);
        var lk = key1.length;
        for (var i=0; i<lk; i++) {
            key1[i].value = value;
            if(key1[i].value == value) {
                key1[i].checked = true;
            }
        }
        var key2 = document.getElementsByName(key + '[]');
        var lk = key2.length;
        for (var i=0; i<lk; i++) {
            console.log(key2[i]);
            if(key2[i].value == value) {
                key2[i].selected = true;
                key2[i].checked = true;
            }
        }
    }
</script>
