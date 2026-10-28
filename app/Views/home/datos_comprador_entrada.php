<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Datos del comprador</title></head><body>
<div class="container">
    <?php if (session('error')): ?><div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div><?php endif; ?>
    <h2>Datos del comprador</h2>
    <?php if (session('error')): ?><div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div><?php endif; ?>
    <form id="compraForm" action="<?= site_url('VentaSouvenirsController/confirmarCompraEntrada') ?>" method="post" onsubmit="submitForm(event)">
        <?= csrf_field() ?>
        <input type="hidden" name="partidoId" value="<?= (int) $id ?>">
        <label for="nombre_comprador">Nombre completo:</label>
        <input class="input-field" type="text" id="nombre_comprador" name="nombre_comprador" maxlength="100" required value="<?= esc(old('nombre_comprador'), 'attr') ?>">
        <label for="rut_comprador">RUT:</label>
        <input class="input-field" type="text" id="rut_comprador" name="rut_comprador" placeholder="12.345.678-K" maxlength="12" pattern="[0-9.]+-[0-9Kk]" required value="<?= esc(old('rut_comprador'), 'attr') ?>">
        <button type="submit" class="btn-registrar-datos">Confirmar compra</button>
    </form>
</div>
<style>body{background:#333;color:#fff;font-family:Arial,sans-serif}.container{max-width:460px;margin:30px auto;padding:16px}.input-field{width:100%;box-sizing:border-box;padding:10px;border:0;border-radius:5px;margin-bottom:16px;background:#555;color:#fff}.btn-registrar-datos{background:#4caf50;color:#fff;padding:10px 20px;border:0;border-radius:5px;cursor:pointer}</style>
<script>
function submitForm(event){event.preventDefault();const form=event.currentTarget;const xhr=new XMLHttpRequest();xhr.open('POST',form.action,true);xhr.responseType='blob';xhr.onload=function(){if(xhr.status===200&&xhr.getResponseHeader('Content-Type')?.includes('application/pdf')){const url=URL.createObjectURL(xhr.response);const link=document.createElement('a');link.href=url;link.download='boleta_entrada.pdf';link.click();URL.revokeObjectURL(url);window.location.href='<?= site_url('VentaEntradas') ?>';}else{alert('No se pudo registrar la compra. Verifica los datos.');}};xhr.onerror=function(){alert('No se pudo conectar con el servidor.');};xhr.send(new FormData(form));}
</script></body></html>