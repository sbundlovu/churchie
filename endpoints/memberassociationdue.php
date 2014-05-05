<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$memberAssociationDue = $app['controllers_factory'];

$memberAssociationDue->get('/', function() use ($app) {
	return 'Hello';
});

return $memberAssociationDue;