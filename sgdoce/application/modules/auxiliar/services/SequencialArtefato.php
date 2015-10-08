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
namespace Auxiliar\Service;
use Doctrine\Common\Util\Debug;

/**
 * Classe para Service de SequencialArtefato
 *
 * @package  Auxiliar
 * @category Service
 * @name     Tipodoc
 * @version  1.0.0
 */

class SequencialArtefato extends \Core_ServiceLayer_Service_CrudDto
{
    /**
    * @var string
    */
    protected $_entityName = 'app:SequencialArtefato';

    public function getNuSequencial($params = NULL, $view = FALSE, $sqPessoaLogada = NULL)
    {
    	$dtoOrigem      = \Core_Dto::factoryFromData(array('sqProfissional' => $sqPessoaLogada), 'search');
    	$unidadeOrg     = $this->getServiceLocator()->getService('Dossie')->unidadeOrigemPessoa($dtoOrigem);

    	$sqUnidadeOrg   = $unidadeOrg->getSqUnidadeExercicio()->getSqUnidadeOrg();
    	$unidadeOrg     = $unidadeOrg->getSqUnidadeExercicio();
    	$sqTipoArtefato = \Core_Configuration::getSgdoceTipoArtefatoProcesso() ; // processo;
    	$sqTipoDocumento = $this->getServiceLocator()->getService('Documento')->find($params['id']);
    	$sqTipoDocumento = $sqTipoDocumento->getSqTipoDocumento()->getSqTipoDocumento();

    	$data = new \Zend_Date();
    	$criteria = array('sqUnidadeOrg' => $sqUnidadeOrg , 'sqTipoArtefato' => $sqTipoArtefato
    			,'sqTipoDocumento' => $sqTipoDocumento  ,'nuAno' => $data->get('yyyy'));
    	$orderBy  = array('sqSequencialArtefato'=> 'desc');

    	$sequencialArtefato = $this->getServiceLocator()->getService('Sequnidorg')->findBy($criteria,$orderBy,1);

    	if($sequencialArtefato){
    		$nuSequencial = $sequencialArtefato->getNuSequencial() + 1;
    		$params['sqSequencialArtefato'] = $sequencialArtefato->getSqSequencialArtefato();
    	}else{
    		$nuSequencial = 1;
    		$params['sqSequencialArtefato'] = NULL;
    	}
		if(!$view){
			//sava somente quando concluir o form.
	    	$params['sqTipoDocumento'] = $sqTipoDocumento;
	    	$params['sqTipoArtefato']  = $sqTipoArtefato;
	    	$params['sqUnidadeOrg']    = $sqUnidadeOrg;
	    	$params['nuAno']    	   = $data->get('yyyy');
	    	$params['nuSequencial']    = $nuSequencial;
			$optionSequencial = array(
					'entity' => 'Sgdoce\Model\Entity\SequencialArtefato',
					'mapping' => array(
							'sqTipoArtefato' => array('sqTipoArtefato'=>'Sgdoce\Model\Entity\TipoArtefato'),
							'sqUnidadeOrg' => array('sqUnidadeOrg'=>'Sgdoce\Model\Entity\VwUnidadeOrg'),
							'sqTipoDocumento' => array('sqTipoDocumento'=>'Sgdoce\Model\Entity\TipoDocumento')
					)
			);
			$dtoSequencial = \Core_Dto::factoryFromData($params, 'entity', $optionSequencial);
			$this->save($dtoSequencial);
			$this->finish($dtoSequencial);
		}

    	return $nuSequencial;
    }

    public function getNuSequencialProcesso()
    {
        $criteria = array(
            'sqTipoArtefato' => \Core_Configuration::getSgdoceTipoArtefatoProcesso(),
            'sqTipoDocumento' => NULL,
            'sqUnidadeOrg' => NULL
        );
        $seqProcesso = $this->findBy($criteria);

        if (!empty($seqProcesso[0])){
            $sequencial = $seqProcesso[0];
        } else {
            $data = new \Zend_Date();
            $sequencial = new \Sgdoce\Model\Entity\SequencialArtefato();
            $sequencial->setNuSequencial(0);
            $sequencial->setSqTipoArtefato(
                $this->_getRepository('app:TipoArtefato')->find(\Core_Configuration::getSgdoceTipoArtefatoProcesso())
            );
            $sequencial->setNuAno($data->get('yyyy'));
            $this->getEntityManager()->persist($sequencial);
            $this->getEntityManager()->flush();
        }

        $session = new \Core_Session_Namespace('Sequencial');
        $session->__set('oldNuSequencial',$sequencial->getNuSequencial());
        $disponivel = FALSE;
        $nuSequencial = (string) str_pad($sequencial->getNuSequencial(), 6, "0", STR_PAD_LEFT);
        do{
            $nuSequencial = $nuSequencial + 1;
            $nuSequencial = (string) str_pad($nuSequencial, 6, "0", STR_PAD_LEFT);
            $disponivel = $this->_getRepository('app:SequencialArtefato')->hasSequencialProcesso($nuSequencial);

            if(!$disponivel){
                $nuSequencial + 1;
            }

        } while($disponivel == FALSE);

        $sequencial->setNuSequencial($nuSequencial +1);
        $this->getEntityManager()->persist($sequencial);
        $this->getEntityManager()->flush();
        return $sequencial->getNuSequencial();
    }

    public function saveSequencialProcesso($sequencial)
    {
        $criteria = array(
            'sqTipoArtefato' => \Core_Configuration::getSgdoceTipoArtefatoProcesso(),
            'sqTipoDocumento' => NULL,
            'sqUnidadeOrg' => NULL,
        );
        $seqProcesso = $this->findBy($criteria);
        $seqProcesso[0]->setNuSequencial($sequencial);
        $this->getEntityManager()->persist($seqProcesso[0]);
    }

    /**
     * RETORNA PROXIMO SEQUENCIAL DA UNIDADE DO USUARIO LOGADO.
     * 
     * @return integer
     */
    public function getNextSequencialProcesso()
    {
        $sqUnidadeOrg = \Core_Integration_Sica_User::getUserUnit();        
        return $this->_getRepository('app:SequencialArtefato')->getNextSequencialProcesso( $sqUnidadeOrg );
    }

    /**
     * ATUALIZA SEQUENCIAL DO ORGÃO.
     * 
     * @return integer
     */
    public function setSequencialProcesso()
    {
        $sqUnidadeOrg = \Core_Integration_Sica_User::getUserUnit();        
        return $this->_getRepository('app:SequencialArtefato')->setSequencialProcesso( $sqUnidadeOrg );
    }    
}
