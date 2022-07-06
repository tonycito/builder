<?php

$el = $this->el('div', [

    'class' => [
        'example-element'
    ]

]);
?>

<?= $el($props, $attrs) ?>

<div class="whatsapp " id="wa_btn">
<div id="first_container" class="container_point">
				<div id="btn1" class="boton_1"></div>
				<div class="boton_2"></div>
				<div class="boton_3"></div>
			</div>
  <div id="whatsapp_text" class="whatsapp_txt"><?= $props['title'] ?></div>
  <div class="whatsapp_icon">
    <span class="icon" uk-icon="icon:whatsapp" uk-svg></span>
    <!-- Agregar los nuevos iconos -->
    <span id="close" uk-icon="icon:close" uk-svg></span>
  </div>
</div>

  <div class="chat_box" id="wa_chat">
    <div class="chat_header">
      <div class="chat_titulo"><?= $props['texto1'] ?></div>
      <div class="chat_msj"><?= $props['texto2'] ?></div>
  </div>

  <div class="chat_body">
    <div class="sub_msj"><?= $props['texto3'] ?></div>

  <div class="chat_list">
        <?php foreach ($children as $child) : ?>
          <?= $builder->render($child, ['element' => $props]) ?>
        <?php endforeach ?>  
    </div>
  </div>
</div>


<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', ()=>{
    let btn = document.getElementById('wa_btn');
    let chat= document.getElementById('wa_chat');
    let puntos = document.getElementById('first_container');
   

    btn.addEventListener('click', ()=>{
      chat.classList.toggle('fade_left')
      chat.classList.toggle('activo')
      chat.classList.toggle('fade_in')
      puntos.classList.toggle('activo')
      btn.classList.toggle('activo')
      puntos.classList.toggle('activo')
     
    })
  })
//esperamos que cargue el DOM, para mostrar los puntos y agregar los efectos.
  setTimeout(function () {
	document.getElementById('first_container').classList.add('mostrar');
  document.getElementById('first_container').classList.add('efectos');

}, 5000);
  //ocultamos los puntos y mostramos el mensaje flotante.
setTimeout(function () {
  
	//Eliminamos el elemento que creamos  para mostrar el mensaje flotante y no afecte con el estilo al hacer click.
  function eliminarElemento(){
    // debugger;
	const imagen = document.getElementById("first_container");	
	// console.log(imagen);
		padre = imagen.parentNode;
		padre.removeChild(imagen);
	
}
eliminarElemento();
	document.getElementById('whatsapp_text').classList.add('whatsappmostrar');
}, 7500);

{
  //crear elemento para mostrar el mensaje flotante.
  setTimeout(function () {
    const agregar = document.querySelector('.whatsapp_icon');
    agregar.innerHTML = `
  <ul>
    <li><span class="icon slider" uk-icon="icon:whatsapp" uk-svg></span></li>
    <li><span class="icon slider" uk-icon="icon:facebook" uk-svg></span></li>
    <li><span class="icon slider" uk-icon="icon:instagram" uk-svg></span></li>
    <li><span class="icon slider" uk-icon="icon:twitter" uk-svg></span></li>
  </ul>
  <span id="close" uk-icon="icon:close" uk-svg></span>`;
}, 10000);
  // console.log(agregar);
}

</script>
