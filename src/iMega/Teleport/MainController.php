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
        $c = $app['controllers_factory'];
        $c->get("/checkauth", array($this, 'checkauth'))->bind('checkauth');
        $c->get("/init", array($this, 'init'))->bind('init');
        $c->get("/file", array($this, 'acceptFile'))->bind('acceptFile');
        $c->get("/import", array($this, 'import'))->bind('import');

        return $c;
    }

    /**
     * @return Response
     */
    public function checkauth()
    {
        return new Response("success\n", Response::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function init()
    {
        return new Response("zip=no\nfile_limit=2000000", Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function acceptFile(Request $request)
    {
        echo "file\n";

        return true;
    }

    public function import(Request $request)
    {
        echo "import\n";

        return true;
    }
}
