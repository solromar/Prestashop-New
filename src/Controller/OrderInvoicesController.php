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

        // Realiza la solicitud para obtener la lista de pedidos
        $client = new Client();
        $response = $client->request('GET', $baseApiUrl, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($authorizationKey . ':'),
            ],
        ]);

        // Lee la respuesta XML de la API como una cadena
        $xmlResponse = $response->getBody()->getContents();
        $xml = simplexml_load_string($xmlResponse);

        $orders = [];

        foreach ($xml->orders->order as $order) {
            $orderId = (string)$order['id'];

            // Realiza la solicitud para obtener los detalles de cada pedido
            $responseOrder = $client->request('GET', $baseApiUrl . '/' . $orderId, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($authorizationKey . ':'),
                ],
            ]);

            // Lee la respuesta XML de la API para los detalles de cada pedido
            $orderDetails = $responseOrder->getBody()->getContents();
            $orderXml = simplexml_load_string($orderDetails);



            // Convierte los elementos XML en un arreglo
            $orderArray = [];
            foreach ($orderXml->order->children() as $element) {
                $keyName = $element->getName();

                // Define un arreglo con los campos que deseas mostrar y sus nombres personalizados
                $ordersEmmitted = [
                    "id" => "ID de Factura",
                    "invoice_number" => "Número de Factura",
                    "invoice_date" => "Fecha de factura",
                    "date_add" => "Fecha de Creación",
                    "date_upd" => "Fecha de Actualización",
                    "delivery_number" => "Número de Entrega",
                    "delivery_date" => "Fecha de Entrega",
                    "shipping_number" => "Número de Envío",
                    "id_shop" => "ID de Tienda",
                    "payment" => "Método de Pago",
                    "total_discounts" => "Descuentos Totales",
                    "total_discounts_tax_incl" => "Descuentos Totales (Impuestos Incluidos)",
                    "total_discounts_tax_excl" => "Descuentos Totales (Impuestos Excluidos)",
                    "total_paid" => "Total Pagado",
                    "total_paid_tax_incl" => "Total Pagado (Impuestos Incluidos)",
                    "total_paid_tax_excl" => "Total Pagado (Impuestos Excluidos)",
                    "total_products" => "Total de Productos",
                    "total_shipping" => "Costo de Envío",
                    "total_shipping_tax_incl" => "Costo de Envío (Impuestos Incluidos)",
                    "total_shipping_tax_excl" => "Costo de Envío (Impuestos Excluidos)",
                    "carrier_tax_rate" => "Tasa de Impuestos del Transportista",
                    "total_wrapping" => "Envoltura Total",
                    "total_wrapping_tax_incl" => "Envoltura Total (Impuestos Incluidos)",
                    "total_wrapping_tax_excl" => "Envoltura Total (Impuestos Excluidos)",
                    "conversion_rate" => "Tasa de Conversión",
                    

                ];
                
                // Verifica si la clave está en el arreglo de campos deseados y, si lo está, agrega al arreglo final
                if (isset($ordersEmmitted[$keyName])) {
                    $keyNameEs = $ordersEmmitted[$keyName];
                    $orderArray[$keyNameEs] = (string)$element;
                }
            }

            // Almacena los detalles en un arreglo junto con el ID
            $orders[$orderId] = $orderArray;
        }

        // Puedes devolver o usar los detalles de los pedidos como desees
        // Por ejemplo, devolverlos como JSON
        $response = new Response(json_encode($orders));
        $response->headers->set('Content-Type', 'application/json');



        return $response;
    }
}
