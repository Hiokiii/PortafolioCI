<?php if (session('error')): ?><div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div><?php endif; ?>
<?php if (session('success')): ?><div class="alert alert-success" role="alert"><?= esc(session('success')) ?></div><?php endif; ?>
<main class="detalle-producto">
    <img class="detalle-image" src="data:image/jpeg;base64,<?= esc(base64_encode($producto['foto']), 'attr') ?>" alt="<?= esc($producto['producto'], 'attr') ?>">
    <section class="detalle-info">
        <a href="<?= site_url('TiendaOficial') ?>">Volver a productos</a>
        <h2><?= esc($producto['producto']) ?></h2>
        <p>Precio: $<?= number_format((float) $producto['precio'], 0, ',', '.') ?></p>
        <p>Género: <?= esc($producto['genero']) ?></p>
        <p><?= esc($producto['detalle']) ?></p>
        <form action="<?= site_url('VentaSouvenirsController/agregarProducto') ?>" method="post">
            <?= csrf_field() ?>
            <?php if (count($producto['tallas']) > 1): ?>
                <label for="id">Talla:</label>
                <select name="id" id="id" required>
                    <option value="">Seleccione una talla</option>
                    <?php foreach ($producto['tallas'] as $talla): ?>
                        <?php if ((int) $talla->stock > 0): ?>
                            <option value="<?= (int) $talla->id ?>"><?= esc($talla->talla) ?> — <?= (int) $talla->stock ?> disponibles</option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="id" value="<?= (int) $producto['id'] ?>">
                <p>Stock: <?= (int) $producto['tallas'][0]->stock ?></p>
            <?php endif; ?>
            <button type="submit" <?= (int) $producto['tallas'][0]->stock < 1 && count($producto['tallas']) === 1 ? 'disabled' : '' ?>>Agregar al carro</button>
        </form>
    </section>
</main>
<style>.detalle-producto{display:flex;gap:24px;max-width:1100px;margin:24px auto;padding:16px}.detalle-image{max-width:55%;height:auto;object-fit:contain}.detalle-info{flex:1}label,select,button{display:block;margin:12px 0;padding:8px}@media(max-width:650px){.detalle-producto{display:block}.detalle-image{max-width:100%}}</style>