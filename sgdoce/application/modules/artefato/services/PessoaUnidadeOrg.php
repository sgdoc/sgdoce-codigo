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

namespace Artefato\Service;
use Sgdoce\Model\Entity\VwUnidadeOrg;

/**
 * Classe para Service de PosicaoData
 *
 * @package  Minuta
 * @category Service
 * @name     PosicaoData
 * @version  1.0.0
 */

class PessoaUnidadeOrg extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:PessoaUnidadeOrg';
    /**
     * Método que retorna pesquisa do banco para preencher combo
     * @return array
     */
    public function findUnidadeSgdoce($entityUnidadeSgdoce)
    {
        return $this->_getRepository()->findUnidadeSgdoce($entityUnidadeSgdoce);
    }

    public function findUnidSgdoce($entityUnidadeSgdoce)
    {
        $criteria['sqPessoaSgdoce'] = $entityUnidadeSgdoce->getSqPessoaSgdoce();
        if ($entityUnidadeSgdoce instanceof \Core_Dto_Entity &&
            $entityUnidadeSgdoce->getSqPessoaUnidadeOrgCorp() instanceof VwUnidadeOrg) {
            $criteria['sqPessoaUnidadeOrgCorp'] = $entityUnidadeSgdoce->getSqPessoaUnidadeOrgCorp()->getSqUnidadeOrg();
        }
        $result = $this->_getRepository()->findBy($criteria, array('sqPessoaUnidadeOrg'  => 'desc' ));

        if(count($result) > 0) {
			return $result[0];
		}

		return NULL;
    }

    /**
     * Método que retorna pesquisa do banco para preencher combo
     * @return array
     */
    public function mountDtoUnidadeSgdoce($params,$dtoSearch)
    {
        //sgdoce
        $dtoUnidadeSgdoce = \Core_Dto::factoryFromData(
            $params,
            'entity',
            array(
                'entity'  => 'Sgdoce\Model\Entity\PessoaUnidadeOrg',
                'mapping' => array(
                    'sqPessoaSgdoce'         => 'Sgdoce\Model\Entity\PessoaSgdoce',
                    'sqPessoaUnidadeOrgCorp' =>  array(
                        'sqUnidadeOrg' => 'Sgdoce\Model\Entity\VwUnidadeOrg'
                    )
                )
            )
        );
        $sqUnidadeSgdoce = $this->findUnidSgdoce($dtoUnidadeSgdoce);

        if(!$sqUnidadeSgdoce){
            //gravar o cargo na pessoa unidade org.
            $pessoaAssinanteCargo = $this->getServiceLocator()->getService('PessoaAssinanteArtefato')->findCargoAssinante($dtoSearch);

            if($pessoaAssinanteCargo && $pessoaAssinanteCargo->getSqCargo()) {
                $dtoUnidadeSgdoce->setNoCargo($pessoaAssinanteCargo->getSqCargo()->getNoCargo());
            }

            $sqUnidadeSgdoce = $this->getServiceLocator()->getService('MinutaEletronica')
                ->saveDestinatario($dtoUnidadeSgdoce)
                ->getSqPessoaUnidadeOrg();
        } else {
            $sqUnidadeSgdoce = $sqUnidadeSgdoce->getSqPessoaUnidadeOrg();
        }

        $params['sqPessoaUnidadeOrg'] = $sqUnidadeSgdoce;

        return $params;
    }
}
