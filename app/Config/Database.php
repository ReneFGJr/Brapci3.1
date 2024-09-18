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

    public array $books = [];
    public array $brapci = [];
    public array $brapci_cited = [];
    public array $authority = [];
    public array $oai = [];
    public array $kanban = [];
    public array $search = [];
    public array $observatorio = [];
    public array $lattes = [];
    public array $click = [];
    public array $gev3nt = [];
    public array $patent = [];
    public array $pgcd = [];
    public array $capes = [];
    public array $pq = [];
    public array $find = [];
    public array $find2 = [];
    //public array $elastic = [];
    public array $bots = [];
    public array $bibliofind = [];
    public array $persistent_indicador = [];
    public array $dataverse = [];
    public array $guide = [];
    public array $handle = [];

    public array $openaire = [];
    public array $wordpress = [];
    public array $public = [];
    public array $rdf = [];
    public array $rdf2 = [];
    public array $thesa = [];
    public array $vc = [];
    public array $management = [];
    public array $manuais = [];

    public $elastic  = [
        'DSN'      => '',
        'hostname' => 'localhost', 'username' => '', 'password' => '', 'database' => 'brapci_elastic',
        'DBDriver' => 'MySQLi', 'DBPrefix' => '', 'pConnect' => false, 'DBDebug'  => (ENVIRONMENT !== 'production'),
        'charset'  => 'utf8', 'DBCollat' => 'utf8_general_ci', 'swapPre'  => '', 'encrypt'  => false, 'compress' => false,
        'strictOn' => false, 'failover' => [],  'port'     => 3306,
    ];

    public $dci  = [
        'DSN'      => '',
        'hostname' => 'localhost', 'username' => '', 'password' => '', 'database' => 'dci',
        'DBDriver' => 'MySQLi', 'DBPrefix' => '', 'pConnect' => false, 'DBDebug'  => (ENVIRONMENT !== 'production'),
        'charset'  => 'utf8', 'DBCollat' => 'utf8_general_ci', 'swapPre'  => '', 'encrypt'  => false, 'compress' => false,
        'strictOn' => false, 'failover' => [],  'port'     => 3306,
    ];
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

        $this->books = $this->default;
        $this->books['database'] = 'brapci_books';
        $this->books['username'] = getenv('database.default.username');
        $this->books['password'] = getenv('database.default.password');

        $this->brapci = $this->default;
        $this->brapci['database'] = 'brapci';
        $this->brapci['username'] = getenv('database.default.username');
        $this->brapci['password'] = getenv('database.default.password');

        $this->brapci_cited = $this->default;
        $this->brapci_cited['database'] = 'brapci';
        $this->brapci_cited['username'] = getenv('database.default.username');
        $this->brapci_cited['password'] = getenv('database.default.password');

        $this->authority = $this->default;
        $this->authority['database'] = 'brapci_authority';
        $this->authority['username'] = getenv('database.default.username');
        $this->authority['password'] = getenv('database.default.password');

        $this->dci['username'] = getenv('database.default.username');
        $this->dci['password'] = getenv('database.default.password');

        $this->elastic = $this->default;
        $this->elastic['database'] = 'brapci_elastic';
        $this->elastic['username'] = getenv('database.default.username');
        $this->elastic['password'] = getenv('database.default.password');

        echo '<pre>';
        print_r($this->dci);
        exit;

    }
}
