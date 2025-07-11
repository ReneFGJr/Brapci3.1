<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => '',
        'password'     => '',
        'database'     => 'brapci',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];
    public array $AI = [];
    public array $books = [];
    public array $brapci = [];
    public array $brapci_cited = [];
    public array $CDU = [];
    public array $authority = [];
    public array $dci  = [];
    public array $oai = [];
    public array $kanban = [];
    public array $search = [];
    public array $elastic = [];
    public array $editais = [];
    public array $observatorio = [];
    public array $liked = [];
    public array $lattes = [];
    public array $click = [];
    public array $gev3nt = [];
    public array $icr = [];
    public array $patent = [];
    public array $pgcd = [];
    public array $capes = [];
    public array $pq = [];
    public array $find = [];
    public array $find2 = [];
    public array $findserver = [];
    public array $bots = [];
    public array $bibliofind = [];
    public array $persistent_indicador = [];
    public array $dataverse = [];
    public array $guide = [];
    public array $software = [];
    public array $handle = [];

    public array $openaire = [];
    public array $wordpress = [];
    public array $public = [];
    public array $rdf = [];
    public array $rdfs = [];
    public array $rdf2 = [];
    public array $reverseindex = [];
    public array $thesa = [];
    public array $vc = [];
    public array $management = [];
    public array $manuais = [];



    /**
     * This database connection is used when running PHPUnit database tests.
     *
     * @var array<string, mixed>
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => 'sa',
        'password'    => '',
        'database'    => 'brapci',
        'DBDriver'    => 'MySQLi',
        'DBPrefix'    => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => '',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
        'dateFormat'  => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        // Ensure that we always set the database group to 'tests' if
        // we are currently running an automated test suite, so that
        // we don't overwrite live data on accident.
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }

        $this->default['username'] = getenv('database.default.username');
        $this->default['password'] = getenv('database.default.password');

        $db = [
            'AI' => 'AI',
            'authority' => 'brapci_authority',
            'books' => 'brapci_books',
            'brapci' => 'brapci',
            'bots' => 'brapci_bots',
            'pq' => 'brapci_pq',
            'capes' => 'capes',
            'CDU' => 'cdu',
            'brapci_cited' => 'brapci_cited',
            'capes' => 'capes',
            'click'=>'brapci_click',
            'dataverse' => 'dataverse',
            'dci' => 'dci',
            'editais' => 'brapci_editais',
            'elastic' => 'brapci_elastic',
            'kanban' => 'kanban',
            'find' => 'find',
            'find2' => 'find2',
            'findserver' => 'find_server',
            'handle' => 'handle.net',
            'icr' => 'brapci_icr',
            'oai' => 'brapci_oaipmh',
            'oaiserver' => 'brapci_oaipmh_editor',
            'observatorio' => 'observatorio',
            'openaire' => 'openaire',
            'lattes'=> 'brapci_lattes',
            'liked' => 'brapci_like',
            'search' => 'brapci_search',
            'gev3nt'=> 'gev3nt',
            'patent'=> 'brapci_patent',
            'rdf' => 'brapci_rdf',
            'rdfs' => 'brapci_rdf',
            'rdf2' => 'brapci_rdf',
            'reverseindex' => 'brapci_network',
            'tools' => 'brapci_tools',
            'thesa' => 'thesa',
            'software' => 'software',
            'guide' => 'guide',
        ];
    /*

    public array $bibliofind = [];
    public array $persistent_indicador = [];
    public array $dataverse = [];
    public array $guide = [];
    public array $handle = [];

    public array $openaire = [];
    public array $wordpress = [];
    public array $public = [];

    public array $thesa = [];
    public array $vc = [];
    public array $management = [];
    public array $manuais = [];
    */

        foreach ($db as $base => $database) {
            $a = '$this->' . $base . ' = $this->default;' . cr();
            $a .= '$this->' . $base . '[\'database\'] = \'' . $database . '\';' . cr();
            $a .= '$this->' . $base . '[\'username\'] = getenv(\'database.default.username\');' . cr();
            $a .= '$this->' . $base . '[\'password\'] = getenv(\'database.default.password\');' . cr();
            eval($a);
        }
    }
}
