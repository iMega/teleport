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
use Symfony\Component\HttpKernel\HttpCache\Store;

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
        $c = $app['controllers_factory'];
        $c->get("/checkauth", array($this, 'checkauth'))->bind('checkauth');
        $c->get("/init", array($this, 'init'))->bind('init');
        $c->post("/file", array($this, 'acceptFile'))->bind('acceptFile');
        $c->get("/import", array($this, 'import'))->bind('import');

        return $c;
    }

    /**
     * The cap of request on checkauth :)
     *
     * @return Response
     */
    public function checkauth()
    {
        return new Response("success\n", Response::HTTP_OK);
    }

    /**
     * Send params for session
     *
     * @return Response
     */
    public function init()
    {
        return new Response("zip=no\nfile_limit=2000000\n", Response::HTTP_OK);
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
        $filename = $request->get('filename');
        $data = $request->getContent();
        /**
         * @var \iMega\Teleport\StorageInterface $storage
         */
        $storage = $app['storage'];
        $result = file_put_contents('gaufrette://teleport/'.$filename, $data, FILE_APPEND);

        if (false === $result) {
            return new Response("failure\n", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response("success\n", Response::HTTP_OK);
    }

    /**
     * Kick me!
     *
     * @param Request $request
     *
     * @return Response
     */
    public function import(Application $app, Request $request)
    {
        $filename = $request->get('filename');
        /**
         * @var \iMega\Teleport\StorageInterface $storage
         */
        $storage = $app['storage'];

        $app['dispatcher']->dispatch(Events::BUFFER_PARSE_START, null);

        $keyStock = $this->getFileNameSource($storage, Parser\Description::CLASSI);
        if (!empty($keyStock)) {
            $stock = new Parser\Stock($storage->read($keyStock), $app['dispatcher']);
            $stock->parse();
            $storage->delete($keyStock);
        }

        $keyOffer = $this->getFileNameSource($storage, Parser\Description::PACKAGEOFFERS);
        if (!empty($keyOffer)) {
            $offers = new Parser\Offers($storage->read($keyOffer), $app['dispatcher']);
            $offers->parse();
            $storage->delete($keyOffer);
        }


        $app['dispatcher']->dispatch(Events::BUFFER_PARSE_END, null);

        return new Response("success\n", Response::HTTP_OK);
    }

    private function getFileNameSource($storage, $type)
    {
        /**
         * @var \Gaufrette\Filesystem $storage
         */
        foreach ($storage->keys() as $file) {
            if (strpos($file, '.xml') >= 1) {
                $res = $storage->read($file);
                if (mb_strpos($res, $type) > 0) {
                    return $file;
                }
            }
        }
    }
}
