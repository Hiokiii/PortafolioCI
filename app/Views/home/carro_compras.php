<?php if (session('error')): ?>
    <div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div>
<?php endif; ?>
<?php if (session('success')): ?>
    <div class="alert alert-success" role="alert"><?= esc(session('success')) ?></div>
<?php endif; ?>
<h2>Carro de compras</h2>
<a href="<?= site_url('TiendaOficial') ?>">Volver a la tienda</a>
<?php if (empty($productos)): ?>
    <p>No hay productos en el carro.</p>
<?php else: ?>
    <ul>
        <?php foreach ($productos as $producto): ?>
            <li>
                <img width="80" src="data:image/jpeg;base64,<?= esc(base64_encode($producto['foto']), 'attr') ?>" alt="<?= esc($producto['nombre'], 'attr') ?>">
                <strong><?= esc($producto['nombre']) ?></strong>
                <span>Talla: <?= esc($producto['talla']) ?></span>
                <span>Precio: $<?= number_format($producto['precio'], 0, ',', '.') ?></span>
                <span>Cantidad: <?= (int) $producto['cantidad'] ?></span>
                <form action="<?= site_url('VentaSouvenirsController/eliminarProducto') ?>" method="post" style="display:inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="item" value="<?= esc($producto['id'] . '-' . $producto['talla'], 'attr') ?>">
                    <button type="submit">Quitar una unidad</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <p>Total: $<?= number_format($total, 0, ',', '.') ?></p>
    <form action="<?= site_url('VentaSouvenirsController/vaciarCarro') ?>" method="post" style="display:inline">
        <?= csrf_field() ?>
        <button type="submit">Vaciar carro</button>
    </form>
    <a class="btn-comprar" href="<?= site_url('Checkout') ?>">Continuar a comprar</a>
<?php endif; ?>