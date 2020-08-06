<h1 class="mt-4 text-uppercase">crear cotización</h1>

<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?= constant("url") ?>">Panel de Control</a></li>
    <li class="breadcrumb-item active"><a href="<?= constant("url") ?>cotizaciones/crear">Crear</a></li>
</ol>

<?php if (empty($url[0])) : ?>

    <div class="card-deck">

        <div class="card">
            <img src="<?= constant("url") ?>public/img/auto.jpg" class="card-img-top">
            <a class="small text-white  stretched-link" href="<?= constant("url") ?>cotizaciones/crear/auto"></a>
            <div class="card-body">
                <h5 class="card-title text-center">AUTO</h5>
            </div>
        </div>

        <div class="card">
            <img src="<?= constant("url") ?>public/img/vida.png" class="card-img-top">
            <a class="small text-white  stretched-link" href="<?= constant("url") ?>cotizaciones/crear/vida"></a>
            <div class="card-body">
                <h5 class="card-title text-center">VIDA</h5>
            </div>
        </div>

        <div class="card">
            <img src="<?= constant("url") ?>public/img/desempleo.jpg" class="card-img-top">
            <a class="small text-white  stretched-link" href="<?= constant("url") ?>cotizaciones/crear/desempleo"></a>
            <div class="card-body">
                <h5 class="card-title text-center">DESEMPLEO</h5>
            </div>
        </div>

        <div class="card">
            <img src="<?= constant("url") ?>public/img/incendio.png" class="card-img-top">
            <a class="small text-white  stretched-link" href="<?= constant("url") ?>cotizaciones/crear/incendio"></a>
            <div class="card-body">
                <h5 class="card-title text-center">INCENDIO</h5>
            </div>
        </div>

    </div>

<?php elseif ($url[0] == "auto") : ?>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="<?= constant("url") ?>cotizaciones/crear/auto">

                        <h4>Deudor</h4>
                        <hr>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">RNC/Cédula</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="rnc_cedula">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Nombre</label>
                            <div class="col-sm-9">
                                <input required type="text" class="form-control" name="nombre">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Apellido</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="apellido">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Dirección</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="direccion">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tel. Celular</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" name="telefono">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tel. Residencial</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" name="tel_residencia">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tel. Trabajo</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" name="tel_trabajo">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Fecha de Nacimiento</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="fecha_nacimiento">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Correo Electrónico</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" name="correo">
                            </div>
                        </div>

                        <br>
                        <h4>Vehículo</h4>
                        <hr>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Marca</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="marca" id="marca" onchange="obtener_modelos(this)" required>
                                    <option value="" selected disabled>Selecciona una Marca</option>
                                    <?php
                                    $num_pagina = 1;
                                    do {
                                        $marcas = $api->lista_registros("Marcas", $num_pagina, 200);
                                        if (!empty($marcas)) {
                                            $num_pagina++;
                                            sort($marcas);
                                            foreach ($marcas as $marca) {
                                                echo '<option value="' . $marca->getEntityId() . '">' . strtoupper($marca->getFieldValue("Name")) . '</option>';
                                            }
                                        } else {
                                            $num_pagina = 0;
                                        }
                                    } while ($num_pagina > 0);
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Modelo</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="modelo" id="modelo" required>
                                    <option value="" selected disabled>Selecciona un Modelo</option>
                                    <div id="modelo"></div>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Uso</label>
                            <div class="col-sm-9">
                                <select name="uso" class="form-control">
                                    <option value="privado" selected>Privado</option>
                                    <option value="publico">Publico</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Año de fabricación</label>
                            <div class="col-sm-9">
                                <input required type="number" class="form-control" name="fabricacion" maxlength="4">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-3 col-form-label font-weight-bold">Estado</div>
                            <div class="col-sm-9">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck1" name="estado">
                                    <label class="form-check-label" for="gridCheck1">
                                        Nuevo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <br>
                        <h4>Plan</h4>
                        <hr>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tipo de plan</label>
                            <div class="col-sm-9">
                                <select name="tipo_plan" class="form-control">
                                    <option value="Full" selected>Full</option>
                                    <option value="Ley">Ley</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Facturación</label>
                            <div class="col-sm-9">
                                <select name="facturacion" class="form-control">
                                    <option value="mensual" selected>Mensual</option>
                                    <option value="anual">Anual</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Valor Asegurado</label>
                            <div class="col-sm-9">
                                <input required type="number" class="form-control" name="valor">
                            </div>
                        </div>

                        <br>
                        <button type="submit" class="btn btn-primary">Crear</button>
                        |
                        <a href="<?= constant("url") ?>cotizaciones/crear" class="btn btn-info">Cancelar</a>

                    </form>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($url[0] == "vida") : ?>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="<?= constant("url") ?>cotizaciones/crear/vida">

                        <h4>Deudor</h4>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">RNC/Cédula</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="rnc_cedula">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Nombre</label>
                            <div class="col-sm-9">
                                <input required type="text" class="form-control" name="nombre">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Apellido</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="apellido">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Dirección</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="direccion">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tel. Celular</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" name="telefono">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tel. Residencial</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" name="tel_residencia">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tel. Trabajo</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" name="tel_trabajo">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Fecha de Nacimiento</label>
                            <div class="col-sm-9">
                                <input required type="date" class="form-control" name="fecha_nacimiento">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Correo Electrónico</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" name="correo">
                            </div>
                        </div>

                        <br>
                        <h4>Codeudor</h4>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Fecha de Nacimiento Codeudor</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="fecha_codeudor">
                            </div>
                        </div>

                        <br>
                        <h4>Plan</h4>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Valor Asegurado</label>
                            <div class="col-sm-9">
                                <input required type="number" class="form-control" name="valor">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Plazo en meses</label>
                            <div class="col-sm-9">
                                <input required type="number" class="form-control" name="plazo">
                            </div>
                        </div>

                        <br>
                        <button type="submit" class="btn btn-primary">Crear</button>
                        |
                        <a href="<?= constant("url") ?>cotizaciones/crear" class="btn btn-info">Cancelar</a>

                    </form>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($url[0] == "desempleo") : ?>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="<?= constant("url") ?>cotizaciones/crear/desempleo">

                        <h4>Deudor</h4>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">RNC/Cédula</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="rnc_cedula">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Nombre</label>
                            <div class="col-sm-9">
                                <input required type="text" class="form-control" name="nombre">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Apellido</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="apellido">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Dirección</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="direccion">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tel. Celular</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" name="telefono">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tel. Residencial</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" name="tel_residencia">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tel. Trabajo</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" name="tel_trabajo">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Fecha de Nacimiento</label>
                            <div class="col-sm-9">
                                <input required type="date" class="form-control" name="fecha_nacimiento">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Correo Electrónico</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" name="correo">
                            </div>
                        </div>

                        <br>
                        <h4>Plan</h4>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Valor Asegurado</label>
                            <div class="col-sm-9">
                                <input required type="number" class="form-control" name="valor">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Plazo (meses)</label>
                            <div class="col-sm-9">
                                <input required type="number" class="form-control" name="plazo">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Cuota Mensual</label>
                            <div class="col-sm-9">
                                <input required type="number" class="form-control" name="cuota">
                            </div>
                        </div>

                        <br>
                        <button type="submit" class="btn btn-primary">Crear</button>
                        |
                        <a href="<?= constant("url") ?>cotizaciones/crear" class="btn btn-info">Cancelar</a>

                    </form>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($url[0] == "incendio") : ?>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="<?= constant("url") ?>cotizaciones/crear/incendio">

                        <h4>Deudor</h4>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">RNC/Cédula</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="rnc_cedula">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Nombre</label>
                            <div class="col-sm-9">
                                <input required type="text" class="form-control" name="nombre">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Apellido</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="apellido">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Dirección</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="direccion">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tel. Celular</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" name="telefono">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tel. Residencial</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" name="tel_residencia">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Tel. Trabajo</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" name="tel_trabajo">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Fecha de Nacimiento</label>
                            <div class="col-sm-9">
                                <input required type="date" class="form-control" name="fecha_nacimiento">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Correo Electrónico</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" name="correo">
                            </div>
                        </div>

                        <br>
                        <h4>Plan</h4>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Valor Asegurado</label>
                            <div class="col-sm-9">
                                <input required type="number" class="form-control" name="valor">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">Plazo en meses</label>
                            <div class="col-sm-9">
                                <input required type="number" class="form-control" name="plazo">
                            </div>
                        </div>

                        <br>
                        <button type="submit" class="btn btn-primary">Crear</button>
                        |
                        <a href="<?= constant("url") ?>cotizaciones/crear" class="btn btn-info">Cancelar</a>

                    </form>
                </div>
            </div>
        </div>
    </div>

<?php endif ?>

<script>
    function obtener_modelos(val) {
        var url = "<?= constant("url") ?>";
        $.ajax({
            url: url + "helpers/lista_modelos.php",
            type: "POST",
            data: {
                marcas_id: val.value
            },
            success: function(response) {
                document.getElementById("modelo").innerHTML = response;
            }
        });
    }
</script>