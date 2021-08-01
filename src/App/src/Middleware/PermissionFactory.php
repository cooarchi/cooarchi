<?php
declare(strict_types=1);

namespace CooarchiApp\Middleware;

use Interop\Container\ContainerInterface;
use Mezzio\Template\TemplateRendererInterface;
use Laminas\Permissions\Rbac\Rbac;
use RuntimeException;

final class PermissionFactory
{
    public function __invoke(ContainerInterface $container) : PermissionMiddleware
    {
        /** @var array $config */
        $config = $container->get('config');
        if (! isset($config['rbac']['roles'])) {
            throw new RuntimeException('Rbac roles are not provided through config');
        }
        if (!isset($config['rbac']['permissions'])) {
            throw new RuntimeException('Rbac permissions are not provided through config');
        }

        $rbac = new Rbac();
        $rbac->setCreateMissingRoles(true);

        foreach ($config['rbac']['roles'] as $role => $parents) {
            $rbac->addRole($role, $parents);
        }

        foreach ($config['rbac']['permissions'] as $role => $permissions) {
            foreach ($permissions as $permission) {
                $rbac->getRole($role)->addPermission($permission);
            }
        }

        return new PermissionMiddleware(
            $rbac,
            $container->get(TemplateRendererInterface::class)
        );
    }
}
