<!DOCTYPE html>
<html><head><meta charset="utf-8"><title><?= esc($title ?? 'Venta de entradas') ?></title></head><body>
<div class="container">
    <h1><?= esc($title ?? 'Venta de entradas') ?></h1>
    <?php if (session('error')): ?><div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div><?php endif; ?>
    <?php if (empty($results)): ?>
        <p>No hay partidos próximos.</p>
    <?php else: ?>
        <?php foreach ($results as $partido): ?>
            <article class="partido-info">
                <h2><?= esc($partido['nombre_equipo_local'] ?? '') ?> vs <?= esc($partido['nombre_equipo_visita'] ?? '') ?></h2>
                <p><?= esc($partido['fecha'] ?? '') ?></p>
                <p>Campeonato: <?= esc($partido['campeonato'] ?? '') ?></p>
                <p>Cancha: <?= esc($partido['nombre_cancha'] ?? '') ?> — <?= esc($partido['ubicacion'] ?? '') ?></p>
                <p>Valor: $5.000</p>
                <a class="confirmar-button" href="<?= site_url('DatosComprador/' . (int) $partido['id']) ?>">Comprar entrada</a>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<style>body{background:#333;color:#fff;font-family:Arial,sans-serif}.container{max-width:900px;margin:24px auto;padding:16px}.partido-info{margin:16px 0;padding:20px;border-radius:5px;background:#444}.confirmar-button{display:inline-block;background:#4caf50;color:#fff;padding:10px 20px;border-radius:5px;text-decoration:none}</style>
</body></html>