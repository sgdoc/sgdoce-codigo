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
 * Classe para Repository de vwDocumentoSgdocFisico
 *
 * @package      Model
 * @subpackage   Repository
 * @name         vwDocumentoSgdocFisico
 * @version      1.0.0
 * @since        2015-07-15
 */
class vwDocumentoSgdocFisico extends \Core_Model_Repository_Base
{
    /**
     * @param \Core_Dto_Search $dto
     */
    public function findByNuDigital( $dto )
    {
        $sql = 'SELECT  id, tipo, numero, origem, 
                        destino, interessado, assunto, 
                        assunto_complementar, 
                        cargo, assinatura, procedencia, 
                        digital, dt_cadastro, dt_prazo, 
                        ultimo_tramite, prioridade, autor
               FROM sgdoce.vw_documento_sgdoc_fisico WHERE digital = lpad(\'%1$d\', 7, \'0\');';


        $strSql = sprintf(
            $sql
            ,$dto->getNuDigital()
        );

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('id', 'id', 'integer');
        $rsm->addScalarResult('tipo', 'tipo', 'string');
        $rsm->addScalarResult('numero', 'numero', 'string');
        $rsm->addScalarResult('origem', 'origem', 'string');
        $rsm->addScalarResult('destino', 'destino', 'string');
        $rsm->addScalarResult('interessado', 'interessado', 'string');
        $rsm->addScalarResult('assunto', 'assunto', 'string');
        $rsm->addScalarResult('assunto_complementar', 'assuntoComplementar', 'string');
        $rsm->addScalarResult('cargo', 'cargo', 'string');
        $rsm->addScalarResult('assinatura', 'assinatura', 'string');
        $rsm->addScalarResult('procedencia', 'procedencia', 'string');
        $rsm->addScalarResult('digital', 'digital', 'string');
        $rsm->addScalarResult('dt_cadastro', 'dtCadastro', 'zenddate');
        $rsm->addScalarResult('dt_prazo', 'dtPrazo', 'zenddate');
        $rsm->addScalarResult('ultimo_tramite', 'ultimoTramite', 'string');
        $rsm->addScalarResult('prioridade', 'prioridade', 'string');
        $rsm->addScalarResult('autor', 'autor', 'string');

        $nativeQuery = $this->_em->createNativeQuery($strSql, $rsm);

        return $nativeQuery->getArrayResult();
        
    }
}