<?php

/*
 * Copyright 2012 ICMBio
* Este arquivo é parte do programa SISICMBio
* O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
* da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
* 2 da Licença.
*
* Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
* uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
* Licença Pública Geral GNU/GPL em português para maiores detalhes.
* Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
* junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
* www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
* Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
* */

namespace Sica\Model\Repository;

use Bisna\Application\Resource\Doctrine;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * SISICMBio
 *
 * Classe para Repository de Logger para o WS
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Pais
 * @version	 1.0.0
 */
class LoggerWs extends \Sica_Model_Repository
{

    /**
     * Executa a function de banco para persistencia trilha de auditoria, referente aos registros salvo via WS
     *
     *
     */
    public function saveTrilhaAuditoria($params)
    {
        $sqUsuario = is_null($params['sqUsuario']) ? 'null' : $params['sqUsuario'];
        $query = 'SELECT auditoria.trilha_insere';
        $query .= "('{$params['sqSistema']}'::int,
        '{$params['sqClasse']}'::int,
        '{$params['sqMetodo']}'::int,
         {$sqUsuario},
        '{$params['sgOperacao']}',
        '{$params['stUsuarioExterno']}'::bool,
        '{$params['xmTrilha']}'::xml);";

        return $this->_em->getConnection()->exec($query);
    }
}
