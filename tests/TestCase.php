<?php

use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator;
use JeroenNoten\LaravelAdminLte\AdminLte;
use JeroenNoten\LaravelAdminLte\Menu\ActiveChecker;
use JeroenNoten\LaravelAdminLte\Menu\Builder;
use JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter;
use JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter;
use JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter;
use JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter;
use JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Auth\Access\Gate;

class TestCase extends PHPUnit_Framework_TestCase
{
    private $dispatcher;

    protected function makeMenuBuilder($uri = 'http://example.com', GateContract $gate = null)
    {
        return new Builder([
            new ActiveFilter($this->makeActiveChecker($uri)),
            new HrefFilter($this->makeUrlGenerator($uri)),
            new SubmenuFilter(),
            new ClassesFilter(),
            new GateFilter($gate ?: $this->makeGate()),
        ]);
    }

    protected function makeActiveChecker($uri = 'http://example.com')
    {
        return new ActiveChecker($this->makeRequest($uri));
    }

    private function makeRequest($uri)
    {
        return Request::createFromBase(SymfonyRequest::create($uri));
    }

    protected function makeAdminLte()
    {
        return new AdminLte($this->getFilters(), $this->getDispatcher(), $this->makeContainer());
    }

    protected function makeUrlGenerator($uri = 'http://example.com')
    {
        return new UrlGenerator(new RouteCollection, $this->makeRequest($uri));
    }

    protected function makeGate()
    {
        $userResolver = function () {
            return new GenericUser([]);
        };

        return new Gate($this->makeContainer(), $userResolver);
    }

    protected function makeContainer()
    {
        return new Illuminate\Container\Container();
    }

    protected function getDispatcher()
    {
        if (! $this->dispatcher) {
            $this->dispatcher = new Dispatcher;
        }

        return $this->dispatcher;
    }

    private function getFilters()
    {
        return [];
    }
}
