<?php

namespace iMega\Teleport;

use iMega\Teleport\Cloud\ApiCloud;
use iMega\Teleport\Service\AcceptFileService;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainControllerTest extends TestCase
{
    public function test_acceptFile_ValidData_ReturnsCode200()
    {
        $json = json_encode([
            [
                'url' => 'http://example.com/output.sql',
                'sum' => 'd0ba8c0bea4cce2a5848a68faddea53c',
            ]
        ]);
        $req  = Request::create('http://example.com/accept-file', 'POST', array(), array(), array(), array(), $json);

        $accept = $this->createMock(AcceptFileService::class);
        $cloud  = $this->createMock(ApiCloud::class);
        $app    = new Application([
            'service.acceptfile' => $accept,
            'teleport.cloud'     => $cloud,
        ]);

        $ctrl = new MainController();
        $resp = $ctrl->acceptFile($app, $req);

        $this->assertEquals(Response::HTTP_OK, $resp->getStatusCode());
    }
}
