<?php

class HL7Service {


    //A28
    public function DatosAXaviaAdcionarPersona(
            $datos, $subsistema
    ) {
        $persona = $datos;
        //var_dump($persona[0][nombre]); die;
        $dir2 = split("web", $_SERVER['DOCUMENT_ROOT']);
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7Client.php';
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7.php';
        $hl7 = new HL7();
        $msh = $hl7->addSegment('MSH');
        $msh->setValue('3.1', ''); // Sending Application
        $msh->setValue('4.1', ''); // Sending Facility
        $msh->setValue('5.1', ''); // Receiving Application
        $msh->setValue('6.1', ''); // Receiving Application
        $msh->setValue('7.1', strftime("%Y%m%d%H%M%S")); // Receiving Facility
        $msh->setValue('9.1', 'ADT');
        $msh->setValue('9.2', 'A28');
        $msh->setValue('9.3', 'ADT_A05');
        $msh->setValue('10.1', '1234asd567');
        $msh->setValue('11.1', 'T'); // P = Production
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('17.1', 'CUB'); // HL7 version
        $msh->setValue('18.1', 'ISO-8859-1'); // HL7 version
        $pid = $hl7->addSegment('PID');
        $pid->setValue('1', 1);
        $pid->setValue('2', $persona[0]['idsituacionlaboralp']);
        $pid->setValue('3.1', $persona[0]['idsituacionlaboralp']);
        $pid->setValue('5.1', $persona[0]['papel']);
        $pid->setValue('5.2', $persona[0]['nombre']);
        $pid->setValue('5.3', $persona[0]['snombre']);
        $pid->setValue('6.1', $persona[0]['sapel']);
        $pid->setValue('8', $persona[0]['sexo']);
        $pid->setValue('10', 'N');
        $pid->setValue('12', 'CUB');
        $rol = $hl7->addSegment('ROL');
        $rol->setValue('1.1', $persona[0]['idcargo']);
        $rol->setValue('1.2', $persona[0]['idcargo']);
        $rol->setValue('2', 'AD');
        $rol->setValue('3.1', $persona[0]['idcargo']);
        $rol->setValue('3.2', $persona[0]['cargo']);

        $pvv = $hl7->addSegment('PV1');
        $pvv->setValue('1', 1);
        //var_dump($hl7->getMessage()); die;
        $client = new HL7Client();
        $client->Connect();
        //var_dump($client); die;
        $res = $client->Send($hl7->getMessage());

        $client->Disconnect();
        return 1;
    }

    //A29
    public function DatosAXaviaEliminarPersona(
            $datos, $subsistema
    ) {
        $persona = $datos;
        //var_dump($persona[0][nombre]); die;
        $dir2 = split("web", $_SERVER['DOCUMENT_ROOT']);
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7Client.php';
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7.php';
        $hl7 = new HL7();
        $msh = $hl7->addSegment('MSH');
        $msh->setValue('3.1', ''); // Sending Application
        $msh->setValue('4.1', ''); // Sending Facility
        $msh->setValue('5.1', ''); // Receiving Application
        $msh->setValue('6.1', ''); // Receiving Application
        $msh->setValue('7.1', strftime("%Y%m%d%H%M%S")); // Receiving Facility
        $msh->setValue('9.1', 'ADT');
        $msh->setValue('9.2', 'A29');
        $msh->setValue('9.3', 'ADT_A21');
        $msh->setValue('10.1', '1234asd567');
        $msh->setValue('11.1', 'T'); // P = Production
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('17.1', 'CUB'); // HL7 version
        $msh->setValue('18.1', 'ISO-8859-1'); // HL7 version
        $pid = $hl7->addSegment('PID');
        $pid->setValue('1', 1);
        $pid->setValue('2', $persona[0]['idsituacionlaboralp']);
        $pid->setValue('3.1', $persona[0]['idsituacionlaboralp']);
        $pid->setValue('5.1', $persona[0]['papel']);
        $pid->setValue('5.2', $persona[0]['nombre']);
        $pid->setValue('5.3', $persona[0]['snombre']);
        $pid->setValue('6.1', $persona[0]['sapel']);
        $pid->setValue('8', $persona[0]['sexo']);
        $pid->setValue('10', 'N');
        $pid->setValue('12', 'CUB');
        $rol = $hl7->addSegment('ROL');
        $rol->setValue('1.1', $persona[0]['idcargo']);
        $rol->setValue('1.2', $persona[0]['idcargo']);
        $rol->setValue('2', 'AD');
        $rol->setValue('3.1', $persona[0]['idcargo']);
        $rol->setValue('3.2', $persona[0]['cargo']);

        $pvv = $hl7->addSegment('PV1');
        $pvv->setValue('1', 1);
        //var_dump($hl7->getMessage()); die;
        $client = new HL7Client();
        $client->Connect();
        //var_dump($client); die;
        $res = $client->Send($hl7->getMessage());

        $client->Disconnect();

        var_dump($hl7->getMessage());
        var_dump($res);
        return 1;
    }

    //A29
    public function DatosAXaviaModificarPersona(
            $datos, $subsistema
    ) {
        $persona = $datos;
        //var_dump($persona[0][nombre]); die;
        $dir2 = split("web", $_SERVER['DOCUMENT_ROOT']);
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7Client.php';
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7.php';
        $hl7 = new HL7();
        $msh = $hl7->addSegment('MSH');
        $msh->setValue('3.1', ''); // Sending Application
        $msh->setValue('4.1', ''); // Sending Facility
        $msh->setValue('5.1', ''); // Receiving Application
        $msh->setValue('6.1', ''); // Receiving Application
        $msh->setValue('7.1', strftime("%Y%m%d%H%M%S")); // Receiving Facility
        $msh->setValue('9.1', 'ADT');
        $msh->setValue('9.2', 'A31');
        $msh->setValue('9.3', 'ADT_A05');
        $msh->setValue('10.1', '1234asd567');
        $msh->setValue('11.1', 'T'); // P = Production
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('17.1', 'CUB'); // HL7 version
        $msh->setValue('18.1', 'ISO-8859-1'); // HL7 version
        $pid = $hl7->addSegment('PID');
        $pid->setValue('1', 1);
        $pid->setValue('2', $persona[0]['idsituacionlaboralp']);
        $pid->setValue('3.1', $persona[0]['idsituacionlaboralp']);
        $pid->setValue('5.1', $persona[0]['papel']);
        $pid->setValue('5.2', $persona[0]['nombre']);
        $pid->setValue('5.3', $persona[0]['snombre']);
        $pid->setValue('6.1', $persona[0]['sapel']);
        $pid->setValue('8', $persona[0]['sexo']);
        $pid->setValue('10', 'N');
        $pid->setValue('12', 'CUB');
        $rol = $hl7->addSegment('ROL');
        $rol->setValue('1.1', $persona[0]['idcargo']);
        $rol->setValue('1.2', $persona[0]['idcargo']);
        $rol->setValue('2', 'UP');
        $rol->setValue('3.1', $persona[0]['idcargo']);
        $rol->setValue('3.2', $persona[0]['cargo']);

        $pvv = $hl7->addSegment('PV1');
        $pvv->setValue('1', 1);
        //var_dump($hl7->getMessage()); die;
        $client = new HL7Client();
        $client->Connect();
        //var_dump($client); die;
        $res = $client->Send($hl7->getMessage());
//        var_dump($hl7->getMessage());
//        var_dump($res);
        $client->Disconnect();
        return 1;
    }

    //ORM_O01, OML_O21, OMI_23
    public function serviciosFacturados(
            $iddocumento
    ) {
        foreach ($iddocumento as $value) {
            $data = Doctrine_Query::create()
                    ->from('DatDocumentoCliente d')
                    ->where('d.iddocumento = ? ', array($value))
                    ->fetchArray();
            $idcliente = $data[0]['idcliente'];
            $conn = Doctrine_Manager::connection();
            $sql = "SELECT idclientesproveedores, codigo, nombre
                           FROM mod_datosmaestros.nom_clientesproveedor where idclientesproveedores =$idcliente;";
            $arrayidcliente = $conn->execute($sql)->fetchAll(PDO::FETCH_ASSOC);

            $conn = Doctrine_Manager::connection();
            $sql = "SELECT 
                        mod_datosmaestros.nom_indicadorhoja.codigoauxiliar as codigo,
                        mod_datosmaestros.nom_indicador.codigo as distracodigo,
                        mod_logfacturacion.dat_elementosfactura.idfactura,
                        mod_logfacturacion.dat_elementosfactura.idexterno
                      FROM
                        mod_logfacturacion.dat_elementosfactura
                        INNER JOIN mod_logfacturacion.dat_prodservnomenclador ON (mod_logfacturacion.dat_elementosfactura.idelementofactura = mod_logfacturacion.dat_prodservnomenclador.idelementofactura)
                        INNER JOIN mod_datosmaestros.nom_indicador ON (mod_logfacturacion.dat_prodservnomenclador.idindicador = mod_datosmaestros.nom_indicador.idindicador)
                        INNER JOIN mod_datosmaestros.nom_indicadorhoja ON (mod_datosmaestros.nom_indicador.idindicador = mod_datosmaestros.nom_indicadorhoja.idindicador)
                      WHERE
                        mod_logfacturacion.dat_elementosfactura.idfactura = $value";
            $arrayservicios = $conn->execute($sql)->fetchAll(PDO::FETCH_ASSOC);

//            var_dump($arrayidcliente);
//                var_dump($arrayservicios); die;

            $t->EnviarORMO01($arrayidcliente, $arrayservicios);
            $t->EnviarOMIO23($arrayidcliente, $arrayservicios);
            $t->EnviarOMLO21($arrayidcliente, $arrayservicios);
        }
    }

    public function serviciosNoHaFacturar(
            $arrayidcliente, $arrayservicios
    ) {
        //var_dump($persona[0][nombre]); die;
        $dir2 = split("web", $_SERVER['DOCUMENT_ROOT']);
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7Client.php';
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7.php';
        $hl7 = new HL7();
        $msh = $hl7->addSegment('MSH');
        $msh->setValue('3.1', ''); // Sending Application
        $msh->setValue('4.1', ''); // Sending Facility
        $msh->setValue('5.1', ''); // Receiving Application
        $msh->setValue('6.1', ''); // Receiving Application
        $msh->setValue('7.1', strftime("%Y%m%d%H%M%S")); // Receiving Facility
        $msh->setValue('9.1', 'ORM');
        $msh->setValue('9.2', 'O01');
        $msh->setValue('9.3', 'ORM_O01');
        $msh->setValue('10.1', '1234asd567');
        $msh->setValue('11.1', 'T'); // P = Production
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('17.1', 'CUB'); // HL7 version
        $msh->setValue('18.1', 'ISO-8859-1'); // HL7 version

        $pid = $hl7->addSegment('PID');
        $pid->setValue('1', 1);
        $pid->setValue('2', $arrayidcliente[0]['codigo']);
        $pid->setValue('3.1', $arrayidcliente[0]['codigo']);
        $pid->setValue('5.1', $arrayidcliente[0]['nombre']);
        $pid->setValue('5.2', $arrayidcliente[0]['nombre']);
        $pid->setValue('10', 'N');
        $pid->setValue('12', 'CUB');

        $pvv = $hl7->addSegment('PV1');
        $pvv->setValue('1', 1);

        foreach ($arrayservicios as $key => $value) {
            $orc = $hl7->addSegment('ORC');
            $orc->setValue('1', '');
            $orc->setValue('2', $value['codigo']);
            $orc->setValue('3.1', $value['codigo']);
            $orc->setValue('5.1', $value['nombre']);
            $orc->setValue('5.2', $value['nombre']);
            $obr = $hl7->addSegment('OBR');
            $obr->setValue('1', $key);
            $obr->setValue('2', $value['codigo']);
            $obr->setValue('3.1', $value['codigo']);
            $obr->setValue('5.1', $value['nombre']);
            $obr->setValue('5.2', $value['nombre']);
            $obx = $hl7->addSegment('OBX');
            $obx->setValue('1', $key);
            $obx->setValue('2', $value['codigo']);
            $obx->setValue('3.1', $value['codigo']);
            $obx->setValue('5.1', $value['nombre']);
            $obx->setValue('5.2', $value['nombre']);
        }

        //var_dump($hl7->getMessage()); die;
        $client = new HL7Client();
        $client->Connect();
        //var_dump($client); die;
        $res = $client->Send($hl7->getMessage());
//        var_dump($hl7->getMessage());
//        var_dump($res);
        $client->Disconnect();
        return 1;
    }

    protected function EnviarORMO01($arrayidcliente, $arrayservicios) {
        //var_dump($persona[0][nombre]); die;
        $dir2 = split("web", $_SERVER['DOCUMENT_ROOT']);
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7Client.php';
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7.php';
        $hl7 = new HL7();
        $msh = $hl7->addSegment('MSH');
        $msh->setValue('3.1', ''); // Sending Application
        $msh->setValue('4.1', ''); // Sending Facility
        $msh->setValue('5.1', ''); // Receiving Application
        $msh->setValue('6.1', ''); // Receiving Application
        $msh->setValue('7.1', strftime("%Y%m%d%H%M%S")); // Receiving Facility

        $msh->setValue('9.1', 'ORM');
        $msh->setValue('9.2', 'O01');
        //$msh->setValue('9.3', 'ORM_O01');
        $msh->setValue('10.1', '1234asd567');
        $msh->setValue('11.1', 'T'); // P = Production
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('17.1', 'CUB'); // HL7 version
        $msh->setValue('18.1', 'ISO-8859-1'); // HL7 version

        $pid = $hl7->addSegment('PID');
        $pid->setValue('1', 1);
        $pid->setValue('2', $arrayidcliente[0]['codigo']);
        //$pid->setValue('3.1', $arrayidcliente['codigo']);
        $pid->setValue('5.1', $arrayidcliente[0]['nombre']);
        $pid->setValue('5.2', $arrayidcliente[0]['nombre']);
//        $pid->setValue('10', 'N');
//        $pid->setValue('12', 'CUB');

        $pvv = $hl7->addSegment('PV1');
        $pvv->setValue('1', 1);

        $bandera = false;

        foreach ($arrayservicios as $key => $value) {
            $cont = 1;
            $aux = split('-', $value['idexterno']);
            if ($aux[1] == "SIU" || $aux[1] == "ORM") {
                $orc = $hl7->addSegment('ORC');
                $orc->setValue('1', 'AF');
                $orc->setValue('3.1', $aux[0]);
                $orc->setValue('3.2', "");
                $orc->setValue('3.3', "");
                $orc->setValue('3.4', "ISO");
                $orc->setValue('4.1', $aux[0]);
                $orc->setValue('4.2', "");
                $orc->setValue('4.3', "");
                $orc->setValue('4.4', "ISO");
                $orc->setValue('9', strftime("%Y%m%d%H%M%S"));
                $orc->setValue('12', "idmedico");
                $orc->setValue('29', $aux[2]);
                $obr = $hl7->addSegment('OBR');
                $obr->setValue('1', $cont);
                $obr->setValue('2.1', "");
                $obr->setValue('2.2', "");
                $obr->setValue('2.3', "");
                $obr->setValue('2.4', "ISO");
                $obr->setValue('3.1', $aux[0]);
                $obr->setValue('3.2', "");
                $obr->setValue('3.3', "");
                $obr->setValue('3.4', "ISO");
                $obr->setValue('4.1', $value['codigo']);
                $obr->setValue('4.2', "");
                $obr->setValue('4.3', "");
                $obr->setValue('4.4', $cont);
                $obx = $hl7->addSegment('OBX');
                $obx->setValue('1', $cont);
                $obx->setValue('2', "");
                $obx->setValue('3.1', "");
                $obx->setValue('3.2', "");
                $obx->setValue('3.3', "");
                $obx->setValue('3.4', "ISO");
                $obx->setValue('3.1', "");
                $obx->setValue('5.1', "");
                $obx->setValue('5.2', "");
                $bandera = true;
                $cont++;
            }
        }
        if ($bandera == true) {
            $client = new HL7Client();
            $client->Connect();
            $res = $client->Send($hl7->getMessage());
            $client->Disconnect();
            var_dump($hl7->getMessage());
            var_dump($res); die;
        }

        return 1;
    }

    protected function EnviarOMIO23($arrayidcliente, $arrayservicios) {
        //var_dump($persona[0][nombre]); die;
        $dir2 = split("web", $_SERVER['DOCUMENT_ROOT']);
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7Client.php';
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7.php';
        $hl7 = new HL7();
        $msh = $hl7->addSegment('MSH');
        $msh->setValue('3.1', ''); // Sending Application
        $msh->setValue('4.1', ''); // Sending Facility
        $msh->setValue('5.1', ''); // Receiving Application
        $msh->setValue('6.1', ''); // Receiving Application
        $msh->setValue('7.1', strftime("%Y%m%d%H%M%S")); // Receiving Facility

        $msh->setValue('9.1', 'OMI');
        $msh->setValue('9.2', 'O23');
        //$msh->setValue('9.3', 'ORM_O01');
        $msh->setValue('10.1', '1234asd567');
        $msh->setValue('11.1', 'T'); // P = Production
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('17.1', 'CUB'); // HL7 version
        $msh->setValue('18.1', 'ISO-8859-1'); // HL7 version

        $pid = $hl7->addSegment('PID');
        $pid->setValue('1', 1);
        $pid->setValue('2', $arrayidcliente[0]['codigo']);
        //$pid->setValue('3.1', $arrayidcliente['codigo']);
        $pid->setValue('5.1', $arrayidcliente[0]['nombre']);
        $pid->setValue('5.2', $arrayidcliente[0]['nombre']);
//        $pid->setValue('10', 'N');
//        $pid->setValue('12', 'CUB');

        $pvv = $hl7->addSegment('PV1');
        $pvv->setValue('1', 1);

        $bandera = false;

        foreach ($arrayservicios as $key => $value) {
            $cont = 1;
            $aux = split('-', $value['idexterno']);
            if ($aux[1] == "OMI") {
                $orc = $hl7->addSegment('ORC');
                $orc->setValue('1', 'AF');
                $orc->setValue('3.1', "");
                $orc->setValue('3.2', "");
                $orc->setValue('3.3', "");
                $orc->setValue('3.4', "ISO");
                $orc->setValue('4.1', "");
                $orc->setValue('4.2', "");
                $orc->setValue('4.3', "");
                $orc->setValue('4.4', "ISO");
                $orc->setValue('9', strftime("%Y%m%d%H%M%S"));
                $obr = $hl7->addSegment('OBR');
                $obr->setValue('1', $cont);
                $obr->setValue('2.1', "");
                $obr->setValue('2.2', "");
                $obr->setValue('2.3', "");
                $obr->setValue('2.4', "ISO");
                $obr->setValue('3.1', $aux[0]);
                $obr->setValue('3.2', "");
                $obr->setValue('3.3', "");
                $obr->setValue('3.4', "ISO");
                $obr->setValue('4.1', $value['codigo']);
                $obr->setValue('4.2', "");
                $obr->setValue('4.3', "");
                $obr->setValue('4.4', $cont);
                $obx = $hl7->addSegment('OBX');
                $obx->setValue('1', $cont);
                $obx->setValue('2', "");
                $obx->setValue('3.1', "");
                $obx->setValue('3.2', "");
                $obx->setValue('3.3', "");
                $obx->setValue('3.4', "ISO");
                $obx->setValue('3.1', "");
                $obx->setValue('5.1', "");
                $obx->setValue('5.2', "");
                $bandera = true;
                $cont++;
            }
        }
        if ($bandera == true) {
            $client = new HL7Client();
            $client->Connect();
            $res = $client->Send($hl7->getMessage());
            $client->Disconnect();
        }

        return 1;
    }

    protected function EnviarOMLO21($arrayidcliente, $arrayservicios) {
        //var_dump($persona[0][nombre]); die;
        $dir2 = split("web", $_SERVER['DOCUMENT_ROOT']);
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7Client.php';
        require_once '' . $dir2[0] . '/apps/masterhl7/HL7/HL7.php';
        $hl7 = new HL7();
        $msh = $hl7->addSegment('MSH');
        $msh->setValue('3.1', ''); // Sending Application
        $msh->setValue('4.1', ''); // Sending Facility
        $msh->setValue('5.1', ''); // Receiving Application
        $msh->setValue('6.1', ''); // Receiving Application
        $msh->setValue('7.1', strftime("%Y%m%d%H%M%S")); // Receiving Facility

        $msh->setValue('9.1', 'OML');
        $msh->setValue('9.2', 'O21');
        //$msh->setValue('9.3', 'ORM_O01');
        $msh->setValue('10.1', '1234asd567');
        $msh->setValue('11.1', 'T'); // P = Production
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msh->setValue('17.1', 'CUB'); // HL7 version
        $msh->setValue('18.1', 'ISO-8859-1'); // HL7 version

        $pid = $hl7->addSegment('PID');
        $pid->setValue('1', 1);
        $pid->setValue('2', $arrayidcliente[0]['codigo']);
        //$pid->setValue('3.1', $arrayidcliente['codigo']);
        $pid->setValue('5.1', $arrayidcliente[0]['nombre']);
        $pid->setValue('5.2', $arrayidcliente[0]['nombre']);
//        $pid->setValue('10', 'N');
//        $pid->setValue('12', 'CUB');

        $pvv = $hl7->addSegment('PV1');
        $pvv->setValue('1', 1);

        $bandera = false;

        foreach ($arrayservicios as $key => $value) {
            $cont = 1;
            $aux = split('-', $value['idexterno']);
            if ($aux[1] == "OML") {
                $orc = $hl7->addSegment('ORC');
                $orc->setValue('1', 'AF');
                $orc->setValue('3.1', "");
                $orc->setValue('3.2', "");
                $orc->setValue('3.3', "");
                $orc->setValue('3.4', "ISO");
                $orc->setValue('4.1', "");
                $orc->setValue('4.2', "");
                $orc->setValue('4.3', "");
                $orc->setValue('4.4', "ISO");
                $orc->setValue('9', strftime("%Y%m%d%H%M%S"));
                $obr = $hl7->addSegment('OBR');
                $obr->setValue('1', $cont);
                $obr->setValue('2.1', "");
                $obr->setValue('2.2', "");
                $obr->setValue('2.3', "");
                $obr->setValue('2.4', "ISO");
                $obr->setValue('3.1', $aux[0]);
                $obr->setValue('3.2', "");
                $obr->setValue('3.3', "");
                $obr->setValue('3.4', "ISO");
                $obr->setValue('4.1', $value['codigo']);
                $obr->setValue('4.2', "");
                $obr->setValue('4.3', "");
                $obr->setValue('4.4', $cont);
                $obx = $hl7->addSegment('OBX');
                $obx->setValue('1', $cont);
                $obx->setValue('2', "");
                $obx->setValue('3.1', "");
                $obx->setValue('3.2', "");
                $obx->setValue('3.3', "");
                $obx->setValue('3.4', "ISO");
                $obx->setValue('3.1', "");
                $obx->setValue('5.1', "");
                $obx->setValue('5.2', "");
                $bandera = true;
                $cont++;
            }
        }
        if ($bandera == true) {
            $client = new HL7Client();
            $client->Connect();
            $res = $client->Send($hl7->getMessage());
            $client->Disconnect();
        }

        return 1;
    }

}
