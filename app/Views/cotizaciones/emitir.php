<?= $this->extend('portal') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Emtir cotización No. <?= $cotizacion->getFieldValue('Quote_Number') ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= site_url("cotizaciones/detalles/" . $cotizacion->getEntityId()) ?>" class="btn btn-sm btn-outline-secondary">Volver</a>
        </div>
    </div>
</div>

<?php if ($cotizacion->getFieldValue('Tipo') == 'Auto') : ?>
    <form action="<?= site_url("cotizaciones/emitirAuto") ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <input type="text" value="<?= $cotizacion->getEntityId() ?>" name="id" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Plan') ?>" name="plantipo" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Marca')->getLookupLabel() ?>" name="marca" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Modelo')->getLookupLabel() ?>" name="modelo" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('A_o') ?>" name="a_o" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Tipo_veh_culo') ?>" name="modelotipo" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Condiciones') ?>" name="condiciones" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Suma_asegurada') ?>" name="suma" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Uso') ?>" name="uso" hidden>


        <div class="card mb-4">
            <h5 class="card-header">Cliente</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><b>Nombre</b></label>
                        <input required type="text" class="form-control" name="nombre" value="<?= $cotizacion->getFieldValue('Nombre') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Apellido</b></label>
                        <input type="text" class="form-control" name="apellido" value="<?= $cotizacion->getFieldValue('Apellido') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>RNC/Cédula</b></label>
                        <input required type="text" class="form-control" name="rnc_cedula" value="<?= $cotizacion->getFieldValue('RNC_C_dula') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Fecha de Nacimiento</b></label>
                        <input type="date" class="form-control" name="fecha_nacimiento" required value="<?= $cotizacion->getFieldValue('Fecha_de_nacimiento') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Correo Electrónico</b></label>
                        <input type="email" class="form-control" name="correo" value="<?= $cotizacion->getFieldValue('Correo_electr_nico') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Dirección</b></label>
                        <input type="text" class="form-control" name="direccion" value="<?= $cotizacion->getFieldValue('Direcci_n') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Tel. Celular</b></label>
                        <input type="tel" class="form-control" name="telefono" value="<?= $cotizacion->getFieldValue('Tel_Celular') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Tel. Residencial</b></label>
                        <input type="tel" class="form-control" name="tel_residencia" value="<?= $cotizacion->getFieldValue('Tel_Residencia') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Tel. Trabajo</b></label>
                        <input type="tel" class="form-control" name="tel_trabajo" value="<?= $cotizacion->getFieldValue('Tel_Trabajo') ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <h5 class="card-header">Vehículo</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><b>Chasis</b></label>
                        <input required type="text" class="form-control" name="chasis">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Placa</b></label>
                        <input type="text" class="form-control" name="placa">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Color</b></label>
                        <input type="text" class="form-control" name="color">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <h5 class="card-header">Emitir con</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><b>Aseguradora</b></label>
                        <select name="planid" class="form-control" required>
                            <?php foreach ($cotizacion->getLineItems() as $detalles) : ?>
                                <?php $planDetalles = $api->getRecord("Products", $detalles->getProduct()->getEntityId()) ?>
                                <?php if ($detalles->getListPrice() > 0) : ?>
                                    <option value="<?= $detalles->getProduct()->getEntityId() ?>">
                                        <?= $planDetalles->getFieldValue('Vendor_Name')->getLookupLabel() ?>
                                    </option>
                                <?php endif ?>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Documentos</b></label> <br>
                        <input required type="file" multiple class="form-control-file" name="documentos[]">
                    </div>
                </div>
            </div>
        </div>

        <button class="btn btn-primary" type="submit">Emitir</button>
    </form>
<?php elseif ($cotizacion->getFieldValue('Tipo') == 'Vida') : ?>
    <form action="<?= site_url("cotizaciones/emitirVida") ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <input type="text" value="<?= $cotizacion->getEntityId() ?>" name="id" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Plan') ?>" name="plantipo" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Edad_codeudor') ?>" name="edad_deudor" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Edad_deudor') ?>" name="edad_codeudor" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Cuota') ?>" name="cuota" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Plazo') ?>" name="plazo" hidden>
        <input type="text" value="<?= $cotizacion->getFieldValue('Suma_asegurada') ?>" name="suma" hidden>

        <div class="card mb-4">
            <h5 class="card-header">Deudor</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><b>Nombre</b></label>
                        <input required type="text" class="form-control" name="nombre" value="<?= $cotizacion->getFieldValue('Nombre') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Apellido</b></label>
                        <input type="text" class="form-control" name="apellido" value="<?= $cotizacion->getFieldValue('Apellido') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>RNC/Cédula</b></label>
                        <input required type="text" class="form-control" name="rnc_cedula" value="<?= $cotizacion->getFieldValue('RNC_C_dula') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Fecha de Nacimiento</b></label>
                        <input type="date" class="form-control" name="fecha_nacimiento" required value="<?= $cotizacion->getFieldValue('Fecha_de_nacimiento') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Correo Electrónico</b></label>
                        <input type="email" class="form-control" name="correo" value="<?= $cotizacion->getFieldValue('Correo_electr_nico') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Dirección</b></label>
                        <input type="text" class="form-control" name="direccion" value="<?= $cotizacion->getFieldValue('Direcci_n') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Tel. Celular</b></label>
                        <input type="tel" class="form-control" name="telefono" value="<?= $cotizacion->getFieldValue('Tel_Celular') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Tel. Residencial</b></label>
                        <input type="tel" class="form-control" name="tel_residencia" value="<?= $cotizacion->getFieldValue('Tel_Residencia') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Tel. Trabajo</b></label>
                        <input type="tel" class="form-control" name="tel_trabajo" value="<?= $cotizacion->getFieldValue('Tel_Trabajo') ?>">
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($cotizacion->getFieldValue('Edad_codeudor'))) : ?>
            <div class="card mb-4">
                <h5 class="card-header">Codeudor</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label><b>Nombre</b></label>
                            <input required type="text" class="form-control" name="nombre_codeudor">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Apellido</b></label>
                            <input type="text" class="form-control" name="apellido_codeudor">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>RNC/Cédula</b></label>
                            <input required type="text" class="form-control" name="rnc_cedula_codeudor">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Fecha de Nacimiento</b></label>
                            <input type="date" class="form-control" name="fecha_nacimiento_codeudor">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Correo Electrónico</b></label>
                            <input type="email" class="form-control" name="correo_codeudor">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Dirección</b></label>
                            <input type="text" class="form-control" name="direccion_codeudor">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Tel. Celular</b></label>
                            <input type="tel" class="form-control" name="telefono_codeudor">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Tel. Residencial</b></label>
                            <input type="tel" class="form-control" name="tel_residencia_codeudor">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label><b>Tel. Trabajo</b></label>
                            <input type="tel" class="form-control" name="tel_trabajo_codeudor">
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <div class="card mb-4">
            <h5 class="card-header">Emitir con</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><b>Aseguradora</b></label>
                        <select name="planid" class="form-control" required>
                            <?php foreach ($cotizacion->getLineItems() as $detalles) : ?>
                                <?php $planDetalles = $api->getRecord("Products", $detalles->getProduct()->getEntityId()) ?>
                                <?php if ($detalles->getListPrice() > 0) : ?>
                                    <option value="<?= $detalles->getProduct()->getEntityId() ?>">
                                        <?= $planDetalles->getFieldValue('Vendor_Name')->getLookupLabel() ?>
                                    </option>
                                <?php endif ?>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><b>Documentos</b></label> <br>
                        <input required type="file" multiple class="form-control-file" name="documentos[]">
                    </div>
                </div>
            </div>
        </div>

        <button class="btn btn-primary" type="submit">Emitir</button>
    </form>
<?php endif ?>

<br><br>

<?= $this->endSection() ?>