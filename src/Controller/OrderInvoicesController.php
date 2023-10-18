<?php

namespace App\Controller;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderInvoicesController extends AbstractController
{
    #[Route('/order/invoices', name: 'app_order_invoices')]
    public function getOrders(Request $request)
    {
        $authorizationKey = 'NQ54AUNLK7F4TRDSLWRMA9WQ13WQK5RK';
        $baseApiUrl = 'https://tiendaprestashop.lndo.site/api/orders';

        //------------------------------------------------- 1- Realiza la solicitud para obtener todos los ID's de las facturas-------------------------------------------------//
        $client = new Client();
        $response = $client->request('GET', $baseApiUrl, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($authorizationKey . ':'),
            ],
        ]);
        // Lee la respuesta XML de la API como una cadena
        $xmlResponse = $response->getBody()->getContents();
        $xml = simplexml_load_string($xmlResponse);
        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------//
        //Array donde mostraremos la respuesta completa
        $responseData = [];

        // --------------------------------------------- 2-Realiza la solicitud para obtener los detalles de cada order --------------------------------------------------------//
        foreach ($xml->orders->order as $order) {
            $orderId = (string)$order['id'];
            $responseOrder = $client->request('GET', $baseApiUrl . '/' . $orderId, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($authorizationKey . ':'),
                ],
            ]);
            $orderDetails = $responseOrder->getBody()->getContents();
            $orderXml = simplexml_load_string($orderDetails);
            //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------//    

            // ---------------------------------------------- 3-Realiza la solicitud de los ID's de addresses para obtener el detalle de los Clientes -------------------------------//
            $idAddressInvoice = (string)$orderXml->order->id_address_invoice;
            $addressUrl = 'http://tiendaprestashop.lndo.site/api/addresses/' . $idAddressInvoice;
            $responseAddress = $client->request('GET', $addressUrl, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($authorizationKey . ':'),
                ],
            ]);
            $addressDetails = $responseAddress->getBody()->getContents();
            $addressXml = simplexml_load_string($addressDetails);

            //------------------------------------- Acceso a la estructura y asignacion de variables --------------------------------------------------------------------------------//
            $detalleData = [
                "ID Factura" => (string)$orderXml->order->id,                
                "Número de Factura" => (int)$orderXml->order->invoice_number,
                "Fecha de Factura" => (string)$orderXml->order->invoice_date,
                "Cliente" => [],
                "Número de Entrega" => (string)$orderXml->order->delivery_number,
                "Fecha de Entrega" => (string)$orderXml->order->delivery_date,
                "Estado de Validez" => (string)$orderXml->order->valid,
                "Fecha de Creación" => (string)$orderXml->order->date_add,
                "Fecha de Actualización" => (string)$orderXml->order->date_upd,
                "Número de Envío" => (string)$orderXml->order->shipping_number,
                "Nota" => (string)$orderXml->order->note,
                "ID de Grupo de Tienda" => (string)$orderXml->order->id_shop_group,
                "ID de Tienda" => (string)$orderXml->order->id_shop,
                "Clave Segura" => (string)$orderXml->order->secure_key,
                "Método de Pago" => (string)$orderXml->order->payment,
                "Reciclable" => (string)$orderXml->order->recyclable,
                "Regalo" => (string)$orderXml->order->gift,
                "Mensaje de Regalo" => (string)$orderXml->order->gift_message,
                "Tema Móvil" => (string)$orderXml->order->mobile_theme,
                "Descuentos Totales" => number_format((float)$orderXml->order->total_discount, 2, '.', ','),
                "Descuentos Totales (Impuestos Incluidos)" => number_format((float)$orderXml->order->total_discounts_tax_incl, 2, '.', ','),
                "Descuentos Totales (Impuestos Excluidos)" => number_format((float)$orderXml->order->total_discounts_tax_excl, 2, '.', ','),
                "Total Pagado" => number_format((float)$orderXml->order->total_paid, 2, '.', ','),
                "Total Pagado (Impuestos Incluidos)" => number_format((float)$orderXml->order->total_paid_tax_incl, 2, '.', ','),
                "Total Pagado (Impuestos Excluidos)" => number_format((float)$orderXml->order->total_paid_tax_excl, 2, '.', ','),
                "Total Pagado Real" => number_format((float)$orderXml->order->total_paid_real, 2, '.', ','),
                "Total de Productos" => number_format((float)$orderXml->order->total_products, 2, '.', ','),
                "Total de Productos (Impuestos Incluidos)" => number_format((float)$orderXml->order->total_products_wt, 2, '.', ','),
                "Costo de Envío" => number_format((float)$orderXml->order->total_shipping, 2, '.', ','),
                "Costo de Envío (Impuestos Incluidos)" => number_format((float)$orderXml->order->total_shipping_tax_incl, 2, '.', ','),
                "Costo de Envío (Impuestos Excluidos)" => number_format((float)$orderXml->order->total_shipping_tax_excl, 2, '.', ','),
                "Tasa de Impuestos del Transportista" => number_format((float)$orderXml->order->carrier_tax_rate, 2, '.', ','),
                "Envoltura Total" => number_format((float)$orderXml->order->total_wrapping, 2, '.', ','),
                "Envoltura Total (Impuestos Incluidos)" => number_format((float)$orderXml->order->total_wrapping_tax_incl, 2, '.', ','),
                "Envoltura Total (Impuestos Excluidos)" => number_format((float)$orderXml->order->total_wrapping_tax_excl, 2, '.', ','),
                "Modo Redondeo" => (string)$orderXml->order->round_mode,
                "Tipo de Redondeo" => (string)$orderXml->order->round_type,
                "Tasa de Conversión" => number_format((float)$orderXml->order->conversion_rate, 2, '.', ','),
                "Productos" => [],

            ];
            // Anidado en association
            if (isset($orderXml->order->associations->order_rows->order_row)) {
                foreach ($orderXml->order->associations->order_rows->order_row as $row) {
                    // Accede a los elementos dentro de "order_row" para obtener el detalle de los productos y agrega la información al arreglo
                    $rowData = [
                        "ID" => (string)$row->id,
                        "ID de Producto" => (string)$row->product_id,
                        "Cantidad del Producto" => (string)$row->product_quantity,
                        "Nombre del Producto" => (string)$row->product_name,
                        "Referencia del Producto" => (string)$row->product_reference,
                        "Precio del Producto" => number_format((float)$row->product_price, 2, '.', ','),
                        "Precio Unitario (Impuestos Incluidos)" => number_format((float)$row->unit_price_tax_incl, 2, '.', ','),
                        "Precio Unitario (Impuestos Excluidos)" => number_format((float)$row->unit_price_tax_excl, 2, '.', ','),
                    ];
                    $detalleData["Productos"][] = $rowData;
                }
            }
            //Datos del cliente
            $customerData = [
                "Nombre del Cliente" => (string)$addressXml->address->firstname,
                "Apellido del Cliente" => (string)$addressXml->address->lastname,
                "NIF" => (string)$addressXml->address->vat_number,
                "DNI" => (string)$addressXml->address->dni,
                "Dirección" => (string)$addressXml->address->address1,
                "Código Postal" => (string)$addressXml->address->postcode,
                "Ciudad" => (string)$addressXml->address->city,
                "Telefóno" => (string)$addressXml->address->phone,
                "Móvil" => (string)$addressXml->address->phone_mobile,
            ];
            $detalleData["Cliente"][] = $customerData;


            // Agrega los datos del detalle al arreglo de respuesta
            $responseData[] = $detalleData;
        }

        return new JsonResponse(["Facturas Emitidas" => $responseData]);
    }
}
