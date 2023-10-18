# Guia de Uso de la API de PRESTASHOP  :floppy_disk:
 

 ### Requisitos previos <br>
 **Clave de Autorización:** Nos la otorga el cliente desde el back de la tienda <br>
 ![Ejemplo](https://devdocs.prestashop-project.org/8/webservice/img/enable_webservice.png)

## Base URL :electric_plug:
La URL base para todas las solicitudes a la API de Contasimple es: https://api.contasimple.com/api<br>

 ### **Token de Acceso:**<br> 
 Se llama a la Api de Auth para obtener un token de acceso utilizando la clave de autorización para autenticarte en la API. <br>
 El "access_token" obtenido es válido durante un tiempo limitado (por defecto 1 hora). <br>
 Pasado este tiempo el token caduca pero es posible obtener un nuevo token realizando de nuevo el proceso de login. <br>


### **Facturas: <br>
Para obtener las facturas tanto emitidas como recibidas, hay que pasarle a la URL la version de la API (la actual es la 2) y el periodo que se quiere consultar, <br>
este ultimo dato es **OBLIGATORIO** y en formato YYYY-nT, donde n es el trimestre (1,2,3 y4) <br>

### **Link a la documentacion de la [API](https://api.contasimple.com/swagger/ui/index)<br>