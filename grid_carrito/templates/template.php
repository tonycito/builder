<?php

// Resets
if ($props['panel_style'] || !$props['show_image']) { $props['image_box_shadow_bottom'] = ''; }
if ($props['panel_link']) {
    $props['title_link'] = '';
    $props['image_link'] = '';
}

$el = $this->el('div', [

    'uk-filter' => [
        '.js-filter' => $tags,
    ],

]);

// Grid
$grid = $this->el('div', [

    'class' => [
        'js-filter' => $tags,
        'uk-child-width-[1-{@!grid_default: auto}]{grid_default}',
        'uk-child-width-[1-{@!grid_small: auto}]{grid_small}@s',
        'uk-child-width-[1-{@!grid_medium: auto}]{grid_medium}@m',
        'uk-child-width-[1-{@!grid_large: auto}]{grid_large}@l',
        'uk-child-width-[1-{@!grid_xlarge: auto}]{grid_xlarge}@xl',
        'uk-flex-center {@grid_column_align}',
        'uk-flex-middle {@grid_row_align}',
        $props['grid_column_gap'] == $props['grid_row_gap'] ? 'uk-grid-{grid_column_gap}' : '[uk-grid-column-{grid_column_gap}] [uk-grid-row-{grid_row_gap}]',
        'uk-grid-divider {@grid_divider} {@!grid_column_gap:collapse} {@!grid_row_gap:collapse}',
        'uk-grid-match {@!grid_masonry}',
    ],

    'uk-grid' => $this->expr([
        'masonry: {grid_masonry};',
        'parallax: {grid_parallax};',
    ], $props) ?: true,

    'uk-lightbox' => [
        'toggle: a[data-type];' => $props['lightbox'],
    ],

]);

// Filter
$filter_grid = $this->el('div', [

    'class' => [
        'uk-child-width-expand',
        $props['filter_grid_column_gap'] == $props['filter_grid_row_gap'] ? 'uk-grid-{filter_grid_column_gap}' : '[uk-grid-column-{filter_grid_column_gap}] [uk-grid-row-{filter_grid_row_gap}]',
    ],

    'uk-grid' => true,
]);

$filter_cell = $this->el('div', [

    'class' => [
        'uk-width-{filter_grid_width}@{filter_grid_breakpoint}',
        'uk-flex-last@{filter_grid_breakpoint} {@filter_position: right}',
    ],

]);

?>

<?php if ($tags) : ?>
<?= $el($props, $attrs) ?>

    <?php if ($filter_horizontal = in_array($props['filter_position'], ['left', 'right'])) : ?>
    <?= $filter_grid($props) ?>
        <?= $filter_cell($props) ?>
    <?php endif ?>

        <?= $this->render("{$__dir}/template-nav", compact('props')) ?>

    <?php if ($filter_horizontal) : ?>
        </div>
        <div>
    <?php endif ?>

        <?= $grid($props) ?>
        <?php foreach ($children as $child) : ?>
        <?= $this->el('div', ['data-tag' => $child->tags], $builder->render($child, ['element' => $props])) ?>
        <?php endforeach ?>
        </div>

    <?php if ($filter_horizontal) : ?>
        </div>
    </div>
    <?php endif ?>

</div>
<?php else : ?>
<?= $el($props, $attrs) ?>

    <?= $grid($props) ?>
    <?php foreach ($children as $child) : ?>
    <div><?= $builder->render($child, ['element' => $props]) ?></div>
    <?php endforeach ?>
    </div>

  


<div class="icon-pedidos">
    <button uk-toggle="target: #boton-carrito" id="modal-carrito" class="uk-icon-button" uk-icon="cart"></button>
    <span id="contador">0</span>
</div>
<div id="boton-carrito" uk-modal>
    <div class="uk-modal-dialog">
        <div class="uk-modal-body">
            <h3 class="uk-modal-title">Mi pedido</h3>
            <div id="carrito">
                <table id="lista-carrito" class="uk-table uk-table-small uk-table-striped">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Precio</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>          
            </div>
            <h3 class="uk-align-right" >Total: S/<span id="gran_total">0.00</span></h3>
        </div>
        <div class="uk-modal-footer">
            <a id="vaciar-carrito" class="uk-button uk-button-danger uk-modal-close">Vaciar Carrito</a>
            <a id="realizar-pedido" href="https://wa.me/51<?= $props['whatsapp'] ?>/?text=Hola, quiero hacer un pedido:" target="_blank" class="uk-button uk-button-primary uk-align-right" type="button">Realizar pedido</a>
        </div>
    </div>
</div>
   
<script>
    const carrito = document.getElementById('carrito');
    const cursos = document.getElementById('lista-productos');
    const listaCursos = document.querySelector('#lista-carrito tbody');
    const vaciarCarritoBtn = document.getElementById('vaciar-carrito');
    const realizar_pedido = document.getElementById('realizar-pedido');
    const contador = document.querySelector('#contador');
    const modal = document.querySelector('#modal-carrito');
    const gran_total = document.querySelector('#gran_total');
    cargarEventListeners();
    carrito_vacio();
    
    function carrito_vacio(){
    	modal.classList.add('uk-disabled');
        modal.style.filter = 'grayscale()';
    };
    function cargarEventListeners(){
        cursos.addEventListener('click', comprarCurso);
        carrito.addEventListener('click', eliminarCurso);
        vaciarCarritoBtn.addEventListener('click', vaciarCursos);
    }

    function comprarCurso(e){
        e.preventDefault();

        if(e.target.classList.contains('agregar-carrito')){
            const curso = e.target.parentElement.parentElement;     
            leerDatosCurso(curso);
        };
    }

    function leerDatosCurso(curso){
        const infoCurso = {
            titulo: curso.querySelector('.el-title').textContent,
            precio: curso.querySelector('.el-meta').textContent,
            id: curso.querySelector('a').getAttribute('data-id')
        }
        insertarCarrito(infoCurso);
    }

    function insertarCarrito(curso){
        const row = document.createElement('tr');
        const verificar = document.getElementById(`cant-${curso.id}`); 
        let total = 0;
        
        if(verificar){
            verificar.value++;         
            total = parseFloat(curso.precio) * parseFloat(verificar.value);
            let acortar = verificar.parentElement.parentElement.children;
            acortar[1].querySelector('span').textContent = verificar.value;
            acortar[2].innerHTML = `S/<span id='p_curso'>${parseFloat(total).toFixed(2)}</span>`;
        }else{
            row.innerHTML = `
<td style='display:none;'><input id='cant-${curso.id}' value="1"></td><td>${curso.titulo}<br><span>1</span> x S/${curso.precio}</td>
<td>S/<span id='p_curso'>${curso.precio}</span></td>
<td><a href="#" class="borrar-curso" data-id="${curso.id}">X</a></td>`;       
            listaCursos.appendChild(row);    
            modal.classList.remove('uk-disabled');
     		modal.style.filter = 'grayscale(0)';
        }
         let sumando = parseFloat(gran_total.textContent) + parseFloat(curso.precio);
         gran_total.textContent = sumando.toFixed(2);
        
        UIkit.notification({
            message: 'Producto añadido al carrito',
            status: 'primary',
            pos: 'top-right',
            timeout: 2000
        });

        contador.innerHTML = listaCursos.rows.length;

       pedir();
    }

    function eliminarCurso(e){
        e.preventDefault();

        let curso, cursoID;
        if(e.target.classList.contains('borrar-curso')){
            e.target.parentElement.parentElement.remove();
            curso = e.target.parentElement.parentElement;
            cursoID = curso.querySelector('a').getAttribute('data-id');
        }
        console.log(curso.querySelector('#p_curso').textContent);
        contador.innerHTML = listaCursos.rows.length;
              
        let restando = parseFloat(gran_total.textContent) - parseFloat(curso.querySelector('#p_curso').textContent);
         gran_total.textContent = restando.toFixed(2);
        
        pedir();
    }

    function vaciarCursos(){
        while(listaCursos.firstChild){
            listaCursos.removeChild(listaCursos.firstChild);
        }
        contador.innerHTML = listaCursos.rows.length;
        gran_total.textContent = 0.00;

        UIkit.notification({
            message: 'Su carrito esta vacio',
            status: 'danger',
            pos: 'top-right',
            timeout: 2000
        });

        carrito_vacio();
    }
    
    function pedir(){
        let link = '';
        for(i=0; i<listaCursos.rows.length; i++){
          link += `%0A${listaCursos.children[i].children[1].textContent}`;
        }
        realizar_pedido.href = "https://wa.me/51" + <?= $props['whatsapp'] ?> + "/?text=Hola, quiero hacer un pedido:"+link+'%0A%0A*Monto total: S/'+gran_total.textContent+'*';
    }
</script>

<style>
.icon-pedidos{
    position: fixed !important;
    bottom: 70px;
    right: 70px;
    transform: scale(1.5);
    z-index:99;
    filter: drop-shadow(0 0 5px #888);
}
.icon-pedidos span{
    position:absolute;
    top: -10px;
    right: 0;
    background: green;
    color: #fff;
    padding:3px;
    display:inline-block;
    width:12px;
    text-align: center;
    border-radius:50%;
}
.el-meta:before{
    content: 'S/';
}
</style>

</div>
<?php endif ?>
