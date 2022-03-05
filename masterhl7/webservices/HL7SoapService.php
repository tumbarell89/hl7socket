<?php

/**
 * Class HL7SoapService
 * Implementa los servicios soap del subistema CRM
 *
 * @author Alberto Mesa Martinez
 */
define('APP_HOME', realpath(dirname(__FILE__)) . '/');
class HL7SoapService  extends ZendExt_Model
{

    private $_modelFactura, $arrayData;

    function __construct(){
        parent::ZendExt_Model();
        $this->_modelFactura = new Propuesta();
        $this->arrayData = array('isPrincipal' => true, 'alias' => 'facturaLog', 'subsistema' =>
            LogisticaGlobal::SUBSISTEMA_FAC);
        
    }

    function adicionar($parametros){
        try{
            $parametros['connection'] = Doctrine_Manager::getInstance()->getCurrentConnection()->getName();
            $respuesta = $this->_modelFactura->adicionarFactura($parametros);
            return json_encode(array('success'=>true, 'msg'=>$respuesta));
        }catch (Exception $e){
            return json_encode(array('code'=>$e->getCode(), 'msg'=>$e->getMessage(), 'success'=>false));
        }
    }
    
    function eliminarFactura($parametros){
        try{
            $respuesta = $this->_modelFactura->eliminarServByIdexterno($parametros['idcliente'], $parametros['servicios']);
            return json_encode(array('success'=>true, 'msg'=>$respuesta));
        }catch (Exception $e){
            return json_encode(array('code'=>$e->getCode(), 'msg'=>$e->getMessage(), 'success'=>false));
        }
    }

    public function obtenerClientesProveedores($parametros){
        try {            
            $conn = Doctrine_Manager::connection();
            $sql = "SELECT idclientesproveedores
                           FROM mod_datosmaestros.nom_clientesproveedor where codigo ='{$parametros['pubpid']}';";
            //print_r($sql); die;
            $arrayResult = $conn->execute($sql)->fetchAll(PDO::FETCH_ASSOC);
            if($parametros['evt']=='A04'){
                if (count($arrayResult) == 0) {
                    $conn1 = Doctrine_Manager::connection();
                    $parametros['idtipo']=2;
                    $parametros['idClasificacion']=2;
                    $parametros['registrado']=0;
                    $sqlupd = "INSERT INTO mod_datosmaestros.nom_clientesproveedor(
                                    nombre, idtipo,codigo, idclasificacion, 
                                    registrado)
                            VALUES ('{$parametros['nombre']}', {$parametros['idtipo']}, '{$parametros['pubpid']}', {$parametros['idClasificacion']}, {$parametros['registrado']});";
                    //return $sqlupd;        
                    $conn1->execute($sqlupd);
                    $conn1->commit();
                    return 1;
                }else{
                    return $arrayResult;
                }
            }else if($parametros['evt']=='A08'){
                if (count($arrayResult) == 1) {
                    $conn1 = Doctrine_Manager::connection();
                    $parametros['idtipo']=2;
                    $parametros['idClasificacion']=2;
                    $parametros['registrado']=0;
                    $sqlupd = "UPDATE mod_datosmaestros.nom_clientesproveedor
                                SET nombre= '{$parametros['nombre']}', codigo= '{$parametros['pubpid']}'
                              WHERE codigo= '{$parametros['pubpid']}';";
                    //return $sqlupd;        
                    $conn1->execute($sqlupd);
                    $conn1->commit();
                    return $arrayResult;
                }else{
                    return 0;
                }
            }
            return $arrayResult;
//            $busqueda= $this->integrator->datosmaestros->getClientesProveedorByParams(
//                null, null, null, null, $parametros, null, null
//            );
//            if($busqueda['cantidad']==0){
//                $parametros['idtipo']=2;
//                $parametros['idClasificacion']=2;
//                $parametros['registrado']=0;
//                $result= $this->integrator->datosmaestros->adicionarClienteProveedor(
//                    $parametros
//                );
//                return json_encode($result);
//            }else{
//                return json_encode($busqueda);
//            }
        } catch (Exception $e) {
            return array();
        }
    }
    
    public function eliminarClienteProveedor($parametros){
        try {
            
            $conn = Doctrine_Manager::connection();
            $sql = "SELECT idclientesproveedores
                           FROM mod_datosmaestros.nom_clientesproveedor where codigo ='{$parametros['pubpid']}';";
            //print_r($sql); die;
            $arrayResult = $conn->execute($sql)->fetchAll(PDO::FETCH_ASSOC);
            if($parametros['evt']=='A04'){
                if (count($arrayResult) == 0) {
                    $conn1 = Doctrine_Manager::connection();
                    $parametros['idtipo']=2;
                    $parametros['idClasificacion']=2;
                    $parametros['registrado']=0;
                    $sqlupd = "INSERT INTO mod_datosmaestros.nom_clientesproveedor(
                                    nombre, idtipo,codigo, idclasificacion, 
                                    registrado)
                            VALUES ('{$parametros['nombre']}', {$parametros['idtipo']}, '{$parametros['pubpid']}', {$parametros['idClasificacion']}, {$parametros['registrado']});";
                    //return $sqlupd;        
                    $conn1->execute($sqlupd);
                    $conn1->commit();
                    return 1;
                }else{
                    return $arrayResult;
                }
            }else if($parametros['evt']=='A08'){
                if (count($arrayResult) == 1) {
                    $conn1 = Doctrine_Manager::connection();
                    $parametros['idtipo']=2;
                    $parametros['idClasificacion']=2;
                    $parametros['registrado']=0;
                    $sqlupd = "UPDATE mod_datosmaestros.nom_clientesproveedor
                                SET nombre= '{$parametros['nombre']}', codigo= '{$parametros['pubpid']}'
                              WHERE codigo= '{$parametros['pubpid']}';";
                    //return $sqlupd;        
                    $conn1->execute($sqlupd);
                    $conn1->commit();
                    return $arrayResult;
                }else{
                    return 0;
                }
            }
            return $arrayResult;
//            $busqueda= $this->integrator->datosmaestros->getClientesProveedorByParams(
//                null, null, null, null, $parametros, null, null
//            );
//            if($busqueda['cantidad']==0){
//                $parametros['idtipo']=2;
//                $parametros['idClasificacion']=2;
//                $parametros['registrado']=0;
//                $result= $this->integrator->datosmaestros->adicionarClienteProveedor(
//                    $parametros
//                );
//                return json_encode($result);
//            }else{
//                return json_encode($busqueda);
//            }
        } catch (Exception $e) {
            return array();
        }
//        $datosmaestros = new DatosMaestrosComunicationInterface();
//        $result = $datosmaestros->getClientesProveedores($parametros);
////        $respuesta=array('success' => 'true');
////        $respuesta=array_merge($result);
//        return json_encode($result);
    }

}
