<div class="alert alert-success" role="alert">
  <h4 class="alert-heading">¡Bienvenido al Insurance Tech de Grupo Nobe!</h4>
  <p>Desde su panel de control podrá ver la infomación necesaria manejar sus pólizas y cotizaciones.</p>
</div>
<div class="card-deck">
  <div class="card">
    <div class="card-header text-white bg-primary">
      <div class="row">
        <div class="col">
          <h1><?= $total ?></h1>
        </div>
        <div class="col">
          <h5>
            Registros Totales
          </h5>
        </div>
      </div>
    </div>
    <div class="card-body">
      <a href="<?= constant('url') ?>home/buscar" class="card-link">Ver más</a>
    </div>
  </div>
  <div class="card">
    <div class="card-header text-white bg-success">
      <div class="row">
        <div class="col">
          <h1><?= $pendientes ?></h1>
        </div>
        <div class="col">
          <h5>
            Cotizaciones al Mes
          </h5>
        </div>
      </div>
    </div>
    <div class="card-body">
      <a href="<?= ($pendientes > 0) ? constant('url') . "home/lista/" . $filtro_pendientes : "#"; ?>" class="card-link">Ver más</a>
    </div>
  </div>
  <div class="card">
    <div class="card-header text-white bg-info">
      <div class="row">
        <div class="col">
          <h1><?= $emisiones ?></h1>
        </div>
        <div class="col">
          <h5>
            Emisiones al Mes
          </h5>
        </div>
      </div>
    </div>
    <div class="card-body">
      <a href="<?= ($emisiones > 0) ? constant('url') . "home/lista/" . $filtro_emisiones : "#"; ?>" class="card-link">Ver más</a>
    </div>
  </div>
  <div class="card">
    <div class="card-header text-white bg-warning">
      <div class="row">
        <div class="col">
          <h1><?= $vencimientos ?></h1>
        </div>
        <div class="col">
          <h5>
            Vencimientos al Mes
          </h5>
        </div>
      </div>
    </div>
    <div class="card-body">
      <a href="<?= ($vencimientos > 0) ? constant('url') . "home/lista/" . $filtro_vencimientos : "#"; ?>" class="card-link">Ver más</a>
    </div>
  </div>
</div>