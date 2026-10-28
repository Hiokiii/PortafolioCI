<main class="shop">
    <?php if (session('error')): ?><div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div><?php endif; ?>
    <h1>Tienda oficial</h1>
    <div class="products">
    <?php foreach ($results as $article): ?>
        <article class="product">
            <a href="<?= site_url('DetalleProducto/' . (int) $article['id']) ?>">
                <img src="data:image/jpeg;base64,<?= esc(base64_encode($article['foto']), 'attr') ?>" alt="<?= esc($article['producto'], 'attr') ?>">
                <h2><?= esc($article['producto']) ?></h2>
            </a>
            <p>$<?= number_format((float) $article['precio'], 0, ',', '.') ?></p>
        </article>
    <?php endforeach; ?>
    </div>
    <a class="cart" href="<?= site_url('CarroCompras') ?>">Ver carro</a>
</main>
<style>.shop{max-width:1200px;margin:24px auto;padding:16px}.products{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:20px}.product img{width:100%;height:220px;object-fit:contain}.product a{color:inherit;text-decoration:none}.cart{position:fixed;right:20px;bottom:70px;background:#4caf50;color:white;padding:14px;border-radius:50px}</style>