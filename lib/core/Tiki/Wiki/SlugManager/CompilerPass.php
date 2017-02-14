<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: CompilerPass.php 57968 2016-03-17 20:06:57Z jonnybradley $

namespace Tiki\Wiki\SlugManager;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		if (!$container->hasDefinition('tiki.lib.slugmanager')) {
			return;
		}

		$definition = $container->getDefinition('tiki.lib.slugmanager');

		$taggedServices = $container->findTaggedServiceIds('tiki.wiki.slug.generator');
		foreach ($taggedServices as $id => $attributes) {
			$definition->addMethodCall('addGenerator', [
				new Reference($id),
			]);
		}
	}
}

