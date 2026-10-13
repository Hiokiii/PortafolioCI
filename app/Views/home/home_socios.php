<style>
  #video-background {
    position: fixed;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 70%;
    min-height: 100%;
    z-index: -1;
    opacity: 0.8;
    object-fit: cover;
  }

  .gradient-overlay {
    position: absolute;
    left: 50%;
    height: 100%;
    width: 20%;
    background-image: linear-gradient(to left, rgba(0, 0, 0, 1), rgba(0, 0, 0, 0));
    z-index: 0;
  }

  body {
    justify-content: flex-end;
    align-items: center;
    height: 100vh;
    background: #000;
    margin: 0;
    padding-top: 0px;
  }

  .buttons-container {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    flex-direction: row;
    height: 100%;
    width: 30%;
    padding-right: 2rem;
    position: relative;
    z-index: 1;
    margin-left: auto;
    margin-right: 4%;
  }

  .btn {
    display: inline-block;
    margin: 10px;
    padding: 10px 20px;
    background-color: yellow;
    color: black;
    border: none;
    text-align: center;
    font-size: 16px;
    cursor: pointer;
    outline: none;
    border-radius: 0;
  }

  .btn:hover {
    background-color: #f1c40f;
  }

  @media screen and (max-width: 991px) {
    #video-background {
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      width: 100%;
      height: 100%;
      filter: blur(10px);
    }

    .gradient-overlay {
      display: none;
    }

    body {
      justify-content: center;
    }

    .buttons-container {
      justify-content: center; /* Align buttons to the center */
      width: 100%;
      flex-direction: column;
    }

    .btn {
      width: 40%;
      height: auto;
      font-size: x-large;
      flex-wrap: wrap;
      margin-left: auto; /* Add this line to center-align the buttons horizontally */
      margin-right: auto; /* Add this line to center-align the buttons horizontally */
    }
  }
</style>


<body>
  <!-- Contenido de la página -->
  <div>
  <video autoplay muted loop poster="<?= base_url('public/images/estadio2.jpg') ?>" id="video-background">
    <source src="<?php echo base_url('public/Background5.mp4'); ?>" type="video/mp4">
  </video>
  </div>
  
  <div class="gradient-overlay"></div>
  <div class="buttons-container">
    <a type="button" class="btn" href=<?php echo base_url('IniciarSesion'); ?> style="float: right;">Iniciar Sesión</a>
    <a type="button" class="btn" href=<?php echo base_url('Registrarse'); ?> style="float: right;">Registrarse</a>
  </div>

</body>
