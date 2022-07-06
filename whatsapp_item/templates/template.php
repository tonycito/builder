<div class="chat_item">
  
<?php $whatsapp = $props['option'];?>
<?php if ($whatsapp == "whatsapp") :?>
  <a class="whatsapp_avatar miembro online" target="_blank" href="https://api.whatsapp.com/send?phone=<?= $props['telef'] ?>&text=<?= $props['mensaje'] ?>" >
<div class="avatar">
        <div class="avatar_img" style="background-size: cover;">
        <span class="icon_avatar" uk-icon="icon:<?= $props['icon'] ?>" uk-svg></span>
      </div>
        
    </div>
    <div class="miembro_info">
       <div class="nombre"><?= $props['title'] ?></div>
       <div class="estado"><?= $props['meta'] ?></div>
    </div>
</a>
 <?php else: ?> 
  <a class="link_avatar miembro online" target="_blank" href="<?= $props['link'] ?>">
    <div class="avatar">
        <div class="avatar_img" style="background-size: cover;">
        <span class="icon_avatar" uk-icon="icon:<?= $props['icon'] ?>" uk-svg></span>
      </div>
        
    </div>
    <div class="miembro_info">
       <div class="nombre"><?= $props['title'] ?></div>
       <div class="estado"><?= $props['meta'] ?></div>
    </div>
  </a>
<?php endif; ?>
</div>


