<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Boleta de compra</title><style>body{font-family:Arial,sans-serif}h2{text-align:center}table{width:100%;border-collapse:collapse;margin-bottom:20px}th,td{border:1px solid #ddd;padding:8px}.total{text-align:right;font-weight:bold}</style></head><body>
<h2>Boleta de compra</h2>
<p><strong>ID:</strong> <?= (int) $idCompra ?></p>
<p><strong>Nombre:</strong> <?= esc($nombre) ?></p><p><strong>Teléfono:</strong> <?= esc($telefono) ?></p>
<?php if ($entrega === 'despacho'): ?><p><strong>Despacho a:</strong> <?= esc($direccion) ?></p><?php else: ?><p>Retiro en club. Dirección Progreso 720, Chiguayante, Bío Bío.</p><?php endif; ?>
<table><thead><tr><th>Producto</th><th>Talla</th><th>Cantidad</th><th>Precio unitario</th><th>Subtotal</th></tr></thead><tbody>
<?php foreach ($productos as $producto): ?><tr><td><?= esc($producto['nombre']) ?></td><td><?= esc($producto['talla'] !== '' ? $producto['talla'] : 'N/A') ?></td><td><?= (int) $producto['cantidad'] ?></td><td>$<?= number_format((float) $producto['precio'], 0, ',', '.') ?></td><td>$<?= number_format((float) $producto['precio'] * (int) $producto['cantidad'], 0, ',', '.') ?></td></tr><?php endforeach; ?>
<?php if ($entrega === 'despacho'): ?><tr><td colspan="4">Despacho</td><td>$10.000</td></tr><?php endif; ?>
<tr class="total"><td colspan="4">Total</td><td>$<?= number_format((float) $total, 0, ',', '.') ?></td></tr></tbody></table>
</body></html>