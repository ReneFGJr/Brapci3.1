<?php

namespace App\Models\Dataverse\Install;

use CodeIgniter\Model;

class PreInstall extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'preinstalls';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function index($d1, $d2)
    {
        $sx = bsc(h(lang('dataverse.preinstall')), 12);
        $sb = '';
        switch ($d1) {
            case 'java':
                $sb = $this->java();
                break;
            case 'payara':
                $sb = $this->payara();
                break;
            case 'postgresql':
                $sb = $this->postgresql();
                break;
            case 'solr':
                $sb = $this->solr();
                break;
            case 'R':
                $sb = $this->R();
                break;
        }
        $menu = [];
        $menu['#' . lang('dataverse.software')] = '';
        $menu[PATH . '/dados/dataverse/preinstall/java'] = 'JAVA';
        $menu[PATH . '/dados/dataverse/preinstall/payara'] = 'Payara';
        $menu[PATH . '/dados/dataverse/preinstall/postgresql'] = 'PostGreSQL';
        $menu[PATH . '/dados/dataverse/preinstall/solr'] = 'SolR';
        $menu[PATH . '/dados/dataverse/preinstall/R'] = 'R-server';
        $sa = menu($menu);

        $sx .= bsc($sa, 4) . bsc($sb, 8);
        $sx = bs($sx);
        return $sx;
    }

    function solr()
    {
    }

    function imagemagick()
        {
            $sx = '';
            $sr = 'apt install imagemagick';
            $sx .= bscode($sr);
            return $sx;
        }
    function R()
        {
            $sx = '';
            $sr = 'apt install r-base';
            $sx .= bscode($sr);
            $sx .= 'Acessando o R';
            $sr = 'R';
            $sx .= bscode($sr);

            $sx .= 'Instalando as bibliotecas';
            $sr = 'install.packages("R2HTML", repos="https://cloud.r-project.org/", lib="/usr/lib64/R/library" )'.cr(0);
            $sr .= 'install.packages("rjson", repos="https://cloud.r-project.org/", lib="/usr/lib64/R/library" ) '.cr(0);
            $sr .= 'install.packages("DescTools", repos="https://cloud.r-project.org/", lib="/usr/lib64/R/library" )' . cr(0);
            $sr .= 'install.packages("Rserve", repos="https://cloud.r-project.org/", lib="/usr/lib64/R/library" ) '.cr(0);
            $sr .= 'install.packages("haven", repos="https://cloud.r-project.org/", lib="/usr/lib64/R/library" )'.cr(0);
            $sr .= 'q();'.cr(0);
            $sr .= 'Save workspace image? [y/n/c]: y';
            $sx .= bscode($sr);

            return $sx;
        }

    function jq()
    {
        $sx = '';
        $sr = 'apt install jq';
        $sx .= bscode($sr);
        $sx .= 'ou';
        $sr = 'cd /usr/bin'.cr(0);
        $sr .= 'wget http://stedolan.github.io/jq/download/linux64/jq'.cr(0);
        $sr .= 'chmod +x jq '.cr(0);
        $sr .= 'jq --version' . cr(0);
        return $sx;
    }


    function postgresql()
    {
        $ver = get("ver");
        if ($ver == '')
            {
                $ver = '15';
            }
        $sx = '';
        $so = 'ubuntu';
        switch ($so) {
            case 'ubuntu':
                $sr = '';
                $sr .= 'apt install postgresql';
                $sx .= bscode($sr);

                $sx .= 'Editar o arquivo /etc/postgresql/'.$ver.'/main/postgresql.conf';
                $sr = 'nano /etc/postgresql/' . $ver . '/main/postgresql.conf';
                $sx .= bscode($sr);

                $sx .= 'Na linha:';
                $sr = "#listen_addresses = 'localhost'";
                $sx .= bscode($sr,'info');

                $sx .= 'Troque por (acesso somente no Localhost):';
                $sr = "listen_addresses = 'localhost'";
                $sx .= bscode($sr, 'info');

                $sx .= 'Troque por (acesso por qualquer máquina):';
                $sr = "listen_addresses = '*'";
                $sx .= bscode($sr, 'info');

                $sx .= 'Edite o arquivo nano /etc/postgresql/'.$ver.'/main/pg_hba.conf';
                $sr = 'nano /etc/postgresql/'.$ver.'/main/pg_hba.conf';
                $sx .= bscode($sr);

                $sx .= 'Altere o tipo de acesso de:';
                $sr = 'host    all             all             127.0.0.1/32            md5'.cr(0);
                $sr .= 'host    all             all             ::1/128                 md5';
                $sx .= bscode($sr,'info');

                $sx .= 'Para:';
                $sr = 'host    all             all             127.0.0.1/32            trust' . cr(0);
                $sr .= 'host    all             all             ::1/128                 trust';
                $sx .= bscode($sr, 'info');

                $sx .= 'Para testar a conectivdade, o sistemas deve retornar uma tabela com o comando:';
                $sr = "/usr/bin/psql -h localhost -p 5432 -U postgres -d postgres -c 'SELECT * FROM pg_roles'";
                $sx .= bscode($sr);

                $sr = '          rolname          | rolsuper | rolinherit | rolcreaterole | rolcreatedb | rolcanlogin | rolreplication | rolconnlimit | rolpassword | rolvaliduntil | rolbypassrls | rolconfig |  oid'.cr(0);
                $sr .= '---------------------------+----------+------------+---------------+-------------+-------------+----------------+--------------+-------------+---------------+--------------+-----------+-------'.cr(0);
                $sr .= 'pg_signal_backend         | f        | t          | f             | f           | f           | f              |           -1 | ********    |               | f            |           |  4200' . cr(0);
                $sr .= 'pg_read_server_files      | f        | t          | f             | f           | f           | f              |           -1 | ********    |               | f            |           |  4569 '.cr(0);
                $sr .= 'postgres                  | t        | t          | t             | t           | t           | t              |           -1 | ********    |               | t            |           |    10' . cr(0);
                $sr .= 'pg_write_server_files     | f        | t          | f             | f           | f           | f              |           -1 | ********    |               | f            |           |  4570 '.cr(0);
                $sr .= 'pg_execute_server_program | f        | t          | f             | f           | f           | f              |           -1 | ********    |               | f            |           |  4571' . cr(0);
                $sr .= 'pg_read_all_stats         | f        | t          | f             | f           | f           | f              |           -1 | ********    |               | f            |           |  3375 '.cr(0);
                $sr .= 'pg_monitor                | f        | t          | f             | f           | f           | f              |           -1 | ********    |               | f            |           |  3373' . cr(0);
                $sr .= 'dvnapp                    | f        | t          | t             | t           | t           | f              |           -1 | ********    |               | f            |           | 16384 '.cr(0);
                $sr .= 'pg_read_all_settings      | f        | t          | f             | f           | f           | f              |           -1 | ********    |               | f            |           |  3374 ' . cr(0);
                $sr .= 'pg_stat_scan_tables       | f        | t          | f             | f           | f           | f              |           -1 | ********    |               | f            |           |  3377 '.cr(0);
                $sr .= '(10 rows)' . cr(0);
                $sx .= bscode($sr, 'info');

                break;
        }
        return $sx;
    }

    function payara()
    {
        $ver = '5.2021.6';
        $sx = '';
        $so = 'ubuntu';
        switch ($so) {
            case 'ubuntu':
                $sr = '';
                $sr .= 'wget https://s3-eu-west-1.amazonaws.com/payara.fish/Payara+Downloads/' . $ver . '/payara-' . $ver . '.zip' . cr(0);
                $sr .= 'unzip payara-5.2021.6.zip' . cr(0);
                $sr .= 'mv payara5 /usr/local' . cr(0);
                $sr .= cr(0);
                $sr .= 'echo "Change Permissions"' . cr(0);
                $sr .=
                    'chown -R root:root /usr/local/payara5' . cr(0);
                $sr .= 'chown dataverse /usr/local/payara5/glassfish/lib ' . cr(0);
                $sr .= 'chown -R dataverse:dataverse /usr/local/payara5/glassfish/domains/domain1' . cr(0);
                $sx .= bscode($sr);

                $sr = 'echo "Criar o Payara como um servico"' . cr(0);
                $sr .= './asadmin create-service --serviceuser payaraadmin --system-type systemv domain1' . cr(0);
                $sr .= 'systemctl daemon-reload' . cr(0);
                $sr .= 'systemctl start payara_domain1.service';
                $sx .= bscode($sr);

                $sr = 'echo "Criar path do Payara no perfil do usuário"' . cr(0);
                $sr .= 'nano ~/.bashrc' . cr(0);
                $sx .= bscode($sr);
                $sx .= 'Na última linha, insira:';
                $sr = 'export PAYARA=/usr/local/payara5/glassfish/bin/';
                $sx .= bscode($sr, 'info');

                $sx .= 'Comandos para:';
                $sr = 'echo "Iniciar o Payara"' . cr(0);
                $sr .= '$PAYARA/asadmin start-domain' . cr(0) . cr(0);

                $sr .= 'echo "Parar o Payara"' . cr(0);
                $sr .= ' $PAYARA/asadmin stop-domain';

                $sx .= bscode($sr, 'answer');

                $sx .= '</pre>';
        }
        return $sx;
    }


    function java()
    {
        $sx = '';
        $so = 'ubuntu';
        switch ($so) {
            case 'ubuntu':
                $sx .= bscode('apt install default-jdk');
                $sx .= 'Verificar a versão instalada';
                $sx .= bscode('java -version');

                $sx .= 'Resposta';
                $sr =
                    'openjdk version "11.0.18" 2023-01-17' . cr(0);
                $sr .=
                    'OpenJDK Runtime Environment (build 11.0.18+10-post-Ubuntu-0ubuntu120.04.1) ' . cr(0);
                $sr .= 'OpenJDK 64-Bit Server VM (build 11.0.18+10-post-Ubuntu-0ubuntu120.04.1, mixed mode, sharing)';
                $sx .= bscode($sr, 'answer');

                $sx .= '</pre>';
        }
        return $sx;
    }
}
