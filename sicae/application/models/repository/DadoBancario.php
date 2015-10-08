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

/**
 * SISICMBio
 *
 * Classe para Repository de Dados Bancarios
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 DadoBancario
 * @version	 1.0.0
 */
class DadoBancario extends \Sica_Model_Repository
{

    /**
     * Realiza a pesquisa da grid
     * @param \Core_Dto_Abstract $dto
     */
    public function listGrid(\Core_Dto_Abstract $dto)
    {
        return $this->_em->createQueryBuilder()
                        ->select('
                            c.sqDadoBancario,
                            td.noTipoDadoBancario,
                            b.noBanco,
                            b.coBanco,
                            a.noAgencia,
                            a.coDigitoAgencia,
                            a.coAgencia,
                            c.nuConta,
                            c.nuContaDv,
                            p.sqPessoa
                            ')
                        ->from('app:DadoBancario', 'c')
                        ->innerJoin('c.sqTipoDadoBancario', 'td')
                        ->innerJoin('c.sqAgencia', 'a')
                        ->innerJoin('a.sqBanco', 'b')
                        ->innerJoin('c.sqPessoa', 'p')
                        ->where('p.sqPessoa = :sqPessoa')
                        ->setParameter('sqPessoa', $dto->getSqPessoa());
    }

}
