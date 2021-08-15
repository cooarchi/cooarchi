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
        $basePath = $container->get('config')['basePath'] ?? null;
        if ($basePath === null) {
            throw new RuntimeException('basePath config value is missing');
        }

        $isPublicReadable = $container->get('config')['cooarchi']['isPublicReadable'] ?? false;
        $isPublicWriteable = $container->get('config')['cooarchi']['isPublicWriteable'] ?? false;

        $rbacConfig = $this->getRbacRoles($basePath, $isPublicReadable, $isPublicWriteable);

        if (isset($rbacConfig['roles']) === false) {
            throw new RuntimeException('Rbac roles are not provided through config');
        }
        if (isset($rbacConfig['permissions']) === false) {
            throw new RuntimeException('Rbac permissions are not provided through config');
        }

        $rbac = new Rbac();
        $rbac->setCreateMissingRoles(true);

        foreach ($rbacConfig['roles'] as $role => $parents) {
            $rbac->addRole($role, $parents);
        }

        foreach ($rbacConfig['permissions'] as $role => $permissions) {
            foreach ($permissions as $permission) {
                $rbac->getRole($role)->addPermission($permission);
            }
        }

        return new PermissionMiddleware(
            $rbac,
            $container->get(TemplateRendererInterface::class)
        );
    }

    private function getRbacRoles(string $basePath, bool $isPublicReadable, bool $isPublicWriteable) : array
    {

        if ($isPublicWriteable === true) {
            return include $basePath . '/config/rbac_public_writeable.php';
        }
        if ($isPublicReadable === true) {
            return include $basePath . '/config/rbac_public_readable.php';
        }

        return include $basePath . '/config/rbac_closed.php';
    }
}
