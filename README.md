# masterhl7

1-Para su utilización en distra se debe descargar este repo dentro del directorio raiz apps del distra y en el ioc.xml (apps\comun\recursos\xml\ioc.xml)principal del distra agregar

<masterhl7 src="masterhl7/comun/recursos/xml/ioc.xml" />

descargar dentro del directorio raiz web/ el repositorio git masterhl7-web

2-Se debe configurar el xml config_gis.xml, con los parametros que el mismo contiene
los cuales seran utilizados 
3-Se debe configurar el archivo HL7Server.php parametro puerto para indicar porque puerto se estará ejecutando el socket para recibir la información del HIS  
(Estos dos pasos se debem sustituir por un archivo de configuración xml dentro de esta misma estructura)

Para su utilización en distra para enviar información al sistema HIS CIRA GARCIA se debe llamar al servicio DatosAXavia
el cual recibe un arreglo de datos del subsistema o módulo que desee enviar información, y un parámetro string referente al nombre del subsitema o módulo que envia la información,
el servicio returna 0 en caso de no enviarse la informacion y 1 si todo fue correcto.
ejemplo: $this->integrator->masterhl7->DatosAXavia(datos,subsistema);

ejemplo
datos: Array(
	nombre: 'Pedro',
	apellidos: 'Perez Perez',
	cargo: 'jefe de departamento',
	.
	.
	.
	.
	.
	
)
subsitema: 'pvd'

