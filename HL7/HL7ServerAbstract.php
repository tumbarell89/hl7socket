<?php

/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 8/5/14
 * Time: 6:48 PM
 */
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;

//include '/HL7.php';
require (str_replace('\\', '/', dirname(__FILE__)) . '/HL7.php');

//require ('A:/Trabajo/sisplan-uim.prod.xetid.cu/htdocs/sige/apps/configuracion/clientesyproveedores/services/ClientesyproveedoresServices.php');
//require (str_replace('\\', '/', dirname(__FILE__)) . '../../configuracion/clientesyproveedores/services/ClientesyproveedoresServices.php');


class HL7ServerAbstract implements MessageComponentInterface {

    protected $clients;
    private $class;
    private $method;
    private $port;
    private $site;
    private $token;
    private $dirsoap;

    public function __construct() {
        global $class, $method, $port, $site, $token, $dirsoap;


        $t->port = $port;
        $t->site = $site;
        $t->class = $class;
        $t->method = $method;
        $t->token = $token;

        $configFile = (str_replace('\\', '/', dirname(__FILE__)) . '/../comun/recursos/xml/config_.xml');
        if (file_exists($configFile)) {
            $xmlCargado = simplexml_load_file($configFile);
            $t->dirsoap = ((string) $xmlCargado->config['dirsoap']);
        }


        //TODO hard coded for now
        date_default_timezone_set('America/Puerto_Rico');
        $t->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $conn->handler = new $t->class($t->port, $t->site);
        $t->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $conn, $message, IoServer $server) {
        try {

            if ($message == '') {
                $conn->send('');
            }if ($message == $t->token) {
                $server->loop->stop();
                $server->socket->shutdown();
                die();
            }
            print('1 \n');
            $ack = call_user_func(array($conn->handler, $t->method), $message);
            $conn->send($ack);
        } catch (\Exception $e) {
            error_log($e->getMessage(), 3, 'server_error.log');
        }
    }

    public function onClose(ConnectionInterface $conn) {
        unset($conn->handler);
        $t->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        unset($conn->handler);
        $conn->close();
    }

    public function Process($msg = '', $addSocketCharacters = true) {
        //		try{
        $t->msg = $msg;

        $t->ackStatus = 'AA';
        $t->ackMessage = '';

        /**
         * Parse the HL7 Message
         */
        $hl7 = new HL7();
        $msg = $hl7->readMessage($t->msg);

        $application = $hl7->getSendingApplication();
        $facility = $hl7->getSendingFacility();
        $version = $hl7->getMsgVersionId();

        /**
         * check HL7 version
         */
//		if($version != '2.5.1'){
//			$t->ackStatus = 'AR';
//			$t->ackMessage = 'HL7 version unsupported';
//		}
        /**
         * Check for IP address access
         */
        //$t->recipient = $t->r->load(array('application_name' => $application))->one();
        $t->recipient = true;
        if ($t->recipient == false) {
            $t->ackStatus = 'AR';
            $t->ackMessage = "T application '$application' Not Authorized";
        }
        /**
         *
         */
        if ($msg == false) {
            $t->ackStatus = 'AE';
            $t->ackMessage = 'Unable to parse HL7 message, please contact Support Desk';
        }
        /**
         *
         */
        $msgRecord = new stdClass();
        $msgRecord->msg_type = $hl7->getMsgType();
        $msgRecord->message = $t->msg;
        $msgRecord->foreign_facility = $hl7->getSendingFacility();
        $msgRecord->foreign_application = $hl7->getSendingApplication();
        $msgRecord->foreign_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $msgRecord->isOutbound = '0';
        $msgRecord->status = '2';
        $msgRecord->date_processed = date('Y-m-d H:i:s');
//		$msgRecord = $t->m->save($msgRecord);
//		$msgRecord = (array)$msgRecord['data'];
//
        if ($t->ackStatus == 'AA') {
            /**
             *
             */
            print($t->msg);
            //print($hl7->getMsgType());
            switch ($hl7->getMsgType()) {
                case 'ADT':
                    print('2');
                    $t->ProcessADT($hl7, $msg, $msgRecord);
                    break;
                case 'SIU':
                    print('3');
                    $t->ProcessSIU($hl7, $msg, $msgRecord);
                    break;
                case 'ORM':
                    print('4');
                    $t->ProcessORM($hl7, $msg, $msgRecord);
                    break;
                case 'OMI':
                    print('1');
                    $t->ProcessOMI($hl7, $msg, $msgRecord);
                    break;
                case 'OML':
                    print('1');
                    $t->ProcessOML($hl7, $msg, $msgRecord);
                    break;
                default:

                    break;
            }
        }

        /**
         *
         */
        $ack = new HL7();
        $msh = $ack->addSegment('MSH');
        $msh->setValue('3.1', ''); // Sending Application
        $msh->setValue('4.1', ''); // Sending Facility
        $msh->setValue('5.1', ''); // Sending Facility
        $msh->setValue('6.1', ''); // Sending Facility
        $msh->setValue('7.1', strftime("%Y%m%d%H%M%S")); // Receiving Facility
        $msh->setValue('9.1', 'ACK');
        $msh->setValue('11.1', 'P'); // P = Production
        $msh->setValue('12.1', '2.5.1'); // HL7 version
        $msa = $ack->addSegment('MSA');
        $msa->setValue('1', $t->ackStatus); // AA = Positive acknowledgment, AE = Application error, AR = Application reject
        $msa->setValue('2', $hl7->getMsgControlId()); // Message Control ID from MSH
        $msa->setValue('3', $t->ackMessage); // Error Message
        $ackMsg = $ack->getMessage();

        //$msgRecord['response'] = $ackMsg;
        //$t->m->save((object)$msgRecord);
        // unset all the variables to release memory
        //unset($ack, $hl7, $msg, $msgRecord, $oData, $result);

        return $addSocketCharacters ? "\v" . $ackMsg . chr(0x1c) . chr(0x0d) : $ackMsg;
    }

    /**
     * @param $data
     *
     * @return bool
     */
    private function notEmpty($data) {
        return isset($data) && ($data != '' && $data != '""' && $data != '\'\'');
    }

    protected function ProcessADT($hl7, $msg, $msgRecord) {

        $evt = $hl7->getMsgEventType();
        $r = array();

        $contextOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
        ));

        $sslContext = stream_context_create($contextOptions);

        $params11 = array(
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'stream_context' => $sslContext
        );

        $clientSoap = new SoapClient("{$t->dirsoap}" . "/masterhl7/webservices/server.wsdl.php", $params11);

        if ($evt == 'A04') {
            /**
             * Register a Patient
             */
            $patientData = $t->PidToPatient($msg->data['PID'], $hl7);
            $patientData['evt'] = 'A04';

            $r = $clientSoap->obtenerClientesProveedores($patientData);

            if (count($r) == 0) {
                $t->ackStatus = 'AR';
                $t->ackMessage = 'Problemas para crear pacienta en  contacte al administrador ';
            } else {
                $t->ackStatus = 'AA';
                $t->ackMessage = 'Paciente creado como cliente satisfacoriamente ';
            }
            print_r($r);
//                      
            return;
        } elseif ($evt == 'A08') {
            /**
             * Update Patient Information
             */
            $patientData = $t->PidToPatient($msg->data['PID'], $hl7);

            $patientData['evt'] = 'A08';

            $r = $clientSoap->obtenerClientesProveedores($patientData);

            if (count($r) == 0) {
                $t->ackStatus = 'AR';
                $t->ackMessage = 'Paciente no encontrado como cliente en ';
            } else {
                $t->ackStatus = 'AA';
                $t->ackMessage = 'Paciente actualizado como cliente satisfacoriamente ';
            }
            print_r($r);
            return;
        }

        /**
         * Un handle event error
         */
        $t->ackStatus = 'AR';
        $t->ackMessage = 'No se reconoce el tipo mensaje ADT_' . $evt;
    }

    protected function ProcessSIU($hl7, $msg, $msgRecord) {

        $evt = $hl7->getMsgEventType();
        $r2 = array();
        $contextOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
        ));

        $sslContext = stream_context_create($contextOptions);

        $params11 = array(
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'stream_context' => $sslContext
        );

        $clientSoap = new SoapClient("{$t->dirsoap}" . "/masterhl7/webservices/server.wsdl.php", $params11);


        if ($evt == 'S12') {
            /**
             * crear factura
             */
            $siu = new SIU($hl7);

            $patientData = $t->PidToPatient($siu->hl7->segments[2]->data, $hl7);

            $r = $clientSoap->obtenerClientesProveedores($patientData);

            $servicioData = $t->SCHToServicio($siu->hl7->segments[1]->data, $hl7);

            $param = array(
                'codigo' => 0,
                'tipoDoc' => 2,
                'idcliente' => $r[0]['idclientesproveedores'],
                'idsubsistema' => -1,
                'idoperacion' => 11,
                'idestructuracomun' => 444,
                'servicios' => array(
                    $servicioData
                )
            );
            print_r($param);
            //ULTIM0 ERROR {"code":0,"msg":"sequence sec_datdocumentocliente does not exist","success":false}
            $r2 = $clientSoap->adicionar($param);
            print_r($r2);
//                      
            return;
        } elseif ($evt == 'S14') {
            /**
             * MOdificar factura
             */
            $siu = new SIU($hl7);

            $patientData = $t->PidToPatient($siu->hl7->segments[2]->data, $hl7);

            $r = $clientSoap->obtenerClientesProveedores($patientData);

            $servicioData = $t->SCHToServicio($siu->hl7->segments[1]->data, $hl7);

            $param = array(
                'codigo' => 0,
                'tipoDoc' => 2,
                'idcliente' => $r[0]['idclientesproveedores'],
                'idsubsistema' => -1,
                'idoperacion' => 11,
                'idestructuracomun' => 444,
                'servicios' => array(
                    $servicioData
                )
            );
            //ULTIM0 ERROR {"code":0,"msg":"sequence sec_datdocumentocliente does not exist","success":false}
            $r2 = $clientSoap->adicionar($param);
            print_r($r2);
//                      
            return;
        } elseif ($evt == 'S17') {
            /**
             * MOdificar factura
             */
            $siu = new SIU($hl7);

            $patientData = $t->PidToPatient($siu->hl7->segments[2]->data, $hl7);

            $r = $clientSoap->obtenerClientesProveedores($patientData);
            if (count($r) == 0) {
                $t->ackStatus = 'AR';
                $t->ackMessage = 'Paciente no encontrado ';
                return;
            } else {

                $servicioData = $t->SCHToServicio($siu->hl7->segments[1]->data, $hl7);

                $param = array(
                    'idcliente' => $r[0]['idclientesproveedores'],
                    'servicios' => $servicioData['idexterno']
                );
                //ULTIM0 ERROR {"code":0,"msg":"sequence sec_datdocumentocliente does not exist","success":false}
                $r2 = $clientSoap->eliminarFactura($param);
                print_r($r2);
            }
            return;
        }

        /**
         * Un handle event error
         */
        $t->ackStatus = 'AR';
        $t->ackMessage = 'No se reconoce el tipo mensaje SIU_' . $evt;
        return;
    }

    protected function ProcessORM($hl7, $msg, $msgRecord) {

        $evt = $hl7->getMsgEventType();
        $r2 = array();
        print_r($evt);
        $contextOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
        ));

        $sslContext = stream_context_create($contextOptions);

        $params11 = array(
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'stream_context' => $sslContext
        );

        $clientSoap = new SoapClient("{$t->dirsoap}" . "/masterhl7/webservices/server.wsdl.php", $params11);

        if ($evt == 'O01') {
            /**
             * crear factura
             */
            $orm = new ORM($hl7);
            $servicios = array();
            $serviciosEliminar = array();
            $patientData = $t->PidToPatient($orm->hl7->segments[2]->data, $hl7);
            $r = $clientSoap->obtenerClientesProveedores($patientData);
            if (count($r) == 0) {
                $t->ackStatus = 'AR';
                $t->ackMessage = 'Paciente no encontrado ';
                return;
            } else {
                $cont = 0;
                $cont2 = 0;
                foreach ($orm->hl7->segments as $key => $seg) {
                    if ($seg->data[0] == 'OBR') {
                        //print_r($orm->hl7->segments[$key-1]); die;
                        $orc = $t->ORCToServicio($orm->hl7->segments[$key - 1]->data, $hl7);
                        if ($orc['op'] == 'OC' || $orc['op'] == 'CA' || $orc['op'] == 'XO') {
                            $serviciosEliminar[$cont2] = $t->OBRToServicio($seg->data, $hl7);
                            $serviciosEliminar[$cont2]['fecha'] = $orc['fecha'];
                            $serviciosEliminar[$cont]['idexterno'] = $serviciosEliminar[$cont]['codigo'] . '-' . $hl7->getMsgType().'-'.$orc['tiposervicio'];
                            $cont2++;
                        } else {
                            $servicios[$cont] = $t->OBRToServicio($seg->data, $hl7);
                            $servicios[$cont]['fecha'] = $orc['fecha'];
                            $servicios[$cont]['idexterno'] = $servicios[$cont]['codigo'] . '-' . $hl7->getMsgType().'-'.$orc['tiposervicio'];
                            $cont++;
                        }
                    }
                }
                print_r($servicios);
                print_r($serviciosEliminar);
                if (count($servicios) > 0) {
                    $param = array(
                        'codigo' => 0,
                        'tipoDoc' => 2,
                        'idcliente' => $r[0]['idclientesproveedores'],
                        'idsubsistema' => -1,
                        'idoperacion' => 11,
                        'idestructuracomun' => 444,
                        'servicios' => array(
                            $servicios
                        )
                    );
                    //ULTIM0 ERROR {"code":0,"msg":"sequence sec_datdocumentocliente does not exist","success":false}
                    $r2 = $clientSoap->adicionar($param);
                    print_r($r2);
//                                if($r2['success'] == false){
//
//                                    $t->ackStatus = 'AR';
//                                    $t->ackMessage = 'Contartar con servcio administracion Distra, error: '. $r2['msg'];
//                                    return;
//                                }
                }
                if (count($serviciosEliminar) > 0) {
                    foreach ($serviciosEliminar as $value) {
                        $param = array(
                            'idcliente' => $r[0]['idclientesproveedores'],
                            'servicios' => $value['idexterno']
                        );
                        $r2 = $clientSoap->eliminarFactura($param);
                        print_r($r2);
//                                    if($r2['success'] == false){
//                                        $t->ackStatus = 'AA';
//                                        $t->ackMessage = 'Eliminado servicio correctamente;
//                                        return;
//                                    }
                    }
                }
            }
//                      
            return;
        }

        /**
         * Un handle event error
         */
        $t->ackStatus = 'AR';
        $t->ackMessage = 'No se reconoce el tipo mensaje ORM_' . $evt;
    }

    protected function ProcessOMI($hl7, $msg, $msgRecord) {

        $evt = $hl7->getMsgEventType();
        $r2 = array();
        print_r($evt);
        $contextOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
        ));

        $sslContext = stream_context_create($contextOptions);

        $params11 = array(
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'stream_context' => $sslContext
        );

        $clientSoap = new SoapClient("{$t->dirsoap}" . "/masterhl7/webservices/server.wsdl.php", $params11);

        if ($evt == 'O23') {
            /**
             * crear factura
             */
            $omi = new OMI($hl7);
            $servicios = array();
            $serviciosEliminar = array();
            $patientData = $t->PidToPatient($omi->hl7->segments[2]->data, $hl7);
            $r = $clientSoap->obtenerClientesProveedores($patientData);
            if (count($r) == 0) {
                $t->ackStatus = 'AR';
                $t->ackMessage = 'Paciente no encontrado ';
                return;
            } else {
                $cont = 0;
                $cont2 = 0;
                foreach ($omi->hl7->segments as $key => $seg) {
                    if ($seg->data[0] == 'OBR') {
                        //print_r($orm->hl7->segments[$key-1]); die;
                        $orc = $t->ORCToServicio($omi->hl7->segments[$key - 1]->data, $hl7);
                        if ($orc['op'] == 'OC' || $orc['op'] == 'CA' || $orc['op'] == 'XO') {
                            $serviciosEliminar[$cont2] = $t->OBRToServicio($seg->data, $hl7);
                            $serviciosEliminar[$cont2]['fecha'] = $orc['fecha'];
                            $serviciosEliminar[$cont]['idexterno'] = $serviciosEliminar[$cont]['codigo'] . '-' . $hl7->getMsgType().'-'.$orc['tiposervicio'];
                            $cont2++;
                        } else {
                            $servicios[$cont] = $t->OBRToServicio($seg->data, $hl7);
                            $servicios[$cont]['fecha'] = $orc['fecha'];
                            $servicios[$cont]['idexterno'] = $servicios[$cont]['codigo'] . '-' . $hl7->getMsgType().'-'.$orc['tiposervicio'];
                            $cont++;
                        }
                    }
                }
                print_r($servicios);
                print_r($serviciosEliminar);
                if (count($servicios) > 0) {
                    $param = array(
                        'codigo' => 0,
                        'tipoDoc' => 2,
                        'idcliente' => $r[0]['idclientesproveedores'],
                        'idsubsistema' => -1,
                        'idoperacion' => 11,
                        'idestructuracomun' => 444,
                        'servicios' => array(
                            $servicios
                        )
                    );
                    //ULTIM0 ERROR {"code":0,"msg":"sequence sec_datdocumentocliente does not exist","success":false}
                    $r2 = $clientSoap->adicionar($param);
                    print_r($r2);
//                                if($r2['success'] == false){
//
//                                    $t->ackStatus = 'AR';
//                                    $t->ackMessage = 'Contartar con servcio administracion Distra, error: '. $r2['msg'];
//                                    return;
//                                }
                }
                if (count($serviciosEliminar) > 0) {
                    foreach ($serviciosEliminar as $value) {
                        $param = array(
                            'idcliente' => $r[0]['idclientesproveedores'],
                            'servicios' => $value['idexterno']
                        );
                        $r2 = $clientSoap->eliminarFactura($param);
                        print_r($r2);
//                                    if($r2['success'] == false){
//
//                                        $t->ackStatus = 'AR';
//                                        $t->ackMessage = 'Contartar con servcio administracion Distra, error: '. $r2['msg'];
//                                        return;
//                                    }
                    }
                }

//                            if($r2['success'] == true){
//                                $t->ackStatus = 'AA';
//                                $t->ackMessage = 'Creada la factura satisfactoriamente ';
//                                return;
//                            }else{
//                                $t->ackStatus = 'AR';
//                                $t->ackMessage = 'Contartar con servcio administracion Distra, error: '. $r2['msg'];
//                                return;
//                            }
            }
//                      
            return;
        }

        /**
         * Un handle event error
         */
        $t->ackStatus = 'AR';
        $t->ackMessage = 'No se reconoce el tipo mensaje OMI_' . $evt;
    }

    protected function ProcessOML($hl7, $msg, $msgRecord) {

        $evt = $hl7->getMsgEventType();
        print_r($evt);
        $contextOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
        ));

        $sslContext = stream_context_create($contextOptions);

        $params11 = array(
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'stream_context' => $sslContext
        );

        $clientSoap = new SoapClient("{$t->dirsoap}" . "/masterhl7/webservices/server.wsdl.php", $params11);

        if ($evt == 'O21') {
            /**
             * crear factura
             */
            $oml = new OML($hl7);
            $servicios = array();
            $serviciosEliminar = array();
            $patientData = $t->PidToPatient($oml->hl7->segments[2]->data, $hl7);
            $r = $clientSoap->obtenerClientesProveedores($patientData);
            if (count($r) == 0) {
                $t->ackStatus = 'AR';
                $t->ackMessage = 'Paciente no encontrado ';
                return;
            } else {
                $cont = 0;
                $cont2 = 0;
                foreach ($oml->hl7->segments as $key => $seg) {
                    if ($seg->data[0] == 'OBR') {
                        //print_r($orm->hl7->segments[$key-1]); die;
                        $orc = $t->ORCToServicio($oml->hl7->segments[$key - 1]->data, $hl7);
                        if ($orc['op'] == 'OC' || $orc['op'] == 'CA' || $orc['op'] == 'XO') {
                            $serviciosEliminar[$cont2] = $t->OBRToServicio($seg->data, $hl7);
                            $serviciosEliminar[$cont2]['fecha'] = $orc['fecha'];
                            $serviciosEliminar[$cont]['idexterno'] = $serviciosEliminar[$cont]['codigo'] . '-' . $hl7->getMsgType().'-'.$orc['tiposervicio'];
                            $cont2++;
                        } else {
                            $servicios[$cont] = $t->OBRToServicio($seg->data, $hl7);
                            $servicios[$cont]['fecha'] = $orc['fecha'];
                            $servicios[$cont]['idexterno'] = $servicios[$cont]['codigo'] . '-' . $hl7->getMsgType().'-'.$orc['tiposervicio'];
                            $cont++;
                        }
                    }
                }
                print_r($servicios);
                print_r($serviciosEliminar);
                if (count($servicios) > 0) {
                    $param = array(
                        'codigo' => 0,
                        'tipoDoc' => 2,
                        'idcliente' => $r[0]['idclientesproveedores'],
                        'idsubsistema' => -1,
                        'idoperacion' => 11,
                        'idestructuracomun' => 444,
                        'servicios' => array(
                            $servicios
                        )
                    );
                    //ULTIM0 ERROR {"code":0,"msg":"sequence sec_datdocumentocliente does not exist","success":false}
                    $r2 = $clientSoap->adicionar($param);
                    print_r($r2);
                    if ($r2['success'] == false) {

                        $t->ackStatus = 'AR';
                        $t->ackMessage = 'Contartar con servcio administracion Distra, error: ' . $r2['msg'];
                        return;
                    }
                }
                if (count($serviciosEliminar) > 0) {
                    foreach ($serviciosEliminar as $value) {
                        $param = array(
                            'idcliente' => $r[0]['idclientesproveedores'],
                            'servicios' => $value['idexterno']
                        );
                        $r2 = $clientSoap->eliminarFactura($param);
                        print_r($r2);
                        
                    }
                }

            }
//                      
            return;
        }

        /**
         * Un handle event error
         */
        $t->ackStatus = 'AR';
        $t->ackMessage = 'No se reconoce el tipo mensaje OML_' . $evt;
    }

    protected function PidToPatient($PID, $hl7) {
        $p = array();
        //var_dump($PID);
        if ($t->notEmpty($PID[2][1]))
            $p['pubpid'] = $PID[2][1]; // Patient ID (External ID)

        if ($t->notEmpty($PID[3][0][1]))
            $p['pid'] = $PID[3][0][1]; // Patient ID (Internal ID)

        if ($t->notEmpty($PID[5][0][2]))
            $p['fname'] = $PID[5][0][2]; // Patient Name...

        if ($t->notEmpty($PID[5][0][3]))
            $p['mname'] = $PID[5][0][3]; //

        if ($t->notEmpty($PID[5][0][1][1]))
            $p['lname'] = $PID[5][0][1][1]; //

        if ($t->notEmpty($PID[6][0][3]))
            $p['mothers_name'] = "{$PID[6][0][2]} {$PID[6][0][3]} {$PID[6][0][1][1]}"; // Motherâ€™s Maiden Name

        if ($t->notEmpty($PID[6][0][3]) || $t->notEmpty($PID[5][0][1][1]) || $t->notEmpty($PID[5][0][2]))
            $p['nombre'] = "{$PID[5][0][2]}" . ' ' . "{$PID[5][0][1][1]}" . ' ' . "{$PID[6][0][3]}"; // Motherâ€™s Maiden Name

        if ($t->notEmpty($PID[7][1]))
            $p['DOB'] = $hl7->time($PID[7][1]); // Date/Time of Birth

        if ($t->notEmpty($PID[8]))
            $p['sex'] = $PID[8]; // Sex

        if ($t->notEmpty($PID[9][0][3]))
            $p['alias'] = "{$PID[9][0][2]} {$PID[9][0][3]} {$PID[9][0][1][1]}"; // Patient Alias

        if ($t->notEmpty($PID[10][0][1]))
            $p['race'] = $PID[10][0][1]; // Race

        if ($t->notEmpty($PID[11][0][1][1]))
            $p['address'] = $PID[11][0][1][1]; // Patient Address

        if ($t->notEmpty($PID[11][0][3]))
            $p['city'] = $PID[11][0][3]; //

        if ($t->notEmpty($PID[11][0][4]))
            $p['state'] = $PID[11][0][4]; //

        if ($t->notEmpty($PID[11][0][5]))
            $p['zipcode'] = $PID[11][0][5]; //

        if ($t->notEmpty($PID[11][0][6]))
            $p['country'] = $PID[11][0][6]; // Country Code

        if ($t->notEmpty($PID[13][0][7]))
            $p['home_phone'] = "{$PID[13][0][7]} . '-' . {$PID[13][0][1]}"; // Phone Number â€“ Home

        if ($t->notEmpty($PID[14][0][7]))
            $p['work_phone'] = "{$PID[14][0][7]} . '-' . {$PID[14][0][1]}"; // Phone Number â€“ Business

        if ($t->notEmpty($PID[15][1]))
            $p['language'] = $PID[15][1]; // Primary Language

        if ($t->notEmpty($PID[16][1]))
            $p['marital_status'] = $PID[16][1]; // Marital Status

            
//if($t->notEmpty($PID[17]))
        //  $p['00'] = $PID[17]; // Religion

        if ($t->notEmpty($PID[18][1]))
            $p['pubaccount'] = $PID[18][1]; // Patient Account Number

        if ($t->notEmpty($PID[19]))
            $p['SS'] = $PID[19]; // SSN Number â€“ Patient

        if ($t->notEmpty($PID[19]))
            $p['codigo'] = $PID[19]; // SSN Number â€“ Patient

        if ($t->notEmpty($PID[20][1]))
            $p['drivers_license'] = $PID[20][1]; // Driverâ€™s License Number - Patient

        if ($t->notEmpty($PID[20][2]))
            $p['drivers_license_state'] = $PID[20][2]; // Driverâ€™s License State - Patient

        if ($t->notEmpty($PID[20][3]))
            $p['drivers_license_exp'] = $PID[20][3]; // Driverâ€™s License Exp Date - Patient

            
//if($t->notEmpty($PID[21]))
        //  $p['00'] = $PID[21]; // Motherâ€™s Identifier

        if ($t->notEmpty($PID[22][0][1]))
            $p['ethnicity'] = $PID[22][0][1]; // Ethnic Group

        if ($t->notEmpty($PID[23]))
            $p['birth_place'] = $PID[23]; // Birth Place

        if ($t->notEmpty($PID[24]))
            $p['birth_multiple'] = $PID[24]; // Multiple Birth Indicator

        if ($t->notEmpty($PID[25]))
            $p['birth_order'] = $PID[25]; // Birth Order

        if ($t->notEmpty($PID[26][0][1]))
            $p['citizenship'] = $PID[26][0][1]; // Citizenship

        if ($t->notEmpty($PID[27][1]))
            $p['is_veteran'] = $PID[27][1]; // Veterans Military Status

        if ($t->notEmpty($PID[27][1]))
            $p['death_date'] = $PID[29][1]; // Patient Death Date and Time

        if ($t->notEmpty($PID[30]))
            $p['deceased'] = $PID[30]; // Patient Death Indicator

        if ($t->notEmpty($PID[33][1]))
            $p['update_date'] = $hl7->time($PID[33][1]); // Last update time stamp

        return $p;
    }

    protected function SCHToServicio($SCH, $hl7) {
        $p = array();
        //var_dump($PID);
        if ($t->notEmpty($SCH[1][0]))
            $p['pubpid'] = $SCH[1][0]; // ID (External ID)

        if ($t->notEmpty($SCH[7][1]))
            $p['idservicio'] = $SCH[7][1];

        if ($t->notEmpty($SCH[7][1]))
            $p['codigo'] = $SCH[7][1];

        if ($t->notEmpty($SCH[7][2]))
            $p['servicio'] = utf8_encode($SCH[7][2]); //

        if ($t->notEmpty($SCH[7][3]))
            $p['codesystem'] = $SCH[7][3]; // 

        if ($t->notEmpty($SCH[8][0]))
            $p['tipodeservicio'] = $SCH[8][0]; // 

        if ($t->notEmpty($SCH[11][0][4][0]))
            $p['fecha'] = $hl7->time($SCH[11][0][4][0]); //

        if ($t->notEmpty($SCH[1][0]))
            $p['idexterno'] = $SCH[1][0] . '-' . $hl7->getMsgType().'-'.$SCH[8][0]; // ID (External ID)
        
        $p['precio'] = 1; //
        $p['cantidad'] = 1; //
        return $p;
    }

    protected function OBRToServicio($OBR, $hl7) {
        $p = array();
        //var_dump($PID);
        if ($t->notEmpty($OBR[1]))
            $p['pubpid'] = $OBR[1]; // ID (External ID)

        if ($t->notEmpty($OBR[4][1]))
            $p['idservicio'] = $OBR[4][1];

        if ($t->notEmpty($OBR[4][1]))
            $p['codigo'] = $OBR[4][1];

        if ($t->notEmpty($OBR[4][2]))
            $p['servicio'] = utf8_encode($OBR[4][2]); //

        if ($t->notEmpty($OBR[4][3]))
            $p['codesystem'] = $OBR[4][3]; // 

        $p['precio'] = 1; //
        $p['cantidad'] = 1; //
        return $p;
    }

    protected function ORCToServicio($ORC, $hl7) {
        $p = array();
        //var_dump($PID);
        if ($t->notEmpty($ORC[1]))
            $p['op'] = $hl7->time($ORC[1]); // .

        if ($t->notEmpty($ORC[3][1]))
            $p['idexterno'] = $ORC[3][1]; // ID (External ID

        if ($t->notEmpty($ORC[9][0]))
            $p['fecha'] = $hl7->time($ORC[9][0]); //
        
        if ($t->notEmpty($ORC[29]))
            $p['tipodeservicio'] = $ORC[29]; // 

        return $p;
    }

}
