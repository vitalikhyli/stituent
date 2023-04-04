<?php

namespace App\Http\Controllers;

use Solarium\Client;

// https://solarium.readthedocs.io/en/stable/queries/ping-query/

class SolariumController extends Controller
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function search()
    {

        // $client = new Client;

        $client = $this->client;

        // $client->setAdapter('Solarium\Core\Client\Adapter\Http');

        //dd($client);

        // // create a ping query
        $ping = $client->createPing();
        $client->ping($ping);

        // dd($ping);
        // execute the ping query
        // try {
        //     $result = $client->ping($ping);
        //     echo 'Ping query successful';
        //     echo '<br/><pre>';
        //     var_dump($result->getData());
        //     echo '</pre>';
        // } catch (Solarium\Exception $e) {
        //     echo 'Ping query failed';
        // }

        // try {
        //     $client->createPing();
        //     dd(reponse());
        //     return response()->json('OK');
        // } catch (\Solarium\Exception $e) {
        //     return response()->json('ERROR', 500);
        // }

        // $query = $client->createSelect();
        // $query->addFilterQuery(array('key'=>'provence', 'query'=>'provence:Groningen', 'tag'=>'include'));
        // $query->addFilterQuery(array('key'=>'degree', 'query'=>'degree:MBO', 'tag'=>'exclude'));
        // $facets = $query->getFacetSet();
        // $facets->createFacetField(array('field'=>'degree', 'exclude'=>'exclude'));
        // $resulset = $client->select($query);

        // // display the total number of documents found by solr
        // echo 'NumFound: ' . $resultset->getNumFound();

        // // show documents using the resultset iterator
        // foreach ($resultset as $document) {

        //     echo '<hr/><table>';

        //     // the documents are also iterable, to get all fields
        //     foreach ($document as $field => $value) {
        //         // this converts multivalue fields to a comma-separated string
        //         if (is_array($value)) {
        //             $value = implode(', ', $value);
        //         }

        //         echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
        //     }

        //     echo '</table>';
        // }
    }
}
