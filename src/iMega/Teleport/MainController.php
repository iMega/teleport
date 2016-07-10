<?php
/**
 * Copyright (C) 2014 iMega ltd Dmitry Gavriloff (email: info@imega.ru),
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace iMega\Teleport;

use iMega\Teleport\Parser;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Controller
 */
class MainController implements ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given controller.
     *
     * @param Application $app Application.
     *
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        /**
         * @var \Silex\ControllerCollection $c
         */
        $c = $app['controllers_factory'];
        $c->post("/accept-file", array($this, 'acceptFile'))->bind('acceptFile');
        $c->post("/progress", array($this, 'progress'))->bind('progress');
        $c->post("/import", array($this, 'import'))->bind('import');

        return $c;
    }

    /**
     * Accept files
     *
     * @param Request $request
     *
     * @return bool
     */
    public function acceptFile(Application $app, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $path = sprintf('%s/%s/%s', $data['url'], $data['uripath'], $data['uuid']);
        $app['service.acceptfile']->downloads($path, $data['files']);

        return new Response('', Response::HTTP_OK);
    }


    /**
     * Write progress
     *
     * @param Request $request
     *
     * @return bool
     */
    public function progress(Application $app, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        return new Response('', Response::HTTP_OK);
    }

    /**
     * Import data
     *
     * @param Request $request
     *
     * @return Response
     */
    public function import(Application $app, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        return new Response('', Response::HTTP_OK);
    }
}
