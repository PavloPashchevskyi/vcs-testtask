<?php

namespace Application\Modules\Hac\Controllers;

use Application\Core\Controller;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Description of GrabberController
 *
 * @author Pavlo
 */
class SenderController extends Controller
{
    private const BASE_HREF = 'https://finance.yahoo.com';
    private const URL = self::BASE_HREF.'/quote/TQQQ/history';
    private const URL_WITH_PARAMS = self::URL.'?p=TQQQ&.tsrc=fin-srch';

    public function indexAction()
    {
        $data = $this->parse();

        // output color name
        echo $data."<br/>";
        
        // send color name by email
        $options = \Application\Config\Options::get();
        $emailOptions = $options['email'];
        // send via Unirest
        \Unirest\Request::verifyPeer(false);
        /** @var \Unirest\Response $response */
        $response = \Unirest\Request::post(
                "https://rapidprod-sendgrid-v1.p.rapidapi.com/mail/send",
                [
                    "X-RapidAPI-Host" => "rapidprod-sendgrid-v1.p.rapidapi.com",
                    "X-RapidAPI-Key" => $emailOptions['sendgrid_api_key']/*"ec4f9e536emsh66266cb488b832ep121a24jsn334107e08a90"*/,
                    "Content-Type" => "application/json",
                    "Accept" => "application/json"
                ],
                [
                    "{\"personalizations\":[{\"to\":[{\"email\":\"".$emailOptions['to']."\"}],\"subject\":\"".$emailOptions['subject']."\"}],\"from\":{\"email\":\"".$emailOptions['from']."\"},\"content\":[{\"type\":\"text/plain\",\"value\":\"".$data."\"}]}"
                ]
            );
            exit(json_encode([
                'response' => true,
                'code' => 0,
                'status' => 'success',
                'message' => $response->raw_body,
            ]));
    }

    private function parse(): string
    {
        $html = $this->grab();

        /** @var Crawler $crawler */
        $crawler = new Crawler($html);
        try {
            $latestDataRow = $crawler->filter('table tbody tr')->eq(0);
            $previousDataRow = $crawler->filter('table tbody tr')->eq(1);
            $rowData['latest'] = [];
            $rowData['previous'] = [];
            $headerRow = $crawler->filter('table thead tr')->children();
            /** @var \DOMElement $headerColumn */
            foreach ($headerRow as $i => $headerColumn) {
                $columnName = str_replace(' ', '_', $headerColumn->nodeValue);
                $columnName = preg_replace('/[*]+/', '', strtolower($columnName));
                $rowData['latest'][$columnName] = str_replace(',', '', $latestDataRow->filter('td')->eq($i)->filter('span')->html());
                $rowData['previous'][$columnName] = str_replace(',', '', $previousDataRow->filter('td')->eq($i)->filter('span')->html());
            }
            $currentPrice = $rowData['latest']['adj_close'];
            $hao = ($rowData['previous']['open'] + $rowData['previous']['close']) / 2;
            $hac = ($rowData['latest']['open'] + $rowData['latest']['high'] + $rowData['latest']['low'] + $currentPrice) / 4;
            
            return ($hac > $hao) ? 'green' : 'red';
        } catch (\Exception $e) {
            exit(json_encode([
                'response' => false,
                'code' => -1,
                'status' => 'error',
                'message' => 'EXCEPTION #'.$e->getCode().': '.$e->getMessage(),
            ]));
        }
    }

    private function grab(): string
    {
        /** @var Client $client */
        $client = new Client();
        try {
            $result = $client->request('GET', self::URL_WITH_PARAMS, [
                'verify' => false
            ]);
        } catch (\Exception $e) {
            exit(json_encode([
                'response' => false,
                'code' => -1,
                'status' => 'error',
                'message' => 'EXCEPTION #'.$e->getCode().': '.$e->getMessage(),
            ]));
        }

        return $result->getBody()->getContents();
    }
}
