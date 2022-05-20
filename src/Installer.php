<?php

namespace Hito\Installer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;

class Installer extends LibraryInstaller
{
    private array $types = [
        'hito-module' => [
            'basedir' => 'modules',
            'subdir' => ':vendor/:name'
        ]
    ];

    public function getInstallPath(PackageInterface $package)
    {
        $type = $package->getType();

        if (empty($this->types[$type])) {
            return parent::getInstallPath($package);
        }

        $fullPackageName = explode('/', $package->getPrettyName());

        if (count($fullPackageName) === 2) {
            [$vendor, $name] = $fullPackageName;
        } else {
            $vendor = '';
            $name = array_shift($fullPackageName);
        }

        $type = $this->types[$type];

        $this->filesystem->ensureDirectoryExists($type['basedir']);

        $basePath = strtr("{$type['basedir']}/{$type['subdir']}", [
            ':vendor' => $vendor,
            ':name' => $name,
        ]);

        $targetDir = $package->getTargetDir();

        return $basePath . ($targetDir ? '/'.$targetDir : '');
    }

    public function supports($packageType)
    {
        if (in_array($packageType, ['metapackage', 'composer-plugin'])) {
            return false;
        }

        return !empty($this->types[$packageType]);
    }
}
