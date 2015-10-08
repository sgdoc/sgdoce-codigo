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

/**
 * SISICMBio
 *
 * Classe para Repository de Email
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Email
 * @version	 1.0.0
 */
class Email extends \Sica_Model_Repository
{

    /**
     * Realiza a pesquisa da grid
     * @param \Core_Dto_Abstract $dto
     */
    public function listGrid(\Core_Dto_Abstract $dto)
    {
        return $this->_em->createQueryBuilder()
                        ->select('p.sqPessoa, te.noTipoEmail, e.sqEmail, e.txEmail')
                        ->from('app:Email', 'e')
                        ->innerJoin('e.sqTipoEmail', 'te')
                        ->innerJoin('e.sqPessoa', 'p')
                        ->where('p.sqPessoa = :sqPessoa')
                        ->setParameter('sqPessoa', $dto->getSqPessoa());
    }

}