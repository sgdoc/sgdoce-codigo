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

/**
 * SISICMBio
 *
 * Classe para Repository de PessoaFuncao
 *
 * @package      Model
 * @subpackage   Repository
 * @name         PessoaFuncao
 * @version      1.0.0
 * @since        2012-11-20
 */
class PessoaFuncao extends \Core_Model_Repository_Base
{
    /**
    * Nome da entidade
    * @var string
    */
    protected $_entityName = 'app:PessoaFuncao';

    public function listPessoaFuncao($params)
    {
        $parConsulta = mb_strtolower($params['query'], 'UTF-8');

        $query = $this->_em->createQueryBuilder()
                           ->select('p.sqPessoaFuncao, p.noPessoaFuncao')
                           ->from($this->_entityName, 'p')
                           ->where('lower(p.noPessoaFuncao) like :noPessoaFuncao')
                           ->setParameter('noPessoaFuncao', '%' . $parConsulta . '%');

        $resultado = $query->getQuery()->getArrayResult();

        $arrReturn = array();

        if(count($resultado)){
            foreach ($resultado as $values){
                $arrReturn[$values["sqPessoaFuncao"]] = $values["noPessoaFuncao"];
            }
        }

        return $arrReturn;
    }

}