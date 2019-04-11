<?php

use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use App\DataFixtures\AppFixtures;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Behat\Gherkin\Node\PyStringNode;

class FeatureContext extends RestContext
{
    const USERS = [
        'maciek' => 'Maciek12345'
    ];
    const AUTH_URL = '/api/login_check';
    const AUTH_JSON = '
        {
            "username": "%s",
            "password": "%s"
        }
    ';

    private $fixtures;

    private $matcher;

    private $em;

    public function __construct(Request $request, AppFixtures $fixtures, EntityManagerInterface $em)
    {
        parent::__construct($request);
        $this->fixtures = $fixtures;
        $this->matcher = (new SimpleFactory)->createMatcher();
        $this->em = $em;
    }

    /**
     * @Given I am authenticated as :arg1
     */
    public function iAmAuthenticatedAs($user)
    {
        $this->request->setHttpHeader('Content-Type', 'application/ld+json');
        $this->request->send(
            Symfony\Component\HttpFoundation\Request::METHOD_POST,
            $this->locatePath(self::AUTH_URL),
            [],
            [],
            sprintf(self::AUTH_JSON, $user, self::USERS[$user])
        );
        $json = json_decode($this->request->getContent(), true);
        
        $this->assertTrue(isset($json['token']));
        $token = $json['token'];
        $this->request->setHttpHeader(
            'Authorization',
            'Bearer '.$token
        );
    }

    /**
     * @Then the JSON matches expexted template:
     */
    public function theJsonMatchesExpextedTemplate(PyStringNode $json)
    {
        $actual = $this->request->getContent();
        $this->assertTrue(
            $this->matcher->match($actual, $json->getRaw())
        );
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createSchema()
    {
        // Get entity metadata
        $classes = $this->em->getMetadataFactory()->getAllMetadata();

        // Drop and create schema
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        // Load fixtures and execute
        $purger = new ORMPurger($this->em);
        $fixturesExecutor = new ORMExecutor($this->em, $purger);

        $fixturesExecutor->execute([
            $this->fixtures
        ]);
    }

    /**
     * @BeforeScenario @image
     */
    public function prepareImages()
    {
        copy(
            __DIR__.'/../fixtures/Stewie.png',
            __DIR__.'/../fixtures/files/Stewie.png'
        );
    }
}
