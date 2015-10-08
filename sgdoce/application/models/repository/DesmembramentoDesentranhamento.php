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
 * Classe para Repository de DesmembramentoDesentranhamento
 *
 * @package      Model
 * @subpackage   Repository
 * @name         DesmembramentoDesentranhamento
 * @version      1.0.0
 * @since        2014-12-03
 */
class DesmembramentoDesentranhamento extends \Core_Model_Repository_Base
{
    /**
     * Váriavel DesmembramentoDesentranhamento
     * @var string
     * @name app:DesmembramentoDesentranhamento
     * @access private
     */
    private $_enName = 'app:DesmembramentoDesentranhamento';
	
    

    /**
     * Método retorna dados de um registro solicitado.
     *  
     * @param \Core_Dto_Search $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getData(\Core_Dto_Search $objDtoSearch)
    {
    	$objQuery = $this->getEntityManager()
			    	->createQueryBuilder()
			    	->select("pdd.sqDesmembramentoDesentra, 
			    			  at.nuArtefato, 
			    			  ad.nuArtefato as nuArtefatoDestino,
			    			  us.sgUnidadeOrg,
			    			  pa.noPessoa")
			    	->from('app:ProcessoDesmembramentoDesentranhamento', 'pdd')
			    	->innerJoin('pdd.sqArtefato', 'at')
			    	->innerJoin('pdd.sqArtefatoDestino', 'ad')
			    	->innerJoin('pdd.sqUnidadeSolicitacao', 'us')
			    	->innerJoin('pdd.sqPessoaAssinatura', 'pa')
			    	->andWhere('pdd.sqProcessoDesmembramento = :sqProcessoDesmembramento')
			    	->setParameter('sqProcessoDesmembramento', $objDtoSearch->getSqProcessoDesmembramento());
        
    	return $objQuery->getQuery()->execute();
    }
    
}
