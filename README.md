Pephpit \ HttpClient
============

Gère les méthodes http GET, POST (creation), PUT (mise à jour) et DELETE


Installation
------------

	composer require "pephpit/http-client"


Exemple d'utilisation
---------------------

Récupérer une ressource

	use Pephpit\HttpClient;

	$request = new HttpClient\Request('http://monapi/ressources/1');
	$Response = $request->send();

	if($Response->isOk()){
		$ressource = $Response->getJsonDecode();
	}

Ajouter une ressource

	use Pephpit\HttpClient;

	$body = array( 'name' => 'ma ressource' );

	$request = new HttpClient\Request('http://monapi/ressources', HttpClient\Method::POST, $body );
	$Response = $request->send();

	if($Response->isCreated()){
		$id = $Response->getBody();
	}

Mettre à jour une ressource

	use Pephpit\HttpClient;

	$body = array( 'name' => 'nouveau nom' );

	$request = new HttpClient\Request('http://monapi/ressources/1', HttpClient\Method::PUT, $body );
	$Response = $request->send();

	if($Response->isSuccessful()){
		$id = $Response->getBody();
	}

Supprimer une ressource

	use Pephpit\HttpClient;

	$request = new HttpClient\Request('http://monapi/ressources/1', HttpClient\Method::DELETE);
	$Response = $request->send();

	if($Response->isSuccessful()){
		// delete ok
	}

Requètes en parallèle

	use Pephpit\HttpClient;

	$requestCollection = new HttpClient\RequestCollection();
	// ajoute la suite de la collection
	$requestCollection->add(new HttpClient\Request('http://monapi/ressources/1');
	// ajoute en définissant la clé
	$requestCollection->set('res2', new HttpClient\Request('http://monapi/ressources/2');
	// ajoute en définissant la clé, utilise offsetSet d'ArrayAccess
	$requestCollection['res42'] = new HttpClient\Request('http://monapi/ressources/42');

	// on exécute les requètes
	$responseCollection = $requestCollection->send();

	// on récupère le retour de la requète ajouté avec add (index 0)
	$body = $responseCollection->get(0)->getBody();
	// on récupère le retour de la requète res2 grâce à offsetGet d'ArrayAccess
	$body2 = $responseCollection['res2']->getBody();
	// on récupère le retour de la requète res42
	$body = $responseCollection->get('res42')->getBody();

	// on peut aussi boucler sur la collection
	foreach($responseCollection as $Response){
		if($Response->isSuccessful()){
			// do something
		}
	}