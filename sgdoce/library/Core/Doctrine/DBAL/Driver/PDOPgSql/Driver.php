<?php

use Doctrine\DBAL\Driver\PDOPgSql\Driver;

class Core_Doctrine_DBAL_Driver_PDOPgSql_Driver extends Driver
{
    public function getDatabasePlatform()
    {
        return new Core_Doctrine_DBAL_Platforms_PostgreSqlPlatform();
    }
}
