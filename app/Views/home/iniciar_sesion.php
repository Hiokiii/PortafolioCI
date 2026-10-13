<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión · Los Alces | Demo</title>
  <!--Made with love by Mutiullah Samim -->

  <!--Bootsrap 4 CDN-->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

  <!--Fontawesome CDN-->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
    integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

  <!--Custom styles-->

  <style>
    html,
    body {
    background-image: url(<?php echo base_url('/public/images/estadio2.jpg'); ?>);
    background-size: cover;
    background-repeat: no-repeat;
    height: 100%;
    font-family: 'Numans', sans-serif;
  }


    .container {
      min-height: 100vh;
      padding: 24px 15px;
      align-content: center;
    }

    .card {
      height: auto;
      margin-top: auto;
      margin-bottom: auto;
      width: 480px;
      max-width: 100%;
      background-color: rgba(10, 20, 15, 0.94) !important;
    }

    .social_icon span {
      font-size: 60px;
      margin-left: 10px;
      color: #000000;
    }

    .social_icon span:hover {
      color: white;
      cursor: pointer;
    }

    .card-header h3 {
      color: rgb(255, 238, 0);
    }

    .social_icon {
      position: absolute;
      right: 20px;
      top: -45px;
    }
    .back_button {
      position: absolute;
      right: 350px;
      top: -45px;
    }
    .back_button .btn {
      color: #000;
      font-size: 2rem;
    }

    .back_button .btn:hover {
      color: #fff;
    }

    .input-group-prepend span {
      width: 50px;
      background-color: #FFC312;
      color: black;
      border: 0 !important;
    }

    input:focus {
      outline: 0 0 0 0 !important;
      box-shadow: 0 0 0 0 !important;

    }

    .remember {
      color: white;
    }

    .remember input {
      width: 20px;
      height: 20px;
      margin-left: 15px;
      margin-right: 5px;
    }

    .login_btn {
      color: black;
      background-color: #FFC312;
      width: 150px;
    }

    .login_btn:hover {
      color: black;
      background-color: white;
    }

    .links {
      color: white;
    }

    .links a {
      margin-left: 4px;
    }
    .card-header h1 { color: white; font-size: 1.7rem; margin: 16px 0 6px; }
    .card-header p { color: #d4ded7; margin: 0; }
    .home-link { color: #ffc312; font-size: .9rem; }
    .demo-heading { color: #fff; font-size: 1.15rem; margin-bottom: 8px; }
    .demo-intro, .demo-status { color: #d4ded7; font-size: .9rem; }
    .demo-password { color: #fff; margin: 12px 0 16px; }
    .demo-password code { color: #ffc312; font-size: 1rem; overflow-wrap: anywhere; }
    .demo-accounts { display: grid; gap: 8px; margin-bottom: 18px; }
    .demo-account {
      display: flex; flex-wrap: wrap; justify-content: space-between; gap: 4px 12px;
      width: 100%; padding: 10px 12px; border: 1px solid #66766c;
      border-radius: 6px; background: #1d3024; color: #fff; text-align: left;
      cursor: pointer; font-size: .95rem;
    }
    .demo-account:hover { background: #2b4533; border-color: #ffc312; }
    .demo-account small { color: #d4ded7; overflow-wrap: anywhere; }
    .demo-account[aria-pressed="true"] { border-color: #ffc312; background: #35432a; }
    :focus-visible { outline: 3px solid #ffc312 !important; outline-offset: 3px; }
    .demo-status { min-height: 1.4em; margin: 0; }
    .login_btn { float: none !important; width: 100%; margin-top: 4px; }
    .card-footer { padding-top: 20px; }
  </style>
</head>

<body>
  <div class="container">
    <div class="d-flex justify-content-center h-100">
      <div class="card">
        <div class="card-header">
          <a href="<?= esc(base_url('Home'), 'attr') ?>" class="home-link">← Volver al inicio</a>
          <h1>Iniciar sesión</h1>
          <p>Club de fútbol Los Alces · Demo interactiva</p>
        </div>
        <div class="card-body">
          <form action="<?= esc(base_url('Home/validarIngreso'), 'attr') ?>" method="post">
            <div class="input-group form-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
              </div>
              <label for="email" class="sr-only">Correo electrónico</label>
              <input type="email" name="email" id="email" class="form-control" placeholder="Correo electrónico" autocomplete="username" required>

            </div>
            <div class="input-group form-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-key"></i></span>
              </div>
              <label for="password" class="sr-only">Contraseña</label>
              <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" autocomplete="current-password" required>
            </div>
            <div class="form-group">
              <input type="submit" value="Iniciar Sesión" class="btn float-right login_btn">
            </div>
          </form>

          <div class="row">
            <div class="col-12" style="text-align: center;">
              <?php
              if (isset($mensaje)) {
              ?>
                <div class="alert alert-danger" role="alert">
                  <?= esc($mensaje); ?>
                </div>

              <?php } ?>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <section aria-labelledby="demo-heading">
            <h2 id="demo-heading" class="demo-heading">Explora con una cuenta de demo</h2>
            <p class="demo-intro">Selecciona un perfil para completar el formulario. Las cuentas usan datos ficticios.</p>
            <p class="demo-password">Contraseña para todos: <code>DemoAlces2026!</code></p>
            <div class="demo-accounts">
              <?php foreach ([
                  'Administrador' => 'admin@example.com',
                  'Socio' => 'socio@example.com',
                  'Dirección' => 'direccion@example.com',
                  'Jugador' => 'jugador@example.com',
                  'Equipo técnico' => 'tecnico@example.com',
              ] as $role => $email): ?>
                <button type="button" class="demo-account" data-demo-email="<?= esc($email, 'attr') ?>" aria-pressed="false">
                  <strong><?= esc($role) ?></strong><small><?= esc($email) ?></small>
                </button>
              <?php endforeach; ?>
            </div>
            <p id="demo-status" class="demo-status" role="status" aria-live="polite"></p>
          </section>
          <div class="d-flex justify-content-center links">
            ¿Eres nuevo?<a href="<?= esc(base_url('Registrarse'), 'attr') ?>">Regístrate</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.querySelectorAll('[data-demo-email]').forEach(function (button) {
      button.addEventListener('click', function () {
        document.getElementById('email').value = button.dataset.demoEmail;
        document.getElementById('password').value = 'DemoAlces2026!';
        document.querySelectorAll('[data-demo-email]').forEach(function (option) {
          option.setAttribute('aria-pressed', String(option === button));
        });
        document.getElementById('demo-status').textContent = 'Perfil ' + button.querySelector('strong').textContent + ' seleccionado. Pulsa Iniciar Sesión.';
        document.querySelector('[type="submit"]').focus();
      });
    });
  </script>
</body>

</html>
