<?php

namespace App\Controllers;

use App\Libraries\Zoho;

class Cotizaciones extends BaseController
{
    function __construct()
    {
        $this->api = new Zoho;
    }

    public function index($pag = 1)
    {
        $criteria = "Account_Name:equals:" . session()->get("empresaid");
        $lista =  $this->api->searchRecordsByCriteria("Quotes", $criteria, $pag, 15);
        return view("cotizaciones/index", ["pag" => $pag, "cotizaciones" => $lista]);
    }

    public function buscar()
    {
        $criteria = "((Account_Name:equals:" . session("empresaid") . ") and (Quote_Number:equals:" . $this->request->getVar("busqueda") . "))";
        $lista =  $this->api->searchRecordsByCriteria("Quotes", $criteria, 1, 15);
        return view("cotizaciones/index", ["pag" => 1, "cotizaciones" => $lista]);
    }

    public function cotizar($tipo = null)
    {
        $marcas = $this->api->getRecords("Marcas");
        sort($marcas);
        return view("cotizaciones/cotizar", [
            "tipo" => $tipo,
            "marcas" => $marcas
        ]);
    }

    public function modelos()
    {
        $pag = 1;
        $criteria = "Marca:equals:" . $this->request->getVar("marcaid");
        do {
            if ($modelos = $this->api->searchRecordsByCriteria("Modelos", $criteria, $pag, 200)) {
                $pag++;
                asort($modelos);
                foreach ($modelos as $modelo) {
                    echo '<option value="' . $modelo->getEntityId() . "," . $modelo->getFieldValue('Tipo') . '">' . strtoupper($modelo->getFieldValue('Name')) . '</option>';
                }
            } else {
                $pag = 0;
            }
        } while ($pag > 0);
    }

    public function cotizarAuto()
    {
        $modelo = explode(",", $this->request->getVar("modelo"));
        $modeloid = $modelo[0];
        $modelotipo = $modelo[1];
        $planes = array();

        $criterio = "((Corredor:equals:" . session("empresaid") . ") and (Product_Category:equals:Auto))";
        $listaPlanes = $this->api->searchRecordsByCriteria("Products", $criterio, 1, 200);
        foreach ($listaPlanes as $plan) {
            $prima = 0;
            $motivo = "";

            if (in_array($this->request->getVar("uso"), $plan->getFieldValue('Restringir_veh_culos_de_uso'))) {
                $motivo = "Uso del vehículo restringido.";
            }

            if ((date("Y") - $this->request->getVar("a_o")) > $plan->getFieldValue('Max_antig_edad')) {
                $motivo = "La antigüedad del vehículo (" . (date("Y") - $this->request->getVar("a_o")) . ")  es mayor al limite establecido (" . $plan->getFieldValue('Max_antig_edad') . ")";
            }

            $criterio = "((Marca:equals:" . $this->request->getVar("marca") . ") and (Aseguradora:equals:" . $plan->getFieldValue('Vendor_Name')->getEntityId() . "))";
            $marcas = $this->api->searchRecordsByCriteria("Restringidos", $criterio, 1, 200);
            foreach ($marcas as $marca) {
                if (empty($marca->getFieldValue('Modelo'))) {
                    $motivo = "Marca restrigida.";
                }
            }

            $criterio = "((Modelo:equals:$modeloid) and (Aseguradora:equals:" . $plan->getFieldValue('Vendor_Name')->getEntityId() . "))";
            $modelos = $this->api->searchRecordsByCriteria("Restringidos", $criterio, 1, 200);
            foreach ($modelos as $modelo) {
                $motivo = "Modelo restrigido.";
            }

            if (empty($motivo)) {
                $criterio = "Plan:equals:" . $plan->getEntityId();
                $tasas = $this->api->searchRecordsByCriteria("Tasas", $criterio, 1, 200);
                foreach ($tasas as $tasa) {
                    if (
                        in_array($modelotipo, $tasa->getFieldValue('Grupo_de_veh_culo'))
                        and
                        $tasa->getFieldValue('A_o') == $this->request->getVar("a_o")
                    ) {
                        $tasavalor = $tasa->getFieldValue('Valor') / 100;
                    }
                }

                if (!empty($tasavalor)) {
                    $criterio = "Plan:equals:" . $plan->getEntityId();
                    $recargos = $this->api->searchRecordsByCriteria("Recargos", $criterio, 1, 200);
                    foreach ($recargos as $recargo) {
                        if (
                            $recargo->getFieldValue('Marca')->getEntityId() == $this->request->getVar("marca")
                            and
                            (($modelotipo == $recargo->getFieldValue("Tipo")
                                or
                                empty($recargo->getFieldValue("Tipo")))
                                and
                                ((empty($recargo->getFieldValue('Desde'))
                                    and
                                    empty($recargo->getFieldValue('Hasta')))
                                    or
                                    ($this->request->getVar("a_o") > $recargo->getFieldValue('Desde')
                                        and
                                        $this->request->getVar("a_o") < $recargo->getFieldValue('Hasta'))
                                    or
                                    ($this->request->getVar("a_o") > $recargo->getFieldValue('Desde')
                                        or
                                        $this->request->getVar("a_o") < $recargo->getFieldValue('Hasta'))))
                        ) {
                            $recargovalor = $recargo->getFieldValue('Valor') / 100;
                        }
                    }

                    if (!empty($recargovalor)) {
                        $tasavalor = ($tasavalor + ($tasavalor * $recargovalor));
                    }

                    $prima = $this->request->getVar("suma") * $tasavalor;

                    if ($prima < $plan->getFieldValue('Prima_m_nima')) {
                        $prima = $plan->getFieldValue('Prima_m_nima');
                    }

                    $prima = $prima / 12;
                } else {
                    $motivo = "No existen tasas para el tipo de vehículo especificado.";
                }
            }

            $planes[] = ["id" => $plan->getEntityId(), "precio" => $prima, "descripcion" => $motivo];
        }

        $registro = [
            "Subject" => "Cotización",
            "Account_Name" => session("empresaid"),
            "Contact_Name" => session("id"),
            "Nombre" => $this->request->getVar("nombre"),
            "Apellido" => $this->request->getVar("apellido"),
            "RNC_C_dula" => $this->request->getVar("rnc_cedula"),
            "Correo_electr_nico" => $this->request->getVar("correo"),
            "Direcci_n" => $this->request->getVar("direccion"),
            "Fecha_de_nacimiento" => $this->request->getVar("fecha_nacimiento"),
            "Tel_Celular" => $this->request->getVar("telefono"),
            "Tel_Residencia" => $this->request->getVar("tel_residencia"),
            "Tel_Trabajo" => $this->request->getVar("tel_trabajo"),
            "Plan" => $this->request->getVar("plan"),
            "A_o" => $this->request->getVar("a_o"),
            "Marca" => $this->request->getVar("marca"),
            "Modelo" => $modeloid,
            "Uso" => $this->request->getVar("uso"),
            "Tipo" => "Auto",
            "Tipo_veh_culo" => $modelotipo,
            "Suma_asegurada" =>  $this->request->getVar("suma"),
            "Condiciones" =>  $this->request->getVar("condiciones")
        ];

        $id = $this->api->createRecords("Quotes", $registro, $planes);
        return redirect()->to(site_url("cotizaciones/detalles/$id"));
    }

    public function cotizarVida()
    {
        $planes = array();
        $criterio = "((Corredor:equals:" . session("empresaid") . ") and (Product_Category:equals:Vida))";
        $listaPlanes = $this->api->searchRecordsByCriteria("Products", $criterio, 1, 200);

        foreach ($listaPlanes as $plan) {
            $prima = 0;
            $motivo = "";

            if (
                $this->request->getVar("edad_deudor") > $plan->getFieldValue('Edad_max')
                or
                (!empty($this->request->getVar("edad_codeudor"))
                    and
                    $this->request->getVar("edad_codeudor") > $plan->getFieldValue('Edad_max'))
            ) {
                $motivo = "La edad del deudor/codeudor es mayor al limite establecido.";
            }

            if (
                $this->request->getVar("edad_deudor") < $plan->getFieldValue('Edad_min')
                or (!empty($this->request->getVar("edad_codeudor"))
                    and
                    $this->request->getVar("edad_codeudor") < $plan->getFieldValue('Edad_min'))
            ) {
                $motivo = "La edad del deudor/codeudor es menor al limite establecido.";
            }

            if ($this->request->getVar("plazo") > $plan->getFieldValue('Plazo_max')) {
                $motivo = "El plazo es mayor al limite establecido.";
            }

            if ($this->request->getVar("suma") > $plan->getFieldValue('Suma_asegurada_max')) {
                $motivo = "La suma asegurada es mayor al limite establecido.";
            }

            if (empty($motivo)) {
                $criterio = "Plan:equals:" . $plan->getEntityId();
                $tasas = $this->api->searchRecordsByCriteria("Tasas", $criterio, 1, 200);
                foreach ($tasas as $tasa) {
                    switch ($tasa->getFieldValue('Tipo')) {
                        case 'Deudor':
                            $deudor = ($tasa->getFieldValue('Valor') / 100);
                            break;

                        case 'Codeudor':
                            $codeudor = ($tasa->getFieldValue('Valor') / 100);
                            break;

                        case 'Vida':
                            $vida = ($tasa->getFieldValue('Valor') / 100);
                            break;

                        case 'Desempleo':
                            $desempleo = $tasa->getFieldValue('Valor');
                            break;
                    }
                }

                $prima = ($this->request->getVar("suma") / 1000) * $deudor;
                $plantipo = "Vida";

                if ($this->request->getVar("edad_deudor")) {
                    $prima_codeudor = ($this->request->getVar("suma") / 1000) * ($codeudor - $deudor);
                    $prima = $prima + $prima_codeudor;
                }

                if ($this->request->getVar("cuota")) {
                    $prima_vida = ($this->request->getVar("suma") / 1000) * $vida;
                    $prima_desempleo =  ($this->request->getVar("cuota") / 1000) * $desempleo;
                    $prima = $prima_vida + $prima_desempleo;
                    $plantipo = "Vida/desempleo";
                }
            }

            $planes[] = ["id" => $plan->getEntityId(), "precio" => $prima, "descripcion" => $motivo];
        }

        $registro = [
            "Subject" => "Cotización",
            "Account_Name" => session("empresaid"),
            "Contact_Name" => session("id"),
            "Nombre" => $this->request->getVar("nombre"),
            "Apellido" => $this->request->getVar("apellido"),
            "RNC_C_dula" => $this->request->getVar("rnc_cedula"),
            "Correo_electr_nico" => $this->request->getVar("correo"),
            "Direcci_n" => $this->request->getVar("direccion"),
            "Tel_Celular" => $this->request->getVar("telefono"),
            "Tel_Residencia" => $this->request->getVar("tel_residencia"),
            "Tel_Trabajo" => $this->request->getVar("tel_trabajo"),
            "Plan" => $plantipo,
            "Edad_codeudor" => $this->request->getVar("edad_codeudor"),
            "Edad_deudor" => $this->request->getVar("edad_deudor"),
            "Cuota" => $this->request->getVar("cuota"),
            "Plazo" => $this->request->getVar("plazo"),
            "Tipo" => "Vida",
            "Suma_asegurada" =>  $this->request->getVar("suma"),
        ];

        $id = $this->api->createRecords("Quotes", $registro, $planes);
        return redirect()->to(site_url("cotizaciones/detalles/$id"));
    }

    public function detalles($id)
    {
        $detalles = $this->api->getRecord("Quotes", $id);
        if (!empty($detalles->getFieldValue('Deal_Name'))) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view("cotizaciones/detalles", ["cotizacion" => $detalles, "api" => $this->api]);
    }

    public function documentos($id)
    {
        $detalles = $this->api->getRecord("Quotes", $id);
        if (!empty($detalles->getFieldValue('Deal_Name'))) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view("cotizaciones/documentos", ["cotizacion" => $detalles, "api" => $this->api]);
    }

    public function adjuntos($json)
    {
        $json = json_decode($json);
        $fichero = $this->api->downloadAttachment("Products", $json[0], $json[1], WRITEPATH . "uploads");
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fichero) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fichero));
        readfile($fichero);
        unlink($fichero);
    }

    public function descargar($id)
    {
        $detalles = $this->api->getRecord("Quotes", $id);
        if (!empty($detalles->getFieldValue('Deal_Name'))) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view("cotizaciones/descargar", [
            "cotizacion" => $detalles,
            "api" => $this->api,
            "titulo" => "Cotización No." .  $detalles->getFieldValue('Quote_Number')
        ]);
    }

    public function emitir($id)
    {
        $detalles = $this->api->getRecord("Quotes", $id);
        if (!empty($detalles->getFieldValue('Deal_Name'))) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view("cotizaciones/emitir", ["cotizacion" => $detalles, "api" => $this->api]);
    }

    public function emitirAuto()
    {
        $cotizacion = $this->api->getRecord("Quotes", $this->request->getVar("id"));
        foreach ($cotizacion->getLineItems() as $detalles) {
            if ($this->request->getVar("planid") == $detalles->getProduct()->getEntityId()) {
                $plan = $this->api->getRecord("Products", $detalles->getProduct()->getEntityId());
                $comisionnobe = $detalles->getNetTotal() * ($plan->getFieldValue('Comisi_n_grupo_nobe') / 100);
                $comisionintermediario = $detalles->getNetTotal() * ($plan->getFieldValue('Comisi_n_intermediario') / 100);
                $comisionaseguradora = $detalles->getNetTotal() * ($plan->getFieldValue('Comisi_n_aseguradora') / 100);
                $comisioncorredor = $detalles->getNetTotal() * ($plan->getFieldValue('Comisi_n_corredor') / 100);
                $aseguradoraid = $plan->getFieldValue('Vendor_Name')->getEntityId();
                $primaneta = round($detalles->getListPrice(), 2);
                $isc = round($detalles->getTaxAmount(), 2);
                $primatotal = round($detalles->getNetTotal(), 2);
                $poliza = $plan->getFieldValue('P_liza');
            }
        }

        $bien = [
            "Apellido" => $this->request->getVar("apellido"),
            "Aseguradora" => $aseguradoraid,
            "Corredor" => session("empresaid"),
            "A_o" => $this->request->getVar("a_o"),
            "Name" => $this->request->getVar("chasis"),
            "Color" => $this->request->getVar("color"),
            "Email" => $this->request->getVar("correo"),
            "Direcci_n" => $this->request->getVar("direccion"),
            "Estado" => "Activo",
            "Fecha_de_nacimiento" => $this->request->getVar("fecha_nacimiento"),
            "Marca" => $this->request->getVar("marca"),
            "Modelo" => $this->request->getVar("modelo"),
            "Nombre" => $this->request->getVar("nombre"),
            "Placa" => $this->request->getVar("placa"),
            "Plan" => $this->request->getVar("plantipo"),
            "Prima" => $primatotal,
            "P_liza" => $poliza,
            "RNC_C_dula" => $this->request->getVar("rnc_cedula"),
            "Suma_asegurada" => $this->request->getVar("suma"),
            "Tel_Celular" => $this->request->getVar("telefono"),
            "Tel_Residencia" => $this->request->getVar("tel_residencia"),
            "Tel_Trabajo" => $this->request->getVar("tel_trabajo"),
            "Tipo" => $this->request->getVar("modelotipo"),
            "Vigencia_desde" => date("Y-m-d"),
            "Vigencia_hasta" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 1 years"))
        ];

        $bienid =  $this->api->createRecords("Bienes", $bien);

        $trato = [
            "Deal_Name" => "Emisión",
            "Account_Name" => session("empresaid"),
            "Contact_Name" => session("id"),
            "Bien" => $bienid,
            "Aseguradora" => $aseguradoraid,
            "Coberturas" => $this->request->getVar("planid"),
            "Comisi_n_aseguradora" => round($comisionaseguradora, 2),
            "Comisi_n_corredor" => round($comisioncorredor, 2),
            "Comisi_n_intermediario" => round($comisionintermediario, 2),
            "Stage" => "Pre aprobada",
            "Closing_Date" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 1 years")),
            "Amount" => round($comisionnobe, 2),
            "ISC" => round($isc, 2),
            "Prima_neta" => round($primaneta, 2),
            "Prima_total" => round($primatotal, 2),
            "Type" => "Auto"
        ];

        $tratoid =  $this->api->createRecords("Deals", $trato);

        $this->api->update("Quotes", $this->request->getVar("id"), [
            "Deal_Name" => $tratoid,
            "Valid_Till" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 1 years"))
        ]);

        if ($imagefile = $this->request->getFiles()) {
            foreach ($imagefile['documentos'] as $img) {
                $newName = $img->getRandomName();
                $img->move(WRITEPATH . 'uploads', $newName);
                $this->api->uploadAttachment("Deals", $tratoid, WRITEPATH . "uploads/$newName");
                unlink(WRITEPATH . "uploads/$newName");
            }
        }

        return redirect()->to(site_url("polizas/detalles/$tratoid"));
    }

    public function emitirVida()
    {
        $cotizacion = $this->api->getRecord("Quotes", $this->request->getVar("id"));
        foreach ($cotizacion->getLineItems() as $detalles) {
            if ($this->request->getVar("planid") == $detalles->getProduct()->getEntityId()) {
                $plan = $this->api->getRecord("Products", $detalles->getProduct()->getEntityId());
                $comisionnobe = $detalles->getNetTotal() * ($plan->getFieldValue('Comisi_n_grupo_nobe') / 100);
                $comisionintermediario = $detalles->getNetTotal() * ($plan->getFieldValue('Comisi_n_intermediario') / 100);
                $comisionaseguradora = $detalles->getNetTotal() * ($plan->getFieldValue('Comisi_n_aseguradora') / 100);
                $comisioncorredor = $detalles->getNetTotal() * ($plan->getFieldValue('Comisi_n_corredor') / 100);
                $aseguradoraid = $plan->getFieldValue('Vendor_Name')->getEntityId();
                $primaneta = round($detalles->getListPrice(), 2);
                $isc = round($detalles->getTaxAmount(), 2);
                $primatotal = round($detalles->getNetTotal(), 2);
                $poliza = $plan->getFieldValue('P_liza');
            }
        }

        $bien = [
            "Apellido" => $this->request->getVar("apellido"),
            "Aseguradora" => $aseguradoraid,
            "Corredor" => session("empresaid"),
            "Name" => $this->request->getVar("rnc_cedula"),
            "Email" => $this->request->getVar("correo"),
            "Direcci_n" => $this->request->getVar("direccion"),
            "Estado" => "Activo",
            "Fecha_de_nacimiento" => $this->request->getVar("fecha_nacimiento"),
            "Nombre" => $this->request->getVar("nombre"),
            "Plan" => $this->request->getVar("plantipo"),
            "Prima" => $primatotal,
            "P_liza" => $poliza,
            "RNC_C_dula" => $this->request->getVar("rnc_cedula"),
            "Suma_asegurada" => $this->request->getVar("suma"),
            "Tel_Celular" => $this->request->getVar("telefono"),
            "Tel_Residencia" => $this->request->getVar("tel_residencia"),
            "Tel_Trabajo" => $this->request->getVar("tel_trabajo"),
            "Vigencia_desde" => date("Y-m-d"),
            "Vigencia_hasta" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 1 years")),
            "Cuota" => $this->request->getVar->input("cuota"),
            "Plazo" => $this->request->getVar->input("plazo"),
            "Tel_Celular_codeudor" => $this->request->getVar->input("telefono_codeudor"),
            "Tel_Residencia_codeudor" => $this->request->getVar->input("tel_residencia_codeudor"),
            "Tel_Trabajo_codeudor" => $this->request->getVar->input("tel_trabajo_codeudor"),
            "RNC_C_dula_codeudor" => $this->request->getVar->input("rnc_cedula_codeudor"),
            "Fecha_de_nacimiento_codeudor" => $this->request->getVar->input("fecha_nacimiento_codeudor"),
            "Direcci_n_codeudor" => $this->request->getVar->input("direccion_codeudor"),
            "Correo_electr_nico_codeudor" => $this->request->getVar->input("correo_codeudor"),
        ];

        $bienid =  $this->api->createRecords("Bienes", $bien);

        $trato = [
            "Account_Name" => session("empresaid"),
            "Contact_Name" => session("id"),
            "Bien" => $bienid,
            "Aseguradora" => $aseguradoraid,
            "Coberturas" => $this->request->getVar("planid"),
            "Comisi_n_aseguradora" => $comisionaseguradora,
            "Comisi_n_corredor" => $comisioncorredor,
            "Comisi_n_intermediario" => $comisionintermediario,
            "Stage" => "Pre aprobada",
            "Closing_Date" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 1 years")),
            "Amount" => $comisionnobe,
            "ISC" => $isc,
            "Deal_Name" => "Emisión",
            "Prima_neta" => $primaneta,
            "Prima_total" => $primatotal,
            "Type" => "Vida"
        ];

        $tratoid =  $this->api->createRecords("Deals", $trato);

        $this->api->update("Quotes", $this->request->getVar("id"), [
            "Deal_Name" => $tratoid,
            "Valid_Till" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 1 years"))
        ]);

        if ($imagefile = $this->request->getFiles()) {
            foreach ($imagefile['documentos'] as $img) {
                $newName = $img->getRandomName();
                $img->move(WRITEPATH . 'uploads', $newName);
                $this->api->uploadAttachment("Deals", $tratoid, WRITEPATH . "uploads/$newName");
                unlink(WRITEPATH . "uploads/$newName");
            }
        }

        return redirect()->to(site_url("polizas/detalles/$tratoid"));
    }
}
