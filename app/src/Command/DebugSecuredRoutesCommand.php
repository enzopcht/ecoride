<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use ReflectionMethod;

#[AsCommand(name: 'app:debug:secured-routes', description: 'Affiche les routes avec les rôles requis')]
class DebugSecuredRoutesCommand extends Command
{
    public function __construct(private RouterInterface $router)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $routes = $this->router->getRouteCollection();

        $rows = [];

        foreach ($routes as $name => $route) {
            $defaults = $route->getDefaults();
            $controller = $defaults['_controller'] ?? null;

            $requiredRole = '—';

            if ($controller && str_contains($controller, '::')) {
                [$class, $method] = explode('::', $controller);
                if (class_exists($class) && method_exists($class, $method)) {
                    $reflection = new ReflectionMethod($class, $method);
                    foreach ($reflection->getAttributes(IsGranted::class) as $attr) {
                        /** @var IsGranted $instance */
                        $instance = $attr->newInstance();
                        $requiredRole = $instance->attribute;
                    }
                }
            }

            $rows[] = [
                $name,
                $route->getPath(),
                implode(', ', $route->getMethods()),
                $requiredRole,
            ];
        }

        $io->table(
            ['Nom', 'Path', 'Méthodes', 'Accès Requis (@IsGranted)'],
            $rows
        );

        return Command::SUCCESS;
    }
}