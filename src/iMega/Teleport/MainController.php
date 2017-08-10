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

use iMega\Teleport\Cloud\ApiCloud;
use iMega\Teleport\Events\DumpEvent;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use iMega\Teleport\Service\AcceptFileService;

/**
 * Class Controller
 */
class MainController implements ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given controller.
     *
     * @param Application $app
     *
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        /**
         * @var \Silex\ControllerCollection $c
         */
        $c = $app['controllers_factory'];
        $c->get("/", array($this, 'ping'))->bind('ping');
        $c->post("/accept-file", array($this, 'acceptFile'))->bind('acceptFile');
        $c->post("/progress", array($this, 'progress'))->bind('progress');
        $c->post("/import", array($this, 'import'))->bind('import');

        return $c;
    }

    /**
     * Ping
     *
     * @return Response
     */
    public function ping()
    {
        return new Response('pong', Response::HTTP_OK);
    }

    /**
     * Accept files
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return Response
     */
    public function acceptFile(Application $app, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        /**
         * @var AcceptFileService $service
         */
        $service = $app['service.acceptfile'];
        try {
            $service->downloads($data);
        } catch (\Exception $e) {
            return new Response(json_encode(['msg' => $e->getMessage()]), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        /**
         * @var ApiCloud $cloud
         */
        $cloud = $app['teleport.cloud'];
        try {
            $cloud->downloadComplete();
        } catch (\Exception $e) {
            return new Response(json_encode(['msg' => $e->getMessage()]), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        foreach ($data as $item) {
            $file = basename($item['url']);
            $app['dispatcher']->dispatch(Events::EXECUTE_DUMP, new DumpEvent($file));
        }

        return new Response('', Response::HTTP_OK);
    }

    /**
     * Write progress
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return Response
     */
    public function progress(Application $app, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        /**
         * @var \Closure $progress
         */
        $progress = $app->offsetGet('progress');
        $progress($data['progress']);

        return new Response('', Response::HTTP_OK);
    }

    /**
     * Import data
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return Response
     */
    public function import(Application $app, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $app['dispatcher']->dispatch(Events::EXECUTE_DUMP, new DumpEvent($data['file']));

        return new Response('', Response::HTTP_OK);
    }
}
