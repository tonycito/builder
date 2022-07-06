<?php
$session = JFactory::getSession();

if(
    ( !$props['id'] AND ($_SERVER["REQUEST_METHOD"] == "POST") AND (isset($_POST['submit'])) ) XOR
    (  $props['id'] AND ($_SERVER["REQUEST_METHOD"] == "POST") AND ($_POST['moreJoomlaFormID'] === $props['id']) )
) {

    $moreJoomlaFormID = $_POST['moreJoomlaFormID'];
    $props['recaptcha'] = $session->get($moreJoomlaFormID.'-recaptcha'); $session->clear($moreJoomlaFormID.'-recaptcha');
    $props['sender'] = $session->get($moreJoomlaFormID.'-sender'); $session->clear($moreJoomlaFormID.'-sender');
    $props['cc'] = $session->get($moreJoomlaFormID.'-cc'); $session->clear($moreJoomlaFormID.'-cc');
    $props['copytosender'] = $session->get($moreJoomlaFormID.'-copytosender'); $session->clear($moreJoomlaFormID.'-copytosender');
    $props['recipient'] = $session->get($moreJoomlaFormID.'-recipient'); $session->clear($moreJoomlaFormID.'-recipient');
    $props['bcc'] = $session->get($moreJoomlaFormID.'-bcc'); $session->clear($moreJoomlaFormID.'-bcc');
    $props['subject'] = $session->get($moreJoomlaFormID.'-subject'); $session->clear($moreJoomlaFormID.'-subject');
    $props['header'] = $session->get($moreJoomlaFormID.'-header'); $session->clear($moreJoomlaFormID.'-header');
    $props['footer'] = $session->get($moreJoomlaFormID.'-footer'); $session->clear($moreJoomlaFormID.'-footer');
    $props['recipient_subject'] = $session->get($moreJoomlaFormID.'-recipient_subject'); $session->clear($moreJoomlaFormID.'-recipient_subject');
    $props['recipient_header'] = $session->get($moreJoomlaFormID.'-recipient_header'); $session->clear($moreJoomlaFormID.'-recipient_header');
    $props['recipient_footer'] = $session->get($moreJoomlaFormID.'-recipient_footer'); $session->clear($moreJoomlaFormID.'-recipient_footer');
    $props['confirmation_message'] = $session->get($moreJoomlaFormID.'-confirmation_message'); $session->clear($moreJoomlaFormID.'-confirmation_message');
    $props['redirect_data'] = $session->get($moreJoomlaFormID.'-redirect_data'); $session->clear($moreJoomlaFormID.'-redirect_data');
    $props['redirect'] = $session->get($moreJoomlaFormID.'-redirect'); $session->clear($moreJoomlaFormID.'-redirect');
    $props['hidden_post'] = $session->get($moreJoomlaFormID.'-hidden_post'); $session->clear($moreJoomlaFormID.'-hidden_post');
    $props['notification'] = $session->get($moreJoomlaFormID.'-notification'); $session->clear($moreJoomlaFormID.'-notification');
    $props['notification_position'] = $session->get($moreJoomlaFormID.'-notification_position'); $session->clear($moreJoomlaFormID.'-notification_position');
    $props['notification_style'] = $session->get($moreJoomlaFormID.'-notification_style'); $session->clear($moreJoomlaFormID.'-notification_style');
    $props['notification_timeout'] = $session->get($moreJoomlaFormID.'-notification_timeout'); $session->clear($moreJoomlaFormID.'-notification_timeout');
    $props['attach_data'] = $session->get($moreJoomlaFormID.'-attach_data'); $session->clear($moreJoomlaFormID.'-attach_data');

    $spam = false;

    // reCaptcha
    if($props['recaptcha']){

        $dispatcher = JEventDispatcher::getInstance();
        JPluginHelper::importPlugin('captcha');
        $res = $dispatcher->trigger('onCheckAnswer',$_POST['recaptcha']);
        if(!$res[0]){
            $spam = true;
            $submitMsg = "El reCaptcha falló, por favor intente nuevamente!";
            $submitNotificationStyle = "danger";
        }
    }

    $pattern = 'sender-input-*';
    $sender_tmp = array_filter($_POST, function($entry) use ($pattern) {
        return fnmatch($pattern, $entry);
    });
    $sender = array();
    foreach($sender_tmp as $key => $value) {
        $sender_key = str_replace($value, '', $key);
        $sender[] = $_POST[$sender_key];
        unset($_POST[$key]);
    }
    if(stristr($sender[0],'@')) {
        $sender = $sender[0];
    } else {
        $sender = $props['sender'];
    }

    if($_POST["your-email-from-sender-input"] !== ""){
        $spam = true;
    }

    unset($_POST['g-recaptcha-response']);
    unset($_POST["GDPR"]);
    unset($_POST["Copy"]);
    unset($_POST["your-email-from-sender-input"]);
    unset($_POST["submit"]);
    unset($_POST["moreJoomlaFormID"]);

    $csv = array();
    $body = "<table style='width: 100%'>";
    foreach ($_POST as $key => $value) {
        if($value) {
            if(!is_array($value)) {
                $key = str_replace("_", " ", $key);
                $_POST[$key] = $key;
                $props['recipient_subject'] = str_replace('%'.$key.'%', $value, $props['recipient_subject']);
                $props['recipient_header'] = str_replace('%'.$key.'%', $value, $props['recipient_header']);
                $props['recipient_footer'] = str_replace('%'.$key.'%', $value, $props['recipient_footer']);
                $props['subject'] = str_replace('%'.$key.'%', $value, $props['subject']);
                $props['header'] = str_replace('%'.$key.'%', $value, $props['header']);
                $props['footer'] = str_replace('%'.$key.'%', $value, $props['footer']);
                $body .= "<tr><td>".$key."</td><td>".$value."</td></tr>";
            } else if(is_array($value)) {
                $value = implode(",",$value);
                $body .= "<tr><td>".$key."</td><td>".$value."</td></tr>";
            }
        }
        $csv[$key] = $value;
    }
    $body .= "</table>";

    if(!$spam){

        $formfiles = array();
        foreach ($_FILES as $file) {

            $accept = array("application/msword", "application/vnd.ms-excel", "application/vnd.ms-powerpoint", "text/plain", "application/pdf");
            $accept_wildcard = array("image", "audio", "video");
            if(count($file['name']) > 1){
                foreach ($file['name'] as $key => $value) {
                    if ((in_array($file['type'][$key], $accept)) OR (in_array(substr($file['type'][$key], 0, 5), $accept_wildcard))) {
                        $formfiles[$key]['path'] = $file['tmp_name'][$key];
                        $formfiles[$key]['name'] = $file['name'][$key];
                    } else {
                        echo '<div class="uk-alert-warning" uk-alert>';
                        echo '<a class="uk-alert-close" uk-close></a>';
                        echo 'Tipo de archivo no permitido: ' . $file[$key]['name'];
                        echo '</div>';
                    }
                }
            } else if(count($file['name']) == 1) {
                if ((in_array($file['type'], $accept)) OR (in_array(substr($file['type'], 0, 5), $accept_wildcard))) {
                    $formfiles[$key]['path'] = $file['tmp_name'];
                    $formfiles[$key]['name'] = $file['name'];
                } else {
                    echo '<div class="uk-alert-warning" uk-alert>';
                    echo '<a class="uk-alert-close" uk-close></a>';
                    echo 'Tipo de archivo no permitido: ' . $file[$key]['name'];
                    echo '</div>';
                }
            }
        }

        if($props['copytosender'] && $sender) {

            // recipient mail

            $mailer = JFactory::getMailer();
            $mailer->setSender($props['recipient']);
            $mailer->addRecipient($sender);
            $mailer->setSubject($props['recipient_subject']);
            $mailer->isHtml(true);
            $mailer->Encoding = 'base64';
            $mailer->setBody($props['recipient_header'].$body.$props['recipient_footer']);
            foreach($formfiles as $file) {
                $mailer->addAttachment($file['path'], $file['name']);
            }

            $send = $mailer->Send();

            unset($mailer);

            // end recipient
        }

        // admin mail

        $mailer = JFactory::getMailer();
        $mailer->setSender($sender);
        $mailer->addRecipient($props['recipient']);
        $mailer->addCc($props['cc']);
        $mailer->addBcc($props['bcc']);
        $mailer->setSubject($props['subject']);
        $mailer->isHtml(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody($props['header'].$body.$props['footer']);
        foreach($formfiles as $file) {
            $mailer->addAttachment($file['path'], $file['name']);
        }

        if($props['attach_data']) {
            $file = tmpfile();
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv( $file, array_keys($csv));
            fputcsv( $file, array_values($csv));
            $filepath = stream_get_meta_data($file)['uri'];
            $mailer->addAttachment($filepath, 'data.csv');
        }

        $send = $mailer->Send();

        unset($mailer);

        if ( $send !== true ) {
            echo '<div class="uk-alert-danger" uk-alert>';
            echo '<a class="uk-alert-close" uk-close></a>';
            echo '<p>Error al enviar este mensaje. Por favor contáctenos por otro medio.</p>';
            echo '</div>';

        } else {

            echo '<div class="uk-alert-success" uk-alert>';
            echo '<a class="uk-alert-close" uk-close></a>';
            echo $props['confirmation_message'];
            echo '</div>';

            $db = JFactory::getDbo();
            $query = 'CREATE TABLE IF NOT EXISTS #__mj_form (id INT(6) AUTO_INCREMENT PRIMARY KEY, form VARCHAR(255), date DATETIME NOT NULL, data LONGTEXT)';
            $db->setQuery($query);
            $result = $db->execute();
            $log = htmlspecialchars($log);

            $formdbtitle = ($props['form_title']) ? $props['form_title'] : JFactory::getApplication()->getMenu()->getActive()->title;
            $insert = addslashes(json_encode($csv, JSON_FORCE_OBJECT));
            $query = $db->getQuery(true);
            $columns = array('form', 'date', 'data');
            $values = array($db->quote($formdbtitle), $db->quote(date("Y-m-d H:i:s")), $db->quote($insert));
            $query
                ->insert($db->quoteName('#__mj_form'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));
//            $query = 'INSERT INTO #__mj_form (id, form, date, data) VALUES ("", "'.$formdbtitle.'", "' . date("Y-m-d H:i:s") . '", "' . $insert . '");';
            $db->setQuery($query);
            $result = $db->execute();

            if($props['hidden_post']) {
                $ch = curl_init($props['hidden_post']);
                curl_setopt($ch, CURLOPT_POST,true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
            }

            if($props['redirect']) {
                $app = JFactory::getApplication();
                if($props['redirect_data'] === "GET") {
                    if(stristr($props['redirect'],"?")) { $delimiter = "&"; } else { $delimiter = "?"; }
                    $props['redirect'] = $props['redirect'].$delimiter.http_build_query($_POST);
                } else if($props['redirect_data'] === "POST") {
                    $ch = curl_init($props['redirect']);
                    curl_setopt($ch, CURLOPT_POST,true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);
                }
                $app->redirect(JRoute::_($props['redirect']));
            }

            if($props['attach_data']) {
                fclose( $file );
            }

        }

    }

    if($props['notification']){
        echo "<script>UIkit.notification('".$props['confirmation_message']."', { status: '".$props['notification_style']."', pos: '".$props['notification_position']."', timeout: ".$props['notification_timeout']." });</script>";
    }

} else {

    $moreJoomlaFormID = ($props['id']) ? $props['id'] : uniqid();
    $session->clear($moreJoomlaFormID.'-recaptcha'); $session->set($moreJoomlaFormID.'-recaptcha', $props['recaptcha']);
    $session->clear($moreJoomlaFormID.'-sender'); $session->set($moreJoomlaFormID.'-sender', $props['sender']);
    $session->clear($moreJoomlaFormID.'-cc'); $session->set($moreJoomlaFormID.'-cc', $props['cc']);
    $session->clear($moreJoomlaFormID.'-copytosender'); $session->set($moreJoomlaFormID.'-copytosender', $props['copytosender']);
    $session->clear($moreJoomlaFormID.'-recipient'); $session->set($moreJoomlaFormID.'-recipient', $props['recipient']);
    $session->clear($moreJoomlaFormID.'-bcc'); $session->set($moreJoomlaFormID.'-bcc', $props['bcc']);
    $session->clear($moreJoomlaFormID.'-subject'); $session->set($moreJoomlaFormID.'-subject', $props['subject']);
    $session->clear($moreJoomlaFormID.'-header'); $session->set($moreJoomlaFormID.'-header', $props['header']);
    $session->clear($moreJoomlaFormID.'-footer'); $session->set($moreJoomlaFormID.'-footer', $props['footer']);
    $session->clear($moreJoomlaFormID.'-recipient_subject'); $session->set($moreJoomlaFormID.'-recipient_subject', $props['recipient_subject']);
    $session->clear($moreJoomlaFormID.'-recipient_header'); $session->set($moreJoomlaFormID.'-recipient_header', $props['recipient_header']);
    $session->clear($moreJoomlaFormID.'-recipient_footer'); $session->set($moreJoomlaFormID.'-recipient_footer', $props['recipient_footer']);
    $session->clear($moreJoomlaFormID.'-confirmation_message'); $session->set($moreJoomlaFormID.'-confirmation_message', $props['confirmation_message']);
    $session->clear($moreJoomlaFormID.'-redirect_data'); $session->set($moreJoomlaFormID.'-redirect_data', $props['redirect_data']);
    $session->clear($moreJoomlaFormID.'-redirect'); $session->set($moreJoomlaFormID.'-redirect', $props['redirect']);
    $session->clear($moreJoomlaFormID.'-hidden_post'); $session->set($moreJoomlaFormID.'-hidden_post', $props['hidden_post']);
    $session->clear($moreJoomlaFormID.'-notification'); $session->set($moreJoomlaFormID.'-notification', $props['notification']);
    $session->clear($moreJoomlaFormID.'-notification_position'); $session->set($moreJoomlaFormID.'-notification_position', $props['notification_position']);
    $session->clear($moreJoomlaFormID.'-notification_style'); $session->set($moreJoomlaFormID.'-notification_style', $props['notification_style']);
    $session->clear($moreJoomlaFormID.'-notification_timeout'); $session->set($moreJoomlaFormID.'-notification_timeout', $props['notification_timeout']);
    $session->clear($moreJoomlaFormID.'-attach_data'); $session->set($moreJoomlaFormID.'-attach_data', $props['attach_data']);

    if($props['recaptcha']) {

        $joomla_captcha = JFactory::getConfig()->get('captcha');
        if ( $joomla_captcha != '0') {
            JPluginHelper::importPlugin('captcha');
            $dispatcher = JDispatcher::getInstance();
            $dispatcher->trigger('onInit', 'recaptcha');
            $captcha_class = "uk-margin uk-flex uk-flex-".$props['recaptcha_position'];
            $recaptcha = $dispatcher->trigger('onDisplay', array(null, 'recaptcha', $captcha_class));
        }

    }

    if($props['form_card'] != "none"){
        $card = "uk-card uk-card-body uk-card-" . $props['form_card'];
    } else {
        $card = "";
    }

    // change action url if joomla gzip is on
    if(JFactory::getConfig()->get('gzip') == 1) {
        $jinput = JFactory::getApplication()->input;
        $uriQuery = $jinput->getArray();
        unset($uriQuery['Itemid']);
        $action = 'index.php?' . JUri::buildQuery($uriQuery);
    } else {
        $action = "";
    }

    if($props['modal']) {

        if($props['modal_id']) {
            $modal_id = $props['modal_id'];
        } else {
            $modal_id = "contact-form-{$this->uid()}";
        }

        if($props['modal'] == "button") {

            if($props['modal_button_position'] !== "left") {
                echo "<div class=\"uk-text-".$props['modal_button_position']."\">";
            }
            echo "<button uk-toggle=\"target: #" . $modal_id . "\" type=\"button\" class=\"uk-button uk-button-" . $props['modal_button_style'] . " uk-text-center " . $props['modal_button_width'] . "\">";

            if ($props['modal_button_icon']) {
                if ($props['modal_button_icon_align'] !== 'right') {
                    echo " <span uk-icon=\"".$props['modal_button_icon']."\"></span> ";
                }
                echo "<span class=\"uk-text-middle\">".$props['modal_button_text']."</span>";
                if ($props['modal_button_icon_align'] == 'right') {
                    echo " <span uk-icon=\"".$props['modal_button_icon']."\"></span>";
                }
            } else {
                echo $props['modal_button_text'];
            }
            echo "</button>";

            if($props['modal_button_position'] !== "left") {
                echo "</div>";
            }

        }

        if($props['modal_width']) { $modal_style = "style=\"width: ".$props['modal_width']."\""; }

        echo "<div id=\"".$modal_id."\" class=\"uk-modal-container\" uk-modal>";
        echo "<div class=\"uk-modal-dialog\" ".$modal_style.">";
        echo "<button class=\"uk-modal-close-default\" type=\"button\" uk-close></button>";
        if($props['form_title']) {
            echo "<div class=\"uk-modal-header\">";
            echo "<h2 class=\"uk-modal-title\">".$props['form_title']."</h2>";
            echo "</div>";
        }
        echo "<div class=\"uk-modal-body uk-padding-remove\">";
    }

    $el = $this->el('form', [
        'class' => [
            'uk-form-' . $props['form_layout'],
            $card
        ],
        'method' => 'post',
        'name' => $moreJoomlaFormID."-google",
        'enctype' => 'multipart/form-data',
        'action' => $action
    ]);

    ?>

    <?= $el($props, $attrs) ?>

    <?php if($props['form_title'] AND !$props['modal'])  {
        echo "<div class=\"uk-card-title\">".$props['form_title']."</div>";
    } ?>

    <?php foreach ($children as $child) : ?>
        <?= $builder->render($child, ['element' => $props]) ?>
    <?php endforeach ?>

    <?php if($props["copytosender"]) {
        $copytosenderid = uniqid('copytosender-');
        echo '<div class="uk-margin">';
        echo '<label class="uk-form-label" for="'.$copytosenderid.'">'.$props["copytosender_name"].'</label>';
        echo '<div class="uk-form-controls">';
        echo '<div class="uk-grid-small uk-child-width-auto uk-grid">';
        echo '<label class="uk-form-label" style="width: auto"><input class="uk-checkbox" id="'.$copytosenderid.'" type="checkbox" name="Copy" value="'.$props["copytosender_text"].'"> '.$props["copytosender_text"].' </label>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    } ?>

    <?php if( ($props['recaptcha']) AND  (isset($recaptcha[0]) and $joomla_captcha != "0") ) {
        echo $recaptcha[0];
    } ?>

    <div class="uk-invisible uk-position-absolute">
        <input class="uk-input" type="email" placeholder="email address" name="your-email-from-sender-input">
    </div>

    <input type="hidden" name="moreJoomlaFormID" value="<?php echo $moreJoomlaFormID; ?>">

    <?php if(!$props['modal']) { ?>
        <div class="uk-width-1-1 uk-text-<?= $props['submit_position'] ?>">
            <input class="uk-button uk-button-<?= $props['submit_button'] ?> uk-<?= $props['submit_width'] ?>" type="submit" name="submit" value="<?= $props['submit_text'] ?>">
        </div>
    <?php } ?>



    <?php
    if($props['modal']) {
        echo "<div class=\"uk-modal-footer uk-text-".$props['submit_position']."\">";
        echo "<button class=\"uk-button uk-button-".$props['submit_button']." uk-".$props['submit_width']."\" type=\"submit\" name=\"submit\">".$props['submit_text']."</button>";
        echo "</div>";
    }
    ?>

    </form>

    <?php
    if($props['modal']) {
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    ?>

    <?php if($props['recaptcha']): ?>
        <script type="text/javascript">
            window.onload = function() {
                var reCaptcha = document.getElementById("g-recaptcha-response");
                reCaptcha.setAttribute("required", "required");
            }
        </script>
    <?php endif ?>
   
    <?php if($props['drive']): ?>
        <script>
            const scriptURL<?php echo $moreJoomlaFormID; ?> = "<?php echo $props['drive_link'] ?>"
            const form<?php echo $moreJoomlaFormID; ?>  = document.forms["<?php echo $moreJoomlaFormID.'-google' ?>"]
            
            form<?php echo $moreJoomlaFormID; ?>.addEventListener('submit', e => {
                fetch(scriptURL-<?php echo $moreJoomlaFormID; ?>, { method: 'POST', body: new FormData(form)})
                .then(response => console.log('Success!', response))
                .catch(error => console.error('Error!', error.message))
            })
        </script>
    <?php endif ?>

<?php } ?>

<?php

$cache = JFactory::getCache();
$cache->setCaching(true);
$cache->clean();
$cache->setLifeTime(1);

?>