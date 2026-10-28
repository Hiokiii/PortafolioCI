<center><h2>Checkout</h2></center>
<?php if (session('error')): ?><div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div><?php endif; ?>
<ul>
<?php foreach ($productos as $producto): ?>
    <li><?= esc($producto['nombre']) ?> <?= $producto['talla'] !== '' ? '(' . esc($producto['talla']) . ')' : '' ?> × <?= (int) $producto['cantidad'] ?> — $<?= number_format($producto['precio'] * $producto['cantidad'], 0, ',', '.') ?></li>
<?php endforeach; ?>
</ul>
<p>Subtotal: $<?= number_format($total, 0, ',', '.') ?>. Despacho: $10.000 si corresponde.</p>
<form id="checkoutForm" action="<?= site_url('VentaSouvenirsController/confirmarCompra') ?>" method="post" class="checkout-form" onsubmit="submitForm(event)">
    <?= csrf_field() ?>
    <label for="nombre">Nombre completo:</label>
    <input type="text" id="nombre" name="nombre" maxlength="100" required value="<?= esc(old('nombre'), 'attr') ?>">
    <label for="telefono">Teléfono (solo números):</label>
    <input type="tel" id="telefono" name="telefono" inputmode="numeric" pattern="[0-9]{8,9}" maxlength="11" required value="<?= esc(old('telefono'), 'attr') ?>">
    <label for="entrega">Opción de entrega:</label>
    <select id="entrega" name="entrega" onchange="mostrarInputDireccion(this.value)" required>
        <option value="">Seleccione una opción...</option>
        <option value="retiro">Retiro en club</option>
        <option value="despacho">Despacho a domicilio</option>
    </select>
    <div id="direccionInput" style="display:none">
        <label for="direccion">Dirección de envío:</label>
        <input type="text" id="direccion" name="direccion" maxlength="100" value="<?= esc(old('direccion'), 'attr') ?>">
    </div>
    <div id="retiroInfo" style="display:none"><p>Cuenta con 30 días a partir de la compra para retirar sus productos.</p></div>
    <div id="despachoInfo" style="display:none"><p>El despacho tiene un valor total de $10.000 a todo Chile.</p></div>
    <button type="submit" id="myBtn" class="btn-registrar-datos">Confirmar compra</button>
</form>
<style>
body{background:#333;color:#fff;font-family:Arial,sans-serif}.checkout-form{display:flex;flex-direction:column;width:min(100% - 32px,360px);margin:0 auto}.checkout-form label{margin:10px 0 5px}.checkout-form input,.checkout-form select{padding:10px;margin-bottom:10px;border:0;background:#555;color:#fff}.checkout-form button{padding:10px;border:0;background:#4caf50;color:#fff;cursor:pointer;margin-top:10px}
</style>
<script>
function submitForm(event){event.preventDefault();const form=event.currentTarget;const xhr=new XMLHttpRequest();xhr.open('POST',form.action,true);xhr.responseType='blob';xhr.onload=function(){if(xhr.status===200&&xhr.getResponseHeader('Content-Type')?.includes('application/pdf')){const url=URL.createObjectURL(xhr.response);const link=document.createElement('a');link.href=url;link.download='boleta_compra.pdf';link.click();URL.revokeObjectURL(url);window.location.href='<?= site_url('TiendaOficial') ?>';}else{alert('No se pudo confirmar la compra. Revisa los datos e inténtalo otra vez.');}};xhr.onerror=function(){alert('No se pudo conectar con el servidor.');};xhr.send(new FormData(form));}
function mostrarInputDireccion(value){const despacho=value==='despacho';document.getElementById('direccionInput').style.display=despacho?'block':'none';document.getElementById('retiroInfo').style.display=value==='retiro'?'block':'none';document.getElementById('despachoInfo').style.display=despacho?'block':'none';document.getElementById('direccion').required=despacho;}
</script>