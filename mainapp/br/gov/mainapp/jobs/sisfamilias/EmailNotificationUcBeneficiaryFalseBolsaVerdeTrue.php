<?php
require_once __DIR__ . '/../SIALEnvironment.php';

$exp = new \br\gov\mainapp\application\sisfamilias\jobs\mvcb\business\EmailNotificationBusiness;
$exp->findUcBeneficiaryFalseBolsaVerdeTrueByInterval();