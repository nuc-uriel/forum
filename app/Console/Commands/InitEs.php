<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class InitEs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化es,创建索引';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();
        $this->createTemplate($client);
        $this->createIndex($client);
    }

    protected function createIndex(Client $client)
    {
        $url = config('scout.elasticsearch.config.hosts')[0] . ':9200/' . config('scout.elasticsearch.index');
//        $client->delete($url);
        $client->put($url, [
            'json' => [
                'settings' => [
                    'refresh_interval' => '5s',
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                ],
                'mappings' => [
                    '_default_' => [
                        '_all' => [
                            'enabled' => false
                        ]
                    ]
                ]
            ]
        ]);
    }

    protected function createTemplate(Client $client)
    {
        $url = config('scout.elasticsearch.config.hosts')[0] . ':9200/' . '_template/tmp';
//        $client->delete($url);
        $client->put($url, [
            'json' => [
                'template' => config('scout.elasticsearch.index'),
                'mappings' => [
                    '_default_' => [
                        '_all' => [
                            'enabled' => true
                        ],
                        'dynamic_templates' => [
                            [
                                'strings' => [
                                    'match_mapping_type' => 'string',
                                    'mapping' => [
                                        'type' => 'text',
                                        'analyzer' => 'ik_max_word',
                                        'ignore_above' => 256,
                                        'fields' => [
                                            'keyword' => [
                                                'type' => 'keyword'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

    }
}
