# Guia de Uso de la API de PRESTASHOP  :floppy_disk:
 

 ### Requisitos previos <br>
 **Habilitacion webServices:** Lo hace el cliente desde el back de la tienda, primero debe crear el acceso <br>
 ![Ejemplo](https://devdocs.prestashop-project.org/8/webservice/img/enable_webservice.png)<br>

 **Clave de acceso:** Luego para poder tener acceso a la API se debe crear una clave de acceso con todos los permisos habilitados <br>
 ![Ejemplo](https://devdocs.prestashop-project.org/8/webservice/img/create_access_key.png)<br>


## Pruebas :electric_plug:
Existen varias formas de acceder a la API:<br>
1-Si PrestaShop está instalado en la raíz del servidor, puedes acceder a la API aquí: http://nombreDeLaTienda.com/api/<br>
2-Incluir la clave en la URL en la raíz del servidor: https://UCCLLQ9N2ARSHWCXLT74KUKSSK34BFKX@example.com/api/<br>
3-Uso de un encabezado de autorización (*el cual es la forma recomendada y en la cual esta realizado*) la clave debe estar en base64_encode<br>
4-Con Postman, en BasicAuth pasando la clave como username<br>

 ### **Recursos:**<br> 
 En este Link se encuentran todos los recursos para llamar a la [API] (https://devdocs.prestashop-project.org/8/webservice/resources/) <br>


### **Resumen de como se obtienen las Facturas: <br>
En resumen, el código realiza una serie de llamadas a la API de PrestaShop para obtener información de facturas y clientes, procesa estos datos<br>
 y los organiza en una respuesta JSON estructurada para su uso posterior. Cada llamada a la API se realiza dentro de un bucle que recorre todas las<br>
facturas obtenidas en la primera llamada. <br>

### **Detalle: <br>
# **Primera Llamada a la API:**<br>
URL: https://tiendaprestashop.lndo.site/api/orders<br>
Propósito: Esta llamada obtiene una lista de IDs de facturas desde la API de PrestaShop.<br>
Datos Obtenidos: Se recibe un archivo XML que contiene una lista de IDs de facturas.<br>
Bucle para Detalles de Facturas:<br>
Propósito: Un bucle recorre cada factura en la lista obtenida anteriormente y realiza solicitudes adicionales para obtener detalles específicos de cada factura.<br>


# **Segunda Llamada a la API (Dentro del bucle):**<br>
URL: https://tiendaprestashop.lndo.site/api/orders/{ID_FACTURA}<br>
Propósito: Esta llamada obtiene detalles específicos de una factura individual, como fechas, métodos de pago y más.<br>
Datos Obtenidos: Un archivo XML que contiene detalles de una factura en particular.<br>

# **Tercera Llamada a la API (Dentro del bucle):**<br>
URL: http://tiendaprestashop.lndo.site/api/addresses/{ID_ADDRESS}<br>
Propósito: Esta llamada obtiene detalles de la dirección del cliente asociada a la factura.<br>
Datos Obtenidos: Un archivo XML con detalles de la dirección del cliente, que incluye información como nombre, dirección, código postal, etc.<br>

# **Procesamiento de Datos (Dentro del bucle):**<br>
Propósito: Se accede a los archivos XML recibidos de las llamadas a la API para extraer información específica.<br>
Datos Obtenidos: Se extraen datos como el ID de la factura, fechas, información de cliente, detalles de productos y más.<br>

# **Construcción de Arreglos de Datos (Dentro del bucle):**<br>
Propósito: Se estructuran y organizan los datos obtenidos en un arreglo multidimensional llamado $responseData.<br>
Datos Obtenidos: El arreglo $responseData se llena con información detallada de cada factura, incluyendo información del cliente y detalles de productos.<br>

# **Respuesta JSON:**<br>
Propósito: Una vez que se han procesado todos los detalles de las facturas y los clientes, se genera una respuesta JSON con la información recopilada.<br>
Datos Obtenidos: Se devuelve un objeto JSON que contiene todos los datos procesados, organizados bajo la clave "Facturas Emitidas".<br>


### **Link a la documentacion de la [ApiPrestashop](https://api.contasimple.com/swagger/ui/index)<br>