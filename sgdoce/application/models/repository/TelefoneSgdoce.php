<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
namespace Sgdoce\Model\Repository;
use Doctrine\DBAL\Types\IntegerType,
    Doctrine\ORM\Mapping\Entity;

/**
 * SISICMBio
 *
 * Classe para Repository de TelefoneSgdoce
 *
 * @package      Model
 * @subpackage   Repository
 * @name         TelefoneSgdoce
 * @version      1.0.0
 * @since        2013-02-08
 */
class TelefoneSgdoce extends \Core_Model_Repository_Base
{

    public function deleteByPessoaSgdoce ($sqPessoaSgdoce)
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb->delete($this->_entityName, 't')
                        ->where($qb->expr()->eq('t.sqPessoaSgdoce', $sqPessoaSgdoce))
                        ->getQuery()
                        ->execute();
    }
}
